<?php
defined('_JEXEC') or die( 'Restricted access' );

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

if (is_array($_FILES['FM'])){
	$uploaddir = '/var/www/vhosts/footinfive.com/httpdocs/LEDG/Feuilles-de-matchs/';
	
	foreach ($_FILES["FM"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["FM"]["tmp_name"][$key];
		$name = $_FILES["FM"]["name"][$key];
		if (!is_file($uploaddir."$name"))
			move_uploaded_file($tmp_name, $uploaddir."$name");
	    }
	}
	header("Location: article/?id=59&ttes=1");
}
menu_acces_rapide($id_client);
?>
<!-- Le type d'encodage des données, enctype, DOIT être spécifié comme ce qui suit -->
<form enctype="multipart/form-data" action="article?id=64" method="post">
	<p>Merci de bien vouloir selectionner les fichiers en respectant le format de fichier suivant : "numero_de_match.pdf" <br><br>
		<? for ($i=0;$i<15;$i++){?>
			<input type="file" name="FM[]" /><br>
		<?}?>
		<center><input type="submit" value="Enregistrer les feuilles scann&eacute;es" /></center>
	</p>
</form>

<?
}
?>
