<link rel="stylesheet" href="libraries/ya2/styles/style.css"
	type="text/css" />
<script type="text/javascript" src="libraries/jquery/jquery.last.min.js"></script>

<link rel="stylesheet"
	href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="libraries/jquery-ui/jquery-ui.min.js"></script>
<script src="libraries/jquery-ui/datepicker-fr.js"></script>
<script src="libraries/parsley.js/dist/parsley.min.js"></script>
<?php
require_once ('admin_base.php');
$siteURL= $config->get( 'site_url' );

nettoyer_resa_non_payees ();

if (est_min_agent ( $user )) {
	if (test_non_vide ( $_POST ["id_client"] ))
		$id_client = $_POST ["id_client"];
	else
		$id_client = $_GET ["id_client"];
} else
	$id_client = idclient_du_user ();

menu_acces_rapide ( $id_client, "R&eacute;server" );

if (test_non_vide ( $_POST ["num_resa"] ))
	$num_resa = $_POST ["num_resa"];
else
	$num_resa = $_GET ["num_resa"];

$force_valider=0;
if(test_non_vide ($_GET["force_valider"])) {
	$force_valider = $_GET["force_valider"];
}

$date_debut_resa="";
if(test_non_vide ($_GET["date_debut_resa"])) {
	$date = DateTime::createFromFormat ( "Y-m-d", $_GET["date_debut_resa"] );
	$date_debut_resa=$date->format ( "d/m/Y" );
}

proprietaire_resa ( $num_resa );

if (test_non_vide ( $_GET ["rubiks_cube"] )) {
	
	$resas_a_optimiser = recup_resa_sur_periode ( $_GET ["date_debut_resa"], $_GET ["heure_debut_resa"], $_GET ["heure_fin_resa"] );
	
	foreach ( $resas_a_optimiser as $la_resa_a_optimiser ) {
		$tab_liste_terrains_dispo_pour_optimisation = test_dispo ( $la_resa_a_optimiser->type_terrain, $la_resa_a_optimiser->date_debut_resa, $la_resa_a_optimiser->heure_debut_resa, $la_resa_a_optimiser->heure_fin_resa, '', $la_resa_a_optimiser->id_resa );
		if (is_array ( $tab_liste_terrains_dispo_pour_optimisation )) {
			foreach ( $tab_liste_terrains_dispo_pour_optimisation as $liste_terrains_dispo ) {
				if ($liste_terrains_dispo < $la_resa_a_optimiser->id_terrain) {
					// ///////////
					// on met à jour la resa dans le cas d'une simple modif
					// ////////////
					$id_resa_new = maj_resa ( $la_resa_a_optimiser->id_resa, $la_resa_a_optimiser->date_debut_resa, $la_resa_a_optimiser->date_fin_resa, $la_resa_a_optimiser->heure_debut_resa, $la_resa_a_optimiser->heure_fin_resa, $la_resa_a_optimiser->id_client, $liste_terrains_dispo, $la_resa_a_optimiser->montant_total, $la_resa_a_optimiser->duree_resa, $la_resa_a_optimiser->commentaire, $la_resa_a_optimiser->cautionnable );
					
					$rdv = ajout_event_cal_google ( $id_resa_new );
					suppr_event_cal_google ( $la_resa_a_optimiser->id_resa );
					
					// /////////////////
					// on met à jour la resa avec l'adresse du rdv
					// ///////////////////
					maj_cal_dans_resa ( $id_resa_new, $rdv->getEditLink ()->href );
					break;
				}
			}
		}
	}
}

if (isset ( $user ) and (est_register ( $user )) and ($id_client != "")) {
	$requete_recup_client = "select id_client, prenom, nom, mobile1, fixe,commentaire from Client where id_client=" . $id_client . " order by nom,prenom,code_insee";
	$db->setQuery ( $requete_recup_client );
	$recup_client = $db->loadObject ();
	if (! test_non_vide ( $recup_client->nom ))
		header ( "Location: $siteURL/index.php/component/content/article?id=57&modif=1&id_client=" . $user->id . "" );
}
if (! test_non_vide ( $id_client ))
	header ( "Location: $siteURL/index.php?option=com_content&view=article&id=57" );
