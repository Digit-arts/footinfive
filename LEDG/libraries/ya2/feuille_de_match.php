<?php



$user =& JFactory::getUser();
$db = & JFactory::getDBO();


$requete = "SELECT p.id as Num_Joueur, p.first_name as Prenom, p.nick as Flocage, md.id, m.m_date as Date, m.m_time as creneau, m.m_location as Terrain, m.id as match_id,  ";
$requete .= " t.id as Num_Equipe, t.t_name as Nom_Equipe, pt.player_id, (select ph.ph_filename from #__bl_photos as ph where ph.id=(if(p.def_img>0,p.def_img,(select max(photo_id) ";
$requete .= " from #__bl_assign_photos as ass_ph where ass_ph.cat_type = 1 AND cat_id = p.id)))) as filename, (SELECT (fvalue-21) as num ";
$requete .= " FROM  #__bl_extra_values where uid=p.id and f_id=4)as Num_maillot, (SELECT u.email FROM  #__users as u where u.id=p.usr_id)as email FROM `#__bl_match` as m, #__bl_teams as t, ";
$requete .= "   #__bl_matchday as md, #__bl_players_team as pt,  #__bl_players as p where (m.`team1_id`=t.id or m.`team2_id`=t.id) ";
$requete .= " and m.id=\"".$_GET["Num_Match"]."\" and md.id=m.m_id and pt.season_id=md.s_id and t.id=pt.team_id and pt.player_id=p.id and p.nick<>\"CSC\" ";
$requete .= " order by  creneau, Terrain,m.id, t.t_name, Num_maillot";

//echo $requete;

$db->setQuery($requete);	
$resultat = $db->loadObjectList();

$fond_tab="#9BBB59";
$fond_tab_titre="#38892E";

$i=0;

foreach ($resultat as $joueur){
	
	if ($i==0 ){
		if ($i==0){
			echo "<center><div class=\"saut\"><table border=5 cellpadding=\"2\" width=\"100%\" height=\"100%\" >";
			echo "<tr bgcolor=\"".$fond_tab_titre."\"><td align=\"left\" width=\"50%\"  ><b>Journ&eacute;e : </b>".$joueur->Date."</td><td align=\"left\" width=\"50%\"  ><b>Num_Match : </b>".$joueur->match_id."</td>";
			echo "<td align=\"center\" width=\"50%\" colspan=\"2\" ><b>Terrain : </b>".$joueur->Terrain." - <b>Creneau :</b> ".$joueur->creneau."</td></tr>";
		}				
		echo "<td align=\"center\" valign=\"top\" colspan=\"2\" nowrap height=\"100%\">";
			echo "<table class=\"zebra\" border=1 width=\"100%\"  >";
				echo "<tr><td  align=\"center\" nowrap height=\"30\" colspan=\"7\" valign=\"MIDDLE\"><b>";
				echo $joueur->Nom_Equipe;
				echo "</b></td></tr><tr ><td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>PH</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>PRENOM</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>FLOCAGE</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>NUM</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\"  height=\"30\" bgcolor=\"#808080\"><b>ABS</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" width=\"250\" height=\"30\"><b>MT1</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" width=\"250\" height=\"30\"><b>MT2</b></td></tr>";
	}
	else {
		if ($Num_Equipe_prec<>$joueur->Num_Equipe){
			echo "<tr ><td align=\"center\" colspan=\"5\" bgcolor=\"".$fond_tab_titre."\" height=\"25\">TOTAL DES BUTS PAR MI-TEMPS</td>";
			echo "<td height=\"25\">&nbsp;</td><td height=\"25\">&nbsp;</td></tr>";
			echo "<tr ><td align=\"center\" colspan=\"5\" bgcolor=\"".$fond_tab_titre."\" height=\"25\">TOTAL DES BUTS DU MATCH</td>";
			echo "<td height=\"25\"  colspan=\"2\">&nbsp;</td></tr></table></td>";
			echo "<td align=\"center\" valign=\"top\" colspan=\"2\" nowrap height=\"100%\">";
			echo "<table class=\"zebra\" border=1 width=\"100%\"  >";
				echo "<tr><td  align=\"center\" nowrap height=\"30\" colspan=\"7\" valign=\"MIDDLE\"><b>";
				echo $joueur->Nom_Equipe;
				echo "</b></td></tr><tr ><td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>PH</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>PRENOM</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>FLOCAGE</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" height=\"30\"><b>NUM</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\"  height=\"30\" bgcolor=\"#808080\"><b>ABS</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" width=\"250\" height=\"30\"><b>MT1</b></td>";
				echo "<td valign=\"MIDDLE\" align=\"center\" width=\"250\" height=\"30\"><b>MT2</b></td></tr>";
			
		}
	}
	
	$Num_Equipe_prec=$joueur->Num_Equipe;
	$i++;

	echo "<tr ><td align=\"center\" height=\"40\" ><img valign=\"middle\" src=\"".JURI::base()."/media/bearleague/".$joueur->filename."\" border=0  width=\"30\" height=\"38\"  /></td>";
	echo "<td align=\"center\" ><font size=\"2\">&nbsp;".$joueur->Prenom."</font>";
	if (is_null($joueur->email))
		echo "<br><img valign=\"middle\" src=\"".JURI::base()."/images/stories/no-email.png\" border=0  />";
	echo "</td>";
	echo"<td align=\"center\" nowrap ><font size=\"2\">&nbsp;".$joueur->Flocage."</font></td>";
	
	
	echo "<td align=\"center\" >&nbsp;";
	if ($joueur->Num_maillot>=0)
		echo $joueur->Num_maillot;
	echo "</td>";
	echo "<td bgcolor=\"#808080\">&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td></tr>";	
}
echo "<tr ><td align=\"center\" colspan=\"5\" bgcolor=\"".$fond_tab_titre."\" height=\"25\">TOTAL DES BUTS PAR MI-TEMPS</td><td height=\"25\">&nbsp;</td><td height=\"25\">&nbsp;</td></tr>";
echo "<tr ><td align=\"center\" colspan=\"5\" bgcolor=\"".$fond_tab_titre."\" height=\"25\">TOTAL DES BUTS DU MATCH</td><td height=\"25\"  colspan=\"2\">&nbsp;</td></tr></table>";
		
echo "</td></tr><tr><td align=\"center\" nowrap valign=\"MIDDLE\" height=\"70\" width=\"30\">&nbsp;</td><td height=\"70\" width=\"50%\">&nbsp;</td><td align=\"center\" nowrap valign=\"MIDDLE\" height=\"70\" width=\"30\">&nbsp;</td><td height=\"70\" width=\"50%\">&nbsp;</td></tr><tr><td align=\"center\" nowrap valign=\"MIDDLE\" height=\"20\" width=\"50\"  >SIGNATURE</td><td height=\"20\" width=\"50%\" align=\"center\" >COMMENTAIRES</td><td align=\"center\" nowrap valign=\"MIDDLE\" height=\"20\" width=\"50\"  >SIGNATURE</td><td height=\"20\" width=\"50%\" align=\"center\" >COMMENTAIRES</td></tr>";
echo "</table></center></div>";


?>