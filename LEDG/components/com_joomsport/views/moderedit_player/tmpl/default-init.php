<?php
/*------------------------------------------------------------------------
# JoomSport Professional 
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2011 JoomSport.com. All Rights Reserved.
# @license - http://joomsport.com/news/license.html GNU/GPL
# Websites: http://www.JoomSport.com 
# Technical Support:  Forum - http://joomsport.com/helpdesk/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

	if(isset($this->message)){

		$this->display('message');

	}

	$row = $this->row;

	$lists = $this->lists;

	$Itemid = JRequest::getInt('Itemid');

?>


<?php
echo $lists["panel"];
?>

<!-- <module middle> -->
<div class="module-middle solid">
	
	<!-- <back box> -->
	<!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
	<!-- </back box> -->
	<!-- <title box> -->
		<div class="title-box padd-bot">
			<h2>
				<?php echo $row->id?JText::_('BLFA_PLAYER_EDIT'):JText::_('BLFA_PLAYER_NEW');?>
			</h2>
			
		</div>
		<!-- </div>title box> -->
	
	
</div>
<!-- </module middle> -->
<?php if($lists["canmore"]){	?>
<!-- <control bar> -->
<div class="control-bar-wr dotted">
	<ul class="control-bar">
		<li><a class="save" href="#" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('mdplayer_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>

		<li><a class="delete" href="#" onclick="javascript:submitbutton('admin_player');return false;" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
	</ul>
</div>
<!-- </control bar> -->

<!-- <content module> -->
	<div class="content-module admin-mo-co">

<?php



		$editor =& JFactory::getEditor();

		JHTML::_('behavior.tooltip');

		?>

		<script type="text/javascript">

		<!--
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {

			var form = document.adminForm;

			 if(pressbutton == 'mdplayer_apply' || pressbutton == 'mdplayer_save'){

			 	if(form.first_name.value == '' || form.last_name.value == ''){

			 		alert('<?php echo JText::_( 'BLFA_JSNOTICEPL' ); ?>');

			 	
			 	}else{

			 		submitform( pressbutton );

					return;

			 	}

			 }else{

				submitform( pressbutton );

					return;

			 }		

		}	

		

		function Delete_tbl_row(element) {

			var del_index = element.parentNode.parentNode.sectionRowIndex;

			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;

			element.parentNode.parentNode.parentNode.deleteRow(del_index);

		}

		//-->

		</script>

		<?php
	

		if(!count($row)){

			echo "<div id='system-message'>".JText::_('BLFA_NOITEMS')."</div>";

		}

		?>

		<form action="<?php echo JURI::base();?>index.php?option=com_joomsport&controller=moder&tid=<?php echo $lists["tid"];?>&Itemid=<?php echo $Itemid;?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		

		<table class="season-list">
			<tr>
				<td width="100">
					<?php echo JText::_( 'User' ); ?>
				</td>
				<td>
					<div class="selectsty"><span class='down'><!-- --></span><?php echo $lists['usrid'];?></div>
				</td>
			</tr>
			<tr>

				<td width="100">

					<?php echo JText::_( 'BLFA_FIRSTNAME' ); ?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_FIRSTNAME' ); ?>::<?php echo JText::_( 'BLFA_TT_FIRST_NAME' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

				</td>

				<td>

					<input type="text" class="feed-back inp-big" maxlength="255" size="60" name="first_name" value="<?php echo $row->first_name?>" />

				</td>

			</tr>

			<tr>

				<td width="100">

					<?php echo JText::_( 'BLFA_LASTNAME' ); ?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_LASTNAME' ); ?>::<?php echo JText::_( 'BLFA_TT_LAST_NAME' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

				</td>

				<td>

					<input type="text" class="feed-back inp-big" maxlength="255" size="60" name="last_name" value="<?php echo $row->last_name?>" />

				</td>

			</tr>

			<tr>
				<td width="100">
					<?php echo JText::_( 'BL_NICK' ); ?>
				</td>
				<td>
					<input type="text" class="feed-back inp-big" maxlength="255" size="60" name="nick" value="<?php echo $row->nick?>" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BL_COUNTRY' ); ?>
				</td>
				<td>
					<div class="selectsty"><span class='down'><!-- --></span><?php echo $lists['country']?></div>
				</td>
			</tr>
			<?php
			for($p=0;$p<count($lists['ext_fields']);$p++){
			if($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)){
			}else{
			?>
			<tr>
				<td width="100">
					<?php echo $lists['ext_fields'][$p]->name;?>
				</td>
				<td>
					<?php
					
						switch($lists['ext_fields'][$p]->field_type){
								
							case '1':	echo $lists['ext_fields'][$p]->selvals;
										break;
							case '2':	echo $this->editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore', 'image') ) ;
										break;
							case '3':	echo '<div class="selectsty"><span class="down"><!-- --></span>'.$lists['ext_fields'][$p]->selvals.'</div>';
										break;	
							case '0':					
							default:	echo '<input type="text" class="feed-back inp-big" maxlength="255" size="60" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):"").'" />';		
										break;
								
						}
					?>
					<input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->field_type?>" />
					<input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->id?>" />
				</td>
			</tr>
			<?php	
			}
			}
			?>
			

		</table>
		<div style="margin-top:10px;border:1px solid #BBB;">
		<table style="padding:10px;" class="season-list">

			<tr>

				<td>

					<?php echo JText::_('BLFA_UPLFOTO');?>

					<span class="editlinktip hasTip" title="<?php echo JText::_('BLFA_UPLFOTO');?>::<?php echo JText::_( 'BLFA_TT_UPLOAD_PL_PHOTO' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

				</td>

			</tr>

			<tr>

				<td>&nbsp;

				

				</td>

			</tr>

			<tr>

				<td>

				<input type="file" name="player_photo_1" value="" class="feed-back inp-small" />

				</td>

			</tr>

			<tr>

				<td>

				<input type="file" name="player_photo_2" value="" class="feed-back inp-small" />

				</td>

			</tr>
			<tr>
				<td>
					<button class="send-button" onclick="javascript:submitbutton('mdplayer_apply');" ><span><?php echo JText::_( 'BLFA_UPLOAD' ); ?></span></button>
				</td>

			</tr>
			

		</table>

		<?php

		if(count($lists['photos'])){

		?>

		<table class="adminlist">

			<tr>

				<th class="title" width="30"><?php echo JText::_('BLFA_DELETE')?></th>

				<th class="title" width="30"><?php echo JText::_('BLFA_DEFAULT')?></th>

				<th class="title" ><?php echo JText::_('BLFA_TITLE')?></th>

				<th class="title" width="250"><?php echo JText::_('BLFA_IMAGE')?></th>

			</tr>

			<?php

			foreach($lists['photos'] as $photos){

			?>

			<td align="center">

				<a href="javascript:void(0);" title="<?php echo JText::_('BLFA_REMOVE')?>" onClick="javascript:Delete_tbl_row(this);"><img src="<?php echo JURI::base();?>components/com_joomsport/img/ico/close.png" title="<?php echo JText::_('BLFA_REMOVE')?>" /></a>

			</td>

			<td align="center">

				<?php

				$ph_checked = ($row->def_img == $photos->id) ? 'checked="true"' : "";

				

				?>

				<input type="radio" name="ph_default" value="<?php echo $photos->id;?>" <?php echo $ph_checked?>/>

				<input type="hidden" name="photos_id[]" value="<?php echo $photos->id;?>"/>

			</td>

			<td>

				<input type="text" maxlength="255" size="60" name="ph_names[]" value="<?php echo htmlspecialchars($photos->name)?>" />

			</td>

			<td align="center">

				<?php

				$imgsize = getimagesize('media/bearleague/'.$photos->filename);

				if($imgsize[0] > 200){

					$width = 200;

				}else{

					$width  = $imgsize[0];

				}

				?>

				<a rel="{handler: 'image'}" href="<?php echo JURI::base();?>../media/bearleague/<?php echo $photos->filename?>" title="Image" class="modal-button"><img src="<?php echo JURI::base();?>../media/bearleague/<?php echo $photos->filename?>" width="<?php echo $width;?>" /></a>

			</td>

			</tr>

			<?php

			}

			?>

		</table>

		<?php

		}

		?>
		</div>
		<input type="hidden" name="controller" value="moder" />
		<input type="hidden" name="task" value="" />

		<input type="hidden" name="id" value="<?php echo $row->id?>" />

		<input type="hidden" name="boxchecked" value="0" />

		<input type="hidden" name="tid" value="<?php echo $lists["tid"];?>" />
		
		<?php echo JHTML::_( 'form.token' ); ?>

		</form>
</div>		
<?php }else{ echo "<div>".JText::_('BLFA_PLAYERLIMITIS')."</div>"; }?>	

