<?php
defined ( '_JEXEC' ) or die ( 'Access Deny' );
jimport ( 'joomla.application.component.controller' );
class ReservationController extends JControllerLegacy {

	function LoadResaTable() {
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/fonctions_module_reservation.php');
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/fonctions_gestion_user.php');
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/tarif_speciale/clienttarificationspeciale_controller.class.php');
		require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/libraries/ya2/tarif_speciale/groupetarificationspeciale_controller.class.php');
		
		$user = JFactory::getUser ();
		$db = JFactory::getDBO ();
		$config = JFactory::getConfig ();
		
		$ctsController = new ClientTarificationSpecialeController ();
		$gtsController = new GroupeTarificationSpecialeController ();
		
		if (est_min_agent ( $user )) {
			if (test_non_vide ( $_POST ["id_client"] ))
				$id_client = $_POST ["id_client"];
			else
				$id_client = $_GET ["id_client"];
		} else
			$id_client = idclient_du_user ();
		
		if (isset ( $user ) and (est_register ( $user )) and ($id_client != "")) {
			$requete_recup_client = "select id_client, prenom, nom, mobile1, fixe,commentaire from Client where id_client=" . $id_client . " order by nom,prenom,code_insee";
			$db->setQuery ( $requete_recup_client );
			$recup_client = $db->loadObject ();
			if (! test_non_vide ( $recup_client->nom ))
				header ( "Location: index.php/component/content/article?id=57&modif=1&id_client=" . $user->id . "" );
		}
		if (! test_non_vide ( $id_client ))
			header ( "Location: index.php?option=com_content&view=article&id=57" );
		
		$requete_recup_client = "select id_client, prenom, (select u.email from #__users as u where u.id=c.id_user) as courriel, c.nom as nom, mobile1, fixe,id_type_regroupement,tr.nom as type_reg, police " . " from Client as c LEFT JOIN Type_Regroupement as tr on c.id_type_regroupement=tr.id  where id_client=" . $id_client . " ";
		$db->setQuery ( $requete_recup_client );
		$recup_client = $db->loadObject ();
		
		$typeClient = $recup_client->type_reg . " " . $recup_client->nom_entite;
		
		$type_terrain = $_GET ["type_terrain"] . $_POST ["type_terrain"];
		$type_terrain_texte = '';
		
		$mode_resa = $_POST ["mode_resa"];
		
		$total_caution_actuel = recup_caution_total_client ( $id_client );
		
		if (test_non_vide ( $_POST ["nbre_terrains_a_reserver"] ))
			$nbre_terrains_a_reserver = $_POST ["nbre_terrains_a_reserver"];
		
		if (test_non_vide ( $_POST ["resa_mult"] ))
			$resa_mult = $_POST ["resa_mult"];
		
		if (isset ( $_POST ["duree_resa"] ))
			$duree_resa = $_POST ["duree_resa"];
		else {
			if (isset ( $_GET ["heure_fin_resa"] ))
				$duree_resa = duree_en_horaire ( diff_dates_en_minutes ( "", $heure_debut_resa, "", $_GET ["heure_fin_resa"] ) );
			else
				$duree_resa = $_GET ["duree_resa"];
		}
		
		if (test_non_vide ( $_POST ["num_resa"] )) {
			$num_resa = $_POST ["num_resa"];
			proprietaire_resa ( $num_resa );
		}
		
		$disable_submit_caution = "";
		$caution_insuffisante = false;
		
		$type_terain_disabled = '';
		switch ($type_terrain) {
			case 1 :
				$type_terrain_texte = 'Int&eacute;rieur';
				$type_terain_disabled = 'name="terrain_choisit"';
				break;
			case 2 :
				$type_terrain_texte = 'Ext&eacute;rieur';
				$type_terain_disabled = 'name="terrain_choisit"';
				break;
			case 3 :
				$type_terrain_texte = 'Loge VIP';
				$type_terain_disabled = 'name="terrain_choisit_select" disabled="true"';
				
				break;
			
			default :
				$type_terrain_texte = 'Int&eacute;rieur';
				$type_terain_disabled = 'name="terrain_choisit"';
				break;
		}
		$result = '';
		// //////////////////////////////////////
		// Résultat de la demande
		// //////////////////////////////////////
		$is_cautionable = false;
		if (isset ( $_POST ["date_input_0"] )) {
			$date = DateTime::createFromFormat ( "d/m/Y", $_POST ["date_input_0"] );
			$jour = $date->format ( "d" );
			$mois = $date->format ( "m" );
			$annee = $date->format ( "Y" );
			if ($mode_resa != 3)
				$is_cautionable = test_limite_nbre_resa_par_caution ( $date->format ( "Y-m-d" ), $id_client );
		} else {
			$date = DateTime::createFromFormat ( "d/m/Y", $_GET ["date_debut_resa"] );
			$jour = $date->format ( "d" );
			$mois = $date->format ( "m" );
			$annee = $date->format ( "Y" );
			if ($mode_resa != 3)
				$is_cautionable = test_limite_nbre_resa_par_caution ( $date->format ( "Y-m-d" ), $id_client );
		}
		
		$is_max_sans_acompte_ni_caution = false;
		if (isset ( $_POST ["date_input_0"] ) && $mode_resa == 3) {
			$date->sub ( new DateInterval ( 'P7D' ) );
			$is_max_sans_acompte_ni_caution = test_limite_nbre_resa_sans_caution_ni_acompte ( $date->format ( "Y-m-d" ), $id_client, $num_resa );
		}
		
		if (! isset ( $_GET ["modif"] )) {
			if (isset ( $_POST ["id_client"] ) and ($_POST ["id_client"] == "") and (est_min_agent ( $user ))) {
				echo "<br><font color=red>Veuillez selectionner un client.</font><br>";
			} else {
				if (isset ( $_POST ["heure_debut_resa"] ) or isset ( $_GET ["heure_debut_resa"] )) {
					
					echo "<br /><hr><br />";
					
					// //////////////////////////////////////////
					// /////// Si la resa provient d'une recherche avec date_input_0 ou d'une modif avec date_debut_resa
					// /////////////////////////////////////////////
					
					$date_saisie_Min = $annee . "-" . $mois . "-" . $jour;
					$date_saisie_Max = $annee . "-" . $mois . "-" . $jour;
					
					if (isset ( $_POST ["heure_debut_resa"] ))
						$heure_saisie_Min = $_POST ["heure_debut_resa"];
					else
						$heure_saisie_Min = $_GET ["heure_debut_resa"];
					
					$heure_saisie_Max = decaler_heure ( $heure_saisie_Min, duree_en_minutes ( $duree_resa ) );
					
					// ////////////////////////////////////////////
					// /// Le cas où la date est passée
					// /////////////////////////////////////////
					
					// if (diff_dates_en_minutes($date_saisie_Min,$heure_saisie_Min)>-2880)
					// echo "Attention : moins de 48h, pas de modifs possible... Tarif=acompte<br><br>";
					if (test_nuit_avant_ouverture ( $heure_saisie_Min ) == 1)
						$date_Min = decaler_jour ( $date_saisie_Min, 1 );
					else
						$date_Min = $date_saisie_Min;
					
					if (test_nuit_avant_ouverture ( $heure_saisie_Max ) == 1)
						$date_Max = decaler_jour ( $date_saisie_Max, 1 );
					else
						$date_Max = $date_saisie_Max;
					
					$position_date_saisie_date_du_jour = diff_dates_en_minutes ( $date_Min, $heure_saisie_Min );
					// echo $position_date_saisie_date_du_jour."mins";
					if (! test_valid_existence_date ( $date_Min ) or ((est_register ( $user )) and $position_date_saisie_date_du_jour > 0) or ((est_agent ( $user ) and $position_date_saisie_date_du_jour > 120))) {
						
						if (! test_valid_existence_date ( $date_Min ))
							echo " <font color=red>Erreur : la date est incorrecte.</font>";
						else {
							if ($position_date_saisie_date_du_jour > 1440) // si c'est pas le meme jour
								echo "La date <font color=red>" . date_longue ( $date_Min ) . "</font> est ant&eacute;rieure &agrave; aujourd'hui.";
							else
								echo "Vous avez s&eacute;lectionn&eacute; : <font color=red>" . $heure_saisie_Min . "</font>, heure d&eacute;j&agrave; pass&eacute;e ! <br />Il est " . date ( "H" ) . "h" . date ( "i" ) . ".";
						}
					} else {
						// //////////////////////////////////////////////
						// /////Recherche sur les agendas des terrains
						// //////////////////////////////////////////////
						
						if (test_horaires_ouverture ( decaler_heure ( $heure_saisie_Min, - 1 * 60 ), "" ) == 0)
							$horaire_Min = horaire_ouverture ();
						else
							$horaire_Min = decaler_heure ( $heure_saisie_Min, - 1 * 60 );
						
						if (test_horaires_ouverture ( "", decaler_heure ( $heure_saisie_Max, + 1 * 60 ) ) == 0)
							$horaire_Max = horaire_fermeture ();
						else
							$horaire_Max = decaler_heure ( $heure_saisie_Max, + 1 * 60 );
							
							// echo "<br>rempli avec :".$horaire_Min." - ".$horaire_Max;
						
						$infos_terrain = trouve_dispo ( $type_terrain, $date_saisie_Min, $heure_saisie_Min, $heure_saisie_Max, $num_resa );
						// echo "<br>terrain : ".$recup_id_cal_terrains->id_terrain.", dispo : ".$terrain_dispo;
						
						if (test_non_vide ( $_POST ["date_fin"] ))
							if (! test_non_vide ( $_POST ["Frequence"] ))
								echo "<font color=red>La frequence est obligatoire ! Si vous indiquez une date de fin.</font><br><br><br>";
							else {
								$date = DateTime::createFromFormat ( "d/m/Y", $_POST ["date_fin"] );
								$date_fin = $date->format ( "Y-m-d" );
								$diff_jours = diff_dates_en_jours ( $date_Min, $date_fin );
								// echo "diff_jours".$diff_jours."<br>";
								$nbre_de_jours_a_tester = $diff_jours / $_POST ["Frequence"];
								// echo "nbre_de_jours_a_tester".$nbre_de_jours_a_tester."<br>";
							}
						
						if (is_array ( $infos_terrain )) {
							?>
<FORM id="formulaire_resa" name="formulaire_resa" class="submission box"
	action="article?id=63" method="post" data-parsley-validate>
	<div id="list_de_trucs" class="table-responsive">
		<table class="zebra">
			<tr>
				<th>Date</th>
				<th>Heure</th>
				<th>Terrain</th>
				<th>Tarif</th>
			<?php if($mode_resa==1) { ?>
			<th>Tarif avec remise</th>
				<th>Acompte</th>
			<?php } elseif($mode_resa==2) { ?>
			<th>Tarif avec remise</th>
				<th>Caution</th>
			<?php } ?>
			<?php if($mode_resa==3 && $recup_client->police==1) { ?>
			<th>Tarif avec remise</th>
				<th>Acompte</th>
			<?php }?>
			<th>Terrain</th>
			</tr>
			<tr>
				<td nowrap><?
							
							echo date_longue ( $date_Min );
							?></td>
				<td>de <? echo  $heure_saisie_Min." &agrave; ".$heure_saisie_Max; ?></td>
				<td><?=$type_terrain_texte ?></td>
				<td>
								<?
							$tarif_special = - 1;
							$cts = $ctsController->GetClientTarificationSpecialeByClientId ( $id_client );
							if ($cts != null) {
								$gts = $gtsController->GetGroupeTarificationSpecialeById ( $cts->Getgtsid () );
								$tarif_special = $gts->Gettarifhc ();
							}
							$tarif = tarif ( $date_Min, $heure_saisie_Min, $heure_saisie_Max, $recup_client->id_type_regroupement, $recup_client->police, $type_terrain, $mode_resa, $tarif_special,$type_terrain_texte );
							$montant_total = $tarif ['tarif'];
							$montant_total_avec_remise = $tarif ['tarif_avec_remise'];
							echo $montant_total . "€";
							echo "<input name=\"montant_total" . coller_jma ( $date_Min ) . "\" type=\"hidden\"  value=\"" . $montant_total . "\">";
							echo "<input name=\"montant_total_avec_remise" . coller_jma ( $date_Min ) . "\" type=\"hidden\"  value=\"" . $montant_total_avec_remise . "\">";
							?>
								</td>
								<?php
							
if ($mode_resa < 3 || ($mode_resa==3 && $recup_client->police==1)) {
								echo "<td>" . $montant_total_avec_remise . "€</td>";
							}
							$redInit = "";
							$redEnd = "";
							if ($mode_resa < 3 || ($mode_resa==3 && $recup_client->police==1)) {
								$acompte = calcul_acompte ( $date_Min, $heure_saisie_Min, $montant_total );
								echo "<input name=\"acompte\" type=\"hidden\"  value=\"" . $acompte . "\">";
								if ($acompte > $total_caution_actuel && $mode_resa == 2) {
									$redInit = "<font color=red>";
									$redEnd = "</font>";
									$disable_submit_caution = " disabled";
									$caution_insuffisante = true;
								}
								echo "<td>" . $redInit . $acompte . "€$redEnd</td>";
							}
							?>
			<td>
								<?
							if (est_min_manager ( $user ) and ! test_non_vide ( $num_resa ) and $resa_mult == "1") {
								$temp = $nbre_terrains_a_reserver;
								foreach ( $infos_terrain as $terrain ) {
									if ($temp > 0)
										echo " <input type=\"checkbox\" name=\"terrain_choisit" . coller_jma ( $date_Min ) . $terrain [2] . "\" value=\"" . $terrain [3] . "\" checked >" . $terrain [2];
									$temp --;
								}
							} else {
								?>
									<select <?= $type_terain_disabled?>>
									<?
								foreach ( $infos_terrain as $terrain )
									echo "<option value=\"" . $terrain [3] . "\">" . $terrain [2] . "</option>";
								?>
									</select>
								<?
							
}
							if ($type_terrain == 3) {
								?>
									<input type="hidden" name="terrain_choisit"
					value="<?= $infos_terrain[0][3]; ?>" />
									<?php
							}
							
							?>
								
								</td>
			</tr>
							<?
							if (est_min_manager ( $user ) and $nbre_de_jours_a_tester > 1 and ! test_non_vide ( $num_resa )) {
								$les_jours_a_tester = $date_Min;
								for($nbre_de_jours_a_tester_restants = 0; $nbre_de_jours_a_tester_restants < $nbre_de_jours_a_tester; $nbre_de_jours_a_tester_restants ++) {
									$les_jours_a_tester = decaler_jour ( $les_jours_a_tester, $_POST ["Frequence"] );
									$infos_terrain = trouve_dispo ( $type_terrain, $les_jours_a_tester, $heure_saisie_Min, $heure_saisie_Max, $num_resa );
									?>
									
									<tr>
				<td nowrap><?
									
									echo date_longue ( $les_jours_a_tester );
									?></td>
				<td>de <? echo  $heure_saisie_Min." &agrave; ".$heure_saisie_Max; ?></td>
				<td><?=$type_terrain_texte ?></td>
				<td>
									<?
									$tarif_special = - 1;
									$cts = $ctsController->GetClientTarificationSpecialeByClientId ( $id_client );
									if ($cts != null) {
										$gts = $gtsController->GetGroupeTarificationSpecialeById ( $cts->Getgtsid () );
										$tarif_special = $gts->Gettarifhc ();
									}
									$tarif = tarif ( $les_jours_a_tester, $heure_saisie_Min, $heure_saisie_Max, $recup_client->id_type_regroupement, $recup_client->police, $type_terrain, $mode_resa, $tarif_special,$type_terrain_texte );
									$montant_total = $tarif ['tarif'];
									$montant_total_avec_remise = $tarif ['tarif_avec_remise'];
									echo $montant_total . "€";
									echo "<input name=\"montant_total" . coller_jma ( $les_jours_a_tester ) . "\" type=\"hidden\"  value=\"" . $montant_total . "\">";
									echo "<input name=\"montant_total_avec_remise" . coller_jma ( $les_jours_a_tester ) . "\" type=\"hidden\"  value=\"" . $montant_total_avec_remise . "\">";
									?>
									</td>
									<?php
									if ($mode_resa < 3 || ($mode_resa==3 && $recup_client->police==1)) {
										echo "<td>" . $montant_total_avec_remise . "€</td>";
									}
									
									$redInit = "";
									$redEnd = "";
									if ($mode_resa < 3 || ($mode_resa==3 && $recup_client->police==1)) {
										$acompte = calcul_acompte ( $les_jours_a_tester, $heure_saisie_Min, $montant_total );
										if ($acompte > $total_caution_actuel && $mode_resa == 2) {
											$redInit = "<font color=red>";
											$redEnd = "</font>";
											$disable_submit_caution = " disabled";
											$caution_insuffisante = true;
										}
										echo "<td>" . $redInit . $acompte . "€$redEnd</td>";
									}
									?>
			<td>
									<?
									if (est_min_manager ( $user ) and $resa_mult == "1") {
										$temp = $nbre_terrains_a_reserver;
										foreach ( $infos_terrain as $terrain ) {
											if ($temp > 0)
												echo " <input type=\"checkbox\" name=\"terrain_choisit" . coller_jma ( $les_jours_a_tester ) . $terrain [2] . "\" value=\"" . $terrain [3] . "\" checked >" . $terrain [2];
											
											$temp --;
										}
									} else {
										?>
										<select <?= $type_terain_disabled?>>
										<?
										foreach ( $infos_terrain as $terrain )
											echo "<option value=\"" . $terrain [3] . "\">" . $terrain [2] . "</option>";
										?>
										</select>
									<?
									
}
									if ($type_terrain == 3) {
										?>
									<input type="hidden" name="terrain_choisit"
					value="<?= $infos_terrain[0][3]; ?>" />
									<?php
									}
									
									?>
									
									</td>
			</tr>
							<?
								}
							}
							if (est_min_agent ( $user )) {
								?>
							<tr>
				<th>Commentaire <br>(15 caract&egrave;res min.)
				</th>
			<?php
								
if ($mode_resa < 3 and ! in_array ( $recup_client->id_type_regroupement, array (
										1,
										2 
								) ) and $recup_client->police != 1) {
									echo "<td colspan='6'>";
								} else {
									echo "<td colspan='5'>";
								}
								
								if ($mode_resa == 2 && ! $is_cautionable) {
									$disable_submit_caution = " disabled";
								}
								if ($mode_resa == 3 && $is_max_sans_acompte_ni_caution) {
									$disable_submit_caution = " disabled";
								}
								?>
			<textarea <?= $disable_submit_caution ?> required
					data-parsley-required-message="Le commentaire est obligatoire"
					rows="4" cols="90" name="commentaire" data-parsley-trigger="keyup"
					data-parsley-minlength="15"
					data-parsley-minlength-message="Veuillez saisir 15 caract&egrave;res minimum"><?
								if ($caution_insuffisante) {
									echo "Le client ne dispose pas d'assez de caution pour effectuer cette r&eacute;servation. Veuillez changer de mode de r&eacute;servation.";
								} elseif ($mode_resa == 2 && ! $is_cautionable) {
									echo "Le client n'est pas cautionable. Il a d&eacute;pass&eacute; les quota de r&eacute;servations avec caution sur la p&eacute;riode.";
								} elseif ($is_max_sans_acompte_ni_caution) {
									echo "Le client ne peut plus effectuer de r&eacute;servation sans caution ni acompte. La limite est de 2 tous les 7 jours glissants.";
								} elseif (test_non_vide ( $num_resa )) {
									$ligne_commentaire_resa = recup_derniere_commentaire ( "id_resa", $num_resa );
									if ($ligne_commentaire_resa->Commentaire != "")
										echo $ligne_commentaire_resa->Commentaire;
								}
								?></textarea>
				</td>
			</tr>
							<?
							} else {
								?>
								<tr>
				<th>Infos g&eacute;n&eacute;rales</th>
				<td colspan="5"><br>Il est obligatoire de r&eacute;gler l’acompte
					minimum pour chaque r&eacute;servations <br>Une r&eacute;servation
					peut- être d&eacute;plac&eacute; jusqu'&agrave; 48h avant la
					r&eacute;servation <br>En cas d’annulation, l’acompte sera
					plac&eacute; sous la forme d’un avoir jusqu'&agrave; 48h avant la
					r&eacute;servation. Au-del&agrave; de ce d&eacute;lai votre acompte
					sera perdu. <br>Les r&eacute;servations ne peuvent pas être
					d&eacute;plac&eacute;es en ligne moins de 48h avant la
					r&eacute;servation. <br>Pour toutes infos et demandes
					suppl&eacute;mentaires merci de contacter l’accueil de votre
					centre au 01 49 51 27 04</td>
			</tr>
								
							<?
							}
							?>
						</table>
		<!--	Le créneau <font color="red"><? echo  $heure_saisie_Min."-".$heure_saisie_Max; ?></font> du <font color="red">
						<? echo  date_longue($date_Min); ?>
						</font> est disponible, vous devez cliquez ci-dessous sur le mode paiement de votre choix.<br /-->


		<input name="nbre_de_jours_a_tester" type="hidden"
			value="<? echo  $nbre_de_jours_a_tester; ?>"> <input
			name="la_Frequence" type="hidden"
			value="<? echo  $_POST["Frequence"]; ?>"> <input name="type_terrain"
			type="hidden" value="<? echo  $type_terrain; ?>"> <input
			name="date_debut_resa" type="hidden" value="<? echo  $date_Min; ?>">
		<input name="date_fin_resa" type="hidden"
			value="<? echo  $date_Max; ?>"> <input name="heure_debut_resa"
			type="hidden" value="<? echo  $heure_saisie_Min;?>"> <input
			name="heure_fin_resa" type="hidden"
			value="<? echo  $heure_saisie_Max;?>"> <input name="duree_resa"
			type="hidden" value="<? echo  $duree_resa;?>"> <input
			name="id_client" type="hidden" value="<? echo  $id_client;?>"> <input
			name="infos_client" type="hidden"
			value="<? echo  $tab_client[$id_client];?>"> <input name="mois_resa"
			type="hidden" value="<? echo  $mois_fr[date("n", $date_longue)-1];?>">
		<input name="date_debut_resa_longue" type="hidden"
			value="<? echo  date_longue($date_Min)." de ".$heure_saisie_Min."-".($heure_saisie_Max);?>">
		<input name="mode_resa" type="hidden" value="<? echo  $mode_resa; ?>">

		<br /> <br />
		<center>
			<table width="100%">
				<tr>
					<td align="center">
				<?
							
							if (test_non_vide ( $num_resa ))
								echo "<input name=\"num_resa\" type=\"hidden\"  value=\"" . $num_resa . "\" >";
							echo "<input$disable_submit_caution name=\"valide\" type=\"submit\"  value=\"";
							if (test_non_vide ( $num_resa ))
								echo "Modifier ma r&eacute;servation";
							else
								echo "R&eacute;gler cette r&eacute;sa";
							echo "\" ></td><td align=\"center\" valign=\"center\"><a target=blank href=\"libraries/ya2/devis.php?%3Afm&tmpl=component&print=1" . "&layout=default&page=&option=com_content&date_debut_resa=" . $date_Min . "&date_fin_resa=" . $date_Max . "&heure_debut_resa=" . $heure_saisie_Min . "&heure_fin_resa=" . $heure_saisie_Max . "&id_client=" . $id_client . "&montant_total=" . $montant_total . "&sortie=I&devis_fact=DEVIS&tva=" . recup_taux_TVA_d_une_date ( $date_Min ) . "\" >" . "<img src=\"images/imprimante-icon.png\" title=\"Imprimer le devis\"></a> &nbsp;&nbsp;&nbsp;" . "<a target=blank href=\"libraries/ya2/devis.php?%3Afm&tmpl=component&print=1" . "&layout=default&page=&option=com_content&date_debut_resa=" . $date_Min . "&date_fin_resa=" . $date_Max . "&heure_debut_resa=" . $heure_saisie_Min . "&heure_fin_resa=" . $heure_saisie_Max . "&id_client=" . $id_client . "&montant_total=" . $montant_total . "&sortie=D&devis_fact=DEVIS&tva=" . recup_taux_TVA_d_une_date ( $date_Min ) . "\" >" . "<img src=\"images/PDF-Document-icon.png\" title=\"T&eacute;l&eacute;charger le devis\"></a> ";
							if ($recup_client->courriel != "agent@footinfive.com") {
								$corps = "Bonjour " . $recup_client->prenom . " " . $recup_client->nom . "," . "%0A%0ANous faisons suite &agrave; votre demande de devis et vous prions, comme convenu, de bien vouloir trouver " . " en pi&egrave;ce jointe notre proposition.%0A%0A" . "Merci de nous retourner le devis sign&eacute; pour confirmer la date de resa." . "%0A%0AL'&eacute;quipe du Foot In Five vous remercie de votre confiance !" . "%0A%0AA bient&ocirc;t sur nos terrains..." . "%0A%0AFOOT IN FIVE" . "%0ACentre de FOOT en salle 5vs5" . "%0A187 Route de Saint-Leu" . "%0A93800 Epinay-sur-Seine" . "%0ATel : 01 49 51 27 04" . "%0AMail : contact@footinfive.com";
								
								echo "<a href=\"mailto:" . $recup_client->courriel . "?subject=Suite à votre demande de devis&body=" . $corps . "\">" . $recup_client->courriel . "</a>";
							}
							?>
						</td>
				</tr>
			</table>
	
	</div>

</form>

<?
						} else {
							$tranche_30_mins_debut = $heure_saisie_Min;
							$tranche_30_mins_fin = decaler_heure ( $heure_saisie_Min, 30 );
							while ( diff_dates_en_minutes ( "", $tranche_30_mins_fin, "", $heure_saisie_Max ) >= 0 ) {
								/*
								 * echo "<br>".$tranche_30_mins_debut." * ".$tranche_30_mins_fin." test :"
								 * .diff_dates_en_minutes("",$tranche_30_mins_fin,"",$heure_saisie_Max)."<br>";
								 */
								if (is_array ( trouve_dispo ( $type_terrain, $date_saisie_Min, $tranche_30_mins_debut, $tranche_30_mins_fin, $num_resa, true ) )) {
									$tranche_30_mins_debut = decaler_heure ( $tranche_30_mins_debut, 30 );
									$tranche_30_mins_fin = decaler_heure ( $tranche_30_mins_fin, 30 );
									// echo " ok !";
								} else
									break;
							}
							/*
							 * echo "<br>".$tranche_30_mins_debut." - ".$tranche_30_mins_fin
							 * ." ------ ".diff_dates_en_minutes("",$tranche_30_mins_fin,"",$heure_saisie_Max)
							 * ." ++++++++ ".$heure_saisie_Min." - ".$heure_saisie_Max."<br>";
							 */
							
							if (diff_dates_en_minutes ( "", $tranche_30_mins_fin, "", $heure_saisie_Max ) < 0 and $type_terrain != 3) {
								echo "<a href=\"index.php/component/content/article?id=62" . "&id_client=" . $_GET ["id_client"] . $_POST ["id_client"] . "&num_resa=" . $num_resa . "&date_debut_resa=" . $date_saisie_Min . "&heure_fin_resa=" . $heure_saisie_Max . "&heure_debut_resa=" . $heure_saisie_Min . "&rubiks_cube=1\" />Tenter une optimisation ?</a><br><br>";
							}
							
							echo "D&eacute;sol&eacute; il n'y a plus de terrains disponibles entre <font color=red>";
							echo $heure_saisie_Min . "-" . $heure_saisie_Max . " </font> pour la date du <font color=red>";
							echo date_longue ( $date_Min ) . "</font>.<br />Cr&eacute;neaux disponibles pour cette même date<br />";
							
							if (test_horaires_ouverture ( decaler_heure ( $heure_saisie_Min, - 6 * 60 ), "" ) == 0)
								$horaire_Min = horaire_ouverture ();
							else {
								if (diff_dates_en_minutes ( $date_Min, decaler_heure ( $heure_saisie_Min, - 6 * 60 ) ) < 0)
									$horaire_Min = date ( "H" ) . ":" . date ( "i" );
								else
									$horaire_Min = decaler_heure ( $heure_saisie_Min, - 6 * 60 );
							}
							if (test_horaires_ouverture ( "", decaler_heure ( $heure_saisie_Max, + 6 * 60 ) ) == 0)
								$horaire_Max = horaire_fermeture ();
							else
								$horaire_Max = decaler_heure ( $heure_saisie_Max, + 6 * 60 );
							
							$lien = "<a href=\"index.php/component/content/article?id=62";
							$lien .= "&id_client=" . $_GET ["id_client"] . $_POST ["id_client"];
							$lien .= "&num_resa=" . $num_resa . "&type_terrain=" . $type_terrain;
							
							// en jouant une demi_heure de plus avec la meme heure de début
							if (test_horaires_ouverture ( $heure_saisie_Min, decaler_heure ( $heure_saisie_Max, 30 ) ) != 0) {
								if (is_array ( trouve_dispo ( $type_terrain, $date_saisie_Min, $heure_saisie_Min, decaler_heure ( $heure_saisie_Max, 30 ), $num_resa ) )) {
									echo "Dur&eacute;e : " . decaler_heure ( $duree_resa, 30 ) . "<br><li>";
									echo $lien . "&duree_resa=" . decaler_heure ( $duree_resa, 30 ) . "&date_debut_resa=" . $annee . "-" . $mois . "-" . $jour;
									echo "&heure_debut_resa=" . $heure_saisie_Min . "#deb_form\" />";
									echo $heure_saisie_Min . " &agrave; " . decaler_heure ( $heure_saisie_Max, 30 );
									echo " </a></li>";
								}
							}
							// en jouant une demi_heure de plus avec la meme heure de fin
							if (test_horaires_ouverture ( decaler_heure ( $heure_saisie_Min, - 30 ), $heure_saisie_Max ) != 0) {
								if (is_array ( trouve_dispo ( $type_terrain, $date_saisie_Min, decaler_heure ( $heure_saisie_Min, - 30 ), $heure_saisie_Max, $num_resa ) )) {
									echo "Dur&eacute;e : " . decaler_heure ( $duree_resa, 30 );
									echo " &agrave; un horaire proche :<br><li>";
									echo $lien . "&duree_resa=" . decaler_heure ( $duree_resa, 30 ) . "&date_debut_resa=" . $annee . "-" . $mois . "-" . $jour;
									echo "&heure_debut_resa=" . decaler_heure ( $heure_saisie_Min, - 30 ) . "#deb_form\" />";
									echo decaler_heure ( $heure_saisie_Min, - 30 ) . " &agrave; " . $heure_saisie_Max;
									echo " </a></li>";
								}
							}
							// en enlevant une demi_heure avec la meme heure de début
							if (diff_dates_en_minutes ( "", $heure_saisie_Min, "", decaler_heure ( $heure_saisie_Max, - 30 ) ) >= 60) {
								if (test_horaires_ouverture ( $heure_saisie_Min, decaler_heure ( $heure_saisie_Max, - 30 ) ) != 0) {
									if (is_array ( trouve_dispo ( $type_terrain, $date_saisie_Min, $heure_saisie_Min, decaler_heure ( $heure_saisie_Max, - 30 ), $num_resa ) )) {
										echo "Dur&eacute;e : " . decaler_heure ( $duree_resa, - 30 ) . "<br><li>";
										echo $lien . "&duree_resa=" . decaler_heure ( $duree_resa, - 30 ) . "&date_debut_resa=" . $annee . "-" . $mois . "-" . $jour;
										echo "&heure_debut_resa=" . $heure_saisie_Min . "#deb_form\" />";
										echo $heure_saisie_Min . " &agrave; " . decaler_heure ( $heure_saisie_Max, - 30 );
										echo " </a></li>";
									}
								}
							}
							// en enlevant une demi_heure avec la meme heure de fin
							if (diff_dates_en_minutes ( "", decaler_heure ( $heure_saisie_Min, 30 ), "", $heure_saisie_Max ) >= 60) {
								if (test_horaires_ouverture ( decaler_heure ( $heure_saisie_Min, 30 ), $heure_saisie_Max ) != 0) {
									if (is_array ( trouve_dispo ( $type_terrain, $date_saisie_Min, decaler_heure ( $heure_saisie_Min, 30 ), $heure_saisie_Max, $num_resa ) )) {
										echo "Dur&eacute;e : " . decaler_heure ( $duree_resa, - 30 );
										echo " &agrave; un horaire proche :<br><li>";
										echo $lien . "&duree_resa=" . decaler_heure ( $duree_resa, - 30 ) . "&date_debut_resa=" . $annee . "-" . $mois . "-" . $jour;
										echo "&heure_debut_resa=" . decaler_heure ( $heure_saisie_Min, + 30 ) . "#deb_form\" />";
										echo decaler_heure ( $heure_saisie_Min, + 30 ) . " &agrave; " . $heure_saisie_Max;
										echo " </a></li>";
									}
								}
							}
							echo "<br><br>Même dur&eacute;e &agrave; un autre horaire :<br>";
							
							for($i = - 6; $i < 7; $i ++) {
								// echo "<br>".decaler_heure($heure_saisie_Min,30*$i)."-".decaler_heure($heure_saisie_Max,30*$i);
								if (test_horaires_ouverture ( decaler_heure ( $heure_saisie_Min, 30 * $i ), decaler_heure ( $heure_saisie_Max, 30 * $i ) ) != 0) {
									// echo "pass";
									if (is_array ( trouve_dispo ( $type_terrain, $date_saisie_Min, decaler_heure ( $heure_saisie_Min, 30 * $i ), decaler_heure ( $heure_saisie_Max, 30 * $i ), $num_resa ) )) {
										echo "<li>" . $lien;
										echo "&duree_resa=" . $duree_resa . "&date_debut_resa=" . $annee . "-" . $mois . "-" . $jour . "&heure_debut_resa=";
										echo decaler_heure ( $heure_saisie_Min, 30 * $i ) . "&force_valider=1#deb_form\" >" . decaler_heure ( $heure_saisie_Min, 30 * $i );
										echo " &agrave; " . decaler_heure ( $heure_saisie_Max, 30 * $i );
										echo " </a></li>";
									}
								}
							}
						}
					}
				}
			}
		}
		
		$config = JFactory::getConfig ();
		$siteURL = $config->get ( 'site_url' );
		echo "<div class='content-loading' style=\"background: white url('" . $siteURL . "/images/loading_spinner.gif') center center  no-repeat;\"></div>";
	}

	function LoadMultiResa() {
		echo "
	<div class='panelRow'>
		<span class='panelTitle'>Date de fin :</span> 
		<span class='panelValue'><input type='text' name='date_fin' onChange='valider()' id='datepicker2'></span>
	</div>
	<div class='panelRow'>
		<span class='panelTitle'>Frequence :</span> 
		<span class='panelValue'>
			<input type='radio' name='Frequence' value='1' onChange='valider()'>Quotidien 
			<input type='radio' name='Frequence' value='7' onChange='valider()'>Hebdomadaire
		</span>
	</div>
	<div class='panelRow'>
		<span class='panelTitle'>Nbre de terrains :</span> 
		<span class='panelValue'>
		 	<input type='radio' name='nbre_terrains_a_reserver' value='1' onChange='valider()'>1
		 	<input type='radio' name='nbre_terrains_a_reserver' value='2' onChange='valider()'>2
		 	<input type='radio' name='nbre_terrains_a_reserver' value='3' onChange='valider()'>3
		 	<input type='radio' name='nbre_terrains_a_reserver' value='4' onChange='valider()'>4
		</span>
	</div>";
	}
}

?>