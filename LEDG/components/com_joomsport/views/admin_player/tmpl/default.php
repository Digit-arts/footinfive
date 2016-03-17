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
	$rows = $this->rows;
	$page = $this->page;
	
	global $Itemid;
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
		<div class="title-box padd-bot">
			<h2><?php echo JText::_('BLFA_PLAYERSLIST')?>
			</h2>
			<div class="select-wr">
				<form action='<?php echo JURI::base();?>index.php?option=com_joomsport&task=team_edit&controller=moder&Itemid=<?php echo $Itemid?>' method='post' name='chg_team'>
					<div style="position:relative;"><span class='down'><!-- --></span><?php echo $this->lists['tm_filtr'];?></div>
					<!--div style="position:relative;"><span class='down'><!-- -- ></span><?php //echo $this->lists['seass_filtr'];?></div-->
				</form>
			</div>
		</div>
		<!-- </div>title box> -->
		<!-- <tab box> -->
		<ul class="tab-box-main">
			<li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=moderedit_team&tid='.$this->tid.'&Itemid='.$Itemid);?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" class="star" /><?php echo JText::_('BLFA_TEAM')?></span></a></li>
			<?php if($this->lists["enmd"]):?>
			<!--li><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=moder&task=moderedit_matchday&tid='.$this->tid.'&Itemid='.$Itemid )?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_MATCHDAY')?></span></a></li-->
			<?php endif;?>
			<li class="active"><a href="#" title=""><span><img class="players" src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_PLAYER')?></span></a></li>

			
		</ul>
		<!-- </tab box> -->
	
</div>
<!-- </module middle> -->
<!-- <control bar> -->
<div class="control-bar-wr dotted">
	<ul class="control-bar">
		<li><a class="add" href="#" title="<?php echo JText::_('BLFA_NEW')?>" onclick="javascript:bl_submit('moderedit_player',0);return false;"><?php echo JText::_('BLFA_NEW')?></a></li>
		<li><a class="edit" href="#" title="<?php echo JText::_('BLFA_EDIT')?>" onclick="javascript:bl_submit('moderedit_player',1);return false;"><?php echo JText::_('BLFA_EDIT')?></a></li>
		<li><a class="delete" href="#" title="<?php echo JText::_('BLFA_DELETE')?>" onclick="javascript:bl_submit('mdplayer_del',1);return false;"><?php echo JText::_('BLFA_DELETE')?></a></li>
	</ul>
</div>
<!-- </control bar> -->

<!-- <content module> -->
	<div class="content-module admin-mo-co">


<?php
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLFA_NOITEMS')."</ul></dd></div>";
		}
		?>
		<form action="<?php echo JURI::base();?>index.php?option=com_joomsport&controller=moder&tid=<?php echo $this->tid;?>&Itemid=<?php echo $Itemid;?>" method="post" name="adminForm" id="adminForm">
		
		<table class="season-list">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLFA_NUM' ); ?>
				</th>
				<th width="2%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
				</th>
				<th class="title">
					<?php echo JText::_( 'BLFA_PLAYERR' ); ?>
				</th>
				
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="13" style="text-align:center;">
				<?php echo $page->getListFooter(); ?>
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
			$link = JRoute::_( 'index.php?option=com_joomsport&controller=moder&task=moderedit_player&tid='.$this->tid.'&cid[]='. $row->id.'&Itemid='.$Itemid );
			$checked 	= @JHTML::_('grid.checkedout',   $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $page->getRowOffset( $i ); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<img class="player-ico" src="<?php echo JURI::base();?>components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">
					<p class="player-name">
					<?php
						echo '<a href="'.$link.'">'.$row->first_name.' '.$row->last_name.'</a>';
					?>
					</p>
				</td>
				
				
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="admin_player" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
</div>