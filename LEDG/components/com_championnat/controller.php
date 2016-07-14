<?php
defined ( '_JEXEC' ) or die ( 'Access Deny' );
jimport ( 'joomla.application.component.controller' );
class ChampionnatController extends JControllerLegacy {

	function UpdateDateHeureTerrain() {
		$type = $_POST ['Type'];
		$match = $_POST ['Match'];
		$nb_equipes = $_POST ['Nb_Equipes'];
		$date_saisie = $_POST ['Date_Saisie'];
		$heure_saisie = $_POST ['Heure_saisie'];
		$terrain_saisi = $_POST ['Terrain_saisi'];
		
		$la_date = "date_" . $nb_equipes . "_" . $match;
		$l_heure = "heure_" . $nb_equipes . "_" . $match;
		$le_terrain = "terrain_choisit_" . $nb_equipes . "_" . $match;
		
		$date_saisie = str_replace('/', '-', $date_saisie);
		
		$date_saisie = date("Y-m-d", strtotime($date_saisie));
		
		
		echo $this->menu_deroulant_des_heures_avec_dispo_fif ( $l_heure, $heure_saisie, $date_saisie, " onChange=\"updateDateHeureTerrain('heure'," . $match . "," . $nb_equipes . ",'" . $date_saisie . "','" . $heure_saisie . "','" . $terrain_saisi . "')\" " );
		
		echo $this->menu_deroulant_des_terrains_avec_dispo_fif ( $le_terrain, $terrain_saisi, $date_saisie, $heure_saisie, " onChange=\"updateDateHeureTerrain('terrain'," . $match . "," . $nb_equipes . ",'" . $date_saisie . "','" . $heure_saisie . "','" . $terrain_saisi . "')\" " );
	}

	function menu_deroulant_des_terrains_avec_dispo_fif($le_terrain, $old_terrain, $la_date, $l_heure, $function = "") {
		$les_terrains_dispos = $this->dispo_date_fif ( $la_date, $l_heure, $this->decaler_heure ( $l_heure, 60 ) );
		// $le_select_terrain=$la_date.",".$l_heure.",".decaler_heure($l_heure,60);
		if ($les_terrains_dispos != "pas de terrains dispo") {
			
			$les_terrains_disposen_tableau = explode ( " ", substr ( $les_terrains_dispos, 0, - 1 ) ); // enleve l'espace Ã  la fin
			
			$le_select_terrain = "<select name=\"" . $le_terrain . "\"  " . $function . " >";
			foreach ( $les_terrains_disposen_tableau as $terrain ) {
				if ($this->test_non_vide ( $old_terrain ) and $old_terrain == $terrain)
					$select_terrain = " selected ";
				else
					$select_terrain = "";
				$le_select_terrain .= "<option value=\"" . $terrain . "\" " . $select_terrain . ">" . $terrain . "</option>";
			}
			$le_select_terrain .= "</select>";
		}
		return ($le_select_terrain);
	}

	function menu_deroulant_des_heures_avec_dispo_fif($nom_du_select, $heure_selectionee, $date_a_verifier, $function = "") {
		$le_select_heure = "<select name=\"" . $nom_du_select . "\" " . $function . " >";
		
		list ( $heure_resa, $minutes_resa ) = explode ( ":", $heure_selectionee );
		
		for($time = 9; $time <= 23; $time ++) {
			$select_heure = "";
			$select_demie = "";
			if ($this->dispo_date_fif ( $date_a_verifier, $time . ":00", ($time + 1) . ":00" ) != "pas de terrains dispo") {
				
				if (($heure_resa == "") and ($time == 9))
					$select_heure = " selected ";
				else if ($heure_resa == $time) {
					if ($minutes_resa == "30")
						$select_demie = " selected ";
					else
						$select_heure = " selected ";
				}
				$le_select_heure .= "<option value=\"" . $time . ":00\" \"" . $select_heure . "\">" . $time . "h00</option>";
			}
			
			if ($this->dispo_date_fif ( $date_a_verifier, $time . ":30", ($time + 1) . ":30" ) != "pas de terrains dispo")
				$le_select_heure .= "<option value=\"" . $time . ":30\"  \"" . $select_demie . "\" >" . $time . "h30</option>";
		}
		$le_select_heure .= "</select> ";
		return ($le_select_heure);
	}

