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

class match_menuJSModel extends JSPRO_Models
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
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
		$this->getData();
	}

	function getData()
	{
		if (empty($this->_data))
		{
			$query = "SELECT t.t_single,t.t_type,m.id,m.m_name FROM #__bl_tournament as t, #__bl_seasons as s, #__bl_matchday as m WHERE s.t_id = t.id AND s.s_id=m.s_id ORDER BY s.s_id,m.m_name";
			$this->db->setQuery($query);
			$md = $this->db->loadObjectList();
			$rows = array();
			for($i=0;$i<count($md);$i++){
				$t_single = $md[$i]->t_single;
				$t_type = $md[$i]->t_type;
				if($t_single){
					$query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away, '".$md[$i]->m_name."' as m_name FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id  WHERE t.id=m.team1_id AND t2.id=m.team2_id AND m.m_played='1' AND m.m_id = ".$md[$i]->id."  ORDER BY ".($t_type?"m.k_stage,m.k_ordering":"m.id");
				}else{
					$query = "SELECT m.*,t.t_name as home, t2.t_name as away, '".$md[$i]->m_name."' as m_name FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id WHERE t.id=m.team1_id AND t2.id=m.team2_id AND m.m_played='1' AND m.m_id = ".$md[$i]->id." ORDER BY ".($t_type?"m.k_stage,m.k_ordering":"m.id");
				}
				$this->db->setQuery($query);
				$match = $this->db->loadObjectList();
				if(count($match)){
					$rows = array_merge($rows,$match);
					$this->_data = $rows;
				}
			}
			
				$query = "SELECT m.*,md.m_name,md.id as mdid,md.s_id, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
						." WHERE md.s_id='-1' AND m.m_single='1' AND m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='1'";
				$this->db->setQuery($query);
				$friendly_single = $this->db->loadObjectList();		
				//var_dump($friendly_single);
				$query = "SELECT md.m_name,m.*, t1.t_name as home, t2.t_name as away,md.s_id,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
						." WHERE md.s_id='-1' AND m.m_single='0' AND m.m_id = md.id AND m.published = 1  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='0'";		
				$this->db->setQuery($query);
				$friendly_team = $this->db->loadObjectList();
				$this->_data= @array_merge($this->_data,$friendly_single,$friendly_team);
			
		}

		return $this->_data;
	}

	function getTotal()
	{
		if (empty($this->_total))
		{
			$this->_total = count($this->_data);
		}

		return $this->_total;
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

	
	
}