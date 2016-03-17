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

	JHTML::_('behavior.tooltip');
		?>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLBE_NUM' ); ?>
				</th>
				
				<th class="title">
					<?php echo JText::_( 'BLBE_MENTEAMS' ); ?>
				</th>
			</tr>
		</thead>
		
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
					<?php echo  $i+1; ?>
				</td>
				
				<td>
					<a href="javascript:window.parent.jSelectArticle('<?php echo $row->id?>', '<?php echo htmlspecialchars(addslashes($row->t_name), ENT_QUOTES, 'UTF-8')?>', 'tid');"><?php echo $row->t_name; ?></a>
				</td>
				
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
	</table>