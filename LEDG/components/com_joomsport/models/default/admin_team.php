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

class admin_teamJSModel extends JSPRO_Models
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
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart', 'limitstart', 0, 'int' );
		$this->season_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.sid', 'sid', 0, 'int' );
		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
	}

	function getData()
	{
		
		$this->getPagination();
		$this->_params = $this->JS_PageTitle("");
		$query = "SELECT DISTINCT(t.id),t.* "
				." FROM #__bl_teams as t, #__bl_seasons as s, #__bl_season_teams as st, #__bl_tournament as tr"
				." WHERE s.s_id=st.season_id AND st.team_id = t.id AND s.t_id = tr.id AND s.s_id=".$this->season_id
				." ORDER BY t.t_name";
	
		$this->db->setQuery($query, $this->limitstart, $this->limit);
		$rows = $this->db->loadObjectList();
		$this->_data = $rows;
		
		$tourn = $this->getTournOpt($this->season_id);
		$this->_lists['tournname'] = $tourn->name;

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
		$query = "SELECT COUNT(DISTINCT(t.id))"
				." FROM #__bl_teams as t, #__bl_seasons as s, #__bl_season_teams as st, #__bl_tournament as tr"
				." WHERE s.s_id=st.season_id AND st.team_id = t.id AND s.t_id = tr.id AND s.s_id=".$this->season_id
				." ORDER BY t.t_name";

		$this->db->setQuery($query);

		$this->_total = $this->db->loadResult();
		
		return $this->_total;
	}
	
}
