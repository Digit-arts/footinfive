#!/usr/local/bin/php5

<?php

set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries');

$mysqli = new mysqli("localhost", "LEDG20132014TEST", "4lulu9", "LEDG-2013-2014");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

$requete_liste_des_joueurs_avec_vote="SELECT p.first_name,t.t_name, u.email, p.id,  p.nick,t.id, "
				."(select es.id from vlxhj_bl_extra_select as es, vlxhj_bl_extra_values as ev where es.fid=13 and p.id=ev.uid and es.id=ev.fvalue) as vote"
				." FROM  vlxhj_bl_players as p LEFT OUTER JOIN vlxhj_users u ON p.usr_id = u.id, vlxhj_bl_players_team as pt, vlxhj_bl_teams as t, "
				." `vlxhj_bl_match` as m, vlxhj_bl_matchday as md where (m.`team1_id`=t.id or m.`team2_id`=t.id) and m.`published`=1 and m.`m_played`=0 "
				." and md.id=m.m_id and pt.season_id=md.s_id and p.id=pt.player_id  and t.id=pt.team_id and pt.season_id in (97) and p.nick<>\"CSC\" " //=6
				." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))<10080 "
				." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))>0  "
				." and u.id=p.usr_id order by vote asc,u.email desc ";



//echo $requete_liste_des_joueurs_avec_vote;


$tete = 'MIME-Version: 1.0' . "\n"
	."Content-type: text/html; charset=\"UTF-8\"\r\n"
	."From: FOOT IN FIVE <contact@footinfive.com>\n"
	."Reply-To: contact@footinfive.com\n"
	."Return-Path: contact@footinfive.com\n"; 
   // et zou... false si erreur d'&eacute;mission

$compteur=0;
$nbre_boucles=0;
$liste_contacts="";
$compteur_votants=0;

if($result = $mysqli->query($requete_liste_des_joueurs_avec_vote)) {

	while ($row = $result->fetch_row()) {
		//printf ("%s (%s)\n", $row[0], $row[1]);
	
		$le_client=$row[0]." (".$row[1]."),";
		$corps="Salut ".$le_client."\n\n";
		$mail_client=$row[2];
		
		switch ($row[6]){
											
			case '185':	$reponse="seras pr&eacute;sent";
					break;
			case '186':	$reponse="seras absent";
					break;
			case '187':	$reponse="es ind&eacute;cis";
					break;	
			case '188':	$reponse="pourrais d&eacute;panner";
					break;		
			case '189':	$reponse="es bless&eacute;";
					break;
			case '190':	$reponse="es malade";
					break;
			default:	$reponse="";break;
		}
			
		$corps.="Ceci est un mail auotmatique.";
		if ($reponse!="") {
			$compteur_votants++;
			$corps.="\n\nTu as indiqu&eacute; que tu ".$reponse." pour le prochain match.";
		}
		
		$corps.="\nCi-dessous voici les reponses de tes co-equipiers : \n";
		
		$requete_liste_des_joueurs_de_son_equipe_avec_vote="SELECT p.first_name,t.t_name, u.email, p.id,  p.nick, "
					."(select es.id from vlxhj_bl_extra_select as es, vlxhj_bl_extra_values as ev where es.fid=13 and p.id=ev.uid and es.id=ev.fvalue) as vote"
					." FROM  vlxhj_bl_players as p LEFT OUTER JOIN vlxhj_users u ON p.usr_id = u.id, vlxhj_bl_players_team as pt, vlxhj_bl_teams as t, "
					." `vlxhj_bl_match` as m, vlxhj_bl_matchday as md where (m.`team1_id`=t.id or m.`team2_id`=t.id)  "
					." and md.id=m.m_id and pt.season_id=md.s_id and p.id=pt.player_id  and t.id=pt.team_id and pt.season_id in (97) and p.nick<>\"CSC\" " //=6
					." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))<10080 "
					." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))>0  and m.`published`=1 and m.`m_played`=0 "
					." and t.id=".$row[5]." order by vote desc,u.email desc ";
	
	
	
		//echo $requete_liste_des_joueurs_de_son_equipe_avec_vote;
		if($result_co_equipiers = $mysqli->query($requete_liste_des_joueurs_de_son_equipe_avec_vote)) {
			$reponse_prec_co_equipers="";	
			while ($row_co_equipiers = $result_co_equipiers->fetch_row()) {
				//printf ("%s (%s)\n", $row[0], $row[1]);
				
				if ($reponse_prec_co_equipers=="" and is_null($row_co_equipiers[5]))
					break;
				$mail_co_equipiers=$row_co_equipiers[2];
				
				switch ($row_co_equipiers[5]){
											
					case '185':	$reponse_co_equipers="sera pr&eacute;sent";
							break;
					case '186':	$reponse_co_equipers="sera absent";
							break;
					case '187':	$reponse_co_equipers="est ind&eacute;cis";
							break;	
					case '188':	$reponse_co_equipers="pourrait d&eacute;panner";
							break;		
					case '189':	$reponse_co_equipers="est bless&eacute;";
							break;
					case '190':	$reponse_co_equipers="est malade";
							break;
					default:	if (is_null($mail_co_equipiers)) $reponse_co_equipers="n'a pas d email"; else $reponse_co_equipers="n a pas encore r&eacute;pondu";break;
				}
				
				if ($reponse_prec_co_equipers!=$reponse_co_equipers) {
					$reponse_prec_co_equipers=$reponse_co_equipers;
					$corps.="\n"; 
				}
				
				$corps.=" -".$row_co_equipiers[0]." ".$reponse_co_equipers."\n";
				
				$nbre_boucles++;
			}
			$corps.="\nSi tu souhaites renseigner ton info pr&eacute;sence : <a href=\"http://www.footinfive.com/LEDG/index.php/ep/info-presence\">clic-ici</a>";
			$corps.="\nSi tu as oubli&eacute; ton mot de passe : <a href=\"http://www.footinfive.com/LEDG/index.php/component/users/?view=reset\">clic-ici</a>";
			$corps.="\n\nSite : http://www.footinfive.com/LEDG/ (optimis&eacute; pour <a href=\"https://www.google.com/intl/fr/chrome/browser/?hl=fr\">Google Chrome</a>)\n\n.";
				
				
			$objet="Info presence : prochain match";
			$corps=str_replace("\n", "<br>", $corps);
				
			if ($reponse_prec_co_equipers!=""){
				//echo "<br><hr><br>".$corps;
				     
				if (mail($mail_client,$objet,$corps,$tete)){
					$compteur++;
					$liste_contacts.=$le_client."\n";
				}
			}
		}
		$result_co_equipiers->close();
	}
}
$result->close();

$corps_bly=$compteur." mails envoy&eacute;s en ".$nbre_boucles." boucles.\n\n".$corps."\n\n".$liste_contacts;
//mail("ya2-95@hotmail.fr","Info presence : $compteur_votants votants",$corps_bly,$tete);
    
/* Fermeture de la connexion */
$mysqli->close();





?>
