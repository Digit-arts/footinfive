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

class team_listJSModel extends JSPRO_Models
{
	
	var $_data = null;

	var $_total = null;

	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;

	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_teams', 'limitstart', 0, 'int' );

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
		$this->db->setQuery($query,$this->limitstart,$this->limit);
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
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT * '
			. ' FROM #__bl_teams '
			. $orderby
		;

		return $query;
	}

	function _buildContentOrderBy()
	{
		
		$mainframe = JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_joomsport.filter_order',		'filter_order',		't_name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_joomsport.filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// sanitize $filter_order
		if (!in_array($filter_order, array('t_name', 'published','id'))) {
			$filter_order = 't_name';
		}

		if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC'))) {
			$filter_order_Dir = '';
		}

		
		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		

		return $orderby;
	}

	///delete
	function delTeam($cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `#__bl_teams` WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			
			$query = "DELETE FROM `#__bl_match_events` WHERE t_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			
			
			$query = "SELECT s.s_id FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.t_id=t.id AND t_single = 0";
			$this->db->setQuery($query);
			$sid = $this->db->loadResultArray();
			if(count($sid)){
				$sids = implode(',',$sid);
				$query = "SELECT id FROM #__bl_matchday WHERE s_id IN (".$sids.")";
				$this->db->setQuery($query);
				$mdid = $this->db->loadResultArray();
				
				if(count($mdid)){
					$mdids = implode(',',$mdid);
						$query = "DELETE FROM `#__bl_match` WHERE m_id IN (".$mdids.") AND (team1_id IN (".$cids.") OR team2_id IN (".$cids."))";
						$this->db->setQuery($query);
						$this->db->query();
				}
			}	
			
		}	
	}
}