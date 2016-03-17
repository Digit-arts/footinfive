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

class group_editJSModel extends JSPRO_Models
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
		
		$row 	= new JTableGroups($this->db);
		$row->load($is_id);
		$lists = array();	
		$is_tourn = array();
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_groups = '1' AND s.t_id = t.id ORDER BY t.name, s.s_name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		$is_tourn[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTOURNAMENT'), 'id', 'name' ); 
		$tourn_is = array_merge($is_tourn,$tourn);
		$this->_lists['tourn'] = JHTML::_('select.genericlist',   $tourn_is, 's_id', 'class="inputbox" size="1"', 'id', 'name', $row->s_id );
		
		
		
		if($is_id){
			
			$query = "SELECT t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($row->s_id)." AND s.t_id = t.id";
			$this->db->setQuery($query);
			$tourn = $this->db->loadResult();
			
			if($tourn == 1){
				$query = "SELECT t.id as id FROM #__bl_season_players as st, #__bl_players as t, #__bl_grteams as gr WHERE gr.t_id = t.id AND gr.g_id = ".$row->id." AND st.season_id = ".$row->s_id." AND t.id = st.player_id ORDER BY t.first_name";
			
			}else{
				$query = "SELECT t.id as id FROM #__bl_season_teams as st, #__bl_teams as t, #__bl_grteams as gr WHERE gr.t_id = t.id AND gr.g_id = ".$row->id." AND st.season_id = ".$row->s_id." AND t.id = st.team_id ORDER BY t.t_name";
			
			}
			
			$this->db->setQuery($query);
			$teams_season_ids = $this->db->loadResultArray();
			
			
			if($tourn == 1){
				$query = "SELECT t.id as id FROM #__bl_season_players as st, #__bl_players as t, #__bl_grteams as gr, #__bl_groups as g WHERE gr.t_id = t.id AND gr.g_id != ".$row->id." AND g.id=gr.g_id AND g.s_id=st.season_id AND st.season_id = ".$row->s_id." AND t.id = st.player_id ORDER BY t.first_name";
			
			}else{
				$query = "SELECT t.id as id FROM #__bl_season_teams as st, #__bl_teams as t, #__bl_grteams as gr, #__bl_groups as g WHERE gr.t_id = t.id AND gr.g_id != ".$row->id." AND g.id=gr.g_id AND g.s_id=st.season_id  AND st.season_id = ".$row->s_id." AND t.id = st.team_id ORDER BY t.t_name";
			
			}
			
			$this->db->setQuery($query);
			$teams_season_ogr = $this->db->loadResultArray();
			
			if($tourn == 1){
				$query = "SELECT CONCAT(p.first_name,' ',p.last_name) as t_name,p.id FROM #__bl_players as p,#__bl_season_players as st WHERE st.season_id = ".$row->s_id." AND p.id = st.player_id ".(count($teams_season_ids)?"AND p.id NOT IN (".implode(',',$teams_season_ids).")":"")." ".(count($teams_season_ogr)?"AND p.id NOT IN (".implode(',',$teams_season_ogr).")":"")." ORDER BY p.first_name";
			}else{
				$query = "SELECT * FROM #__bl_teams as t, #__bl_season_teams as st WHERE st.season_id = ".$row->s_id." AND t.id = st.team_id ".(count($teams_season_ids)?"AND t.id NOT IN (".implode(',',$teams_season_ids).")":"")." ".(count($teams_season_ogr)?"AND t.id NOT IN (".implode(',',$teams_season_ogr).")":"")." ORDER BY t.t_name";
			}
			
			$this->db->setQuery($query);
			$teams = $this->db->loadObjectList();
			$this->_lists['teams'] = @JHTML::_('select.genericlist',   $teams, 'teams_id', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
			
			if($tourn == 1){
				$query = "SELECT t.id as id, CONCAT(t.first_name,' ',t.last_name) as t_name FROM #__bl_season_players as st, #__bl_players as t, #__bl_grteams as gr WHERE gr.t_id = t.id AND gr.g_id = ".$row->id." AND st.season_id = ".$row->s_id." AND t.id = st.player_id ORDER BY t.first_name";
			
			}else{
				$query = "SELECT t.id as id, t.t_name as t_name FROM #__bl_season_teams as st, #__bl_teams as t, #__bl_grteams as gr WHERE gr.t_id = t.id AND gr.g_id = ".$row->id." AND st.season_id = ".$row->s_id." AND t.id = st.team_id ORDER BY t.t_name";
			
			}
			
			$this->db->setQuery($query);
			$teams_season = $this->db->loadObjectList();
			$this->_lists['teams2'] = @JHTML::_('select.genericlist',   $teams_season, 'teams_seasons[]', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		}
		$this->_lists['single'] = $tourn;
		
		$this->_data = $row;
		
	}
	
	public function orderGroup(){
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array(), 'post', 'array' );
		
		$row		= new JTableGroups($this->db);;
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
			
				
			}
		}
	}
	
	public function saveGroup(){
		
		$post		= JRequest::get( 'post' );
		$row 	= new JTableGroups($this->db);
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
		$query = "DELETE FROM #__bl_grteams WHERE g_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		$teams_season 		= JRequest::getVar( 'teams_seasons', array(0), '', 'array' );
		JArrayHelper::toInteger($teams_season, '');
		if(count($teams_season)){
			foreach($teams_season as $teams){
				$query = "INSERT INTO #__bl_grteams(g_id,t_id) VALUES(".$row->id.",".$teams.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		$this->_id = $row->id;
	}
	
}