<?php
require_once ('admin_base.php');
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "/tarif_speciale/clienttarificationspeciale_controller.class.php");
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "/tarif_speciale/groupetarificationspeciale_controller.class.php");
$siteURL = $config->get ( 'site_url' );
?>
<script type="text/javascript">
	
	function enregistrer() {
		document.register_user.submit()
	}
	
	function recharger(texte_a_afficher,lien) {
		if (texte_a_afficher!=''){
			if (confirm(texte_a_afficher)){
				if (lien!='') document.location.href=lien;
				//else document.register_versement.submit();
			}
		}
		else {
			if (lien!='') document.location.href=lien;
			//else {
				//document.register_versement.Montant.value='';
				//document.register_versement.submit();
			//}
		}
	}
	
</script>

<?

if (est_min_agent ( $user )) {
	if (test_non_vide ( $_POST ["id_client"] ))
		$id_client = $_POST ["id_client"];
	else
		$id_client = $_GET ["id_client"];
} else
	$id_client = idclient_du_user ();
$newsletter = 0;
$ctsController = new ClientTarificationSpecialeController ();
$gtsController = new GroupeTarificationSpecialeController ();

$allGts = $gtsController->GetAllGroupeTarificationSpeciale ();

if (test_non_vide ( $id_client )) {
	$cts = $ctsController->GetClientTarificationSpecialeByClientId ( $id_client );
	// if($cts->Getgtsid()==-1)$cts=null;
	if ($cts != null)
		$gts = $gtsController->GetGroupeTarificationSpecialeById ( $cts->Getgtsid () );
}

if (! isset ( $id_client ))
	$_GET ["modif"] = 1;

if (est_min_manager ( $user ) and test_non_vide ( $_GET ["accompte_necessaire"] )) {
	$requete_maj_accompte_necessaire = "UPDATE Client SET accompte_necessaire=" . $_GET ["accompte_necessaire"] . " where id_client=" . $id_client;
	// echo "<br>reqsuppr: ".$requete_maj_accompte_necessaire;
	$db->setQuery ( $requete_maj_accompte_necessaire );
	$db->query ();
	if ($_GET ["accompte_necessaire"] == 1)
		$vip = "VIP";
	else
		$vip = "NON-VIP";
	sendMail ( 267, "Client " . $vip . " : " . $id_client . " ", "<br>user_modif:" . $user->id . "<br><br>$siteURL/index.php/client/creer?id=60&id_client=" . $id_client . "" );
}

if (est_min_agent ( $user ) and test_non_vide ( $_GET ["police"] )) {
	$requete_maj_police = "UPDATE Client SET police=" . $_GET ["police"] . " where id_client=" . $id_client;
	// echo "<br>reqsuppr: ".$requete_maj_police;
	$db->setQuery ( $requete_maj_police );
	$db->query ();
	if ($_GET ["police"] == 1)
		$police = "POLICE";
	else
		$police = "NON-POLICE";
	sendMail ( 267, "Client " . $police . " : " . $id_client . " ", "<br>user_modif:" . $user->id . "<br><br>$siteURL/index.php/client/creer?id=60&id_client=" . $id_client . "" );
}

if (est_min_agent ( $user ) and test_non_vide ( $_GET ["suppr_client"] )) {
	echo "<font color=red>";
	$requete_verif_resa_client = "Select id_resa from Reservation where id_client=" . $_GET ["suppr_client"] . " and indic_annul=0";
	// echo "<br>reqsuppr: ".$requete_verif_resa_client;
	$db->setQuery ( $requete_verif_resa_client );
	$db->query ();
	$nbre_resultats_resa = $db->getNumRows ();
	
	if ($nbre_resultats_resa > 0)
		echo "Ce client ne peut pas etre supprim&eacute; car il a des r&eacute;sas.";
	else {
		$requete_verif_credit_client = "Select id_credit_client from `Credit_client`  where id_client=" . $_GET ["suppr_client"] . " and validation_credit=1";
		// echo "<br>reqsuppr: ".$requete_verif_credit_client;
		$db->setQuery ( $requete_verif_credit_client );
		$db->query ();
		$nbre_resultats_credit = $db->getNumRows ();
		
		if ($nbre_resultats_credit > 0)
			echo "Ce client ne peut pas etre supprim&eacute; car il a du cr&eacute;dit.";
		else {
			$requete_verif_reglement_client = "Select id_reglement from Reglement where id_client=" . $_GET ["suppr_client"] . " and validation_reglement=1";
			// echo "<br>reqsuppr: ".$requete_verif_reglement_client;
			$db->setQuery ( $requete_verif_reglement_client );
			$db->query ();
			$nbre_resultats_reglement = $db->getNumRows ();
			
			if ($nbre_resultats_reglement > 0)
				echo "Ce client ne peut pas etre supprim&eacute; car il a pass&eacute; des reglements.";
			else {
				$resultat_recup_id_user = recup_1_element ( "id_user", "Client", "id_client", $_GET ["suppr_client"] );
				
				supprimer_1_element ( "Client", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "Reservation", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "Credit_client", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "Reglement", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "Hist_Client", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "Hist_Credit_client", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "Hist_Reservation", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "Commentaires", "id_client", $_GET ["suppr_client"] );
				supprimer_1_element ( "#__users", "id", $resultat_recup_id_user );
				supprimer_1_element ( "#_user_usergroup_map", "user_id", $resultat_recup_id_user );
				
				$rootPath = $config->get ( 'root_app_path' );
				header ( "Location: $rootPath" );
			}
		}
	}
	echo "</font>";
}

if (test_non_vide ( $user->id ))
	menu_acces_rapide ( $id_client, "Fiche client" );

