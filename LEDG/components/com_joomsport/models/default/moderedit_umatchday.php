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

class moderedit_umatchdayJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $_data = null;
	var $sid = null;
	var $_user = null;
	var $mid = null;
	
	function __construct()
	{
		parent::__construct();
		$this->_user	=& JFactory::getUser();
		if ( $this->_user->get('guest')) {

			$return_url = $_SERVER['REQUEST_URI'];
			$return_url = base64_encode($return_url);
			
			if($this->getVer() >= '1.6'){
				$uopt = "com_users";
			}else{
				$uopt = "com_user";
			}
			$return	= 'index.php?option='.$uopt.'&view=login&return='.$return_url;

			// Redirect to a login form
			$this->mainframe->redirect( $return, JText::_('BLFA_MSGLOGIN') );
			
		}
		$this->sid	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.sid', 'sid', $this->sid, 'int' );
		$this->mid = JRequest::getVar( 'mid', 0, '', 'int' );
		$query = "SELECT s.s_id as id,CONCAT(tr.name,' ',s.s_name) as t_name"
				." FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_tournament as tr"
				." WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id=".$this->_user->id
				." ORDER BY s.s_id desc";
		$this->db->setQuery($query);
		$seass = $this->db->loadObjectList();
		if(!$this->sid) {$this->sid = $seass[0]->id;};
		$isinseas = false;
		for($j=0;$j<count($seass);$j++){
			if($this->sid == $seass[$j]->id){
				$isinseas = true;
			}
		}
		if(!$isinseas && count($seass)){
			
			$this->sid = $seass[0]->id;
		}	
	}

	function getData()
	{
		
		$this->getFilterseas();
		$this->getFiltermday();
		//
		$this->_params = $this->JS_PageTitle("");
		
		
		
		$row 	= new JTableMday($this->db);

		$row->load($this->mid);
		
		
		$tourn = $this->getTournOpt($this->sid);
		$this->_lists["t_type"] = $tourn->t_type;
		
		$this->_lists['is_playoff'] 		= JHTML::_('select.booleanlist',  'is_playoff', 'class="inputbox"', $row->is_playoff );

		$is_team = array();

		$query = "SELECT t.*,CONCAT(t.first_name,' ',t.last_name) as t_name FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->sid)." ORDER BY t.first_name,t.last_name";

		$this->db->setQuery($query);

		$team = $this->db->loadObjectList();

		$is_team[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 't_name' ); 

		$teamis = array_merge($is_team,$team);

		$lists['teams1'] = JHTML::_('select.genericlist',   $teamis, 'teams1', 'class="inputbox" size="1" id="teams1"', 'id', 't_name', 0 );

		$lists['teams2'] = JHTML::_('select.genericlist',   $teamis, 'teams2', 'class="inputbox" size="1" id="teams2"', 'id', 't_name', 0 );

		
		$this->_data = $row;

		
		$this->getMdMatch();
		$this->getlTeams();
		
		
		//---------------------------//

		$this->_lists["msg"] = JRequest::getVar( 'msg', '', 'get', 'string', JREQUEST_ALLOWRAW );
		
		$this->_lists["teams_season"] = $this->teamsToModer();;
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
	}
	function getlTeams(){
		$is_team = array();

		$query = "SELECT t.*,CONCAT(t.first_name,' ',t.last_name) as t_name"
				." FROM #__bl_players as t , #__bl_season_players as st"
				." WHERE st.player_id = t.id AND st.season_id = ".($this->sid)
				." ORDER BY t.first_name,t.last_name";

		$this->db->setQuery($query);

		$team = $this->db->loadObjectList();

		$is_team[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 't_name' ); 

		$teamis = array_merge($is_team,$team);

		$this->_lists['teams1'] = JHTML::_('select.genericlist',   $teamis, 'teams1', 'class="inputbox" size="1" id="teams1"', 'id', 't_name', 0 );

		$this->_lists['teams2'] = JHTML::_('select.genericlist',   $teamis, 'teams2', 'class="inputbox" size="1" id="teams2"', 'id', 't_name', 0 );
	}
	function getMdMatch(){
		$query = "SELECT m.*,md.m_name,md.id as mdid, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away, t1.id as hm_id,t2.id as aw_id"
				." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
				." WHERE m.m_id = md.id AND md.s_id=".$this->sid." AND (t1.usr_id = ".$this->_user->id." OR t2.usr_id = ".$this->_user->id.")"
				." AND md.id=".$this->mid
				." ORDER BY m.id";
		
		$this->db->setQuery($query);

		$this->_lists["match"] = $this->db->loadObjectList();

	}
	function getFilterseas(){
		$query = "SELECT s.*,CONCAT(tr.name,' ',s.s_name) as t_name,tr.t_type,tr.t_single"
				." FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_tournament as tr"
				." WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id=".$this->_user->id
				." ORDER BY s.ordering,s.s_id desc";
		$this->db->setQuery($query);
		$seasons = $this->db->loadObjectList();
		$javascript = "onchange='document.filtrForm.submit();'";	
		$this->_lists['seas_filtr'] = JHTML::_('select.genericlist',   $seasons, 'sid', 'class="styled jfsubmit" size="1"'.$javascript, 's_id', 't_name', $this->sid );	
		if(!$this->sid && count($seasons)){
			$this->sid = $seasons[0]->s_id;
		}
	}
	
	function getFiltermday(){
		$query = "SELECT m.* FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_matchday as m"
				." WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id=".$this->_user->id." AND s.s_id=".$this->sid
				." ORDER BY m.id desc";
		$this->db->setQuery($query);
		$mdays = $this->db->loadObjectList();	
		
		$query = "SELECT COUNT(*) FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_matchday as m"
				." WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id=".$this->_user->id." AND s.s_id=".$this->sid." AND m.id=".$this->mid
				." ORDER BY m.id desc";

		$this->db->setQuery($query);
		if(!$this->db->loadResult()){
			$this->mid = isset($mdays[0]->id)?$mdays[0]->id:0;
		}
		
	
		if(!$this->mid && count($mdays)){
			$this->mid = $mdays[0]->id;
		}
		$javascript = "onchange='document.filtrForm.submit();'";	
		if(count($mdays)){
			$this->_lists['md_filtr'] = JHTML::_('select.genericlist',   $mdays, 'mid', 'class="styled jfsubmit" size="1"'.$javascript, 'id', 'm_name', $this->mid );
		}else{
			$this->_lists['md_filtr'] = '';
		}
	}
	
	function SaveMdUmod(){

		$post		= JRequest::get( 'post' );
	
		$row 	= new JTableMday($this->db);

	
		//$row->s_id = $s_id;
	
		unset($post['s_id']);
		if (!$row->bind( $post )) {
	
			JError::raiseError(500, $row->getError() );
	
		}
	
		if (!$row->check()) {
	
			JError::raiseError(500, $row->getError() );
	
		}
		$row->load($row->id);
		// if new item order last in appropriate group
		$query = "SELECT tr.t_type FROM #__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=".$row->s_id;
		$this->db->setQuery($query);
		
		$t_type = $this->db->loadResult();
		
		
		$query = "SELECT id FROM #__bl_players WHERE usr_id=".$this->_user->id;
		$this->db->setQuery($query);
		$playerid = $this->db->LoadResult();
	
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
					$match->is_extra = intval($_POST['extra_time'][$mj]);
					$match->published = 1;
				}
				$match->m_played = $match->m_played?$match->m_played:0;
				$match->m_date = strval($_POST['match_data'][$mj]);
				$match->m_time = strval($_POST['match_time'][$mj]);
				
				$query = "SELECT COUNT(*) FROM #__bl_players WHERE usr_id=".$this->_user->id." AND (id=".$match->team1_id." OR id=".$match->team2_id.")";
				$this->db->setQuery($query);
			
				if(!$this->db->loadResult()){
				//	JError::raiseError(500, $match->getError() );
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
				$this->db->setQuery("DELETE FROM #__bl_match WHERE id NOT IN (".implode(',',$arr_match).") AND (team1_id = ".$playerid." OR team2_id=".$playerid.") AND m_id = ".$row->id);
				$this->db->query();
			}	
		}else{
			if(!$t_type){	
				$this->db->setQuery("DELETE FROM #__bl_match WHERE (team1_id = ".$playerid." OR team2_id=".$playerid.") AND m_id = ".$row->id);
				$this->db->query();
			}	
		}	
		$this->mid = $row->id;
		$this->sid = $row->s_id;
	}
	
}