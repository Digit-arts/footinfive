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
defined('_JEXEC') or die;
$row = $this->row;
$lists = $this->lists;
JHTML::_('behavior.tooltip');		
		?>
		<script type="text/javascript">
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			 if(pressbutton == 'venue_apply' || pressbutton == 'venue_save' || pressbutton == 'venue_save_new'){
			 	if(form.v_name.value != ''){
					submitform( pressbutton );
					return;
				}else{
					getObj('vname').style.border = "1px solid red";
					alert("<?php echo JText::_('BLBE_JSMDNOT1');?>");
					
					
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
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<table>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_VENNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_VENNAME' ); ?>::<?php echo JText::_( 'BLBE_TT_VENNAME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="v_name" id="vname" value="<?php echo htmlspecialchars($row->v_name)?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_VADDRESS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_VADDRESS' ); ?>::<?php echo JText::_( 'BLBE_VADDRESS' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="v_address" value="<?php echo htmlspecialchars($row->v_address)?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_VCOORDY' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_VCOORDY' ); ?>::<?php echo JText::_( 'BLBE_TT_VCOORDY' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					
					<input type="text" maxlength="255" size="60" name="v_coordx" value="<?php echo htmlspecialchars($row->v_coordx)?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_VCOORDX' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_VCOORDX' ); ?>::<?php echo JText::_( 'BLBE_TT_VCOORDX' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="v_coordy" value="<?php echo htmlspecialchars($row->v_coordy)?>" />
				</td>
			</tr>
			
			
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_VDESCR' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_VDESCR' ); ?>::<?php echo JText::_( 'BLBE_TT_VDESCR' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<?php echo $editor->display( 'v_descr',  htmlspecialchars($row->v_descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				
				</td>
			</tr>
		</table>
		<div style="margin-top:10px;bOrder:1px solid #BBB;">
		<table style="padding:10px;">
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_UPLFOTO_VENUE' ); ?>
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_1" value="" /><input type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" onclick="submitbutton('venue_apply');" />
				</td>
			</tr>
			<!--tr>
				<td>
				<input type="file" name="player_photo_2" value="" />
				</td>
			</tr-->
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_ONEPHSEL' ); ?>
				</td>
			</tr>
		</table>
		<?php
		if(count($lists['photos'])){
		?>
		<table class="adminlist">
			<tr>
				<th class="title" width="30"><?php echo JText::_( 'BLBE_DELETE' ); ?></th>
				<th class="title" width="30"><?php echo JText::_( 'BLBE_DEFAULT' ); ?></th>
				<th class="title" ><?php echo JText::_( 'BLBE_TITLE' ); ?></th>

				<th class="title" width="250"><?php echo JText::_( 'BLBE_IMAGE' ); ?></th>
			</tr>
			<?php
			foreach($lists['photos'] as $photos){
			?>
			<td align="center">
				<a href="javascript:void(0);" title="<?php echo JText::_( 'BLBE_REMOVE' ); ?>" onClick="javascript:Delete_tbl_row(this);"><img src="<?php echo JURI::base();?>components/com_joomsport/img/publish_x.png" title="<?php echo JText::_( 'BLBE_REMOVE' ); ?>" /></a>
			</td>
			<td align="center">
				<?php
				$ph_checked = ($row->v_defimg == $photos->id) ? 'checked="true"' : "";
				
				?>
				<input type="radio" name="ph_default" value="<?php echo $photos->id;?>" <?php echo $ph_checked?>/>
				<input type="hidden" name="photos_id[]" value="<?php echo $photos->id;?>"/>
			</td>
			<td>
				<input type="text" maxlength="255" size="60" name="ph_names[]" value="<?php echo htmlspecialchars($photos->name)?>" />
			</td>
			<td align="center">
				<?php
				$imgsize = getimagesize('../media/bearleague/'.$photos->filename);
				if($imgsize[0] > 200){
					$width = 200;
				}else{
					$width  = $imgsize[0];
				}
				?>
				<a rel="{handler: 'image'}" href="<?php echo JURI::base();?>../media/bearleague/<?php echo $photos->filename?>" title="<?php echo JText::_( 'BLBE_IMAGE' ); ?>" class="modal-button"><img src="<?php echo JURI::base();?>../media/bearleague/<?php echo $photos->filename?>" width="<?php echo $width;?>" /></a>
			</td>
			</tr>
			<?php
			}
			?>
		</table>
		</div>
		<?php
		}
		
		
		?>
		</table>
		
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="venue_edit" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>