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
		<!--
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			 if(pressbutton == 'map_apply' || pressbutton == 'map_save' || pressbutton == 'map_save_new'){
			 	if(form.m_name.value == ''){
			 		getObj("m_name").style.border = "1px solid red";
					alert('<?php echo JText::_( 'BLBE_JSMDNOT200' ); ?>');
			 	}else{
			 		submitform( pressbutton );
					return;
			 	}
			 }else{
				submitform( pressbutton );
					return;
			 }		
		}	
		
		function delete_logo(){
			getObj("logoiddiv").innerHTML = '';
		}	
		
		//-->
		</script>
		<script language="javascript" type="text/javascript">
		<!--
		function imposeMaxLength(Object, MaxLen)
		{
		  return (Object.value.length <= MaxLen);
		}
		-->
		</script> 
		<?php
		if(!count($row)){
			echo "<div id='system-message'>".JText::_('BLBE_NOITEMS')."</div>";
		}
		?>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<table>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_MAPNAME' ); ?>
					
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="m_name" id="m_name" value="<?php echo htmlspecialchars($row->m_name)?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo JText::_( 'BLBE_MAPIMAGE' ); ?>
					
				</td>
				<td>
					<input type="file" name="t_logo" /><input type="button" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" onclick="submitbutton('map_apply');" style="cursor:pointer;" />
					<br />
					<div id="logoiddiv">
					<?php
					
					if($row->map_img && is_file('../media/bearleague/'.$row->map_img)){
						
						$imgsize = getimagesize('../media/bearleague/'.$row->map_img);
						if($imgsize[0] > 200){
							$width = 200;
						}else{
							$width  = $imgsize[0];
						}
				
						echo '<img src="'.JURI::base().'../media/bearleague/'.$row->map_img.'" width="'.$width.'" />';
						echo '<input type="hidden" name="istlogo" value="1" />';
						?>
						<a href="javascript:void(0);" title="<?php echo JText::_( 'BLBE_REMOVE' ); ?>" onClick="javascript:delete_logo();"><img src="<?php echo JURI::base();?>components/com_joomsport/img/publish_x.png" title="Remove" /></a>
						</div>
					<?php	
					}
					?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_MAPDESCR' ); ?>
				</td>
				<td>
					<textarea name="map_descr" cols="60" rows="10" onkeypress="return imposeMaxLength(this, 150);"><?php echo htmlspecialchars($row->map_descr, ENT_QUOTES);?></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>