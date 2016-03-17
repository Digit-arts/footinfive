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

class playerlistJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $s_id = null;
	var $t_single = null;
	var $t_type = null;
	var $_total = null;

	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->s_id = $this->mainframe->getUserStateFromRequest( 'com_joomsport.sidsel', 'sidsel', -1, 'int' );
		if($this->s_id){
			$this->mainframe->setUserState('com_joomsport.sidsel',$this->s_id);
		}
		if($this->s_id == '-1'){
			$this->s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		}

		// Get the pagination request variables
		$this->limit		= $this->mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $this->mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= JRequest::getVar( 'limitstart', 0,'', 'int' );
		$this->sortfield	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.sortfield', 'sortfield', 'name', 'string' );
		$this->sortdest		= $this->mainframe->getUserStateFromRequest( 'com_joomsport.sortdest', 'sortdest', 0, 'string' );
		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		//get tiurnament type
		if($this->s_id){
			$tourn = $this->getTournOpt($this->s_id);
			$this->t_single = $tourn->t_single;
			$this->t_type = $tourn->t_type;
		}
		
		$this->getPagination();
		
	}

	function getData()
	{
		//title
		$this->_params = $this->JS_PageTitle(JText::_('BLFA_PLAYER_LIST'));
		

		$this->getPlayerList();
		if($this->s_id){
			$this->getPlEvents();
		}else{
			//photos

			for($z=0;$z<count($this->_lists["players"]);$z++)
			{
				$def_img2 = '';
				if($this->_lists["players"][$z]["def_img"]){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists["players"][$z]["def_img"];
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}
				if(!$def_img2){
					$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists["players"][$z]["id"];
					$this->db->setQuery($query);
					$photos2 = $this->db->loadObjectList();
					if(isset($photos2[0])){
						$def_img2 = $photos2[0]->filename;
					}
				}
				$this->_lists["players"][$z]['photo'] = $def_img2;
			}	
		}
		$this->getFiltersPl();
		
		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1);
		
	}
	function getPlayerList(){
		$pln = $this->getJS_Config('player_name');
		$orderby = "ORDER BY ".$this->sortfield." ".$this->sortdest;
		if($this->s_id){
			if($this->t_single){
				$query = "SELECT DISTINCT(p.id),p.* FROM #__bl_players as p, #__bl_season_players as sp"
						." WHERE sp.player_id = p.id AND sp.season_id = ".$this->s_id
						."  ORDER BY p.first_name, p.last_name";
			}else{
				$query = "SELECT DISTINCT(p.id),p.*"
						." FROM #__bl_players as p, #__bl_players_team as pt, #__bl_season_teams as st"
						." WHERE pt.player_id = p.id AND pt.team_id = st.team_id AND st.season_id = ".$this->s_id." ".($this->s_id?" AND pt.season_id=".$this->s_id:"")
						." ORDER BY p.first_name, p.last_name";	
			}
			$this->db->setQuery($query);
			$this->_lists["players"] = $this->db->loadObjectList();
		
		}else{
			$query = "SELECT ".($pln?"IF(p.nick<>'',p.nick,CONCAT(p.first_name,' ',p.last_name))":"CONCAT(p.first_name,' ',p.last_name)")." AS name,p.* FROM #__bl_players as p"
						."  ORDER BY p.first_name, p.last_name";
			$this->db->setQuery($query);
			$this->_lists["players"] = $this->db->loadAssocList();			
		}
		
		
		
	}
	function getPlEvents(){
		$Itemid = JRequest::getInt('Itemid');
		$query = "SELECT DISTINCT(ev.id),ev.*"
				." FROM #__bl_events as ev, #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md"
				." WHERE (ev.id = me.e_id OR (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id)) AND me.match_id = m.id"
				." AND m.m_id=md.id ".($this->s_id?" AND md.s_id=".$this->s_id:"")." AND (ev.player_event = 1 OR ev.player_event = 2)"
				." ORDER BY ev.ordering";
		$this->db->setQuery($query);
		$events = $this->db->loadObjectList();
		$arr_pllist = array();
		for($z=0;$z<count($this->_lists["players"]);$z++){
			$arr_pllist[$z] = array();
			//name
			$arr_pllist[$z]["name"] = $this->selectPlayerName($this->_lists["players"][$z]);//$this->_lists["players"][$z]->first_name." ".$this->_lists["players"][$z]->last_name;
			$arr_pllist[$z]["id"] = $this->_lists["players"][$z]->id;
			
			$query = "SELECT t.id,t.t_name FROM #__bl_teams as t, #__bl_players_team as p ".($this->s_id?",#__bl_season_teams as pt":"")." WHERE p.team_id=t.id AND p.player_id=".$this->_lists["players"][$z]->id." ".($this->s_id?" AND pt.season_id=".$this->s_id." AND pt.team_id=t.id AND p.season_id=".$this->s_id:"")." ORDER BY t.t_name";
			$this->db->setQuery($query);
			$arr_pllist[$z]["teams"] = '';
			$arr_pllist[$z]["sortteams"] = '';
			$teamzar = $this->db->loadObjectList();
			if(count($teamzar)){
				foreach($teamzar as $tmz){
					$link2 = JRoute::_("index.php?option=com_joomsport&task=team&tid=".$tmz->id."&sid=".$this->s_id."&Itemid=".$Itemid);
					$arr_pllist[$z]["teams"] .= '<a href="'.$link2.'">'.$tmz->t_name.'</a> ';
					$arr_pllist[$z]["sortteams"] .= $tmz->t_name;
				}
			}
			
			//photos
			
			
			$def_img2 = '';
			if($this->_lists["players"][$z]->def_img){
				$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists["players"][$z]->def_img;
				$this->db->setQuery($query);
				$def_img2 = $this->db->loadResult();
			}
			if(!$def_img2){
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists["players"][$z]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
			}
			$arr_pllist[$z]['photo'] = $def_img2;
			
			
			for($j=0;$j<count($events);$j++){
				
				//$this->_lists["players"][$z]->stat_array[$j][0] = $events[$j]->e_name;
				$query_all = " FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md"
							." WHERE me.e_id = ".$events[$j]->id." AND me.player_id = ".$this->_lists["players"][$z]->id." AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id AND md.s_id=".$this->s_id;
				if($events[$j]->result_type == '1'){
					$query = "SELECT AVG(me.ecount) ".$query_all;

				}else{
					$query = "SELECT SUM(me.ecount) ".$query_all;

				}
				if($events[$j]->player_event == '2'){
					$query = "SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE (me.e_id = ".$events[$j]->sumev1." OR me.e_id = ".$events[$j]->sumev2.") AND me.player_id = ".$this->_lists["players"][$z]->id." AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id ".($this->s_id?"AND md.s_id=".$this->s_id:"");
				}
				$this->db->setQuery($query);
				$arr_pllist[$z][$events[$j]->id] = floatval($this->db->loadResult());
				if(!$arr_pllist[$z][$events[$j]->id]){
					$arr_pllist[$z][$events[$j]->id] = 0;
				}
				//$stat_array[$jn][2] = '&nbsp;';
				if($events[$j]->e_img && is_file('media/bearleague/events/'.$events[$j]->e_img)){
					$events[$j]->e_imgth = '<img src="media/bearleague/events/'.$events[$j]->e_img.'" title="'.$events[$j]->e_name.'" height="20" />';
				}
			
			}
		}
		
		if(count($arr_pllist)){
			$sort_arr = array();
			foreach($arr_pllist AS $uniqid => $row){
				foreach($row AS $key=>$value){
					$sort_arr[$key][$uniqid] = $value;
				}
			}
			@array_multisort($sort_arr[$this->sortfield],$this->sortdest?SORT_DESC:SORT_ASC,$arr_pllist);
		}	
		
		$this->_lists["players"] = $arr_pllist;
		
		$this->_lists["events"] = $events;
	}
	function getFiltersPl(){
		$is_tourn = array();
		
		$query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		$javascript = " onchange='document.adminForm.submit();'";
		$jqre = '<select name="sidsel" id="sid" class="styled jfsubmit" size="1" '.$javascript.'>';
		$jqre .= '<option value="0">'.JText::_('BLFA_ALL').'</option>';
		for($i=0;$i<count($tourn);$i++){
			$is_tourn2 = array();
			$query = "SELECT s.s_id as id,s.s_name as s_name"
					." FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id"
					." WHERE s.published = '1' AND t.id=".$tourn[$i]->id
					."  ORDER BY s.ordering";
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();
			
			if(count($rows)){
				$jqre .= '<optgroup label="'.$tourn[$i]->name.'">';
				for($g=0;$g<count($rows);$g++){
					$jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $this->s_id)?"selected":"").'>'.$rows[$g]->s_name.'</option>';
				}
				$jqre .= '</optgroup>';
			}
		}
		$jqre .= '</select>';

		$this->_lists['tourn'] = $jqre;
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
	function getTotal()
	{
		if (empty($this->_total))
		{
			if($this->s_id){
				if($this->t_single){
					$query = "SELECT COUNT(DISTINCT(p.id)) FROM #__bl_players as p, #__bl_season_players as sp"
							." WHERE sp.player_id = p.id AND sp.season_id = ".$this->s_id;
				}else{
					$query = "SELECT COUNT(DISTINCT(p.id))"
							." FROM #__bl_players as p, #__bl_players_team as pt, #__bl_season_teams as st, #__bl_teams as t"
							." WHERE pt.player_id = p.id AND pt.team_id = st.team_id AND st.season_id = ".$this->s_id." ".($this->s_id?" AND pt.season_id=".$this->s_id:"")." AND st.team_id=t.id";	
				}
			}else{
				
				$query = "SELECT COUNT(*) FROM #__bl_players as p"
						."  ORDER BY p.first_name, p.last_name";
			}
			$this->db->setQuery($query);
			$this->_total = $this->db->loadResult();
		}

		return $this->_total;
	}
	
}	