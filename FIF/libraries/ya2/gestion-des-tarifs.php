<?


require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

?>
<script type="text/javascript">
	
	function enregistrer() {
		document.register_tarif.submit()
	}
	function enregistrer2() {
		document.register_plage_tarif.submit()
	}
	function enregistrer3() {
		document.register_horaires.submit()
	}
	
</script>

<?

$user =& JFactory::getUser();
$db = & JFactory::getDBO();


$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {

menu_acces_rapide($_GET["id_client"],"Gestion des tarifs");

$requete_recup_Type_terrain="SELECT * FROM Type_terrain as tt WHERE  1 order by tt.id ";
	//echo "req77: ".$requete_recup_Type_terrain;
		
$db->setQuery($requete_recup_Type_terrain);	
$db->query();
		
$requete_recup_Type_terrain = $db->loadObjectList();

echo "<br><font color=\"red\">";

if (test_non_vide($_POST["nbre_tarifs"])){
	for($i=0;$i<=$_POST["nbre_tarifs"];$i++){
		foreach($requete_recup_Type_terrain as $recup_Type_terrain){
			$nom_champ="Tarif_".$i."_".$recup_Type_terrain->id;
			if (test_non_vide($_POST["$nom_champ"])){
				if (test_non_vide($_POST["Tarif_defaut_$i"]))
					$Tarif_defaut=$_POST["Tarif_defaut_$i"];
				else $Tarif_defaut=0;
				$requete_recup_maj_tarif="UPDATE `Tarif_periode` SET tarif_par_defaut=".$Tarif_defaut
						." , date_debut_periode=\"".$_POST["Date_debut_$i"]."\", date_fin_periode=\"".$_POST["Date_fin_$i"]
						."\" WHERE id_periode=".$i;
				//echo "<br>req88: ".$requete_recup_maj_tarif;
					
				$db->setQuery($requete_recup_maj_tarif);	
				$db->query();
			
			
				$requete_Tarif_periode_type_terrain="UPDATE `Tarif_periode_type_terrain` "
					." SET `montant_horaire`=".$_POST["$nom_champ"]." WHERE id_periode=".$i." and id_type_terrain=".$recup_Type_terrain->id;
				//echo "<br>req55: ".$requete_Tarif_periode_type_terrain;
				
				$db->setQuery($requete_Tarif_periode_type_terrain);	
				$db->query();
			}
		}
		
	}
	echo "Montants horaires mis &agrave; jour";
	
}


if (test_non_vide($_POST["horaire_ouverture"]) and test_non_vide($_POST["horaire_fermeture"])
    and test_non_vide($_POST["heure_fin_creuse_avec_remises"]) and test_non_vide($_POST["heure_fin_creuse_sans_remises"])){
	
	$requete_maj_horaire_ouverture="UPDATE Parametres SET valeur_parametre=\"".$_POST["horaire_ouverture"]."\" WHERE `nom_parametre`=\"heure_ouverture\" ";
	//echo "<br>requete: ".$requete_maj_horaire_ouverture;
	$db->setQuery($requete_maj_horaire_ouverture);	
	$db->query();	
	
	$requete_maj_horaire_fermeture="UPDATE Parametres SET valeur_parametre=\"".$_POST["horaire_fermeture"]."\" WHERE `nom_parametre`=\"heure_fermeture\" ";
	//echo "<br>requete: ".$requete_maj_horaire_fermeture;
	$db->setQuery($requete_maj_horaire_fermeture);	
	$db->query();
	
	$requete_maj_horaire_plage_tarif_1="UPDATE Plage_tarif SET heure_debut=\"".$_POST["horaire_ouverture"]."\",heure_fin=\"".$_POST["heure_fin_creuse_avec_remises"]."\" "
		." WHERE `id_plage_tarif`=1 ";
	//echo "<br>requete: ".$requete_maj_horaire_plage_tarif_1;
	$db->setQuery($requete_maj_horaire_plage_tarif_1);	
	$db->query();
	
	$requete_maj_horaire_plage_tarif_2="UPDATE Plage_tarif SET heure_debut=\"".$_POST["horaire_ouverture"]."\",heure_fin=\"".$_POST["horaire_fermeture"]."\" "
		." WHERE `id_plage_tarif`=2 ";
	//echo "<br>requete: ".$requete_maj_horaire_plage_tarif_2;
	$db->setQuery($requete_maj_horaire_plage_tarif_2);	
	$db->query();
	
	$requete_maj_horaire_plage_tarif_3="UPDATE Plage_tarif SET heure_debut=\"".$_POST["heure_fin_creuse_sans_remises"]."\", heure_fin=\"".$_POST["horaire_fermeture"]."\" "
		." WHERE `id_plage_tarif`=3 ";
	//echo "<br>requete: ".$requete_maj_horaire_plage_tarif_3;
	$db->setQuery($requete_maj_horaire_plage_tarif_3);	
	$db->query();
	
	$requete_maj_horaire_plage_tarif_4="UPDATE Plage_tarif SET heure_debut=\"".$_POST["heure_fin_creuse_avec_remises"]."\", heure_fin=\"".$_POST["heure_fin_creuse_sans_remises"]."\" "
		." WHERE `id_plage_tarif`=4 ";
	//echo "<br>requete: ".$requete_maj_horaire_plage_tarif_4;
	$db->setQuery($requete_maj_horaire_plage_tarif_4);	
	$db->query();
	
	echo "Horaires mis &agrave; jour";
	
}


echo "</font><br>";


	
	$horaire_ouverture=horaire_ouverture();
	$horaire_fermeture=horaire_fermeture();
	
	$heure_fin_creuse_avec_remises=recup_fin_heure_remise();
	
	$requete_recup_heure_fin_creuse_sans_remises="SELECT heure_fin FROM `Plage_tarif` WHERE id_plage_tarif=4 ";
	//echo "req88: ".$requete_recup_heure_fin_creuse_sans_remises;
		
	$db->setQuery($requete_recup_heure_fin_creuse_sans_remises);	
	$db->query();
	$heure_fin_creuse_sans_remises=$db->loadResult();
	
?>
	<h3>Horaires</h3>
	<form name="register_horaires" class="submission box" action="<?php echo JRoute::_( 'index.php/component/content/article?id=66'); ?>" method="post"  >
	<br>
	<table class="zebra" border="0"  >
		<tr>
			<th>Horaire ouverture</th>
			<td><input type="text" name="horaire_ouverture" value="<? echo $horaire_ouverture; ?>"/></td>

		</tr>
		<tr>
			<th>Fin heure creuse avec remises</th>
			<td><input type="text" name="heure_fin_creuse_avec_remises" value="<? echo $heure_fin_creuse_avec_remises; ?>"/></td>

		</tr>
		<tr>
			<th>Fin heure creuse sans remises</th>
			<td><input type="text" name="heure_fin_creuse_sans_remises" value="<? echo $heure_fin_creuse_sans_remises; ?>"/></td>

		</tr>		
		<tr>
			<th>Horaire fermeture</th>
			<td><input type="text" name="horaire_fermeture" value="<? echo $horaire_fermeture; ?>"/></td>

		</tr>		
		<tr>
			<th> </th>
			<td>
				<input name="suppr" type="button"  value="Modifier les horaires" onclick="enregistrer3()">
			</td>
		</tr>
	</table>
	</form>

<?
$requete_recup_infos_tarif="SELECT * FROM Tarif as t, `Tarif_periode` tp WHERE  t.id_tarif=tp.id_tarif order by tp.id_periode ";
//echo "req88: ".$requete_recup_infos_tarif;
	
$db->setQuery($requete_recup_infos_tarif);	
$db->query();
        
$resultat_recup_infos_tarif = $db->loadObjectList();

	
?>
	<h3>Tarifs</h3>
	<form name="register_tarif" class="submission box" action="<?php echo JRoute::_( 'index.php/component/content/article?id=66'); ?>" method="post"  >
	<br>
	<table class="zebra" border="0"  >
		<tr>
			<th>Plage</th><th>Libell&eacute;</th><th>Date debut</th><th>Date fin</th><?
			foreach($requete_recup_Type_terrain as $recup_Type_terrain)
				echo "<th>".$recup_Type_terrain->nom."</th>";
			?><th>Par<br>defaut</th></tr>
	<?
	
	$nbre_tarifs=0;
	foreach($resultat_recup_infos_tarif as $recup_infos_tarif){
		if ($nbre_tarifs<$recup_infos_tarif->id_periode)
			$nbre_tarifs=$recup_infos_tarif->id_periode;
	?>
			<tr>
				<th><? echo $recup_infos_tarif->libelle_tarif;?></th><th><? echo $recup_infos_tarif->libelle_periode;?></th>
				<td><input type="date" name="Date_debut_<? echo $recup_infos_tarif->id_periode; ?>" value="<? echo $recup_infos_tarif->date_debut_periode; ?>"/></td>
				<td><input type="date" name="Date_fin_<? echo $recup_infos_tarif->id_periode; ?>" value="<? echo $recup_infos_tarif->date_fin_periode; ?>"/></td>
				<?
				foreach($requete_recup_Type_terrain as $recup_Type_terrain)
					echo "<td><input size=\"5\" type=\"text\" name=\"Tarif_".$recup_infos_tarif->id_periode."_".$recup_Type_terrain->id
						."\" value=\"".recup_1_element("montant_horaire","Tarif_periode_type_terrain","id_type_terrain=".$recup_Type_terrain->id." and id_periode",$recup_infos_tarif->id_periode)."\"/></td>";
				?>
				<td><input type="checkbox" name="Tarif_defaut_<? echo $recup_infos_tarif->id_periode; ?>" value="1" 
					<? if ($recup_infos_tarif->tarif_par_defaut==1)
						echo " checked ";
					?>
				/></td>

			</tr>
	<?
	}
	?>
			<tr>
				<td colspan="6" align="right">
					<input type="hidden" name="nbre_tarifs" value="<? echo $nbre_tarifs; ?>"/>
					<input name="suppr" type="button"  value="Modifier les tarifs et les periodes" onclick="enregistrer()">
				</td>
			</tr>
	</table>
	</form>
	
<?

	
$requete_recup_infos_plage_tarif="SELECT * FROM `Plage_tarif` WHERE 1 order by Libelle ";
//echo "req88: ".$requete_recup_infos_plage_tarif;
	
$db->setQuery($requete_recup_infos_plage_tarif);	
$db->query();
        
$resultat_recup_infos_plage_tarif = $db->loadObjectList();
	
?>
	<h3>Plage des tarifs</h3>
	<br>
	<table class="zebra" border="0"  >
	<?
	foreach($resultat_recup_infos_plage_tarif as $recup_infos_plage_tarif){
	?>
			<tr>
				<th><? echo $recup_infos_plage_tarif->Libelle;?></th>
				<td><? echo $recup_infos_plage_tarif->heure_debut; ?> - <? echo $recup_infos_plage_tarif->heure_fin; ?></td>
			</tr>
	<?
	}
	?>
	</table>
	<?
}
?>