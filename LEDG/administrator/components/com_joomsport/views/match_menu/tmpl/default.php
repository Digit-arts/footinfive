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
	<form action="index.php?option=<?php echo $option;?>" method="post" name="adminForm" id="adminForm">
		<table class="adminlist">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLBE_NUM' ); ?>
				</th>
				<th class="title"><?php echo JText::_( 'BLBE_MATCH' ); ?></th>
				<th class="title"><?php echo JText::_( 'BLBE_MATCHDAY' ); ?></th>
				
				
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="13">
				<?php //echo $this->page->getListFooter(); ?>			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		if( count( $rows ) ) {
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= $rows[$i];
			JFilterOutput::objectHtmlSafe($row);
			
			
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->page->getRowOffset( $i ); ?>				
				</td>
				<td>
					<?php echo '<a href="javascript:window.parent.jSelectArticle('.$row->id.', \''.htmlspecialchars(addslashes($row->home." ".$row->score1.":".$row->score2." ".$row->away), ENT_QUOTES, 'UTF-8').'\', \'id\');">'.$row->home." ".$row->score1.":".$row->score2." ".$row->away.'</a>';?>				
				</td>
				<td>
					<?php
						echo $row->m_name;
					?>
				</td>
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="match_menu" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="tmpl" value="component" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>