	function dispo_date_fif($la_date, $heure_debut, $heure_fin) {
		$tab_liste_terrains_dispo = $this->test_dispo ( $la_date, $heure_debut, $heure_fin );
		$liste_terrains_dispos = "";
		if (is_array ( $tab_liste_terrains_dispo )) {
			foreach ( $tab_liste_terrains_dispo as $terrains_dispo ) {
				$liste_terrains_dispos .= "T" . $terrains_dispo . " ";
			}
		} else
			$liste_terrains_dispos = "pas de terrains dispo";
		return ($liste_terrains_dispos);
	}

	function test_dispo($date_saisie_Min, $horaire_Min, $horaire_Max, $terrain = "", $resa_modif = "") {
		// cette fonction renvoie 0 si aucune dispo sinon tableau des terrains
		$mysql_fif = $this->connect_fif ();
		
		list ( $heure_min, $minutes_min ) = explode ( ':', $horaire_Min );
		list ( $heure_max, $minutes_max ) = explode ( ':', $horaire_Max );
		
		// echo "<br>Test_dispo apres : ".$date_saisie_Min." ".$horaire_Min." ---- ".$date_Max." ".$horaire_Max." - ".$terrain."-".$resa_modif;
		
		$requete_test_dispo = "select id_cal_google,nom_long,nom,id from Terrain as t where t.id_type=1 and t.id not in " . " (SELECT r.id_terrain FROM `Reservation` as r where " . " not(((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_debut_resa`,\" \",`heure_debut_resa`) AS CHAR(22))," . " CAST(concat(\"" . $date_saisie_Min . "\",\" \",\"" . $horaire_Max . "\") AS CHAR(22)))) <=0) " . " or ((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_fin_resa`,\" \",`heure_fin_resa`) AS CHAR(22))," . " CAST(concat(\"" . $date_saisie_Min . "\",\" \",\"" . $horaire_Min . "\") AS CHAR(22))))>=0))  ";
		
		if ($this->test_non_vide ( $resa_modif ))
			$requete_test_dispo .= " and r.id_resa<>" . $resa_modif;
		$requete_test_dispo .= " and r.indic_annul<>1) ";
		if ($terrain != "")
			$requete_test_dispo .= " and t.id=" . $terrain;
		
		$resultat_test_dispo = $mysql_fif->query ( $requete_test_dispo );
		
		// echo $requete_test_dispo;
		
		while ( $row = $resultat_test_dispo->fetch_row () ) {
			if ($terrain != "") {
				$recup_infos_terrain [0] = $row [0];
				$recup_infos_terrain [1] = $row [1];
				$recup_infos_terrain [2] = $row [2];
				$recup_infos_terrain [3] = $row [3];
			} else
				$recup_infos_terrain [] = $row [3];
		}
		$resultat_test_dispo->close ();
		
		$mysql_fif->close ();
		
		if (is_array ( $recup_infos_terrain ))
			return ($recup_infos_terrain);
		else
			return (0);
	}

	function connect_fif() {
		$mysqli = new mysqli ( "localhost", "Cyclople", "MixMax123", "MySql_FIF_j3" );
		
		/* VÃ©rification de la connexion */
		if (mysqli_connect_errno ()) {
			printf ( "Echec de la connexion : %s\n", mysqli_connect_error () );
			exit ();
		}
		return ($mysqli);
	}
	
	function test_non_vide($var){
		 
		if(isset($var) and ($var<>""))
			return true;
		else
			return false;
	}
	
	function decaler_heure($heure,$ajout)
	{
		$timestamp = strtotime("$heure");
		$heure_ajout = strtotime("+$ajout minutes", $timestamp);
		return ($this->Ajout_zero_si_absent(date('H:i', $heure_ajout)));
	}
	
	function Ajout_zero_si_absent($horaire) {
	
		list($heures,$minutes) = explode(':', $horaire);
	
		$zero_heures="";
		$zero_minutes="";
	
		$valeur_heures=$heures+0;
		$valeur_minutes=$minutes+0;
	
		if ($valeur_heures<10) $zero_heures="0";
		else $zero_heures="";
	
		if ($valeur_minutes<10) $zero_minutes="0";
		else $zero_minutes="";
	
		return ($zero_heures.$valeur_heures.":".$zero_minutes.$valeur_minutes);
	}
}

?>