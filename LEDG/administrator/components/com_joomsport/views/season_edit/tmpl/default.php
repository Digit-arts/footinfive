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
			if (pressbutton == 'season_save' || pressbutton == 'season_apply' || pressbutton == 'season_save_new') {
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
			
			
			
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_$this->row(this); return false;" title="<?php echo JText::_('BLBE_DELETE');?>"><img src="components/com_joomsport/img/publish_x.png"  bOrder="0" alt="Delete"></a>';
			
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
		<!-- <tab box> -->
		<ul class="tab-box">
			<?php
			echo $etabs->newTab(JText::_( 'BLBE_MAIN' ),'main_conf','','vis');
			echo $etabs->newTab(JText::_( 'BLBE_JOOMSOPT' ),'esport_conf','');
			if($this->lists['t_type'] == 0){
				echo $etabs->newTab(JText::_( 'BLBE_TTCOLOR' ),'col_conf','');
			}
			if(count($lists["teams_regs"])){
				echo $etabs->newTab(JText::_( 'BLBE_PARTREGFROMFE' ),'partr_conf','');
			}
			if($lists["is_betting"]){
				echo $etabs->newTab(JText::_( 'BLBE_BET_OPTIONS' ),'bet_option','');
			}
			?>
		</ul>	
		<div style="clear:both"></div>
		<div id="main_conf_div" class="tabdiv">
		<table>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_SEASONNAME' ); ?>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="s_name" id="easname" value="<?php echo htmlspecialchars($this->row->s_name)?>" />
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
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_PUBLISH' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PUBLISH' ); ?>::<?php echo JText::_( 'BLBE_PUBLISH' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $this->lists['published'];?>
				</td>
			</tr>
			<?php if($this->lists['t_type'] == 0){?>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_GROUPS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_GROUPS' ); ?>::<?php echo JText::_( 'BLBE_TT_GROUPS' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $this->lists['s_groups'];?>
				</td>
			</tr>
			
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_WPH' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_WPH' ); ?>::<?php echo JText::_( 'BLBE_TT_WPH' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_win_point" value="<?php echo floatval($this->row->s_win_point)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_WPA' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_WPA' ); ?>::<?php echo JText::_( 'BLBE_TT_WPA' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_win_away" value="<?php echo floatval($this->row->s_win_away)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_DPH' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_DPH' ); ?>::<?php echo JText::_( 'BLBE_TT_DPH' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_draw_point" value="<?php echo floatval($this->row->s_draw_point)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_DPA' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_DPA' ); ?>::<?php echo JText::_( 'BLBE_TT_DPA' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_draw_away" value="<?php echo floatval($this->row->s_draw_away)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_LPH' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_LPH' ); ?>::<?php echo JText::_( 'BLBE_TT_LPH' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_lost_point" value="<?php echo floatval($this->row->s_lost_point)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_LPA' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_LPA' ); ?>::<?php echo JText::_( 'BLBE_TT_LPA' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_lost_away" value="<?php echo floatval($this->row->s_lost_away)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
				</td>
			</tr>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_EXTIME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_EXTIME' ); ?>::<?php echo JText::_( 'BLBE_TT_EXTIME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="checkbox" name="s_enbl_extra" id="s_enbl_extra" value="1" onclick="javascript:showopt();" <?php if($this->row->s_enbl_extra) { echo "checked";}?> />
				</td>
			</tr>
			<tr>
				<td id="extraoptions" colspan="2" <?php if(!$this->row->s_enbl_extra){echo "style='visibility:hidden';";}?>>	
					<table cellpadding="1" cellspacing="0">	
						<tr>
							<td width="150">
								<?php echo JText::_( 'BLBE_WPEXTIME' ); ?>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_WPEXTIME' ); ?>::<?php echo JText::_( 'BLBE_TT_WPEXTIME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
							</td>
							<td>
								<input type="text" maxlength="5" size="10" name="s_extra_win" value="<?php echo floatval($this->row->s_extra_win)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
							</td>
						</tr>
						<tr>
							<td width="150">
								<?php echo JText::_( 'BLBE_LPEXTIME' ); ?>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_LPEXTIME' ); ?>::<?php echo JText::_( 'BLBE_TT_LPEXTIME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
							</td>
							<td>
								<input type="text" maxlength="5" size="10" name="s_extra_lost" value="<?php echo floatval($this->row->s_extra_lost)?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
							</td>
						</tr>
					</table>		
				</td>		
			</tr>
			<?php }else{ ?>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_EXTIME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_EXTIME' ); ?>::<?php echo JText::_( 'BLBE_TT_EXTIME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="checkbox" name="s_enbl_extra" id="s_enbl_extra" value="1" <?php if($this->row->s_enbl_extra) { echo "checked";}?> />
				</td>
			</tr>
			<?php } ?>
			<?php
			for($p=0;$p<count($this->lists['ext_fields']);$p++){
			if($this->lists['ext_fields'][$p]->field_type == '3' && !isset($this->lists['ext_fields'][$p]->selvals)){
			}else{
			?>
			<tr>
				<td width="100">
					<?php echo $this->lists['ext_fields'][$p]->name;?>
				</td>
				<td>
					<?php
					
						switch($lists['ext_fields'][$p]->field_type){
								
							case '1':	echo $lists['ext_fields'][$p]->selvals;
										break;
							case '2':	echo $editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;
										break;
							case '3':	echo $lists['ext_fields'][$p]->selvals;
										break;	
							case '0':					
							default:	echo '<input type="text" maxlength="255" size="60" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):"").'" />';		
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
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_ABOUT_SEASON' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ABOUT_SEASON' ); ?>::<?php echo JText::_( 'BLBE_TT_ABOUT_SEASON' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $editor->display( 's_descr',  htmlspecialchars($this->row->s_descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				</td>
			</tr>
		</table>
		
		<table  bOrder="0">
			<tr>
				<td width="150">
					<?php echo $this->lists['tourntype']?JText::_( 'BLBE_ADDPARTIC' ):JText::_( 'BLBE_ADDTEAMS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo $this->lists['tourntype']?JText::_( 'BLBE_ADDPARTIC' ):JText::_( 'BLBE_ADDTEAMS' ); ?>::<?php echo JText::_( 'BLBE_TT_ADD_TEAMS' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td width="150">
					<?php echo $this->lists['teams'];?>
				</td>
				<td valign="middle" width="60" align="center">
					<input type="button" style="cursor:pointer;" value=">>" onClick="javascript:JS_addSelectedToList('adminForm','teams_id','teams_season');" /><br />
					<input type="button" style="cursor:pointer;" value="<<" onClick="javascript:JS_delSelectedFromList('adminForm','teams_season','teams_id');" />
				</td>
				<td >
					<?php echo $this->lists['teams2'];?>
				</td>
			</tr>
		</table>
		<br />
		<table  bOrder="0">
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_ADD_MOD' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ADD_MOD' ); ?>::<?php echo JText::_( 'BLBE_TT_ADD_MOD' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>

				</td>
				<td width="150">
					<?php echo $this->lists['usrlist'];?>
				</td>
				<td valign="middle" width="60" align="center">
					<input type="button" style="cursor:pointer;" value=">>" onClick="javascript:JS_addSelectedToList('adminForm','usracc_id','usr_admins');" /><br />
					<input type="button" style="cursor:pointer;" value="<<" onClick="javascript:JS_delSelectedFromList('adminForm','usr_admins','usracc_id');" />
				</td>
				<td >
					<?php echo $this->lists['usrlist_vyb'];?>
				</td>
			</tr>
		</table>
		<br />
		<?php if($this->lists['t_type'] == 0){?>
		<table>
			<tr>
				<td colspan="2" style="font-weight:bold"><?php echo JText::_( 'BLBE_TOURN_TABLE' ); ?><span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TOURN_TABLE' ); ?>::<?php echo JText::_( 'BLBE_TT_TOURN_TABLE' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_PLAYED');?></td>
				<td align="right"><input type="checkbox" name="soptions['played_chk']" value="1" <?php echo @$this->lists["soptions"]['played_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo $this->lists['t_single']?JText::_('BLBE_PARTICS_EMBLEM'):JText::_('BLBE_TEAMEMBL');?></td>
				<td align="right"><input type="checkbox" name="soptions['emblem_chk']" value="1" <?php echo @$this->lists["soptions"]['emblem_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTWC');?></td>
				<td align="right"><input type="checkbox" name="soptions['win_chk']" value="1" <?php echo @$this->lists["soptions"]['win_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTLC');?></td>
				<td align="right"><input type="checkbox" name="soptions['lost_chk']" value="1" <?php echo @$this->lists["soptions"]['lost_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTDC');?></td>
				<td align="right"><input type="checkbox" name="soptions['draw_chk']" value="1" <?php echo @$this->lists["soptions"]['draw_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTEWC');?></td>
				<td align="right"><input type="checkbox" name="soptions['otwin_chk']" value="1" <?php echo @$this->lists["soptions"]['otwin_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTELC');?></td>
				<td align="right"><input type="checkbox" name="soptions['otlost_chk']" value="1" <?php echo @$this->lists["soptions"]['otlost_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTDIFC');?></td>
				<td align="right"><input type="checkbox" name="soptions['diff_chk']" value="1" <?php echo @$this->lists["soptions"]['diff_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTGDC');?></td>
				<td align="right"><input type="checkbox" name="soptions['gd_chk']" value="1" <?php echo @$this->lists["soptions"]['gd_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTPC');?></td>
				<td align="right"><input type="checkbox" name="soptions['point_chk']" value="1" <?php echo @$this->lists["soptions"]['point_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTWPC');?></td>
				<td align="right"><input type="checkbox" name="soptions['percent_chk']" value="1" <?php echo @$this->lists["soptions"]['percent_chk']?'checked':'';?> /></td>
			</tr>
			
			<tr>
				<td><?php echo JText::_('BLBE_TTGSC');?></td>
				<td align="right"><input type="checkbox" name="soptions['goalscore_chk']" value="1" <?php echo @$this->lists["soptions"]['goalscore_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTGCC');?></td>
				<td align="right"><input type="checkbox" name="soptions['goalconc_chk']" value="1" <?php echo @$this->lists["soptions"]['goalconc_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTWHC');?></td>
				<td align="right"><input type="checkbox" name="soptions['winhome_chk']" value="1" <?php echo @$this->lists["soptions"]['winhome_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTWAC');?></td>
				<td align="right"><input type="checkbox" name="soptions['winaway_chk']" value="1" <?php echo @$this->lists["soptions"]['winaway_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTDHC');?></td>
				<td align="right"><input type="checkbox" name="soptions['drawhome_chk']" value="1" <?php echo @$this->lists["soptions"]['drawhome_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTDAC');?></td>
				<td align="right"><input type="checkbox" name="soptions['drawaway_chk']" value="1" <?php echo @$this->lists["soptions"]['drawaway_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTLHC');?></td>
				<td align="right"><input type="checkbox" name="soptions['losthome_chk']" value="1" <?php echo @$this->lists["soptions"]['losthome_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTLAC');?></td>
				<td align="right"><input type="checkbox" name="soptions['lostaway_chk']" value="1" <?php echo @$this->lists["soptions"]['lostaway_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTPHC');?></td>
				<td align="right"><input type="checkbox" name="soptions['pointshome_chk']" value="1" <?php echo @$this->lists["soptions"]['pointshome_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_TTPAC');?></td>
				<td align="right"><input type="checkbox" name="soptions['pointsaway_chk']" value="1" <?php echo @$this->lists["soptions"]['pointsaway_chk']?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_OPWININGROUP');?></td>
				<td align="right"><input type="checkbox" name="soptions['grwin_chk']" value="1" <?php echo (isset($this->lists["soptions"]['grwin_chk']) && $this->lists["soptions"]['grwin_chk'])?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_OPLOSTINGROUP');?></td>
				<td align="right"><input type="checkbox" name="soptions['grlost_chk']" value="1" <?php echo (isset($this->lists["soptions"]['grlost_chk']) && $this->lists["soptions"]['grlost_chk'])?'checked':'';?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_OPPERINGROUP');?></td>
				<td align="right"><input type="checkbox" name="soptions['grwinpr_chk']" value="1" <?php echo (isset($this->lists["soptions"]['grwinpr_chk']) && $this->lists["soptions"]['grwinpr_chk'])?'checked':'';?> /></td>
				
			</tr>
		</table>
		<br />
		<table class="admin">
			<tr>
				<th colspan="2"><?php echo JText::_('BLBE_RANK_CRIT');?></th>
			</tr>
			<tr>
				<td><?php echo JText::_('BLBE_RANK_EQUAL');?></td>
				<td><input type="checkbox" name="soptions['equalpts_chk']" value="1" <?php echo $this->lists['equalpts_chk']?'checked':'';?> /></td>
			</tr>
			<?php
			for($i=0;$i<4;$i++){
				echo '<tr>';
				echo '<td>'.JHTML::_('select.genericlist',   $this->lists['sortfield'], 'sortfield[]', 'class="inputbox"', 'id', 'name', ((isset($this->lists['savedsort'][$i]->sort_field))?$this->lists['savedsort'][$i]->sort_field:0) ).'</td>';
				echo '<td>'.JHTML::_('select.genericlist',   $this->lists['sortway'], 'sortway[]', 'class="inputbox"', 'id', 'name', ((isset($this->lists['savedsort'][$i]->sort_way))?$this->lists['savedsort'][$i]->sort_way:0) ).'</td>';
				echo '</tr>';
			}
			?>
		</table>
		<?php } ?>
		</div>
		<div id="esport_conf_div" class="tabdiv" style="display:none;">
		<table>
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_SETNUMPART' ); ?>
				</td>
				<td>
					<input type="text" maxlength="5" size="10" name="s_participant" value="<?php echo $this->row->s_participant?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);"  />
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
		<table class="adminlist"  bOrder="0" id="map_tbl">
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
					<td><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLBE_DELETE');?>"><img src="components/com_joomsport/img/publish_x.png"  bOrder="0" alt="Delete"></a></td>
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
						<?php echo JText::_( 'BLBE_COLORS' ); ?><span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_COLORS' ); ?>::<?php echo JText::_( 'BLBE_TT_COLORS' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
						<input type="button" style="cursor:pointer;" onclick="showColorGrid2('input_field_1','sample_1');" value="...">&nbsp;<input type="text" ID="input_field_1" name="input_field_1" size="9" value=""><input type="text" ID="sample_1" size="1" value="" />
						<?php echo JText::_( 'BLBE_PLACE' ); ?> <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLACE' ); ?>::<?php echo JText::_( "BLBE_TT_PLACE" );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
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

							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_COLORS' ); ?>::<?php echo JText::_( 'BLBE_TT_COLORS' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
							<input type="button" style="cursor:pointer;" onclick="showColorGrid2('input_field_<?php echo $m?>','sample_<?php echo $m?>');" value="...">&nbsp;<input type="text" ID="input_field_<?php echo $m?>" name="input_field_<?php echo $m?>" size="9" value="<?php echo $colores->color?>"><input type="text" ID="sample_<?php echo $m?>" size="1" value="" style="background-color:<?php echo $colores->color?>" />
							<?php echo JText::_( 'BLBE_PLACE' ); ?> 


							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLACE' ); ?>::<?php echo JText::_( 'BLBE_TT_PLACES' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
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
		<?php if(count($lists["teams_regs"])) {?>
		<div id="partr_conf_div" class="tabdiv" style="display:none;">
			<table class="adminlist">
				<?php foreach($lists["teams_regs"] as $trg){?>
					<tr>
						<td>
							<a href="javascript: void(0);" onClick="javascript:JS_del_REGFE('teams_season','<?php echo $trg->id?>'); Delete_tbl_row(this); return false;" <?php echo JText::_('BLBE_DELETE');?>><img src="components/com_joomsport/img/publish_x.png"  bOrder="0" alt="Delete"></a>
						</td>
						<td>
							<?php echo $trg->t_name;?>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<?php } ?>
		<?php if($lists["is_betting"]) {?>
		    <div id="bet_option_div" class="tabdiv">
				<table>
				<tr>
					<td width="150"><?php echo JText::_('BLBE_BET_SELECT_TEMPLATE')?>:</td>
					<td><?php echo $this->lists['templates']?></td>
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