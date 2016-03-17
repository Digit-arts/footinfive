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

class fields_editJSModel extends JSPRO_Models
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
		
		$row 	= new JTableFields($this->db);
		$row->load($is_id);
		$is_field[] = JHTML::_('select.option',  0, JText::_('BLBE_PLAYER'), 'id', 't_name' ); 
		$is_field[] = JHTML::_('select.option',  1, JText::_('BLBE_TEAM'), 'id', 't_name' ); 
		$is_field[] = JHTML::_('select.option',  2, JText::_('BLBE_MATCH'), 'id', 't_name' ); 
		$is_field[] = JHTML::_('select.option',  3, JText::_('BLBE_SEASON'), 'id', 't_name' ); 
		$this->_lists['is_type'] = JHTML::_('select.genericlist',   $is_field, 'type', 'class="inputbox" size="1" onchange="tblview_hide();"', 'id', 't_name', $row->type );
		$published = ($row->id) ? $row->published : 1;
		$fdisplay = ($row->id) ? $row->fdisplay : 1;
		$this->_lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $published );
		$this->_lists['t_view'] 		= JHTML::_('select.booleanlist',  'e_table_view', 'class="inputbox"', $row->e_table_view );
		$this->_lists['fdisplay'] 		= JHTML::_('select.booleanlist',  'fdisplay', 'class="inputbox"', $fdisplay );
		
		$fldtype[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTXTFD'), 'id', 'name' ); 
		$fldtype[] = JHTML::_('select.option',  1, JText::_('BLBE_SELRADB'), 'id', 'name' ); 
		$fldtype[] = JHTML::_('select.option',  2, JText::_('BLBE_TXTAR'), 'id', 'name' ); 
		$fldtype[] = JHTML::_('select.option',  3, JText::_('BLBE_SELBX'), 'id', 'name' ); 
		$fldtype[] = JHTML::_('select.option',  4, JText::_('BLBE_LINK'), 'id', 'name' ); 
		$this->_lists['field_type'] = JHTML::_('select.genericlist',   $fldtype, 'field_type', 'class="inputbox" size="1" onchange="shide();"', 'id', 'name', $row->field_type );
		
		$faccess[] = JHTML::_('select.option',  0, JText::_('BLBE_ALL'), 'id', 'name' ); 
		$faccess[] = JHTML::_('select.option',  1, JText::_('BLBE_SELREGONLY'), 'id', 'name' ); 
		
		$this->_lists['faccess'] = JHTML::_('select.genericlist',   $faccess, 'faccess', 'class="inputbox" size="1"', 'id', 'name', $row->faccess );
		
		//----for selectbox----///
		$this->_lists['selval'] = array();
		
		if($row->field_type == '3'){
			$query = "SELECT * FROM #__bl_extra_select WHERE fid=".$row->id." ORDER BY sel_value";
			$this->db->setQuery($query);
			$this->_lists['selval'] = $this->db->loadObjectList();
		}
		
		$this->_data = $row;
		
	}
	public function orderFields(){
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array(), 'post', 'array' );
		$f_type		= JRequest::getVar( 'f_type', 0, 'post', 'int' );
		$row		= new JTableFields($this->db);;
		$total		= count( $cid );
		
		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
				// remember to reorder this category
				
				
			}
		}
	}
	public function saveFields(){
		
		$post		= JRequest::get( 'post' );
		$post['descr'] = JRequest::getVar( 'descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$row 	= new JTableFields($this->db);
		
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
		
		
		
		$mj = 0;
		$mjarr = array();
		if(isset($_POST['selnames']) && count($_POST['selnames'])){
			foreach($_POST['selnames'] as $selname){
				if($_POST['adeslid'][$mj]){
					$this->db->setQuery("UPDATE #__bl_extra_select SET sel_value='".addslashes($selname)."' WHERE id=".$_POST['adeslid'][$mj]);
					
				}else{
					$this->db->setQuery("INSERT INTO #__bl_extra_select(fid,sel_value) VALUES(".$row->id.",'".addslashes($selname)."')");
				}
				$this->db->query();
				$mjarr[] = $_POST['adeslid'][$mj]?$_POST['adeslid'][$mj]:$this->db->insertid(); 
				$mj++;
				
			}
		}else{
			$query = "DELETE FROM #__bl_extra_select WHERE fid=".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		}
		//var_dump($mjarr);
		$query = "DELETE FROM #__bl_extra_select WHERE fid=".$row->id." AND id NOT IN (".implode(',',$mjarr).")";
		$this->db->setQuery($query);
		$this->db->query();
		
		$this->_id = $row->id;
	}
	
}