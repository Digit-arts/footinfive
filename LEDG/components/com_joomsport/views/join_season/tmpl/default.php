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
	global $Itemid;
	$Itemid = JRequest::getInt('Itemid');
?>
<?php
echo $lists["panel"];
?>
<!-- <module middle> -->
	<div class="module-middle solid">
		
		<!-- <back box> -->
		<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div>
		<!-- </back box> -->
		
		<!-- <title box> -->
		<div class="title-box">
			<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
			
		</div>
		<!-- </div>title box> -->
	</div>
	<!-- </module middle> -->
	<div class="content-module">
		<div class="gray-box">
					
			<table cellpadding="0" cellspacing="0" border="0" class="adf-fields-table">
				
				<tr>
					<td><?php echo JText::_('BL_STARTDATE')?>:</td>
					<td><?php echo $this->lists["season_par"]->reg_start?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('BL_ENDDATE')?>:</td>
					<td><?php echo $this->lists["season_par"]->reg_end?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('BL_PARTIC')?>:</td>
					<td><?php echo $this->lists["season_par"]->s_participant." (".JText::_('BL_NOW')." ".$this->lists["part_count"].")";?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('BL_STARTDATE')?>:</td>
					<td><?php echo $this->lists["season_par"]->reg_start?></td>
				</tr>
			</table>
			

				<?php if($this->lists["unable_reg"]){?>
				<form method="POST" action="">
				<?php
				if($this->lists["t_single"]){
					echo "<input type='hidden' name='reg_team' value='".$this->user->id."' />";
					echo "<input type='hidden' name='is_team' value='0' />";
				}else if(!$this->lists['no_team']){
					echo "<div class='div_for_styled'>";
					echo "<span class='down'><!-- --></span>";
					echo $this->lists['cap'];
					echo "<input type='hidden' name='is_team' value='1' />";
					echo '</div>';
				}
				?>
				<?php if($this->lists['no_team']){
					echo "<span class='errjoin'>".JText::_('BL_NOCAP')."</span>";
				}else{?>
				<input type="hidden" name="task" value="joinme" />
				<input type="hidden" name="sid" value="<?php echo $this->lists["s_id"];?>" />
				<br /><br />
				<button class="send-button" type="submit"><span><b><?php echo JText::_('BL_JOINSEAS');?></b></span></button>
				<?php } ?>
				</form>
				<?php }?>
			<div class="gray-box-cr tl"><!-- --></div>
			<div class="gray-box-cr tr"><!-- --></div>
			<div class="gray-box-cr bl"><!-- --></div>
			<div class="gray-box-cr br"><!-- --></div>
		</div>
</div>