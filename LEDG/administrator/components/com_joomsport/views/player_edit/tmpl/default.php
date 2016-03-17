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
		<!--
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			 if(pressbutton == 'player_apply' || pressbutton == 'player_save' || pressbutton == 'player_save_new'){
			 	if(form.first_name.value == '' || form.last_name.value == ''){
			 		alert('<?php echo JText::_( 'BLBE_JSNOTICEPL' ); ?>');
			 	}else{
					var srcListName2 = 'in_teams';
					var srcList2 = eval( 'form.' + srcListName2 );
				
					var srcLen2 = srcList2.length;
				
					for (var i=0; i < srcLen2; i++) {
							srcList2.options[i].selected = true;
					} 
					
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
			echo "<div id='system-message'>".JText::_('BLBE_NOITEMS')."</div>";
		}
		?>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div><?php echo $lists['seasf'];?></div>
		<!-- <tab box> -->
		<ul class="tab-box">
		<?php
		echo $etabs->newTab(JText::_('BLBE_MAIN'),'main_pl','','vis');
		echo $etabs->newTab(JText::_('BLBE_BONUSES'),'bonuses_conf','');
		?>
		</ul>
		<div style="clear:both"></div>
		<div id="main_pl_div" class="tabdiv">
		<table>
			<tr>
				<td width="100">
					<?php echo JText::_( 'User' ); ?>
				</td>
				<td>
					<?php echo $lists['usrid'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_FIRSTNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_FIRSTNAME' ); ?>::<?php echo JText::_( 'BLBE_TT_FIRSTNAME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="first_name" value="<?php echo htmlspecialchars($row->first_name)?>" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_LASTNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_LASTNAME' ); ?>::<?php echo JText::_( 'BLBE_TT_LASTNAME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="last_name" value="<?php echo htmlspecialchars($row->last_name)?>" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_NICKNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_NICKNAME' ); ?>::<?php echo JText::_( 'BLBE_TT_NICKNAME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="nick" value="<?php echo htmlspecialchars($row->nick)?>" />
				</td>
			</tr>
			<!--tr>
				<td width="100">
					<?php //echo JText::_( 'BLBE_TEAM' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TEAM' ); ?>::<?php echo JText::_( 'BLBE_TT_TEAM' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php //echo $lists['teams'];?>
				</td>
			</tr-->
			<tr>
				<td width="150">
					<?php echo JText::_( 'BLBE_ADD_TEAMS' ); ?>
				</td>
				<td>	
					<table><tr>
					<td width="150">
						<?php echo $lists['allteams'];?>
					</td>
					<td valign="middle" width="60" align="center">
						<input type="button" style="cursor:pointer;" value=">>" onClick="javascript:JS_addSelectedToList('adminForm','allteams','in_teams');" /><br />
						<input type="button" style="cursor:pointer;" value="<<" onClick="javascript:JS_delSelectedFromList('adminForm','in_teams','allteams');" />
					</td>
					<td >
						<?php echo $lists['in_teams'];?>
					</td>
					</tr></table>
				</td>	
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_COUNTRY' ); ?>
				</td>
				<td>
					<?php echo $lists['country']?>
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
					<input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id?>]" value="<?php echo $lists['ext_fields'][$p]->field_type?>" />
					<input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id?>]" value="<?php echo $lists['ext_fields'][$p]->id?>" />
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
				<td width="100">
					<?php echo JText::_( 'BLBE_ABPLAYER' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ABPLAYER' ); ?>::<?php echo JText::_( 'BLBE_TT_ABPLAYER' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $editor->display( 'about',  htmlspecialchars($row->about, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				</td>
			</tr>
			
		</table>
		<div style="margin-top:10px;bOrder:1px solid #BBB;">
		<table style="padding:10px;">
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_UPLPHTOPLYR' ); ?>
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_1" value="" />
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_2" value="" /><br /><input type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" onclick="submitbutton('player_apply');" />
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
				<a rel="{handler: 'image'}" href="<?php echo JURI::base();?>../media/bearleague/<?php echo $photos->filename?>" title="Image" class="modal-button"><img src="<?php echo JURI::base();?>../media/bearleague/<?php echo $photos->filename?>" width="<?php echo $width;?>" /></a>
			</td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
		}
		echo '</div></div>';
		echo '<div id="bonuses_conf_div" class="tabdiv" style="display:none;">';
		echo '<table class="adminlist">';
		echo '<tr>';
		echo '<th class="title">'.JText::_('BLBE_MENSEASON').'</th>';
		echo '<th class="title">'.JText::_('BLBE_BONUSES').'</th>';
		echo '</tr>';
		for($i=0;$i<count($lists['bonuses']);$i++){
			$bonuses = $lists['bonuses'][$i];
			echo '<tr><td><input type="hidden" name="sids[]" value="'.$bonuses->season_id.'" />'.$bonuses->name.'</td>';
			echo '<td><input type="text" name="bonuses[]" value="'.floatval($bonuses->bonus_point).'" />'.'</td></tr>';
		}
		?>
		</table>
		</div>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="player_edit" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>