?>
<script type="text/javascript">
	function valider() {
		$('.content-loading').show();
		$.ajax({
            type: 'post',
            data: $('#formulaire').serialize(),
            url: '../../../index.php?option=com_reservation&task=LoadResaTable&format=raw',
            success: function(data) {
            	$('#table_resa').html(data);
            },
            complete: function(){
                $('.content-loading').hide();
                $('#formulaire_resa').parsley();

             // on form submit
                $("#formulaire_resa").submit(function(event) {
                    // validate form with parsley.
                    $(this).parsley().validate();


                    // if this form is valid
                    if ($(this).parsley().isValid()) {
                    	if (typeof document.formulaire_resa.terrain_choisit != 'undefined'){
                			if (!confirm('Confirmez votre choix : \n\n' + document.formulaire_resa.date_debut_resa_longue.value + ' sur le terrain ' + document.formulaire_resa.terrain_choisit.value ))
                				event.preventDefault();
                		}
                		else {
                			if (!confirm('Confirmez-vous votre choix ?'))
                				event.preventDefault();
                		}
                    }
                });
              },
            error: function() {
                alert('Error occured');
            }
          });		
	}

	function loadMulti() {
		$.ajax({
            type: 'post',
            url: '../../../index.php?option=com_reservation&task=LoadMultiResa&format=raw',
            success: function(data) {
            	$('#type_multi').html(data);
      		  $( "#datepicker2" ).datepicker( $.datepicker.regional[ "fr" ] );
            },
            error: function() {
                alert('Error occured');
            }
          });		
	}
	
	function reserver() {
		
		if (typeof document.formulaire_resa.terrain_choisit != 'undefined'){
			if (confirm('Confirmez votre choix : \n\n' + document.formulaire_resa.date_debut_resa_longue.value + ' sur le terrain ' + document.formulaire_resa.terrain_choisit.value ))
				document.formulaire_resa.submit();
		}
		else {
			if (confirm('Confirmez-vous votre choix ?'))
				document.formulaire_resa.submit();
		}
	}

	  $(function() {
		  $( "#datepicker" ).datepicker( $.datepicker.regional[ "fr" ] );
	  });

</script>
<?

// ///////////////////////////////////////////////////
// ///// Formulaire de recherche de creneaux
// /////////////////////////////////////////////////

?>

