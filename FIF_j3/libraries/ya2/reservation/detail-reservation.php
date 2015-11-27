<script type="text/javascript">
	function valider() {
		if (document.suppr.Motif_annul_resa.value!='' && document.suppr.commentaire_annul_resa.value!=''){	
		
			if ( document.suppr.commentaire_annul_resa.value.length>10){	
		
				if (confirm('Voulez-vous vraiment supprimer cette resa ?')){
					document.suppr.submit();
				}
			}
			else {
				alert('Le commentaire doit contenir 10 caracteres au moins');
			}
		}
		else {
			alert('Le motif de suppression et le commentaire sont obligatoires !');
		}

	}
	
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
					else document.register.submit();
				}
			}
			else {
				if (lien!='') document.location.href=lien;
				else {
					document.register.Montant.value='';
					document.register.submit();
				}
			}
	}
	
	function maj_terrain() {
		if (confirm('Voulez-vous vraiment deplacer cette resa sur un autre terrain ?')){
			document.form_maj_terrain.submit();
		}
	}
	function enregistrer2() {
		document.register_modif_op.submit();
	}	
</script>

<?
require_once ('admin_base.php');

if (!test_non_vide($_GET["id_resa"])) echo "Num&eacute;ro de r&eacute;sa inexistant...";
else {
proprietaire_resa($_GET["id_resa"]);

$id_resa = $_GET["id_resa"];


if (est_min_agent($user))	
	$id_client = recup_client_de_resa($id_resa);
else $id_client=idclient_du_user();


if (est_min_manager($user) and test_non_vide($_GET["bloquer_optimisation"])) {
	if ($_GET["bloquer_optimisation"]==0)
		$maj_bloquer_optimisation=1;
	else $maj_bloquer_optimisation=0;
	
	$requete_maj_bloquer_optimisation="UPDATE Reservation SET bloquer_optimisation=".$maj_bloquer_optimisation." where id_resa=".$id_resa;
	//echo "<br>reqsuppr: ".$requete_maj_bloquer_optimisation;
	$db->setQuery($requete_maj_bloquer_optimisation);	
	$db->query();
}

if (test_non_vide($_POST["modif_op"]) and test_non_vide($_POST["Moyen_paiement"]) and $_POST["modif_op"]>0 and in_array($_POST["Moyen_paiement"],array(1,3,4,6))){
	$requete_maj_Moyen_paiement="UPDATE `Reglement` SET `id_moyen_paiement`=".$_POST["Moyen_paiement"]." WHERE `id_reglement`=".$_POST["modif_op"];
	//echo "<br>reqsuppr: ".$requete_maj_Moyen_paiement;
	$db->setQuery($requete_maj_Moyen_paiement);	
	$db->query();
}

if (est_min_agent($user) and test_non_vide($_GET["anniv"])) {
	$requete_maj_anniv="UPDATE Reservation SET anniv=".$_GET["anniv"]." where id_resa=".$id_resa;
	//echo "<br>reqsuppr: ".$requete_maj_anniv;
	$db->setQuery($requete_maj_anniv);	
	$db->query();
	if ($_GET["anniv"]==1)
		$anniv="ANNIV";
	else $anniv="NON-ANNIV";
	sendMail(267,"Resa ".$anniv." : ".$id_resa." ","<br>user_modif:".$user->id."<br><br>http://www.footinfive.com/FIF/index.php/component/content/article?id=61&premiere=1&id_resa=".$id_resa."");
}

if (est_min_agent($user) and test_non_vide($_GET["id_regl"])) {

	echo "<font color=red>";
	if (maj_validation_reglement(0,$_GET["id_regl"])) {
		echo "reglement supprim&eacute;<br>";
		
		$requete_recredit_si_besoin="Select montant_reglement FROM Reglement where id_moyen_paiement=2 and id_reglement=".$_GET["id_regl"];
		//echo "<br>reqsuppr: ".$requete_recredit_si_besoin;
		$db->setQuery($requete_recredit_si_besoin);	
		$db->query();
		$montant_a_recrediter_suite_annulation=$db->LoadResult();
		if ($montant_a_recrediter_suite_annulation<>0)	
			if (ajout_credit($id_client,$montant_a_recrediter_suite_annulation,1,"Suppr_regl_resa_".$id_resa,2)) 
				echo "Montant recr&eacute;dit&eacute; :".$montant_a_recrediter_suite_annulation."€<br>";
	}
	else echo "Erreur : reglement inexistant";
	echo "</font>";
	//maj_cautionnement_des_resas($id_client,1);
	maj_resa_a_supprimer($id_resa);
}

if (est_min_agent($user) and test_non_vide($_GET["id_presta"])) {

	echo "<font color=red>";
	if (maj_validation_prestation(0,$_GET["id_presta"])) {
		echo "prestation supprim&eacute;e<br>";
		/*
		 *corriger ci-dessous le code)
		 *
		 *
		$requete_recredit_si_besoin="Select montant_reglement FROM Reglement where id_moyen_paiement=2 and id_presation=".$_GET["id_presta"]." and id_reservation=".$id_resa;
		//echo "<br>reqsuppr: ".$requete_recredit_si_besoin;
		$db->setQuery($requete_recredit_si_besoin);	
		$db->query();
		$montant_a_recrediter_suite_annulation=$db->LoadResult();
		if ($montant_a_recrediter_suite_annulation<>0)	
			if (ajout_credit($id_client,$montant_a_recrediter_suite_annulation,1,"Suppr_regl_resa_".$id_resa,2)) 
				echo "Montant recr&eacute;dit&eacute; :".$montant_a_recrediter_suite_annulation."€<br>";*/
	}
	else echo "Erreur : prestation inexistante";
	echo "</font>";
}


if (est_register($user)) $complement_req1=" and r.`id_client`=".$id_client;

$requete_info_resa="Select format(r.montant_total,2) as le_montant_total, r.*,mr.mode_name,c.*, t.nom as le_terrain,t.id_type as type_terrain,t.id_cal_google,";
$requete_info_resa.=" (SELECT `nom` FROM `Motif_annul_resa` WHERE id=id_Motif_annul_resa) as Motif_annul, id_Motif_annul_resa,";
$requete_info_resa.=" (select FORMAT(sum(reg1.montant_reglement),2) from Reglement as reg1 ";
$requete_info_resa.=" where reg1.id_reservation=r.id_resa and reg1.validation_reglement=1) as total_versement,";
$requete_info_resa.=" (select FORMAT(sum(reg2.montant_reglement),2) from Reglement as reg2 where reg2.validation_reglement=1 and ";
$requete_info_resa.="  reg2.id_reservation=r.id_resa and (reg2.id_remise is NULL or reg2.id_remise=0)) as total_versement_hors_remises,";
$requete_info_resa.="  tr.nom as type_reg ,(select u.email from #__users as u where u.id=c.id_user) as email ";
$requete_info_resa.=" ,(select u.name from #__users as u where u.id=r.id_user) as nom_user ";
$requete_info_resa.=" ,(select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=r.id_user) as gid_user FROM `Reservation` as r, `Mode_Reservation` as mr, Terrain as t , "
			." Client as c LEFT JOIN Type_Regroupement as tr on c.id_type_regroupement=tr.id ";
$requete_info_resa.="  where c.id_client=r.id_client and r.id_terrain=t.id and r.id_mode_reservation=mr.mode_id";
$requete_info_resa.=" and r.id_resa=".$id_resa." ".$complement_req1;

//echo $requete_info_resa;

$db->setQuery($requete_info_resa);	
$info_resa= $db->loadObject();

if (!$info_resa) echo "Num&eacute;ro de r&eacute;sa inexistant...";
else {
	
if (est_min_agent($user) and test_non_vide($_POST["terrains_dispo"])) {
	$id_resa_new=maj_resa($info_resa->id_resa,$info_resa->date_debut_resa,$info_resa->date_fin_resa, $info_resa->heure_debut_resa,
		      $info_resa->heure_fin_resa,$info_resa->id_client,$_POST["terrains_dispo"],$info_resa->montant_total,
		      $info_resa->duree_resa,$info_resa->cautionnable,$info_resa->id_mode_reservation,$info_resa->montant_sans_remise);
									
	//$rdv=ajout_event_cal_google ($id_resa_new);
	//suppr_event_cal_google($info_resa->id_resa);
										
	///////////////////
	// on met à jour la resa avec l'adresse du rdv
	/////////////////////
	maj_cal_dans_resa($id_resa_new,"");//$rdv->getEditLink()->href);
	$db->setQuery($requete_info_resa);	
	$info_resa= $db->loadObject();
}

$total_versement_hors_remises=str_replace(",","",number_format(str_replace(",","",$info_resa->total_versement_hors_remises),2));
$le_montant_total=str_replace(",","",number_format(str_replace(",","",$info_resa->le_montant_total),2));
$total_versement=str_replace(",","",number_format(str_replace(",","",$info_resa->total_versement),2));

$les_erreurs="";

if (test_non_vide($_POST["ajout_presta"]))
	$ajout_presta=$_POST["ajout_presta"];
else $ajout_presta=$_GET["ajout_presta"];

if (test_non_vide($_GET["ok"]) and $_GET["ok"]==1) 
	echo "<font color=red>La transaction est valid&eacute;e.<br><br></font>";
else {
	if (test_non_vide($_GET["ok"]) and $_GET["ok"]==0) 
		echo "<font color=red>La transaction a echou&eacute;e.<br><br></font>";
}
if (!test_non_vide($_GET["annul"])) {
	if ( !isset($_GET["premiere"]) and (!(test_non_vide($_POST["Montant"])) or str_replace(",", ".",$_POST["Montant"])==0) )
		$les_erreurs.="Le montant est obligatoire.<br>";
	else {
		if (test_non_vide($_POST["Montant"]) and !(VerifierMonetaire($_POST["Montant"]))){
			
			$clef_existe=clef_existe($_POST["Montant"]);
			$clef_deja_utilisee=clef_deja_utilisee($_POST["Montant"]);
			
			if (($clef_existe==1) and !test_non_vide($clef_deja_utilisee)  and ($_POST["Moyen_paiement"]==10)
				and ($total_versement_hors_remises==0) and (substr($_POST["Montant"],0,1)==recup_id_tarif_d_une_resa($id_resa))
				and diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa,$info_resa->date_fin_resa,$info_resa->heure_fin_resa)==60)
				ajout_reglement($id_resa,$id_client,$info_resa->montant_total,$_POST["Moyen_paiement"],$_POST["info"],$_POST["Remise"],1,$_POST["Montant"]);
			else {
				if ($_POST["Moyen_paiement"]<>10)
				    $les_erreurs.="Le Montant saisie est incorrect.<br>";
				else{
					if ($clef_existe==0)
						$les_erreurs.="Cette contremarque n'existe pas.<br>";
					else {
						if ($clef_existe>1)
							$les_erreurs.="Erreur (5147) contremarque : contactez le webmaster au 0646427587<br>";
						else {
							if (test_non_vide($clef_deja_utilisee))
								$les_erreurs.="Cette contremarque est d&eacute;j&egrave;a utilis&eacute;e par la resa : "
									."<a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$clef_deja_utilisee."\" target=\"_blank\" />".$clef_deja_utilisee."</a>.<br>";
							else {
								if (substr($_POST["Montant"],0,1)<>recup_id_tarif_d_une_resa($id_resa))
									$les_erreurs.="Cette contremarque n'est pas utilisable sur cet horaire<br>";
								else {
									if ($total_versement_hors_remises<>0)
										$les_erreurs.="Cette contremarque n'est pas cumulable avec d'autres remises <br>";
									else {
										if (diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa,$info_resa->date_fin_resa,$info_resa->heure_fin_resa)<>60)
											$les_erreurs.="Cette contremarque n'est utilisable que sur une resa d'une heure<br>";
										else $les_erreurs.="Erreur (5684) contremarque : contactez le webmaster au 0646427587<br>";
									}
								}
							}
						}
					} 
				}
				
				
				
				
			}
		}
		else {
			////dans le cas où le client veut une deduction d'acompte plus importante que ce qu'il a versé
			if ($_POST["type"]==3 or $_POST["type"]==4){
				if ($total_versement_hors_remises<str_replace(",", ".",$_POST["Montant"]))
						$les_erreurs.="Le montant que vous souhaitez rembourser est superieur aux paiements de cette r&eacute;sa.<br>";
				else $Montant_a_recrediter=$_POST["Montant"];
			}
			
			if ( ($_POST["type"]==1) and ($_POST["Moyen_paiement"]==2) ){
					$reste_credit=(str_replace(",","",$_POST["credit_en_base"])-str_replace(",",".", $_POST["Montant"]));
					if ($reste_credit<0)
						$les_erreurs.="Le montant saisie est superieur &agrave; votre cr&eacute;dit.<br>";
			}
			if (($_POST["type"]<>4) and ($_POST["type"]<>3) and test_non_vide($_POST["Reste_a_payer"]) and ($_POST["Reste_a_payer"]<str_replace(",", ".",$_POST["Montant"])))
				$les_erreurs.="Le montant saisie est superieur au reste &agrave; payer.<br>";
			if ((!(test_non_vide($_POST["Remise"]))) and ($_POST["type"]<>3) and test_non_vide($_POST["Montant_Min"]) and ($_POST["Montant_Min"]>str_replace(",", ".",$_POST["Montant"])))
				$les_erreurs.="Le Montant saisie est inferieur &agrave; l'acompte.<br>";
			if (!test_non_vide($ajout_presta) and $_POST["type"]!=4 and !isset($_GET["premiere"]) and (!test_non_vide($_POST["Remise"]) and !(test_non_vide($_POST["Moyen_paiement"]))))
				$les_erreurs.="Le moyen de paiement est obligatoire.<br>";
			if ($_POST["type"]==4 and !test_non_vide($_POST["Type_credit"]))
				$les_erreurs.="La destination du transfert est obligatoire.<br>";
			
			$horaire_ouverture=horaire_ouverture();
			$horaire_fermeture=horaire_fermeture();
			$recup_fin_heure_remise=recup_fin_heure_remise();
	
			if ($_POST["type"]==2 and test_non_vide($_POST["Remise"]) and test_non_vide($_POST["Montant"]) and (est_agent($user) or est_register($user))
				and diff_dates_en_minutes("",$recup_fin_heure_remise,"",$info_resa->heure_fin_resa)<=0
				and diff_dates_en_minutes("",$horaire_ouverture,"",$info_resa->heure_debut_resa)>=0
			    and (($total_versement-$total_versement_hors_remises+str_replace(",",".", $_POST["Montant"]))>floor((50*$le_montant_total)/100)))
				$les_erreurs.="En heure creuse les remises ne peuvent pas depasser 50% du montant total.<br>";
			/*else echo "(".$_POST["type"]." and ".$_POST["Remise"]." and ".$_POST["Montant"]." and (".est_agent($user)." or ".est_register($user).")<br>"
				." and ".diff_dates_en_minutes("",$recup_fin_heure_remise,"",$info_resa->heure_fin_resa)."<0 <br>"
				." and ". diff_dates_en_minutes("",$horaire_ouverture,"",$info_resa->heure_debut_resa).">0 <br>"
				." and ((".$total_versement."-".$total_versement_hors_remises."-".str_replace(",",".", $_POST["Montant"]).")>".floor((50*$le_montant_total)/100)."))";
			*/	
			if ($_POST["type"]==2 and test_non_vide($_POST["Remise"]) and test_non_vide($_POST["Montant"]) and (est_agent($user) or est_register($user))
				and diff_dates_en_minutes("",$recup_fin_heure_remise,"",$info_resa->heure_debut_resa)>=0
			    and (($total_versement-$total_versement_hors_remises+str_replace(",",".", $_POST["Montant"]))>floor((34*$le_montant_total)/100)))
				$les_erreurs.="En heure pleine les remises ne peuvent pas depasser 34% du montant total.<br>";
			
				
			if (test_non_vide($ajout_presta) and !test_non_vide($_POST["Type_presta"]) and !isset($_GET["premiere"]))
				$les_erreurs.="Le type de prestation est obligatoire.<br>";
		}
	}


	if (!test_non_vide($les_erreurs) and $_POST["Moyen_paiement"]<>10){
		if (test_non_vide($ajout_presta)){
			ajout_presta($id_resa,$_POST["Type_presta"],$_POST["Montant"],$_POST["TVA"]);
		}
		else {
			if ($_POST["Moyen_paiement"]==8){
				$id_regl=ajout_reglement($id_resa,$id_client,$_POST["Montant"],$_POST["Moyen_paiement"],$_POST["info"],$_POST["Remise"],0);
				
				$requete_marq_client="UPDATE Client set page_retour=\"".$user->id."#\" where id_client=".$id_client;
				$db->setQuery($requete_marq_client);	
				$db->query();
				
				header("Location: libraries/ya2/CMCIC_Paiement_3_0i/Phase1Aller.php?Montant=".str_replace(",",".",$_POST["Montant"])."&ref=R".$id_regl);
			}
			else {
			
				if (str_replace(",", ".",$_POST["Montant"])>0){
					if ($_POST["type"]==3 or $_POST["type"]==4 ) $maj_montant=(-1*str_replace(",", ".",$_POST["Montant"]));
					else $maj_montant=$_POST["Montant"];
					
					if ($_POST["type"]==4) $moyen=7;
					else $moyen=$_POST["Moyen_paiement"];
				
					ajout_reglement($id_resa,$id_client,$maj_montant,$moyen,$_POST["info"],$_POST["Remise"]);
					
					if ($_POST["type"]==4 )
						ajout_credit($id_client,$_POST["Montant"],$_POST["Type_credit"],"Transfert_Resa_".$info_resa->id_resa,7);
					
					
				}		
				
				if ((isset($reste_credit) and $reste_credit>=0) or (test_non_vide($Montant_a_recrediter) and ($_POST["Moyen_paiement"]==2))){
					
					/*if (isset($reste_credit) and $reste_credit>=0){
						//sauvegarder les lignes credit avant suppr
						save_credit_client($id_client,1);
						suppr_credit_client($id_client,1);
						
						//la ligne contenant le montant à deduire
						$requete_credit_neg="INSERT INTO `Hist_Credit_client`(`id_user_suppr`, `date_suppr`, `heure_suppr`, ";
						$requete_credit_neg.=" `id_credit_client`, `id_client`, `id_user_creation`, `type_credit`, `id_moyen_paiement`, ";
						$requete_credit_neg.=" `credit`, `unite_credit`, `date_credit`, `heure_credit`, `origine_credit`, `validation_credit`)";
						$requete_credit_neg.=" VALUES (".$user->id.",\"".date("Y")."-".date("m")."-".date("d")."\",\"".date("H").":".date("i")."\",";
						$requete_credit_neg.=" \"\",".$id_client.",\"\",\"\",\"\",\"".(-1*str_replace(",", ".", $_POST["Montant"]))."\",";
						$requete_credit_neg.=" \"\",\"\",\"\",\"\",\"\")";
						// echo "<br>reqsave: ".$requete_credit_neg;
						$db->setQuery($requete_credit_neg);	
						$resultat_credit_neg = $db->query();	
					}
					echo "<br>Reste credit : ".$reste_credit."<br>";
					echo "<br>Montant : ".$_POST["Montant"]."<br>";
					echo "<br>Montant_a_recrediter : ".$Montant_a_recrediter."<br>";
					*/
					
					if (isset($reste_credit) and $reste_credit>=0)
						$new_credit=(-1*str_replace(",",".", $_POST["Montant"]));
					else $new_credit=$Montant_a_recrediter;
					
					ajout_credit($id_client,$new_credit,1,"Avoir_Resa_".$info_resa->id_resa,2);
				
				}
			}
			
		}
	}
}
$db->setQuery($requete_info_resa);	
$info_resa= $db->loadObject();


