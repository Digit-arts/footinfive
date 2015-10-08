#!/usr/local/bin/php5

<?php

set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries');

function jours_en_fr(){

    return (array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"));

}

function mois_en_fr(){

    return (array("Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"));

}

function date_longue($date_mysql=""){

    if ($date_mysql=="") $date_mysql=date("Y-m-d");
    $jour_fr = jours_en_fr();
    $mois_fr = mois_en_fr();
    
    list($annee, $mois, $jour) = explode('-', $date_mysql);
    $date_longue = mktime (0, 0, 0, $mois, $jour, $annee);
                    
    return($jour_fr[date("w", $date_longue)]." ".$jour." ".$mois_fr[date("n", $date_longue)-1]." ".$annee);
}


$mysqli = new mysqli("localhost", "Cyclople", "MixMax123", "MySql_FIF");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

$requete_info_resa="Select c.prenom,c.nom, format(r.montant_total,2) as le_montant_total, r.date_debut_resa, r.heure_debut_resa,r.heure_fin_resa ";
$requete_info_resa.=" ,(select u.email from s857u_users as u where u.id=c.id_user) as email, r.id_resa FROM `Reservation` as r, Client as c ";
$requete_info_resa.="  where c.id_client=r.id_client ";
$requete_info_resa.=" and TIMESTAMPDIFF(MINUTE,CAST(concat(r.date_debut_resa,\" \",concat(r.heure_debut_resa,\":00\")) AS CHAR(22)),";
$requete_info_resa.="  CAST(concat(\"".date("Y-m-d")."\",\" \",\"".date("H:i").":00\") AS CHAR(22)))>-1440 ";
$requete_info_resa.=" and TIMESTAMPDIFF(MINUTE,CAST(concat(r.date_debut_resa,\" \",concat(r.heure_debut_resa,\":00\")) AS CHAR(22)),";
$requete_info_resa.="  CAST(concat(\"".date("Y-m-d")."\",\" \",\"".date("H:i").":00\") AS CHAR(22)))<=-1380 and indic_annul=0 ";

//echo "Req:<br>".$requete_info_resa;

   $tete = 'MIME-Version: 1.0' . "\n";
   $tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
   $tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
   $tete .= "Reply-To: contact@footinfive.com\n";
   $tete .= "Return-Path: contact@footinfive.com\n"; 
   // et zou... false si erreur d'&eacute;mission


if ($result = $mysqli->query($requete_info_resa)) {

    while ($row = $result->fetch_row()) {
        //printf ("%s (%s)\n", $row[0], $row[1]);
        
        $le_montant_total=str_replace(",","",number_format(str_replace(",","",$row[2]),2));
        $le_client=$row[0]." ".$row[1].",";
        $mail_client=$row[6];
        if ($mail_client<>"agent@footinfive.com"){
            $corps="Bonjour ".$le_client;
            $corps.="\n\nNous vous rappelons les informations de votre r&eacute;seravtion \nDate : ".date_longue($row[3])." \nCreneau : ".$row[4]."-".$row[5];
            
            $corps.="\n\n L'&eacute;quipe du Foot In Five vous remercie de votre confiance !\n A demain sur nos terrains...";
            $corps.="\n\nFOOT IN FIVE\n187 Route de Saint-Leu\n93800 Epinay-sur-Seine\n\nTel : 01 49 51 27 04";
            
            $objet="Rappel de votre reservation (Num : ".$row[7].")";
            $corps=str_replace("\n", "<br>", $corps);
            
            
            mail("<$mail_client>","$objet",$corps,$tete);
        }
    }

    $result->close();
}

/* Fermeture de la connexion */
$mysqli->close();





?>