<FORM id="formulaire" name="date_form_0" class="submission box"
	action="<?php echo $siteURL."/index.php/component/content/";?>article?id=62" method="post">
	<?
	// ///////////// Modif d'une RESA
	
	if (test_non_vide ( $num_resa )) {
		echo "<input name=\"num_resa\" type=\"hidden\"  value=\"" . $num_resa . "\" >";
		echo "Pour modifier votre r&eacute;servation (<font color=red>#" . $num_resa . "</font>), selectionnez ci-dessous vos nouvelles informations.<br><hr>";
	}
	$requete_recup_client = "select id_client, prenom, (select u.email from #__users as u where u.id=c.id_user) as courriel, c.nom as nom, mobile1, fixe,id_type_regroupement,tr.nom as type_reg, police " . " from Client as c LEFT JOIN Type_Regroupement as tr on c.id_type_regroupement=tr.id  where id_client=" . $id_client . " ";
	$db->setQuery ( $requete_recup_client );
	$recup_client = $db->loadObject ();
	
	$typeClient = $recup_client->type_reg . " " . $recup_client->nom_entite;
	if ($recup_client->police == 1)
		$typeClient .= " <img src=\"../images/police-icon.png\" title=\"Ce client est policier\">";
	
	$ligne_commentaire_client = recup_derniere_commentaire ( "id_client", $id_client );
	
	$total_credit_actuel = recup_credit_total_client ( $id_client );
	$total_caution_actuel = recup_caution_total_client ( $id_client );
	
	$resaMulti = '';
	$resaSimple = '';
	$resaQuoti = '';
	$resaHebdo = '';
	$resaVide = '';
	if (test_non_vide ( $_POST ["resa_mult"] ) and $_POST ["resa_mult"] == "1")
		$resaMulti = "checked";
	if (! test_non_vide ( $_POST ["resa_mult"] ) or (test_non_vide ( $_POST ["resa_mult"] ) and $_POST ["resa_mult"] == "0"))
		$resaSimple = "checked";
	
	if (test_non_vide ( $_POST ["resa_mult"] ))
		$resa_mult = $_POST ["resa_mult"];
	
	if (test_non_vide ( $_POST ["Frequence"] ) and $_POST ["Frequence"] == "1")
		$resaQuoti = "checked";
	if (test_non_vide ( $_POST ["Frequence"] ) and $_POST ["Frequence"] == "7")
		$resaHebdo = "checked";
	if (test_non_vide ( $_POST ["nbre_terrains_a_reserver"] ) and $_POST ["nbre_terrains_a_reserver"] == $i)
		$resaVide = "checked";
	
	$resaAcompte='';
	$resaCaution='';
	$resaSans='';
	if (test_non_vide ( $_GET ["mode_resa"] ) and $_GET ["mode_resa"] == "1")
		$resaAcompte = "selected";
	if (test_non_vide ( $_GET ["mode_resa"] ) and $_GET ["mode_resa"] == "2")
		$resaCaution = "selected";
	if (test_non_vide ( $_GET ["mode_resa"] ) and $_GET ["mode_resa"] == "3")
		$resaSans = "selected";
	
	$select_type_int = "";
	$select_type_ext = "";
	$select_type_loge = "";
	if (! isset ( $_GET ["type_terrain"] ))
		$select_type_int = " checked ";
	if (($_GET ["type_terrain"] == 1) or ($_POST ["type_terrain"] == 1))
		$select_type_int = " checked ";
	if (($_GET ["type_terrain"] == 2) or ($_POST ["type_terrain"] == 2))
		$select_type_ext = " checked ";
	if (($_GET ["type_terrain"] == 3) or ($_POST ["type_terrain"] == 3))
		$select_type_loge = " checked ";
	
	?>
	<div class='smallPanel'>
		<div class='panelHead'>Identification</div>
		<div class='panelRow'>
			<span class='panelTitle'>Type :</span> <span class='panelValue'><?php echo $typeClient; ?></span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Client :</span> <span class='panelValue'> <input
				name='id_client' type='hidden'
				value='<?php echo $recup_client->id_client; ?>'><?php echo $recup_client->nom. " " . $recup_client->prenom; ?>
				<?php if ($ligne_commentaire_client->Commentaire != "" and (est_min_agent ( $user ))) {?>
				<a
				href='<?php echo siteURL;?>/index.php/component/content/article?id=75&art=62&id_client=<?php echo $recup_client->id_client; ?>'>
					<img class='smallImage' src='../../../images/Comment-icon.png'
					title='<?php echo $ligne_commentaire_client->Commentaire; ?>'>
			</a>
				<?php }?>
			</span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Montant caution actuelle :</span> <span
				class='panelValue'><?php echo $total_caution_actuel; ?>&euro;</span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Montant avoir actuel :</span> <span
				class='panelValue'><?php echo $total_credit_actuel; ?>&euro;</span>
		</div>
	</div>
	<br />
	<div class='smallPanel'>
		<div class='panelHead'>Donn&eacute;es de r&eacute;servation</div>
		<div class='panelRow'>
			<span class='panelTitle'>Date :</span> <span class='panelValue'> <input
				type="text" name="date_input_0"
				value="<? echo $_POST["date_input_0"].$date_debut_resa;?>"
				onChange="valider()" id="datepicker">
			</span>
		</div>
		<?
		if (est_min_manager ( $user ) and ! test_non_vide ( $num_resa )) {
			?>
		<div class='panelRow'>
			<span class='panelTitle'>Type de r&eacute;servation :</span> <span
				class='panelValue'> <input type='radio' name='resa_mult' value='1'
				<?php echo $resaMulti?> onChange='loadMulti()'>Multiple <input
				type='radio' name='resa_mult' value='0' <?php echo $resaSimple?>
				onChange='valider()'>Simple
			</span>
		</div>
		<div id='type_multi'></div>
		<?php
		}
		?>
		<div class='panelRow'>
			<span class='panelTitle'>Type :</span> <span class='panelValue'> <input
				type='radio' name='type_terrain' value='1' onChange='valider()'
				<? echo "\"".$select_type_int."\"";?>>Int&eacute;rieur <input
				type='radio' name='type_terrain' value='2' onChange='valider()'
				<? echo "\"".$select_type_ext."\"";?>>Ext&eacute;rieur <input
				type='radio' name='type_terrain' value='3' onChange='valider()'
				<? echo "\"".$select_type_loge."\"";?>>Loge VIP
			</span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Heure :</span> <span class='panelValue'> <select
				name="heure_debut_resa" onChange="valider()">
			<?
			if (isset ( $_POST ["heure_debut_resa"] ))
				$heure_debut_resa = $_POST ["heure_debut_resa"];
			else
				$heure_debut_resa = $_GET ["heure_debut_resa"];
			list ( $heure_resa, $minutes_resa ) = explode ( ':', $heure_debut_resa );
			$heure_demarrage = substr ( horaire_ouverture (), 0, 2 );
			for($i = $heure_demarrage; $i <= 23; $i ++) {
				$select_heure = "";
				$select_demie = "";
				if (($heure_resa == "") and ($i == 9))
					$select_heure = " selected ";
				else if ($heure_resa == $i) {
					if ($minutes_resa == "30")
						$select_demie = " selected ";
					else
						$select_heure = " selected ";
				}
				echo "<option value=\"" . $i . ":00\" \"" . $select_heure . "\">" . $i . "h00</option>";
				if ($i != 9)
					echo "<option value=\"" . $i . ":30\" \"" . $select_demie . "\">" . $i . "h30</option>";
			}
			$select = "";
			if (($heure_resa != "") and ($heure_resa == 0))
				$select = " selected ";
			echo "<option value=\"00:00\" \"" . $select . "\">00h00</option>";
			$select = "";
			if (($heure_resa != "") and ($heure_resa == 0) and $minutes_resa == "30")
				$select = " selected ";
			echo "<option value=\"00:30\" \"" . $select . "\">00h30</option>";
			$select = "";
			if (($heure_resa != "") and ($heure_resa == 1))
				$select = " selected ";
			echo "<option value=\"01:00\" \"" . $select . "\">01h00</option>";
			$select = "";
			if (($heure_resa != "") and ($heure_resa == 1) and $minutes_resa == "30")
				$select = " selected ";
			echo "<option value=\"01:30\" \"" . $select . "\">01h30</option>";
			$select = "";
			if (($heure_resa != "") and ($heure_resa == 2))
				$select = " selected ";
			echo "<option value=\"02:00\" \"" . $select . "\">02h00</option>";
			$select = "";
			if (($heure_resa != "") and ($heure_resa == 2) and $minutes_resa == "30")
				$select = " selected ";
			echo "<option value=\"02:30\" \"" . $select . "\">02h30</option>";
			$select = "";
			if (($heure_resa != "") and ($heure_resa == 3))
				$select = " selected ";
			echo "<option value=\"03:00\" \"" . $select . "\">03h00</option>";
			$select = "";
			?>
			</select>
			</span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Dur&eacute;e :</span> <span
				class='panelValue'> <select name="duree_resa" onChange="valider()">
			<?
			if (isset ( $_POST ["duree_resa"] ))
				$duree_resa = $_POST ["duree_resa"];
			else {
				if (isset ( $_GET ["heure_fin_resa"] ))
					$duree_resa = duree_en_horaire ( diff_dates_en_minutes ( "", $heure_debut_resa, "", $_GET ["heure_fin_resa"] ) );
				else
					$duree_resa = $_GET ["duree_resa"];
			}
			list ( $duree_heure_resa, $duree_minutes_resa ) = explode ( ':', $duree_resa );
			if (est_min_manager ( $user ))
				$max = 17;
			else
				$max = 3;
			for($i = 1; $i <= $max; $i ++) {
				$select_duree = "";
				$select_demie = "";
				
				if ($duree_heure_resa == $i) {
					if ($duree_minutes_resa == "30")
						$select_demie = " selected ";
					else
						$select_duree = " selected ";
				}
				
				echo "<option value=\"" . $i . ":00\" \"" . $select_duree . "\">" . $i . "h</option>";
				if ($i < $max)
					echo "<option value=\"" . $i . ":30\" \"" . $select_demie . "\">" . $i . "h30</option>";
			}
			?>
			</select>
			</span>
		</div>
		<div class='panelRow'>
			<span class='panelTitle'>Mode de r&eacute;servation :</span> 
			<span class='panelValue'> 
				<select name="mode_resa" onChange="valider()">
					<option value="1" <?= $resaAcompte?>>Avec acompte (-10&euro;/h)</option>
					<option value="2" <?= $resaCaution?>>Avec caution (-10&euro;/h)</option>
					<?php if(!in_array ( $recup_client->id_type_regroupement, array (1,2,10000) )){?>
					<option value="3" <?= $resaSans?>>Sans acompte ni caution</option>
					<?php }?>
			</select>
			</span>
		</div>
	</div>
	<input type="hidden" value="<?= $id_client ?>"/>
</form>
<div class='clear'></div>
<div id='table_resa' class='content-to-load'>
<?php 
	if(is_numeric($num_resa) || $force_valider==1) {
		?>
		<script type="text/javascript">
	valider();
	</script>
<?php
	}
?>
</div>

