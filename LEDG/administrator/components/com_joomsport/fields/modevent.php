<?php/*------------------------------------------------------------------------# JoomSport Professional # ------------------------------------------------------------------------# BearDev development company # Copyright (C) 2011 JoomSport.com. All Rights Reserved.# @license - http://joomsport.com/news/license.html GNU/GPL# Websites: http://www.JoomSport.com # Technical Support:  Forum - http://joomsport.com/helpdesk/-------------------------------------------------------------------------*/// no direct accessdefined( '_JEXEC' ) or die( 'Restricted access' );jimport('joomla.form.formfield');class JFormFieldModEvent extends JFormField{	/**	 * Element name	 *	 * @access	protected	 * @var		string	 */	 protected $type = 'event';	protected function getInput()	{				// Load the modal behavior script.		JHtml::_('behavior.modal', 'a.modal');		$db	= JFactory::getDBO();		// Build the script.		$script = array();		$script[] = '	function jSelectJS_'.$this->id.'(id, title, catid, object) {';		$script[] = '		document.id("'.$this->id.'_id").value = id;';		$script[] = '		document.id("'.$this->id.'_name").value = title;';		$script[] = '		SqueezeBox.close();';		$script[] = '	}';		// Add the script to the document head.		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));				$query = "SELECT * FROM #__bl_events WHERE (player_event = '1' OR player_event = '2') ORDER BY e_name";		$db->setQuery($query);		$events = $db->loadObjectList();						$html = JHTML::_('select.genericlist',   $events, 'jform[params][event_id]', 'class="inputbox" size="1"', 'id', 'e_name', $this->value);		//$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.$this->value.'" />';				return $html;	}}