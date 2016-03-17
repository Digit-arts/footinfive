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

class admin_playerJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $_total = null;
	var $_user = null;
	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	var $season_id = null;
	var $tid	= null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart', 'limitstart', 0, 'int' );
		$this->season_id	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.moderseason', 'moderseason', 0, 'int' );
		$this->tid 		= JRequest::getVar( 'tid', 0, '', 'int' );
		$query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$this->tid." ORDER BY s.s_id desc";
		$this->db->setQuery($query);
		$seass = $this->db->loadObjectList();
		if(!$this->season_id) {$this->season_id = $seass[0]->id;};
		$isinseas = false;
		for($j=0;$j<count($seass);$j++){
			if($this->season_id == $seass[$j]->id){
				$isinseas = true;
			}
		}
		if(!$isinseas && count($seass)){
			
			$this->season_id = $seass[0]->id;
		}
		
		
		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$user	=& JFactory::getUser();
		$this->_user = $user;
		if ( $user->get('guest')) {

			$return_url = $_SERVER['REQUEST_URI'];
			$return_url = base64_encode($return_url);
			if(getVer() >= '1.6'){
				$uopt = "com_users";
			}else{
				$uopt = "com_user";
			}
			$return	= 'index.php?option='.$uopt.'&view=login&return='.$return_url;
			$this->mainframe->redirect( $return, JText::_('BLMESS_NOT_LOGIN') );
			
		}
		

		$this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');
		
		$query = "SELECT COUNT(*) FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid=".$user->id." AND t.id=".$this->tid;
		$this->db->setQuery($query);
		if(!$this->db->loadResult() || !$this->_lists['moder_addplayer']){
			JError::raiseError( 403, JText::_('Access Forbidden') );
				return;
		}	
	}

	function getData()
	{
		$this->getGlobFilters();
		$this->getPagination();
		$this->_params = $this->JS_PageTitle("");
		$query = "SELECT p.* FROM #__bl_players as p WHERE p.created_by = ".$this->_user->id."  ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query, $this->limitstart, $this->limit);
		$rows = $this->db->loadObjectList();
		$this->_data = $rows;
		$query = "SELECT COUNT(*) FROM #__bl_season_teams as sp, #__bl_matchday as m WHERE m.s_id=sp.season_id AND sp.team_id = ".$this->tid;
		$this->db->setQuery($query);
		$this->_lists["enmd"] = $this->db->loadResult();
		
		$query = "SELECT m.* FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_matchday as m"
				." WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.team_id=".$this->tid." AND s.s_id=".$this->season_id
				." ORDER BY m.ordering";
		$this->db->setQuery($query);
		$mdays = $this->db->loadObjectList();
		
		if(!count($mdays)){
			$this->_lists["enmd"] = 0;
		}
		
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
		
		$query = "SELECT COUNT(*) FROM #__bl_players as p WHERE p.created_by = ".$this->_user->id;
		$this->db->setQuery($query);

		$this->_total = $this->db->loadResult();
		
		return $this->_total;
	}
	
}
