#!/usr/local/bin/php5

<?php

$mysqli = new mysqli("localhost", "LEDG20132014TEST", "4lulu9", "LEDG-2013-2014");

/* V�rification de la connexion */
if (mysqli_connect_errno()) {
    printf("�chec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

function recup_nom_feuille_match($id_match){
	$chemin="/var/www/vhosts/footinfive.com/httpdocs/LEDG/Feuilles-de-matchs/";
	if (is_file($chemin.$id_match.'.pdf'))
		return($chemin.$id_match.'.pdf');
	
	if (is_file($chemin.$id_match.'.jpg'))
		return($chemin.$id_match.'.jpg');

	if (is_file($chemin.$id_match.'.png'))
		return($chemin.$id_match.'.png');
	
	if (is_file($chemin.$id_match.'.jpeg'))
		return($chemin.$id_match.'.jpeg');
	
	if (is_file($chemin.$id_match.'.PDF'))
		return($chemin.$id_match.'.PDF');
	
	if (is_file($chemin.$id_match.'.JPG'))
		return($chemin.$id_match.'.JPG');

	if (is_file($chemin.$id_match.'.PNG'))
		return($chemin.$id_match.'.PNG');
	
	if (is_file($chemin.$id_match.'.JPEG'))
		return($chemin.$id_match.'.JPEG');
return("");
}

$requete_equipes_sans_resultats="SELECT  p.first_name as Prenom, t.t_name as Nom_Equipe,  m.m_date as Date, m.id as match_id, u.email as mail_client ";
$requete_equipes_sans_resultats.="FROM vlxhj_users as u, `vlxhj_bl_match` as m, vlxhj_bl_teams as t, vlxhj_bl_matchday as md, vlxhj_bl_players_team as pt, vlxhj_bl_players as p"; 
$requete_equipes_sans_resultats.=" where (m.`team1_id`=t.id or m.`team2_id`=t.id) ";
$requete_equipes_sans_resultats.=" and m.m_played<>1 and md.id=m.m_id and pt.season_id=md.s_id and t.id=pt.team_id and pt.player_id=p.id and p.nick<>\"CSC\" ";
$requete_equipes_sans_resultats.=" and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))>-10080 ";
$requete_equipes_sans_resultats.=" and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))<0 ";
$requete_equipes_sans_resultats.=" and t.id not in (SELECT distinct(`t_id`) FROM `vlxhj_bl_match_events` WHERE `match_id`=m.id)  ";
$requete_equipes_sans_resultats.=" and u.id=p.usr_id order by m.id, t.t_name";

//echo $requete_equipes_sans_resultats;

   $tete = 'MIME-Version: 1.0' . "\n";
   $tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
   $tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
   $tete .= "Reply-To: contact@footinfive.com\n";
   $tete .= "Return-Path: contact@footinfive.com\n"; 
   // et zou... false si erreur d'&eacute;mission



if($result = $mysqli->query($requete_equipes_sans_resultats)) {
	$compteur=0;
	$nbre_boucles=0;
	$liste_contacts="";
    while ($row = $result->fetch_row()) {
        //printf ("%s (%s)\n", $row[0], $row[1]);

        $le_client=$row[0]." (".$row[1]."),";
        $mail_client=$row[4];
        
        $corps="\nSalut ".$le_client;

        $corps.="\n\nSi tu re&ccedil;ois ce mail automatique, c est qu aucun joueur de ton &eacute;quipe n a ins&eacute;r&eacute; les r&eacute;sultats de dimanche dernier sur le site web.";
            
        $corps.="\n\nLes feuilles de match sont scann&eacute;es et mises en ligne sur le site.";
	$corps.="\n\nMerci de te concerter avec ton &eacute;quipe pour qu il y ait au moins un joueur (pas forcement le capitaine) qui saisisse les r&eacute;sultats apr&egrave;s chaque journ&eacute;e de championnat.";
	$corps.="\n\nSi tu as le moindre probl&egrave;me pour saisir les r&eacute;sultats, merci de nous le signaler par email.";
	$corps.="\n\nSi tu souhaites ins&eacute;rer les r&eacute;sultats : <a href=\"http://www.footinfive.com/LEDG/index.php/ep/inserer-resultats\">clic-ici</a>";
        $corps.="\nSi tu as oubli&eacute; ton mot de passe : <a href=\"http://www.footinfive.com/LEDG/index.php/component/users/?view=reset\">clic-ici</a>";
	$corps.="\n\nSite : http://www.footinfive.com/LEDG/ (optimis&eacute; pour <a href=\"https://www.google.com/intl/fr/chrome/browser/?hl=fr\">Google Chrome</a>)"
		."\n\nFOOT IN FIVE"
		."\nCentre de FOOT en salle 5vs5"
		."\n187 Route de Saint-Leu"
		."\n93800 Epinay-sur-Seine"
		."\nTel : 01 49 51 27 04"
		."\nMail : contact@footinfive.com";
	
	
        $objet="Rappel dernier match";
        $corps=str_replace("\n", "<br>", $corps);
	$feuile_match=recup_nom_feuille_match($row[3]);
	if ($feuile_match<>""){
		if (mail($mail_client,$objet,$corps,$tete)){
			$compteur++;
			$liste_contacts.=$le_client."\n";
		}
		$nbre_boucles++;
	}
	

    }

    $result->close();
    if ($compteur==0)
	$corps_bly="Aucun mail envoy&eacute; : pas de feuilles de match en ligne ou tous les scores ont &eacute;t&eacute; inser&eacute;s.";
    else $corps_bly=$compteur." mails envoy&eacute;s.\n\n".$corps."\n\n".$liste_contacts;//"en .$nbre_boucles." boucles
    //mail("ya2-95@hotmail.fr",$objet,$corps_bly,$tete);
    mail("lefloch.g@gmail.com",$objet,$corps_bly,$tete);
}

/* Fermeture de la connexion */
$mysqli->close();





?>