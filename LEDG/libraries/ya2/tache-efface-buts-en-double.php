#!/usr/local/bin/php5

<?php

set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries');


$mysqli = new mysqli("localhost", "LEDG20132014TEST", "4lulu9", "LEDG-2013-2014");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

$requete_select_doublons=" SELECT id FROM vlxhj_bl_match_events WHERE 1  group by player_id, match_id HAVING count(e_id) > 1 ";


$requete_suppr_doublons="";
if($result = $mysqli->query($requete_select_doublons)) {
	
	
	while ($row = $result->fetch_row()) {
		
		$requete_suppr_doublons=" DELETE FROM `vlxhj_bl_match_events` WHERE id=".$row[0]." ; ";
		$mysqli->query($requete_suppr_doublons);
		$requete_suppr_doublons_empil.=$requete_suppr_doublons."<br>";
	}
	



}

if ( $requete_suppr_doublons<>""){
   $tete = 'MIME-Version: 1.0' . "\n";
   $tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
   $tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
   $tete .= "Reply-To: contact@footinfive.com\n";
   $tete .= "Return-Path: contact@footinfive.com\n"; 
   // et zou... false si erreur d'&eacute;mission

	mail("ya2-95@hotmail.fr","Suppr_doublons",$requete_suppr_doublons_empil,$tete);
}
/* Fermeture de la connexion */
$mysqli->close();





?>