if (isset ( $_GET ["modif"] )) {
	$existe_erreur = 0;
	
	if (test_non_vide ( $_POST ["code_insee"] ))
		$code_insee = $_POST ["code_insee"];
	
	if (test_non_vide ( $_POST ["date_nais"] )) {
		if (! test_valid_existence_date ( $_POST ["date_nais"] )) {
			echo "<font color=red>La date de naissance est incorrecte.<br></font>";
			$existe_erreur ++;
		}
		if (diff_dates_en_minutes ( inverser_date ( $_POST ["date_nais"] ) ) < 8409600) { // age min de 16 ans en minutes
			echo "<font color=red>Le client doit avoir plus de 16 ans.<br></font>";
			$existe_erreur ++;
		}
	}
	if (! test_non_vide ( $_POST ["nom"] ) or ! test_non_vide ( $_POST ["prenom"] ) or ! test_non_vide ( $_POST ["telmob1"] ) or ! test_non_vide ( $_POST ["courriel"] ) or (! test_non_vide ( $_POST ["cp"] ) && ! test_non_vide ( $_POST ["code_insee"] ))) {
		echo "<font color=red>Le nom, le prenom, le numero de mobile, le code postal ainsi que l'email sont obligatoires.<br></font>";
		$existe_erreur ++;
	}
	
	if (test_non_vide ( $_POST ["nom"] ) and test_non_vide ( $_POST ["prenom"] ) and ! (VerifierNom ( $_POST ["nom"] ) and VerifierNom ( $_POST ["prenom"] ))) {
		echo "<font color=red>Le nom ou le prenom est incorrect.<br></font>";
		$existe_erreur ++;
	}
	
	for($i = 1; $i < 5; $i ++) {
		if (test_non_vide ( $_POST ["telmob$i"] ) and ! (VerifierNumMob ( $_POST ["telmob$i"] ))) {
			echo "<font color=red>Numero de Tel mobile " . $i . " incorrect.<br></font>";
			$existe_erreur ++;
		} else {
			$resultat_verif_si_mob_existe = 0;
			
			if (test_non_vide ( $_POST ["telmob$i"] )) {
				for($j = 1; $j < 5; $j ++) {
					$requete_verif_si_mob_existe = "select  ( Trim(\"" . Trim ( $_POST ["telmob$i"] ) . "\") in  (select mobile" . $j . " from Client where mobile" . $j . "<>\"0600000000\" ";
					if (test_non_vide ( $id_client ))
						$requete_verif_si_mob_existe .= " and id_client<>" . $id_client . "";
					$requete_verif_si_mob_existe .= " ) ) as nbre_occurences";
					// echo $requete_verif_si_mob_existe."<br>";
					$db->setQuery ( $requete_verif_si_mob_existe );
					$resultat_verif_si_mob_existe += $db->loadResult ();
				}
			}
			if ($resultat_verif_si_mob_existe > 0) {
				echo "<font color=red>Num&eacute;ro de mobile d&eacute;j&agrave; attribu&eacute; : " . $_POST ["telmob$i"] . ".<br></font>";
				$existe_erreur ++;
			}
		}
	}
	if (test_non_vide ( $_POST ["telfixe"] ) and ! (VerifierNumFixe ( $_POST ["telfixe"] ))) {
		echo "<font color=red>Numero de Tel fixe incorrect.<br></font>";
		$existe_erreur ++;
	}
	
	/*
	 * if (test_non_vide($_POST["Adresse"]) and !VerifierNomVille($_POST["Adresse"])){
	 * echo "<font color=red>Adresse incorrecte.<br></font>";
	 * $existe_erreur++;
	 * }
	 */
	
	verif_cp_ville ( $code_insee, $_POST ["cp"], $_POST ["ville"], $tab_villes, $tab_cp, $nbre_villes, $nbre_cp, $existe_erreur );
	
	if (test_non_vide ( $_POST ["courriel"] ) and ! (VerifierAdresseMail ( $_POST ["courriel"] ))) {
		echo "<font color=red>Votre adresse email est incorrecte.<br></font>";
		$existe_erreur ++;
	} else {
		if (test_non_vide ( $_POST ["courriel"] ) and ! test_non_vide ( $_POST ["id_user"] )) {
			$requete_verif_si_email_existe = "SELECT count(id) FROM #__users where email=Trim(\"" . Trim ( $_POST ["courriel"] ) . "\") and Trim(\"" . Trim ( $_POST ["courriel"] ) . "\")<>\"agent@footinfive.com\";";
			$db->setQuery ( $requete_verif_si_email_existe );
			$resultat_verif_si_email_existe = $db->loadResult ();
			
			if ($resultat_verif_si_email_existe > 0) {
				echo "<font color=red>Adresse email d&eacute;j&agrave; utilis&eacute;e.</font>.<br>";
				$existe_erreur ++;
			}
		}
	}
}
if ($existe_erreur == 0 and (isset ( $_GET ["modif"] )) and test_non_vide ( $_POST ["condition"] )) {
	
	if ($code_insee == "") {
		if ((test_non_vide ( $_POST ["cp"] )) and (test_non_vide ( $_POST ["ville"] ))) {
			
			$le_resultat = recup_insee ( "", $_POST ["cp"], $_POST ["ville"] );
			$resultat_recup_insee = $le_resultat->code_insee;
			
			if ($resultat_recup_insee > 0)
				$code_insee = $resultat_recup_insee;
			else {
				echo "<font color=red>Erreur code insee.</font>.<br>";
				break;
			}
		}
	}
	if (test_non_vide ( $_POST ["id_client"] ) and $_POST ["id_user"] != 0) {
		if (test_non_vide ( $_POST ["courriel"] ))
			maj_user ( $_POST ["id_user"], $_POST ["prenom"], $_POST ["courriel"] );
	}
	if (((test_non_vide ( $_POST ["id_client"] ) and $_POST ["id_user"] == 0) or ! test_non_vide ( $_POST ["id_client"] )) and test_non_vide ( $_POST ["courriel"] )) {
		
		$user_id = ajout_user ( $_POST ["prenom"], $_POST ["courriel"], $_POST ["password"] );
		ajout_user_au_groupe ( $user_id, 2 );
	}
	if (test_non_vide ( $_POST ["id_client"] )) {
		
		$requete_recup_old_client = "select * from Client WHERE id_client=" . $_POST ["id_client"];
		$db->setQuery ( $requete_recup_old_client );
		$recup_old_client = $db->loadObject ();
		
		$requete_insert_old_client = "INSERT INTO `Hist_Client`( id_client, `id_user`, ";
		$requete_insert_old_client .= " `id_user_modif`,date_modif, heure_modif, `nom`, `prenom`,";
		for($i = 1; $i < 5; $i ++)
			$requete_insert_old_client .= "`mobile" . $i . "`,";
		$requete_insert_old_client .= " `fixe`, `code_insee`, `adresse`, `date_naissance`";
		if (est_min_agent ( $user ))
			$requete_insert_old_client .= " ,societe, equipe";
		$requete_insert_old_client .= " ) ";
		$requete_insert_old_client .= " VALUES ( " . $_POST ["id_client"] . " ,";
		if (isset ( $user_id ) and $user_id != "")
			$requete_insert_old_client .= $user_id . " , ";
		else
			$requete_insert_old_client .= $recup_old_client->id_user . " , ";
		$requete_insert_old_client .= $recup_old_client->id_user_modif . " ,\"" . $recup_old_client->date_modif . "\",\"" . $recup_old_client->heure_modif . "\",";
		$requete_insert_old_client .= "\"" . $recup_old_client->nom . "\",\"" . $recup_old_client->prenom . "\",";
		$requete_insert_old_client .= "\"" . $recup_old_client->mobile1 . "\",";
		$requete_insert_old_client .= "\"" . $recup_old_client->mobile2 . "\",";
		$requete_insert_old_client .= "\"" . $recup_old_client->mobile3 . "\",";
		$requete_insert_old_client .= "\"" . $recup_old_client->mobile4 . "\",";
		$requete_insert_old_client .= " \"" . $recup_old_client->fixe . "\",\"" . $recup_old_client->code_insee . "\", ";
		$requete_insert_old_client .= " \"" . $recup_old_client->adresse . "\",\"" . $recup_old_client->date_naissance . "\"";
		if (est_min_agent ( $user ))
			$requete_insert_old_client .= " ,\"" . $recup_old_client->societe . "\",\"" . $recup_old_client->equipe . "\"";
		$requete_insert_old_client .= " )";
		// echo "<br>".$requete_insert_old_client;
		
		$db->setQuery ( $requete_insert_old_client );
		$db->query ();
		
		$newsletter = isset ( $_POST ["newsletter"] ) ? $_POST ["newsletter"] : 0;
		
		$requete_update_client = "UPDATE `Client` SET `id_user_modif`=" . $user->id . ", date_modif=\"" . date ( "Y" ) . "-" . date ( "m" ) . "-" . date ( "d" ) . "\",";
		if (isset ( $user_id ) and $user_id != "")
			$requete_update_client .= " `id_user`=" . $user_id . ", ";
		$requete_update_client .= " heure_modif=\"" . Ajout_zero_si_absent ( date ( "H" ) . ":" . date ( "i" ) ) . "\", `nom`=\"" . tout_majuscule ( $_POST ["nom"] ) . "\",";
		$requete_update_client .= " `prenom`=\"" . premiere_lettre_maj ( $_POST ["prenom"] ) . "\",";
		for($i = 1; $i < 5; $i ++)
			$requete_update_client .= " `mobile" . $i . "`=\"" . $_POST ["telmob$i"] . "\",";
		$requete_update_client .= " `fixe`=\"" . $_POST ["telfixe"] . "\" ";
		$requete_update_client .= " ,`newsletter`=\"" . $newsletter . "\" ";
		$requete_update_client .= " ,`code_insee`=\"" . $code_insee . "\", ";
		$requete_update_client .= " `adresse`=\"" . $_POST ["Adresse"] . "\",`date_naissance`=\"" . inverser_date ( $_POST ["date_nais"] ) . "\"";
		if (est_min_agent ( $user )) {
			$requete_update_client .= " ,`societe`=\"" . $_POST ["societe"] . "\"";
			$requete_update_client .= " , `equipe`=\"" . $_POST ["equipe"] . "\" , `nom_entite`=\"" . $_POST ["nom_entite"] . "\", `nom_service`=\"" . $_POST ["nom_service"] . "\" " . ", `Raison_Sociale`=\"" . $_POST ["Raison_Sociale"] . "\", `Siret`=\"" . $_POST ["Siret"] . "\"," . " `Adresse_facturation`=\"" . $_POST ["Adresse_facturation"] . "\", Ville_facturation=\"" . $_POST ["Ville_facturation"] . "\", " . " `Code_postal_facturation`=\"" . $_POST ["Code_postal_facturation"] . "\", `Effectif`=\"" . $_POST ["Effectif"] . "\"";
			
			if (test_non_vide ( $_POST ["Type_Regroupement"] ))
				$requete_update_client .= ", id_type_regroupement=" . $_POST ["Type_Regroupement"] . " ";
			else
				$requete_update_client .= ", id_type_regroupement=0 ";
		}
		$requete_update_client .= " WHERE id_client=" . $_POST ["id_client"];
		// echo "<br>".$requete_update_client;
		
		$db->setQuery ( $requete_update_client );
		$res = $db->query ();
		
		syncMailchimp ( $mailchimpAPIKey, $_POST ["nom"], $_POST ["prenom"], $_POST ["courriel"], $_POST ["newsletter"], $mailchimpURL, $_POST ["Type_Regroupement"] );
		
		// MAJ groupe tarification
		$gts = $_POST ['gts_id'];
		if ($cts != null) {
			if ($cts->Getgtsid () != $gts) {
				$now = date ( "Y-m-d H:i:s" );
				$cts->Setgtsid ( $gts );
				$cts->Setdatemodification ( $now );
				$result = $ctsController->UpdateClientTarificationSpeciale ( $cts );
			}
		} else {
			if ($gts != - 1) {
				$cts = new ClientTarificationSpecialeEntity ();
				$now = date ( "Y-m-d H:i:s" );
				$cts->Setclientid ( $id_client );
				$cts->Setgtsid ( $gts );
				$cts->Setdatemodification ( $now );
				$ctsId = $ctsController->AddClientTarificationSpeciale ( $cts );
			}
		}
		
		maj_commentaire ( "id_client", $_POST ["id_client"], $_POST ["commentaire"] );
		
		// le client a été modifié avec succes...
		if ($res)
			header ( "Location: $siteURL/index.php/client/creer?id=60&id_client=" . $_POST ["id_client"] . "" );
		else
			echo "pb resa bdd update";
	}
	
	if (! test_non_vide ( $_POST ["id_client"] )) {
		$requete_insert_client = "INSERT INTO `Client`(";
		if (isset ( $user_id ) and $user_id != "")
			$requete_insert_client .= " `id_user`, ";
		$requete_insert_client .= " `id_user_modif`,date_modif, heure_modif, `nom`, `prenom`, ";
		for($i = 1; $i < 5; $i ++)
			$requete_insert_client .= "`mobile" . $i . "`,";
		$requete_insert_client .= " `fixe`, `code_insee`, `adresse`, `date_naissance`";
		if (est_min_agent ( $user ))
			$requete_insert_client .= ", societe,equipe, " . "`id_type_regroupement`, `nom_entite`,nom_service, `Raison_Sociale`, `Siret`," . " `Adresse_facturation`, `Code_postal_facturation`,Ville_facturation, `Effectif`";
		$requete_insert_client .= " ) ";
		$requete_insert_client .= " VALUES (";
		if (isset ( $user_id ) and $user_id != "")
			$requete_insert_client .= $user_id . " , ";
		$requete_insert_client .= $user->id . " ,\"" . date ( "Y" ) . "-" . date ( "m" ) . "-" . date ( "d" ) . "\",";
		$requete_insert_client .= " \"" . Ajout_zero_si_absent ( date ( "H" ) . ":" . date ( "i" ) ) . "\",\"" . tout_majuscule ( $_POST ["nom"] ) . "\",";
		$requete_insert_client .= " \"" . premiere_lettre_maj ( $_POST ["prenom"] ) . "\",";
		for($i = 1; $i < 5; $i ++)
			$requete_insert_client .= "\"" . $_POST ["telmob$i"] . "\",";
		$requete_insert_client .= " \"" . $_POST ["telfixe"] . "\",\"" . $code_insee . "\",";
		$requete_insert_client .= " \"" . $_POST ["Adresse"] . "\",\"" . inverser_date ( $_POST ["date_nais"] ) . "\"";
		if (est_min_agent ( $user ))
			$requete_insert_client .= ", \"" . $_POST ["societe"] . "\",\"" . $_POST ["equipe"] . "\"," . "\"" . $_POST ["Type_Regroupement"] . "\",\"" . $_POST ["nom_entite"] . "\",\"" . $_POST ["nom_service"] . "\",\"" . $_POST ["Raison_Sociale"] . "\"," . " \"" . $_POST ["Siret"] . "\",\"" . $_POST ["Adresse_facturation"] . "\"," . " \"" . $_POST ["Code_postal_facturation"] . "\",\"" . $_POST ["Ville_facturation"] . "\",\"" . $_POST ["Effectif"] . "\"";
		$requete_insert_client .= " )";
		// echo "<br>".$requete_insert_client;
		
		$db->setQuery ( $requete_insert_client );
		$res = $db->query ();
		$id_new_client = $db->insertid ();
		
		syncMailchimp ( $mailchimpAPIKey, $_POST ["nom"], $_POST ["prenom"], $_POST ["courriel"], $_POST ["newsletter"], $mailchimpURL, $_POST ["Type_Regroupement"] );
		
		// ajout groupe tarification
		$gts = $_POST ['gts_id'];
		if ($gts != - 1) {
			$cts = new ClientTarificationSpecialeEntity ();
			$now = date ( "Y-m-d H:i:s" );
			$cts->Setclientid ( $id_new_client );
			$cts->Setgtsid ( $gts );
			$cts->Setdatemodification ( $now );
			$ctsId = $ctsController->AddClientTarificationSpeciale ( $cts );
		}
		
		maj_commentaire ( "id_client", $id_new_client, $_POST ["commentaire"] );
		if ($res) {
			if (est_register ( $user ))
				echo "Pour finaliser votre enregistrement <a href=\"$siteURL/index.php?option=com_user&view=reset\">cliquez-ici pour confirmer que vous &ecirc;tes le propri&eacutetaire de l'adresse email</a>.";
			else
				header ( "Location: $siteURL/index.php/client/creer?id=60&id_client=" . $id_new_client . "" );
		} else
			echo "pb resa bdd insert";
	}
} else {
	
	if (test_non_vide ( $_GET ["id_client"] ) or (test_non_vide ( $user->id ) and est_register ( $user ) and ! test_non_vide ( $_POST ["prenom"] ))) {
		if (est_min_agent ( $user ))
			$compl_criteres = " and c.id_client=" . $_GET ["id_client"] . " ";
		else
			$compl_criteres = " and c.id_user=" . $user->id . " ";
		
		$recup_client = recup_recup_client ( $compl_criteres, $id_client );
		
		$nom = $recup_client->nom_client;
		$prenom = $recup_client->prenom;
		$telmob [1] = $recup_client->mobile1;
		$telmob [2] = $recup_client->mobile2;
		$telmob [3] = $recup_client->mobile3;
		$telmob [4] = $recup_client->mobile4;
		$telfixe = $recup_client->fixe;
		$courriel = $recup_client->courriel;
		$Raison_Sociale = $recup_client->Raison_Sociale;
		$Siret = $recup_client->Siret;
		$Effectif = $recup_client->Effectif;
		$newsletter = $recup_client->newsletter;
		
		// Recupere la valeur courante de l'abonnement newsletter chez MailChimp
		$newsletter = Update_newsletter_fromMailchimp ( $courriel, $newsletter, $mailchimpAPIKey, $mailchimpURL );
		
		// mise à jour de la valeur en bdd
		if ($newsletter != $recup_client->newsletter) {
			$requete_update_newsletter = "UPDATE `Client` SET newsletter=" . $newsletter . " WHERE id_client=" . $recup_client->id_client;
			$db->setQuery ( $requete_update_newsletter );
			$res = $db->query ();
		}
		
		$Adresse = $recup_client->adresse;
		$Adresse_facturation = $recup_client->Adresse_facturation;
		$cp = $recup_client->code_postal;
		$Code_postal_facturation = $recup_client->Code_postal_facturation;
		$ville = $recup_client->nom_maj_ville;
		$ville_facturation = $recup_client->Ville_facturation;
		if ($recup_client->date_naissance != "0000-00-00")
			$date_nais = inverser_date ( $recup_client->date_naissance );
		$id_client = $recup_client->id_client;
		$id_user = $recup_client->id_user;
		if (est_min_agent ( $user )) {
			$ligne_commentaire = recup_derniere_commentaire ( "id_client", $recup_client->id_client );
			$commentaire = $ligne_commentaire->Commentaire;
			$societe = $recup_client->societe;
			$equipe = $recup_client->equipe;
			$id_type_regroupement = $recup_client->id_type_regroupement;
			$nom_entite = $recup_client->nom_entite;
			$nom_service = $recup_client->nom_service;
		}
	} else {
		if (test_non_vide ( $_POST ["nom"] ))
			$nom = $_POST ["nom"];
		if (test_non_vide ( $_POST ["prenom"] ))
			$prenom = $_POST ["prenom"];
		for($i = 1; $i < 5; $i ++)
			if (test_non_vide ( $_POST ["telmob$i"] ))
				$telmob [$i] = $_POST ["telmob$i"];
		if (test_non_vide ( $_POST ["telfixe"] ))
			$telfixe = $_POST ["telfixe"];
		if (test_non_vide ( $_POST ["courriel"] ))
			$courriel = $_POST ["courriel"];
		if (test_non_vide ( $_POST ["nom_entite"] ))
			$nom_entite = $_POST ["nom_entite"];
		if (test_non_vide ( $_POST ["nom_service"] ))
			$nom_service = $_POST ["nom_service"];
		if (test_non_vide ( $_POST ["Raison_Sociale"] ))
			$Raison_Sociale = $_POST ["Raison_Sociale"];
		if (test_non_vide ( $_POST ["Siret"] ))
			$Siret = $_POST ["Siret"];
		if (test_non_vide ( $_POST ["Effectif"] ))
			$Effectif = $_POST ["Effectif"];
		if (test_non_vide ( $_POST ["newsletter"] ))
			$newsletter = isset ( $_POST ["newsletter"] ) ? $_POST ["newsletter"] : 0;
		if (test_non_vide ( $_POST ["Adresse"] ))
			$Adresse = $_POST ["Adresse"];
		if (test_non_vide ( $_POST ["Adresse_facturation"] ))
			$Adresse_facturation = $_POST ["Adresse_facturation"];
		if (test_non_vide ( $_POST ["cp"] ))
			$cp = $_POST ["cp"];
		if (test_non_vide ( $_POST ["ville"] ))
			$ville = $_POST ["ville"];
		if (test_non_vide ( $_POST ["Code_postal_facturation"] ))
			$Code_postal_facturation = $_POST ["Code_postal_facturation"];
		if (test_non_vide ( $_POST ["Ville_facturation"] ))
			$ville_facturation = $_POST ["Ville_facturation"];
		if (test_non_vide ( $_POST ["date_nais"] ))
			$date_nais = $_POST ["date_nais"];
		if (test_non_vide ( $_POST ["id_client"] ))
			$id_client = $_POST ["id_client"];
		if (test_non_vide ( $_POST ["id_user"] ))
			$id_user = $_POST ["id_user"];
		if (est_min_agent ( $user )) {
			if (test_non_vide ( $_POST ["commentaire"] ))
				$commentaire = $_POST ["commentaire"];
			if (test_non_vide ( $_POST ["societe"] ))
				$societe = $_POST ["societe"];
			if (test_non_vide ( $_POST ["equipe"] ))
				$equipe = $_POST ["equipe"];
			if (test_non_vide ( $_POST ["Type_Regroupement"] ))
				$id_type_regroupement = $_POST ["Type_Regroupement"];
		}
	}
	?>

<form name="register_user" class="submission box"
	action="<?php echo $siteURL;?>/index.php/client/creer?id=60&modif=1"
	method="post">
	<br>
	<table class="zebra" border="0">
<?
	
	if (test_non_vide ( $id_client )) {
		
		$info_ledg = existe_joueur_capitaine ( $id_user );
		?>
		<tr>
			<td>
			<?
		$requete_id_client_precedent = "Select max(id_client) from Client where id_client<" . $id_client;
		$db->setQuery ( $requete_id_client_precedent );
		$client_precedent = $db->LoadResult ();
		if (test_non_vide ( $client_precedent )) {
			echo " <a href=\"$siteURL/index.php/client/creer?id=60&id_client=" . $client_precedent . "\">";
			echo "<img src=\"images/prec-icon.png\" title=\"Fiche client precedente\"></a>";
		}
		?></td>

			<td align="right" colspan="2">
			<?
		$requete_id_client_suivant = "Select min(id_client) from Client where id_client>" . $id_client;
		$db->setQuery ( $requete_id_client_suivant );
		$client_suivant = $db->LoadResult ();
		
		if (test_non_vide ( $client_suivant )) {
			echo " <a href=\"$siteURL/index.php/client/creer?id=60&id_client=" . $client_suivant . "\">";
			echo "<img src=\"images/suiv-icon.png\" title=\"Fiche client suivante\"></a>";
		}
		?></td>
		</tr>
		<tr>
			<th>Num client :</th>
			<td>
				<?php
		
		echo $id_client;
		
		if (! test_non_vide ( $_GET ["modif"] )) {
			echo " <a href=\"$siteURL/index.php/client/creer?id=60&modif=1&id_client=" . $id_client . "\">";
			echo "<img src=\"images/modif-client-icon.png\" title=\"Modifier la fiche client\"></a>";
			if (est_min_manager ( $user ) and ! test_non_vide ( $info_ledg )) {
				echo " <a onClick=\"recharger('Voulez-vous supprimer definitivement ce client ?'";
				echo ",'$siteURL/index.php/client/creer?id=60&suppr_client=" . $id_client . "&id_client=" . $id_client . "')\">";
				echo "<img src=\"images/del-client.png\" title=\"Supprimer le client\"></a>";
			}
			if (est_min_manager ( $user )) {
				
				if (recup_accompte_necessaire ( $id_client ) == 0) {
					echo " <a href=\"$siteURL/index.php/client/creer?id=60&accompte_necessaire=1&id_client=" . $id_client . "\">";
					echo "<img src=\"images/VIP-Empty-icon.png\" title=\"Cliquez ici pour autoriser le client &agrave; faire des r&eacute;sa sans acompte\"></a>";
				} else {
					echo " <a href=\"$siteURL/index.php/client/creer?id=60&accompte_necessaire=0&id_client=" . $id_client . "\">";
					echo "<img src=\"images/VIP-icon.png\" title=\"Cliquez ici pour interdire le client &agrave; faire des r&eacute;sa sans acompte\"></a>";
				}
			}
			if (est_min_agent ( $user )) {
				if (recup_1_element ( "police", "Client", "id_client", $id_client ) == 0) {
					echo " <a href=\"$siteURL/index.php/client/creer?id=60&police=1&id_client=" . $id_client . "\">";
					echo "<img src=\"images/no-police-icon.png\" title=\"Cliquez ici pour indiquer que ce client est policier\"></a>";
				} else {
					echo " <a href=\"$siteURL/index.php/client/creer?id=60&police=0&id_client=" . $id_client . "\">";
					echo "<img src=\"images/police-icon.png\" title=\"Cliquez ici pour indiquer que ce client n'est pas policier\"></a>";
				}
			}
			
			if (test_non_vide ( $info_ledg )) {
				if (strcmp ( substr ( $info_ledg, 0, 9 ), "capitaine" ) == 0)
					echo "<img src=\"images/capitaine-icon.png\" title=\"" . $info_ledg . "\">";
				else
					echo "<img src=\"images/joueur-icon.png\" title=\"" . $info_ledg . "\">";
			}
		}
		?>
			</td>
			<?
		if (test_non_vide ( $info_ledg ))
			echo "<td rowspan=17 valign=top width=\"100\"><img src=\"http://footinfive.com//LEDG/media/bearleague/" . photo_user ( $id_user ) . "\" width=\"100\" height=\"125\"></td>";
		?>
		</tr><?
	}
	?>
		<tr>
			<th>Type client :</th>
			<td>
			<?
	if (isset ( $_GET ["modif"] ))
		menu_deroulant ( "Type_Regroupement", $id_type_regroupement, "enregistrer()" );
	else
		echo recup_1_element ( "nom", "Type_Regroupement", "id", $id_type_regroupement );
	?>
			</td>
		</tr>

		<tr>
			<th>Abonn&eacute; newsletter :</th>
			<td>
			<?
	$disabled = "disabled";
	if (isset ( $_GET ["modif"] ) && est_min_manager ( $user ))
		$disabled = "";
	$checked = $newsletter != 0 ? "checked" : "";
	?>
	<INPUT type="checkbox" name="newsletter" <?= $disabled." ".$checked?>
				value=1>
			</td>
		</tr>

		<tr>
			<th>Tarification sp&eacute;cifique :</th>
			<td>
			<?
	
	if (isset ( $_GET ["modif"] )) {
		if (est_min_manager ( $user )) {
			?>
			<select name='gts_id'>
			<?php
			if ($cts != null) {
				echo "<option value='-1'>Aucune</option>";
			} else {
				echo "<option value='-1' selected>Aucune</option>";
			}
			$gts_selected = '';
			foreach ( $allGts as $gts ) {
				if ($cts != null && $cts->Getgtsid () == $gts->Getid ())
					$gts_selected = ' selected';
				echo "<option value='" . $gts->Getid () . "'" . $gts_selected . ">" . $gts->Getnom () . "</option>";
			}
			
			?>
			
			</select>
		<?php
		} else {
			if ($cts != null) {
				echo "<input type='hidden' name='gts_id' value ='" . $gts->Getid () . "'>";
				echo $gts->Getnom ();
			} else {
				echo "<input type='hidden' name='gts_id' value ='-1'>";
				echo "Aucune";
			}
			?>
			
		<?php
		}
	} else {
		if ($cts != null && $cts->Getgtsid () != - 1) {
			echo $gts->Getnom ();
		} else
			echo "Aucune";
	}
	?>
			</td>
		</tr>
<?
	if ($id_type_regroupement > 0) {
		?>
		<tr>
			<th>Nom <? echo recup_1_element("nom","Type_Regroupement","id",$id_type_regroupement);?></th>
			<td><?
		
		if (isset ( $_GET ["modif"] ))
			echo "<input type=\"text\" name=\"nom_entite\" id=\"nom_entite\" size=\"40\" " . " value=\"" . $nom_entite . "\"  maxlength=\"50\" />";
		else
			echo $nom_entite;
		?></td>
		</tr>
		<?if ($id_type_regroupement==1 ){?>
		<tr>
			<th>Nom du service</th>
			<td><?
			
			if (isset ( $_GET ["modif"] ))
				echo "<input type=\"text\" name=\"nom_service\" id=\"nom_service\" size=\"40\" " . " value=\"" . $nom_service . "\"  maxlength=\"50\" />";
			else
				echo $nom_service;
			?></td>
		</tr>
		<?
		}
		if ($id_type_regroupement == 3 or $id_type_regroupement == 2) {
			?>
		<tr>
			<th>SIRET</th>
			<td><?
			
			if (isset ( $_GET ["modif"] ))
				echo "<input type=\"text\" name=\"Siret\" id=\"Siret\" size=\"40\" " . " value=\"" . $Siret . "\"  maxlength=\"14\" />";
			else
				echo $Siret;
			?></td>
		</tr>
		<?
		}
		if ($id_type_regroupement == 3) {
			?>
		<tr>
			<th>Raison sociale</th>
			<td><?
			
			if (isset ( $_GET ["modif"] ))
				echo "<input type=\"text\" name=\"Raison_Sociale\" id=\"Raison_Sociale\" size=\"40\" " . " value=\"" . $Raison_Sociale . "\"  maxlength=\"30\" />";
			else
				echo $Raison_Sociale;
			?></td>
		</tr>

		<tr>
			<th>Nombre salari&eacute;s</th>
			<td><?
			
			if (isset ( $_GET ["modif"] ))
				echo "<input type=\"number\" name=\"Effectif\" id=\"Effectif\" size=\"40\" " . " value=\"" . $Effectif . "\"  maxlength=\"5\" />";
			else
				echo $Effectif;
			?></td>
		</tr>
		<?}?>
		<tr>
			<th>Adresse facturation</th>
			<td><?
		
		if (isset ( $_GET ["modif"] ))
			echo "<input type=\"text\" name=\"Adresse_facturation\" id=\"Adresse_facturation\" size=\"40\" " . " value=\"" . $Adresse_facturation . "\"  maxlength=\"50\" />";
		else
			echo $Adresse_facturation;
		?></td>
		</tr>
		<tr>
			<th>CP, Ville facturation</th>
			<td><?
		if (isset ( $_GET ["modif"] )) {
			echo "<input type=\"text\" name=\"Code_postal_facturation\" id=\"Code_postal_facturation\" size=\"3\" " . " value=\"" . $Code_postal_facturation . "\"  maxlength=\"5\" />";
			echo "<input type=\"text\" name=\"Ville_facturation\" id=\"Ville_facturation\" size=\"30\" " . " value=\"" . $ville_facturation . "\"  maxlength=\"50\" />";
		} else
			echo $Code_postal_facturation . " " . $ville_facturation;
		?></td>
		</tr>



	<?
	}
	
	// if (test_non_vide($id_type_regroupement) and(( isset($_GET["modif"])) or test_non_vide($id_client))){
	?>
		<!--tr>
			<th>Soci&eacute;t&eacute; <i>(Ce champ va bientot disparaitre)</i> : </th>
			<td>
			<?
	/*
	 * if (isset($_GET["modif"])){?>
	 * <input type="text" name="societe" id="societe" size="40" value="<?php echo $societe;?>" class="inputbox required" maxlength="50" />
	 * <?}
	 * else
	 */
	echo "<input type=\"hidden\"  name=\"societe\" value=\"" . $societe . "\">" . $societe . " &nbsp;&nbsp;";
	?>
			</td>
		</tr-->
		<tr>
			<th>Equipe :</th>
			<td>
			<?
	if (isset ( $_GET ["modif"] )) {
		?>
			<input type="text" name="equipe" id="equipe" size="40"
				value="<?php echo $equipe;?>" class="inputbox required"
				maxlength="50" />
			<?
	} else
		echo "<input type=\"hidden\"  name=\"equipe\" value=\"" . $equipe . "\">" . $equipe;
	?>
			</td>
		</tr>

		<tr>
			<th>Nom :</th>
			<td>
			<?
	
	echo "<input name=\"id_client\" type=\"hidden\"  value=\"" . $id_client . "\">";
	echo "<input name=\"id_user\" type=\"hidden\"  value=\"" . $id_user . "\">";
	
	if (isset ( $_GET ["modif"] )) {
		?>	
			<input type="text" name="nom" id="nom" size="40"
				value="<?php echo $nom;?>" class="inputbox required" maxlength="50" /> *
			<?
	} else
		echo $nom;
	?>
			</td>
		</tr>
		<tr>
			<th>Pr&eacute;nom :</th>
			<td>
			<?
	if (isset ( $_GET ["modif"] ) and ! test_non_vide ( $info_ledg )) {
		?>
			<input type="text" name="prenom" id="prenom" size="40"
				value="<?php echo $prenom;?>" class="inputbox required"
				maxlength="50" /> *
			<?
	} else
		echo "<input type=\"hidden\"  name=\"prenom\" value=" . $prenom . ">" . $prenom;
	?>
			</td>
		</tr>
		<?
	for($i = 1; $i < 5; $i ++) {
		?>
		<tr>
			<th>Tel mobile <?echo $i;?> : </th>
			<td>
			<?
		if (isset ( $_GET ["modif"] )) {
			?>
			<input type="text" name="telmob<?echo $i;?>" id="telmob<?echo $i;?>"
				size="40" value="<?php echo $telmob[$i];?>"
				class="inputbox required" maxlength="10" /> 
			<?
			if ($i == 1)
				echo "*";
		} else
			echo $telmob [$i];
		?>
			</td>
		</tr>
		<?}?>
		<tr>
			<th>Tel fixe :</th>
			<td>
			<?
	if (isset ( $_GET ["modif"] )) {
		?>
			<input type="text" name="telfixe" id="telfixe" size="40"
				value="<?php echo $telfixe;?>" class="inputbox required"
				maxlength="10" />
			<?
	} else
		echo $telfixe;
	?>
			</td>
		</tr>
		<tr>
			<th>Email :</th>
			<td>
			<?
	if (isset ( $_GET ["modif"] ) and ! test_non_vide ( $info_ledg ) and ((est_min_agent ( $user )) or ! test_non_vide ( $user->id ))) {
		?>
			<input type="text" name="courriel" size="40" maxlength="100"
				value="<? echo $courriel;?>"> *
			<?
	} else
		echo "<input type=\"hidden\"  name=\"courriel\" value=" . $courriel . ">" . $courriel;
	?>
			</td>
		</tr>
		<tr>
			<th>Adresse :</th>
			<td><?
	if (isset ( $_GET ["modif"] )) {
		?>
			<input type="text" name="Adresse" id="Adresse" size="40"
				value="<?php echo $Adresse;?>" class="inputbox required"
				maxlength="100" />
			<?
	} else
		echo $Adresse;
	?>
			</td>
		</tr>
		<?
	input_cp_ville ( $code_insee, $cp, $ville, $nbre_cp, $nbre_villes, $tab_villes, $tab_cp, $_GET ["modif"] );
	?>
		<tr>
			<th>Date naissance :</th>
			<td>
			<?
	if (isset ( $_GET ["modif"] )) {
		?>
			<input type="text" name="date_nais" size="40" maxlength="100"
				value="<?php echo $date_nais;?>"> jj/mm/aaaa
			<?
	} else
		echo $date_nais;
	?>
			</td>
		</tr>
<?if (est_min_agent($user)){?>
		<tr>
			<th>Commentaire <?
		if (test_non_vide ( $id_client ))
			echo " <a href=\"$siteURL/index.php/component/content/article?id=75&art=57&id_client=" . $id_client . "\">" . "<img src=\"images/Comment-icon.png\" title=\"hsitorique du commentaire\"></a>";
		?></th>
			<td colspan="5">
				<?
		if (isset ( $_GET ["modif"] )) {
			?>
			<textarea rows="4" cols="100" name="commentaire"><?	echo $commentaire;	?></textarea>
			<?
		} else
			echo $commentaire;
		?>
			</td>
		</tr>
<?}?>

		<tr>
			<td colspan="3" align="right" height="40">
			<?
	if (! isset ( $_GET ["modif"] )) {
		
		echo "<i>Fiche client modifi&eacute;e le " . date_longue ( $recup_client->date_modif ) . " &agrave; " . $recup_client->heure_modif . " par " . $recup_client->name_user_modif;
		if (est_min_agent ( $recup_client->group_user_modif ))
			echo " (FIF)";
		echo "</i>";
	} else
		echo "<center><input type=\"checkbox\" name=\"condition\" value=\"1\"/> confirmer les modifications";
	?>
			
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center" height="40">
			<?
	if (isset ( $_GET ["modif"] )) {
		?>
				<input name="valide" type="button" value="Enregistrer"
				onclick="enregistrer()">
			<?
	}
	?>
			
			</td>
		</tr>
	</table>

</form>


<?
	// if (!test_non_vide($_GET["modif"])){
	if (test_non_vide ( $_GET ["hist"] )) {
		$requete_liste_hist_client = "SELECT (select name from #__users where id=`id_user_modif`) as user_modif, ";
		$requete_liste_hist_client .= " (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=`id_user_modif`) as gid_user_modif,";
		$requete_liste_hist_client .= " (select email from #__users where id=`id_user`) as email ,  v.nom_maj_ville, v.code_postal, HC.* ";
		$requete_liste_hist_client .= " FROM `Hist_Client` as HC LEFT JOIN Ville as v  on HC.code_insee=v.code_insee ";
		$requete_liste_hist_client .= " where `id_client`=" . $id_client . " order by `date_modif` desc, `heure_modif` desc";
		// echo $requete_liste_hist_client;
		
		$db->setQuery ( $requete_liste_hist_client );
		$resultat_liste_hist_client = $db->loadObjectList ();
		
		if ($resultat_liste_hist_client) {
			echo "<hr><h2><a name=\"signet\"></a>Historique</h2><hr><table class=\"zebra\"><tr>";
			echo "<th>Modifi&eacute; par</th><th>Date modif.</th><th>Heure modif.</th>";
			echo "<th>Societe<br>Equipe</th><th>Nom<br>Prenom</th><th>Email</th><th>Tel</th>";
			echo "<th>Adresse</th><th>Date de nais.</th></tr>";
			
			foreach ( $resultat_liste_hist_client as $liste_hist_client ) {
				
				echo "<tr>";
				echo "<td nowrap>";
				echo $liste_hist_client->user_modif;
				if (est_min_agent ( $liste_hist_client->gid_user_modif ))
					echo " (FIF)";
				echo "</td><td>";
				echo date_longue ( $liste_hist_client->date_modif ) . "</td><td>";
				echo $liste_hist_client->heure_modif . "</td><td>";
				echo "Soc:" . $liste_hist_client->societe;
				echo "<br>";
				echo "Eq:" . $liste_hist_client->equipe;
				echo "</td><td>";
				echo $liste_hist_client->nom;
				echo "<br>";
				echo $liste_hist_client->prenom;
				echo "</td><td>";
				echo $liste_hist_client->email;
				echo "</td><td>";
				if (test_non_vide ( $liste_hist_client->mobile1 ))
					echo "M1:" . $liste_hist_client->mobile1 . "<br>";
				if (test_non_vide ( $liste_hist_client->mobile2 ))
					echo "M2:" . $liste_hist_client->mobile2 . "<br>";
				if (test_non_vide ( $liste_hist_client->mobile3 ))
					echo "M3:" . $liste_hist_client->mobile3 . "<br>";
				if (test_non_vide ( $liste_hist_client->mobile4 ))
					echo "M4:" . $liste_hist_client->mobile4 . "<br>";
				if (test_non_vide ( $liste_hist_client->fixe ))
					echo "F:" . $liste_hist_client->fixe;
				echo "</td><td>";
				echo $liste_hist_client->adresse;
				echo "<br>" . $liste_hist_client->code_postal . " " . $liste_hist_client->nom_maj_ville;
				echo "</td><td>";
				if ($liste_hist_client->date_naissance != "0000-00-00")
					echo inverser_date ( $liste_hist_client->date_naissance );
				echo "</td></tr>";
			}
			echo "</table>";
		}
		// }
	} else
		echo "<a href=\"" . $_SERVER ['REQUEST_URI'] . "&hist=1#signet\" />Afficher l'historique</a>";
}

