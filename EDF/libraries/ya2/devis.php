<?php

set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries');
    require_once('../tcpdf/config/lang/fra.php');
    require_once('../tcpdf/tcpdf.php');
    require_once ('../ya2/fonctions_module_reservation.php');
    require_once ('../ya2/fonctions_gestion_user.php');
	
   
    
$mysqli = new mysqli("bj643040-001.privatesql.ha.ovh.net", "Cyclople", "MixMax123", "MySql_FIF","35405");

    
// Vérification de la connexion 
if (mysqli_connect_errno()) {
	printf("Échec de la connexion : %s\n", mysqli_connect_error());
	exit();
}

	
$id_client = $_GET["id_client"];

$le_montant_total=$_GET["montant_total"];
$montant_total_presta_20=$_GET["montant_total_presta_20"];
$montant_total_presta_10=$_GET["montant_total_presta_10"];
$montant_total_des_remises=$_GET["montant_total_des_remises"];


$bordure_tab=" style=\"border-collapse: collapse;\" ";
$bordure_td=" style=\"border: 1px solid black;\" ";
$bordure_th=$bordure_td." bgcolor=\"#CCCCCC\" ";

if ($_GET["devis_fact"]=="DEVIS")
	$num_devis=$_GET["id_client"].".".date("Ymd").'.'.date("Hi");
else {
	list($a,$m,$j)=explode("-",$_GET["date_debut_resa"]);
	$num_devis=$a.$m.$j."-".$_GET["id_resa"];
}

$lignes_detail_devis="<table  border=5 width=\"600\" height=\"800\">";
$lignes_detail_devis.="<tr><td height=\"50\" valign=\"top\"><b><font size=\"15\">".$_GET["devis_fact"]." N&deg; ".$num_devis."</font></b></td></tr><tr><td>";
$lignes_detail_devis.="<table ".$bordure_tab.">";
		$lignes_detail_devis.="<tr ><th ".$bordure_th."  width=\"150\">Date</th><th ".$bordure_th."  width=\"50\">Heure</th></tr>";
		$lignes_detail_devis.="<tr><td ".$bordure_td.">".date_longue(date("Y-m-d"))."</td><td ".$bordure_td.">".date("H:i")."</td>";
		$lignes_detail_devis.="</tr>";
$lignes_detail_devis.="</table>";
$lignes_detail_devis.="</td></tr>";

$lignes_detail_devis.="<tr><td align=\"right\" ><br><br><br>";
$lignes_detail_devis.="<table >";
  
$requete_recup_client="select c.prenom,c.nom, c.nom_entite, id_type_regroupement,tr.nom as type_reg, c.nom_service, c.Siret,   "
        ."  (select concat(v.code_postal,\" \",v.nom_maj_ville )  from Ville as  v where c.code_insee=v.code_insee  ) as ville_client, c.adresse,c.Adresse_facturation, "
        ." concat(Code_postal_facturation,\" \",Ville_facturation ) as ville_client_facturation "
	." from Client as c LEFT JOIN Type_Regroupement as tr on c.id_type_regroupement=tr.id  where id_client=".$id_client." LIMIT 0,1 ";
	$result = $mysqli->query($requete_recup_client);
	//echo $requete_recup_client;
	$recup_client=$result->fetch_row();
     
		$lignes_detail_devis.="<tr><td><b>".htmlentities($recup_client[4])."</b> ";
			
		if (test_non_vide($recup_client[2]))
			$lignes_detail_devis.=" ".htmlentities($recup_client[2]);
			
		if (test_non_vide($recup_client[5]))
			$lignes_detail_devis.="<br>".htmlentities($recup_client[5]);	
			
		$lignes_detail_devis.="<br>";

                if (test_non_vide($recup_client[6]))
                    $lignes_detail_devis.=" <b>Siret</b> ".$recup_client[6];
                
                $lignes_detail_devis.="</td></tr>";
			
		$lignes_detail_devis.="<tr><td>".htmlentities($recup_client[0])." ".htmlentities($recup_client[1])."</td></tr>";
		
		if (test_non_vide($recup_client[9]) or test_non_vide($recup_client[10]))
                    $l_adresse=htmlentities($recup_client[9])."<br>".htmlentities($recup_client[10]);
                else {
                        if (test_non_vide($recup_client[8]) or test_non_vide($recup_client[7]))
                            $l_adresse=htmlentities($recup_client[8])."<br>".htmlentities($recup_client[7]);
                }
                if (test_non_vide($l_adresse))
                        $lignes_detail_devis.="<tr><td>".$l_adresse."</td></tr>";

$lignes_detail_devis.="</table>";
$lignes_detail_devis.="</td></tr>";

