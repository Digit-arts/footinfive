<?php
defined('_JEXEC') or die( 'Restricted access' );

$user =& JFactory::getUser();
$db = & JFactory::getDBO();



if ($user->id==42 or $user->id==5455){

$requete_con_user="SELECT distinct(u.id), u.name, u.username, t.t_name as equipe,t.id as teamid,   ";
$requete_con_user.=" (select ph.ph_filename from #__bl_photos as ph where ph.id=(if(p.def_img>0,p.def_img,(select max(photo_id) ";
$requete_con_user .= " from #__bl_assign_photos as ass_ph where ass_ph.cat_type = 1 AND cat_id = p.id)))) as fichier,";
$requete_con_user.="concat(day(u.lastvisitDate),\"-\", month(u.lastvisitDate),\"-\",year(u.lastvisitDate)) as date, ";
$requete_con_user.="concat(hour(u.lastvisitDate)+1,\":\", minute(u.lastvisitDate)) as heure ";
$requete_con_user.=" FROM #__users as u, #__bl_players_team as pt, #__bl_players as p, #__bl_teams as t  ";
$requete_con_user.=" where pt.player_id=u.id and pt.team_id=t.id and p.id=pt.player_id ";
$requete_con_user.=" and u.lastvisitDate<> \"0000-00-00 00:00:00\" ORDER BY lastvisitDate DESC";

//echo $requete_con_user;

$db->setQuery($requete_con_user );
$resultat_con_user = $db->loadObjectList();

if (!$resultat_con_user) echo $prb;
else {
        $i=0;
	$jour="";
        $total=0;
        $total_date=0;

	foreach ($resultat_con_user as $con_user){

		if ($i==0) echo "<table class=zebra border=1>";
		else if ($jour<>$con_user->date) {
                           echo "</table><br />Total date : ".$total_date." connect&eacute;s<br />Total cumul : "
			   .$total." connect&eacute;s<br /><table class=zebra border=1>";
                           $total_date=0;
                }
		
		if ($jour<>$con_user->date) echo "<tr><td colspan=4 align=center>".$con_user->date."</td></tr>";
		echo "<tr><td><img src=\"media/bearleague/".$con_user->fichier."\" width=40 height=50 > "
			."<a href=\"index.php/ep?id_joueur=".$con_user->id."\">".$con_user->name."</a></td>"
			."<td><a href=\"index.php/accueil/gestion-des-equipes?id_equipe=".$con_user->teamid."\">".$con_user->equipe."</a></td>"
			."<td>".$con_user->username."</td><td>".$con_user->heure."</td></tr>";
		
		$jour=$con_user->date;
		$i++;
		$total_date++;
                $total++;
	}
	echo "</table><br />Au total : ".$total." connect&eacute;s";
}
}
?>
