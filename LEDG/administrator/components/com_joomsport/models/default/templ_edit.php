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
// No direct access.
defined('_JEXEC') or die;

require(dirname(__FILE__).'/../models.php');

class templ_editJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_mode = 1;
	var $_id = null;
	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();
	
		$this->getData();
	}

	function getData()
	{
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$db			=& JFactory::getDBO();
		$row = new JTableTempl( & $db);
		$row->load($cid[0]);
		$this->_data = $row;
		
		jimport('joomla.filesystem.file');
		$template = $row->name?$row->name:'default';
		$filename = 'joomsport.css';
		$content = JFile::read(JURI::root().'components'.DS.'com_joomsport'.DS.'templates'.DS.$template.DS.'css'.DS.$filename);
		$this->_data->content = $content;
	}
	function saveTempl(){
		
		$db			=& JFactory::getDBO();
		$post		= JRequest::get( 'post' );

		$row 	= new JTableTempl($db);
		
		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		// if new item order last in appropriate group
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		$this->_id = $row->id;
	}
}