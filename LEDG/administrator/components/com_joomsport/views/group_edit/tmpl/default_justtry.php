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
		
		?>
		<script type="text/javascript">
		<!--
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'group_save' || pressbutton == 'group_apply') {
				if(form.group_name.value != "" && form.s_id.value != 0){
					if('<?php echo $row->id?>' != 0){
						var srcListName = 'teams_seasons';
						var srcList = eval( 'form.' + srcListName );
						if(srcList){
						var srcLen = srcList.length;
					
						for (var i=0; i < srcLen; i++) {
								srcList.options[i].selected = true;
						} 
						}
					}
					submitform( pressbutton );
					return;
				}else{	
					alert("<?php echo JText::_( 'BLBE_JSMDNOT10' ); ?>");
				}
			}else{
				submitform( pressbutton );
					return;
			}
		}	
		
		
		//-->
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
				<br />
		<div style="background-color:#eee;"><?php echo JText::_( 'BLBE_ASSIGNTEAMGROUP' ); ?></div>
		<br />
		<table>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_GROUPNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_GROUPNAME' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<input type="text" name="group_name" value="<?php echo $row->group_name?>" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td width="100" valign="top">
					<?php echo JText::_( 'BLBE_SEASON' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_SEASON' ); ?>::<?php echo JText::_( 'BLBE_TT_SEASONGROUP' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php echo $lists['tourn'];?>
				</td>
			</tr>
			
			
		</table>
		<?php
		if($row->id && $row->s_id){
		?>
		<table>
			<tr>
				<td width="150">
					
					<?php echo $lists['single']?JText::_( 'BLBE_ADD_PARTICS' ):JText::_( 'BLBE_ADD_TEAMS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo $lists['single']?JText::_( 'BLBE_ADD_PARTICS' ):JText::_( 'BLBE_ADD_TEAMS' ); ?>::<?php echo $lists['single']?JText::_( 'BLBE_TT_ADD_PARTICS' ):JText::_( 'BLBE_TT_ADD_TEAMS' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td width="150">
					<?php echo $lists['teams'];?>
				</td>
				<td valign="middle" width="60" align="center">
					<input type="button" style="cursor:pointer;" value=">>" onClick="javascript:JS_addSelectedToList('adminForm','teams_id','teams_seasons');" /><br />
					<input type="button" style="cursor:pointer;" value="<<" onClick="javascript:JS_delSelectedFromList('adminForm','teams_seasons','teams_id');" />
				</td>
				<td >
					<?php echo $lists['teams2'];?>
				</td>
			</tr>
		</table>
		<?php
		}
		?>
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>