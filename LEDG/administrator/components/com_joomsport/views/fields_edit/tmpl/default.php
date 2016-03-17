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
			 if(pressbutton == 'fields_apply' || pressbutton == 'fields_save' || pressbutton == 'fields_save_new'){
			 	if(form.name.value != ''){
					
					var selnames = eval("document.adminForm['selnames[]']");
					
				
						
					if(!selnames && getObj('field_type').value==3){
						getObj('addsel').style.border = "1px solid red";
					}else{
						submitform( pressbutton );
						return;
					}	
				}else{
					getObj('fldname').style.border = "1px solid red";
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
		
		function add_selval(){
			if(!getObj("addsel").value){
				return false;
			}
			var tbl_elem = getObj("seltable");
			var row = tbl_elem.insertRow(tbl_elem.rows.length - 2);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			
			cell1.innerHTML = '<input type="hidden" name="adeslid[]" value="0" /><a href="javascript:void(0);" title="Remove" onClick="javascript:Delete_tbl_row(this);"><input type="hidden" value="0" name="selid[]" /><img src="<?php echo JURI::base()?>components/com_joomsport/img/publish_x.png" title="Remove" /></a>';
			
			var inp = document.createElement('input');
			inp.type="text";
			inp.setAttribute('maxlength',255);
			inp.value = getObj("addsel").value;
			inp.name = 'selnames[]';
			inp.setAttribute('size',50);
			cell2.appendChild(inp);
			row.appendChild(cell1);
			row.appendChild(cell2);
			
			getObj("addsel").value = '';
		}
		function shide(){
			if(getObj('field_type').value==3){
				getObj("seltable").style.display='block';
			}else{
				getObj("seltable").style.display='none';
			}
		}
		function tblview_hide(){
			if(getObj('type').value < 2){
				getObj("tbl_fv_1").style.visibility='visible';
				getObj("tbl_fv_2").style.visibility='visible';
			}else{
				getObj("tbl_fv_1").style.visibility='hidden';
				getObj("tbl_fv_2").style.visibility='hidden';
			}
		}
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
		
		<table>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_FIELDNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_FIELDNAME' ); ?>::<?php echo JText::_( 'BLBE_TT_FIELDNAME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="name" id="fldname" value="<?php echo htmlspecialchars($row->name)?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'Publish' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Publish' ); ?>::<?php echo JText::_( 'Publishing' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $lists['published'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_FIELDTYP' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_FIELDTYP' ); ?>::<?php echo JText::_( 'BLBE_TT_FIELDTYP' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $lists['field_type'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_FEVENTTYPE' ); ?>
				</td>
				<td>
					<?php echo $lists['is_type'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_VISFOR' ); ?>
				</td>
				<td>
					<?php echo $lists['faccess'];?>
				</td>
			</tr>
			<?php
			$stf = 'style="visibility:visible;"';
			if($row->type >= 2){
				$stf = 'style="visibility:hidden;"';
			}
			?>
			<tr>
				<td width="100" id="tbl_fv_1" <?php echo $stf;?>>
					<?php echo JText::_( 'BLBE_FIELDTABVIEW' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_FIELDTABVIEW' ); ?>::<?php echo JText::_( "BLBE_TT_FIELDTABVIEW" );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td id="tbl_fv_2" <?php echo $stf;?>>
					<?php echo $lists['t_view'];?>
				</td>
			</tr>
			
			
		</table>
		<br />
		<?php
		$st = 'style="display:none;"';
		if($row->field_type == '3'){
			$st = 'style="display:block;"';
		}
		?>
		<table id="seltable" <?php echo $st?>>
			<tr>
				<th>#</th>
				<th><?php echo JText::_( 'NAME' ); ?></th>
			</tr>
			<?php
			for($i=0;$i<count($lists['selval']);$i++){
				echo "<tr>";
				echo '<td><input type="hidden" name="adeslid[]" value="'.$lists['selval'][$i]->id.'" /><a href="javascript:void(0);" title="Remove" onClick="javascript:Delete_tbl_row(this);"><img src="'.JURI::base().'components/com_joomsport/img/publish_x.png" title="Remove" /></a></td>';
				echo "<td><input type='text' name='selnames[]' size='50' value='".htmlspecialchars(stripslashes($lists['selval'][$i]->sel_value),ENT_QUOTES)."' /></td>";
				echo "</tr>";
			}
			?>
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
			<tr>
				<th><input type="button" style="cursor:pointer;" value="<?php echo JText::_('BLBE_ADDCHOICE');?>" onclick="add_selval();" /></th>
				<th><input type="text" maxlength="255" size="50" name="addsel" value="" id="addsel" /></th>
			</tr>
		</table>
		
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>