$db->setQuery(requete_resas_a_supprimer($id_resa));
$db->query();
$nbre_resultats=$db->getNumRows();

$les_versements=versements_sans_remise_et_avec_validation($id_resa);

$montant_total_presta=str_replace(",","",number_format(str_replace(",","",montant_total_presta($info_resa->id_resa)),2));
$le_montant_total=str_replace(",","",number_format(str_replace(",","",$info_resa->le_montant_total),2));
$credit_client=str_replace(",","",number_format(str_replace(",","",recup_credit_total_client($info_resa->id_client)),2));
$total_versement=str_replace(",","",number_format(str_replace(",","",$info_resa->total_versement),2));
$total_versement_hors_remises=str_replace(",","",number_format(str_replace(",","",$info_resa->total_versement_hors_remises),2));

if (!($nbre_resultats>0) and (($info_resa->notification<>1) or test_non_vide($_GET["renvoyer"])) and ($les_versements>0 or ($info_resa->cautionnable==1 or recup_accompte_necessaire($info_resa->id_client)==1))){
	if ((test_non_vide($_GET["ok"]) and $_GET["ok"]==1) or (!test_non_vide($_GET["ok"]))){
		$corps=texte_resa($info_resa->prenom." ".$info_resa->nom,$info_resa->date_debut_resa,$info_resa->heure_debut_resa,$info_resa->heure_fin_resa,$le_montant_total+$montant_total_presta,"",1);
		$objet="Confirmation de votre reservation (Num : ".$id_resa.")";
		if (($info_resa->date_debut_resa<date("Y-m-d")) or sendMail($info_resa->id_client,$objet,$corps)){
			maj_resa_notification($id_resa,1);
			header("Location: article?id=61&id_resa=".$info_resa->id_resa."");	
		}
		else echo "\n<p><center>D&eacute;sol&eacute;, nous avons eu un probl&egrave;me lors de l'&eacute;mission du mail</center></p>\n";
	}
}

