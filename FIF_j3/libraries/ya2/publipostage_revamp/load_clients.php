<?php
session_start ();

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

$user = & JFactory::getUser ();
$db = & JFactory::getDBO ();


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
			$creationDate = date_longue ( $recup_publipostage->date_creation ) . " &agrave; " . $recup_publipostage->heure_creation;
			$subject = "<a href=\"index.php/component/content/article?id=65&id_pub=" . $recup_publipostage->id_pub . "\">" . $recup_publipostage->objet . "</a>";
			$sendTo = recup_dest_publipostage ( $recup_publipostage->id_pub );
			$nbSent = $resultat_nbre_mails_envoyese;
			
			$status = "<img src=\"images/";
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
					$status .= "publipostage_probleme.png\"  title=\"Le publipostage rencontre un probleme";
					break;
			}
			$status .= "\" />";
			
			$body = $recup_publipostage->corps;
			
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
	
	header ( "Content-type: application/json" );
	$jsonData = array (
			'page' => $page,
			'total' => 0,
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
	$jsonData ['total'] = count ( $rows );
	echo json_encode ( $jsonData );
}
?>