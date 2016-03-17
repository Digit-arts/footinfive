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

class calendarJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $s_id = null;
	var $t_single = null;
	var $t_type = null;
	var $tid = null;
	var $mid = null;
	var $fromdate = null;
	var $todate   = null;
	var $teamhm	  = null;
	var $limit	  = null;
	var $limitstart = null;
	var $_total		= null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		
		$this->limit		= $this->mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $this->mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.limitstart', 'limitstart', 0, 'int' );
		
		$this->tid = $this->mainframe->getUserStateFromRequest( 'com_joomsport.ftid', 'ftid', 0, 'int' );
		$this->mid = $this->mainframe->getUserStateFromRequest( 'com_joomsport.fmid','fmid', 0, 'int' );
		$this->teamhm = $this->mainframe->getUserStateFromRequest( 'com_joomsport.fteamhm','fteamhm', 0, '', 'int' );
		$this->fromdate = $this->mainframe->getUserStateFromRequest( 'com_joomsport.ffromdate','ffromdate', '', 'string' );
		$this->todate = $this->mainframe->getUserStateFromRequest( 'com_joomsport.ftodate','ftodate', '', 'string' );
		if(!$this->s_id){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
		
	}

	function getData()
	{
		
		$query = "SELECT CONCAT(t.name,' ',s.s_name) FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		$p_title = $this->db->loadResult();
		
		//title
		$this->_params = $this->JS_PageTitle($p_title);
		
		//get tiurnament type
		$tourn = $this->getTournOpt($this->s_id);
		$this->t_single = $tourn->t_single;
		$this->t_type = $tourn->t_type;
		
        //$this->_lists["events"] = $this->getTemplate($this->s_id);
        
		$this->_lists["matchs"] = $this->getMatchs();
		if(count($this->_lists["matchs"])){
			for($z=0;$z<count($this->_lists["matchs"]);$z++){
				if($this->t_single){
					$this->_lists["matchs"][$z]->home = $this->selectPlayerName($this->_lists["matchs"][$z]);
					$this->_lists["matchs"][$z]->away = $this->selectPlayerName($this->_lists["matchs"][$z],"fn2","ln2","nick2");
				}
				$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.team_id"
						." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
						." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
						." AND me.match_id = ".$this->_lists["matchs"][$z]->id." AND ".($this->t_single?"me.player_id=".$this->_lists["matchs"][$z]->hm_id:"me.t_id=".$this->_lists["matchs"][$z]->hm_id)
						." ORDER BY CAST(me.minutes AS UNSIGNED)";
				$this->db->setQuery($query);
				$this->_lists["matchs"][$z]->m_events_home = $this->db->loadObjectList();
				
				$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.team_id"
						." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
						." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
						." AND me.match_id = ".$this->_lists["matchs"][$z]->id." AND ".($this->t_single?"me.player_id=".$this->_lists["matchs"][$z]->aw_id:"me.t_id=".$this->_lists["matchs"][$z]->aw_id)
						." ORDER BY CAST(me.minutes AS UNSIGNED)";
				$this->db->setQuery($query);
				
				$this->_lists["matchs"][$z]->m_events_away = $this->db->loadObjectList();
				
                $this->_lists["matchs"][$z]->betevents = $this->getMatchBetEvents($this->_lists["matchs"][$z]->id);
                
			}
		}

		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
		$this->_lists["teams_season"] = $this->teamsToModer();;
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1);
		$this->getCTFilter();
		$this->_lists['locven'] = $this->getJS_Config("cal_venue");		
		
	}
    
    function getMatchBetEvents($idmatch){
        $query = "SELECT bbc.*, bbe.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_coeffs bbc ON bbc.idevent=bbe.id"
                ."\n WHERE bbc.idmatch =".$idmatch;

        $this->db->setQuery($query);
        $matchevents = $this->db->loadObjectList();

        return $matchevents;
    }        
    
	function getMatchs(){
		
		$team_sql = '';
		if($this->tid){
			$team_sql .= $this->teamhm?(($this->teamhm == 1)?(" AND t1.id=".$this->tid):(" AND t2.id=".$this->tid)):(" AND (t1.id=".$this->tid." OR t2.id=".$this->tid.")");
		}
		
		$date_sql = '';
		if($this->fromdate){
			$date_sql .= " AND m.m_date >= '{$this->fromdate}'";
		}
		if($this->todate){
			$date_sql .= " AND m.m_date <= '{$this->todate}'";
		}
		
		if($this->t_single){
			$query = "SELECT m.*,md.m_name,md.id as mdid,t1.first_name, t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,t1.id as hm_id,t2.id as aw_id, m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
					.$team_sql
					.$date_sql
					." ORDER BY m.m_date,m.m_time,md.ordering,md.id";
		}else{
			$query = "SELECT m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id, m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
					.$team_sql
					.$date_sql
					.($this->mid?" AND md.id=".$this->mid:"")
					." ORDER BY m.m_date,m.m_time,md.ordering,md.id";
		}
		$this->db->setQuery($query,$this->limitstart, $this->limit);
    
		return $this->db->loadObjectList();
	}
	function getCTFilter(){
		if($this->t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id"
					." FROM #__bl_players as t, #__bl_season_players as st"
					." WHERE st.player_id = t.id AND st.season_id = ".$this->s_id
					." ORDER BY t.first_name";		
		}else{
			$query = "SELECT t.* FROM #__bl_teams as t, #__bl_season_teams as st"
					." WHERE st.team_id = t.id AND st.season_id = ".$this->s_id
					." ORDER BY t.t_name";	
		}
		
		$this->db->setQuery($query);
		$parti = $this->db->loadObjectList();
		
		$arr = array();
		$arr[] = JHTML::_("select.option",0,JText::_('BLFA_ALL'),'id','t_name');
		
		if(count($parti)){
			$arr = array_merge($arr,$parti);
		}
		$javascript = "";
		$this->_lists['teams'] = JHTML::_('select.genericlist',   $arr, 'ftid', 'class="inputbox" size="1" '.$javascript, 'id', 't_name', $this->tid);
		
		$arr = array();
		$arr[] = JHTML::_("select.option",0,JText::_('BLFA_ALL'),'id','m_name');
		
		$query = "SELECT id,m_name FROM #__bl_matchday WHERE s_id=".$this->s_id." ORDER BY ordering,id";
		$this->db->setQuery($query);
		$mdays = $this->db->loadObjectList();
		if(count($mdays)){
			$arr = array_merge($arr,$mdays);
		}

		$this->_lists['mdays'] = JHTML::_('select.genericlist',   $arr, 'fmid', 'class="inputbox" size="1" '.$javascript, 'id', 'm_name', $this->mid);
		
		$this->_lists['fromdate'] = JHTML::_('calendar', $this->fromdate, 'ffromdate', 'ffromdate', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10')); 
		$this->_lists['todate'] = JHTML::_('calendar', $this->todate, 'ftodate', 'ftodate', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10')); 
		
		$arr = array();
		$arr[] = JHTML::_("select.option",0,JText::_('BLFA_ALL'),'id','name');
		$arr[] = JHTML::_("select.option",1,JText::_('BLFA_HOMETEAM'),'id','name');
		$arr[] = JHTML::_("select.option",2,JText::_('BLFA_AWAYTEAM'),'id','name');
		$this->_lists['teamhm'] = JHTML::_('select.genericlist',   $arr, 'fteamhm', 'class="inputbox" size="1" '.$javascript, 'id', 'name', $this->teamhm);
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
				$team_sql = '';
			if($this->tid){
				$team_sql .= $this->teamhm?(($this->teamhm == 1)?(" AND t1.id=".$this->tid):(" AND t2.id=".$this->tid)):(" AND (t1.id=".$this->tid." OR t2.id=".$this->tid.")");
			}
			
			$date_sql = '';
			if($this->fromdate){
				$date_sql .= " AND m.m_date >= '{$this->fromdate}'";
			}
			if($this->todate){
				$date_sql .= " AND m.m_date <= '{$this->todate}'";
			}
			
			if($this->t_single){
			$query = "SELECT COUNT(m.id)"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
					.$team_sql
					.$date_sql;
			}else{
				$query = "SELECT COUNT(m.id)"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
						.$team_sql
						.$date_sql
						.($this->mid?" AND md.id=".$this->mid:"");
			}
			$this->db->setQuery($query);
			$this->_total = $this->db->loadResult();
		}

		return $this->_total;
	}
	
    function saveBets(){
        $betmatches = JRequest::getVar('bet_match');
        $bet_events_radio = JRequest::getVar('betevents_radio');
        $bet_events_points1 = JRequest::getVar('betevents_points1');
        $bet_events_points2 = JRequest::getVar('betevents_points2');
        if ($betmatches) {
            $userpoints = $this->getUserPoints(JFactory::getUser()->get('id'));
            $points = 0;
            $matches = array();
            foreach($betmatches as $idmatch){
                $match = new JTableMatch($this->db);
                $match->load($idmatch);
                if($match->betfinishdate.' '.$match->betfinishtime > date('Y-m-d H:i') && $match->betavailable){
                    $matches[] = $match;
                    if ($bet_events_radio[$idmatch]){
                        foreach($bet_events_radio[$idmatch] as $idevent=>$value){
                            $points += (float)$bet_events_points1[$idmatch][$idevent] + (float)$bet_events_points2[$idmatch][$idevent];
                        }
                    }
                }
            }
            if ($userpoints < $points) {
                return BLFA_BET_NOT_ENOUGH_POINTS;
            }
            
            if ($matches) {
                foreach($matches as $match){
                    $idmatch = $match->id;
                    if ($bet_events_radio[$idmatch]){
                        foreach($bet_events_radio[$idmatch] as $idevent=>$value){
                            $who=0;
                            if ((float)$bet_events_points1[$idmatch][$idevent]){
                                $currentbetpoints = (float)$bet_events_points1[$idmatch][$idevent];
                                $who=1;
                            } elseif ((float)$bet_events_points2[$idmatch][$idevent]){
                                $currentbetpoints = (float)$bet_events_points2[$idmatch][$idevent];
                                $who=2;
                            }
                            $this->saveBet($currentbetpoints, $idmatch, $idevent, $who);
                        }
                    }
                }
            }
        }
        return 1;
    }
}	