menu_acces_rapide($info_resa->id_client,"D&eacute;tail r&eacute;servation");

echo "<table class=\"zebra\" >";
		echo "<tr><th>";
		if ($info_resa->id_type_regroupement>0)
			echo "Type";
		else echo "Equipe";
		echo "</th><th> </th><th>Nom</th><th>Prenom</th><th>Mobile1</th><th>Tel</th><th>Email</th><th>Avoir dispo</th><th>Caution dispo</th></tr>";
		echo "<tr><td>";
		if ($info_resa->id_type_regroupement>0)
			echo $info_resa->type_reg;
		else echo $info_resa->equipe;
		
		echo "</td><td>";
		if (recup_accompte_necessaire($id_client)==0) 
			echo "<img src=\"images/VIP-Empty-icon.png\" title=\"Client non-autoris&eacute; &agrave; faire des r&eacute;sa sans acompte plus de 2 fois par semaine\">";
		else echo "<img src=\"images/VIP-icon.png\" title=\"Client autoris&eacute; &agrave; faire des r&eacute;sa sans acompte\">";
			
		echo "</td><td><a href=\"index.php/component/content/article?id=60&id_client=".$info_resa->id_client."\"/>".$info_resa->nom."</a> ";
		$ligne_commentaire_client=recup_derniere_commentaire("id_client",$info_resa->id_client);
		if ($ligne_commentaire_client->Commentaire<>"" and est_min_agent($user))
			echo "<img src=\"images/Comment-icon.png\" title=\"".$ligne_commentaire_client->Commentaire."\">";
		echo "</td>";
		echo "<td>".$info_resa->prenom."</td>";
		echo "<td>".$info_resa->mobile1."</td>";
		echo "<td>".$info_resa->fixe."</td>";
		echo "<td>".$info_resa->email."</td>";
		echo "<td>".$credit_client."</td>";
		echo "<td><font color=red><b>".str_replace(",","",recup_caution_total_client($info_resa->id_client))."</b></font></td>";
		echo "</tr>";
