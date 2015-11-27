<link rel="stylesheet" type="text/css"
	href="libraries/ya2/flexigrid-master/css/flexigrid.pack.css" />
<script type="text/javascript"
	src="libraries/jquery/jquery.1.8.3.min.js"></script>
<script type="text/javascript"
	src="libraries/ya2/flexigrid-master/js/flexigrid.pack.js"></script>
<script type="text/javascript" src="libraries/tinymce/tinymce.min.js"></script>
<!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript"
	src="libraries/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<!-- Add fancyBox -->
<link rel="stylesheet"
	href="libraries/fancybox/source/jquery.fancybox.css?v=2.1.5"
	type="text/css" media="screen" />
<script type="text/javascript"
	src="libraries/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

<!-- Optionally add helpers - button, thumbnail and/or media -->
<link rel="stylesheet"
	href="libraries/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5"
	type="text/css" media="screen" />
<script type="text/javascript"
	src="libraries/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript"
	src="libraries/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

<link rel="stylesheet"
	href="libraries/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7"
	type="text/css" media="screen" />
<script type="text/javascript"
	src="libraries/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<?
require_once ('admin_base.php');
	
	?>
<script type="text/javascript">
	
	function enregistrer() {
		if (confirm('Vous allez envoyer cet email a tous vos clients,\n confirmez-vous cette action ?'))
			document.envoyer_email.submit()
	}

</script>

