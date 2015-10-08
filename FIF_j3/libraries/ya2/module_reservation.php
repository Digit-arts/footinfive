<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');


nettoyer_resa_non_payees();

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {
	
if (est_min_agent($user)){
	if (test_non_vide($_POST["id_client"])) $id_client=$_POST["id_client"];
	else $id_client=$_GET["id_client"];
} else $id_client=idclient_du_user();

menu_acces_rapide($id_client,"R&eacute;server");

if (test_non_vide($_POST["num_resa"])) 
	$num_resa = $_POST["num_resa"];
else $num_resa = $_GET["num_resa"];

proprietaire_resa($num_resa);

if (test_non_vide($_GET["rubiks_cube"])) {
					
	$resas_a_optimiser=recup_resa_sur_periode($_GET["date_debut_resa"], $_GET["heure_debut_resa"], $_GET["heure_fin_resa"]);
					
	foreach($resas_a_optimiser as $la_resa_a_optimiser){
		$tab_liste_terrains_dispo_pour_optimisation=test_dispo($la_resa_a_optimiser->type_terrain,$la_resa_a_optimiser->date_debut_resa, $la_resa_a_optimiser->heure_debut_resa,
								       $la_resa_a_optimiser->heure_fin_resa,'',$la_resa_a_optimiser->id_resa);
		if (is_array($tab_liste_terrains_dispo_pour_optimisation)){
			foreach($tab_liste_terrains_dispo_pour_optimisation as $liste_terrains_dispo) {
				if ($liste_terrains_dispo<$la_resa_a_optimiser->id_terrain){
					/////////////
					// on met à jour la resa dans le cas d'une simple modif
					//////////////
					$id_resa_new=maj_resa($la_resa_a_optimiser->id_resa,$la_resa_a_optimiser->date_debut_resa,
						      $la_resa_a_optimiser->date_fin_resa, $la_resa_a_optimiser->heure_debut_resa,
						      $la_resa_a_optimiser->heure_fin_resa,$la_resa_a_optimiser->id_client,
						      $liste_terrains_dispo,$la_resa_a_optimiser->montant_total,
						      $la_resa_a_optimiser->duree_resa,$la_resa_a_optimiser->commentaire,
						      $la_resa_a_optimiser->cautionnable);
									
					$rdv=ajout_event_cal_google ($id_resa_new);
					suppr_event_cal_google($la_resa_a_optimiser->id_resa);
											
					///////////////////
					// on met à jour la resa avec l'adresse du rdv
					/////////////////////
					maj_cal_dans_resa($id_resa_new,$rdv->getEditLink()->href);
					break;
				}
			}
		}
	}
}

if (isset($user) and (est_register($user)) and ($id_client<>"")) {
	$requete_recup_client="select id_client, prenom, nom, mobile1, fixe,commentaire from Client where id_client=".$id_client." order by nom,prenom,code_insee";
	$db->setQuery($requete_recup_client);	
	$recup_client = $db->loadObject();
	if (!test_non_vide($recup_client->nom))
		header("Location: index.php/component/content/article?id=57&modif=1&id_client=".$user->id."");
}
if (!test_non_vide($id_client))
	header("Location: index.php?option=com_content&view=article&id=57");
?>	
<script type="text/javascript">
	function valider() {
		document.date_form_0.submit();
		
	}
	
	function reserver() {
		
		if (typeof document.formulaire_resa.terrain_choisit != 'undefined'){
			if (confirm('Confirmez votre choix : \n\n' + document.formulaire_resa.date_debut_resa_longue.value + ' sur le terrain ' + document.formulaire_resa.terrain_choisit.value ))
				document.formulaire_resa.submit();
		}
		else {
			if (confirm('Confirmez-vous votre choix ?'))
				document.formulaire_resa.submit();
		}
	}
</script>
<?

/////////////////////////////////////////////////////
/////// Formulaire de recherche de creneaux
///////////////////////////////////////////////////

?>

<FORM id="formulaire" name="date_form_0" class="submission box" action="article?id=62" method="post" >
	<?
	/////////////// Modif d'une RESA


	if (test_non_vide($num_resa)) {
		echo "<input name=\"num_resa\" type=\"hidden\"  value=\"".$num_resa."\" >";		
		echo "Pour modifier votre r&eacute;servation (<font color=red>#".$num_resa."</font>), selectionnez ci-dessous vos nouvelles informations.<br><hr>";
	}
	$requete_recup_client="select id_client, prenom, (select u.email from #__users as u where u.id=c.id_user) as courriel, c.nom as nom, mobile1, fixe,id_type_regroupement,tr.nom as type_reg, police "
	." from Client as c LEFT JOIN Type_Regroupement as tr on c.id_type_regroupement=tr.id  where id_client=".$id_client." ";
	$db->setQuery($requete_recup_client);	
	$recup_client = $db->loadObject();
	
	?>
	<table border="0">
		<tr>
			<td width="50" valign="top">Type </td>
			<td valign="top"><? echo $recup_client->type_reg." ".$recup_client->nom_entite;
			if ($recup_client->police==1)
				echo " <img src=\"images/police-icon.png\" title=\"Ce client est policier\">";
				?>
			</td>
		</tr>
		<tr>
			<td width="50" valign="top">Client </td>
			<td valign="top"><?

				echo "<input name=\"id_client\" type=\"hidden\"  value=\"".$recup_client->id_client."\">".$recup_client->nom." ".$recup_client->prenom;
				$tab_client[$recup_client->id_client]=$recup_client->nom." | ".$recup_client->prenom." | ".$recup_client->mobile1." | ".$recup_client->fixe;
				$ligne_commentaire_client=recup_derniere_commentaire("id_client",$id_client);
				if ($ligne_commentaire_client->Commentaire<>"" and (est_min_agent($user))){
					echo " <a href=\"index.php/component/content/article?id=75&art=62&id_client=".$recup_client->id_client."\">";
					echo "<img src=\"images/Comment-icon.png\" title=\"".$ligne_commentaire_client->Commentaire."\"></a>";
				}
				?>
			</td>
		</tr>
		<tr>
			<td width="50">Date </td>
			<td width="300"><input type="date" name="date_input_0" value="<? echo $_POST["date_input_0"].$_GET["date_debut_resa"];?>"  onChange="valider()"></td>
		</tr>
		<?
		if (est_min_manager($user) and !test_non_vide($num_resa)){
			?><tr>
				<td width="50">Type</td>
				<td>
					<?
					echo " <input type=\"radio\" name=\"resa_mult\" value=\"1\" ";
						if (test_non_vide($_POST["resa_mult"]) and $_POST["resa_mult"]=="1")
							echo "checked";
						echo " onChange=\"valider()\">Multiple";
					echo " <input type=\"radio\" name=\"resa_mult\" value=\"0\" ";
						if (!test_non_vide($_POST["resa_mult"]) or (test_non_vide($_POST["resa_mult"]) and $_POST["resa_mult"]=="0"))
							echo "checked";
						echo " onChange=\"valider()\">Simple";
							
					if (test_non_vide($_POST["resa_mult"]))
						$resa_mult=$_POST["resa_mult"];
					?>
				</td>
			</tr><?
		}
			
		if (est_min_manager($user) and !test_non_vide($num_resa) and test_non_vide($_POST["resa_mult"]) and ($_POST["resa_mult"]==1) ){
		?>
		<tr>
			<td width="50">Date fin</td>
			<td><input type="date" name="date_fin" value="<? echo $_POST["date_fin"];?>"  onChange="valider()"></td>
		</tr>
		<tr>
			<td width="50">Frequence </td>
			<td>
				<?
				echo "<input type=\"radio\" name=\"Frequence\" value=\"1\"";
				if (test_non_vide($_POST["Frequence"]) and $_POST["Frequence"]=="1")
					echo "checked";
				echo " onChange=\"valider()\">quotidien <input type=\"radio\" name=\"Frequence\" value=\"7\" ";
				if (test_non_vide($_POST["Frequence"]) and $_POST["Frequence"]=="7")
					echo "checked";
				echo " onChange=\"valider()\">hebdomadaire";
				?>
			</td>
		</tr>
		<tr>
			<td width="50">Nbre de terrains</td>
			<td>
				<?
				for ($i=1;$i<=4;$i++){
					echo " <input type=\"radio\" name=\"nbre_terrains_a_reserver\" value=\"".$i."\"";
					if (test_non_vide($_POST["nbre_terrains_a_reserver"]) and $_POST["nbre_terrains_a_reserver"]==$i)
						echo "checked";
					echo " onChange=\"valider()\">".$i;
				}
				if (test_non_vide($_POST["nbre_terrains_a_reserver"]))
					$nbre_terrains_a_reserver=$_POST["nbre_terrains_a_reserver"];
				?>
			</td>
		</tr>
		
		<?
		}
		$select_type_int="";
		$select_type_ext="";
		$select_type_loge="";

		if (($_GET["type_terrain"]==1) or ($_POST["type_terrain"]==1)) $select_type_int=" selected ";
		if (($_GET["type_terrain"]==2) or ($_POST["type_terrain"]==2)) $select_type_ext=" selected ";
		if (($_GET["type_terrain"]==3) or ($_POST["type_terrain"]==3)) $select_type_loge=" selected ";
		$type_terrain=$_GET["type_terrain"].$_POST["type_terrain"];
		?>
		<tr>
			<td>Terrain </td>
			<td><select name="type_terrain" onChange="valider()">
				<option value="1" <? echo "\"".$select_type_int."\"";?> >interieur</option>
				<option value="2" <? echo "\"".$select_type_ext."\"";?> >extérieur</option>
				<option value="3" <? echo "\"".$select_type_loge."\"";?> >loge</option>
				</select>
			</td>
		</tr>
		<!--tr>
			<td>Nbre de joueurs </td>
			<td><select name="nbre_joueurs">
				<option value="5" <? //echo "\"".$select_nbre_joueurs."\"";?>>5 vs 5</option>
				<option value="7" <? //echo "\"".$select_nbre_joueurs."\"";?>>7 vs 7</option>
				</select>
			</td>
		</tr-->
		<tr>
			<td>Heure </td>
			<td><select name="heure_debut_resa" onChange="valider()" >
			<?
				if (isset($_POST["heure_debut_resa"])) $heure_debut_resa = $_POST["heure_debut_resa"];
				else $heure_debut_resa = $_GET["heure_debut_resa"];
			list($heure_resa,$minutes_resa) = explode(':', $heure_debut_resa );
			$heure_demarrage=substr(horaire_ouverture(),0,2);
			for ($i=$heure_demarrage;$i<=23;$i++) {
					  $select_heure="";
					  $select_demie="";
					  if (($heure_resa=="") and ($i==9)) $select_heure=" selected ";
					  else 
						if ($heure_resa==$i) {
						  if ($minutes_resa=="30") $select_demie=" selected ";
						  else $select_heure=" selected ";
						}
					  echo "<option value=\"".$i.":00\" \"".$select_heure."\">".$i."h00</option>";
					  if ($i<>9) echo "<option value=\"".$i.":30\" \"".$select_demie."\">".$i."h30</option>";
			}
			$select="";
			if (($heure_resa<>"") and ($heure_resa==0)) $select=" selected ";
			echo "<option value=\"00:00\" \"".$select."\">00h00</option>";
			$select="";
			if (($heure_resa<>"") and ($heure_resa==0) and $minutes_resa=="30") $select=" selected ";
			echo "<option value=\"00:30\" \"".$select."\">00h30</option>";
			$select="";
			if (($heure_resa<>"") and ($heure_resa==1)) $select=" selected ";
			echo "<option value=\"01:00\" \"".$select."\">01h00</option>";
			$select="";
			if (($heure_resa<>"") and ($heure_resa==1) and $minutes_resa=="30") $select=" selected ";
			echo "<option value=\"01:30\" \"".$select."\">01h30</option>";
			$select="";
			if (($heure_resa<>"") and ($heure_resa==2)) $select=" selected ";
			echo "<option value=\"02:00\" \"".$select."\">02h00</option>";
			$select="";
			if (($heure_resa<>"") and ($heure_resa==2) and $minutes_resa=="30") $select=" selected ";
			echo "<option value=\"02:30\" \"".$select."\">02h30</option>";
			$select="";
			if (($heure_resa<>"") and ($heure_resa==3)) $select=" selected ";
			echo "<option value=\"03:00\" \"".$select."\">03h00</option>";
			$select="";
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Dur&eacute;e </td>
			<td><select name="duree_resa" onChange="valider()">
			<?
				if (isset($_POST["duree_resa"])) $duree_resa = $_POST["duree_resa"];
				else {
					if (isset($_GET["heure_fin_resa"]))
						$duree_resa = duree_en_horaire(diff_dates_en_minutes("",$heure_debut_resa,"",$_GET["heure_fin_resa"]));
					else $duree_resa = $_GET["duree_resa"];
				}
				list($duree_heure_resa,$duree_minutes_resa) = explode(':', $duree_resa);
				if (est_min_manager($user)) 
					$max=17;
				else $max=3;
				for ($i=1;$i<=$max;$i++) {
						  $select_duree="";
						  $select_demie="";
						  
						  if ($duree_heure_resa==$i) {
							  if ($duree_minutes_resa=="30") $select_demie=" selected ";
							  else $select_duree=" selected ";
						  }
						  
						  echo "<option value=\"".$i.":00\" \"".$select_duree."\">".$i."h</option>";
						  if ($i<$max) echo "<option value=\"".$i.":30\" \"".$select_demie."\">".$i."h30</option>";
				}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3">
			
				<!--input name="valide" type="button"  value="Vérifier la disponibilité" onclick="valider()"-->
			</td>
		</tr>
		</table>
</form>
<?
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////
// Résultat de la demande
////////////////////////////////////////
if (isset($_POST["date_input_0"])) list($annee,$mois,$jour) = explode('-', $_POST["date_input_0"]);
		else list($annee,$mois,$jour) = explode('-', $_GET["date_debut_resa"]);
if (!isset($_GET["modif"])){
if (isset($_POST["id_client"]) and ($_POST["id_client"]=="") and (est_min_agent($user))){
	echo "<br><font color=red>Veuillez selectionner un client.</font><br>";
}
else{
	if  (isset($_POST["heure_debut_resa"]) or isset($_GET["heure_debut_resa"])){

		echo  "<br /><hr><br />";

			////////////////////////////////////////////
			///////// Si la resa provient d'une recherche avec date_input_0 ou d'une modif avec date_debut_resa
			///////////////////////////////////////////////

		$date_saisie_Min = $annee."-".$mois."-".$jour;
		$date_saisie_Max = $annee."-".$mois."-".$jour;

		if (isset($_POST["heure_debut_resa"])) $heure_saisie_Min = $_POST["heure_debut_resa"];
		else $heure_saisie_Min = $_GET["heure_debut_resa"];

		$heure_saisie_Max = decaler_heure($heure_saisie_Min,duree_en_minutes($duree_resa));
		
		
	
		//////////////////////////////////////////////
		///// Le cas où la date est passée
		///////////////////////////////////////////
	
	
		
	// if (diff_dates_en_minutes($date_saisie_Min,$heure_saisie_Min)>-2880) 
		// echo "Attention : moins de 48h, pas de modifs possible... Tarif=acompte<br><br>";
	if (test_nuit_avant_ouverture($heure_saisie_Min)==1)  
		$date_Min=decaler_jour($date_saisie_Min,1);
	else $date_Min=$date_saisie_Min;
			
	if (test_nuit_avant_ouverture($heure_saisie_Max)==1)  
		$date_Max=decaler_jour($date_saisie_Max,1);
	else $date_Max=$date_saisie_Max;
	
	$position_date_saisie_date_du_jour=diff_dates_en_minutes($date_Min,$heure_saisie_Min);
	//echo $position_date_saisie_date_du_jour."mins";		
	if (!test_valid_existence_date($date_Min)
	    or ((est_register($user)) and $position_date_saisie_date_du_jour>0)
	    or ((est_agent($user) and $position_date_saisie_date_du_jour>120))){ 
			
		if (!test_valid_existence_date($date_Min)) echo " <font color=red>Erreur : la date est incorrecte.</font>";
		else{
			if ($position_date_saisie_date_du_jour>1440)// si c'est pas le meme jour
				echo "La date <font color=red>".date_longue($date_Min)."</font> est ant&eacute;rieure &agrave; aujourd'hui.";
			else
				echo "Vous avez s&eacute;lectionn&eacute; : <font color=red>".$heure_saisie_Min."</font>, heure d&eacute;j&agrave; pass&eacute;e ! <br />Il est ".date("H")."h".date("i").".";	
			
			
		}
	}
	else {
		////////////////////////////////////////////////
		///////Recherche sur les agendas des terrains
		////////////////////////////////////////////////
		
		if (test_horaires_ouverture(decaler_heure($heure_saisie_Min,-1*60),"")==0) $horaire_Min=horaire_ouverture();
		else $horaire_Min=decaler_heure($heure_saisie_Min,-1*60);
		
		if (test_horaires_ouverture("",decaler_heure($heure_saisie_Max,+1*60))==0) $horaire_Max=horaire_fermeture();
		else $horaire_Max=decaler_heure($heure_saisie_Max,+1*60);

		//echo "<br>rempli avec :".$horaire_Min." - ".$horaire_Max;
					
		$infos_terrain=trouve_dispo($type_terrain,$date_saisie_Min,$heure_saisie_Min, $heure_saisie_Max,$num_resa);
		//echo "<br>terrain : ".$recup_id_cal_terrains->id_terrain.", dispo : ".$terrain_dispo;
		
		if (test_non_vide($_POST["date_fin"]))
			if (!test_non_vide($_POST["Frequence"]))
				echo "<font color=red>La frequence est obligatoire ! Si vous indiquez une date de fin.</font><br><br><br>";
			else {
				$diff_jours=diff_dates_en_jours($date_Min,$_POST["date_fin"]);
				//echo "diff_jours".$diff_jours."<br>";
				$nbre_de_jours_a_tester=$diff_jours/$_POST["Frequence"];
				//echo "nbre_de_jours_a_tester".$nbre_de_jours_a_tester."<br>";
			}
		
	
		if (is_array($infos_terrain)) {
			?>
				<FORM id="formulaire" name="formulaire_resa" class="submission box" action="article?id=63" method="post">

					<table class="zebra">
						<tr>
							<th>Date</th><th>Heure</th><th>Terrain</th><th>Tarif</th><th>Acompte</th><th>Terrain</th>
						</tr>
						<tr>
							<td nowrap><? 
	
								echo date_longue($date_Min); ?></td>
							<td>de <? echo $heure_saisie_Min." &agrave; ".$heure_saisie_Max; ?></td>
							<td>Int.</td>
							<td>
							<? 
								$montant_total=tarif($date_Min,$heure_saisie_Min,$heure_saisie_Max,$recup_client->id_type_regroupement,$recup_client->police,$type_terrain);
								echo $montant_total."€";
								echo "<input name=\"montant_total".coller_jma($date_Min)."\" type=\"hidden\"  value=\"".$montant_total."\">";
							?>
							</td>
							<td><?
								
								$acompte=calcul_acompte($date_Min,$heure_saisie_Min,$montant_total);
								echo "<input name=\"acompte\" type=\"hidden\"  value=\"".$acompte."\">";
								echo $acompte."€";
							?>
							</td>
							<td>
							<?
							if (est_min_manager($user) and !test_non_vide($num_resa) and $resa_mult=="1"){
								$temp=$nbre_terrains_a_reserver;
								foreach ($infos_terrain as $terrain){
									if ($temp>0)
										echo " <input type=\"checkbox\" name=\"terrain_choisit".coller_jma($date_Min).$terrain[2]."\" value=\"".$terrain[3]."\" checked >".$terrain[2];
									$temp--;
								}
							}
							else {?>
								<select name="terrain_choisit">
								<?
									foreach ($infos_terrain as $terrain) echo "<option value=\"".$terrain[3]."\">".$terrain[2]."</option>";
								?>
								</select>
							<?}?>
							
							</td>
						</tr>
						<?
						if (est_min_manager($user) and $nbre_de_jours_a_tester>1 and !test_non_vide($num_resa)){
							$les_jours_a_tester=$date_Min;
							for ($nbre_de_jours_a_tester_restants=0;$nbre_de_jours_a_tester_restants<$nbre_de_jours_a_tester;$nbre_de_jours_a_tester_restants++){
								$les_jours_a_tester=decaler_jour($les_jours_a_tester,$_POST["Frequence"]);
								$infos_terrain=trouve_dispo($type_terrain,$les_jours_a_tester,$heure_saisie_Min, $heure_saisie_Max,$num_resa);
							?>
								
								<tr>
								<td nowrap><? 
		
									echo date_longue($les_jours_a_tester); ?></td>
								<td>de <? echo $heure_saisie_Min." &agrave; ".$heure_saisie_Max; ?></td>
								<td>Int.</td>
								<td>
								<? 
									$montant_total=tarif($les_jours_a_tester,$heure_saisie_Min,$heure_saisie_Max,$recup_client->id_type_regroupement,$recup_client->police,$type_terrain);
									echo $montant_total."€";
									echo "<input name=\"montant_total".coller_jma($les_jours_a_tester)."\" type=\"hidden\"  value=\"".$montant_total."\">";
								?>
								</td>
								<td><?
									
									$acompte=calcul_acompte($les_jours_a_tester,$heure_saisie_Min,$montant_total);
									echo $acompte."€";
								?>
								</td>
								<td>
								<?
								if (est_min_manager($user) and $resa_mult=="1"){
									$temp=$nbre_terrains_a_reserver;
									foreach ($infos_terrain as $terrain){
										if ($temp>0)
											echo " <input type=\"checkbox\" name=\"terrain_choisit".coller_jma($les_jours_a_tester).$terrain[2]."\" value=\"".$terrain[3]."\" checked >".$terrain[2];
										
										$temp--;
									}
								}
								else {?>
									<select name="terrain_choisit">
									<?
									foreach ($infos_terrain as $terrain) echo "<option value=\"".$terrain[3]."\">".$terrain[2]."</option>";
									?>
									</select>
								<?}?>
								
								</td>
							</tr>
						<?
							}
						}
						if (est_min_agent($user)){?>
						<tr>
							<th>Commentaire</th>
							<td colspan="5">
							<textarea rows="4" cols="100" name="commentaire"><?
							if (test_non_vide($num_resa)){
								$ligne_commentaire_resa=recup_derniere_commentaire("id_resa",$num_resa);
								if ($ligne_commentaire_resa->Commentaire<>"") 
									echo $ligne_commentaire_resa->Commentaire;
							}
							?></textarea>
							</td>
						</tr>
						<?}
						else {?>
							<tr>
							<th>Infos g&eacute;n&eacute;rales</th>
							<td colspan="5">
							<br>Il est obligatoire de r&eacute;gler l’acompte minimum pour chaque r&eacute;servations
							<br>Une r&eacute;servation peut- être d&eacute;plac&eacute; jusqu'&agrave; 48h avant la r&eacute;servation
							<br>En cas d’annulation, l’acompte sera plac&eacute; sous la forme d’un avoir jusqu'&agrave; 48h avant la r&eacute;servation. Au-del&agrave; de ce d&eacute;lai votre acompte sera perdu.
							<br>Les r&eacute;servations ne peuvent pas être d&eacute;plac&eacute;es en ligne moins de 48h avant la r&eacute;servation.
							<br>Pour toutes infos et demandes suppl&eacute;mentaires merci de contacter l’accueil de votre centre au 01 49 51 27 04

							</td>
						</tr>
							
						<?}
						?>
					</table>
					<!--	Le créneau <font color="red"><? echo $heure_saisie_Min."-".$heure_saisie_Max; ?></font> du <font color="red">
					<? echo date_longue($date_Min); ?>
					</font> est disponible, vous devez cliquez ci-dessous sur le mode paiement de votre choix.<br /-->
					
					
					<input name="nbre_de_jours_a_tester" type="hidden"  value="<? echo $nbre_de_jours_a_tester; ?>">
					<input name="la_Frequence" type="hidden"  value="<? echo $_POST["Frequence"]; ?>">
					
					
					<input name="type_terrain" type="hidden"  value="<? echo $type_terrain; ?>">
					<input name="date_debut_resa" type="hidden"  value="<? echo $date_Min; ?>">
					<input name="date_fin_resa" type="hidden"  value="<? echo $date_Max; ?>">
					<input name="heure_debut_resa" type="hidden"  value="<? echo $heure_saisie_Min;?>">
					<input name="heure_fin_resa" type="hidden"  value="<? echo $heure_saisie_Max;?>">
					<input name="duree_resa" type="hidden"  value="<? echo $duree_resa;?>">
					<input name="id_client" type="hidden"  value="<? echo $id_client;?>">
					<input name="infos_client" type="hidden"  value="<? echo $tab_client[$id_client];?>">
					<input name="mois_resa" type="hidden"  value="<? echo $mois_fr[date("n", $date_longue)-1];?>">
					<input name="date_debut_resa_longue" type="hidden"  value="<? echo date_longue($date_Min)." de ".$heure_saisie_Min."-".($heure_saisie_Max);?>">

					<br /><br /><center>
					<table width="100%"><tr><td align="center">
			<?		if (test_non_vide($num_resa))
						echo "<input name=\"num_resa\" type=\"hidden\"  value=\"".$num_resa."\" >";
					echo "<input name=\"valide\" type=\"button\"  value=\"";
					if (test_non_vide($num_resa)) echo "Modifier ma r&eacute;servation";
					else echo "R&eacute;gler cette r&eacute;sa";
					echo "\" onclick=\"reserver()\"></td><td align=\"center\" valign=\"center\"><a target=blank href=\"libraries/ya2/devis.php?%3Afm&tmpl=component&print=1"
						."&layout=default&page=&option=com_content&date_debut_resa=".$date_Min."&date_fin_resa=".$date_Max
						."&heure_debut_resa=".$heure_saisie_Min."&heure_fin_resa=".$heure_saisie_Max."&id_client=".$id_client
						."&montant_total=".$montant_total."&sortie=I&devis_fact=DEVIS&tva=".recup_taux_TVA_d_une_date($date_Min)."\" />"
						."<img src=\"images/imprimante-icon.png\" title=\"Imprimer le devis\"></a> &nbsp;&nbsp;&nbsp;"
						."<a target=blank href=\"libraries/ya2/devis.php?%3Afm&tmpl=component&print=1"
						."&layout=default&page=&option=com_content&date_debut_resa=".$date_Min."&date_fin_resa=".$date_Max
						."&heure_debut_resa=".$heure_saisie_Min."&heure_fin_resa=".$heure_saisie_Max."&id_client=".$id_client
						."&montant_total=".$montant_total."&sortie=D&devis_fact=DEVIS&tva=".recup_taux_TVA_d_une_date($date_Min)."\" />"
						."<img src=\"images/PDF-Document-icon.png\" title=\"T&eacute;l&eacute;charger le devis\"></a> ";
					if ($recup_client->courriel<>"agent@footinfive.com"){
						$corps="Bonjour ".$recup_client->prenom." ".$recup_client->nom.","
							."%0A%0ANous faisons suite &agrave; votre demande de devis et vous prions, comme convenu, de bien vouloir trouver "
							." en pi&egrave;ce jointe notre proposition.%0A%0A"
							."Merci de nous retourner le devis sign&eacute; pour confirmer la date de resa."
							."%0A%0AL'&eacute;quipe du Foot In Five vous remercie de votre confiance !"
							."%0A%0AA bient&ocirc;t sur nos terrains..."
							."%0A%0AFOOT IN FIVE"
							."%0ACentre de FOOT en salle 5vs5"
							."%0A187 Route de Saint-Leu"
							."%0A93800 Epinay-sur-Seine"
							."%0ATel : 01 49 51 27 04"
							."%0AMail : contact@footinfive.com";
					
						echo "<a href=\"mailto:".$recup_client->courriel."?subject=Suite à votre demande de devis&body=".$corps."\">"
							.$recup_client->courriel."</a>";
					}
					?>
					</td></tr></table></form>
					
			<?
				
		}
		else {
			$tranche_30_mins_debut=$heure_saisie_Min;
			$tranche_30_mins_fin=decaler_heure($heure_saisie_Min,30);
			while(diff_dates_en_minutes("",$tranche_30_mins_fin,"",$heure_saisie_Max)>=0){
				/*echo "<br>".$tranche_30_mins_debut." * ".$tranche_30_mins_fin." test :"
					.diff_dates_en_minutes("",$tranche_30_mins_fin,"",$heure_saisie_Max)."<br>";*/
				if (is_array(trouve_dispo($type_terrain,$date_saisie_Min, $tranche_30_mins_debut,$tranche_30_mins_fin,$num_resa,true))) {
					$tranche_30_mins_debut=decaler_heure($tranche_30_mins_debut,30);
					$tranche_30_mins_fin=decaler_heure($tranche_30_mins_fin,30);
					//echo " ok !";
				}
				else break;
			}
			/*echo "<br>".$tranche_30_mins_debut." - ".$tranche_30_mins_fin
				." ------ ".diff_dates_en_minutes("",$tranche_30_mins_fin,"",$heure_saisie_Max)
				." ++++++++ ".$heure_saisie_Min." - ".$heure_saisie_Max."<br>";*/
			
			if (diff_dates_en_minutes("",$tranche_30_mins_fin,"",$heure_saisie_Max)<0 and $type_terrain<>3){
				echo "<a href=\"index.php/component/content/article?id=62"
					."&id_client=".$_GET["id_client"].$_POST["id_client"]."&num_resa=".$num_resa
					."&date_debut_resa=".$date_saisie_Min."&heure_fin_resa=".$heure_saisie_Max
					."&heure_debut_resa=".$heure_saisie_Min."&rubiks_cube=1\" />Tenter une optimisation ?</a><br><br>";

			}
			
			echo "D&eacute;sol&eacute; il n'y a plus de terrains disponibles entre <font color=red>";
			echo $heure_saisie_Min."-".$heure_saisie_Max." </font> pour la date du <font color=red>";
			echo date_longue($date_Min)."</font>.<br />Cr&eacute;neaux disponibles pour cette même date<br />";
				
			if (test_horaires_ouverture(decaler_heure($heure_saisie_Min,-6*60),"")==0) $horaire_Min=horaire_ouverture();
			else {
				if (diff_dates_en_minutes($date_Min,decaler_heure($heure_saisie_Min,-6*60))<0)
					$horaire_Min=date("H").":".date("i");
				else $horaire_Min=decaler_heure($heure_saisie_Min,-6*60);
			}
			if (test_horaires_ouverture("",decaler_heure($heure_saisie_Max,+6*60))==0) $horaire_Max=horaire_fermeture();
			else $horaire_Max=decaler_heure($heure_saisie_Max,+6*60);
			
			$lien="<a href=\"index.php/component/content/article?id=62";
			$lien.="&id_client=".$_GET["id_client"].$_POST["id_client"];
			$lien.="&num_resa=".$num_resa."&type_terrain=".$type_terrain;
				
				
				
			//en jouant une demi_heure de plus avec la meme heure de début
			if (test_horaires_ouverture($heure_saisie_Min, decaler_heure($heure_saisie_Max,30))<>0){
				if (is_array(trouve_dispo($type_terrain,$date_saisie_Min, $heure_saisie_Min, decaler_heure($heure_saisie_Max,30),$num_resa))) {
					echo "Dur&eacute;e : ".decaler_heure($duree_resa,30)."<br><li>";
					echo $lien."&duree_resa=".decaler_heure($duree_resa,30)."&date_debut_resa=".$annee."-".$mois."-".$jour;
					echo "&heure_debut_resa=".$heure_saisie_Min."#deb_form\" />";
					echo $heure_saisie_Min." &agrave; ".decaler_heure($heure_saisie_Max,30);
					echo " </a></li>";
				}
			}
			//en jouant une demi_heure de plus avec la meme heure de fin
			if (test_horaires_ouverture(decaler_heure($heure_saisie_Min,-30),$heure_saisie_Max)<>0){
				if (is_array(trouve_dispo($type_terrain,$date_saisie_Min, decaler_heure($heure_saisie_Min,-30), $heure_saisie_Max,$num_resa))){
					echo "Dur&eacute;e : ".decaler_heure($duree_resa,30);
					echo " &agrave; un horaire proche :<br><li>";
					echo $lien."&duree_resa=".decaler_heure($duree_resa,30)."&date_debut_resa=".$annee."-".$mois."-".$jour;
					echo "&heure_debut_resa=".decaler_heure($heure_saisie_Min,-30)."#deb_form\" />";
					echo decaler_heure($heure_saisie_Min,-30)." &agrave; ".$heure_saisie_Max;
					echo " </a></li>";
				}
			}
			//en enlevant une demi_heure avec la meme heure de début
			if (diff_dates_en_minutes("",$heure_saisie_Min,"",decaler_heure($heure_saisie_Max,-30))>=60){
				if (test_horaires_ouverture($heure_saisie_Min,decaler_heure($heure_saisie_Max,-30))<>0){
					if (is_array(trouve_dispo($type_terrain,$date_saisie_Min, $heure_saisie_Min, decaler_heure($heure_saisie_Max,-30),$num_resa))){
						echo "Dur&eacute;e : ".decaler_heure($duree_resa,-30)."<br><li>";
						echo $lien."&duree_resa=".decaler_heure($duree_resa,-30)."&date_debut_resa=".$annee."-".$mois."-".$jour;
						echo "&heure_debut_resa=".$heure_saisie_Min."#deb_form\" />";
						echo $heure_saisie_Min." &agrave; ".decaler_heure($heure_saisie_Max,-30);
						echo " </a></li>";
					}
				}
			}
			//en enlevant une demi_heure avec la meme heure de fin
			if (diff_dates_en_minutes("",decaler_heure($heure_saisie_Min,30),"",$heure_saisie_Max)>=60){
				if (test_horaires_ouverture(decaler_heure($heure_saisie_Min,30),$heure_saisie_Max)<>0){
					if (is_array(trouve_dispo($type_terrain,$date_saisie_Min, decaler_heure($heure_saisie_Min,30), $heure_saisie_Max,$num_resa))) {
						echo "Dur&eacute;e : ".decaler_heure($duree_resa,-30);
						echo " &agrave; un horaire proche :<br><li>";
						echo $lien."&duree_resa=".decaler_heure($duree_resa,-30)."&date_debut_resa=".$annee."-".$mois."-".$jour;
						echo "&heure_debut_resa=".decaler_heure($heure_saisie_Min,+30)."#deb_form\" />";
						echo decaler_heure($heure_saisie_Min,+30)." &agrave; ".$heure_saisie_Max;
						echo " </a></li>";
					}
				}
			}
			echo "<br><br>Même dur&eacute;e &agrave; un autre horaire :<br>";
	
			for ($i=-6;$i<7;$i++) {
				//echo "<br>".decaler_heure($heure_saisie_Min,30*$i)."-".decaler_heure($heure_saisie_Max,30*$i);
				if (test_horaires_ouverture(decaler_heure($heure_saisie_Min,30*$i),decaler_heure($heure_saisie_Max,30*$i))<>0){		
					//echo "pass";
					if (is_array(trouve_dispo($type_terrain,$date_saisie_Min, decaler_heure($heure_saisie_Min,30*$i), decaler_heure($heure_saisie_Max,30*$i),$num_resa))) {
						echo "<li>".$lien;
						echo "&duree_resa=".$duree_resa."&date_debut_resa=".$annee."-".$mois."-".$jour."&heure_debut_resa=";
						echo decaler_heure($heure_saisie_Min,30*$i)."#deb_form\" />".decaler_heure($heure_saisie_Min,30*$i);
						echo " &agrave; ".decaler_heure($heure_saisie_Max,30*$i);
						echo " </a></li>";
					}
				}
			}
			
		}
	}
	}
}
}
}
?>