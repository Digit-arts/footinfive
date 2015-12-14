<?php
defined ( '_JEXEC' ) or die ( 'Access Deny' );
jimport ( 'joomla.application.component.controller' );
class PublipostageController extends JControllerLegacy {

	function Load() {
		session_start ();
		
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/fonctions_module_reservation.php');
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/fonctions_gestion_user.php');
		
		$db = JFactory::getDBO ();
		$config = JFactory::getConfig();
		
		$page = isset ( $_POST ['page'] ) ? $_POST ['page'] : 1;
		$rp = isset ( $_POST ['rp'] ) ? $_POST ['rp'] : 10;
		$sortname = isset ( $_POST ['sortname'] ) ? $_POST ['sortname'] : 'name';
		$sortorder = isset ( $_POST ['sortorder'] ) ? $_POST ['sortorder'] : 'desc';
		$query = isset ( $_POST ['query'] ) ? $_POST ['query'] : false;
		$qtype = isset ( $_POST ['qtype'] ) ? $_POST ['qtype'] : false;
		
		if (isset ( $_GET ['Add'] )) { // this is for adding records
			
			$rows = $_SESSION ['clients_list'];
			$rows [$_GET ['EmpID']] = array (
					'name' => $_GET ['Name'],
					'favorite_color' => $_GET ['FavoriteColor'],
					'favorite_pet' => $_GET ['FavoritePet'],
					'primary_language' => $_GET ['PrimaryLanguage'] 
			);
			$_SESSION ['clients_list'] = $rows;
		} elseif (isset ( $_GET ['Edit'] )) { // this is for Editing records
			$rows = $_SESSION ['clients_list'];
			
			unset ( $rows [trim ( $_GET ['OrgEmpID'] )] ); // just delete the original entry and add.
			
			$rows [$_GET ['EmpID']] = array (
					'name' => $_GET ['Name'],
					'favorite_color' => $_GET ['FavoriteColor'],
					'favorite_pet' => $_GET ['FavoritePet'],
					'primary_language' => $_GET ['PrimaryLanguage'] 
			);
			$_SESSION ['clients_list'] = $rows;
		} elseif (isset ( $_GET ['Delete'] )) { // this is for removing records
			$rows = $_SESSION ['clients_list'];
			unset ( $rows [trim ( $_GET ['Delete'] )] ); // to remove the \n
			$_SESSION ['clients_list'] = $rows;
		} else {
			unset ( $_SESSION ['clients_list'] );
			if (isset ( $_SESSION ['clients_list'] )) { // get session if there is one
				$rows = $_SESSION ['clients_list'];
			} else { // create session with some data if there isn't
				
				$requete_recup_publipostage = "SELECT p.*,  (select name from #__users where id=p.id_user) as nom_user  FROM  `Publipostage` as p ";
				
				// echo $requete_recup_publipostage;
				$db->setQuery ( $requete_recup_publipostage );
				$db->query ();
				$resultat_recup_publipostage = $db->loadObjectList ();
				foreach ( $resultat_recup_publipostage as $recup_publipostage ) {
					$requete_nbre_mails_envoyes = "SELECT count(id_client) as mails_envoyes  FROM  `Publipostage_send` as p WHERE  p.id_pub=" . $recup_publipostage->id_pub;
					
					// echo $requete_nbre_mails_envoyes;
					$db->setQuery ( $requete_nbre_mails_envoyes );
					$db->query ();
					$resultat_nbre_mails_envoyese = $db->loadResult ();
					
					$pubID = $recup_publipostage->id_pub;
					$username = $recup_publipostage->nom_user;
					// $creationDate = date_longue ( $recup_publipostage->date_creation ) . " &agrave; " . $recup_publipostage->heure_creation;
					$creationDate = $recup_publipostage->date_creation . " " . $recup_publipostage->heure_creation;
					$subject = "<a class='fancybox' rel='group' href='#bodyPub" . $recup_publipostage->id_pub . "'>" . $recup_publipostage->objet . "</a>";
					$sendTo = recup_dest_publipostage ( $recup_publipostage->id_pub );
					$nbSent = $resultat_nbre_mails_envoyese;

					$siteURL= $config->get( 'site_url' );
					$status = "<img src=\"$siteURL/images/";
					switch ($recup_publipostage->actif) {
						case 0 :
							$status .= "publipostage_non_demarrer.png\" title=\"Le publipostage n'est pas lanc&eacute;";
							break;
						
						case 1 :
							$status .= "publipostage_en_cours.png\"  title=\"Le publipostage est en cours d'execution";
							break;
						
						case 2 :
							$status .= "publipostage_terminer.png\"  title=\"Le publipostage est termin&eacute;";
							break;
							
							defaut	:
							$status .= "images\publipostage_probleme.png\"  title=\"Le publipostage rencontre un probleme";
							break;
					}
					$status .= "\" />";
					
					$body = "<div id='bodyPub" . $recup_publipostage->id_pub . "'>" . $recup_publipostage->corps . "</div>";
					
					$rows [] = array (
							'pubID' => $pubID,
							'username' => $username,
							'creationDate' => $creationDate,
							'subject' => $subject,
							'sendTo' => $sendTo,
							'nbSent' => $nbSent,
							'status' => $status,
							'body' => $body 
					);
				}
				$_SESSION ['clients_list'] = $rows;
			}
			
			if ($qtype && $query) {
				$query = strtolower ( trim ( $query ) );
				foreach ( $rows as $key => $row ) {
					if (strpos ( strtolower ( $row [$qtype] ), $query ) === false) {
						unset ( $rows [$key] );
					}
				}
			}
			// Make PHP handle the sorting
			$sortArray = array ();
			foreach ( $rows as $key => $row ) {
				$sortArray [$key] = $row [$sortname];
			}
			$sortMethod = SORT_ASC;
			if ($sortorder == 'desc') {
				$sortMethod = SORT_DESC;
			}
			array_multisort ( $sortArray, $sortMethod, $rows );
			$total = count ( $rows );
			$rows = array_slice ( $rows, ($page - 1) * $rp, $rp );
			
			header ( "Content-type: application/json" );
			$jsonData = array (
					'page' => $page,
					'total' => $total,
					'rows' => array () 
			);
			foreach ( $rows as $rowNum => $row ) {
				// If cell's elements have named keys, they must match column names
				// Only cell's with named keys and matching columns are order independent.
				$entry = array (
						'id' => $rowNum,
						'cell' => array (
								'employeeID' => $rowNum,
								'name' => $row ['name'],
								'pubID' => $row ['pubID'],
								'username' => $row ['username'],
								'creationDate' => $row ['creationDate'],
								'subject' => $row ['subject'],
								'sendTo' => $row ['sendTo'],
								'nbSent' => $row ['nbSent'],
								'status' => $row ['status'],
								'body' => $row ['body'] 
						) 
				);
				$jsonData ['rows'] [] = $entry;
			}
			echo json_encode ( $jsonData );
		}
	}

