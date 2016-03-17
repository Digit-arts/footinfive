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

class moderedit_matchdayJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $mid = null;
	var $season_id = null;
	var $t_single = null;
	var $t_type = null;
	var $id = null;
	var $tid = null;
	function __construct()
	{
		parent::__construct();
		$this->tid 		= JRequest::getVar( 'tid', 0, '', 'int' );	
		$this->season_id	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.moderseason', 'moderseason', 0, 'int' );
		$this->mid = JRequest::getVar( 'mid', 0, '', 'int' );
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
		if($this->season_id == -1){
			$isinseas = true;
		}
		if(!$isinseas){
			
			$this->season_id = $seass[0]->id;
		}
	
	}

	function getData()
	{
		$this->getGlobFilters(true);
		$this->_params = $this->JS_PageTitle("");
		$this->SeasModerfilter();
		$tourn = $this->getTournOpt($this->season_id);
		if($this->season_id != -1){
			$this->t_single = $tourn->t_single;
			$this->t_type = $tourn->t_type;
			$this->_lists['s_enbl_extra'] = $tourn->s_enbl_extra;
		}else{
			$this->t_single = 0;
			$this->t_type = 0;
			$this->_lists['s_enbl_extra'] = 1;

		}
		
		$this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');
		$row 	= new JTableMday($this->db);
		$row->load($this->mid);
		
		$this->getMatchesModer($row->id);
		$this->_lists['is_playoff'] 		= JHTML::_('select.booleanlist',  'is_playoff', 'class="inputbox"', $row->is_playoff );
		
		$this->_data = $row;

		$this->getTeamListMod();
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
	}
	function SeasModerfilter(){

		$javascript = "onchange='document.filtrForm.submit();'";
		
		
		
		$query = "SELECT m.* FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_matchday as m"
				." WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.team_id=".$this->tid." AND s.s_id=".$this->season_id
				." ORDER BY m.ordering";
		if($this->season_id == -1){
			$query = "SELECT m.* FROM #__bl_matchday as m"
				." WHERE m.s_id=".$this->season_id
				." ORDER BY m.ordering";
		}
		$this->db->setQuery($query);
		$mdays = $this->db->loadObjectList();	

		$query = "SELECT COUNT(*) FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_matchday as m"
				." WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.team_id=".$this->tid
				." AND s.s_id=".$this->season_id." AND m.id=".$this->mid;
		if($this->season_id == -1){
			$query = "SELECT COUNT(*) FROM #__bl_matchday as m"
				." WHERE m.s_id=".$this->season_id." AND m.id=".$this->mid;
		}
		$this->db->setQuery($query);
		if(!$this->db->loadResult()){
			$mid = isset($mdays[0]->id)?$mdays[0]->id:0;
		}
		
		if(!$this->mid && count($mdays)){
			$this->mid = $mdays[0]->id;
		}
		
		$this->_lists['md_filtr'] = JHTML::_('select.genericlist',   $mdays, 'mid', 'class="styled jfsubmit" size="1"'.$javascript, 'id', 'm_name', $this->mid );
		if(!count($mdays)){
			$this->_lists['md_filtr'] = '';
		}
	}
	function getMatchesModer($id){
		$query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team, t.id as t1id, t2.id as t2id"
				." FROM #__bl_match as m, #__bl_teams as t, #__bl_teams as t2"
				." WHERE m.m_id = ".$id." ".($this->season_id==-1?" AND m.m_single='0'":"")." AND t.id = m.team1_id AND t2.id = m.team2_id AND (t.id = ".$this->tid." OR t2.id = ".$this->tid.")"
				." ORDER BY m.id";
		$this->db->setQuery($query);
		$this->_lists["match"] = $this->db->loadObjectList();
	}
	function getteamsSeas($sid){
		if($this->t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id"
					." FROM #__bl_players as t , #__bl_season_players as st"
					." WHERE st.player_id = t.id AND st.season_id = ".$sid
					." ORDER BY t.first_name";
		}else{
			$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st"
					." WHERE st.team_id = t.id AND st.season_id = ".$sid
					." ORDER BY t.t_name";
		}
		if($sid == -1){
			$query = "SELECT * FROM #__bl_teams ORDER BY t_name";
		}
		$this->db->setQuery($query);
		$team = $this->db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, ($this->t_single?JText::_('BLFA_SELPLAYER'):JText::_('BLFA_SELTEAM')), 'id', 't_name' ); 
		if(count($team)){
			$is_team = array_merge($is_team,$team);
		}	
		$this->_lists['teams1'] = JHTML::_('select.genericlist',   $is_team, 'teams1', 'class="inputbox" size="1" id="teams1"', 'id', 't_name', 0 );
		$this->_lists['teams2'] = JHTML::_('select.genericlist',   $is_team, 'teams2', 'class="inputbox" size="1" id="teams2"', 'id', 't_name', 0 );
		
		return $is_team;
		
	}
	function getTeamListMod(){
		$is_team = array();
		$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st"
				." WHERE st.team_id = t.id AND st.season_id = ".($this->season_id)
				." ORDER BY t.t_name";
		if($this->season_id == -1){
			$query = "SELECT * FROM #__bl_teams ORDER BY t_name";
		}		
		$this->db->setQuery($query);
		$team = $this->db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, JText::_('BLFA_SELTEAM'), 'id', 't_name' );
		if(count($team)){
			$is_team = array_merge($is_team,$team);
		}
		$this->_lists['teams1'] = JHTML::_('select.genericlist',   $is_team, 'teams1', 'class="inputbox" size="1" id="teams1"', 'id', 't_name', 0 );
		$this->_lists['teams2'] = JHTML::_('select.genericlist',   $is_team, 'teams2', 'class="inputbox" size="1" id="teams2"', 'id', 't_name', 0 );
	}
	
	function saveMdModer(){
		$tid = JRequest::getVar( 'tid', 0, '', 'int' );
		$sid = JRequest::getVar( 'sid', 0, '', 'int' );
		$mid = JRequest::getVar( 'mid', 0, '', 'int' );

		$user	=& JFactory::getUser();
		$post		= JRequest::get( 'post' );

		unset($post['m_descr']);
		unset($post['m_name']);

		$row 	= new JTableMday($this->db);

		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->load($row->id);

		$query = "SELECT tr.t_type FROM #__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=".$row->s_id;
		$this->db->setQuery($query);
		$t_type = $this->db->loadResult();	
	
		// save match
		$mj = 0;
		$arr_match = array();
		if(isset($_POST['home_team']) && count($_POST['home_team'])){
			foreach($_POST['home_team'] as $home_team){
				$match 	= new JTableMatch($this->db);
				$match->load($_POST['match_id'][$mj]);
				if(!$t_type){
					$match->m_id = $row->id;
					$match->team1_id = intval($home_team);
					$match->team2_id = intval($_POST['away_team'][$mj]);
					$match->score1 = intval($_POST['home_score'][$mj]);
					$match->score2 = intval($_POST['away_score'][$mj]);
					$match->is_extra = isset($_POST['extra_time'][$mj])?intval($_POST['extra_time'][$mj]):0;
					$match->published = 1;
				}
				$match->m_played = $match->m_played?$match->m_played:0;
				$match->m_date = strval($_POST['match_data'][$mj]);
				$match->m_time = strval($_POST['match_time'][$mj]);
				
				if(!$match->id){
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".intval($home_team);
						$this->db->setQuery($query);
						$venue = $this->db->loadResult();
						if($venue){
							$match->venue_id = $venue;
						}	
					}
				
				$query = "SELECT COUNT(*) FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid=".$user->id." AND (t.id=".$match->team1_id." OR t.id=".$match->team2_id.")";
				$this->db->setQuery($query);
		
				if(!$this->db->loadResult()){
					JError::raiseError(500, $match->getError() );
				}
				if (!$match->check()) {
					JError::raiseError(500, $match->getError() );
				}
				
				if (!$match->store()) {
					JError::raiseError(500, $match->getError() );
				}
				$match->checkin();
				$arr_match[] = $match->id;
				$mj++;
			}
			if(!$t_type){
				$this->db->setQuery("DELETE FROM #__bl_match WHERE id NOT IN (".implode(',',$arr_match).") AND (team1_id = ".$tid." OR team2_id=".$tid.") AND m_id = ".$row->id);
				$this->db->query();
			}
		}else{
			if(!$t_type){
				$this->db->setQuery("DELETE FROM #__bl_match WHERE (team1_id = ".$tid." OR team2_id=".$tid.") AND m_id = ".$row->id);
				$this->db->query();
			}
		}
		$this->id = $row->id;
		$this->tid = $tid;
		$this->mid = $mid;
		$this->season_id = $sid;
	}
	
	
}