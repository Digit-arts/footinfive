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

class map_editJSModel extends JSPRO_Models
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
		$is_id = $cid[0];
		
		$row 	= new JTableMaps($this->db);
		$row->load($is_id);
		
		$this->_data = $row;
		
	}

	public function saveMap(){
		
		$post		= JRequest::get( 'post' );
		$post['map_descr'] = JRequest::getVar( 'map_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['map_descr'] = strip_tags($post['map_descr']);
		$row 	= new JTableMaps($this->db);
		$istlogo = JRequest::getVar( 'istlogo', 0, 'post', 'int' );
		
		if(!$istlogo){
			$post['map_img'] = '';
		}
		if(isset($_FILES['t_logo']['name']) && $_FILES['t_logo']['tmp_name'] != '' && isset($_FILES['t_logo']['tmp_name'])){
			$bl_filename = strtolower($_FILES['t_logo']['name']);
			$ext = pathinfo($_FILES['t_logo']['name']);
			$bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
			$bl_filename = str_replace(" ","",$bl_filename);
			//echo $bl_filename;
			 if($this->uploadFile($_FILES['t_logo']['tmp_name'], $bl_filename)){
				$post['map_img'] = $bl_filename;
			 }
		}
		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		
		$this->_id = $row->id;
	}
	
}