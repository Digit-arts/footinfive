<?php
defined('_JEXEC') or die( 'Restricted access' );

$user =& JFactory::getUser();
$db = & JFactory::getDBO();


if (is_array($_FILES['FM'])){
	$uploaddir = '/home/footinfrza/ledg/Feuilles-de-matchs/';
	
	foreach ($_FILES["FM"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["FM"]["tmp_name"][$key];
		$name = $_FILES["FM"]["name"][$key];
		if (!is_file($uploaddir."$name"))
			move_uploaded_file($tmp_name, $uploaddir."$name");
	    }
	}
}

?>
<!-- Le type d'encodage des données, enctype, DOIT être spécifié comme ce qui suit -->
<form enctype="multipart/form-data" action="scans-fm" method="post">
	<p>FM:
		<? for ($i=0;$i<15;$i++){?>
			<input type="file" name="FM[]" /><br>
		<?}?>
		<input type="submit" value="Send" />
	</p>
</form>
