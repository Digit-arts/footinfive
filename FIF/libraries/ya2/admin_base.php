<?php
require_once (dirname ( __FILE__ ).'/fonctions_module_reservation.php');
require_once (dirname ( __FILE__ ).'/fonctions_gestion_user.php');


$user = JFactory::getUser ();
$db = JFactory::getDBO ();
$config = JFactory::getConfig ();

$etat_actuel_acces_apllication = acces_application ();

if (($etat_actuel_acces_apllication == 1 and est_register ( $user )) or ($etat_actuel_acces_apllication == 2 and est_agent ( $user )) or ($etat_actuel_acces_apllication == 3 and est_manager ( $user ))){
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
	exit();
}

?>