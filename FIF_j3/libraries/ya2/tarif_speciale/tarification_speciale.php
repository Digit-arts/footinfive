<?php
require_once (dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . "admin_base.php");
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "clienttarificationspeciale_controller.class.php");
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "groupetarificationspeciale_controller.class.php");
$siteURL = $config->get ( 'site_url' );
?>
<link rel="stylesheet" href="libraries/ya2/styles/style.css"
	type="text/css" />
<script type="text/javascript" src="libraries/jquery/jquery.last.min.js"></script>
<script src="libraries/parsley.js/dist/parsley.min.js"></script>

<?php

menu_acces_rapide ( - 1, "Tarification S&eacute;pciale" );

$MAJ = false;
$gts = null;
$title = 'Cr&eacute;er un groupe de tarification sp&eacute;ciale';
$gts_nom = '';
$gts_tarif_hc = '';
$gts_description = '';
$gts_id = '';
$gts_creation = '';
if (isset ( $_GET ['groupeID'] ) and is_numeric ( $_GET ['groupeID'] )) {
	$groupeID = $_GET ['groupeID'];
	$groupeController = new GroupeTarificationSpecialeController ();
	
	$gts = $groupeController->GetGroupeTarificationSpecialeById ( $groupeID );
	$MAJ = true;
	$title = 'Modifier un groupe de tarification sp&eacute;ciale';
	$gts_nom = $gts->Getnom ();
	$gts_tarif_hc = $gts->Gettarifhc ();
	$gts_description = $gts->Getdescription ();
	$gts_id = "<input type='hidden' name='gts_id' value='".$gts->Getid()."'>";
	$gts_creation = "<input type='hidden' name='gts_date_creation' value='".$gts->Getdatecreation()."'>";
}

if (isset ( $_GET ['groupeDelID'] ) and is_numeric ( $_GET ['groupeDelID'] )) {
	$groupeID = $_GET ['groupeDelID'];
	$groupeController = new GroupeTarificationSpecialeController ();
	
}

?>

<script type="text/javascript">
//on form submit
$("#formulaire").submit(function(event) {
    // validate form with parsley.
    $(this).parsley().validate();

    // if this form is valid
    if ($(this).parsley().isValid()) {
    	event.preventDefault();
    }
});
</script>
<form id="formulaire" name="formulaire" class="submission box"
	action="<?php echo $siteURL."/index.php/tarifs/edit-tarif-special";?>"
	method="post" data-parsley-validate>

	<div class='smallPanel'>
		<div class='panelHead'><?php echo $title; ?></div>
		<div class='panelRow'>
			<span class='panelTitle'>Nom du groupe :</span> <span
				class='panelValue'><input required type="text" name="gts_nom"
				data-parsley-required-message="Veuillez saisir le nom du groupe" value="<?php echo $gts_nom;?>"></span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Tarif heure pleine :</span> <span
				class='panelValue'><input required type="number" name="gts_tarif_hc"
				data-parsley-required-message="Veuillez saisir un tarif valide (num&eacute;rique)" value="<?php echo $gts_tarif_hc;?>">
			</span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Description :</span> <span
				class='panelValue'> <textarea required
					data-parsley-required-message="La description est obligatoire"
					rows="10" cols="35" name="gts_description"
					data-parsley-trigger="keyup" data-parsley-minlength="15"
					data-parsley-minlength-message="Veuillez saisir 15 caract&egrave;res minimum"><?php echo $gts_description;?></textarea>
			</span>
		</div>
		<?php echo $gts_id.$gts_creation;?>	
		<div class='panelRow'>
			<span class='panelTitle'><input type="submit" text="Valider"></span>
		</div>
	</div>
</form>


<?php
if (isset($_POST['gts_nom'] )) {
	if (! isset ( $_POST ['gts_id'] )) {
		
		AddTarification ($siteURL);
	} else {
		UpdateTarification ($siteURL);
	}
}

function AddTarification($siteURL) {
	$groupeController = new GroupeTarificationSpecialeController ();
	$groupetarificationspeciale = $groupeController->CaptureGroupeTarificationSpecialeFromPost ( $_POST );
	$groupetarificationspeciale->Setdatecreation ( date ( "Y-m-d H:i:s" ) );
	$groupetarificationspeciale->Setdatedernieremodification ( date ( "Y-m-d H:i:s" ) );
	
	$groupetarificationspeciale = $groupeController->AddGroupeTarificationSpeciale ( $groupetarificationspeciale );
	
	header ( 'Location: ' . $siteURL . '/index.php/tarifs/edit-tarif-special?groupeID=' . $groupetarificationspeciale->Getid () );
}

function UpdateTarification($siteURL) {
	$groupeController = new GroupeTarificationSpecialeController ();
	$groupetarificationspeciale = $groupeController->CaptureGroupeTarificationSpecialeFromPost ( $_POST );
	$groupetarificationspeciale->Setdatedernieremodification ( date ( "Y-m-d H:i:s" ) );
	
	$result = $groupeController->UpdateGroupeTarificationSpeciale ( $groupetarificationspeciale );
	
	header ( 'Location: ' . $siteURL . '/index.php/tarifs/edit-tarif-special?groupeID=' . $groupetarificationspeciale->Getid () );
}
?>