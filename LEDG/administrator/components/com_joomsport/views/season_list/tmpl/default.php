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
$rows = $this->rows;
?>
	<script type="text/javascript">
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'season_add') {
				if(form.tfilt_id.value != 0){
					submitform( pressbutton );
					return;
				}else{	
					alert("<?php echo JText::_('BLBE_SELTOURNAMENT');?>");	
				}
			}else if(pressbutton == 'season_del'){
				if(confirm("<?php echo JText::_("BLBE_SEDELCONFIRM");?>")){
					submitform( pressbutton );
					return;
				}
			}else{
				submitform( pressbutton );
					return;
			}
		}	
		</script>
		<?php
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLBE_NOITEMS')."</ul></dd></div>";
		}
		?>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
		<div><?php echo JText::_('BLBE_FILTERS')." :". $this->lists['tourn'];?></div>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( '#' ); ?>
				</th>
				<!--th width="2%" align="left">
					<?php //echo JText::_( 'ID' ); ?>
				</th-->
				<th width="2%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
				</th>
				<th class="title">
					<?php echo JText::_( 'BLBE_SEASON' ); ?>
				</th>
				<th width="8%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   JText::_( 'BLBE_ORDER' ), 'ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
					<?php echo JHTML::_('grid.order',  $rows,'filesave.png', 'season_ordering' ); ?>
				</th>
				<th class="title">
					<?php echo JText::_( 'BLBE_TOURNAMENT' ); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('BLBE_PUBLISHED'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="13">
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
			$link = JRoute::_( 'index.php?option=com_joomsport&task=season_edit&cid[]='. $row->id );
			$checked 	= @JHTML::_('grid.checkedout',   $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->page->getRowOffset( $i ); ?>
				</td>
				<!--td>
					<?php //echo $row->id; ?>
				</td-->
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php
						echo '<a href="'.$link.'">'.$row->s_name.'</a>';
					?>
				</td>
				<td class="order">
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
				</td>
				<td>
					<?php echo $row->name; ?>
				</td>
				<td align="center">
					<?php 
					if(!$row->published){
						?>
						<a title="<?php echo JText::_('BLBE_PUBLITEM');?>" onclick="return listItemTask('cb<?php echo $i?>','season_publ')" href="javascript:void(0);">
						<img border="0" alt="Unpublished" src="components/com_joomsport/img/publish_x.png"/></a>
						<?php
					}else{
						?>
						<a title="<?php echo JText::_('BLBE_UNPUBLITEM');?>" onclick="return listItemTask('cb<?php echo $i?>','season_unpubl')" href="javascript:void(0);">
						<img border="0" alt="Published" src="components/com_joomsport/img/tick.png"/></a>
						<?php
					}
					?>
				</td>
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="season_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>