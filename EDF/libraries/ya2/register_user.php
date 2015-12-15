<?php
require_once ('fonctions_gestion_user.php');
require_once ('fonctions_module_reservation.php');
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
$db = & JFactory::getDBO();
$user =& JFactory::getUser();


$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {
	

if (est_min_agent($user)){
	if (test_non_vide($_POST["id_client"])) $id_client=$_POST["id_client"];
	else $id_client=$_GET["id_client"];
} else $id_client=idclient_du_user();


if (is_array($_FILES['doc'])){
	$uploaddir = '/var/www/vhosts/footinfive.com/httpdocs/EDF/libraries/ya2/Photos/';
	
	foreach ($_FILES["doc"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["doc"]["tmp_name"][$key];
		$name = $_FILES["doc"]["name"][$key];
		
		if (!is_file($uploaddir."$name")){
                            $name=$id_client."-".$name;
                            if (move_uploaded_file($tmp_name, $uploaddir."$name"))
                                ajout_photo_client($name,$id_client);
                }
                else echo "<font color=red>Ce nom de fichier existe deja...</font><br><br>";
	    }
	}
}

if (test_non_vide($_GET["suppr_photo"]))
	supprimer_photo_client($_GET["suppr_photo"]);


if (est_min_agent($user) and test_non_vide($_GET["suppr_client"])) {

	echo "<font color=red>";
	$requete_verif_reglement_client="Select id_reglement from Reglement where id_client=".$_GET["suppr_client"]." and validation_reglement=1";
			//echo "<br>reqsuppr: ".$requete_verif_reglement_client;
			$db->setQuery($requete_verif_reglement_client);	
			$db->query();
			$nbre_resultats_reglement=$db->getNumRows();
			
			if ($nbre_resultats_reglement>0)
				echo "Ce client ne peut pas etre supprim&eacute; car il a pass&eacute; des reglements.";
			else {
				$resultat_recup_id_user=recup_1_element("id_user","Client","id_client",$_GET["suppr_client"]);
				
				supprimer_1_element("Client","id_client",$_GET["suppr_client"]);
				supprimer_1_element("Reglement","id_client",$_GET["suppr_client"]);
				supprimer_1_element("Hist_Client","id_client",$_GET["suppr_client"]);
				supprimer_1_element("Commentaires","id_client",$_GET["suppr_client"]);
				supprimer_1_element("Relation_enfant_contacts","id_client_contact",$_GET["suppr_client"]);
				supprimer_1_element("Relation_enfant_contacts","id_client_enfant ",$_GET["suppr_client"]);
				supprimer_1_element("#__users","id",$resultat_recup_id_user);
				supprimer_1_element("#_user_usergroup_map","user_id",$resultat_recup_id_user);
				
				header("Location: http://www.footinfive.com/EDF/");
				
			}

	echo "</font>";
	
}

if (test_non_vide($user->id))
	menu_acces_rapide($id_client,"Fiche client");

if (isset($_GET["modif"])){
$existe_erreur=0;

if (test_non_vide($_POST["code_insee"]))
	$code_insee=$_POST["code_insee"];

if (test_non_vide($_POST["date_nais"])) {
	if (!test_valid_existence_date($_POST["date_nais"])){
		echo "<font color=red>La date de naissance est incorrecte.<br></font>";
		$existe_erreur++;
	}
	/*if (diff_dates_en_minutes(inverser_date($_POST["date_nais"]))<8409600){ // age min de 16 ans en minutes
		echo "<font color=red>Le client doit avoir plus de 16 ans.<br></font>";
		$existe_erreur++;
	}*/
}
if (!test_non_vide($_POST["nom"]) or !test_non_vide($_POST["prenom"]) or !test_non_vide($_POST["telmob1"]) or !test_non_vide($_POST["courriel"])) {
	echo "<font color=red>Le nom, le prenom, le numero de mobile ainsi que l'email sont obligatoires.<br></font>";
	$existe_erreur++;
}
if (!test_non_vide($_POST["Type_statut"])and (isset($_GET["modif"]) and !test_non_vide($id_client)))  {
	echo "<font color=red>Le statut est obligatoire.<br></font>";
	$existe_erreur++;
}

if (!test_non_vide($_POST["sexe"])and (isset($_GET["modif"]) and !test_non_vide($id_client)))  {
	echo "<font color=red>Le sexe est obligatoire.<br></font>";
	$existe_erreur++;
}

if (test_non_vide($_POST["Type_statut"]) and $_POST["Type_statut"]>1 and !test_non_vide($_POST["id_client_relation"]))  {
	echo "<font color=red>La relation avec un eleve est obligatoire.<br></font>";
	$existe_erreur++;
}
if (test_non_vide($_POST["Type_statut"]) and $_POST["Type_statut"]>1
	and test_non_vide($_POST["id_client_relation"]) and !exist_id_client($_POST["id_client_relation"])) {
	echo "<font color=red>Le num client choisit n'existe pas.<br></font>";
	$existe_erreur++;
}
if (test_non_vide($_POST["Type_statut"]) and $_POST["Type_statut"]>1 and exist_id_client($_POST["id_client_relation"])
	and test_non_vide($_POST["id_client_relation"]) and !exist_id_client_eleve($_POST["id_client_relation"])) {
	echo "<font color=red>Le num client choisit n'est pas celui d'un eleve.<br></font>";
	$existe_erreur++;
}
if (test_non_vide($_POST["nom"]) and test_non_vide($_POST["prenom"]) and !(VerifierNom($_POST["nom"]) and VerifierNom($_POST["prenom"]))){
	echo "<font color=red>Le nom ou le prenom est incorrect.<br></font>";
	$existe_erreur++;
}

for ($i=1;$i<5;$i++){
	if (test_non_vide($_POST["telmob$i"]) and !(VerifierNumMob($_POST["telmob$i"]))){
		echo "<font color=red>Numero de Tel mobile ".$i." incorrect.<br></font>"; 
		$existe_erreur++;
	}
	else {
		$resultat_verif_si_mob_existe=0;
		
		if (test_non_vide($_POST["telmob$i"])) {
			for ($j=1;$j<5;$j++){
				$requete_verif_si_mob_existe="select  ( Trim(\"".Trim($_POST["telmob$i"])."\") in  (select mobile".$j." from Client where mobile".$j."<>\"0600000000\" ";
				if (test_non_vide($id_client)) $requete_verif_si_mob_existe.=" and id_client<>".$id_client."";
				$requete_verif_si_mob_existe.=" ) ) as nbre_occurences";
				// echo $requete_verif_si_mob_existe."<br>";
				$db->setQuery($requete_verif_si_mob_existe);		
				$resultat_verif_si_mob_existe += $db->loadResult();
			}
		}
		if ($resultat_verif_si_mob_existe>0) {
			echo "<font color=red>Num&eacute;ro de mobile d&eacute;j&agrave; attribu&eacute; : ".$_POST["telmob$i"].".<br></font>";
			$existe_erreur++;	
		}
	}
}
if (test_non_vide($_POST["telfixe"]) and !(VerifierNumFixe($_POST["telfixe"]))){
	echo "<font color=red>Numero de Tel fixe incorrect.<br></font>";
	$existe_erreur++;
}


verif_cp_ville(&$code_insee,$_POST["cp"],$_POST["ville"],&$tab_villes,&$tab_cp,&$nbre_villes,&$nbre_cp,&$existe_erreur);

if (test_non_vide($_POST["courriel"]) and !(VerifierAdresseMail($_POST["courriel"]))){ 
	echo "<font color=red>Votre adresse email est incorrecte.<br></font>";
	$existe_erreur++;
}
else {
	if (test_non_vide($_POST["courriel"]) and !test_non_vide($_POST["id_user"]) ) {
		$requete_verif_si_email_existe="SELECT count(id) FROM #__users where email=Trim(\"".Trim($_POST["courriel"])."\") and Trim(\"".Trim($_POST["courriel"])."\")<>\"agent@footinfive.com\";";
		$db->setQuery($requete_verif_si_email_existe);		
		$resultat_verif_si_email_existe = $db->loadResult();
						
		if ($resultat_verif_si_email_existe>0){
			echo "<font color=red>Adresse email d&eacute;j&agrave; utilis&eacute;e.</font>.<br>";
			$existe_erreur++;
		}
	}
}
}
if ($existe_erreur==0 and !test_non_vide($_POST["ajouter_relation"]) and (isset($_GET["modif"]))  and test_non_vide($_POST["condition"])){
	

	if ($code_insee==""){
		if ((test_non_vide($_POST["cp"])) and (test_non_vide($_POST["ville"])) ){

			$le_resultat=recup_insee("",$_POST["cp"],$_POST["ville"]);
			$resultat_recup_insee = $le_resultat->code_insee;
			
			if ($resultat_recup_insee>0) $code_insee=$resultat_recup_insee;
			else {
				echo "<font color=red>Erreur code insee.</font>.<br>";
				break;
			}
		}
	}
	if (test_non_vide($_POST["id_client"]) and $_POST["id_user"]<>0) {
		if (test_non_vide($_POST["courriel"])) 
			maj_user($_POST["id_user"],$_POST["prenom"],$_POST["courriel"]);
		
	}
	if ( ((test_non_vide($_POST["id_client"]) and $_POST["id_user"]==0) or !test_non_vide($_POST["id_client"])) and test_non_vide($_POST["courriel"])) {

		$user_id = ajout_user($_POST["prenom"],$_POST["courriel"],$_POST["password"]);												
		ajout_user_au_groupe($user_id,2);

	}	
	if (test_non_vide($_POST["id_client"])) {

		$requete_recup_old_client="select * from Client WHERE id_client=".$_POST["id_client"];
		$db->setQuery($requete_recup_old_client);
		$recup_old_client = $db->loadObject();
		
			$requete_insert_old_client="INSERT INTO `Hist_Client`( id_client, `id_user`, ";
			$requete_insert_old_client.=" `id_user_modif`,date_modif, heure_modif, `nom`, `prenom`,";
			for ($i=1;$i<5;$i++) 
				$requete_insert_old_client.="`mobile".$i."`,";
			$requete_insert_old_client.=" `fixe`, `code_insee`, `adresse`, `date_naissance` ) ";
			$requete_insert_old_client.=" VALUES ( ".$_POST["id_client"]." ,";
			if (isset($user_id) and $user_id<>"") $requete_insert_old_client.=$user_id." , ";
			else $requete_insert_old_client.=$recup_old_client->id_user." , ";
			$requete_insert_old_client.= $recup_old_client->id_user_modif." ,\"".$recup_old_client->date_modif."\",\"".$recup_old_client->heure_modif."\",";
			$requete_insert_old_client.="\"".$recup_old_client->nom."\",\"".$recup_old_client->prenom."\",";
			$requete_insert_old_client.="\"".$recup_old_client->mobile1."\",";
			$requete_insert_old_client.="\"".$recup_old_client->mobile2."\",";
			$requete_insert_old_client.="\"".$recup_old_client->mobile3."\",";
			$requete_insert_old_client.="\"".$recup_old_client->mobile4."\",";
			$requete_insert_old_client.=" \"".$recup_old_client->fixe."\",\"".$recup_old_client->code_insee."\", ";
			$requete_insert_old_client.=" \"".$recup_old_client->adresse."\",\"".$recup_old_client->date_naissance."\" )";
			//echo "<br>".$requete_insert_old_client;
				
			$db->setQuery($requete_insert_old_client);
			$db->query();
			
		$requete_update_client="UPDATE `Client` SET `id_user_modif`=".$user->id.", date_modif=\"".date("Y")."-".date("m")."-".date("d")."\",";
		if (isset($user_id) and $user_id<>"") $requete_update_client.=" `id_user`=".$user_id.", ";
		$requete_update_client.=" heure_modif=\"".Ajout_zero_si_absent(date("H").":".date("i"))."\", `nom`=\"".tout_majuscule($_POST["nom"])."\",";
		$requete_update_client.=" `prenom`=\"".premiere_lettre_maj($_POST["prenom"])."\",";
		for ($i=1;$i<5;$i++)
			$requete_update_client.=" `mobile".$i."`=\"".$_POST["telmob$i"]."\",";
		$requete_update_client.=" `fixe`=\"".$_POST["telfixe"]."\" ";
		$requete_update_client.=" ,`code_insee`=\"".$code_insee."\", ";
		$requete_update_client.=" `adresse`=\"".$_POST["Adresse"]."\",`date_naissance`=\"".inverser_date($_POST["date_nais"])."\"";

		$requete_update_client.=",sexe=".$_POST["sexe"]." WHERE id_client=".$_POST["id_client"];
		//echo "<br>".$requete_update_client;
			
		$db->setQuery($requete_update_client);
		$res=$db->query();
		
		maj_commentaire("id_client",$_POST["id_client"],$_POST["commentaire"]);
		
		// le client a été modifié avec succes...
		if ($res)
			header("Location: index.php/component/content/article?id=60&id_client=".$_POST["id_client"]."");
		else echo "pb resa bdd update";
	}
		
	if (!test_non_vide($_POST["id_client"])){
		$requete_insert_client="INSERT INTO `Client`(";
		if (isset($user_id) and $user_id<>"") $requete_insert_client.=" `id_user`, ";
		$requete_insert_client.=" `id_user_modif`,date_modif, heure_modif, `nom`, `prenom`, ";
		for ($i=1;$i<5;$i++) 
			$requete_insert_client.="`mobile".$i."`,";
		$requete_insert_client.=" `fixe`, `code_insee`, `adresse`, `date_naissance`, sexe) VALUES (";
		
		if (isset($user_id) and $user_id<>"") $requete_insert_client.=$user_id." , ";
		$requete_insert_client.= $user->id." ,\"".date("Y")."-".date("m")."-".date("d")."\",";
		$requete_insert_client.=" \"".Ajout_zero_si_absent(date("H").":".date("i"))."\",\"".tout_majuscule($_POST["nom"])."\",";
		$requete_insert_client.=" \"".premiere_lettre_maj($_POST["prenom"])."\",";
		for ($i=1;$i<5;$i++)
			$requete_insert_client.= "\"".$_POST["telmob$i"]."\",";
		$requete_insert_client.=" \"".$_POST["telfixe"]."\",\"".$code_insee."\",";
		$requete_insert_client.=" \"".$_POST["Adresse"]."\",\"".inverser_date($_POST["date_nais"])."\",".$_POST["sexe"].")";
		//echo "<br>".$requete_insert_client;
			
		$db->setQuery($requete_insert_client);
		$res=$db->query();
		$id_new_client=$db->insertid();
		maj_commentaire("id_client",$id_new_client,$_POST["commentaire"]);
		if ($res){
			
			if (est_min_agent($user)){
				if (test_non_vide($_POST["id_client_relation"]))
					$id_client_relation=$_POST["id_client_relation"];
				else $id_client_relation=$id_new_client;
				ajouter_relation($id_client_relation,$id_new_client,$_POST["Type_statut"]);
			}
			
			if (est_register($user))
				echo "Pour finaliser votre enregistrement <a href=\"".JRoute::_( 'index.php?option=com_user&view=reset')."\">cliquez-ici pour confirmer que vous &ecirc;tes le propri&eacutetaire de l'adresse email</a>.";
			else header("Location: index.php/component/content/article?id=60&id_client=".$id_new_client."");
		}
		else echo "pb resa bdd insert";
	}
	

	

}
else {
	if (est_min_agent($user) and test_non_vide($_POST["ajouter_relation"])) {
		ajouter_relation($_POST["id_client_relation"],$_POST["id_client"],$_POST["Type_statut"]);
		header("Location: index.php/component/content/article?id=60&id_client=".$_POST["id_client"]."");
	}

	if (test_non_vide($_GET["id_client"]) or (test_non_vide($user->id) and est_register($user) and !test_non_vide($_POST["prenom"]))){
		if (est_min_agent($user)) 
			$compl_criteres=" and c.id_client=".$_GET["id_client"]." ";
		else $compl_criteres=" and c.id_user=".$user->id." ";
		
		$recup_client = recup_recup_client($compl_criteres,$id_client);
							
			$nom=$recup_client->nom_client;
			$prenom=$recup_client->prenom;
			$telmob[1]=$recup_client->mobile1;
			$telmob[2]=$recup_client->mobile2;
			$telmob[3]=$recup_client->mobile3;
			$telmob[4]=$recup_client->mobile4;
			$telfixe=$recup_client->fixe;
			$courriel=$recup_client->courriel;
			$sexe=$recup_client->sexe;
			
			$Adresse=$recup_client->adresse;
			$cp=$recup_client->code_postal;
			$ville=$recup_client->nom_maj_ville;

			if ($recup_client->date_naissance<>"0000-00-00")
				$date_nais=inverser_date($recup_client->date_naissance);
			$id_client=$recup_client->id_client;
			$id_user=$recup_client->id_user;
			if (est_min_agent($user)) {
				$ligne_commentaire=recup_derniere_commentaire("id_client",$recup_client->id_client);
				$commentaire=$ligne_commentaire->Commentaire;
				$Type_statut=recup_id_statut_client($recup_client->id_client);
			}
	}
	else {
		if (test_non_vide($_POST["nom"])) $nom=$_POST["nom"];
		if (test_non_vide($_POST["prenom"])) $prenom=$_POST["prenom"];
		for ($i=1;$i<5;$i++)
			if (test_non_vide($_POST["telmob$i"])) $telmob[$i]=$_POST["telmob$i"];
		if (test_non_vide($_POST["telfixe"])) $telfixe=$_POST["telfixe"];
		if (test_non_vide($_POST["courriel"])) $courriel=$_POST["courriel"];
		if (test_non_vide($_POST["sexe"])) $sexe=$_POST["sexe"];
		if (test_non_vide($_POST["Adresse"])) $Adresse=$_POST["Adresse"];;
		if (test_non_vide($_POST["cp"])) $cp=$_POST["cp"];
		if (test_non_vide($_POST["ville"])) $ville=$_POST["ville"];
		if (test_non_vide($_POST["date_nais"])) $date_nais=$_POST["date_nais"];
		if (test_non_vide($_POST["id_client"])) $id_client=$_POST["id_client"];
		if (test_non_vide($_POST["id_user"])) $id_user=$_POST["id_user"];
		if (est_min_agent($user)) {
			if (test_non_vide($_POST["commentaire"])) $commentaire=$_POST["commentaire"];
			if (test_non_vide($_POST["Type_statut"])) $Type_statut=$_POST["Type_statut"];
			if (test_non_vide($_POST["id_client_relation"])) $id_client_relation=$_POST["id_client_relation"];
		}
	}
if (test_non_vide($_GET["modif"])){
	?>
		
		<form name="register_user" class="submission box" action="<?php echo JRoute::_( 'index.php/component/content/article?id=60&modif=1'); ?>" method="post"  >
		<br>
<?
}
?>		<table class="zebra" border="0"  >
<?

	if (test_non_vide($id_client)) {
		?>
		<tr>
			<td>
			<?
			$requete_id_client_precedent="Select max(id_client) from Client where id_client<".$id_client;
			$db->setQuery($requete_id_client_precedent);	
			$client_precedent=$db->LoadResult();
			if (test_non_vide($client_precedent)){
				echo " <a href=\"index.php/component/content/article?id=60&id_client=".$client_precedent."\">";
				echo "<img src=\"images/prec-icon.png\" title=\"Fiche client precedente\"></a>";
			}
			?></td>
			
			<td align="right" colspan="2">
			<?
			$requete_id_client_suivant="Select min(id_client) from Client where id_client>".$id_client;
			$db->setQuery($requete_id_client_suivant);	
			$client_suivant=$db->LoadResult();
			
			if (test_non_vide($client_suivant)){
				echo " <a href=\"index.php/component/content/article?id=60&id_client=".$client_suivant."\">";
				echo "<img src=\"images/suiv-icon.png\" title=\"Fiche client suivante\"></a>";
			}
			?></td>
		</tr>
		<tr>
			<th>Num client : </th>
			<td>
				<?php echo $id_client;
				
				if (!test_non_vide($_GET["modif"])){ 
					echo " <a href=\"index.php/component/content/article?id=60&modif=1&id_client=".$id_client."\">";
					echo "<img src=\"images/modif-client-icon.png\" title=\"Modifier la fiche client\"></a>";
					if (est_min_manager($user)) {
						echo " <a onClick=\"recharger('Voulez-vous supprimer definitivement ce client ?'";
						echo ",'article?id=60&suppr_client=".$id_client."&id_client=".$id_client."')\">";
						echo "<img src=\"images/del-client.png\" title=\"Supprimer le client\"></a>";	
					}
					if (est_min_agent($user) and test_non_vide($Type_statut) and $Type_statut>1){
						echo " <a href=\"index.php/component/content/article?id=60&ajouter_relation=".$id_client."&id_client=".$id_client."\">";
						echo "<img src=\"images/ajoute-parents-eleve-edf-icon.png\" title=\"ajouter une relation\"  HEIGHT=\"24\" WIDTH=\"24\"  /></a>";
					}
			?>
			</td><td align="right" rowspan="4"  width="100">
			<?
					if (photo_client($id_client)==""){
						?><form name="register" enctype="multipart/form-data" class="submission box" action="<? echo JRoute::_( 'index.php/component/content/article?id=60&id_client='.$id_client.'');?>" method="post"  >
						<?
						echo " <input type=\"file\" name=\"doc[]\" />";
				    
						?><input name="valide" type="submit" value="Ajouter la photo" ><?
						echo "</form>";
					} else {
						echo " <img src=\"libraries/ya2/Photos/".photo_client($id_client)."\" width=75 height=100 >";
						echo " <a title=\"Supprimer la photo\" onclick=\"recharger('Confirmez la suppression de la photo','article?id=60&suppr_photo=".$id_client."&id_client=".$id_client."')\">"
						."<img src=\"images/supprimer.png\" ></a>";
					}
					
				}?>
			</td>
		</tr><?
	}
	?>
		<tr>
			<th>Statut : </th>
			<td  >
			<?
			if (isset($_GET["ajouter_relation"]) or (isset($_GET["modif"]) and !test_non_vide($id_client))){
				if (isset($_GET["ajouter_relation"]))
					$la_fonction="";
				else $la_fonction="enregistrer()";
				menu_deroulant("Type_statut",$Type_statut,$la_fonction);
				echo " * ";
				if (test_non_vide($Type_statut) and $Type_statut>1){
					echo " du eleve <input name=\"id_client_relation\" type=text value=\"".$id_client_relation."\" placeholder=\"num client\"/>*"
						." <a href=\"/EDF/\" target=\"_blank\" />rechercher le num client du eleve ?</a>";
				}
				if (isset($_GET["ajouter_relation"]))
					echo "<input name=\"ajouter_relation\" type=hidden value=\"".$id_client."\"/> "
						."<input name=\"valide\" type=\"button\" value=\"Ajouter\" onclick=\"enregistrer()\">";
					
			}
			else {
				if (test_non_vide($Type_statut) and $Type_statut>1)
					echo "<img src=\"images/parents-eleve-edf-icon.png\" title=\"relation\"  HEIGHT=\"24\" WIDTH=\"24\"  /> | "
						.liste_eleves($id_client)." ";
				else  echo "<img src=\"images/eleve-edf-icon.png\" title=\"eleve\"  HEIGHT=\"24\" WIDTH=\"24\"  /> | "
						.liste_contacts($id_client)." ";
			}
			?>
			</td>
		</tr>
		<tr>
			<th>Nom : </th>
			<td >
			<? echo "<input name=\"id_client\" type=\"hidden\"  value=\"".$id_client."\">";
			echo "<input name=\"id_user\" type=\"hidden\"  value=\"".$id_user."\">";
			
			if (isset($_GET["modif"])){?>	
			<input type="text" name="nom" id="nom" size="40" value="<?php echo $nom;?>" class="inputbox required" maxlength="50" /> *
			<?}
			else echo "<b>".$nom."</b>";?>
			</td>
		</tr>
		<tr>
			<th>Pr&eacute;nom : </th>
			<td>
			<?
			if (isset($_GET["modif"])){?>
			<input type="text" name="prenom" id="prenom" size="40" value="<?php echo $prenom;?>" class="inputbox required" maxlength="50" /> *
			<?}
			else echo "<input type=\"hidden\"  name=\"prenom\" value=".$prenom."><b>".$prenom."</b>";?>
			</td>
		</tr>
		<tr>
			<th>Sexe : </th>
			<td colspan="2">
			<?
			if (isset($_GET["modif"])){
				?>
				<img src="images/m-sexe-icon.png" title="gar&ccedil;on"/>
				<INPUT type="radio" name="sexe" value="1" <? if (test_non_vide($sexe) and $sexe==1) echo "checked"; ?>>
				<INPUT type="radio" name="sexe" value="0" <? if (test_non_vide($sexe) and $sexe==0) echo "checked"; ?>>
				<img src="images/f-sexe-icon.png" title="fille"/>
				<?
			}
			else {
				if (recup_1_element("sexe","Client","id_client",$id_client)==1)
					echo "<img src=\"images/m-sexe-icon.png\" title=\"gar&ccedil;on\"/>";
				else echo "<img src=\"images/f-sexe-icon.png\" title=\"fille\"/>";
			}?>
			</td>
		</tr>
		<?
		for ($i=1;$i<5;$i++){?>
		<tr><th>Tel mobile <?echo $i;?> : </th>
			<td colspan="2">
			<?
			if (isset($_GET["modif"])){?>
			<input type="text" name="telmob<?echo $i;?>" id="telmob<?echo $i;?>" size="40" value="<?php echo $telmob[$i];?>" class="inputbox required" maxlength="10" /> 
			<?
			if ($i==1) echo "*";
			}
			else echo $telmob[$i];?>
			</td>
		</tr>
		<?}?>
		<tr><th>Tel fixe : </th>
			<td colspan="2">
			<?
			if (isset($_GET["modif"])){?>
			<input type="text" name="telfixe" id="telfixe" size="40" value="<?php echo $telfixe;?>" class="inputbox required" maxlength="10" />
			<?}
			else echo $telfixe;?>
			</td>
		</tr>
		<tr>
			<th>Email :	</th>
			<td colspan="2">
			<?
			if (isset($_GET["modif"]) and ((est_min_agent($user)) or !test_non_vide($user->id)) ){?>
			<input type="text"  name="courriel" size="40" maxlength="100" value="<? echo $courriel;?>"> *
			<?}
			else echo "<input type=\"hidden\"  name=\"courriel\" value=".$courriel.">".$courriel;?>
			</td>
		</tr>
		<tr>
			<th>Adresse :	</th>
			<td colspan="2"><?
			if (isset($_GET["modif"])){?>
			<input type="text" name="Adresse" id="Adresse" size="40" value="<?php echo $Adresse;?>" class="inputbox required" maxlength="100" />
			<?}
			else echo $Adresse;?>
			</td>
		</tr>
		<tr>
			<th>Code postal, Ville  :	</th>
			<td colspan="2"><? 
				input_cp_ville ($code_insee,$cp,$ville,$nbre_cp,$nbre_villes,$tab_villes,$tab_cp,$_GET["modif"]);
			?>
			</td>
		</tr>
		<tr>
			<th>Date naissance :	</th>
			<td colspan="2">
			<?
			if (isset($_GET["modif"])){?>
			<input type="text"  name="date_nais" size="40" maxlength="100" value="<?php echo $date_nais;?>"> jj/mm/aaaa
			<?}
			else echo $date_nais;?>
			</td>
		</tr>
<?if (est_min_agent($user)){?>
		<tr>
			<th>Commentaire <?
			if (test_non_vide($id_client))
				echo " <a href=\"index.php/component/content/article?id=75&art=57&id_client=".$id_client."\">"
					."<img src=\"images/Comment-icon.png\" title=\"hsitorique du commentaire\"></a>";
			?></th>
			<td colspan="6">
				<?
			if (isset($_GET["modif"])){?>
			<textarea rows="4" cols="100" name="commentaire"><?	echo $commentaire;	?></textarea>
			<?}
			else echo $commentaire;?>
			</td>
		</tr>
<?}?>

		<tr>
			<td colspan="3" align="right" height="40">
			<?
			if (!isset($_GET["modif"])){
				
				echo "<i>Fiche client modifi&eacute;e le ".date_longue($recup_client->date_modif)." &agrave; ".$recup_client->heure_modif." par ".$recup_client->name_user_modif;
				if (est_min_agent($recup_client->group_user_modif)) echo " (FIF)";
				echo "</i>";
			}
			else echo "<center><input type=\"checkbox\" name=\"condition\" value=\"1\"/> confirmer les modifications";
?>
			
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center" height="40">
			<?
			if (isset($_GET["modif"])){?>
				<input name="valide" type="button" value="Enregistrer" onclick="enregistrer()">
			<?}
		?>
			
			</td>
		</tr>
		</table>
			
		</form>


<?
	//if (!test_non_vide($_GET["modif"])){
	if (test_non_vide($_GET["hist"])){
		$requete_liste_hist_client="SELECT (select name from #__users where id=`id_user_modif`) as user_modif, ";
		$requete_liste_hist_client.=" (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=`id_user_modif`) as gid_user_modif,";
		$requete_liste_hist_client.=" (select email from #__users where id=`id_user`) as email ,  v.nom_maj_ville, v.code_postal, HC.* ";
		$requete_liste_hist_client.=" FROM `Hist_Client` as HC LEFT JOIN Ville as v  on HC.code_insee=v.code_insee ";
		$requete_liste_hist_client.=" where `id_client`=".$id_client." order by `date_modif` desc, `heure_modif` desc";
		//echo $requete_liste_hist_client;

		$db->setQuery($requete_liste_hist_client);	
		$resultat_liste_hist_client= $db->loadObjectList();
		
		if ($resultat_liste_hist_client) {
			echo "<hr><h2><a name=\"signet\"></a>Historique</h2><hr><table class=\"zebra\"><tr>";
			echo "<th>Modifi&eacute; par</th><th>Date modif.</th><th>Heure modif.</th>";
			echo "<th>Nom<br>Prenom</th><th>Email</th><th>Tel</th>";
			echo "<th>Adresse</th><th>Date de nais.</th></tr>";
			
			foreach($resultat_liste_hist_client as $liste_hist_client){

				echo "<tr>";
				echo "<td nowrap>";
				echo $liste_hist_client->user_modif;
				if (est_min_agent($liste_hist_client->gid_user_modif))
					echo " (FIF)";
				echo "</td><td>";
				echo date_longue($liste_hist_client->date_modif)."</td><td>";
				echo $liste_hist_client->heure_modif."</td><td>";
				echo $liste_hist_client->nom;
				echo "<br>";
				echo $liste_hist_client->prenom;
				echo "</td><td>";
				echo $liste_hist_client->email;
				echo "</td><td>";
				if (test_non_vide($liste_hist_client->mobile1))
					echo "M1:".$liste_hist_client->mobile1."<br>";
				if (test_non_vide($liste_hist_client->mobile2))
					echo "M2:".$liste_hist_client->mobile2."<br>";
				if (test_non_vide($liste_hist_client->mobile3))
					echo "M3:".$liste_hist_client->mobile3."<br>";
				if (test_non_vide($liste_hist_client->mobile4))
					echo "M4:".$liste_hist_client->mobile4."<br>";
				if (test_non_vide($liste_hist_client->fixe))
					echo "F:".$liste_hist_client->fixe;
				echo "</td><td>";
				echo $liste_hist_client->adresse;
				echo "<br>".$liste_hist_client->code_postal." ".$liste_hist_client->nom_maj_ville;
				echo "</td><td>";
				if ($liste_hist_client->date_naissance<>"0000-00-00") 
						echo inverser_date($liste_hist_client->date_naissance);
				echo "</td></tr>";
			}
			echo "</table>";
		}
	//}
	}
	else echo "<a href=\"".$_SERVER['REQUEST_URI']."&hist=1#signet\" />Afficher l'historique</a>";	


}
}?>