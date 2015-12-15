<?

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {
	
?>
<script type="text/javascript">
	
	function enregistrer() {
		if (confirm('Vous allez envoyer cet email a tous vos clients,\n confirmez-vous cette action ?'))
			document.envoyer_email.submit()
	}
</script>

<?

if (test_non_vide($_GET["arreter"]) ){

	$requete_maj_arreter_publipostage="UPDATE `Publipostage` SET `actif`=0 WHERE `id_pub`=".$_GET["arreter"];
	//echo $requete_maj_arreter_publipostage;
	$db->setQuery($requete_maj_arreter_publipostage);
	$db->query();
}

if (test_non_vide($_GET["demarrer"]) ){

	$requete_maj_demarrer_publipostage="UPDATE `Publipostage` SET `actif`=1 WHERE `id_pub`=".$_GET["demarrer"];
	//echo $requete_maj_demarrer_publipostage;
	$db->setQuery($requete_maj_demarrer_publipostage);
	$db->query();
}

if (test_non_vide($_POST["corps"]))
	$corps=$_POST["corps"];

if (is_array($_FILES['doc'])){
	$uploaddir = '/var/www/vhosts/footinfive.com/httpdocs/FIF/news/';
	
	foreach ($_FILES["doc"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["doc"]["tmp_name"][$key];
		$name = $_FILES["doc"]["name"][$key];
		if (!nom_fichier_existe($name)){
			if (!is_file($uploaddir."$name"))
                            move_uploaded_file($tmp_name, $uploaddir."$name");
		}
                else echo "<font color=red>Ce nom de fichier existe deja...</font><br><br>";
	    }
	}
	$corps="<a href='http://footinfive.com/FIF/news/".$name."' >"
		."<img src='http://footinfive.com/FIF/news/".$name."' "
		." alt='cliquez-ici si vous ne parvenez pas &agrave; lire ce message' title='cliquez-ici pour acceder au site' /></a>";
}

if (test_non_vide($_POST["objet"]) and test_non_vide($corps) and test_non_vide($_POST["Saison"])){

	$requete_insert_publipostage="INSERT INTO `Publipostage`( `objet`, `corps`,id_saison, `date_creation`, `heure_creation`, `id_user`, `actif`)"
		." VALUES (\"".$_POST["objet"]."\",\"".$corps."\",".$_POST["Saison"].",\"".date("Y-m-d")."\",\"".date("H:i")."\",".$user->id.",0)";

	
	//echo $requete_insert_publipostage;
	$db->setQuery($requete_insert_publipostage);
	$db->query();
	$id_pub_new=$db->insertid();
	if (test_non_vide($id_pub_new)){
		$max_tab_type_regroupement=max_tab("Type_Regroupement");
		if ($max_tab_type_regroupement>9999){
			sendMail(1,"alerte publipostage","L id de Type_Regroupement a depass&eacute; 9999...");
			exit();			
		}
		for($i=0;$i<=$max_tab_type_regroupement;$i++){
			if (test_non_vide($_POST["Type_Regroupement_$i"])){
				$requete_insert_publipostage_Type_Regroupement="INSERT INTO `Publipostage_type_destinataires`(`id_pub`, `id_type_regroupement`) "
					." VALUES (".$id_pub_new.",".$i.")";
				//echo $requete_insert_publipostage_Type_Regroupement;
				$db->setQuery($requete_insert_publipostage_Type_Regroupement);
				$db->query();
			}
		}
		
	}
	
	sendMail(1,$_POST["objet"],$corps);
	sendMail(267,$_POST["objet"],$corps);
}




if (test_non_vide($_GET["new"])){
?>

<form name="envoyer_email"  enctype="multipart/form-data" class="submission box" action="<?php echo JRoute::_( 'index.php/component/content/article?id=65'); ?>" method="post"  >
		<?
			echo "<input name=\"limit\" type=\"hidden\"  value=\"".$limit."\">";
		?>
		<table class="zebra" border="0"  >
		<tr>
			<th>OBJET</th>
			<td align="left"><input name="objet" type="text" value="<? echo $_POST["objet"];?>" >&nbsp;&nbsp;&nbsp;
				<?
				liste_check_box("Type_Regroupement","","","","&nbsp;&nbsp;");
				?>
				
			</td>
			<td align="left">Saison 
				<?
				menu_deroulant_simple("Saison",$_POST["Saison"],"","","");
				?>
				
			</td>
		</tr>
		<tr>
		<? if (test_non_vide($_GET["just_img"])){
			echo "<th>Image</th><td><input type=\"file\" name=\"doc[]\"  accept=\"image/jpg,image/jpeg\"  /> "
				."<br>Le nom du fichier est important car il sera le lien dans le mail \"http://footinfive.com/FIF/news/nom_du_fichier.jpg\""
				."<br>Il faut un nom de fichier sans accent,sans espace et sans caracteres speciaux &agrave; part \"_\""
				."<br>Le nom du fichier peut contenir des lettres en minuscules et des chiffres.</td>";
		}
		else {
		?>
			<th>Corps</th>
			<td align="left" colspan="2">
			<textarea rows="30" cols="100" name="corps"><? echo $_POST["corps"];?></textarea></td>
		<?
		}?>
		</tr>
		</table>
<input name="valide" type="button" value="Enregistrer ce mail et recevoir un exemplaire avant de l'envoyer" onclick="enregistrer()">
</form>
<?
}
else {
	menu_acces_rapide($_GET["id_client"],"Publipostage");
	
	$requete_recup_publipostage="SELECT p.*,  (select name from #__users where id=p.id_user) as nom_user  FROM  `Publipostage` as p ";
	
	//echo $requete_recup_publipostage;
	$db->setQuery($requete_recup_publipostage);
	$db->query();
	$resultat_recup_publipostage = $db->loadObjectList();

	echo "<table class=\"zebra\"><tr><th>Fait par</th><th>Date cr&eacute;ation</th><th>Objet</th><th>Destinataires</th><th>Mails envoy&eacute;s</th><th>Etat</th><tr>";
	foreach($resultat_recup_publipostage as $recup_publipostage) {
		$requete_nbre_mails_envoyes="SELECT count(id_client) as mails_envoyes  FROM  `Publipostage_send` as p WHERE  p.id_pub=".$recup_publipostage->id_pub;
	
		//echo $requete_nbre_mails_envoyes;
		$db->setQuery($requete_nbre_mails_envoyes);
		$db->query();
		$resultat_nbre_mails_envoyese = $db->loadResult();
		
		echo "<tr><td>".$recup_publipostage->nom_user."</td><td>".date_longue($recup_publipostage->date_creation)." &agrave; ".$recup_publipostage->heure_creation."</td>"
			."<td><a href=\"index.php/component/content/article?id=65&id_pub=".$recup_publipostage->id_pub."\">".$recup_publipostage->objet."</a></td>"
			."<td>".recup_dest_publipostage($recup_publipostage->id_pub);	
		echo "</td><td>".$resultat_nbre_mails_envoyese."</td><td><img src=\"images/";
			
		 switch ($recup_publipostage->actif){
			case 0	: echo  "publipostage_non_demarrer.png\" title=\"Le publipostage n'est pas lanc&eacute;";break;
				
			case 1	: echo  "publipostage_en_cours.png\"  title=\"Le publipostage est en cours d'execution";break;
			
			case 2	: echo  "publipostage_terminer.png\"  title=\"Le publipostage est termin&eacute;";break;
				
			defaut	: echo  "publipostage_probleme.png\"  title=\"Le publipostage rencontre un probleme";break;
		 }
		 
		 echo "\" /></td></tr>";
		 if (test_non_vide($_GET["id_pub"]) and $_GET["id_pub"]==$recup_publipostage->id_pub){
			echo "<tr><td colspan=\"5\" bgcolor=\"#FED856\">".str_replace("\n", "<br>",$recup_publipostage->corps)."</td><td>";
			if ($recup_publipostage->actif==0)
				echo "<a href=\"index.php/component/content/article?id=65&demarrer=".$recup_publipostage->id_pub."\" >"
					."<img src=\"images/demarrer_publipostage.png\" title=\"Demarrer ce publipostage\"></a>";
			else {
				if ($recup_publipostage->actif==1)
					echo "<a href=\"index.php/component/content/article?id=65&arreter=".$recup_publipostage->id_pub."\" >"
						."<img src=\"images/suspendre_publipostage.png\" title=\"Suspendre ce publipostage\"></a>";
			}
			echo "</td></tr>";
		 }
	}
	echo "</table><br><br><a href=\"index.php/component/content/article?id=65&new=1\" >"
		."<img src=\"images/nouveau_publipostage.png\" title=\"Nouveau publipostage...\"></a>"
		."<a href=\"index.php/component/content/article?id=65&new=1&just_img=1\" >"
		."<img src=\"images/nouveau_publipostage_image.png\" title=\"Nouveau publipostage en image...\"></a>";
		
	
}

}


?>