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
			 if(pressbutton == 'tour_apply' || pressbutton == 'tour_save' || pressbutton == 'tour_save_new'){
			 	if(form.name.value != ''){
					submitform( pressbutton );
					return;
				}else{
					getObj('trname').style.border = "1px solid red";
					alert("<?php echo JText::_('BLBE_JSMDNOT1');?>");
					
					
				}
			}else{
				submitform( pressbutton );
					return;
			}			
		}	
		function delete_logo(){
			getObj("logoiddiv").innerHTML = '';
		}
		
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<table>
			<tr>
				<td width="120">
					<?php echo JText::_( 'BLBE_TOURNAMENTNAME' ); ?>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="name" id="trname" value="<?php echo htmlspecialchars($this->row->name);?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_PUBLISH' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PUBLISH' ); ?>::<?php echo JText::_( 'BLBE_PUBLISH' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $this->lists['published'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_TOURNMODE' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TOURNMODE' ); ?>::<?php echo JText::_( 'BLBE_TOURNMODE' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $this->lists['t_single'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_TOURNTYPE' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TOURNTYPE' ); ?>::<?php echo JText::_( 'BLBE_TOURNTYPE' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $this->lists['t_type'];?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo JText::_( 'BLBE_TOURN_LOGO' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TOURN_LOGO' ); ?>::<?php echo JText::_( 'BLBE_TT_TOURN_LOGO' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="file" name="t_logo" />&nbsp;<input type="button" style="cursor:pointer;" value="<?php echo JText::_('BLBE_TOURN_UPLOAD_PHOTO');?>" onClick="if(document.adminForm.t_logo.value){submitform('tour_apply');};" />
					<br />
					<?php
					
					if($this->row->logo && is_file('../media/bearleague/'.$this->row->logo)){
						echo '<div id="logoiddiv"><img width="120" src="'.JURI::base().'../media/bearleague/'.$this->row->logo.'">';
						echo '<input type="hidden" name="istlogo" value="1" />';
						?>
						<a href="javascript:void(0);" title="<?php echo JText::_( 'BLBE_REMOVE' ); ?>" onClick="javascript:delete_logo();"><img src="<?php echo JURI::base();?>components/com_joomsport/img/publish_x.png" title="Remove" /></a>
						</div>
					<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_ABOUT_TOURN' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ABOUT_TOURN' ); ?>::<?php echo JText::_( 'BLBE_TT_ABOUT_TOURN' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $editor->display( 'descr',  htmlspecialchars($this->row->descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				</td>
			</tr>

		</table>
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>