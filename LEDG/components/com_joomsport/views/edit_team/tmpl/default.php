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
<script type="text/javascript">
function bl_submit(task,chk){
	if(chk == 1 && document.adminForm.boxchecked.value == 0){
		alert('<?php echo JText::_('BLFA_SELECTITEM')?>');
	}else{
		document.adminForm.task.value = task;
		document.adminForm.submit();	
	}
}
function delete_logo(){
			getObj("logoiddiv").innerHTML = '';
		}	
</script>

<?php
echo $lists["panel"];
?>

<!-- <module middle> -->
<div class="module-middle solid">
	
	<!-- <back box> -->
	<!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
	<!-- </back box> -->
	<div class="title-box padd-bot">
			<h2>
				<?php echo $row->id?JText::_('BLFA_TEAM_EDIT'):JText::_('BLFA_NTEAM');?>
			</h2>
	</div>
	<!-- <tab box> -->
		<ul class="tab-box">
			<?php
			require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
			$etabs = new esTabs();
			echo $etabs->newTab(JText::_('BLFA_TEAM'),'etab_team','star','vis');
			echo $etabs->newTab(JText::_('BLFA_PLAYER'),'etab_pl','players');
			?>
		</ul>
		<!-- </tab box> -->
	
</div>
<!-- </module middle> -->
<!-- <control bar> -->
<div class="control-bar-wr dotted">
	<ul class="control-bar">
		<li><a class="save" href="#" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('team_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
		<li><a class="delete" href="<?php echo JRoute::_("index.php?option=com_joomsport&controller=admin&view=admin_team&sid=".$lists["s_id"]."&Itemid=".$Itemid);?>" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
		<!-- <li><a class="save" href="#" title="Save">Save</a></li>
		<li><a class="apply" href="#" title="Apply">Apply</a></li> -->
	</ul>
</div>
<!-- </control bar> -->
<!-- <content module> -->
	<div class="content-module admin-mo-co">

<?php 

		//$editor =& JFactory::getEditor();
		JHTML::_('behavior.tooltip');
		?>
		<script type="text/javascript">
		
		function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
		}	
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'team_save' || pressbutton == 'team_apply') {
				
				
				if(form.t_name.value != ""){
				
					submitform( pressbutton );
					return;
				}else{	
					alert("<?php echo JText::_("BLFA_ENTERNAME")?>");	
				}
			}else{
				submitform( pressbutton );
					return;
			}
		}	
		function getObj(name) {

		  if (document.getElementById)  {  return document.getElementById(name);  }

		  else if (document.all)  {  return document.all[name];  }

		  else if (document.layers)  {  return document.layers[name];  }

		}
		function addplayer(){
			if(getObj('playerz_id').value == 0){
				return false;
			}
			var tbl = getObj('add_pl');
			var row = tbl.insertRow(tbl.rows.length-1);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			
			var input_hd = document.createElement('input');
			input_hd.type = 'hidden';
			input_hd.name = 'teampl[]';
			input_hd.value = getObj('playerz_id').value;
			
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE');?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';
			cell1.appendChild(input_hd);
			cell2.innerHTML = getObj('playerz_id').options[getObj('playerz_id').selectedIndex].text;
			row.appendChild(cell1);
			row.appendChild(cell2);
			
		}
		</script>
		<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div id="etab_team_div" class="tabdiv">
		<table class="season-list" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_TEAMNAME' ); ?>::<?php echo JText::_( 'BLFA_TT_TEAMNAME' );?>"><?php echo JText::_( 'BLFA_TT_TEAMNAME' ); ?>
					<img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<input type="text" class="feed-back inp-big" maxlength="255" size="60" name="t_name" value="<?php echo htmlspecialchars($row->t_name)?>" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_CITY' ); ?>::<?php echo JText::_( 'BLFA_TT_CITY' );?>"><?php echo JText::_( 'BLFA_CITY' ); ?>
					<img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<input type="text" maxlength="255" class="feed-back inp-big" size="60" name="t_city" value="<?php echo htmlspecialchars($row->t_city)?>" />
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
							case '3':	echo $lists['ext_fields'][$p]->selvals;
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
			
			<tr>
				<td valign="top">
					<?php echo JText::_( 'BLFA_TEAM_LOGO' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_TEAM_LOGO' ); ?>::<?php echo JText::_( 'BLFA_TT_TEAM_LOGO' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<input type="file" name="t_logo" class="feed-back inp-small" />
					
					<button class="send-button" onclick="javascript:submitbutton('team_apply');" ><span><?php echo JText::_( 'BLFA_UPLOAD' ); ?></span></button>
					<br />
					<?php
					
					if($row->t_emblem && is_file('media/bearleague/'.$row->t_emblem)){
						echo '<div id="logoiddiv" style="padding-top:5px;">
						<div class="wrapper-avatar-top">
							<div class="wrapper-avatar">
								<div class="wrapper-avatar-bottom">
									<a class="close" href="javascript:void(0);" title="'.JText::_( 'BLFA_REMOVE' ).'" onclick="javascript:delete_logo();">X<!-- --></a>
									<input type="hidden" name="istlogo" value="1" />
									<img width="120" src="'.JURI::base().'media/bearleague/'.$row->t_emblem.'" />
								</div>
							</div>
						</div>';
						?>
					</div>
					<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_ABOUT_TEAM' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ABOUT_TEAM' ); ?>::<?php echo JText::_( 'BLFA_TT_ABOUT_TEAM' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php  echo $this->editor->display( 't_descr',  htmlspecialchars($row->t_descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
					
				</td>
			</tr>
		</table>
		<br />
		<div style="margin-top:10px;border:1px solid #BBB;">
		<table style="padding:10px;" class="season-list">
			<tr>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLFA_UPLFOTO' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_UPLFOTO' ); ?>::<?php echo JText::_( 'BLFA_TT_UPLOAD_PHOTO' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
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
					<button class="send-button" onclick="javascript:submitbutton('team_apply');" ><span><?php echo JText::_( 'BLFA_UPLOAD' ); ?></span></button>
					
				</td>
			</tr>
		</table>
		
		<?php
		if(count($lists['photos'])){
		?>
		<table class="season-list">
			<tr>
				<th class="title" width="30"><?php echo JText::_( 'BLFA_DELETE' ); ?></th>
				<th class="title" width="30"><?php echo JText::_( 'BLFA_DEFAULT' ); ?></th>
				<th class="title" ><?php echo JText::_( 'BLFA_TITLE' ); ?></th>
				<th class="title" width="250"><?php echo JText::_( 'BLFA_IMAGE' ); ?></th>
			</tr>
			<?php
			foreach($lists['photos'] as $photos){
			?>
			<td align="center">
				<a href="javascript:void(0);" title="<?php echo JText::_( 'BLFA_REMOVE' ); ?>" onClick="javascript:Delete_tbl_row(this);"><img src="<?php echo JURI::base();?>components/com_joomsport/img/ico/close.png" title="<?php echo JText::_( 'BLFA_REMOVE' ); ?>" /></a>
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
				<a rel="{handler: 'image'}" href="<?php echo JURI::base();?>media/bearleague/<?php echo $photos->filename?>" title="Image" class="modal-button"><img src="<?php echo JURI::base();?>media/bearleague/<?php echo $photos->filename?>" width="<?php echo $width;?>" /></a>
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
		</div>
		<div id="etab_pl_div" class="tabdiv" style="display:none;">
		<?php
		echo '<table class="season-list" cellpadding="0" cellspacing="0" border="0" id="add_pl">';
		echo '<tr>';
		echo '<th class="title" width="30">#</th>';
		echo '<th class="title">'.JText::_('BLFA_PLAYER').'</th>';
		echo '</tr>';
		for($i=0;$i<count($lists['team_players']);$i++){
			$pl = $lists['team_players'][$i];
			echo '<tr class="'.($i%2?"":"gray").'"><td><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a><input type="hidden" name="teampl[]" value="'.$pl->id.'" /></td><td>'.$pl->name.'</td></tr>';
		}
		?>
			<tr>
				<td colspan="2">
					<div class="div_for_styled"><span class='down'><!-- --></span><?php echo $lists['player']; ?>
					<button class="send-button" onclick="addplayer();return false;" style="cursor:pointer;" /><span><b><?php echo JText::_('BLFA_ADD');?></b></span></button>
					</div>
				</td>
			</tr>
		</table>
		</div>
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="controller" value="admin" />
		<input type="hidden" name="task" value="edit_team" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="sid" value="<?php echo $lists["s_id"];?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
</div>