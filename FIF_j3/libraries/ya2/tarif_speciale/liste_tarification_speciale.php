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
<link rel="stylesheet" type="text/css"
	href="libraries/ya2/flexigrid-master/css/flexigrid.pack.css" />
<script type="text/javascript"
	src="libraries/ya2/flexigrid-master/js/flexigrid.pack.js"></script>
<!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript"
	src="libraries/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<?php

menu_acces_rapide ( - 1, "Liste des tarifs sp&eacute;ciaux" );

$groupeController = new GroupeTarificationSpecialeController ();
$liste_gts = $groupeController->GetAllGroupeTarificationSpeciale();



?>

<pre>
	<table class="flexme4" style="display: none"></table>
</pre>

<script type="text/javascript">

	
	
			$(".flexme4").flexigrid({
				url : '<?php echo $siteURL;?>/libraries/ya2/tarif_speciale/liste_tarification_speciale.dat.php',
				dataType : 'json',
				colModel : [ {
					display : 'Numero',
					name : 'gts_id',
					sortable : true,
					align : 'left'
				}, {
					display : 'Date creation',
					name : 'gts_date_creation',
					sortable : true,
					align : 'left',
    				width : 130
				}, {
					display : 'Nom',
					name : 'gts_nom',
					sortable : true,
					align : 'left',
    				width : 200
				}, {
                    display : 'Description',
                    name : 'gts_description',
                    align : 'left',
    				width : 275
                }, {
					display : 'Tarif heure pleine',
					name : 'gts_tarif_hc',
					sortable : true,
					align : 'left'
				}],
				buttons : [ {
					name : 'Cr&eacute;er un tarif',
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
				],
				searchitems : [ {
					display : 'Nom',
					name : 'gts_nom'
				} ],
				sortname : "gts_id",
				sortorder : "desc",
				usepager : true,
				title : 'Tarifs sp&eacute;ciaux',
				useRp : true,
				rp : 15,
				showTableToggleBtn : true,
				width : 'auto',
				height : 'auto',
				nowrap : false
			});
	
				function GridAction(com, grid) {
					if (com == 'Supprimer la s&eacute;lection') {
	                    var conf = confirm('Voulez-vous vraiment supprimer les '  + $('.trSelected').length + ' tarifs? ATTENTION, toutes les associations clients seront supprimées!')
	                    if(conf){
	                        $.each($('.trSelected', grid),
	                        		function(key, value){
                                $.get('<?php echo $siteURL;?>/index.php/tarifs/edit-tarif-special', { groupeDelID: value.children[0].textContent}
                                    , function(){
                                        // when ajax returns (callback), update the grid to refresh the data
                                        $(".flexme4").flexReload();
                                });
                        });    
	                    }
	                }
					if (com == 'Modifier la s&eacute;lection') {
						var groupeID = $('.trSelected')[0].children[0].textContent;
						window.location.href = '<?php echo $siteURL;?>/index.php/tarifs/edit-tarif-special?groupeID=' + groupeID;
					}
					else if (com == 'Cr&eacute;er un tarif') {
						window.location.href = '<?php echo $siteURL;?>/index.php/tarifs/edit-tarif-special';
						
					}
				}
				</script>