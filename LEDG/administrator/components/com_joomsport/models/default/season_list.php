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

class season_listJSModel extends JSPRO_Models
{
	
	var $_data = null;

	var $_total = null;
	var $_lists = null;
	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;

	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_seasons', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
		$this->getData();
	}

	function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->limitstart, $this->limit);
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
	
	function _getList($query,$limitstart,$limit){
		$this->db->setQuery($query,$limitstart,$limit);
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
		$tfilt_id = JRequest::getVar( 'tfilt_id', 0, 'post', 'int' );

		$query = "SELECT s.*,s.s_id as id,t.name FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id ".($tfilt_id?" WHERE t.id=".$tfilt_id:"")."  ORDER BY ordering,s.s_name";

		return $query;
	}
	function getFilter(){
		$is_tourn = array();
		$tfilt_id = JRequest::getVar( 'tfilt_id', 0, 'post', 'int' );
		$javascript = 'onchange = "document.adminForm.submit();"';
		$query = "SELECT * FROM #__bl_tournament ORDER BY name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		$is_tourn[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTOURNAMENT'), 'id', 'name' ); 
		$tourn_is = array_merge($is_tourn,$tourn);
		$this->_lists['tourn'] = JHTML::_('select.genericlist',   $tourn_is, 'tfilt_id', 'class="inputbox" size="1"'.$javascript, 'id', 'name', $tfilt_id );
	}
	function js_publish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '1' WHERE s_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
		
	}
	
	function js_unpublish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '0' WHERE s_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
	
	function js_delete($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `".$table."` WHERE s_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}