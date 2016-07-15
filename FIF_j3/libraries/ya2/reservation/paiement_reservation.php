<?php
require_once ('admin_base.php');
$siteURL= $config->get( 'site_url' );


$duree = $_POST ["duree_resa"];

// ///////
// On recupere l'Id client par GET ou POST si c'est un agent
// /////
if (est_min_agent ( $user )) {
	if (test_non_vide ( $_POST ["id_client"] ))
		$id_client = $_POST ["id_client"];
	else
		$id_client = $_GET ["id_client"];
} else
	$id_client = idclient_du_user ();
	
	// ///////
	// On recupere le num resa par GET ou POST
	// /////

if (test_non_vide ( $_POST ["num_resa"] ))
	$id_resa = $_POST ["num_resa"];
else
	$id_resa = $_GET ["num_resa"];

if (test_non_vide ( $_POST ["commentaire"] ) and (strpos ( $_POST ["commentaire"], '"' ) !== false))
	echo "Caractere interdit dans le commentaire : <font color=red>\"</font>";
else {
	
	if (test_non_vide ( $_POST ["num_resa"] ))
		$complement_lien = "&id_resa=" . $_POST ["num_resa"];
	else
		$complement_lien = "";
	
	if (test_nuit_avant_ouverture ( $_POST ["heure_debut_resa"] ) == 1)
		$date_Min = decaler_jour ( $_POST ["date_debut_resa"], - 1 );
	else
		$date_Min = $_POST ["date_debut_resa"];
		// ///////
		// Si la caution est superieur à l'acompte => la resa devient cautionnable
		// /////
	
	$mode_resa = $_POST ["mode_resa"];
	
	if ($_POST ["acompte"] <= recup_caution_total_client ( $id_client ) and est_min_agent ( $user ) and test_limite_nbre_resa_par_caution ( $date_Min, $id_client ) and $mode_resa == 2)
		$cautionnable = 1;
	else
		$cautionnable = 0;
	
	$temp_montant = "montant_total" . coller_jma ( $_POST ["date_debut_resa"] );
	$temp_montant_avec_remise = "montant_total_avec_remise" . coller_jma ( $_POST ["date_debut_resa"] );
	
	$montant_sans_remise = $_POST ["$temp_montant"];
	$montant_total = $_POST ["$temp_montant_avec_remise"];
	
	if ($id_client == 3586 or $id_client == 1720)
		$montant_total = 0;
		
		// ///////
		// On verifie que le terrain est encore dispo car un autre client aurait pu le prendre entre l'affichage et le clic de souris.
		// /////
	
	if (! (is_array ( trouve_dispo ( $_POST ["type_terrain"], $date_Min, $_POST ["heure_debut_resa"], $_POST ["heure_fin_resa"], $_POST ["num_resa"], "1", false ) )))
		header ( "Location: $siteURL/index.php/component/content/article?id=79&id_client=" . $_POST ["id_client"] . "&date_debut_resa=" . $date_Min . "&heure_fin_resa=" . $_POST ["heure_fin_resa"] . "&heure_debut_resa=" . $_POST ["heure_debut_resa"] . $complement_lien . "" );
	else {
		
		// ///////
		// On verifie que l'utilisateur n'a pas cliqué sur retour dans le navigateur pour eviter de creer 2 fois la meme resa.
		// /////
		
		$resultat_recup_resa_si_2_validations = recup_resa_si_2_validations ( $_POST ["date_debut_resa"], $_POST ["heure_debut_resa"], $_POST ["heure_fin_resa"], $_POST ["terrain_choisit"], $mode_resa );
		
		if ($resultat_recup_resa_si_2_validations > 0)
			echo "<font color=red>Cette réservation est dejà validée...</font>";
		else {
			if (test_non_vide ( $_POST ["num_resa"] )) {
				// /////////
				// ////si c'est une modif de reservation, on sauvegarde d'abord les anciennes infos dans Hist_resa
				// //////////
				save_resa ( $_POST ["num_resa"], $_POST ["date_debut_resa"], $_POST ["date_fin_resa"], $_POST ["heure_debut_resa"], $_POST ["heure_fin_resa"], $montant_total );
			}
			if (test_non_vide ( $id_resa )) {
				// ///////////
				// on met à jour la resa dans le cas d'une simple modif
				// ////////////
				$id_resa_new = maj_resa ( $id_resa, $_POST ["date_debut_resa"], $_POST ["date_fin_resa"], $_POST ["heure_debut_resa"], $_POST ["heure_fin_resa"], $id_client, $_POST ["terrain_choisit"], $montant_total, $_POST ["duree_resa"], $cautionnable, $mode_resa, $montant_sans_remise );
				maj_commentaire ( "id_resa", $id_resa_new, $_POST ["commentaire"] );
				// $rdv=ajout_event_cal_google ($id_resa_new);
				// suppr_event_cal_google($id_resa);
				
				// /////////////////
				// on met à jour la resa avec l'adresse du rdv
				// ///////////////////
				maj_cal_dans_resa ( $id_resa_new, "" ); // $rdv->getEditLink()->href);
			} else {
				// /////////////////
				// on ajoute la nouvelle resa
				// /////////////////
				if (est_min_manager ( $user ) and $_POST ["nbre_de_jours_a_tester"] > 1 and ! test_non_vide ( $id_resa )) {
					
					$les_jours_a_tester_debut = $_POST ["date_debut_resa"];
					$les_jours_a_tester_fin = $_POST ["date_fin_resa"];
					
					for($nbre_de_jours_a_tester_restants = 0; $nbre_de_jours_a_tester_restants <= $_POST ["nbre_de_jours_a_tester"]; $nbre_de_jours_a_tester_restants ++) {
						$les_terrains = recup_les_terrains ();
						
						foreach ( $les_terrains as $le_terrain ) {
							$temp1 = "terrain_choisit" . coller_jma ( $les_jours_a_tester_debut ) . $le_terrain->nom;
							$temp2 = "montant_total_avec_remise" . coller_jma ( $les_jours_a_tester_debut );
							
							if ($_POST ["$temp1"] == $le_terrain->id) {
								$id_resa_new = ajout_resa ( $les_jours_a_tester_debut, $les_jours_a_tester_fin, $_POST ["heure_debut_resa"], $_POST ["heure_fin_resa"], $id_client, $le_terrain->id, $_POST ["$temp2"], $_POST ["duree_resa"], $cautionnable, $mode_resa, $montant_sans_remise );
								maj_commentaire ( "id_resa", $id_resa_new, $_POST ["commentaire"] );
								// $rdv=ajout_event_cal_google ($id_resa_new);
								// /////////////////
								// on met à jour la resa avec l'adresse du rdv
								// ///////////////////
								maj_cal_dans_resa ( $id_resa_new, "" ); // $rdv->getEditLink()->href);
							}
						}
						$les_jours_a_tester_debut = decaler_jour ( $les_jours_a_tester_debut, $_POST ["la_Frequence"] );
						$les_jours_a_tester_fin = decaler_jour ( $les_jours_a_tester_fin, $_POST ["la_Frequence"] );
					}
				} else {
					$id_resa_new = ajout_resa ( $_POST ["date_debut_resa"], $_POST ["date_fin_resa"], $_POST ["heure_debut_resa"], $_POST ["heure_fin_resa"], $id_client, $_POST ["terrain_choisit"], $montant_total, $_POST ["duree_resa"], $cautionnable, $mode_resa, $montant_sans_remise );
					maj_commentaire ( "id_resa", $id_resa_new, $_POST ["commentaire"] );
					// $rdv=ajout_event_cal_google ($id_resa_new);
					// /////////////////
					// on met à jour la resa avec l'adresse du rdv
					// ///////////////////
					maj_cal_dans_resa ( $id_resa_new, "" ); // $rdv->getEditLink()->href);
				}
				
				// envoie d'email
				if ($mode_resa == 3) {
					$sql = "select Client.* from Client, Reservation where Client.id_client = Reservation.id_client and Reservation.id_resa=$id_resa_new";
					$db->setQuery($sql);
					$info_resa= $db->loadObject();
					$corps = texte_resa ( $info_resa->prenom . " " . $info_resa->nom, $_POST ["date_debut_resa"], $_POST ["heure_debut_resa"], $_POST ["heure_fin_resa"], $montant_total, "", 1 );
					$objet = "Confirmation de votre reservation (Num : " . $id_resa_new . ")";
					if (($_POST ["date_debut_resa"] < date ( "Y-m-d" )) or sendMail ( $info_resa->id_client, $objet, $corps )) {
						maj_resa_notification ( $id_resa_new, 1 );
						header ( "Location: $siteURL/index.php/component/content/article?id=61&id_resa=" . $id_resa_new . "" );
					}
				}
			}
			// $liste_resas=rechercher_resa_valid_sans_presence_google_cal();
			if (test_non_vide ( $liste_resas ))
				sendMail ( 1, "resas validees absentes du calendrier", $liste_resas );
				// /////////////////
				// si c'est une modif, on supprime l'ancien evenement du calendrier google ...
				// ////////
			if (test_non_vide ( $id_resa )) {
				
				// on recredites le compte si la resa coute moins cher
				$total_versement = (versements_sans_remise_et_avec_validation ( $id_resa ) - montant_total_presta ( $id_resa ));
				
				if (($montant_total - $total_versement) < 0) {
					ajout_credit ( $id_client, ($total_versement - $montant_total), 1, "Modif", 2 );
					// reglement negatif pour compensé le credit client en cas de remboursement de la résa
					ajout_reglement ( $id_resa, $id_client, ($montant_total - $total_versement), 2 );
				}
			}
			
			if (est_min_manager ( $user ) and $_POST ["nbre_de_jours_a_tester"] > 1 and ! test_non_vide ( $id_resa ))
				header ( "Location: $siteURL/index.php?option=com_content&view=article&id=59&ttes=1&id_client=" . $id_client . "" );
			else
				header ( "Location: $siteURL/index.php?option=com_content&view=article&id=61&premiere=1&id_resa=" . $id_resa_new . "" );
		}
	}
}
?>