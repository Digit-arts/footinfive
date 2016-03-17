<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');


$user =& JFactory::getUser();
$db = & JFactory::getDBO();

if (isset($_POST["etat"])){
	$requete_efface_old_etat="delete from #__bl_extra_values where uid=".$user->id." and f_id=13";
       
       $db->setQuery($requete_efface_old_etat);	
       
       $resultat_efface_old_etat = $db->loadObjectList();
       
       $requete_insert_new_etat="INSERT INTO #__bl_extra_values (f_id, uid, fvalue) VALUES (\"13\",".$user->id.",\"".$_POST["etat"]."\")";
       $db->setQuery($requete_insert_new_etat);	
       
       $resultat_insert_new_etat = $db->loadObjectList();
}

if (!test_saisie($user->id)){
 
$query = "select team_id from #__bl_players_team as pt where pt.player_id=".$user->id." and pt.season_id in (97) LIMIT 0,1";

$db->setQuery($query);
$resultat_equipe_user = $db->loadObjectList();

foreach ($resultat_equipe_user as $equipe_user ){

$query = "SELECT m.m_time as heure, m.m_date as jour_match ,t.t_name as nom_equipe_adv, t.id, m.team1_id, m.team2_id "
	." FROM `#__bl_match` as m, #__bl_teams as t where t.id<>".$equipe_user->team_id ." and (t.id=m.`team1_id` or t.id=m.`team2_id`)"
	." and m.`published`=1 and m.`m_played`=0 and (m.`team1_id`=".$equipe_user->team_id."  or m.`team2_id`=".$equipe_user->team_id.")  "
	." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))<=10080 "
	." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))>0";

//echo $query;
//TIMESTAMPDIFF(MINUTE,NOW(),STR_TO_DATE('m.`m_date` m.m_time', '%Y-%m-%d %H:%i'))


$db->setQuery($query);
$resultat_equipe_adv = $db->loadObjectList();

if (empty($resultat_equipe_adv)) echo "Aucun match n&apos;est pr&eacute;vu pour dimanche prochain.<br />";
else {

foreach ($resultat_equipe_adv as $equipe_adv ){

$query = "SELECT u.email, p.id, p.first_name as Prenom, p.nick,"
	." (select es.id from #__bl_extra_select as es, #__bl_extra_values as ev where es.fid=13 and p.id=ev.uid and es.id=ev.fvalue) as vote"
	." FROM  #__bl_players as p LEFT OUTER JOIN #__users u ON p.usr_id = u.id, #__bl_teams as t "
	." where t.id=".$equipe_user->team_id." and t.id in (select pt.team_id FROM #__bl_players_team as pt Where p.id=pt.player_id and pt.season_id in (97)) "
	." and p.nick<>\"CSC\" order by u.email desc,vote asc";


$db->setQuery($query);
$vote_presence = $db->loadObjectList();

$jour_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$mois_fr = array("Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao�t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
list( $annee, $mois,$jour) = explode('-',$equipe_adv->jour_match);
$date_longue = mktime (0, 0, 0, $mois, $jour, $annee);

echo "Ce tableau concerne le match contre ".$equipe_adv->nom_equipe_adv." du ".$jour_fr[date("w", $date_longue)]." ".$jour." ".$mois_fr[date("n", $date_longue)-1]." ".$annee." &agrave; ".$equipe_adv->heure."<br /><br />";

$requete_liste_etat="SELECT * FROM #__bl_extra_select where fid=13 ORDER BY id";

$db->setQuery($requete_liste_etat);	
$resultat_liste_etat = $db->loadObjectList();

echo "<table class=\"zebra\">";
	
echo "<thead><tr><th align=\"center\">Pr&eacute;nom</th><th align=\"center\">Flocage</th><th align=\"center\">Au prochain match</th><th align=\"center\">Choix</th></tr></thead>";

	$rep=JURI::base();
	foreach ($vote_presence as $presence ){
		
		echo "<tr><td >";
                echo $presence->Prenom;
		echo "</td><td >";
                echo $presence->nick;
		echo "</td><td >"; 

		if ($user->id==$presence->id) {
                         echo "<FORM name=\"form\" class=\"submission box\" action=\"info-presence\" method=post >";
                       echo "<select name=\"etat\" size=1><option value=0>pas encore repondu</option>";
                       foreach($resultat_liste_etat as $liste_etat) {
                               echo "<option value=\"".$liste_etat->id."\"";
                               if ($presence->vote==$liste_etat->id) echo " selected ";
                               echo ">".$liste_etat->sel_value."</option>";
                       }
                       echo "</select><input name=\"valide\" type=\"submit\"  value=\"OK\" >";
                echo "</form>";
                }
                echo "</td><td >";
                switch ($presence->vote){
								
		case '185':	echo "<img src=\"".JURI::base()."images/stories/present.png\" title=\"pr&eacute;sent\" />";
					break;
		case '186':	echo "<img src=\"".JURI::base()."images/stories/absent.png\" title=\"absent\" />";
					break;
		case '187':	echo "<img src=\"".JURI::base()."images/stories/indecis.png\" title=\"ind&eacute;cis\"/>";
					break;	
		case '188':	echo "<img src=\"".JURI::base()."images/stories/depannage.png\" title=\"d&eacute;panneur\"/>";
					break;		
		case '189':	echo "<img src=\"".JURI::base()."images/stories/blesse.png\" title=\"bless&eacute;\" />";
					break;
		case '190':	echo "<img src=\"".JURI::base()."images/stories/malade.png\" title=\"malade\" />";
					break;
		default:	if (is_null($presence->email)) echo "<a href=\"index.php/ep/mon-equipe\"><img src=\"".JURI::base()."images/stories/no-email.png\" title=\"pas d&apos;email\" /></a>"; else echo "<img src=\"".JURI::base()."images/stories/pas-repondu.png\" title=\"pas encore r&eacute;pondu\" />";                                         break;
		}
		echo "</td></tr>";
		
	}
	echo "</table><br />";
}

echo "<img src=\"".$rep."images/stories/no-email.png\" title=\"no-email\" />pas d&apos;email&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src=\"".$rep."images/stories/pas-repondu.png\" title=\"pas encore r&eacute;pondu\" />pas encore r&eacute;pondu&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src=\"".$rep."images/stories/present.png\" title=\"pr&eacute;sent\" />pr&eacute;sent&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src=\"".$rep."images/stories/absent.png\" title=\"absent\" />absent&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src=\"".$rep."images/stories/indecis.png\" title=\"ind&eacute;cis\"/>ind&eacute;cis&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src=\"".$rep."images/stories/depannage.png\" title=\"d&eacute;panneur\"/>d&eacute;panneur&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src=\"".$rep."images/stories/blesse.png\" title=\"bless&eacute;\" />bless&eacute;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src=\"".$rep."images/stories/malade.png\" title=\"malade\" />malade";
}
}

echo  "<HR><h3>L&apos;id&eacute;e</h3>Pouvoir informer ton &eacute;quipe de ta pr&eacute;sence ou non au prochain match.<br>Ainsi tu facilites le travail de ton capitaine qui doit r&eacute;pondre chaque semaine de match aux fameuses questions :<br>- Serons-nous assez ?<br>- Qui sera absent ?<br>- Qui sera present ?<br />";

echo  "<h3>La m&eacute;thode</h3>Chaque jeudi, vendredi et samedi &agrave; 20h, l&apos;ensemble des joueurs du championnat re&ccedil;oivent un &eacute;tat (par email) leur mentionnant la liste des absents et pr&eacute;sents pour le prochain match.<br />";

echo  "<h3>Comment indiquer mon info pr&eacute;sence ?</h3>En allant dans ton \"espace perso\" et en cliquant sur \"info presence\".<br>Chaque fois que tu auras un match le dimanche, tu pourras voter.<br />";
}
else header("Location: ../ep");

?>