function Update_newsletter_fromMailchimp($courriel, $newsletter, $mailchimpAPIKey, $mailchimpURL) {
	$email_md5 = md5 ( $courriel );
	$requestURL = $mailchimpURL . $email_md5;
	$auth = base64_encode ( 'user:' . $mailchimpAPIKey );
	
	$data = array (
			'apikey' => $mailchimpAPIKey,
			'email_address' => $courriel 
	);
	$json_data = json_encode ( $data );
	
	$ch = curl_init ( $requestURL );
	curl_setopt ( $ch, CURLOPT_USERPWD, 'user:' . $mailchimpAPIKey );
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, [ 
			'Content-Type: application/json' 
	] );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $json_data );
	$result = curl_exec ( $ch );
	$httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	curl_close ( $ch );
	
	$json = json_decode ( $result );
	$news_data = $json->{'status'};
	$newsletter = $news_data == 'subscribed' ? 1 : 0;
	return $newsletter;
}

function syncMailchimp($mailchimpAPIKey, $nom_user, $prenom_user, $email_user, $is_subscribed, $list_url, $type_user) {
	$email_md5 = md5 ( $email_user );
	$updateURL = $list_url . $email_md5;
	
	if ($is_subscribed)
		$status = 'subscribed';
	else
		$status = 'unsubscribed';
	
	$merge_fields = array (
			'FNAME' => $prenom_user,
			'LNAME' => $nom_user 
	);
	
	$interest = array ();
	switch ($type_user) {
		case 0 :
			$interest ['bc260d6cbe'] = true;
			break;
		case 1 :
			$interest ['2220d3de32'] = true;
			break;
		case 2 :
			$interest ['2b60d8807d'] = true;
			break;
		case 3 :
			$interest ['63f7bf9cc3'] = true;
			break;
		case 10000 :
			$interest ['2e9911e64c'] = true;
			break;
		case 10001 :
			$interest ['6cbba2117b'] = true;
			break;
		default :
			;
			break;
	}
	$data = array (
			'email_address' => $email_user,
			'status' => $status,
			'interests' => $interest,
			'merge_fields' => $merge_fields 
	);
	
	$data_json = json_encode ( $data );
	
	// Update first
	$ch = curl_init ( $updateURL );
	curl_setopt ( $ch, CURLOPT_USERPWD, 'user:' . $mailchimpAPIKey );
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, [ 
			'Content-Type: application/json' 
	] );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_json );
	$result = curl_exec ( $ch );
	$httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	curl_close ( $ch );
	
	if ($httpCode == 200)
		return $httpCode;
		
		// ajout si update en échec
	$ch = curl_init ( $list_url );
	curl_setopt ( $ch, CURLOPT_USERPWD, 'user:' . $mailchimpAPIKey );
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, [ 
			'Content-Type: application/json' 
	] );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_json );
	$result = curl_exec ( $ch );
	$httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	curl_close ( $ch );
	
	return $httpCode;
}

?>