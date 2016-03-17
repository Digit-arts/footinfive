<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

?>
<script type="text/javascript">
	
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
				}
			}
	}

	
</script>
<?

    if (test_non_vide($_GET["archiv_saison"]))
        archiver_saison($_GET["archiv_saison"]);


if (isset($_POST["mon_titre"]) and isset($_POST["mon_texte"]) ){

    $requete_efface_texte_accueil="DELETE FROM `Message_accueil` WHERE 1";
    //echo $requete_efface_texte_accueil;
    $db->setQuery($requete_efface_texte_accueil);	
    $db->query();


    $requete_modif_texte_accueil="INSERT INTO `Message_accueil`(`Titre`, `Texte`, `Image`)  "
	." VALUES (\"".$_POST["mon_titre"]."\",\"".$_POST["mon_texte"]."\",\"".$_POST["mon_image"]."\")";
    //echo $requete_modif_texte_accueil;
    $db->setQuery($requete_modif_texte_accueil);	
    $db->query();


}

$chemin_joomsport="http://www.footinfive.com/LEDG/index.php?option=com_joomsport&view=";

$chemin_joomsport_cal=$chemin_joomsport."calendar&sid=".$saison_coupe;
$chemin_joomsport_1t=$chemin_joomsport."table&gr_id=0&sid=".$saison_coupe;

$objectList=liste_tourn(" group by id_saison",1," t.id ");

echo "<table border=0 width =\"100%\"><tr>";

for ($i=1;$i<=3;$i++){
    switch ($i){
	case 1 : $nom_colone="Calendrier";$nom_lien="calendar";break;
	case 2 : $nom_colone="Classement";$nom_lien="table&gr_id=0";break;
	case 3 : $nom_colone="Statistiques";$nom_lien="playerlist";break;
    }
    echo "<td>";
    echo "<h1 class=title>".$nom_colone."</h1>";
    foreach ($objectList as $liste_tourn) {
	
	echo "<a class=\"button-more\"  href=\"".$chemin_joomsport.$nom_lien."&sid=".$liste_tourn->id_saison."\">"
	    .$liste_tourn->nom_tourn."</a> ";
	    
	if (est_agent($user) and $i==1)
	    echo " <a title=\"Archiver ce tournoi\" onclick=\"recharger('Archiver ces rencontres ?','index.php?option=com_content&view=article&id=43&archiv_saison=$liste_tourn->id_saison')\">"
	        ."<img src=\"images/stories/archive-icon.png\" ></a>";
		
	
	echo "<br>";
    }
    echo "</td>";
}

echo "</tr></table>";

if (est_agent($user))
    echo "<FORM name=\"form_saisie\" class=\"submission box\" action=\"index.php?option=com_content&view=article&id=43\" method=post >";
    
    $requete_recup_texte="SELECT * FROM `Message_accueil` LIMIT 0,1 ";
    //echo "<br>req: ".$requete_recup_texte;
    $db->setQuery($requete_recup_texte);	
    $db->query();
    $texte=$db->LoadObject();
    
    if (test_non_vide($texte->Titre))
	echo "<HR><h1 class=title>";
    
    if (!est_agent($user))
	    echo $texte->Titre;
    else echo "Mon titre <input type=\"text\" name=\"mon_titre\" value=\"".$texte->Titre."\"/>";
    
    if (test_non_vide($texte->Titre))
	echo "</h1>";
    
    if (!est_agent($user))
	echo "<font color=\"NavajoWhite\" size=\"3\">".$texte->Texte."</font>";
    else echo "mon texte<br><textarea name=\"mon_texte\" rows=\"10\" cols=\"50\" />".$texte->Texte."</textarea>";
    echo "<br>";
    

if (est_agent($user))
    echo "<center><input name=\"valide\" type=\"submit\"  value=\"enregistrer\" ></center></form>";



?>