$lignes_detail_devis.="<tr><td >";
$lignes_detail_devis.="<br><br><br><br><table ".$bordure_tab." width=\"100%\" cellpadding=\"2\" >";
		$total_montant_tva=0;
		$total_montant_ttc=0;
		$lignes_detail_devis.="<tr ><th  colspan=\"3\" align=\"center\">VOTRE RESERVATION</th></tr>";
		$lignes_detail_devis.="<tr ><th ".$bordure_th.">Date</th><th ".$bordure_th.">Heure</th><th ".$bordure_th."  align=\"right\">Montant TTC</th></tr>";
		$lignes_detail_devis.="<tr><td ".$bordure_td.">".date_longue($_GET["date_debut_resa"])."</td><td ".$bordure_td.">".$_GET["heure_debut_resa"]."-".$_GET["heure_fin_resa"]."</td>";

		$lignes_detail_devis.="<td ".$bordure_td." align=\"right\">".format_fr($le_montant_total)."&euro;</td>";
		$total_montant_ttc+=$le_montant_total;
		$lignes_detail_devis.="</tr>";
		
		if (test_non_vide($montant_total_presta_20) or test_non_vide($montant_total_presta_10)){
			$lignes_detail_devis.="<tr><td colspan=\"2\" align=\"right\" ".$bordure_td.">Prestations</td>";
			$lignes_detail_devis.="<td ".$bordure_td." align=\"right\">".format_fr($montant_total_presta_10 + $montant_total_presta_20)."&euro;</td>";
			$total_montant_ttc+=($montant_total_presta_10 + $montant_total_presta_20);
			$lignes_detail_devis.="</tr>";
		}
		
		$lignes_detail_devis.="<tr>";
		$lignes_detail_devis.="<td colspan=\"2\" align=\"right\" ".$bordure_td."><b>Montant TTC</b></td>";
		$lignes_detail_devis.="<td  align=\"right\" ".$bordure_td."><b>".format_fr($total_montant_ttc)."&euro;</b></td>";
		$lignes_detail_devis.="</tr>";
		
		$lignes_detail_devis.="<tr>";
		$lignes_detail_devis.="<td colspan=\"2\" align=\"right\" ".$bordure_td.">Montant HT</td>";
		$lignes_detail_devis.="<td  align=\"right\" ".$bordure_td.">".format_fr(($montant_total_presta_10/1.1)+($montant_total_presta_20/1.2)+($le_montant_total/$_GET["tva"]))."&euro;</td>";		
		$lignes_detail_devis.="</tr>";
		
			
		if (test_non_vide($montant_total_presta_10)){
			$lignes_detail_devis.="<tr>";
			$lignes_detail_devis.="<td colspan=\"2\" align=\"right\" ".$bordure_td.">Montant TVA 10%</td>";
			$lignes_detail_devis.="<td  align=\"right\" ".$bordure_td.">".format_fr($montant_total_presta_10-($montant_total_presta_10/1.1))."&euro;</td>";		
			$lignes_detail_devis.="</tr>";
		}
		
		$lignes_detail_devis.="<tr>";
		$lignes_detail_devis.="<td colspan=\"2\" align=\"right\" ".$bordure_td.">Montant TVA 20%</td>";
		$lignes_detail_devis.="<td  align=\"right\" ".$bordure_td.">".format_fr(($le_montant_total+$montant_total_presta_20)-(($le_montant_total+$montant_total_presta_20)/$_GET["tva"]))."&euro;</td>";		
		$lignes_detail_devis.="</tr>";
		
		if ($montant_total_des_remises>0){
			$lignes_detail_devis.="<tr>";
			$lignes_detail_devis.="<td colspan=\"2\" align=\"right\" ".$bordure_td.">Montant total des remises</td>";
			$lignes_detail_devis.="<td  align=\"right\" ".$bordure_td.">".format_fr($montant_total_des_remises)."&euro;</td>";
			$lignes_detail_devis.="</tr>";
			$lignes_detail_devis.="<tr>";
			$lignes_detail_devis.="<td colspan=\"2\" align=\"right\" ".$bordure_td."><b>Montant TTC remis&eacute;</b></td>";
			$lignes_detail_devis.="<td  align=\"right\" ".$bordure_td."><b>".format_fr($total_montant_ttc-$montant_total_des_remises)."&euro;</b></td>";
			$lignes_detail_devis.="</tr>";
		}
                
$lignes_detail_devis.="</table></td></tr>";
$lignes_detail_devis.="<tr ><td valign=\"bottom\" height=\"100%\" >".conditions_generales($_GET["devis_fact"])."</td></tr>";
$lignes_detail_devis.="</table>";


$partie_pdf=$lignes_detail_devis;

$nom_du_fichier_sans_extensions='FIF-'.$_GET["devis_fact"].'-'.$_GET["date_debut_resa"].'-'.$_GET["id_client"].'-'.$recup_client[0]." ".$recup_client[1];

//echo $partie_pdf;
generer_pdf($partie_pdf,$nom_du_fichier_sans_extensions,$_GET["devis_fact"].' FIF',$_GET["devis_fact"].' LOCATION',$_GET["devis_fact"].' location de terrains',$_GET["sortie"]);
			
		




?>