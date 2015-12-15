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


$mysqli = new mysqli("bj643040-001.privatesql.ha.ovh.net", "Marguerite", "Zlatan123", "MySql_EDF","35405");

/* V�rification de la connexion */
if (mysqli_connect_errno()) {
    printf("Echec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

$tete = 'MIME-Version: 1.0' . "\n";
$tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
$tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
$tete .= "Reply-To: contact@footinfive.com\n";
$tete .= "Return-Path: contact@footinfive.com\n";

$corps_du_bas="\n\n<img src=\"http://www.footinfive.com/FIF/images/image002.jpg\"/>\n\n"
		."L'&eacute;quipe du Foot In Five vous remercie de votre confiance !"
		."\n\nA bient&ocirc;t sur nos terrains..."
		."\n\nFOOT IN FIVE"
		."\nCentre de FOOT en salle 5vs5"
		."\n187 Route de Saint-Leu"
		."\n93800 Epinay-sur-Seine"
		."\nTel : 01 49 51 27 04"
		."\nMail : contact@footinfive.com";
		   
$requete_recup_publipostage="SELECT id_pub, objet, corps,id_saison FROM  `Publipostage` WHERE  `actif`=1 ";


if ($result1 = $mysqli->query($requete_recup_publipostage)) {
	    
	    while ($row1 = $result1->fetch_row()) {

		$requete_recup_dest_publipostage="SELECT `id_type_regroupement` FROM `Publipostage_type_destinataires` WHERE `id_pub`=".$row1[0];

		//echo "<br><br>Req recup_dest_publipostage :<br>".$requete_recup_dest_publipostage;
		if ($result2 = $mysqli->query($requete_recup_dest_publipostage)) {
			    while ($row2 = $result2->fetch_row())
				$liste_destinatires[]=$row2[0];
		}
		
		$requete_recup_id_Type_Regroupement="SELECT `id` FROM `Type_Regroupement` WHERE 1";


		if ($result3 = $mysqli->query($requete_recup_id_Type_Regroupement)) {
			    $liste_id_type_regroupement="(";
			    while ($row3 = $result3->fetch_row()){		
				if (in_array($row3[0],$liste_destinatires))
				    $liste_id_type_regroupement.=$row3[0].",";				
			    }
			    $liste_id_type_regroupement.="-1)";
			    if ($liste_id_type_regroupement<>"(-1)")
				    $compl_req=" and sr.id_type_regroupement in ".$liste_id_type_regroupement;
			    else $compl_req="";
		}
		
		$requete_recup_clients="select c.prenom,c.nom,u.email,c.id_client From Client as c LEFT JOIN s857u_users as u on (u.id=c.id_user)
					LEFT JOIN Saison_regroupement as sr on (sr.id_client=c.id_client)  LEFT OUTER JOIN `Publipostage_send` as p_s on (c.id_client=p_s.id_client and p_s.id_pub=".$row1[0]." )
					where p_s.id_client is null and u.email<>\"agent@footinfive.com\" and sr.id_saison=".$row1[3]." ".$compl_req." order by c.id_client LIMIT 0,500 ";
		
		//echo "<br><br>Req recup_clients :<br>".$requete_recup_clients;
		$compteur=0;
		if ($result = $mysqli->query($requete_recup_clients)) {
		
		    while ($row = $result->fetch_row()) {
			
			$mail_client=$row[2];
			
			if (substr($row1[2],0,2)=="<a")
			    $entete_message="";
			else $entete_message="Bonjour ".$row[0]." ".$row[1].",\n\n";
			
			$corps=$entete_message.$row1[2].$corps_du_bas;
			    
			$objet=utf8_encode($row1[1]);
			$corps=str_replace("\n", "<br>", utf8_encode ($corps));
			//printf ("%s \n %s \n\n\n\n", $objet, $corps);

			if (mail("<$mail_client>","$objet",$corps,$tete)){
				$requete_maj_Publipostage_send="INSERT INTO `Publipostage_send`(`id_pub`, `id_client`) VALUES (".$row1[0].",".$row[3].")";
				$mysqli->query($requete_maj_Publipostage_send);
			}
			$compteur++;
		    }
		
		    $result->close();
		}
		mail("<lyassine@ifbi.fr>","$objet",$compteur." mails partis !<br><br>".$corps,$tete);
		mail("<lefloch.g@gmail.com>","$objet",$compteur." mails partis !<br><br>".$corps,$tete);
		if ($compteur<500){
			$requete_maj_demarrer_publipostage="UPDATE `Publipostage` SET `actif`=2 WHERE `id_pub`=".$row1[0];
			//echo "<br>".$requete_maj_demarrer_publipostage;
			$mysqli->query($requete_maj_demarrer_publipostage);			
		}
	    }
	    $result1->close();
	    
}

/* Fermeture de la connexion */
$mysqli->close();





?>