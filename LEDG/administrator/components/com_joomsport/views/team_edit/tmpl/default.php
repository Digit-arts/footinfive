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
		require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
		$etabs = new esTabs();
		?>
		<script type="text/javascript">
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			 if(pressbutton == 'team_apply' || pressbutton == 'team_save' || pressbutton == 'team_save_new'){
			 	if(form.t_name.value != ''){
					var srcListName = 'seas_all_add';
					var srcList = eval( 'form.' + srcListName );
					if(srcList){
						var srcLen = srcList.length;
					
						for (var i=0; i < srcLen; i++) {
								srcList.options[i].selected = true;
						}
					}
					submitform( pressbutton );
					return;
				}else{
					getObj('tmname').style.border = "1px solid red";
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
		
		function addplayer(){
			var tbl = getObj('add_pl');
			var row = tbl.insertRow(tbl.rows.length-1);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			
			var input_hd = document.createElement('input');
			input_hd.type = 'hidden';
			input_hd.name = 'teampl[]';
			input_hd.value = getObj('playerz_id').value;
			
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLBE_DELETE');?>"><img src="components/com_joomsport/img/publish_x.png"  bOrder="0" alt="Delete"></a>';
			cell1.appendChild(input_hd);
			cell2.innerHTML = getObj('playerz_id').options[getObj('playerz_id').selectedIndex].text;
			row.appendChild(cell1);
			row.appendChild(cell2);
			
		}
		function delete_logo(){
			getObj("logoiddiv").innerHTML = '';
		}
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div>
			<?php echo $lists['seasf'];?>
		</div>
		<!-- <tab box> -->
		<ul class="tab-box">
			<?php
			echo $etabs->newTab(JText::_( 'BLBE_MAIN' ),'main_team','','vis');
			echo $etabs->newTab(JText::_( 'BLBE_TABPLAYERS' ),'players_conf','');
			echo $etabs->newTab(JText::_( 'BLBE_BONUSES' ),'bonuses_conf','');

			?>
		</ul>	
		<div style="clear:both"></div>
		<div id="main_team_div" class="tabdiv">
		
		<table>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_TEAMNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TEAMNAME' ); ?>::<?php echo JText::_( 'BLBE_TT_TEAMNAME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="t_name" id="tmname" value="<?php echo htmlspecialchars($row->t_name)?>" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_CITY' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_CITY' ); ?>::<?php echo JText::_( 'BLBE_TT_CITY' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="t_city" value="<?php echo htmlspecialchars($row->t_city)?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_YTEAM' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_YTEAM' ); ?>::<?php echo JText::_( 'BLBE_TT_YTEAM' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="checkbox"  name="t_yteam" value="1" <?php if($row->t_yteam) echo "checked"?> />
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
				<td colspan="2">
				<em><?php echo JText::_( 'BLBE_EMPTYFIELD' ); ?></em>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo JText::_( 'BLBE_TEAM_LOGO' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TEAM_LOGO' ); ?>::<?php echo JText::_( 'BLBE_TT_TEAM_LOGO' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="file" name="t_logo" /><input type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" onclick="submitbutton('team_apply');" />
					<br />
					<div id="logoiddiv">
					<?php
					
					if($row->t_emblem && is_file('../media/bearleague/'.$row->t_emblem)){
						
						$imgsize = getimagesize('../media/bearleague/'.$row->t_emblem);
						if($imgsize[0] > 200){
							$width = 200;
						}else{
							$width  = $imgsize[0];
						}
				
						echo '<img src="'.JURI::base().'../media/bearleague/'.$row->t_emblem.'" width="'.$width.'" />';
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
				<td>
					<?php echo JText::_('BLBE_VENUE');?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_VENUE' ); ?>::<?php echo JText::_( 'BLBE_TT_VENUE' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
					
				</td>
				<td>
					<?php echo $lists["venue"];?>
					
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_ABOUT_TEAM' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ABOUT_TEAM' ); ?>::<?php echo JText::_( 'BLBE_TT_ABOUT_TEAM' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<?php echo $editor->display( 't_descr',  htmlspecialchars($row->t_descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				
				</td>
			</tr>
		</table>
		<?php 
		if(isset($lists["seasall"])){
		?>
		<table  bOrder="0">
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_ADD_SEASON' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ADD_SEASON' ); ?>::<?php echo JText::_( 'BLBE_TT_ADD_SEASON' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>

				</td>
				<td width="150">
					<?php echo $this->lists['seasall'];?>
				</td>
				<td valign="middle" width="60" align="center">
					<input type="button" style="cursor:pointer;" value=">>" onClick="javascript:JS_addSelectedToList('adminForm','seas_all','seas_all_add');" /><br />
					<input type="button" style="cursor:pointer;" value="<<" onClick="javascript:JS_delSelectedFromList('adminForm','seas_all_add','seas_all');" />
				</td>
				<td >
					<?php echo $this->lists['seasall_add'];?>
				</td>
			</tr>
		</table>
		<?php } ?>
		<div style="margin-top:10px;bOrder:1px solid #BBB;">
		<table style="padding:10px;">
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_UPLFOTO' ); ?>
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_1" value="" />
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_2" value="" />
				<br />
				<input type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" onclick="submitbutton('team_apply');" />
				</td>
			</tr>
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
		<?php
		}
		echo "</div></div>";
		echo '<div id="players_conf_div" class="tabdiv" style="display:none;">';
		echo '<table class="adminlist" id="add_pl">';
		echo '<tr>';
		echo '<th class="title" width="30">#</th>';
		echo '<th class="title">'.JText::_('BLBE_PLAYER').'</th>';
		echo '</tr>';
		for($i=0;$i<count($lists['team_players']);$i++){
			$pl = $lists['team_players'][$i];
			echo '<tr><td><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLBE_DELETE').'"><img src="components/com_joomsport/img/publish_x.png"  bOrder="0" alt="Delete"></a><input type="hidden" name="teampl[]" value="'.$pl->id.'" /></td><td>'.$pl->name.'</td></tr>';
		}
		?>
			<tr>
				<td colspan="2">
					<?php echo $lists['player']; ?>
					<input type="button" style="cursor:pointer;" value="<?php echo JText::_('BLBE_ADD');?>" onclick="addplayer();" />
				</td>
			</tr>
		</table>
		
		<?php
		
		echo "</div>";
		echo '<div id="bonuses_conf_div" class="tabdiv" style="display:none;">';
		echo '<table class="adminlist">';
		echo '<tr>';?>
		<th class="title"><?php echo JText::_( 'BLBE_SEASON' ); ?></th>
		<th class="title"><?php echo JText::_( 'BLBE_BONUSES' ); ?></th>
		<?php echo '</tr>';
		for($i=0;$i<count($lists['bonuses']);$i++){
			$bonuses = $lists['bonuses'][$i];
			echo '<tr><td><input type="hidden" name="sids[]" value="'.$bonuses->season_id.'" />'.$bonuses->name.'</td>';
			echo '<td><input type="text" name="bonuses[]" value="'.floatval($bonuses->bonus_point).'" />'.'</td></tr>';
		}
		?>
		</table>
		</div>
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="team_edit" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>