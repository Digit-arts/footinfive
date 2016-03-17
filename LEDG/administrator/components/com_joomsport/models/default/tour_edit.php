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

class tour_editJSModel extends JSPRO_Models
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
		$row = new JTableTourn($db);
		$row->load($cid[0]);
		$this->_data = $row;
		
		
		$published = ($row->id) ? $row->published : 1;
		$this->_lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $published );
		
		$type_single = array();
		$type_single[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTOURNTEAM'), 'id', 'name' ); 
		$type_single[] = JHTML::_('select.option',  1, JText::_('BLBE_SELTOURNSING'), 'id', 'name' ); 
		$this->_lists['t_single'] = JHTML::_('select.genericlist',   $type_single, 't_single', 'class="inputbox" size="1"', 'id', 'name', $row->t_single );
		$type_tourn = array();
		$type_tourn[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTOURNGROUP'), 'id', 'name' ); 
		$type_tourn[] = JHTML::_('select.option',  1, JText::_('BLBE_SELTOURNKO'), 'id', 'name' ); 
		$this->_lists['t_type'] = JHTML::_('select.genericlist',   $type_tourn, 't_type', 'class="inputbox" size="1"', 'id', 'name', $row->t_type );
	}
	function saveTourn(){
		
		$db			=& JFactory::getDBO();
		$post		= JRequest::get( 'post' );
		$post['descr'] = JRequest::getVar( 'descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$istlogo = JRequest::getVar( 'istlogo', 0, 'post', 'int' );
		$row 	= new JTableTourn($db);
		if(!$istlogo){
			$post['logo'] = '';
		}
		if(isset($_FILES['t_logo']['name']) && $_FILES['t_logo']['tmp_name'] != '' && isset($_FILES['t_logo']['tmp_name'])){
			$bl_filename = strtolower($_FILES['t_logo']['name']);
			$ext = pathinfo($_FILES['t_logo']['name']);
			$bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
			$bl_filename = str_replace(" ","",$bl_filename);
			$bl_filename;
			 if($this->uploadFile($_FILES['t_logo']['tmp_name'], $bl_filename)){
				$post['logo'] = $bl_filename;
			 }
		}

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