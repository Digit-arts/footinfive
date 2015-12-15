<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');
?>	
<script type="text/javascript">
	
	function enregistrer() {
		document.ajout_eleve.submit()
	}
	function enregistrer2() {
		document.ajout_cotisation.submit()
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

if (test_non_vide($_POST["id_saison"])) $id_saison=$_POST["id_saison"];
else $id_saison=$_GET["id_saison"];

if (!test_non_vide($id_saison)) echo "Num&eacute;ro de saison absent...";
else {

if (test_non_vide($_POST["sc_id"])) $sc_id=$_POST["sc_id"];
else $sc_id=$_GET["sc_id"];

if (test_non_vide($_POST["sc_id"]) and test_non_vide($_POST["id_saison"]) and test_non_vide($_POST["montant_cotisation"])
	and test_non_vide($_POST["nom_saison"]) and test_non_vide($_POST["date_debut_saison"]) and test_non_vide($_POST["date_fin_saison"]) ){
	
	maj_infos_cotisation($_POST["sc_id"],$_POST["montant_cotisation"]);
	maj_infos_saison($_POST["id_saison"],$_POST["nom_saison"],$_POST["date_debut_saison"],$_POST["date_fin_saison"]);
	RAZ_jour_semaine_dans_cotisation($_POST["sc_id"]);
	for($i=1;$i<=7;$i++)
		if ($_POST["jours_semaine_$i"]==1)
			ajout_jour_semaine_dans_cotisation($i,$_POST["sc_id"]);
}
else {
	if (test_non_vide($_POST["id_saison"]) and test_non_vide($_POST["montant_cotisation"])){
		$sc_id=ajout_saison_cotisation($_POST["id_saison"],$_POST["montant_cotisation"]);
		for($i=1;$i<=7;$i++)
			if ($_POST["jours_semaine_$i"]==1)
				ajout_jour_semaine_dans_cotisation($i,$sc_id);
	}
}

if (test_non_vide($_GET["id_saison"]) and test_non_vide($_GET["id_client"]) and test_non_vide($_GET["suppr_info_taille"]))
	supprimer_equipement_du_eleve($_GET["id_saison"],$_GET["id_client"],$_GET["suppr_info_taille"]);

$requete_info_saison="SELECT s.*, s.id as s_id,sc.id as sc_id,s.nom as s_nom,sc.*, format(sc.montant_cotisation,2) as le_montant_total, "
	." (SELECT count(id_client) from Saison_inscription as si WHERE si.id_cotisation=sc.id) as nbre_inscrits,"
	." (select FORMAT(sum(reg2.montant_reglement),2) from Reglement as reg2 where reg2.validation_reglement=1 and "
	."  reg2.id_cotisation=sc.id and (reg2.id_remise is NULL or reg2.id_remise=0)) as total_versement_hors_remises,"
	." (select sum(reg.montant_reglement) from Reglement as reg where reg.validation_reglement=1 and reg.id_cotisation=sc.id)  as total_versement "
	." FROM `Saison_cotisations` as sc,Saison as s WHERE  sc.id_saison=s.id "
	." and s.id=".$id_saison;
if (test_non_vide($sc_id))
	$requete_info_saison.=" and sc.id=".$sc_id;
//echo $requete_info_saison;

$db->setQuery($requete_info_saison);	
$info_saison= $db->loadObjectList();

if (!$info_saison) echo "Num&eacute;ro de saison inexistant...";
else {
$nom_saison=recup_1_element("nom","Saison","id",$id_saison);
menu_acces_rapide("","Saison : ".$nom_saison);
if (test_non_vide($_GET["ajout_cotisation"]) or test_non_vide($_GET["modif_cotisation"])){
	echo "<h2>";
	if (test_non_vide($_GET["modif_cotisation"]))
		echo "Modifier la ";
	else echo "Ajouter une ";
	echo "cotisation</h2><br><FORM id=\"formulaire\" name=\"ajout_cotisation\" action=\"article?id=61\" method=\"post\" >"
		."<input name=\"id_saison\" type=\"hidden\" value=\"".$id_saison."\" />";
	if (test_non_vide($sc_id))
		echo "<input name=\"sc_id\" type=\"hidden\" value=\"".$sc_id."\" />";
	if (test_non_vide($_GET["modif_cotisation"]))
		echo "<input name=\"nom_saison\" type=\"text\"  placeholder=\"Nom saison\" value=\"".$nom_saison."\"/>"
			." du <input name=\"date_debut_saison\" type=\"date\" value=\"".recup_1_element("date_debut","Saison","id",$id_saison)."\"  />"
			." au <input name=\"date_fin_saison\" type=\"date\"  value=\"".recup_1_element("date_fin","Saison","id",$id_saison)."\" /><br>";
	echo "<input name=\"montant_cotisation\" type=\"text\"  placeholder=\"Montant cotisation\"  value=\"".recup_1_element("montant_cotisation","Saison_cotisations","id",$sc_id)."\" />";
	if (test_non_vide($_GET["modif_cotisation"]))
		$texte_bouton="Modifier";
	else $texte_bouton="Cr&eacute;er";
	
	afficher_check_tous_les_jours_de_semaine($sc_id);
	
	echo " <input name=\"valide\" type=\"button\"  value=\"".$texte_bouton." cette cotisation\" onclick=\"enregistrer2()\" /></form><br>";
}
else {
	echo "<a href=\"index.php/component/content/article?id=61&ajout_cotisation=1&id_saison=".$id_saison."\"/>Ajouter une cotisation</a><br><br>";
	
if (test_non_vide($_GET["suppr_client_saison"]) and test_non_vide($_GET["id_client"]) and test_non_vide($_GET["sc_id"])){
	delete_eleve_saison_cotisation($_GET["id_client"],$_GET["sc_id"]);
	supprimer_equipement_du_eleve($_GET["id_saison"],$_GET["id_client"]);
	echo "<font color=red>eleve supprimé</font><br><br>";
}

if (test_non_vide($_GET["suppr_regroupement_client_saison"]) and test_non_vide($_GET["id_client"]) and test_non_vide($_GET["id_saison"]))
	supprimer_regroupement_du_eleve($_GET["id_saison"],$_GET["id_client"]);



?>
<FORM id="formulaire" name="ajout_eleve" class="submission box" action="article?id=61&sc_id=<? echo $sc_id; ?>&id_saison=<? echo $id_saison; ?>" method="post" >
<?


foreach($info_saison as $une_cotisation){
	
echo menu_deroulant_eleve("Saison_inscription","id_cotisation",$une_cotisation->sc_id)
	." <input name=\"valide\" type=\"button\"  value=\"Ajouter cet eleve\" onclick=\"enregistrer()\" /><hr><br>";
	
	$nom_champ="ajout_eleve_".$une_cotisation->sc_id;
	if (test_non_vide($_POST["$nom_champ"]))
		ajout_eleve_saison_cotisation($_POST["$nom_champ"],$une_cotisation->sc_id);
		
$requete_info_inscription_client_saison="SELECT c.*,(select FORMAT(sum(reg2.montant_reglement),2) from Reglement as reg2 where reg2.validation_reglement=1 and "
	."  reg2.id_cotisation=sc.id and c.id_client=reg2.id_client and (reg2.id_remise is NULL or reg2.id_remise=0)) as total_versement_hors_remises,"	
	." (select sum(reg.montant_reglement) from Reglement as reg where reg.validation_reglement=1 and reg.id_cotisation=sc.id "
	." and reg.id_client=c.id_client)  as total_versement,sc.montant_cotisation  "	
	." FROM `Saison_cotisations` as sc,Saison_inscription as si, Client c "
	." WHERE  si.id_cotisation=sc.id and c.id_client=si.id_client"
	." and sc.id=".$une_cotisation->sc_id." order by prenom";

//echo $requete_info_inscription_client_saison;

$db->setQuery($requete_info_inscription_client_saison);	
$info_inscription_client_saison= $db->loadObjectList();


echo "<table class=\"zebra\" >";
	echo "<tr><td colspan=10 align=\"center\">";
	
	foreach(explode("<br>",recup_liste_nom_jours_semaine_cotisation($une_cotisation->sc_id)) as $jour_sem)
		if (test_non_vide($jour_sem))
			echo "<b>".$jour_sem."</b> ( ".liste_categories_avec_feuille("",$id_saison,$jour_sem)." )<br>";
	
	echo "</td></tr><tr><th>Categorie</th>";
		
	foreach(liste_Type_equipement() as $type_equipement)
		echo "<th>".$type_equipement->nom."</th>";
		
	echo "<th>Eleve</th><th>Montant<br>cotisation</th><th>montant<br>vers&eacute;</th>"
		."<th>Remise</th><th>Reste<br>&agrave; payer</th><th>Suppr</th></tr>";
		
$les_erreurs="";

foreach($info_inscription_client_saison as $client_saison){
	echo "<tr><td>";
		$nom_champ_categorie="Type_Regroupement_".$client_saison->id_client."_".$id_saison;
		
		if (test_non_vide($_POST["$nom_champ_categorie"]))
			ajouter_regroupement_du_eleve($id_saison,$client_saison->id_client,$_POST["$nom_champ_categorie"]);
			
		$categorie=recup_type_regroupement_client_Saison($id_saison,$client_saison->id_client);
		if ($categorie==0)
			menu_deroulant_simple("Type_Regroupement",$id_type_regroupement,"","_".$client_saison->id_client."_".$id_saison);
		else echo "<a href=\"index.php/component/content/article?id=61&suppr_regroupement_client_saison="
				.$categorie."&id_saison=".$id_saison."&sc_id=".$une_cotisation->sc_id."&id_client="
				.$client_saison->id_client."\"/ title=\"supprimer cette info\">"
				.recup_1_element("nom","Type_Regroupement","id",$categorie)."</a>";
		
		
		
	echo "</td>";
		foreach(liste_Type_equipement() as $type_equipement){
			echo "<td>";
			$nom_champ_taille=$type_equipement->Type_taille."_".$client_saison->id_client."_".$type_equipement->id;
			if (test_non_vide($_POST["$nom_champ_taille"]))
				ajouter_equipement_du_eleve($id_saison,$client_saison->id_client,$type_equipement->id,$_POST["$nom_champ_taille"]);
			
			$taille=taille_Saison_equipement($id_saison,$client_saison->id_client,$type_equipement->id);	
			if ($taille==0)
				menu_deroulant_simple($type_equipement->Type_taille,$Taille,"","_".$client_saison->id_client."_".$type_equipement->id);
			else echo "<a href=\"index.php/component/content/article?id=61&suppr_info_taille="
				.$type_equipement->id."&id_saison=".$id_saison."&sc_id=".$une_cotisation->sc_id."&id_client="
				.$client_saison->id_client."\"/ title=\"supprimer cette info\">"
				.recup_1_element("nom",$type_equipement->Type_taille,"id",$taille)."</a>";
			echo "</td>";
		}
		echo "<td>";
		if ($client_saison->sexe==1)
			echo " <img src=\"images/m-sexe-icon.png\" title=\"gar&ccedil;on\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
		else echo " <img src=\"images/f-sexe-icon.png\" title=\"fille\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
		
		echo "<a href=\"index.php/component/content/article?id=60&id_client=".$client_saison->id_client."\"/>".$client_saison->prenom." ".$client_saison->nom."</a> ";
		$ligne_commentaire_client=recup_derniere_commentaire("id_client",$client_saison->id_client);
		if ($ligne_commentaire_client->Commentaire<>"" and est_min_agent($user))
			echo "<img src=\"images/Comment-icon.png\" title=\"".$ligne_commentaire_client->Commentaire."\">";
		echo "</td><td>".format_fr($client_saison->montant_cotisation)."</td>";
		$total_cotisations+=$client_saison->montant_cotisation;
		echo "<td><a href=\"index.php/component/content/article?id=81&sc_id=".$une_cotisation->sc_id
			."&id_client=".$client_saison->id_client."\"/>".format_fr($client_saison->total_versement_hors_remises)."</a></td>";
		echo "<td>";
		if (($client_saison->total_versement-$client_saison->total_versement_hors_remises)>0)
			echo "<font color=blue>";
		echo format_fr($client_saison->total_versement-$client_saison->total_versement_hors_remises)."</font></td>";
		$total_remises+=($client_saison->total_versement-$client_saison->total_versement_hors_remises);
		
		echo "<td>";
		if (($client_saison->montant_cotisation-$client_saison->total_versement)>0)
			echo "<font color=red>";
		echo format_fr($client_saison->montant_cotisation-$client_saison->total_versement)."</font></td>";
		$total_versements+=$client_saison->total_versement_hors_remises;
		if ($client_saison->total_versement>0)
			$lien="";
		else $lien="<a href=\"index.php/component/content/article?id=61&suppr_client_saison=1&id_saison=".$id_saison."&sc_id="
			.$une_cotisation->sc_id."&id_client=".$client_saison->id_client."\"/><img src=\"images/supprimer.png\" title=\"desincrire cet eleve\"/></a>";
		$total_reste_a_payer+=($client_saison->montant_cotisation-$client_saison->total_versement);
		echo "<td>".$lien."</td>";
	echo "</tr>";

}
echo "<tr valign=top><td colspan=5 align=right><b>Totaux</b></td><td><b>".format_fr($total_cotisations)."</b></td><td><b>".format_fr($total_versements)
	."</b></td><td><b><font color=blue>".format_fr($total_remises)."</font></b><br>".format_fr(($total_remises/$total_cotisations)*100)."%</td><td><b><font color=red>".format_fr($total_reste_a_payer)."</font></b></td><td></td></tr></table><br>";
$total_cotisations_saison+=$total_cotisations;
$total_remises_saison+=$total_remises;
$total_versements_saison+=$total_versements;
$total_reste_a_payer_saison+=$total_reste_a_payer;
$total_cotisations=0;
$total_versements=0;
$total_reste_a_payer=0;
$total_remises=0;

}
echo "</form>";
if (!test_non_vide($sc_id))
	echo "<table  class=\"zebra\" ><tr valign=top><td align=center><b>Totaux de la saison</b></td><td><b>".format_fr($total_cotisations_saison)."</b></td><td><b>".format_fr($total_versements_saison)
	."</b></td><td><b><font color=blue>".format_fr($total_remises_saison)."</font></b><br>".format_fr(($total_remises_saison/$total_cotisations_saison)*100)."%</td><td><b><font color=red>".format_fr($total_reste_a_payer_saison)."</font></b></td><td></td></tr></table>"; 
}
}
}
}
?>