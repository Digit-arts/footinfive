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
require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
		$etabs = new esTabs(); 
		?>
		<script type="text/javascript" src="components/com_joomsport/color_piker/201a.js"></script>
		<script type="text/javascript">
		var colors_count = parseInt('<?php echo count($this->lists['colors'])?count($this->lists['colors']):1?>');
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'season_save' || pressbutton == 'season_apply') {
				if(form.s_name.value != ''){
				
					if( '<?php echo $this->lists['t_type'];?>' == 0 && (form.s_win_point.value == '' || form.s_draw_point.value == '' || form.s_lost_point.value == '' || (getObj('s_enbl_extra').checked && (form.s_extra_win.value == '' || form.s_extra_lost.value == '')))){
						alert("<?php echo JText::_( 'BLBE_JSMDNOT8' ); ?>");

					}else
					if(form.t_id.value != "0"){
						if( getObj("s_reg0").checked || form.reg_start.value == '0000-00-00 00:00:00' || form.reg_end.value == '0000-00-00 00:00:00' || form.reg_end.value == '' || form.reg_start.value == '' || form.reg_start.value < form.reg_end.value){
							var srcListName = 'teams_season';
							var srcList = eval( 'form.' + srcListName );
						
							var srcLen = srcList.length;
						
							for (var i=0; i < srcLen; i++) {
									srcList.options[i].selected = true;
							} 
							
							var srcListName2 = 'usr_admins';
							var srcList2 = eval( 'form.' + srcListName2 );
						
							var srcLen2 = srcList2.length;
						
							for (var i=0; i < srcLen2; i++) {
									srcList2.options[i].selected = true;
							} 
							
												
							submitform( pressbutton );
							return;
						}else{
							alert("<?php echo JText::_( 'BLBE_JSMDNOT99' ); ?>");	
						}						
					}else{	
						alert("<?php echo JText::_( 'BLBE_JSMDNOT9' ); ?>");	
					}
				}else{
					getObj("easname").style.border = "1px solid red";
					alert("<?php echo JText::_( 'BLBE_JSMDNOT10' ); ?>");	
				}				
			}else{
				submitform( pressbutton );
					return;
			}
		}	
		
		function showopt(){
			if(getObj('s_enbl_extra').checked){
				getObj('extraoptions').style.visibility = 'visible';
			}else{
				getObj('extraoptions').style.visibility = 'hidden';
			}
		}
		
		function add_colors(){
			var cell = document.createElement("div");
			colors_count++;
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = 'input_field_'+colors_count;
			input_hidden.id = 'input_field_'+colors_count;
			input_hidden.value = '';
			input_hidden.size = 9;
			var input_hidden2 = document.createElement("input");
			input_hidden2.type = "text";
			input_hidden2.id = 'sample_'+colors_count;
			input_hidden2.value = '';
			input_hidden2.size = 1;
			var input_hidden3 = document.createElement("input");
			input_hidden3.type = "text";
			input_hidden3.name = 'place_'+colors_count;
			input_hidden3.value = '';
			input_hidden3.size = 5;
			cell.innerHTML = '<?php echo JText::_('BLBE_COLORS');?>: <input type="button" style="cursor:pointer;" onclick="showColorGrid2(\'input_field_'+colors_count+'\',\'sample_'+colors_count+'\');" value="...">&nbsp;';
			
			var txtnode2 = document.createTextNode(" <?php echo JText::_('BLBE_PLACE');?>: ");
			cell.appendChild(input_hidden);
			cell.appendChild(input_hidden2);
			cell.appendChild(txtnode2);
			
			cell.appendChild(input_hidden3);
			
			getObj('app_newcol').appendChild(cell);
			document.adminForm.col_count.value = colors_count;
		}
		
		
		function bl_add_map(){
			var cur_map = getObj('maps_id');
			
			if (cur_map.value == 0) {
				alert("<?php echo JText::_('BLBE_JSMDNOT201')?>");return;
			}
		
			
			var tbl_elem = getObj('map_tbl');
			var row = tbl_elem.insertRow(tbl_elem.rows.length-1);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			
			
			
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_$this->row(this); return false;" title="Delete"><img src="components/com_joomsport/img/publish_x.png"  border="0" alt="Delete"></a>';
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "maps_s[]";
			input_hidden.value = cur_map.value;
			cell2.innerHTML = cur_map.options[cur_map.selectedIndex].text;
			cell2.appendChild(input_hidden);
			
			
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			
			getObj('maps_id').value =  0;
		
		}
		
		function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
		}
		//-->
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
		<?php
	echo $etabs->newTab(JText::_( 'BLBE_MAIN' ),'main_conf','','vis');
	echo $etabs->newTab(JText::_( 'BLBE_JOOMSOPT' ),'esport_conf','');
	if($this->lists['t_type'] == 0){
		echo $etabs->newTab(JText::_( 'BLBE_TTCOLOR' ),'col_conf','');
	}
	
	?>
		<div style="clear:both"></div>
		<div id="main_conf_div" class="tabdiv">
		<table>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_SEASONNAME' ); ?>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="s_name" id="easname" value="<?php echo $this->row->s_name?>" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_TOURNAMENT' ); ?>
				</td>
				<td>
					<?php echo $this->lists['tourn'];?>
				</td>
			</tr>
			
		</div>
		<div id="esport_conf_div" class="tabdiv" style="display:none;">
		<table>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_SETNUMPART' ); ?>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_participant" value="<?php echo $this->row->s_participant?>" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_UNMLREG' ); ?>
				</td>
				<td>
					<?php echo $this->lists['enbl_reg'];?>
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_STARTREG' ); ?>
				</td>
				<td>
					<?php
					echo JHTML::_('calendar', $this->row->reg_start, 'reg_start', 'reg_start', '%Y-%m-%d %H:00:00', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); 
					?>
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_ENDREG' ); ?>
				</td>
				<td>
					<?php
					echo JHTML::_('calendar', $this->row->reg_end, 'reg_end', 'reg_end', '%Y-%m-%d %H:00:00', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); 
					?>
				</td>
			</tr>
		</table>
		<table class="adminlist"  border="0" id="map_tbl">
			<tr>
				<th width="30">#</th>
				<th>
					<?php echo JText::_( 'BLBE_MAPS' ); ?>
				</th>
			</tr>
			<?php
			for($i=0;$i<count($this->lists['cur_maps']);$i++){
				?>
				<tr>
					<td><a href="javascript: void(0);" onClick="javascript:Delete_tbl_$this->row(this); return false;" title="Delete"><img src="components/com_joomsport/img/publish_x.png"  border="0" alt="Delete"></a></td>
					<td><?php echo $this->lists['cur_maps'][$i]->m_name?><input type="hidden" name="maps_s[]" value="<?php echo $this->lists['cur_maps'][$i]->id?>" /></td>
				</tr>
				<?php
			}
			?>
			<tr>	
				<td colspan="2">
					<?php echo $this->lists['maps'];?>
					<input type="button" style="cursor:pointer;" value="<?php echo JText::_('BLBE_ADD');?>" onclick="bl_add_map();" />
				</td>
				
			</tr>
		</table>
		<br />
		<table>	
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_RULES' ); ?>
				</td>
				<td>
					<?php echo $editor->display( 's_rules',  htmlspecialchars($this->row->s_rules, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				</td>
			</tr>
		</table>
		</div>
		<?php if($this->lists['t_type'] == 0){?>
		<div id="col_conf_div" class="tabdiv" style="display:none;">
		<div style="background-color:#eee;"><?php echo JText::_( 'BLBE_HIGHLIGHT' ); ?></div>
		<br />
		<table>
			<tr>
				<td>
					<div id="colorpicker201" class="colorpicker201"></div>
				</td>
			</tr>
			<tr>
				<td id="app_newcol">
					<?php if(!count($this->lists['colors'])){?>
					<div>
						<?php echo JText::_( 'BLBE_COLORS' ); ?><span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_COLORS' ); ?>::<?php echo JText::_( 'BLBE_TT_COLORS' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span> 
						<input type="button" style="cursor:pointer;" onclick="showColorGrid2('input_field_1','sample_1');" value="...">&nbsp;<input type="text" ID="input_field_1" name="input_field_1" size="9" value=""><input type="text" ID="sample_1" size="1" value="" />
						<?php echo JText::_( 'BLBE_PLACE' ); ?> <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLACE' ); ?>::<?php echo JText::_( "BLBE_TT_PLACE" );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
						<input type="text" ID="place_1" name="place_1" size="5" value="" />
					</div>
					<?php
					}else{
						$m = 0;
						foreach ($this->lists['colors'] as $colores){
							$m++;
					?>
						<div>
							<?php echo JText::_( 'BLBE_COLORS' ); ?>

							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_COLORS' ); ?>::<?php echo JText::_( 'BLBE_TT_COLORS' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span> 
							<input type="button" style="cursor:pointer;" onclick="showColorGrid2('input_field_<?php echo $m?>','sample_<?php echo $m?>');" value="...">&nbsp;<input type="text" ID="input_field_<?php echo $m?>" name="input_field_<?php echo $m?>" size="9" value="<?php echo $colores->color?>"><input type="text" ID="sample_<?php echo $m?>" size="1" value="" style="background-color:<?php echo $colores->color?>" />
							<?php echo JText::_( 'BLBE_PLACE' ); ?> 


							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLACE' ); ?>::<?php echo JText::_( 'BLBE_TT_PLACES' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
							<input type="text" ID="place_<?php echo $m?>" name="place_<?php echo $m?>" size="5" value="<?php echo $colores->place?>" />
						</div>
					<?php	
						}
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
				<input type="hidden" name="col_count" value="<?php echo count($this->lists['colors'])?count($this->lists['colors']):1?>" />
				<input type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_NEWCOLOR' ); ?>" onclick="javascript:add_colors();" />
				</td>
			</tr>
		</table>
		</div>
		<?php } ?>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="s_id" value="<?php echo $this->row->s_id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>