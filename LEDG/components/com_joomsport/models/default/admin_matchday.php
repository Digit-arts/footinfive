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

class admin_matchdayJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $_total = null;

	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	var $season_id = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$this->season_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.sid', 'sid', 0, 'int' );
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_md'.$this->season_id, 'limitstart', 0, 'int' );
		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
	}

	function getData()
	{
		
		
		$this->_params = $this->JS_PageTitle("");
		$tourn = $this->getTournOpt($this->season_id);
		$this->_lists['t_single'] = $tourn->t_single;
		$this->_lists['tournname'] = $tourn->name;
		$this->getPagination();
		
		$query = "SELECT m.*, t.name as tourn,s.s_name"
				." FROM #__bl_matchday as m , #__bl_tournament as t LEFT JOIN #__bl_seasons as s ON s.t_id = t.id"
				." WHERE m.s_id = s.s_id ".($this->season_id?" AND s.s_id=".$this->season_id:"")
				."  ORDER BY s.ordering,m.ordering";
		$this->db->setQuery($query, $this->limitstart, $this->limit);
		$rows = $this->db->loadObjectList();
		$this->_data = $rows;

		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
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
	function getTotal(){
		$query = "SELECT COUNT(*) FROM #__bl_matchday as m , #__bl_tournament as t LEFT JOIN #__bl_seasons as s ON s.t_id = t.id"
				." WHERE m.s_id = s.s_id ".($this->season_id?" AND s.s_id=".$this->season_id:"")
				."  ORDER BY m.m_name";

		$this->db->setQuery($query);

		$this->_total = $this->db->loadResult();
		
		return $this->_total;
	}
	
}