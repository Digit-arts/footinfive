<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();
$config = JFactory::getConfig ();
$siteURL = $config->get ( 'site_url' );

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

    if (test_non_vide($_GET["extract_saison"]))
        extract_saison($_GET["extract_saison"]);


$chemin_joomsport=$siteURL."/index.php?option=com_joomsport&view=";

$chemin_joomsport_cal=$chemin_joomsport."calendar&sid=".$saison_coupe;
$chemin_joomsport_1t=$chemin_joomsport."table&gr_id=0&sid=".$saison_coupe;

$objectList=liste_tourn(" group by id_saison",1," t.id ",0);

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
	    echo " <a title=\"Re-activer ce tournoi\" onclick=\"recharger('Re-activer ces rencontres ?','archive?extract_saison=$liste_tourn->id_saison')\">"
	        ."<img src=\"images/stories/extract-icon.png\" ></a>";
		
	
	echo "<br>";
    }
    echo "</td>";
}

echo "</tr></table>";

?>