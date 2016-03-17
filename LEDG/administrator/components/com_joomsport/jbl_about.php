<?php/*------------------------------------------------------------------------# JoomSport Professional # ------------------------------------------------------------------------# BearDev development company # Copyright (C) 2011 JoomSport.com. All Rights Reserved.# @license - http://joomsport.com/news/license.html GNU/GPL# Websites: http://www.JoomSport.com # Technical Support:  Forum - http://joomsport.com/helpdesk/-------------------------------------------------------------------------*/// no direct accessdefined( '_JEXEC' ) or die( 'Restricted access' );
?>


<div style="font-family: verdana, arial, sans-serif; font-size: 9pt;">
	<div style="float:left; width:65%; margin-bottom:10px;" >
		<div style="float:left; height:100px;">
			<img height="100px" src="/administrator/components/com_joomsport/img/logobigbright.png">
		</div>
		<div style="float:left; margin-left:10px; margin-top:40px;">
			<h1>JoomSport Professional Edition</h1>
		</div>
	</div>
	<div style="float:right;margin:10px;margin-top:130px;">
		<?php
			echo '<a href="http://www.JoomSport.com" target="_blank">'.JHTML::_('image.site',  'logoh.png', '/components/com_joomsport/img/', NULL, NULL, 'JoomSport.com' ).'</a>'
		?>
	</div>

	<div style="float:left; width:450px">
	<table style="background-color:#E7E7E7;border-spacing:2px;color:#666666; width:100%;">
		<tr>
			<td style="font-size:12px; font-weight:bold; ">
				<?php echo JText::_('BLBE_MENAB');?>:		
			</td>
			<td style="width:350px">
				JoomSport component for Joomla! CMS 1.5 / 1.6 / 1.7 / 2.5
			</td>
		</tr>
		<tr>
			<td style="font-size:12px; font-weight:bold;">
				<?php echo JText::_('BLBE_VERSION');?>:
			</td>
			<td>
				Professional edition 2.5.0
			</td>
		</tr>		<tr>			<td style="font-size:12px; font-weight:bold; width:100px;"><?php echo JText::_('BLBE_LATVERSION');?>:</td>			<td><?php echo @file_get_contents('http://joomsport.com/index2.php?option=com_chkversion&id=1&no_html=1');?></td>		</tr>
		<tr>
			<td style="font-size:12px; font-weight:bold;">				<?php echo JText::_('BLBE_SUPPORT');?>:
			</td>
			<td>
				<a href="http://www.joomsport.com/component/agora/" target="_blank">Support Forum</a>
			</td>
			</tr>
		<tr>
			<td style="font-size:12px; font-weight:bold;">
				<?php echo JText::_('BLBE_COPYRIGHT');?>:
			</td>
			<td>
				&copy; 2010-2012 BearDev
			</td>
		</tr>
		<tr>
			<td style="font-size:12px; font-weight:bold;">				
				<?php echo JText::_('BLBE_MAINSITE');?>:
			</td>
			<td>
				<a href="http://www.JoomSport.com">http://www.JoomSport.com</a>
			</td>
		</tr>		<tr>			<td style="font-size:12px; font-weight:bold;">				<?php echo JText::_('BLBE_DEVELOPER');?>:			</td>			<td>				<a href="http://www.beardev.com" target="_blank">BearDev web development company</a>			</td>		</tr>
		<tr>
			<td style="font-size:12px; font-weight:bold;">
				<?php echo JText::_('BLBE_LICENSE');?>:
			</td>
			<td>
				<a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a>
			</td>
		</tr>		<tr>			<td style="font-size:12px; font-weight:bold;">				<?php echo JText::_('BLBE_MENHLP');?>:			</td>			<td>				<a href="http://joomsport.com/joomsport-professional-documentation.html" target="_blank">Documentation</a>			</td>		</tr>				
	</table>
	</div>
</div>



