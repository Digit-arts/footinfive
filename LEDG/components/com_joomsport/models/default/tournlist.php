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

class tournlistJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $_total = null;
	var $id     = null; 
	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	var $_params = null;
	var $_tinfo  = null;
	
	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();
		
		$this->id = JRequest::getVar( 'id', 0, '', 'int' );
		
		$query = "SELECT COUNT(*) FROM  #__bl_tournament as t WHERE t.published='1' AND t.id = ".$this->id;
		$this->db->setQuery($query);
		if(!$this->id || !$this->db->loadResult()){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		//$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart', 'limitstart', 0, 'int' );
		$this->limitstart	= JRequest::getVar( 'limitstart', 0,'', 'int' );
		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
		
		$this->getData();
		$this->getTournInfo();
		$this->_params = $this->JS_PageTitle($this->_tinfo->name);
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
		
	}
	function getTournInfo(){
		$query = "SELECT * FROM #__bl_tournament WHERE id=".$this->id;
		$this->db->setQuery($query);
		$this->_tinfo = $this->db->loadObject();
	}
	function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}
		
		return $this->_data;
	}

	function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	function _getListCount($query){
		$this->db->setQuery($query);
		$tot = $this->db->loadObjectList();
		return count($tot);
	}
	
	function _getList($query){
		$this->db->setQuery($query, $this->limitstart, $this->limit);
		$tot = $this->db->loadObjectList();
		return $tot;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->limitstart, $this->limit );
		}

		return $this->_pagination;
	}

	function _buildQuery()
	{
		
		$query = "SELECT s.*,t.*, s.s_name as name"
				." FROM #__bl_tournament as t, #__bl_seasons as s"
				." WHERE s.published = '1' AND t.published = '1' AND s.t_id = t.id AND t.id=".$this->id
				." ORDER BY s.ordering";
		return $query;
	}
	
}	