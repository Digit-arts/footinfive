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

class moder_listJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
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
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_moders', 'limitstart', 0, 'int' );
		
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
			for($z=0;$z<count($this->_data);$z++){
				$query = "SELECT t.t_name  FROM #__bl_moders as m,#__bl_teams as t WHERE t.id=m.tid AND m.uid=".$this->_data[$z]->id;
				$this->db->setQuery($query);
				$this->_data[$z]->teams = $this->db->loadResultArray();
			}
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
		
		$orderby	= $this->_buildContentOrderBy();

		$query = "SELECT u.*  FROM #__bl_moders as m,#__users as u WHERE u.id=m.uid GROUP BY m.uid";
		$query .= $orderby;
	
		
		
		return $query;
	}

	function _buildContentOrderBy()
	{
		
		$mainframe = JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_joomsport.filter_order',		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_joomsport.filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// sanitize $filter_order
		if (!in_array($filter_order, array('u.username', 'published','id'))) {
			$filter_order = 'u.username';
		}

		if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC'))) {
			$filter_order_Dir = '';
		}

		
		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		

		return $orderby;
	}
	
	
}