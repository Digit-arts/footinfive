#!/usr/local/bin/php5

<?php

set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries');


$mysqli = new mysqli("localhost", "LEDG20132014TEST", "4lulu9", "LEDG-2013-2014");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

$requete_recup_vote_fairplay="SELECT t.id as teamid,  t.t_name, ";
$requete_recup_vote_fairplay.="count((select es.sel_value from vlxhj_bl_extra_select as es, vlxhj_bl_extra_values as ev where f_id=10 and ev.uid=p.id and es.id=ev.fvalue and es.sel_value=0 )) as vote_contre,"; 
$requete_recup_vote_fairplay.=" (select count(id) from `vlxhj_bl_players`  where team_id=t.id ) as nbre_joueurs,"; 
$requete_recup_vote_fairplay.="(select m.id  from vlxhj_bl_match as m "; 
$requete_recup_vote_fairplay.=" where (m.team1_id=t.id or m.team2_id=t.id) and "; 
$requete_recup_vote_fairplay.=" TIMESTAMPDIFF(MINUTE,CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)),NOW())<=10080"; 
$requete_recup_vote_fairplay.=" and TIMESTAMPDIFF(MINUTE,CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)),NOW())>0 LIMIT 0 , 1 ) as Num_Match "; 
$requete_recup_vote_fairplay.=" FROM vlxhj_bl_players as p, vlxhj_bl_players_team as pt, vlxhj_bl_teams as t "; 
$requete_recup_vote_fairplay.="where p.id=pt.player_id and t.id=pt.team_id and pt.season_id in (97) and p.nick<>\"CSC\"  group by teamid order by pt.team_id "; 


if($result = $mysqli->query($requete_recup_vote_fairplay)) {

	$total_votes=0;
	$equipes_contre="Equipes n ayant pas attribu&eacute;es le point FairPlay :\n";
	
    while ($row = $result->fetch_row()) {
        //printf ("%s (%s)\n", $row[0], $row[1]);
	
	if ((2*$row[2])>$row[3]) {
		$resultat_vote=0; // il y a plus de vote contre que pour
		$equipes_contre.=$row[1]."\n";
	}
	else $resultat_vote=1;
	
	$total_votes+=$resultat_vote;
	
	$requete_maj_vote_fairplay1="UPDATE  vlxhj_bl_match SET bonus2=".$resultat_vote." WHERE id=".$row[4]." and team1_id=".$row[0].";";
	$mysqli->query($requete_maj_vote_fairplay1);
	
	$requete_maj_vote_fairplay2="UPDATE  vlxhj_bl_match SET bonus1=".$resultat_vote." WHERE id=".$row[4]." and team2_id=".$row[0].";";
	$mysqli->query($requete_maj_vote_fairplay2);
	
    }
}
   $tete = 'MIME-Version: 1.0' . "\n";
   $tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
   $tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
   $tete .= "Reply-To: contact@footinfive.com\n";
   $tete .= "Return-Path: contact@footinfive.com\n"; 
   // et zou... false si erreur d'&eacute;mission

//mail("ya2-95@hotmail.fr","Vote fairplay : $total_votes",$equipes_contre,$tete);
mail("lefloch.g@gmail.com","Vote fairplay : $total_votes","equipes contre : \n".$equipes_contre,$tete);

/* Fermeture de la connexion */
$mysqli->close();





?>