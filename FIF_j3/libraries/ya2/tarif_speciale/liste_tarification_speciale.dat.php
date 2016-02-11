<?php
define ( '_JEXEC',1 );
define('JPATH_BASE',realpath(dirname ( __FILE__ )."/../../../") );
require(dirname(dirname(dirname(dirname ( __FILE__ )))) . DIRECTORY_SEPARATOR . "/includes/defines.php");
require(dirname(dirname(dirname(dirname ( __FILE__ )))) . DIRECTORY_SEPARATOR . "/includes/framework.php");
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "clienttarificationspeciale_controller.class.php");
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "groupetarificationspeciale_controller.class.php");

$groupeController = new GroupeTarificationSpecialeController ();
$liste_gts = $groupeController->GetAllGroupeTarificationSpeciale();


header ( "Content-type: application/json" );
$jsonData = array (
		'page' => $page,
		'total' => $total,
		'rows' => array ()
);
$gts_array = array();
foreach ($liste_gts as $gts) {
	$jsonData ['rows'] []  = $groupeController->GetGroupeTarificationSpecialeAsArray($gts);
}

echo json_encode($jsonData)

?>

