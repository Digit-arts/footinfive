#!/usr/local/bin/php5

<?php

set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries');


$mysqli = new mysqli("localhost", "LEDG20132014TEST", "4lulu9", "LEDG-2013-2014");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

$requete_maj_fairplay=" delete from vlxhj_bl_extra_values where f_id=10;";

$mysqli->query($requete_maj_fairplay);

   $tete = 'MIME-Version: 1.0' . "\n";
   $tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
   $tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
   $tete .= "Reply-To: contact@footinfive.com\n";
   $tete .= "Return-Path: contact@footinfive.com\n"; 
   // et zou... false si erreur d'&eacute;mission

//mail("ya2-95@hotmail.fr","fairplay",$requete_maj_fairplay,$tete);

/* Fermeture de la connexion */
$mysqli->close();





?>