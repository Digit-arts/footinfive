<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();



if (isset($_POST["vote_select"])){
    $requete_efface_old_vote="delete from #__bl_extra_values where uid=".$user->id." and f_id=10";
    
    $db->setQuery($requete_efface_old_vote);	
    
    $resultat_efface_old_vote = $db->loadObjectList();
    
    $requete_insert_new_vote="INSERT INTO #__bl_extra_values (f_id, uid, fvalue) VALUES (\"10\",".$user->id.",\"".$_POST["vote_select"]."\")";
    $db->setQuery($requete_insert_new_vote);	
    //echo $requete_insert_new_rep;
    
    $resultat_insert_new_vote = $db->loadObjectList();
}

if (!test_saisie($user->id)){

$query = "select team_id from #__bl_players_team as pt where pt.player_id=".$user->id." and pt.season_id in (".liste_saisons_avec_virgules($user->id).") LIMIT 0,1";

$db->setQuery($query);
$resultat_equipe_user = $db->loadObjectList();
foreach ($resultat_equipe_user as $equipe_user ){

$query = "SELECT (TO_DAYS(NOW())-TO_DAYS(m.`m_date`)) as nbre_jours, m.m_time as heure, m.m_date as jour_match ,"
    ." t.t_name as nom_equipe_adv, t.id, m.team1_id, m.team2_id FROM `#__bl_match` as m, #__bl_teams as t "
    ." where t.id<>".$equipe_user->team_id ." and (t.id=m.`team1_id` or t.id=m.`team2_id`) and m.`published`=1 "
    ." and (m.`team1_id`=".$equipe_user->team_id."  or m.`team2_id`=".$equipe_user->team_id.") "
    ." and TIMESTAMPDIFF(MINUTE,CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)),NOW())<=10080 and "
    ." TIMESTAMPDIFF(MINUTE,CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)),NOW())>0";
//echo $query;
$db->setQuery($query);
$resultat_equipe_adv = $db->loadObjectList();

if (empty($resultat_equipe_adv)) echo "Aucun match n&apos; a &eacute;t&eacute; jou&eacute; dimanche dernier.<br />";
else {

foreach ($resultat_equipe_adv as $equipe_adv ){

$query = "SELECT u.email, p.id, p.first_name as Prenom, p.nick,"
        ."(select es.sel_value from #__bl_extra_select as es, #__bl_extra_values as ev where es.fid=10 and p.id=ev.uid and es.id=ev.fvalue) as vote  "
        ." FROM  #__bl_players as p LEFT OUTER JOIN #__users u ON p.usr_id = u.id, #__bl_teams as t "
        ." where t.id=".$equipe_user->team_id." and t.id in (select pt.team_id FROM #__bl_players_team as pt Where p.id=pt.player_id and pt.season_id in (".liste_saisons_avec_virgules($user->id).")) "
	." and p.nick<>\"CSC\" order by p.id";
//echo $query;
$db->setQuery($query);
$vote_joueurs = $db->loadObjectList();

$jour_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$mois_fr = array("Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
list( $annee, $mois,$jour) = explode('-',$equipe_adv->jour_match);
$date_longue = mktime (0, 0, 0, $mois, $jour, $annee);

echo "Ce vote concerne le match contre ".$equipe_adv->nom_equipe_adv." du ".$jour_fr[date("w", $date_longue)]." ".$jour." ".$mois_fr[date("n", $date_longue)-1]." ".$annee." &agrave; ".$equipe_adv->heure."<br /><br />";

echo "<table class=\"zebra\">";
	
echo "<thead><tr><th align=\"center\">Pr&eacute;nom</th><th align=\"center\">Flocage</th><th align=\"center\"> a vot&eacute; ?</th><th align=\"center\">Pour</th><th align=\"center\">Contre</th></tr></thead>";

	$pour=0;
	$contre=0;
	$rep=JURI::base();
	foreach ($vote_joueurs as $joueur){
	
		echo "<tr><td>";
                echo $joueur->Prenom;
		echo "</td><td>";
                echo $joueur->nick;
		echo "</td><td >"; 
		if (($user->id==$joueur->id) and ($equipe_adv->nbre_jours<6)) {
                       echo "<FORM name=\"form\" class=\"submission box\" action=\"point-fairplay\" method=post >";
                       echo "<select name=\"vote_select\" size=1><option value=\"182\"";
                       if (($joueur->vote=="") or ($joueur->vote=="1")) echo " SELECTED ";
                       echo " > pour</option><option value=\"183\"";
                       if ($joueur->vote=="0") echo " SELECTED ";
                       echo " > contre</option>";
                       echo "</select><input name=\"valide\" type=\"submit\"  value=\"OK\" >";
                echo "</form>";
                }
                else {
                     if (is_null($joueur->email))
                        echo "<a href=\"index.php/ep/mon-equipe\"><img src=\"".JURI::base()."images/stories/no-email.png\" title=\"pas d&apos;email\" /></a>";
                     else
                        if (is_null($joueur->vote))
                            echo "<img src=\"".$rep."images/stories/vote-no.png\" />";
		     if (($joueur->vote=="0") or ($joueur->vote=="1")) echo "<img src=\"".$rep."images/stories/vote-yes.png\" />";
                }
			
		echo "</td><td >";
		
		if (($joueur->vote=="") or ($joueur->vote==1)){
					echo "<img src=\"".$rep."images/stories/icon-fairplay.png\" />";
					$pour++;
		}
		
		echo "</td><td >";
		if ($joueur->vote=="0") {
					echo "<img src=\"".$rep."images/stories/icon-no-fairplay.png\" />";
					$contre++;
		}
		echo "</td></tr>";
		
	}
	echo "<tr><td align=\"right\" colspan=3>Sans modifications, ";
        if ($pour>=$contre) echo "le point FairPlay sera accord&eacute; samedi. Total : "; else echo "le point FairPlay sera refus&eacute; samedi";
	echo "</td><td >";
        echo $pour;
	echo "</td><td >";
        echo $contre;
	echo "</td></tr></table>";

}
}
}
echo  "<HR><h3>L&apos;id&eacute;e</h3>Chaque joueur a la possibilit&eacute; de noter l&apos;&eacute;quipe qu&apos;il aura rencontr&eacute;.<br />";

echo  "<h3>Comment ?</h3>En allant dans votre espace perso et en d&eacute;cidant d&apos;affecter ou non un point fairplay &agrave; l&apos;&eacute;quipe adverse.<br />";

echo  "<h3>D&eacute;lais</h3>Vous avez &agrave; chaque fois jusqu&apos;au vendredi suivant pour noter l&apos;&eacute;quipe adverse.<br />";

echo  "<h3>La m&eacute;thode</h3>En fonction des votes des joueurs durant la semaine, le point fairplay ne sera attribu&eacute; (le samedi) que si une majorit&eacute; des joueurs avaient vot&eacute;s &apos;pour&apos;. Attention le vote par d&eacute;faut est &apos;pour&apos;.<br />";

echo  "<h3>Les points et le classement</h3>Une victoire vous permet d&apos;obtenir 3 pts, un nul 1 pt et une d&eacute;faite 0 pt.<br />";

echo  "Evidemment, ce point fairplay s&apos;ajoute aux points des victoires, nuls et d&eacute;faites pour faire le total g&eacute;n&eacute;ral des points qui permet d&apos;&eacute;tablir le classement du championnat.<br />";

echo  "<br /><font color=red>De plus, une &eacute;quipe qui ne totalise pas un minimum de 15 points fairplay passera automatiquement en liste d&apos;attente pour la saison prochaine. &Ccedil;a voudra dire que la r&eacute;inscription sera automatique qu&apos;&agrave; partir de 15 points fairplay minimum pour la D1, D2 et D3.</font><br />";
}
else header("Location: ../ep");
?>