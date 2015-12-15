<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();


$la_saison=recup_1_element("nom","Saison","id",$_GET["id_saison"]);

	if (test_non_vide($_GET["jour_sem"])){
	    $compl_req=" and  sc.id in (select id_Saison_cotisations from Saison_cotisations_jours_semaine "
		." where id_jours_semaine=(select id FROM jours_semaine where nom=\"".$_GET["jour_sem"]."\") ) and si.id_cotisation=sc.id and sc.id_saison=".$_GET["id_saison"];
	    $titre=" (cours du ".$_GET["jour_sem"].") ";
	    $titre_page="FEUILLE DE PRESENCE";
	}
	else if (test_non_vide($_GET["id_saison"])){
	    $compl_req=" and si.id_cotisation=sc.id and sc.id_saison=".$_GET["id_saison"];
	    $titre_page="COMMANDE DE TENUES";
	}

$requete="SELECT distinct(c.id_client), c.*, tr.nom as nom_categorie FROM `Saison_cotisations` as sc,Saison_inscription as si, Client c, "
	." Saison_regroupement as sr LEFT JOIN Type_Regroupement as tr on sr.id_type_regroupement=tr.id "
	." WHERE  c.id_client=si.id_client and c.id_client=sr.id_client  and sr.id_saison=sc.id_saison "
	.$compl_req."  and tr.id=".$_GET["categorie"]." order by prenom ";
//echo $requete;

$db->setQuery($requete);	
$resultat = $db->loadObjectList();

$fond_tab="#9BBB59";
$fond_tab_titre=" bgcolor=#38892E ";


echo "<div class=\"saut\"><center><H1>".$titre_page."</H1></center><b><u>Date du jour :</u></b> ".date("d-m-Y")." ".$titre." - <u><b>Saison :</u></b> ".$la_saison
	." - <u><b>Cat&eacute;gorie :</u></b> ".recup_1_element("nom","Type_Regroupement","id",$_GET["categorie"])." <br><br>";
echo "<center><table class=\"zebra\" border=1 width=\"100%\"  >";
	echo "<tr ><td valign=\"MIDDLE\" align=\"center\" height=\"30\" ".$fond_tab_titre."><b>NUM</b></td>";
		echo "<td valign=\"MIDDLE\" align=\"center\"  height=\"30\" ".$fond_tab_titre." ><b>SEXE</b></td>";
		echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\" ".$fond_tab_titre."><b>PRENOM</b></td>";
		echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\" ".$fond_tab_titre."><b>NOM</b></td>"
			."";
	if (test_non_vide($_GET["jour_sem"]))
		echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\" ".$fond_tab_titre."><b>AGE</b></td>";
	else foreach(liste_Type_equipement() as $type_equipement)
		echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\" ".$fond_tab_titre."><b>".$type_equipement->nom."</b></td>"; 
		
	echo "</tr>";
$i=0;
foreach ($resultat as $eleve){
	$i++;
	echo "<tr ><td valign=\"MIDDLE\" align=\"center\" height=\"30\">".$eleve->id_client."</td>";
		echo "<td valign=\"MIDDLE\" align=\"center\"  height=\"30\" >";
		if ($eleve->sexe==1)
			echo " <img src=\"images/m-sexe-icon.png\" title=\"gar&ccedil;on\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
		else echo " <img src=\"images/f-sexe-icon.png\" title=\"fille\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
		echo "</td>";
		echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\">".$eleve->prenom."</td>";
		echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\">".$eleve->nom."</td>";
	if (test_non_vide($_GET["jour_sem"]))
		echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\">".age($eleve->date_naissance)."</td>";
	else {
		foreach(liste_Type_equipement() as $type_equipement){
			$taille=taille_Saison_equipement($_GET["id_saison"],$eleve->id_client,$type_equipement->id);
			echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\">".recup_1_element("nom",$type_equipement->Type_taille,"id",$taille)."</td>";
		}
		
	}
	echo "</tr>";	
}
	
echo "</table><br>Total : ".$i." enfants</center></div>";


?>