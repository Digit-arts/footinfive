<?php/*------------------------------------------------------------------------# JoomSport Professional # ------------------------------------------------------------------------# BearDev development company # Copyright (C) 2011 JoomSport.com. All Rights Reserved.# @license - http://joomsport.com/news/license.html GNU/GPL# Websites: http://www.JoomSport.com # Technical Support:  Forum - http://joomsport.com/helpdesk/-------------------------------------------------------------------------*/// no direct accessdefined( '_JEXEC' ) or die( 'Restricted access' );class JElementModevent extends JElement{	/**	 * Element name	 *	 * @access	protected	 * @var		string	 */	var	$_name = 'Event';	function fetchElement($name, $value, &$node, $control_name)	{		global $mainframe;		$db			=& JFactory::getDBO();		$doc 		=& JFactory::getDocument();		$template 	= $mainframe->getTemplate();		$fieldName	= $control_name.'['.$name.']';				$query = "SELECT * FROM #__bl_events WHERE (player_event = '1' OR player_event = '2') ORDER BY e_name";		$db->setQuery($query);		$events = $db->loadObjectList();						$html = JHTML::_('select.genericlist',   $events, 'params[event_id]', 'class="inputbox" size="1"', 'id', 'e_name', $value);		//$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.$value.'" />';				return $html;	}}