	function Start() {
		$id_pub = JRequest::getVar ( 'PubId' );
		$db = JFactory::getDBO ();
		$requete_maj_demarrer_publipostage = "UPDATE `Publipostage` SET `actif`=1 WHERE `id_pub`=" . $id_pub;
		$db->setQuery ( $requete_maj_demarrer_publipostage );
		$db->query ();
	}
	
	function Delete() {
		$id_pub = JRequest::getVar ( 'PubId' );
		$db = JFactory::getDBO ();
		
		$requete_clear_publipostage_LEDG = "DELETE FROM `Publipostage_type_destinataires` WHERE `id_pub` = " . $id_pub;
		$db->setQuery ( $requete_clear_publipostage_LEDG );
		$db->query ();
		
		$requete_maj_demarrer_publipostage = "DELETE FROM `Publipostage` WHERE `id_pub`=" . $id_pub;
		$db->setQuery ( $requete_maj_demarrer_publipostage );
		$db->query ();
	}

	function Stop() {
		$id_pub = JRequest::getVar ( 'id_pub' );
		$db = JFactory::getDBO ();
		$requete_maj_arreter_publipostage = "UPDATE `Publipostage` SET `actif`=0 WHERE `id_pub`=" . $id_pub;
		$db->setQuery ( $requete_maj_arreter_publipostage );
		$db->query ();
	}

