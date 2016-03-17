<?php

defined('_JEXEC') or die( 'Restricted access' );


$user =& JFactory::getUser();
$db = & JFactory::getDBO();



if (isset($_POST["requetes"])) {
      /*$les_requetes= explode(";",str_replace("\\","",$_POST["requetes"]));
      foreach ($les_requetes as $req) {
          echo "Bah alors ? ".mysql_query($req);
      }      
      echo "<br />--les requetes :".$les_requetes."<br/>";*/
}
else {

$requete_presence="SELECT t.id as teamid, t.t_name, p.id as pid, p.first_name, p.nick, es.sel_value as vote ";
$requete_presence.="FROM #__bl_extra_select as es, #__bl_extra_values as ev, #__bl_players as p, #__bl_players_team as pt, #__bl_teams as t  ";
$requete_presence.="where f_id=13 and ev.uid=p.id and p.id=pt.player_id and t.id=pt.team_id and es.id=ev.fvalue and pt.season_id in (97) ";
$requete_presence.=" and p.nick<>\"CSC\" and es.sel_value<>\"\" order by pt.team_id,ev.fvalue";

//echo $requete_presence;

$db->setQuery($requete_presence);	
       
$resultat_presence = $db->loadObjectList();



if (!$resultat_presence) echo $prb;
else {

        $equipe_prec="";
	foreach ($resultat_presence as $row_presence ){
				if (($equipe_prec=="") or ($equipe_prec<>$row_presence->teamid)) {
				
					if ($equipe_prec<>$row_presence->teamid) {
					echo "</table>";
					
					}
					$equipe_prec=$row_presence->teamid;
					echo "<br /><table class=\"zebra\" border =1>";
					echo "<tr>";
					echo "<td align=\"center\" colspan=\"6\">".$row_presence->t_name."</td></tr>";
					echo "<tr><td align=\"center\">Id</td><td align=\"center\">Pr&eacute;nom</td><td align=\"center\">Flocage</td><td align=\"center\">vote</td></tr>";

				}	
					echo "<tr><td align=\"center\">".$row_presence->pid."</td>";
					echo "<td align=\"center\">".$row_presence->first_name."</td>";
					echo "<td align=\"center\">".$row_presence->nick."</td>";
					echo "<td align=\"center\">".$row_presence->vote."</td></tr>";
			
		}
		echo "</table>";
}

//echo "<br />delete from `#__bl_extra_values` where `f_id`=13;<br />";

}


?>