<?
	
	if (test_non_vide ( $_GET ["new"] ) || test_non_vide ( $_GET ["update"] )) {
		
		if (test_non_vide ( $_GET ["update"] )) {
			$id_pub = $_GET ["id_pub"];
			
			echo "<input name=\"id_pub\" type=\"hidden\"  value=\"" . $id_pub . "\">";
			
			$requete_recup_publipostage = "SELECT p.* FROM  `Publipostage` as p WHERE p.id_pub=" . $id_pub;
			
			// echo $requete_recup_publipostage;
			$db->setQuery ( $requete_recup_publipostage );
			$db->query ();
			$resultat_recup_publipostage = $db->loadObjectList ();
		}
		?>
<script type="text/javascript">
        tinymce.init({
            selector: "#mail_body",
            theme: "modern",
            language: "fr_FR",  
            relative_urls:false,              
            plugins: [
                 "advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker",
                 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                 "save table contextmenu directionality emoticons template paste textcolor jbimages"
           ],
           toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link jbimages | print preview media fullpage | forecolor backcolor emoticons", 
           style_formats: [
                {title: 'Bold text', inline: 'b'},
                {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                {title: 'Example 1', inline: 'span', classes: 'example1'},
                {title: 'Example 2', inline: 'span', classes: 'example2'},
                {title: 'Table styles'},
                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            ]
                    
        });



        
          
    </script>

<form name="envoyer_email" enctype="multipart/form-data"
	class="submission box"
	action="<?php echo JRoute::_( '../../../index.php?option=com_publipostage&task=Save'); ?>"
	method="post">
		<?
		echo "<input name=\"limit\" type=\"hidden\"  value=\"" . $limit . "\">";
		
		$ledg = 0;
		$police = 0;
		
		// recup de tous les types de destinataires
		$type_destinataires = "SELECT * FROM  `type_regroupement`";
		$db->setQuery ( $type_destinataires );
		$db->query ();
		$resultat_type_destinataires = $db->loadObjectList ();
		;
		$resultat_type_destinataires_select = array ();
		
		if (test_non_vide ( $_GET ["update"] )) {
			echo "<input name=\"id_pub\" type=\"hidden\"  value=\"" . $id_pub . "\">";
			
			// recup types destinataires selectionnes
			$type_destinataires = "SELECT p.*,t.nom FROM  `Publipostage_type_destinataires` as p, `type_regroupement` as t WHERE p.id_type_regroupement=t.id AND p.id_pub=" . $id_pub;
			$db->setQuery ( $type_destinataires );
			$db->query ();
			$resultat_type_destinataires_select = $db->loadObjectList ();
			;
			
			foreach ( $resultat_type_destinataires_select as $type_dest ) {
				if ($type_dest->id_type_regroupement == 10001)
					$ledg = 1;
				if ($type_dest->id_type_regroupement == 10000)
					$police = 1;
			}
		}
		?>
		<table class="zebra" border="0">
		<tr>
			<th>OBJET</th>
			<td align="left"><input name="objet" type="text"
				value="<? if(isset($resultat_recup_publipostage)) echo $resultat_recup_publipostage[0]->objet;?>">&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="joueur_champ" value="1"
				<? if ($ledg==1) echo "checked"; ?>> <img
				src="images/coupe-icon.png" title="les joueurs du championnat" /> <input
				type="checkbox" name="police" value="1"
				<? if ($police==1) echo "checked"; ?>> <img
				src="images/police-icon.png" title="les clients police" />
				<?
		foreach ( $resultat_type_destinataires as $type_dest ) {
			if (($type_dest->id == 10001) or ($type_dest->id == 10000))
				continue;
			$select = '';
			foreach ( $resultat_type_destinataires_select as $type_dest_select ) {
				if ($type_dest_select->id_type_regroupement == $type_dest->id)
					$select = " checked ";
			}
			
			echo "<input type=\"checkbox\" name=\"Type_Regroupement_" . $type_dest->id . "\" value=\"" . $type_dest->id . "\" \"" . $select . "\">" . $type_dest->nom;
		}
		?>
			</td>
		</tr>
		<tr>
		<?
		
		if (test_non_vide ( $_GET ["just_img"] )) {
			echo "<th>Image</th><td><input type=\"file\" name=\"doc[]\"  accept=\"image/jpg,image/jpeg\"  /> " . "<br>Le nom du fichier est important car il sera le lien dans le mail \"http://footinfive.com/FIF/news/nom_du_fichier.jpg\"" . "<br>Il faut un nom de fichier sans accent,sans espace et sans caracteres speciaux &agrave; part \"_\"" . "<br>Le nom du fichier peut contenir des lettres en minuscules et des chiffres.</td>";
		} else {
			$corps = $resultat_recup_publipostage [0]->corps;
			$siteRoot = $config->get ( 'site_root' );
			$corps = str_replace ( 'src="' . $siteRoot, 'src="', $corps );
			?>
			<th>Corps</th>
			<td align="left"><textarea rows="30" cols="100" name="corps"
					id="mail_body"><? if(isset($resultat_recup_publipostage)) echo $resultat_recup_publipostage[0]->corps;?></textarea></td>
		<?
		}
		?>
		</tr>
	</table>
	<input name="valide" type="button"
		value="Enregistrer ce mail et recevoir un exemplaire avant de l'envoyer"
		onclick="enregistrer()">
</form>
<?
	} else {
		
		menu_acces_rapide ( $_GET ["id_client"], "Publipostage" );
		
		?>
<pre>
	<table class="flexme4" style="display: none"></table>
</pre>

<?php
	}


?>

<script type="text/javascript">

	
	
			$(".flexme4").flexigrid({
				url : '../../../index.php?option=com_publipostage&task=Load&format=raw',
				dataType : 'json',
				colModel : [ {
					display : 'Numero',
					name : 'pubID',
					sortable : true,
					align : 'left',
                    hide : true
				},{
					display : 'Fait par',
					name : 'username',
					sortable : true,
					align : 'left'
				}, {
					display : 'Date creation',
					name : 'creationDate',
					sortable : true,
					align : 'left'
				}, {
					display : 'Objet',
					name : 'subject',
					width : 120,
					sortable : true,
					align : 'left'
				}, {
                    display : 'Contenu',
                    name : 'body',
                    align : 'left',
                    hide : true
                }, {
					display : 'Destinataires',
					name : 'sendTo',
					sortable : true,
					align : 'left',
						width : 120
				}, {
					display : 'Mails envoy&eacute;s',
					name : 'nbSent',
					sortable : true,
					align : 'left'
				}, {
					display : 'Etat',
					name : 'status',
					sortable : true,
					align : 'left'
				}],
				buttons : [ {
					name : 'Cr&eacute;er un publipostage',
					bclass : 'add',
					onpress : GridAction
				}
				,
				{
					separator : true
				}
				,
				{
					name : 'Modifier la s&eacute;lection',
					bclass : 'edit',
					onpress : GridAction
				}
				,
				{
					separator : true
				}
				,
				{
					name : 'D&eacute;marrer la s&eacute;lection',
					bclass : 'start',
					onpress : GridAction
				}
				,
				{
					separator : true
				}
				,
				{
					name : 'Supprimer la s&eacute;lection',
					bclass : 'delete',
					onpress : GridAction
				}
				],
				searchitems : [ {
					display : 'Fait par',
					name : 'userame'
				}, {
					display : 'Destinataires',
					name : 'sendTo'
				}, {
					display : 'Objet',
					name : 'subject',
					isdefault : true
				} ],
				sortname : "pubID",
				sortorder : "desc",
				usepager : true,
				title : 'Publipostage',
				useRp : true,
				rp : 15,
				showTableToggleBtn : true,
				width : 'auto',
				height : 'auto',
				nowrap : false
			});
	
				function GridAction(com, grid) {
					if (com == 'Supprimer la s&eacute;lection') {
	                    var conf = confirm('Voulez-vous vraiment supprimer les '  + $('.trSelected').length + ' publipostages?')
	                    if(conf){
	                        $.each($('.trSelected', grid),
	                        		function(key, value){
                                $.get('../../../index.php?option=com_publipostage&task=Delete&format=raw', { PubId: value.children[0].textContent}
                                    , function(){
                                        // when ajax returns (callback), update the grid to refresh the data
                                        $(".flexme4").flexReload();
                                });
                        });    
	                    }
	                }
					if (com == 'D&eacute;marrer la s&eacute;lection') {
						var conf = confirm('Voulez-vous lancer l\'envoi (' + $('.trSelected').length + ' email(s) selectionne(s))?')
						if(conf){
							$.each($('.trSelected', grid),
		                            function(key, value){
		                                $.get('../../../index.php?option=com_publipostage&task=Start&format=raw', { PubId: value.children[0].textContent}
		                                    , function(){
		                                        // when ajax returns (callback), update the grid to refresh the data
		                                        $(".flexme4").flexReload();
		                                });
		                        });
						}
					}
					else if (com == 'Modifier la s&eacute;lection') {
						var id_pub = $('.trSelected')[0].children[0].textContent;
						window.location.href = 'article?id=65&update=1&id_pub=' + id_pub;
					}
					else if (com == 'Cr&eacute;er un publipostage') {
						window.location.href = 'article?id=65&new=1';
						
					}
				}
				</script>

<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
</script>