	function Save() {
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/fonctions_module_reservation.php');
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/fonctions_gestion_user.php');
		
		$corps = $_POST['corps'];
		$objet = $_POST['objet'];
		$id_pub = JRequest::getVar ( 'id_pub' );
		$joueur_champ = JRequest::getVar ( 'joueur_champ' );
		$police = JRequest::getVar ( 'police' );
		
		$db = JFactory::getDBO ();
		$user = JFactory::getUser ();
		$config = JFactory::getConfig();
		$siteRoot= $config->get( 'site_root' );
		
		$corps = str_replace ( 'src="', 'src="'.$siteRoot, $corps );
		
		if (! isset ( $id_pub )) {
			$requete_insert_publipostage = "INSERT INTO `Publipostage`( `objet`, `corps`, `date_creation`, `heure_creation`, `id_user`, `actif`)" . " VALUES (" . $db->quote ( $objet ) . "," . $db->quote ( $corps ) . ",\"" . date ( "Y-m-d" ) . "\",\"" . date ( "H:i" ) . "\"," . $user->id . ",0)";
			$db->setQuery ( $requete_insert_publipostage );
			$db->query ();
			$id_pub = $db->insertid ();
		} else {
			$requete_update_publipostage = "UPDATE `Publipostage` SET `objet` = " . $db->quote ( $objet ) . ", `corps` = " . $db->quote ( $corps ) . " WHERE `id_pub` = " . $id_pub;
			$db->setQuery ( $requete_update_publipostage );
			$db->query ();
			
			$requete_clear_publipostage_LEDG = "DELETE FROM `Publipostage_type_destinataires` WHERE `id_pub` = " . $id_pub;
			$db->setQuery ( $requete_clear_publipostage_LEDG );
			$db->query ();
			
			$id_pub = $id_pub;
		}
		if (test_non_vide ( $id_pub )) {
			if (test_non_vide ( $joueur_champ )) {
				$requete_insert_publipostage_LEDG = "INSERT INTO `Publipostage_type_destinataires`(`id_pub`, `id_type_regroupement`) " . " VALUES (" . $id_pub . ",10001)";
				// echo $requete_insert_publipostage_LEDG;
				$db->setQuery ( $requete_insert_publipostage_LEDG );
				$db->query ();
			}
			if (test_non_vide ( $police )) {
				$requete_insert_publipostage_police = "INSERT INTO `Publipostage_type_destinataires`(`id_pub`, `id_type_regroupement`) " . " VALUES (" . $id_pub . ",10000)";
				// echo $requete_insert_publipostage_police;
				$db->setQuery ( $requete_insert_publipostage_police );
				$db->query ();
			}
			$max_tab_type_regroupement = max_tab ( "Type_Regroupement" );
			/*if ($max_tab_type_regroupement > 9999) {
				sendMail ( 1, "alerte publipostage", "L id de Type_Regroupement a depass&eacute; 9999..." );
				exit ();
			}*/
			for($i = 0; $i <= $max_tab_type_regroupement; $i ++) {
				if (test_non_vide ( $_POST ["Type_Regroupement_$i"] )) {
					$requete_insert_publipostage_Type_Regroupement = "INSERT INTO `Publipostage_type_destinataires`(`id_pub`, `id_type_regroupement`) " . " VALUES (" . $id_pub . "," . $i . ")";
					// echo $requete_insert_publipostage_Type_Regroupement;
					$db->setQuery ( $requete_insert_publipostage_Type_Regroupement );
					$db->query ();
				}
			}
		}
		unset ( $_SESSION ['clients_list'] );
		sendMail ( 1, $objet, $corps );
		sendMail ( 267, $objet, $corps );
		
		header ( 'Location: index.php/component/content/article?id=65' );
	}
}

?>