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
	
	<?php
	if(!count($rows)){
		echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLBE_NOITEMS')."</ul></dd></div>";
	}
	?>
	<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="2%" align="left">
				<?php echo JText::_( '#' ); ?>
			</th>
			<th width="2%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
			</th>
			<th class="title">
				<?php echo JText::_( 'BLBE_TEMPLATE' ); ?>
			</th>
			<th width="5%">
				<?php echo JText::_( 'Default' ); ?>
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
		$link = JRoute::_( 'index.php?option=com_joomsport&task=templ_edit&cid[]='. $row->id );
		$checked 	= @JHTML::_('grid.checkedout',   $row, $i );
		//$published 	= JHTML::_('grid.published', $row, $i);
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->page->getRowOffset( $i ); ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php
					echo '<a href="'.$link.'">'.$row->name.'</a>';
				?>
			</td>
			
			<td align="center">
				<?php 
				if ($row->isdefault == 1) {
?>
								<img src="components/com_joomsport/img/icon-16-default.png" alt="<?php echo JText::_( 'Default' ); ?>" />
								<?php

				} else {
?>
								&nbsp;
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
	<input type="hidden" name="task" value="templ_list" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>