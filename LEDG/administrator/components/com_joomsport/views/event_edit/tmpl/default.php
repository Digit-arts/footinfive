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
			 if(pressbutton == 'event_apply' || pressbutton == 'event_save' || pressbutton == 'event_save_new'){
			 	if(form.e_name.value != ''){
					submitform( pressbutton );
					return;
				}else{
					getObj('evname').style.border = "1px solid red";
					alert("<?php echo JText::_('BLBE_JSMDNOT1');?>");
					
					
				}
			}else{
				submitform( pressbutton );
					return;
			}			
		}	
		function View_eventimg(){
			getObj('view_img').innerHTML = '<img src="<?php echo JURI::base()?>../media/bearleague/events/'+document.adminForm.e_img.value+'" width="25" />';
		}
		function calctpfun(){
			if(getObj("player_event").value == '1'){
				getObj("calctp").style.display = "block";
			}else{
				getObj("calctp").style.display = "none";
			}
			if(getObj("player_event").value == '2'){
				getObj("calctp_sum").style.display = "block";
			}else{
				getObj("calctp_sum").style.display = "none";
			}
			
		}
		//-->
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<table>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_EVENTNAME' ); ?>
				</td>
				<td>
					<input type="text" name="e_name" size="50" value="<?php echo htmlspecialchars($row->e_name)?>" id="evname" maxlength="255" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			<tr>
				<td width="120" valign="top">
					<?php echo JText::_( 'BLBE_PLEVENT' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLEVENT' ); ?>::<?php echo JText::_( 'BLBE_TT_PLEVENT' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $lists['player_event'];?>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<table cellpadding="0" id="calctp" <?php echo $row->player_event?"":"style='display:none;'";?>>
						<tr>
							<td width="120" valign="top">
								<?php echo JText::_( 'BLBE_CALCETYPE' ); ?>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_CALCETYPE' ); ?>::<?php echo JText::_( 'BLBE_TT_CALCETYPE' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
							</td>
							<td>
								<?php echo $lists['restype'];?>
							</td>
						</tr>
					</table>				
				</td>	
			</tr>
			<tr>
				<td colspan="2">
					<table cellpadding="0" id="calctp_sum" <?php echo ($row->player_event == 2)?"":"style='display:none;'";?>>
						<tr>
							<td width="120" valign="top">
								<?php echo JText::_( 'BLBE_SELEVENTFORSUM' ); ?>
							</td>
							<td>
								<?php echo $lists['sumev1'];?><?php echo $lists['sumev2'];?>
							</td>
						</tr>
					</table>	
					
				</td>
			</tr>
			<tr>
				<td width="120" valign="top">
					<?php echo JText::_( 'BLBE_EVIMG' ); ?>
				</td>
				<td>
					<?php echo $lists['image']." ".Jtext::_('BLBE_EVNEWIMG') ;?> <input type="file" name="new_event_img" /><input type="button" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" onclick="submitbutton('event_apply');" style="cursor:pointer;" />
					<br />
					<div id="view_img" style="width:50px; height:50px; margin: 10px; ">
						<?php 
						if( $row->e_img ){
							echo '<img id="img_div" src="../media/bearleague/events/'.$row->e_img.'" width="25" />';
						}
						?>
					</div>
				</td>
			</tr>
		</table>
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>