echo "</table><hr><br>";

		echo "<table class=\"zebra\" >";
		echo "<tr><th>Num r&eacute;sa</th><th>infos</th><th>Date r&eacute;sa</th><th>Heure r&eacute;sa</th><th>Terrain</th>";
		echo "<th>Montant<br>R&eacute;sa</th><th>Mode de <br>r&eacute;sa</th><th>Montant<br>Presta</th><th>";
		if (($info_resa->cautionnable==0) and ($total_versement=="" or $total_versement==0) and $info_resa->accompte_necessaire==0 and $info_resa->id_mode_reservation!=3){
			echo "Acompte";
			if  ($info_resa->indic_annul<>1 and $info_resa->accompte_necessaire<>1) 
				$info_acompte="Attention sans acompte cette resa risque d'&ecirc;tre supprim&eacute;e 5 mins apr&egrave;s l'heure de validation (".$info_resa->heure_valid.").<hr>";
		}
		else {
			echo "Reste<br>&agrave; payer";
			if  ($info_resa->indic_annul<>1 )
				$info_acompte="<font color=\"green\"><b>R&eacute;sa valid&eacute;e</b></font>";
		
		}
		if ($info_resa->cautionnable==1 and  $info_resa->indic_annul<>1) echo " <font color=red>(C)</font>";
		
		echo "</th></tr><tr>";
		echo "<td>".$info_resa->id_resa;
		if (!($nbre_resultats>0) and ($info_resa->email<>"agent@footinfive.com") and ($les_versements>0 or ($info_resa->cautionnable==1 or recup_accompte_necessaire($info_resa->id_client)==1))){
			echo " <a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$info_resa->id_resa."&renvoyer=1\" />";
			echo "<img src=\"images/renvoyer-notification-email-icon.png\" title=\"Renvoyer la confirmation par email au client\"></a>";
		}
		echo "</td><td nowrap>";
		if (est_min_agent($user) and ($info_resa->indic_annul==1)){
			echo "<a href=\"index.php/component/content/article?id=61&reactiver=1&id_resa=".$info_resa->id_resa."\" />reactiver</a>";
			if ($_GET["reactiver"]==1){
				$les_terrains_dispo=trouve_dispo($info_resa->type_terrain,$info_resa->date_debut_resa,$info_resa->heure_debut_resa, $info_resa->heure_fin_resa,$info_resa->id_resa);
				foreach ($les_terrains_dispo as $le_terrain) $liste_id_terrains[]=$le_terrain[3];
				if (is_array($les_terrains_dispo) and in_array($info_resa->id_terrain, $liste_id_terrains)){
										
					//$rdv=ajout_event_cal_google ($info_resa->id_resa);
					
					///////////////////
					// on met à jour la resa avec l'adresse du rdv	
					maj_cal_dans_resa($info_resa->id_resa,"");//$rdv->getEditLink()->href);
					
					maj_cautionnement_des_resas($info_resa->id_client,1,$info_resa->id_resa,$info_resa->date_debut_resa);
					$credit_negatif=(-1*versements_sans_remise_et_avec_validation($info_resa->id_resa));
					if ($credit_negatif<0 and avoir_deja_attribuer($info_resa->id_resa) and diff_dates_en_minutes($info_resa->date_debut_resa, $info_resa->heure_debut_resa,$info_resa->date_valid,$info_resa->heure_valid)<-2880)
						ajout_credit($info_resa->id_client,$credit_negatif,1,"Reactiv-".$info_resa->id_resa,2); 
					header("Location: article?id=61&id_resa=".$info_resa->id_resa."");
				}
				else echo "<br>Terrain indispo !";
				 
			}
		}
		if  ($info_resa->indic_annul<>1){
			if ($info_resa->id_client<>3586 and diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa)<0 and (($info_resa->indic_venue==1) or ($info_resa->indic_venue==4))){
				echo " <a href=\"index.php/component/content/article?id=61&annul=1&id_resa=".$info_resa->id_resa."\" />";
				echo "<img src=\"images/annuler-resa.png\" title=\"annuler cette réservation\"></a> ";
			}
			if (diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa)<-2880 or (est_min_agent($user) and diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa)<120) or (est_min_manager($user)) ) {// plus de modif avant 48h
				echo " <a href=\"index.php/component/content/article?id=62";
				echo "&id_client=".$info_resa->id_client;
				echo "&modif=1&num_resa=".$info_resa->id_resa."&type_terrain=".$info_resa->type_terrain."&date_debut_resa=".$info_resa->date_debut_resa."&mode_resa=".$info_resa->id_mode_reservation."&heure_fin_resa=".$info_resa->heure_fin_resa."&heure_debut_resa=".$info_resa->heure_debut_resa."\" />";
				echo "<img src=\"images/modifier-resa.png\" title=\"modifier cette réservation\"></a> ";
				
			}
		}
		else echo "<img src=\"images/Cancel-resa.png\" title=\"Réservation annulée\">";
		
		if (est_min_agent($user)){
			if (test_non_vide($_GET["hist_com"]))
				$hist_com="";
			else $hist_com="&hist_com=1";
			
			$ligne_commentaire=recup_derniere_commentaire("id_resa",$info_resa->id_resa);
			if ($ligne_commentaire->Commentaire<>"" )
				echo " <a href=\"index.php/component/content/article?id=61".$hist_com."&id_resa=".$info_resa->id_resa."\">"
					."<img src=\"images/Comment-icon.png\" title=\"".$ligne_commentaire->Commentaire."\">";
			else echo " <a href=\"index.php/component/content/article?id=75&art=61&id_resa=".$info_resa->id_resa."\">"
					."<img src=\"images/Comment-add-icon.png\" title=\"Ajouter un commentaire\">"; 
			echo "</a>";

			if ($info_resa->anniv==0) {
				echo " <a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$info_resa->id_resa."&anniv=1\">";
				echo "<img src=\"images/no-anniv-icon.png\" title=\"Cliquez ici pour indiquer que cette resa est un anniversaire\"></a>";
			}
			else {
				echo " <a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$info_resa->id_resa."&anniv=0\">";
				echo "<img src=\"images/anniv-icon.png\" title=\"Cliquez ici pour indiquer que cette resa n'est pas un anniversaire\"></a>";
			}
		}
		$bloquer_optimisation=recup_1_element("bloquer_optimisation","Reservation","id_resa",$id_resa);
		if (est_min_manager($user))
			echo " <a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$info_resa->id_resa."&bloquer_optimisation=".$bloquer_optimisation."\">";
			
		if ($bloquer_optimisation==0)
			echo "<img src=\"images/bloquer_optimisation_0.png\" title=\"Resa déplaçable\" id='img-optim'>";
		else echo "<img src=\"images/bloquer_optimisation_1.png\" title=\"Resa non-déplaçable\" id='img-optim'>";
		
		echo "</a></td>";
		echo "<td>".date_longue($info_resa->date_debut_resa)."</td><td>".$info_resa->heure_debut_resa."-".$info_resa->heure_fin_resa."</td><td>";
		
		if (est_min_manager($user))
			$rubiksCube=true;
		else $rubiksCube=false;
			
		if (($info_resa->indic_annul<>1) and (est_min_agent($user) and $bloquer_optimisation==0) or est_min_manager($user)){
			$infos_terrain=trouve_dispo($info_resa->type_terrain,$info_resa->date_debut_resa,$info_resa->heure_debut_resa,$info_resa->heure_fin_resa,$info_resa->id_resa,$rubiksCube);
			if (is_array($infos_terrain) and sizeof($infos_terrain)>1){
				echo "<FORM name=\"form_maj_terrain\" class=\"submission box\" method=\"post\" action=\"".JRoute::_('article?id=61&id_resa='.$info_resa->id_resa.'')."\" >";
				echo "<select name=\"terrains_dispo\" onChange=\"maj_terrain()\">";
				foreach ($infos_terrain as $terrain) {
					if ($info_resa->le_terrain==$terrain[2])
						$select_terrain=" selected ";
					else $select_terrain="";	
					echo "<option value=\"".$terrain[3]."\" ".$select_terrain.">".$terrain[2]."</option>";
				}
				echo "</select></form>";
			}
			else echo $info_resa->le_terrain;
		}
		else echo $info_resa->le_terrain;
		
		echo "</td><td><b>".$le_montant_total."€</b></td>";
		echo "<td>".$info_resa->mode_name."</td>";		
		echo "<td>".$montant_total_presta."</td><td><font color=red><b>";
		if ($info_resa->cautionnable==1 or  $info_resa->accompte_necessaire==1 or $info_resa->id_mode_reservation==3)
			echo ($le_montant_total+$montant_total_presta-$total_versement);
		else {
			if ($total_versement=="" or $total_versement==0) {
				$Montant_Min=calcul_acompte($info_resa->date_debut_resa,$info_resa->heure_debut_resa,$le_montant_total);
				echo $Montant_Min;
			}
			else echo format_fr($le_montant_total+$montant_total_presta-$total_versement);
		}
		
		echo "€</b></font></td></tr>";
		$requete_recup_hist_commentaires="select * from Commentaires as c, #__users as u where c.id_user=u.id and id_resa=".$info_resa->id_resa
			." order by date desc, heure desc ";
		//echo $requete_recup_hist_commentaires;
		$db->setQuery($requete_recup_hist_commentaires);
		$resultat_recup_hist_commentaires = $db->LoadObjectList();
		
		if (test_non_vide($_GET["hist_com"])){
			?>
			<tr>
				<td colspan="10" align="center" >
			<br>
				<table class="zebra" border="0"  >
				<tr>
					<th>Effectu&eacute; par</th><th>date</th><th>heure</th><th>Commentaire <?
						echo " <a href=\"index.php/component/content/article?id=75&art=61&id_resa=".$info_resa->id_resa."\">";
						echo "<img src=\"images/Comment-add-icon.png\" title=\"Ajouter un commentaire\"></a>";
					?></th>
				</tr>
				<?
				foreach($resultat_recup_hist_commentaires as $les_resultats){
				?><tr>
					<td><?php echo $les_resultats->name;?></td>
					<td><?php echo date_longue($les_resultats->date);?></td>
					<td><?php echo $les_resultats->heure;?></td>
					<td><?php echo $les_resultats->Commentaire;?></td>
				</tr>
				<?
				}
				?>
			</table>
				<br>
				</td></tr>
		<?
		}
		if (test_non_vide($_GET["annul"])) {?>
			<tr>
				<td colspan="10" align="center" >
					<?
					$form="<FORM id=\"formulaire\" name=\"suppr\" class=\"submission box\" ";
					$form.="action=\"article?id=59&ttes=1&id_client=".$info_resa->id_client."&id_resa=".$info_resa->id_resa;
					$form.="&suppr=".$info_resa->id_resa;
					
					if (diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa)>-2880 and ((recup_caution_total_client($info_resa->id_client)<>"" and recup_caution_total_client($info_resa->id_client)>0 and $les_versements<=0) or est_register($user))) {
						$lacompte=calcul_acompte($info_resa->date_debut_resa,$info_resa->heure_debut_resa,$le_montant_total);
						if (est_register($user))
							echo "<font color=red>En supprimant cette resa, vous allez perdre votre acompte de ".$lacompte." euros</font>";
						else {
							if ($lacompte>0){
								$form.="&caution_suppr=".$info_resa->id_client;
								echo "<font color=red>En supprimant cette resa, la caution de cette resa sera perdue : ".$lacompte;
								echo "€ <br>De plus, les r&eacute;sas &agrave; venir (de ce client) seront en sursis pendant 24h le temps qu'il remette une caution.</font>";
							}
						}
					}
				
					$form.="\" method=\"post\" >";
					echo $form;
					if (isset($lacompte) and $lacompte>0 and est_min_manager($user))
						echo "<br><INPUT type=\"checkbox\" name=\"veto_gerant\" value=\"1\" > Ne pas faire sauter la caution<br><br>";
					echo "Motif";
					menu_deroulant("Motif_annul_resa",$_POST["Motif_annul_resa"]);

					?>
					<br><textarea rows="4" cols="100" name="commentaire_annul_resa" placeholder="Commentaire obligatoire avec minimum 10 caracteres"></textarea><br>
						<input name="suppr" type="button"  value="Supprimer cette reservation" onclick="valider()">
					</FORM>
				</td>
			</tr>
		<?
		}
		echo "</table><BR><HR>";
		
		echo "<font color=red><center>";
		if ($info_resa->indic_annul<>1 and $info_resa->cautionnable==2 and ($le_montant_total+$montant_total_presta-$total_versement)>0) {
			echo "<img src=\"images/resa-en-sursis-icon.png\" title=\"R&eacute;sa en sursis (sans caution)\"><br>";
			echo " Cette r&eacute;sa est en sursis pendant 24h car la caution du client a &eacute;t&eacute; supprim&eacute;e ";
			echo " le ".date_longue($info_resa->date_suppr_caution)." &agrave; ".$info_resa->heure_suppr_caution."<br>Il reste encore ";
			$temps_restant=-1*diff_dates_en_minutes(decaler_jour($info_resa->date_suppr_caution,1),$info_resa->heure_suppr_caution);
			if ($temps_restant<60)
				echo $temps_restant." minutes";
			else echo ceil($temps_restant/60)." heures";
			echo " avant que cette r&eacute;sa soit supprim&eacute;e du calendrier.<HR>";
		}
		
			echo "</font><table width=\"100%\"><tr>";
			
			if (test_non_vide($ajout_presta)){
				$gras_presta_deb="<b>";
				$gras_presta_fin="</b>";
				$fond_presta="#E2E2E3";
			}
			else {

				$gras_regl_deb="<b>";
				$gras_regl_fin="</b>";
				$fond_regl="#E2E2E3";
			}
			
			$nbre_presta=nbre_presta($info_resa->id_resa);
			$nbre_regl=nbre_reglements($info_resa->id_resa);
			
			echo "<td  nowrap align=\"center\" width=110 bgcolor=".$fond_regl.">".$gras_regl_deb."Reglements (".$nbre_regl.")".$gras_regl_fin."<br>";
			
			if ($nbre_regl>0){
				echo "<a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$info_resa->id_resa."\" />";
				echo "<img src=\"images/coins-icon.png\" title=\"Reglements de cette r&eacute;sa\"></a> </td>";
			}
			else {
				echo "<a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$info_resa->id_resa."\" />";
				echo "<img src=\"images/coin-add-icon.png\" title=\"Ajouter un reglement &agrave; cette r&eacute;sa\"></a> </td>";
			}
			
			if ($nbre_presta>0 or est_min_agent($user))
				echo "<td nowrap align=\"center\" width=110 bgcolor=".$fond_presta.">".$gras_presta_deb."Prestations (".$nbre_presta.")".$gras_presta_fin."<br>";
			
			if ($nbre_presta>0){
				echo "<a href=\"index.php/component/content/article?id=61&ajout_presta=1&premiere=1&id_resa=".$info_resa->id_resa."\" />";
				echo "<img src=\"images/prestations-icon.png\" title=\"Prestations li&eacute;es &agrave; cette r&eacute;sa\"></a> </td>";
			}
			else {
				if (est_min_agent($user)){
					echo "<a href=\"index.php/component/content/article?id=61&ajout_presta=1&premiere=1&id_resa=".$info_resa->id_resa."\" />";
					echo "<img src=\"images/ajout-prestations-icon.png\" title=\"Ajouter des prestations &agrave; cette r&eacute;sa\"></a> </td>";
				}
			}
			
	
			echo "<td  width=\"100%\" align=\"center\">";
		if (test_non_vide($info_acompte) or $info_resa->accompte_necessaire==1){
			echo $info_acompte."<br><img src=\"images/resa_validee.png\" title=\"r&eacute;sa valid&eacute;e le ".date_longue($info_resa->date_valid)." &agrave; ".$info_resa->heure_valid." par ".$info_resa->nom_user;
				if (est_min_agent($info_resa->gid_user))
					echo " (FIF)";
			echo "\"></td>";
			$lien="libraries/ya2/devis.php?%3Afm&tmpl=component&print=1"
					."&layout=default&page=&option=com_content&id_resa=".$info_resa->id_resa."&date_debut_resa=".$info_resa->date_debut_resa."&date_fin_resa=".$info_resa->date_fin_resa
					."&heure_debut_resa=".$info_resa->heure_debut_resa."&heure_fin_resa=".$info_resa->heure_fin_resa."&id_client=".$info_resa->id_client."&montant_total_des_remises=".($total_versement-$total_versement_hors_remises)
					."&montant_total=".$info_resa->montant_total."&montant_total_presta_10=".(montant_total_presta($info_resa->id_resa,"",4)+montant_total_presta($info_resa->id_resa,"",2))
					."&montant_total_presta_20=".(montant_total_presta($info_resa->id_resa,"",3)+montant_total_presta($info_resa->id_resa,"",1))."&devis_fact=FACTURE&tva=".recup_taux_TVA_d_une_date($info_resa->date_debut_resa);
			if ($le_montant_total+$montant_total_presta-$total_versement==0 )//and diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa)>0) {
				echo "<td nowrap align=\"center\" width=100><b> Ma facture</b><br><a target=blank href=\"".$lien."&sortie=I\" />"
					."<img src=\"images/imprimante-icon.png\" title=\"Imprimer la facture\"></a> &nbsp;&nbsp;&nbsp;"
					."<a target=blank href=\"".$lien."&sortie=D\" />"
					."<img src=\"images/PDF-Document-icon.png\" title=\"T&eacute;l&eacute;charger la facture\"></a> ";;
		}
		echo "</td>";
			echo "<td  nowrap align=\"center\" width=100 ><b>";
		if (test_non_vide($info_acompte) or $info_resa->accompte_necessaire==1){
			if (diff_dates_en_minutes($info_resa->date_debut_resa,$info_resa->heure_debut_resa)>0){
				echo "Ma vid&eacute;o</b><br><a  target=blank href=\"http://www.mysportconnect.net/clubs/footinfive\" />";
				echo "<img src=\"images/Mysportconnect.png\" title=\"Ma video\"></a>";
			}
			
			
		}
		echo "</td>";
		echo "</tr></table><hr>";
		if ($info_resa->indic_annul==1){
			echo "Résa annul&eacute;e le ".date_longue($info_resa->date_valid)." &agrave; ".$info_resa->heure_valid;
			if ($info_resa->id_Motif_annul_resa<>7){
				echo " par ".$info_resa->nom_user;
				if (est_min_agent($info_resa->gid_user))
					echo " (FIF) ";
			}
			echo " pour le motif suivant : ".$info_resa->Motif_annul."<hr>";
		}
		if (test_non_vide($les_erreurs))
			echo "<font color=red>".$les_erreurs."<hr>";
		
		echo "</center></font>";
		
		if (!test_non_vide($_GET["annul"]) and $info_resa->indic_annul<>1 and $info_resa->id_client<>3586 and !test_non_vide($ajout_presta) and !test_non_vide($_GET["modif_op"])) {
			echo "<table class=\"zebra\">";
			echo "<tr><th>Type</th><th>info</th>";
			if ($_POST["type"]==1 or !test_non_vide($_POST["type"])) echo "<th>Moyen de<br> paiement</th>";
			if ($_POST["type"]==2) echo "<th>Remise</th>";
			if ($_POST["type"]==3) echo "<th >Moyen de<br>remboursement</th>";
			if ($_POST["type"]==4) echo "<th >Vers</th>";
			echo "<th>Montant</th>";
			echo "</tr><tr>";
				?><form name="register" class="submission box" action="article?id=61&id_resa=<?echo $id_resa;?>" method="post"  >
				<?
				echo "<td>";
				if (est_min_agent($user)){
					echo "<select name=\"type\" onChange=\"recharger('','')\">";
					echo "<option value=1 ";
					if ($_POST["type"]==1) echo " selected ";
						echo ">Paiement</option>";
					if (($info_resa->id_type_regroupement<>1 and $info_resa->id_type_regroupement<>2) or est_min_manager($user)){
						echo "<option value=2 ";
							if ($_POST["type"]==2) echo " selected ";
								echo ">Remise</option>";
					}
					echo "<option value=3 ";
					if ($_POST["type"]==3) echo " selected ";
						echo ">Remboursement</option>";
					echo "<option value=4 ";
					if ($_POST["type"]==4) echo " selected ";
						echo ">Transfert</option>";
					echo "</select>";
				}
				else echo "Paiement<input type=\"hidden\" name=\"type\"  value=\"1\">";
				echo "</td>";
				echo "<td>";
				echo "<input type=\"text\" name=\"info\" maxlength=\"20\" size=\"8\" value=\"";
				//if (test_non_vide($_POST["info"]) and (test_non_vide($les_erreurs))) echo $_POST["info"]; else echo $info_resa->prenom;
				echo "\">";
				echo "</td>";
				if ($_POST["type"]==1 or $_POST["type"]==3 or !test_non_vide($_POST["type"])){
					if (!test_non_vide($_POST["type"]))
						$type_temp=1;
					else $type_temp=$_POST["type"];
					echo "<td>";
					menu_deroulant("Moyen_paiement","","",$type_temp);
					echo "</td>";
				}
				if ($_POST["type"]==4 ){
					echo "<td>";
					menu_deroulant("Type_credit",$_POST["Type_credit"]);
					echo "</td>";
				}
				if ($_POST["type"]==2){
					echo "<td>";
					menu_deroulant("Remise","","","",0,$info_resa->date_debut_resa,$info_resa->date_fin_resa,$info_resa->heure_fin_resa,$info_resa->id_type_regroupement);
					echo "</td>";
				}
				echo "<td nowrap>";
				if (est_min_agent($user)) {
					echo "<input type=\"text\" name=\"Montant\" maxlength=\"11\" size=\"8\" value=\"";
					if  (test_non_vide($les_erreurs)) echo $_POST["Montant"];
					echo "\">";
				}
				else {
					if (($le_montant_total+$montant_total_presta-$total_versement)>0){
						echo "<select name=\"Montant\" >";
							if ($total_versement=="" or $total_versement==0)
								$montant_a_afficher=$Montant_Min;
							else $montant_a_afficher=($le_montant_total+$montant_total_presta-$total_versement);
							echo "<option value=\"".$montant_a_afficher."\">".$montant_a_afficher." €</option>";
							if (($total_versement=="" or $total_versement==0) and ($info_resa->indic_annul<>1))
								echo "<option value=\"".$le_montant_total."\" \"".$select_montant."\">".$le_montant_total." €</option>";
						echo "</select>";
					}
				}
				if (!test_non_vide($total_versement) or $total_versement==0)
					echo "<input name=\"Montant_Min\" type=\"hidden\"  value=\"".$Montant_Min."\">";
				echo "<input name=\"Reste_a_payer\" type=\"hidden\"  value=\"".($le_montant_total+$montant_total_presta-$total_versement)."\">";

				echo "<input name=\"credit_en_base\" type=\"hidden\"  value=\"".$credit_client."\">";
			echo "<input name=\"total_versement\" type=\"hidden\"  value=\"".$total_versement."\"> ";

			?><input name="valide" type="button" value="Payer" onclick="recharger('Confirmez ce paiement','')"><?
			echo "</td>";
			echo "</tr>";
			echo "</form>";
			echo "</table><br>";
		}
		if (!test_non_vide($_GET["annul"]) and (test_non_vide($ajout_presta)) and $info_resa->id_client<>3586 ) {
			if (test_non_vide($ajout_presta)){
				if (est_min_agent($user) and $info_resa->indic_annul<>1) {
					echo "<table class=\"zebra\" >";
					echo "<tr><th>Type prestation</th><th>TVA</th><th>Montant</th>";
					echo "</tr><tr>";
						?><form name="register" class="submission box" action="article?id=61&ajout_presta=1&id_resa=<?echo $id_resa;?>" method="post"  >
						<?
						echo "<td>";
						echo "<input type=\"text\" name=\"Type_presta\" maxlength=\"20\" size=\"8\" value=\"";
						if (test_non_vide($_POST["Type_presta"]) and (test_non_vide($les_erreurs)))
							echo $_POST["Type_presta"];
						echo "\">";
						echo "</td>";
		
						echo "<td>";
						menu_deroulant("TVA",$_POST["TVA"]);
						echo "</td>";
						
						echo "<td>";
						echo "<input type=\"text\" name=\"Montant\" maxlength=\"8\" size=\"8\" value=\"";
						if  (test_non_vide($les_erreurs))
							echo $_POST["Montant"];
						echo "\">";
						echo "</td>";
						echo "<td >";
						?><input name="valide" type="button" value="Ajouter" onclick="recharger('Confirmez cette presta','')"><?
						echo "</td>";
					echo "</tr>";
					echo "</form>";
					echo "</table><br>";
				}
				$requete_liste_prest="Select p.*, t.nom as la_tva, (select name from #__users where id=p.id_user) as nom_user "
						." FROM `Prestation` as p, TVA as t "
						." Where p.id_TVA=t.id and p.id_resa=".$id_resa;
				//echo $requete_liste_prest;
				$db->setQuery($requete_liste_prest);
				$db->query();
				$nbre_prest=$db->getNumRows();
				
				
				if ($nbre_prest>0) {
					echo "<table class=\"zebra\" >";
					$total_presta_ttc=0;
					$total_presta_ht=0;
					echo "<tr><th>Effectuer par</th><th>Date</th><th>Type</th><th>TVA</th><th>Montant</th><th>suppr</th></tr>";
					$resultat_liste_prest= $db->loadObjectList();
					foreach($resultat_liste_prest as $liste_prest){
						echo "<tr>";
						echo "<td>";
						echo $liste_prest->nom_user;
						echo "</td>";
						echo "<td>".date_longue($liste_prest->date_creation)." &agrave; ".$liste_prest->heure_creation." </td>";
						echo "<td>";
						echo $liste_prest->type_prestation;
						echo "</td>";
						echo "<td>";
						echo $liste_prest->la_tva."% ";
						if  ($liste_prest->prestation_validation==1)
							$total_presta_ht+=($liste_prest->Montant_TTC/(1+($liste_prest->la_tva/100)));
						echo "</td>";
						echo "<td>";
						echo $liste_prest->Montant_TTC."€ ";
						if  ($liste_prest->prestation_validation==1)
							$total_presta_ttc+=$liste_prest->Montant_TTC;
						echo "</td>";
						echo "<td>";
							if  ($liste_prest->prestation_validation==0) {
								echo " <img src=\"images/Cancel-resa.png\" title=\"prestation supprim&eacute;e\">";
								if (test_non_vide($_GET["reactiver_presta"]) and ($_GET["reactiver_presta"]==$liste_prest->id_prestation)){
									
									$resultat_reactiver_presta= maj_validation_prestation(1,$_GET["reactiver_presta"]);
									header("Location: article?id=61&ajout_presta=1&id_resa=".$info_resa->id_resa."");
								}
								else {
									if (est_min_agent($user))
										echo "<a href=\"index.php/component/content/article?id=61&ajout_presta=1&reactiver_presta=".$liste_prest->id_prestation."&id_resa=".$info_resa->id_resa."\" />reactiver</a>";
								}
							}
							else {
								if (est_min_agent($user))	{
									echo " <a onClick=\"recharger('Voulez-vous supprimer cette prestation ?'";
									echo ",'".JRoute::_('article?id=61')."&ajout_presta=1";
									echo "&id_resa=".$info_resa->id_resa."&id_presta=".$liste_prest->id_prestation."')\">";
									echo "<img src=\"images/prestations-suppr-icon.png\" title=\"supprimer cette presta\">";
								}
							}
						echo "</td>";
						echo "</tr>";
					}
					echo "<tr>";
					echo "<td colspan=4 align=right>Sous total ht </td>";
					echo "<td><b>".number_format($total_presta_ht,2,"."," ")."€</b></td>";
					echo "<td>&nbsp;</td>";			
					echo "</tr>";
					echo "<tr>";
					echo "<td colspan=4 align=right>Montant total</td>";
					echo "<td><b>".$total_presta_ttc."€</b></td>";		
					echo "<td>&nbsp;</td>";
					echo "</tr>";
					echo "</table><br>";
				}
			}
			
		}
		else {
			if ($info_resa->id_client<>3586 ){
				$requete_liste_reg="Select m.nom as moy_paie, m.id as id_moy_paie, ";
				$requete_liste_reg.=" (select rem.nom from Remise as rem where reg.id_remise=rem.id) as la_remise, ";
				$requete_liste_reg.=" reg.*, (select name from #__users where id=reg.id_user) as nom_user ";
				$requete_liste_reg.=" , (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=reg.id_user) as gid_user FROM `Reservation` as r, ";
				$requete_liste_reg.=" Reglement as reg left join Moyen_paiement as m on reg.id_moyen_paiement=m.id";
				$requete_liste_reg.="  where reg.id_reservation=r.id_resa ";
				$requete_liste_reg.=" and r.id_resa=".$id_resa." ".$complement_req;
				if (est_register($user)) $requete_liste_reg.=" and reg.validation_reglement=1 ";
				$requete_liste_reg.=" order by reg.date_reglement, reg.heure_reglement";
				
				//echo $requete_liste_reg;
				$db->setQuery($requete_liste_reg);
				$db->query();
				$nbre_reg=$db->getNumRows();
				echo "<table class=\"zebra\" >";
				echo "<tr><th>Effectuer par</th><th>Date du paiement</th><th>info</th><th>Type</th><th>Op</th><th>Montant</th><th>suppr</th></tr>";
				
				if ($nbre_reg>0) {
					
					$resultat_liste_reg= $db->loadObjectList();
					foreach($resultat_liste_reg as $liste_reg){
						echo "<tr>";
						echo "<td>";
						echo $liste_reg->nom_user;
						if (est_min_agent($liste_reg->gid_user)) echo " (FIF)";
						echo "</td>";
						echo "<td>".date_longue($liste_reg->date_reglement)." &agrave; ".$liste_reg->heure_reglement." </td>";
						echo "<td>";
						echo $liste_reg->info;
						echo "</td>";
						echo "<td>";
						if ($liste_reg->montant_reglement<0) {
							if ($liste_reg->id_moyen_paiement==7)
								echo "Transfert";
							else 	echo "Remboursement";
						}
						else if (test_non_vide($liste_reg->moy_paie))
								echo "Paiement";
							else echo "Remise";
						echo "</td>";
						echo "<td>";
						if (test_non_vide($liste_reg->moy_paie)) {
							if (!test_non_vide($_GET["modif_op"]) or $_GET["modif_op"]<>$liste_reg->id_reglement ){
								if (est_manager($user))
									echo "<a href=\"index.php/component/content/article?id=61&modif_op=".$liste_reg->id_reglement."&id_resa=".$info_resa->id_resa."\" />";
								
								echo $liste_reg->moy_paie;
								
								echo"</a>";
							}
							else{
								?><form name="register_modif_op" class="submission box" action="article?id=61&id_resa=<? echo $id_resa; ?>" method="post"  >
								<?
								menu_deroulant("Moyen_paiement","","enregistrer2()",1);
								?>
								<input type="hidden" name="modif_op" value="<? echo $_GET["modif_op"]; ?>"/>
								</form>
								<?
								
							}
						}
						else echo $liste_reg->la_remise;
						echo "</td>";
						echo "<td>";
						echo $liste_reg->montant_reglement."€ ";
						echo "</td>";
						echo "<td>";
							if  ($liste_reg->validation_reglement==0) {
								echo " <img src=\"images/Cancel-resa.png\" title=\"reglement supprim&eacute;\">";
								if (test_non_vide($_GET["reactiver_regl"]) and ($_GET["reactiver_regl"]==$liste_reg->id_reglement)){
									
									$resultat_reactiver_regl= maj_validation_reglement(1,$_GET["reactiver_regl"]);
									
									if (test_non_vide($resultat_reactiver_regl) and $liste_reg->id_moy_paie=="2") {			
										if ($liste_reg->montant_reglement<>0)	
											ajout_credit($id_client,(-1*$liste_reg->montant_reglement),1,"React_regl_resa_".$info_resa->id_resa,2);
									}
									if ((!test_non_vide($liste_reg->id_remise)) or $liste_reg->id_remise==0){
										$requete_maj_resa_a_supprimer="UPDATE Reservation SET a_supprimer=0 where id_resa=".$info_resa->id_resa;
										$db->setQuery($requete_maj_resa_a_supprimer);	
										$resultat_maj_resa_a_supprimer = $db->query();
									}
									header("Location: article?id=61&id_resa=".$info_resa->id_resa."");
								}
								
								else {
									if (est_min_agent($user) and $liste_reg->id_moyen_paiement<7 )
										echo "<a href=\"index.php/component/content/article?id=61&reactiver_regl=".$liste_reg->id_reglement."&id_resa=".$info_resa->id_resa."\" />reactiver</a>";
								}
							}
							else {
								if (est_min_agent($user) and $liste_reg->id_moyen_paiement<7 and $info_resa->indic_annul<>1)	{
									echo " <a onClick=\"recharger('Voulez-vous supprimer cette ligne de reglement ?','".JRoute::_('article?id=61')
										."&id_resa=".$info_resa->id_resa."&id_regl=".$liste_reg->id_reglement."')\">"
										."<img src=\"images/coin-delete-icon.png\" title=\"supprimer ce reglement\">";
								}
							}
							echo "</td>";
						echo "</tr>";
					}
				}
				if (($total_versement-$total_versement_hors_remises)<>0){
					echo "<tr>";
					echo "<td colspan=5 align=right>Sous total des remises </td>";
					echo "<td><b>".($total_versement-$total_versement_hors_remises)."€</b></td>";		
					echo "<td>&nbsp;</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td colspan=5 align=right>Sous total hors-remises </td>";
				echo "<td><b>".$total_versement_hors_remises."€</b></td>";
				echo "<td>&nbsp;</td>";			
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=5 align=right>Montant total</td>";
				echo "<td><b>".$total_versement."€</b></td>";		
				echo "<td>&nbsp;</td>";
				echo "</tr>";
				echo "</table><br>";
			}
		}
	if (test_non_vide($_GET["hist"])){
		$requete_liste_hist_resa="SELECT (select name from #__users where id=`id_user_old`) as user_modif, ";
		$requete_liste_hist_resa.=" (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=`id_user_old`) as gid_user_modif, HR.* FROM `Hist_Reservation` as HR ";
		$requete_liste_hist_resa.=" where id_resa=".$id_resa." order by date_modif_resa desc, heure_modif_resa desc";
		//echo $requete_liste_hist_resa;
	
		$db->setQuery($requete_liste_hist_resa);	
		$resultat_liste_hist_resa= $db->loadObjectList();
		
		if ($resultat_liste_hist_resa) {
			echo "<hr><h2><a name=\"signet\"></a>Historique</h2><hr><table class=\"zebra\"><tr>";
			echo "<th>Modifi&eacute; par</th><th>Date modif.</th><th>Heure modif.</th>";
			echo "<th>date r&eacute;sa</th><th>heure d&eacute;but r&eacute;sa</th><th>heure fin r&eacute;sa</th><th>montant</th></tr>";
			
			foreach($resultat_liste_hist_resa as $liste_hist_resa){
	
				echo "<tr>";
				echo "<td nowrap>";
				echo $liste_hist_resa->user_modif;
				if (est_min_agent($liste_hist_resa->gid_user_modif)) echo " (FIF)";
				echo "</td><td>";
				echo date_longue($liste_hist_resa->date_modif_resa)."</td><td>";
				echo $liste_hist_resa->heure_modif_resa."</td><td>";
				echo date_longue($liste_hist_resa->date_debut_old_resa);
				echo "</td><td align=center>";
				echo $liste_hist_resa->heure_deb_old_resa;
				echo "</td><td align=center>";
				echo $liste_hist_resa->heure_fin_old_resa."</td><td>";
				echo $liste_hist_resa->montant_old_resa." €</td></tr>";
			}
			echo "</table>";
		}
	}
	else echo "<a href=\"".$_SERVER['REQUEST_URI']."&hist=1#signet\" />Afficher l'historique</a>";
	
}
}
?>