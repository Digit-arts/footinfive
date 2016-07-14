<script type="text/javascript" src="libraries/jquery/jquery.last.min.js"></script>
<link rel="stylesheet" href="libraries/ya2/styles/style.css"
	type="text/css" />
<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');

$user = & JFactory::getUser ();
$db = & JFactory::getDBO ();
$config = JFactory::getConfig ();
$siteURL = $config->get ( 'site_url' );

?>
<script type="text/javascript">
	
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
					else document.register_versement.submit();
				}
			}
			else {
				if (lien!='') document.location.href=lien;
				else {
					document.form.submit();
				}
			}
	}
	function valider() {
		//document.gen_phase_finale_coupe.submit();
		
	}
	
</script>
<?

foreach ( $user->groups as $groups )
	$user_group = $groups;

$nbre_terrains_fif = recup_nbre_terrains_fif ();

if ($user_group == 3 or $user_group == 8) {
	
	if (test_non_vide ( $_GET ["suppr_tourn"] )) {
		supprimer_tournoi ( $_GET ["suppr_tourn"] );
		header ( "Location: gen" );
	}
	
	if (isset ( $_POST ["liste_tourn"] ) and $_POST ["liste_tourn"] != 0) {
		list ( $id_tourn, $id_saison, $id_groupe, $nbre_equipes_du_groupe, $nbre_equipes_par_groupe, $nbre_equipes_par_tourn ) = explode ( '_', $_POST ["liste_tourn"] );
	}
	
	/* ajouter la phase finale */
	if (test_non_vide ( $_POST ["nbre_vainq_par_poule"] )) {
		
		$puissance_de_2 = 0;
		$nbre_poules = nbre_groupes_dans_saison ( $id_saison );
		$nbre_equipes_phase_finale = ($_POST ["nbre_vainq_par_poule"] * $nbre_poules);
		
		for($i = 0; $i <= 64; $i ++)
			if (pow ( 2, $i ) == $nbre_equipes_phase_finale) {
				$puissance_de_2 = $i;
				break;
			}
		
		if ($puissance_de_2 > 0) {
			$nom_tournoi = substr ( recup_1_element ( "name", "#__bl_tournament", "id", $id_tourn ), 0, - 21 );
			
			$tourn_coupe = ajout_tournoi ( $nom_tournoi . " - Phase finale Coupe", 1 );
			
			$saison_coupe_phase_finale = ajout_saison ( "Phase finale Coupe", $tourn_coupe, $nbre_equipes_phase_finale );
			
			$mday_coupe_phase_finale = ajout_match_day ( 0, "Phase finale", $saison_coupe_phase_finale, $nbre_equipes_phase_finale );
		} else
			echo "<br>Le produit (Nombre de poule)X(Nombre de vainqueur par poule) = " . ($_POST ["nbre_vainq_par_poule"] * $nbre_poules) . " ne s'ecrit pas sous la forme d'une puissance de 2.<br><br>";
	}
	
	if (test_non_vide ( $_POST ["mday_coupe_phase_finale"] ) and test_non_vide ( $_POST ["le_check"] ) and $_POST ["le_check"] == 1) {
		$erreur = "";
		
		$tab_cles_date_heure_terrain = array ();
		$nbre_equipes_init = recup_1_element ( "k_format", "#__bl_matchday", "id", $_POST ["mday_coupe_phase_finale"] );
		$nbre_equipes = $nbre_equipes_init;
		for($i = 0; $i <= 64; $i ++)
			if (pow ( 2, $i ) == $nbre_equipes_init) {
				$puissance_de_2 = $i;
				break;
			}
		while ( $nbre_equipes >= 2 ) {
			for($e = 1; $e <= $nbre_equipes; $e ++) {
				if ($nbre_equipes == $nbre_equipes_init)
					if (! test_non_vide ( $_POST ["Equipe_$e"] )) {
						$erreur = "<font color=\"red\">Veuillez selectionner les equipes...</font><br>";
						break;
					}
				
				if (($e % 2) == 0) {
					$la_date = "date_" . $nbre_equipes . "_" . $e;
					$l_heure = "heure_" . $nbre_equipes . "_" . $e;
					$le_terrain = "terrain_choisit_" . $nbre_equipes . "_" . $e;
					
					if ($nbre_equipes == $nbre_equipes_init) {
						$tab_infos_rencontres [$nbre_equipes] [($e - 1)] ["equipe2"] = $_POST ["Equipe_$e"];
						$chaine_pour_requete_liste_joueurs .= $_POST ["Equipe_$e"] . ",";
					} else
						$tab_infos_rencontres [$nbre_equipes] [($e - 1)] ["equipe2"] = 0;
					
					$tab_infos_rencontres [$nbre_equipes] [($e - 1)] ["date"] = $_POST ["$la_date"];
					$tab_infos_rencontres [$nbre_equipes] [($e - 1)] ["heure"] = $_POST ["$l_heure"];
					$tab_infos_rencontres [$nbre_equipes] [($e - 1)] ["terrain"] = $_POST ["$le_terrain"];
					
					if (! test_non_vide ( $_POST ["$l_heure"] ) or ! test_non_vide ( $_POST ["$la_date"] ) or ! test_non_vide ( $_POST ["$le_terrain"] )) {
						$erreur = "<font color=\"red\">Veuillez choisir les dates, heures et terrains des rencontres...</font><br>";
						break;
					} else {
						$la_cle_actuelle = $_POST ["$la_date"] . $_POST ["$l_heure"] . $_POST ["$le_terrain"];
						
						foreach ( $tab_cles_date_heure_terrain as $elt ) {
							if (strcmp ( $la_cle_actuelle, $elt ) == 0) {
								$erreur = "<font color=\"red\">Vous avez choisit une meme date, meme heure et meme terrain pour 2 rencontres...</font><br>";
								break;
							}
						}
						if (test_non_vide ( $erreur ))
							break;
						else
							$tab_cles_date_heure_terrain [] = $la_cle_actuelle;
					}
				} else {
					if ($nbre_equipes == $nbre_equipes_init) {
						$tab_infos_rencontres [$nbre_equipes] [$e] ["equipe1"] = $_POST ["Equipe_$e"];
						$chaine_pour_requete_liste_joueurs .= $_POST ["Equipe_$e"] . ",";
					} else
						$tab_infos_rencontres [$nbre_equipes] [$e] ["equipe1"] = 0;
				}
			}
			$nbre_equipes /= 2;
		}
		if (test_non_vide ( $erreur ))
			echo $erreur;
		else {
			$nbre_equipes = 2;
			$mday_differentes_phase_finale = $_POST ["mday_coupe_phase_finale"];
			$saison_de_la_coupe_phase_finale = recup_1_element ( "s_id", "#__bl_matchday", "id", $_POST ["mday_coupe_phase_finale"] );
			$k_ordering = 0;
			$k_stage = $puissance_de_2;
			while ( $nbre_equipes <= $nbre_equipes_init ) {
				for($i = 1; $i <= ($nbre_equipes - 1); $i = ($i + 2)) {
					if (($nbre_equipes - 1) == 1)
						$k_title = "Finale";
					else
						$k_title = "1/" . ($nbre_equipes / 2);
					
					ajout_match ( $mday_differentes_phase_finale, $tab_infos_rencontres [$nbre_equipes] [$i] ["equipe1"], $tab_infos_rencontres [$nbre_equipes] [$i] ["equipe2"], $tab_infos_rencontres [$nbre_equipes] [$i] ["date"], $tab_infos_rencontres [$nbre_equipes] [$i] ["heure"], $tab_infos_rencontres [$nbre_equipes] [$i] ["terrain"], $k_ordering, $k_title, $k_stage );
					
					$k_ordering ++;
				}
				$k_stage --;
				$k_ordering = 0;
				$nbre_equipes *= 2;
				/*
				 * if ($nbre_equipes>1){
				 * if ($nbre_equipes==2)
				 * $titre="Finale";
				 * else $titre="Phase : 1/".($nbre_equipes/2)." finale";
				 * $mday_differentes_phase_finale=ajout_match_day(0,$titre,$saison_de_la_coupe_phase_finale,$nbre_equipes);
				 * }
				 */
			}
			foreach ( joueurs_des_equipes ( substr ( $chaine_pour_requete_liste_joueurs, 0, - 1 ) ) as $liste_joueurs ) {
				ajout_joueurs_dans_saison ( $liste_joueurs->team_id, $liste_joueurs->id, $saison_de_la_coupe_phase_finale );
				$tab_equipes [] = $liste_joueurs->team_id;
			}
			$tab_equipes_sans_doublons = array_unique ( $tab_equipes );
			foreach ( $tab_equipes_sans_doublons as $une_equipe )
				ajout_equipe_dans_saison ( $saison_de_la_coupe_phase_finale, $une_equipe );
			$ajout_terminer = 1;
		}
	}
	
	if ((isset ( $_POST ["liste_tourn"] ) and $_POST ["liste_tourn"] == 0) or (isset ( $_POST ["nom_tournoi"] ) and $_POST ["nom_tournoi"] != "")) {
		if (isset ( $_POST ["nom_tournoi"] ) and $_POST ["nom_tournoi"] != "") {
			
			if (isset ( $_POST ["Champ"] )) {
				
				$puissance_de_2 = 0;
				$nbre_equipes_phase_finale = ($_POST ["nbre_vainq_par_poule"] * $_POST ["nbre_poules"]);
				for($i = 0; $i <= 6; $i ++)
					if (pow ( 2, $i ) == $nbre_equipes_phase_finale)
						$puissance_de_2 = $i;
				
				if ($_POST ["Coupe"] == 1 or ($puissance_de_2 > 0 and ($nbre_equipes_phase_finale == $_POST ["format_coupe"]))) {
					
					if ($_POST ["Champ"] == 1) {
						
						$tourn_champ = ajout_tournoi ( $_POST ["nom_tournoi"] . " - Championnat", 0 );
						
						$saison_champ = ajout_saison ( "Championnat", $tourn_champ, ($_POST ["nbre_equipes_par_div"] * $_POST ["nbre_div"]) );
						
						for($div = 1; $div <= $_POST ["nbre_div"]; $div ++)
							ajout_groupe ( "Division " . $div, $saison_champ );
					}
					
					if ($_POST ["Coupe"] == 3) {
						
						$tourn_coupe_1er = ajout_tournoi ( $_POST ["nom_tournoi"] . " - Premier tour Coupe", 0 );
						
						$saison_coupe = ajout_saison ( "Eliminatoires Coupe", $tourn_coupe_1er, ($_POST ["nbre_equipes_par_poule"] * $_POST ["nbre_poules"]) );
						
						for($poule = 1; $poule <= $_POST ["nbre_poules"]; $poule ++)
							ajout_groupe ( "Poule " . $poule, $saison_coupe );
					}
					
					header ( "Location: gen" );
				} else {
					if ($puissance_de_2 == 0)
						echo "<br>Le produit (Nombre de poule)X(Nombre de vainqueur par poule) = " . "" . ($_POST ["nbre_vainq_par_poule"] * $_POST ["nbre_poules"]) . " ne s'ecrit pas sous la forme d'une puissance de 2.<br><br>";
					if ($nbre_equipes_phase_finale != $_POST ["format_coupe"])
						echo "Le format de la coupe (" . $_POST ["format_coupe"] . " &eacute;quipes) ne correspond pas au nombre d'&eacute;quipes " . " en phase finale (" . $nbre_equipes_phase_finale . " &eacute;quipes).<br><br>";
					
					echo "La coupe ne peut pas &ecirc;tre cr&eacute;&eacute;e.<br>";
				}
			}
		} else {
			
			// // CREATION d'un nouveau TOURNOI
			
			echo "<FORM name=\"nouveau_tourn\"  class=\"submission box\" action=\"gen\" method=post >";
			echo "Nom du nouveau tournoi<input type=text name=nom_tournoi><br>";
			echo "<H3><input type=\"checkbox\" name=\"Champ\" value=\"1\">Championnat</H3>";
			echo "Nombre de divisions <select name=\"nbre_div\">";
			for($i = 1; $i <= 10; $i ++)
				echo "<option value=\"" . $i . "\" >" . $i . "</option>";
			echo "</select> ";
			echo "<br>Nombre d'&eacute;quipes/division <select name=\"nbre_equipes_par_div\">";
			for($i = 2; $i <= 10; $i ++)
				echo "<option value=\"" . (2 * $i) . "\" >" . (2 * $i) . "</option>";
			echo "</select>";
			echo "<H3>Coupe</H3>";
			echo "Type de coupe <select name=\"Coupe\">" . "<option value=\"3\">Coupe avec poules</option>" . "<option value=\"2\">Coupe sans poules</option>" . "<option value=\"1\">Sans coupe</option></select><br>";
			echo "Format de la coupe <select name=\"format_coupe\">" . "<option value=\"4\">1/2</option>" . "<option value=\"8\">1/4</option>" . "<option value=\"16\">1/8</option>" . "<option value=\"32\">1/16</option>" . "<option value=\"64\">1/32</option></select><br>";
			echo "Nombre de poules <select name=\"nbre_poules\">";
			for($i = 1; $i <= 15; $i ++)
				echo "<option value=\"" . (2 * $i) . "\" >" . (2 * $i) . "</option>";
			echo "</select> <br> ";
			echo "Nombre d'&eacute;quipes/poule<select name=\"nbre_equipes_par_poule\">";
			for($i = 2; $i <= 36; $i ++)
				echo "<option value=\"" . $i . "\" >" . $i . "</option>";
			echo "</select> <br> ";
			echo "Nombre d'&eacute;quipes/poule qualifi&eacute;es pour la phase finale<select name=\"nbre_vainq_par_poule\">";
			for($i = 1; $i <= 30; $i ++)
				echo "<option value=\"" . $i . "\" >" . $i . "</option>";
			echo "</select>";
			
			echo "<br><input name=\"checkbox\" type=\"submit\"  value=\"Creer le nouveau tournoi\" >";
			echo "</form>";
		}
	} else {
		
		if (test_non_vide ( $_POST ["date_debut"] ))
			$date_debut = $_POST ["date_debut"];
		
		if (test_non_vide ( $_POST ["date_reprise"] ))
			$date_reprise = $_POST ["date_reprise"];
		
		echo "<FORM name=\"form\"  class=\"submission box\" action=\"gen\" method=post >";
		
		echo "Selectionner un tournoi<select name=\"liste_tourn\" onChange=\"recharger('','')\">";
		echo "<option value=\"\" ></option><option value=\"0\" >Nouveau...</option>";
		$nbre_equipes_inscrites_a_la_saison = 0;
		foreach ( liste_tourn ( "", "", " nom_tourn " ) as $liste_tourn ) {
			$le_nbre_equipes_par_groupe = (nbre_equipes_dans_saison ( $liste_tourn->id_saison ) / nbre_groupes_dans_saison ( $liste_tourn->id_saison ));
			
			echo "<option value=\"" . $liste_tourn->id_tourn . "_" . $liste_tourn->id_saison . "_" . $liste_tourn->gid . "_" . $liste_tourn->nbre_equipes . "_" . $le_nbre_equipes_par_groupe . "_" . $liste_tourn->nbre_equipes_total . "\" ";
			if (($id_tourn . "_" . $id_groupe) == ($liste_tourn->id_tourn . "_" . $liste_tourn->gid)) {
				echo " SELECTED ";
				$nbre_equipes_manquantes = ($le_nbre_equipes_par_groupe - $liste_tourn->nbre_equipes);
			}
			
			echo ">" . $liste_tourn->nom_tourn . " (" . $liste_tourn->nom_saison . " - " . $liste_tourn->group_name . " - " . $liste_tourn->nbre_equipes . "/" . $le_nbre_equipes_par_groupe . " equipes)</option>";
			
			if ($id_saison == $liste_tourn->id_saison)
				$nbre_equipes_inscrites_a_la_saison += $liste_tourn->nbre_equipes;
		}
		foreach ( liste_tourn ( " and t.t_type=1 ", 1 ) as $liste_tourn ) {
			$le_nbre_equipes_par_groupe = $liste_tourn->k_format;
			
			echo "<option value=\"" . $liste_tourn->id_tourn . "_" . $liste_tourn->id_saison . "_0_0_" . $le_nbre_equipes_par_groupe . "_0\" ";
			if (($id_tourn . "_0") == ($liste_tourn->id_tourn . "_0"))
				echo " SELECTED ";
			
			echo ">" . $liste_tourn->nom_tourn . " (" . $liste_tourn->nom_saison . " - " . $le_nbre_equipes_par_groupe . " equipes)</option>";
		}
		echo "</select>";
		if (test_non_vide ( $id_tourn ))
			echo " <a title=\"Supprimer ce tournoi\" onclick=\"recharger('Confirmez la suppression de ce tournoi','gen?suppr_tourn=$id_tourn')\">" . "<img src=\"images/stories/supprimer.png\" ></a>";
			// echo "<br><br><a href=\"index.php/accueil/gen?suppr_tourn=".$id_tourn."\" >Supprimer ce tournoi ?</a>";
		
		if (isset ( $id_saison ) and ($nbre_equipes_inscrites_a_la_saison == $nbre_equipes_par_tourn) and (journees_generes ( $id_saison ) == 0)) {
			echo "<br><br>Selectionnez la date de debut du tournoi ainsi que la date de reprise pour les matchs retours.<br><br>" . "Date de debut : <input type=\"date\" name=\"date_debut\" value=\"" . $date_debut . "\">";
			if (isset ( $_POST ["heure_debut"] ))
				$heure_debut_complete = $_POST ["heure_debut"];
			else
				$heure_debut_complete = $_GET ["heure_debut"];
			list ( $heure_debut, $minutes_debut ) = explode ( ':', $heure_debut_complete );
			echo "<select name=\"heure_debut\">";
			for($i = 9; $i <= 21; $i ++) {
				if (($heure_debut == $i))
					$select_heure = " selected ";
				else
					$select_heure = "";
				echo "<option value=\"" . $i . ":00\" \"" . $select_heure . "\" >" . $i . "h00</option>";
			}
			echo "</select>";
			
			echo "- Date de reprise : <input type=\"date\" name=\"date_reprise\" value=\"" . $date_reprise . "\"><br>";
			echo "Type de matchs " . " <select name=\"aller_retour\"><option value=\"1\" >allers/retours</option>" . " <option value=\"2\" >allers</option></select><br>";
		}
		
		if (isset ( $id_saison )) {
			if ($nbre_equipes_manquantes > 0) {
				// echo "---/".$nbre_equipes_manquantes."/---".$_POST["Groupe"]."<br>";
				if (isset ( $_POST ["Groupe"] )) {
					$nbre_equipes_cochees = 0;
					for($i = $_POST ["min"]; $i <= $_POST ["max"]; $i ++)
						if ($_POST ["Equipe_$i"] == $i)
							$nbre_equipes_cochees ++;
					
					if ($nbre_equipes_cochees <= $nbre_equipes_manquantes) {
						for($i = $_POST ["min"]; $i <= $_POST ["max"]; $i ++)
							if ($_POST ["Equipe_$i"] == $i) {
								ajout_equipe_dans_groupe ( $_POST ["Groupe"], $_POST ["Equipe_$i"] );
								ajout_equipe_dans_saison ( $id_saison, $_POST ["Equipe_$i"] );
							}
					} else
						echo "<br><font color=red>Nombre d'equipes selectionn&eacute;es (" . $nbre_equipes_cochees . ") est superieur au nombre d equipes manquantes (" . $nbre_equipes_manquantes . ").</font><br>";
				} else {
					
					$resultat_groups_sans_equipes = groups_sans_equipes ( $id_groupe );
					
					if (test_non_vide ( $resultat_groups_sans_equipes ) and ($nbre_equipes_manquantes > 0)) {
						echo "<input type=\"hidden\" name=\"Groupe\" value=\"" . $resultat_groups_sans_equipes->id . "\">";
						
						echo "<br><br><u>Selectionner les " . $nbre_equipes_manquantes . " equipes qui joueront en <b><font color=red>" . $resultat_groups_sans_equipes->group_name . "</font> :</u></b><br>";
						$min = 0;
						$max = 0;
						foreach ( equipes_sans_groups ( $id_saison ) as $equipes_sans_groups ) {
							if ($equipes_sans_groups->id > $max)
								$max = $equipes_sans_groups->id;
							if ($min == 0 or $equipes_sans_groups->id < $min)
								$min = $equipes_sans_groups->id;
							echo "<br><input type=\"checkbox\" name=\"Equipe_" . $equipes_sans_groups->id . "\" value=\"" . $equipes_sans_groups->id . "\">" . $equipes_sans_groups->t_name . "";
						}
						echo "<input type=\"hidden\" name=\"min\" value=\"" . $min . "\">";
						echo "<input type=\"hidden\" name=\"max\" value=\"" . $max . "\">";
						$texte_bouton = "Ajouter ces &eacute;quipes";
						
						echo "<br>";
					}
				}
			} else {
				if (test_non_vide ( $date_debut ) and test_non_vide ( $date_reprise ) and (journees_generes ( $id_saison ) == 0)) {
					$tab_horaires_pref = array ();
					$tab_nbre_demandes_par_horaires = array ();
					
					remplir_tableaux_des_preferences ( $tab_horaires_pref, $tab_nbre_demandes_par_horaires );
					
					echo "<br><h3>Nombre d'&eacute;quipes par horaires pr&eacute;f&eacute;r&eacute;s</h3>";
					foreach ( $tab_nbre_demandes_par_horaires as $demandes_par_horaires )
						echo "<u>" . $demandes_par_horaires ["horaire"] . "h :</u> " . $demandes_par_horaires ["nbre_total_demandes"] . " equipes.<br>";
					
					$tab_attribution_terrain_creneau_date = array ();
					$tab_num_match_deja_attribuer = array ();
					$tab_temp_date_journee = array ();
					$tab_matchs = array ();
					
					$heure_fin = decaler_heure ( $_POST ["heure_debut"], (ceil ( $nbre_equipes_par_tourn / 2 / $nbre_terrains_fif ) * 60) ); // /2 pour obtenir nbre match et /4 terrains
					list ( $heure_debut_seule, $minutes_debut_seules ) = explode ( ":", $_POST ["heure_debut"] );
					list ( $heure_fin_seule, $minutes_fin_seules ) = explode ( ":", $heure_fin );
					
					// echo "<br>heure deb:".$heure_debut_seule." - heure fin ".$heure_fin_seule." - decal minutes ".(ceil($nbre_equipes_par_tourn/2/$nbre_terrains_fif)*60);
					
					for($passage = 1; $passage <= 4; $passage ++) {
						// echo "<br>passage:".$passage;
						
						foreach ( liste_groups ( $id_saison ) as $liste_groupe ) {
							$nbre_equipes = $liste_groupe->nbre_equipes;
							// echo "<br><hr><br>".$nbre_equipes." equipes du groupe".$liste_groupe->id_du_groupe;
							
							if ($nbre_equipes % 2 == 0) {
								$compt = 1;
								foreach ( liste_equipes_du_groupe ( $liste_groupe->id_du_groupe ) as $liste_equipes_du_groupe ) {
									$chaine_pour_requete_liste_joueurs .= $liste_equipes_du_groupe->id_equipe . ",";
									$tab_equipe [$compt] ["id"] = $liste_equipes_du_groupe->id_equipe;
									$tab_equipe [$compt] ["nom"] = $liste_equipes_du_groupe->nom_equipe;
									
									// 4 est le nbre de terrains pour determiner le nombre de creneaux partager
									if ($passage == 1)
										$tab_horaires_pref [$liste_equipes_du_groupe->id_equipe] ["reste_hor_pref"] = floor ( (($nbre_equipes - 1) * $nbre_terrains_fif) / $tab_nbre_demandes_par_horaires [$tab_horaires_pref [$liste_equipes_du_groupe->id_equipe] ["hor_pref"]] ["nbre_total_demandes"] );
										
										// echo "<br>Equipe : ".$liste_equipes_du_groupe->nom_equipe." (".$liste_equipes_du_groupe->id_equipe." - ".$tab_horaires_pref[$liste_equipes_du_groupe->id_equipe]["hor_pref"]."h - reste:".$tab_horaires_pref[$liste_equipes_du_groupe->id_equipe]["reste_hor_pref"].")";
									$compt ++;
								}
								
								repartir_les_rencontres_par_journees ( $nbre_equipes, $tab_journee );
								// echo "<br><br>+++++++++++++MATCHS ALLERS<br>";
								$num_match = 1;
								
								for($Equipe_A = 1; $Equipe_A <= $nbre_equipes; $Equipe_A ++) {
									
									for($Equipe_B = 1; $Equipe_B <= $nbre_equipes; $Equipe_B ++) {
										if ($tab_journee [$Equipe_A] [$Equipe_B] != "" and ! in_array ( $liste_groupe->id_du_groupe . "_" . $num_match, $tab_num_match_deja_attribuer )) {
											$id_journee_match = $tab_journee [$Equipe_A] [$Equipe_B];
											$tab_date_journee [$id_journee_match] = decaler_jour ( $date_debut, ($id_journee_match - 1) * 7 );
											$tab_date_journee [($id_journee_match + $nbre_equipes - 1)] = decaler_jour ( $date_reprise, ($id_journee_match - 1) * 7 );
											
											list ( $terrain, $heure ) = explode ( '_', attribution_terrain_creneau ( $tab_attribution_terrain_creneau_date, $tab_equipe [$Equipe_A] ["id"], $tab_equipe [$Equipe_B] ["id"], $id_journee_match, $num_match, $tab_horaires_pref, $passage, $heure_debut_seule, $heure_fin_seule ) );
											
											if ($terrain != "" and $heure != "") {
												$tab_matchs [] = array (
														"journee" => $id_journee_match,
														"heure" => $heure . ":00",
														"terrain" => "T" . $terrain,
														"poule" => $liste_groupe->id_du_groupe,
														"num_match" => $num_match,
														"date" => $tab_date_journee [$id_journee_match],
														"id_equipe_A" => $tab_equipe [$Equipe_A] ["id"],
														"id_equipe_B" => $tab_equipe [$Equipe_B] ["id"],
														"nom_A" => $tab_equipe [$Equipe_A] ["nom"],
														"nom_B" => $tab_equipe [$Equipe_B] ["nom"] 
												);
												// Matchs retour
												if ($_POST ["aller_retour"] == 1)
													$tab_matchs [] = array (
															"journee" => ($id_journee_match + $nbre_equipes - 1),
															"heure" => $heure . ":00",
															"terrain" => "T" . $terrain,
															"poule" => $liste_groupe->id_du_groupe,
															"num_match" => $num_match,
															"date" => $tab_date_journee [($id_journee_match + $nbre_equipes - 1)],
															"id_equipe_A" => $tab_equipe [$Equipe_A] ["id"],
															"id_equipe_B" => $tab_equipe [$Equipe_B] ["id"],
															"nom_A" => $tab_equipe [$Equipe_A] ["nom"],
															"nom_B" => $tab_equipe [$Equipe_B] ["nom"] 
													);
												
												$tab_attribution_terrain_creneau_date [$id_journee_match] [$heure] [$terrain] = $num_match;
												$tab_num_match_deja_attribuer [] = $liste_groupe->id_du_groupe . "_" . $num_match;
												// echo "<br>".$liste_groupe->id_du_groupe."_".$num_match."<br>";
											}
										}
										$num_match ++;
									}
								}
							}
						}
					}
					if ($nbre_equipes % 2 == 0 and (journees_generes ( $id_saison ) == 0)) {
						$tab_journee = array ();
						$tab_terrain_matchs = array ();
						$tab_heure_matchs = array ();
						echo "<H3>Les journ&eacute;es</H3>";
						
						$mday = (1 + dernier_id_mday ());
						$decalage = 0;
						$decalage_des_journees_retour = 0;
						$nbre_creneaux_pb_dispo = 0;
						$tab_cles_date_heure_terrain = array ();
						
						for($i = 1; $i <= ($nbre_equipes - 1); $i ++) {
							
							if (test_non_vide ( $_POST ["valider_dates"] ) and $_POST ["valider_dates"] == 1) {
								ajout_match_day ( $mday, "Journee " . $i, $id_saison );
								$readonly = " readonly=\"readonly\" ";
							}
							
							echo "<table width=\"100%\"><tr><td colspan=\"4\" nowrap>-------------ALLER----- Journ&eacute;e " . $i . " : <input type=\"date\" name=\"date_journee_" . $i . "\" " . $readonly . " value=\"";
							if (test_non_vide ( $_POST ["date_journee_$i"] ) and ($tab_date_journee [$i] != $_POST ["date_journee_$i"])) {
								$la_journee_allee = $_POST ["date_journee_$i"];
								$tab_date_journee [$i] = $_POST ["date_journee_$i"];
								$decalage += (diff_dates_en_jours ( $tab_date_journee [$i - 1], $tab_date_journee [$i] ) - 7);
							} else {
								$tab_date_journee [$i] = decaler_jour ( $tab_date_journee [$i], $decalage );
								$la_journee_allee = $tab_date_journee [$i];
							}
							
							echo $la_journee_allee . "\"><br>";
							
							$tab_journee [$i] = $mday;
							echo "</td></tr>";
							
							foreach ( $tab_matchs as $match )
								if ($match ["journee"] == $i) {
									$nom_du_select = "select_heure_" . $i . "_" . $match ["id_equipe_A"] . "_" . $match ["id_equipe_B"];
									
									if (test_non_vide ( $_POST ["$nom_du_select"] ))
										$old_heure = $_POST ["$nom_du_select"];
									else
										$old_heure = $match ["heure"];
									
									$le_select_heure = menu_deroulant_des_heures_avec_dispo_fif ( $nom_du_select, $old_heure, $match ["date"] );
									
									$nom_du_select_terrain = "select_terrain_" . $i . "_" . $match ["id_equipe_A"] . "_" . $match ["id_equipe_B"];
									
									if (test_non_vide ( $_POST ["$nom_du_select_terrain"] ))
										$old_terrain = $_POST ["$nom_du_select_terrain"];
									else
										$old_terrain = $match ["terrain"];
									
									$le_select_terrain = menu_deroulant_des_terrains_avec_dispo_fif ( $nom_du_select_terrain, $old_terrain, $match ["date"], $old_heure );
									
									if (! test_non_vide ( $le_select_terrain ))
										$erreur = "<font color=\"red\"><b><u>Pb " . ($nbre_creneaux_pb_dispo + 1) . ":</u></b>Aucun terrain dispo</font>";
									else {
										$la_cle_actuelle = $match ["date"] . $old_heure . $old_terrain;
										
										foreach ( $tab_cles_date_heure_terrain as $elt ) {
											if (strcmp ( $la_cle_actuelle, $elt ) == 0) {
												$erreur = "<font color=\"red\"><b><u>Pb " . ($nbre_creneaux_pb_dispo + 1) . ":</u></b> m&ecirc;me heure et terrain selectionn&eacute;s au-dessus</font><br>";
												break;
											} else
												$erreur = "";
										}
										$tab_cles_date_heure_terrain [] = $la_cle_actuelle;
									}
									
									if (test_non_vide ( $erreur ))
										$nbre_creneaux_pb_dispo ++;
									
									$tab_terrain_matchs ["$nom_du_select_terrain"] = $old_terrain;
									$tab_heure_matchs ["$nom_du_select"] = $old_heure;
									
									echo "<tr><td nowrap>" . nom_equipe ( $match ["id_equipe_A"] ) . "</td><td nowrap>" . nom_equipe ( $match ["id_equipe_B"] ) . "</td><td>" . $le_select_heure . "</td><td>" . $le_select_terrain . "</td><td width=\"100%\">" . $erreur . "</td></tr>";
								}
							
							echo "</table><HR>";
							// match retour
							if ($_POST ["aller_retour"] == 1) {
								$le_i_des_journees_retour = ($i + ($nbre_equipes - 1));
								
								if (test_non_vide ( $_POST ["valider_dates"] ) and $_POST ["valider_dates"] == 1) {
									ajout_match_day ( ($mday + ($nbre_equipes - 1)), "Journee " . $le_i_des_journees_retour, $id_saison );
									$readonly = " readonly=\"readonly\" ";
								}
								
								echo "<table width=\"100%\"><tr><td colspan=\"4\" align=\"right\" nowrap>-------------Retour-----";
								echo "Journ&eacute;e " . $le_i_des_journees_retour . " : <input type=\"date\" name=\"date_journee_" . $le_i_des_journees_retour . "\" " . $readonly . " value=\"";
								if (test_non_vide ( $_POST ["date_journee_$le_i_des_journees_retour"] ) and ($tab_date_journee [$le_i_des_journees_retour] != $_POST ["date_journee_$le_i_des_journees_retour"])) {
									$la_journee_retour = $_POST ["date_journee_$le_i_des_journees_retour"];
									$tab_date_journee [$le_i_des_journees_retour] = $_POST ["date_journee_$le_i_des_journees_retour"];
									$decalage_des_journees_retour += (diff_dates_en_jours ( $tab_date_journee [$le_i_des_journees_retour - 1], $tab_date_journee [$le_i_des_journees_retour] ) - 7);
								} else {
									$tab_date_journee [$le_i_des_journees_retour] = decaler_jour ( $tab_date_journee [$le_i_des_journees_retour], $decalage_des_journees_retour );
									$la_journee_retour = $tab_date_journee [$le_i_des_journees_retour];
								}
								
								echo $la_journee_retour . "\"><br>";
								
								$tab_journee [$le_i_des_journees_retour] = $mday + ($nbre_equipes - 1);
								echo "</td></tr>";
								foreach ( $tab_matchs as $match )
									if ($match ["journee"] == $le_i_des_journees_retour) {
										$nom_du_select = "select_heure_" . $le_i_des_journees_retour . "_" . $match ["id_equipe_A"] . "_" . $match ["id_equipe_B"];
										
										if (test_non_vide ( $_POST ["$nom_du_select"] ))
											$old_heure = $_POST ["$nom_du_select"];
										else
											$old_heure = $match ["heure"];
										
										$le_select_heure = menu_deroulant_des_heures_avec_dispo_fif ( $nom_du_select, $old_heure, $match ["date"] );
										
										$nom_du_select_terrain = "select_terrain_" . $le_i_des_journees_retour . "_" . $match ["id_equipe_A"] . "_" . $match ["id_equipe_B"];
										if (test_non_vide ( $_POST ["$nom_du_select_terrain"] ))
											$old_terrain = $_POST ["$nom_du_select_terrain"];
										else
											$old_terrain = $match ["terrain"];
										$le_select_terrain = menu_deroulant_des_terrains_avec_dispo_fif ( $nom_du_select_terrain, $old_terrain, $match ["date"], $old_heure );
										
										if (! test_non_vide ( $le_select_terrain ))
											$erreur = "<font color=\"red\"><b><u>Pb " . ($nbre_creneaux_pb_dispo + 1) . ":</u></b> Aucun terrain dispo</font>";
										else {
											$la_cle_actuelle = $match ["date"] . $old_heure . $old_terrain;
											
											foreach ( $tab_cles_date_heure_terrain as $elt ) {
												if (strcmp ( $la_cle_actuelle, $elt ) == 0) {
													$erreur = "<font color=\"red\"><b><u>Pb " . ($nbre_creneaux_pb_dispo + 1) . ":</u></b> m&ecirc;me heure et terrain selectionn&eacute;s au-dessus</font>";
													break;
												} else
													$erreur = "";
											}
											$tab_cles_date_heure_terrain [] = $la_cle_actuelle;
										}
										
										if (test_non_vide ( $erreur ))
											$nbre_creneaux_pb_dispo ++;
										
										$tab_terrain_matchs ["$nom_du_select_terrain"] = $old_terrain;
										$tab_heure_matchs ["$nom_du_select"] = $old_heure;
										
										echo "<tr><td width=\"100%\" align=\"right\">" . $erreur . "</td><td nowrap>" . nom_equipe ( $match ["id_equipe_A"] ) . "</td><td nowrap>" . nom_equipe ( $match ["id_equipe_B"] ) . "</td><td>" . $le_select_heure . "</td><td><b>" . $le_select_terrain . "</td></tr>";
									}
								
								echo "</table><HR>";
							}
							
							$mday ++;
						}
						$texte_bouton = "G&eacute;n&eacute;rer les matchs";
						if (! (test_non_vide ( $_POST ["valider_dates"] ) and $_POST ["valider_dates"] == 1) and $nbre_creneaux_pb_dispo == 0)
							echo "<input type=\"checkbox\" name=\"valider_dates\" value=\"1\">Cochez pour valider les dates et generer les matchs correspondants" . "<br><font color=red><b>Ne jamais cocher</b> sans avoir au pr&eacute;alable generer les matchs et verifier qu'aucune indisponilit&eacute; n'est signal&eacute;e !</font>";
						else
							$texte_bouton = "Verifier de nouveau les dispos";
						
						if ($nbre_creneaux_pb_dispo != 0)
							echo "<font color=yellow size=4>Pb dispo dates sur FIF sur " . $nbre_creneaux_pb_dispo . " rencontres</font>";
					} else
						echo "<br><br>Erreur : Le nombre d'equipes est impair<br><br>";
				}
			}
		}
		if (isset ( $_POST ["liste_tourn"] ) and $nbre_creneaux_pb_dispo == 0) {
			asort ( $tab_matchs );
			if (count ( $tab_matchs ) != 0 and ! (test_non_vide ( $_POST ["valider_dates"] ) and $_POST ["valider_dates"] == 1) and $nbre_creneaux_pb_dispo == 0)
				echo "<br>" . count ( $tab_matchs ) . " matchs seront g&eacute;n&eacute;r&eacute;s.<br><hr>";
			
			foreach ( $tab_matchs as $match )
				if (test_non_vide ( $_POST ["valider_dates"] ) and $_POST ["valider_dates"] == 1) {
					$nom_du_select = "select_heure_" . $match ["journee"] . "_" . $match ["id_equipe_A"] . "_" . $match ["id_equipe_B"];
					$nom_du_select_terrain = "select_terrain_" . $match ["journee"] . "_" . $match ["id_equipe_A"] . "_" . $match ["id_equipe_B"];
					ajout_match ( $tab_journee [$match ["journee"]], $match ["id_equipe_A"], $match ["id_equipe_B"], $tab_date_journee [$match ["journee"]], $tab_heure_matchs ["$nom_du_select"], $tab_terrain_matchs ["$nom_du_select_terrain"] );
				}
			
			if (test_non_vide ( $_POST ["valider_dates"] ) and $_POST ["valider_dates"] == 1)
				foreach ( joueurs_des_equipes ( substr ( $chaine_pour_requete_liste_joueurs, 0, - 1 ) ) as $liste_joueurs )
					ajout_joueurs_dans_saison ( $liste_joueurs->team_id, $liste_joueurs->id, $id_saison );
		}
		if ((! test_non_vide ( $texte_bouton ) or (test_non_vide ( $_POST ["valider_dates"] ) and $_POST ["valider_dates"] == 1)))
			$texte_bouton = "Continuer...";
		if (isset ( $id_saison ) and ! test_non_vide ( $_POST ["mday_coupe_phase_finale"] ))
			echo "<br> <input name=\"valide\" type=\"submit\"  value=\"" . $texte_bouton . "\" >";
		echo "</form>";
	}
	
	if (! test_non_vide ( $ajout_terminer )) {
		echo "<div id='saisie' class='content-to-load'>";
		echo "<FORM name=\"gen_phase_finale_coupe\"  class=\"submission box\" action=\"gen\" method=post >";
		echo "<input type=\"hidden\" name=\"liste_tourn\" value=\"" . $_POST ["liste_tourn"] . "\">";
		
		if (test_non_vide ( $_POST ["liste_tourn"] ) and (substr ( recup_1_element ( "group_name", "#__bl_groups", "id", $id_groupe ), 0, 5 ) == "Poule") and existe_tourn ( substr ( recup_1_element ( "name", "#__bl_tournament", "id", $id_tourn ), 0, - 21 ) . " - Phase finale Coupe" ) == 0 and ! (test_non_vide ( $resultat_groups_sans_equipes ) and ($nbre_equipes_manquantes > 0)) and ! (isset ( $id_saison ) and ($nbre_equipes_inscrites_a_la_saison == $nbre_equipes_par_tourn) and (journees_generes ( $id_saison ) == 0))) {
			// and nbre_match_non_joues_d_une_saison($id_saison)==0){
			echo "Nombre d'&eacute;quipes/poule qualifi&eacute;es pour la phase finale<select name=\"nbre_vainq_par_poule\">";
			for($i = 2; $i <= nbre_equipes_par_groups ( $id_saison, $id_groupe ); $i = 2 * $i)
				echo "<option value=\"" . $i . "\" >" . $i . "</option>";
			echo "</select>";
			
			echo "<br><input name=\"checkbox\" type=\"submit\"  value=\"Cr&eacute;er la phase finale\" >";
		}
		if (test_non_vide ( $_POST ["nbre_vainq_par_poule"] ) or test_non_vide ( $_POST ["mday_coupe_phase_finale"] )) {
			
			if (test_non_vide ( $_POST ["mday_coupe_phase_finale"] ))
				$mday_coupe_phase_finale = $_POST ["mday_coupe_phase_finale"];
			
			$nbre_equipes_init = recup_1_element ( "k_format", "#__bl_matchday", "id", $mday_coupe_phase_finale );
			$nbre_equipes = $nbre_equipes_init;
			
			while ( $nbre_equipes >= 2 ) {
				echo "<H3>";
				if ($nbre_equipes > 2)
					echo "1/" . ($nbre_equipes / 2);
				echo "finale</H3>";
				for($e = 1; $e <= $nbre_equipes; $e ++) {
					$liste_option = "";
					if ($nbre_equipes == $nbre_equipes_init) {
						foreach ( liste_equipes_du_groupe () as $liste_equipes ) {
							if (test_non_vide ( $_POST ["Equipe_$e"] ) and $_POST ["Equipe_$e"] == $liste_equipes->id_equipe)
								$select_team = " selected ";
							else
								$select_team = "";
							$liste_option .= "<option value=\"" . $liste_equipes->id_equipe . "\" " . $select_team . ">" . $liste_equipes->nom_equipe . "</option>";
						}
						
						echo "<select name=\"Equipe_" . $e . "\"><option value=\"\" ></option>" . $liste_option . "</select><br>";
					}
					if (($e % 2) == 0) {
						$la_date = "date_" . $nbre_equipes . "_" . $e;
						$l_heure = "heure_" . $nbre_equipes . "_" . $e;
						$le_terrain = "terrain_choisit_" . $nbre_equipes . "_" . $e;
						
						$date_saisie = $_POST ["$la_date"];
						
						if (isset ( $_POST ["$la_date"] ) && $_POST ["$la_date"] != null) {
							$date_saisie = str_replace ( '/', '-', $_POST ["$la_date"] );
							
							$date_saisie = date ( "Y-m-d", strtotime ( $date_saisie ) );
						}
						
						echo "Date du match <input type=\"date\" name=\"" . $la_date . "\" onChange=\"updateDateHeureTerrain('date'," . $e . "," . $nbre_equipes . ",'" . $date_saisie . "','" . $_POST ["$l_heure"] . "','" . $_POST ["$le_terrain"] . "')\" value=\"" . $date_saisie . "\">";
						
						echo "<div id='choix_heure_terrain' >";
						echo menu_deroulant_des_heures_avec_dispo_fif ( $l_heure, $_POST ["$l_heure"], $date_saisie, " onChange=\"updateDateHeureTerrain('heure'," . $e . "," . $nbre_equipes . ")\" " );
						
						echo menu_deroulant_des_terrains_avec_dispo_fif ( $le_terrain, $_POST ["$le_terrain"], $date_saisie, $_POST ["$l_heure"], " onChange=\"updateDateHeureTerrain('terrain'," . $e . "," . $nbre_equipes . ")\" " );
						echo "</div>";
						echo "<br><br>";
					}
				}
				$nbre_equipes /= 2;
			}
			
			echo "<input type=\"hidden\" name=\"mday_coupe_phase_finale\" value=\"" . $mday_coupe_phase_finale . "\">";
			echo "<br><input name=\"le_check\" type=\"checkbox\"  value=\"1\" > confirmer votre saisie";
			echo "<br><input name=\"checkbox\" type=\"submit\"  value=\"inscrire ces equipes\" >";
		}
		echo "</form>";
		echo "</div><div class='content-loading' style=\"background: white url('" . $siteURL . "/images/stories/loading_spinner.gif') center center  no-repeat; display:none;\"></div>";
	} else
		echo "Creation des matchs de la phase finale termin&eacute;e !<br>Reste plus qu'&agrave les valider dans FIF en allant dans TODAY !";
}
?>

<script type="text/javascript">
	

	function updateDateHeureTerrain(type,match,nb_equipes) {
		var la_date="date_" + nb_equipes + "_" + match;
		var l_heure="heure_" + nb_equipes + "_" + match;
		var le_terrain="terrain_choisit_" + nb_equipes + "_" + match;
		
		var date_saisie = document.getElementsByName(la_date)[0].value;
		var heure_saisie = document.getElementsByName(l_heure)[0].value;
		var terrain_saisi = document.getElementsByName(le_terrain)[0].value;

		$('.content-loading').show();
		$.ajax({
            type: 'post',
            data: { Type: type, Match:match, Nb_Equipes:nb_equipes, Date_Saisie:date_saisie, Heure_saisie:heure_saisie,Terrain_saisi:terrain_saisi},
            url: '../../index.php?option=com_championnat&task=UpdateDateHeureTerrain&format=raw',
            success: function(data) {
            	$('#choix_heure_terrain').html(data);
            },
            complete: function(){
                $('.content-loading').hide();
            },
            error: function() {
                alert('Error occured');
            }
          });		
	}
</script>