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

JHTML::_('behavior.tooltip');
	
		?>
		<script type="text/javascript">
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			 if(pressbutton == 'templ_apply' || pressbutton == 'templ_save'){
			 	if(form.name.value != ''){
					submitform( pressbutton );
					return;
				}else{
					getObj('tmpname').style.border = "1px solid red";
					alert("<?php echo JText::_('BLBE_JSMDNOT1');?>");
					
					
				}
			}else{
				submitform( pressbutton );
					return;
			}			
		}	
		
		</script>
		<script type="text/javascript" src="components/com_joomsport/color_piker/201a.js"></script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
		
		<table>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_TEMPLNAME' ); ?>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="name" id="tmpname" value="<?php echo $this->row->name?>" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_TEMPLNAME' ); ?>
					<div id="colorpicker201" class="colorpicker201"></div>
				</td>
				<td>
					<input type="button" style="cursor:pointer;" onclick="showColorGrid2('variable1','sample_1');" value="..." />&nbsp;
					<input type="text" ID="variable1" name="variable1" size="9" value="<?php echo $this->row->variable1?>" />
					<input type="text" ID="sample_1" size="1" value="" style="background-color:<?php echo $this->row->variable1?>" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_TEMPLNAME' ); ?>
					
				</td>
				<td>
					<input type="button" style="cursor:pointer;" onclick="showColorGrid2('variable2','sample_2');" value="..." />&nbsp;
					<input type="text" ID="variable2" name="variable2" size="9" value="<?php echo $this->row->variable2?>" />
					<input type="text" ID="sample_2" size="1" value="" style="background-color:<?php echo $this->row->variable2?>" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_TEMPLNAME' ); ?>

				</td>
				<td>
					<input type="button" style="cursor:pointer;" onclick="showColorGrid2('variable3','sample_3');" value="..." />&nbsp;
					<input type="text" ID="variable3" name="variable3" size="9" value="<?php echo $this->row->variable3?>" />
					<input type="text" ID="sample_3" size="1" value="" style="background-color:<?php echo $this->row->variable3?>" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_TEMPLNAME' ); ?>

				</td>
				<td>
					<input type="button" style="cursor:pointer;" onclick="showColorGrid2('variable4','sample_4');" value="..." />&nbsp;
					<input type="text" ID="variable4" name="variable4" size="9" value="<?php echo $this->row->variable4?>" />
					<input type="text" ID="sample_4" size="1" value="" style="background-color:<?php echo $this->row->variable4?>" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_TEMPLNAME' ); ?>

				</td>
				<td>
					<input type="button" style="cursor:pointer;" onclick="showColorGrid2('variable5','sample_5');" value="..." />&nbsp;
					<input type="text" ID="variable5" name="variable5" size="9" value="<?php echo $this->row->variable5?>" />
					<input type="text" ID="sample_5" size="1" value="" style="background-color:<?php echo $this->row->variable5?>" />
				</td>
			</tr>
			<tr>
				<td width="120" valign="top">
					<?php echo JText::_( 'BLBE_TEMPLCSS' ); ?>

				</td>
				<td>
					<textarea cols="100" rows="40"><?php echo $this->row->content?></textarea>
				</td>
			</tr>
	
		</table>
		
		<input type="hidden" name="task" value="templ_list" />
		<input type="hidden" name="id" value="<?php echo $this->row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>