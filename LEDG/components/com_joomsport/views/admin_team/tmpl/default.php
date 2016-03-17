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
defined('_JEXEC') or die('Restricted access'); 
	if(isset($this->message)){
		$this->display('message');
	}
	$rows = $this->rows;
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
</script>
<?php
echo $this->lists["panel"];
?>
<!-- <module middle> -->
	<div class="module-middle solid">
		
		<!-- <back box> -->
		<!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
		<!-- </back box> -->
		
		<!-- <title box> -->
		<div class="title-box">
			<h4><?php echo $this->lists['tournname'];?></h4>
			<h2><?php echo JText::_('BLFA_TEAMSLIST')?></h2>
			
		</div>
		<!-- </div>title box> -->
		
		<!-- <tab box> -->
		<ul class="tab-box-main">
			<li><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$this->s_id.'&Itemid='.$Itemid )?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_MATCHDAY')?></span></a></li>
			<li class="active"><a href="#" title=""><span><img class="star" src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_ADMIN_TEAM')?></span></a></li>
			<li><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=admin&task=adlist_player&sid='.$this->s_id.'&Itemid='.$Itemid)?>" title=""><span><img class="players" src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_PLAYER')?></span></a></li>
		</ul>
		<!-- </tab box> -->
		
	</div>
	<!-- </module middle> -->
	<!-- <control bar> -->
	<div class="control-bar-wr dotted">
		<ul class="control-bar">
			<li><a class="add" href="#" onclick="javascript:bl_submit('edit_team',0);return false;" title="<?php echo JText::_('BLFA_NEW')?>"><?php echo JText::_('BLFA_NEW')?></a></li>
			<li><a class="edit" href="#" onclick="javascript:bl_submit('edit_team',1);return false;" title="<?php echo JText::_('BLFA_EDIT')?>"><?php echo JText::_('BLFA_EDIT')?></a></li>
			<li><a class="delete" href="#" onclick="javascript:bl_submit('team_del',1);return false;" title="<?php echo JText::_('BLFA_DELETE')?>"><?php echo JText::_('BLFA_DELETE')?></a></li>
			<!-- <li><a class="save" href="#" title="Save">Save</a></li>
			<li><a class="apply" href="#" title="Apply">Apply</a></li> -->
		</ul>
	</div>
	<!-- </control bar> -->
<!-- <content module> -->
	<div class="content-module">
<?php
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLFA_NOITEMS')."</ul></dd></div>";
		}
		?>
		<form action="" method="post" name="adminForm" id="adminForm">
		<table class="season-list" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLFA_NUM' ); ?>
				</th>
				<th width="2%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
				</th>
				<th class="title">
					<?php echo JText::_( 'BLFA_TEAM' ); ?>
				</th>
				<th class="title">
					<?php echo JText::_( 'BLFA_CITY' ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="13" style="text-align:center;">
				<?php echo $this->page->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		if( count( $rows ) ) {
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= $rows[$i];
			JFilterOutput::objectHtmlSafe($row);
			$link = JRoute::_( 'index.php?option=com_joomsport&controller=admin&task=edit_team&cid[]='. $row->id .'&sid='.$this->s_id.'&Itemid='.$Itemid);
			$checked 	= @JHTML::_('grid.checkedout',   $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo ($i % 2?"gray":""); ?>">
				<td>
					<?php echo $this->page->getRowOffset( $i ); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php
						echo '<a href="'.$link.'">'.$row->t_name.'</a>';
					?>
				</td>
				<td>
					<?php echo $row->t_city;?>
				</td>
				
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="admin_team" />
		<input type="hidden" name="controller" value="admin" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="sid" value="<?php echo $this->s_id;?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
</div>