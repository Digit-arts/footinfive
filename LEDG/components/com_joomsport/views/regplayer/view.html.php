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
jimport( 'joomla.application.component.view');

class joomsportViewregplayer extends JView
{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
	}
	function display($tpl = null)
	{
		$this->_model->getData();
		$lists = $this->_model->_lists;
		$lists["enmd"] = $this->_model->_enmd;
		$params = $this->_model->_params;
		$editor =& JFactory::getEditor(); 
		$this->assignRef('params',		$params); 

		$this->assignRef('lists', $lists);
		$this->assignRef('editor', $editor);

		require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
}
