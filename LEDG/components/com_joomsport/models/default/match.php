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

class matchJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $s_id = null;
	var $t_single = null;
	var $t_type = null;
	var $m_id = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->m_id = JRequest::getVar( 'id', 0, '', 'int' );
		
		if(!$this->m_id){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		
	}

	function getData()
	{
		
		$query = "SELECT s_id FROM #__bl_matchday as md, #__bl_match as m  WHERE md.id=m.m_id AND m.id = ".$this->m_id;
		$this->db->setQuery($query);
		$this->s_id = $this->db->loadResult();
		
		$row 	= new JTableMatch($this->db);
		$row->load($this->m_id);

		//get tiurnament type
		if($this->s_id != -1){
			$tourn = $this->getTournOpt($this->s_id );
			$this->t_single = $tourn->t_single;
			$this->t_type = $tourn->t_type;
			$this->_lists["s_enbl_extra"] = $tourn->s_enbl_extra;
		}else{

			$this->t_type=0;
			if($row->m_single == 1){
				$this->t_single = 1;

			}else{
				$this->t_single = 0;
			}
			
			$this->_lists["s_enbl_extra"] = 0;
		}		

		$this->_lists["match"] = $this->getMatch();
		//title
		$match = $this->_lists["match"];
		
		$this->_params = $this->JS_PageTitle($match->home.' '.($match->m_played?$match->score1:'-').':'.($match->m_played?$match->score2:'-').' '.$match->away);

		$this->_lists["season_par"] = $this->getSParametrs($this->s_id );
		
		$this->getMEvents($match);
		
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 3 AND cat_id = ".$this->m_id;
		$this->db->setQuery($query);
		$this->_lists["photos"] = $this->db->loadObjectList();
		
		///--------MAPS--------------///
		$query = "SELECT m.*,mp.m_score1,mp.m_score2 FROM #__bl_seas_maps as sm, #__bl_maps as m LEFT JOIN #__bl_mapscore as mp ON m.id=mp.map_id AND mp.m_id=".$this->m_id." WHERE m.id=sm.map_id AND sm.season_id=".$this->s_id." ORDER BY m.id";
		$this->db->setQuery($query);
		$this->_lists['maps'] = $this->db->loadObjectList();
		
		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1);
		
		$this->_lists["mcomments"] = $this->getJS_Config("mcomments");
		if($this->_lists["mcomments"]){
			$this->getComments();
		}
		///line up
		$this->getLineUps($match);
		//betts
		if($this->isBet()){
			$this->_lists["betevents"] = $this->getMatchBetEvents($this->m_id);
		}
		
		///-----EXTRAFIELDS---//
		
		$this->_lists['ext_fields'] = $this->getAddFields($this->m_id,'2','match');
		
		//social buttons
		$tt = $match->home.' '.($match->m_played?$match->score1:'-').':'.($match->m_played?$match->score2:'-').' '.$match->away;
		$this->_lists['socbut'] = $this->getSocialButtons('jsbp_match',$tt,'',htmlspecialchars(strip_tags($match->match_descr)));
	}
	function getLineUps($match){
		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id={$match->hm_id} AND s.mainsquard = '1'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard1'] = $this->db->loadObjectList();
		if(count($this->_lists['squard1'])){
			for($i=0;$i<count($this->_lists['squard1']);$i++){
				$this->_lists['squard1'][$i]->name = $this->selectPlayerName($this->_lists['squard1'][$i]);
				$def_img2 = '';
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard1'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard1'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard1'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
				if($def_img2 && is_file('media/bearleague/'.$def_img2)){
					$img = "<img class='team-embl player-ico' src='".JUri::Base()."media/bearleague/".$def_img2."' />";
				}else{
					$img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
				}
				$this->_lists['squard1'][$i]->photo = $img;
			}
		}
		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id={$match->aw_id} AND s.mainsquard = '1'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard2'] = $this->db->loadObjectList();
		if(count($this->_lists['squard2'])){
			for($i=0;$i<count($this->_lists['squard2']);$i++){
				$def_img2 = '';
				$this->_lists['squard2'][$i]->name = $this->selectPlayerName($this->_lists['squard2'][$i]);
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard2'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard2'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard2'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
				if($def_img2 && is_file('media/bearleague/'.$def_img2)){
					$img = "<img class='team-embl player-ico' src='".JUri::Base()."media/bearleague/".$def_img2."' />";
				}else{
					$img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
				}
				$this->_lists['squard2'][$i]->photo = $img;
			}
		}
		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id={$match->hm_id} AND s.mainsquard = '0'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard1_res'] = $this->db->loadObjectList();
		if(count($this->_lists['squard1_res'])){
			for($i=0;$i<count($this->_lists['squard1_res']);$i++){
				$def_img2 = '';
				$this->_lists['squard1_res'][$i]->name = $this->selectPlayerName($this->_lists['squard1_res'][$i]);
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard1_res'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard1_res'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard1_res'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
				if($def_img2 && is_file('media/bearleague/'.$def_img2)){
					$img = "<img class='team-embl player-ico' src='".JUri::Base()."media/bearleague/".$def_img2."' />";
				}else{
					$img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
				}
				$this->_lists['squard1_res'][$i]->photo = $img;
			}
		}
		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id={$match->aw_id} AND s.mainsquard = '0'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard2_res'] = $this->db->loadObjectList();
		if(count($this->_lists['squard2_res'])){
			for($i=0;$i<count($this->_lists['squard2_res']);$i++){
				$def_img2 = '';
				$this->_lists['squard2_res'][$i]->name = $this->selectPlayerName($this->_lists['squard2_res'][$i]);
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard2_res'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard2_res'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard2_res'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
				if($def_img2 && is_file('media/bearleague/'.$def_img2)){
					$img = "<img class='team-embl player-ico' src='".JUri::Base()."media/bearleague/".$def_img2."' />";
				}else{
					$img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
				}
				$this->_lists['squard2_res'][$i]->photo = $img;
			}
		}
		//subs in
		$query = "SELECT s.*,p1.first_name as p1first,p1.last_name as p1last,p1.nick as p1nick,p2.first_name as p2first,p2.last_name as p2last,p2.nick as p2nick,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$this->m_id." AND s.team_id={$match->hm_id} ORDER BY s.minutes";
		$this->db->setQuery($query);
		$this->_lists['subsin1'] = $this->db->loadObjectList();
		if(count($this->_lists['subsin1'])){
			for($i=0;$i<count($this->_lists['subsin1']);$i++){
				$this->_lists['subsin1'][$i]->plin = $this->selectPlayerName($this->_lists['subsin1'][$i],"p1first","p1last","p1nick");
				$this->_lists['subsin1'][$i]->plout = $this->selectPlayerName($this->_lists['subsin1'][$i],"p2first","p2last","p2nick");
			}
		}	
		$query = "SELECT s.*,p1.first_name as p1first,p1.last_name as p1last,p1.nick as p1nick,p2.first_name as p2first,p2.last_name as p2last,p2.nick as p2nick,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$this->m_id." AND s.team_id={$match->aw_id} ORDER BY s.minutes";
		$this->db->setQuery($query);
		$this->_lists['subsin2'] = $this->db->loadObjectList();
		if(count($this->_lists['subsin2'])){
			for($i=0;$i<count($this->_lists['subsin2']);$i++){
				$this->_lists['subsin2'][$i]->plin = $this->selectPlayerName($this->_lists['subsin2'][$i],"p1first","p1last","p1nick");
				$this->_lists['subsin2'][$i]->plout = $this->selectPlayerName($this->_lists['subsin2'][$i],"p2first","p2last","p2nick");
			}
		}
		
	}
	function getComments(){
		$this->_lists["usera"]	=& JFactory::getUser();
		if($this->getVer() >= '1.6'){
			$query = "SELECT DISTINCT(c.id),c.*,IF(pl.nick <> '',pl.nick,p.name) as nick, p.id as usrid"
					." FROM `#__bl_comments` as c, #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id"
					." WHERE c.match_id = ".$this->m_id." AND c.user_id=p.id"
					." ORDER BY c.date_time";
		
		}else{
			$query = "SELECT DISTINCT(c.id), c.*,IF(pl.nick <> '',pl.nick,p.name) as nick, IF(p.gid <> 25,'0','1') as gid, p.id as usrid"
					." FROM `#__bl_comments` as c, #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id"
					." WHERE c.match_id = ".$this->m_id." AND c.user_id=p.id"
					." ORDER BY c.date_time";
		
		}
		
		$this->db->setQuery($query);
		$this->_lists["comments"] = $this->db->loadObjectList();
		if($this->getVer() >= '1.6'){
			$query = "SELECT IF(m.group_id <> 8,'','1') as gid"
					." FROM  #__users as p, #__user_usergroup_map as m"
					." WHERE m.user_id=p.id AND p.id=".$this->_lists["usera"]->id;
		}else{
			$query = "SELECT IF(p.gid <> 25,'0','1') as gid"
					." FROM #__users as p"
					." WHERE p.id=".$this->_lists["usera"]->id;
		}
		$this->db->setQuery($query);
		$this->_lists["comments_adm"] = $this->db->loadResult();
	}
	function getMatch(){
		if($this->t_single){
			$query = "SELECT m.*,md.m_name,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish,"
					 ." md.s_id,t1.id as hm_id,t2.id as aw_id"
					 ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
					 ." WHERE m.m_id = md.id AND m.published = 1 AND m.id = ".$this->m_id;
		}else{
			$query = "SELECT m.*,md.m_name,t1.t_name as home, t2.t_name as away,md.s_id,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2,m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
					." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.id = ".$this->m_id;
		}
		$this->db->setQuery($query);
		$match = $this->db->loadObject();
		if($this->t_single){
			$match->home = $this->selectPlayerName($match);
			$match->away = $this->selectPlayerName($match,"fn2","ln2","nick2");
		}
		return $match;
	}
	function getMEvents($match){
		$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.team_id,p.first_name,p.last_name,p.nick"
				." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
				." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
				." AND me.match_id = ".$this->m_id." AND ".($this->t_single?"me.player_id=".$match->hm_id:"me.t_id=".$match->hm_id)
				." ORDER BY CAST(me.minutes AS UNSIGNED)";
		$this->db->setQuery($query);
		$this->_lists["m_events_home"] = $this->db->loadObjectList();
		
		if(count($this->_lists['m_events_home'])){
			for($i=0;$i<count($this->_lists['m_events_home']);$i++){
				$this->_lists['m_events_home'][$i]->p_name = $this->selectPlayerName($this->_lists['m_events_home'][$i]);
			}
		}
		
		$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.team_id,p.first_name,p.last_name,p.nick"
				." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
				." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
				." AND me.match_id = ".$this->m_id." AND ".($this->t_single?"me.player_id=".$match->aw_id:"me.t_id=".$match->aw_id)
				." ORDER BY CAST(me.minutes AS UNSIGNED)";
		$this->db->setQuery($query);
		
		$this->_lists["m_events_away"] = $this->db->loadObjectList();
		if(count($this->_lists['m_events_away'])){
			for($i=0;$i<count($this->_lists['m_events_away']);$i++){
				$this->_lists['m_events_away'][$i]->p_name = $this->selectPlayerName($this->_lists['m_events_away'][$i]);
			}
		}
		
		$query = "SELECT me.*,ev.*,p.t_name as p_name,p.id FROM #__bl_match_events as me, #__bl_events as ev, #__bl_teams as p"
				." WHERE me.t_id = p.id AND me.t_id = ".$match->hm_id." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->m_id
				." ORDER BY ev.e_name";
		$this->db->setQuery($query);
		$this->_lists["h_events"] = $this->db->loadObjectList();
		
		$query = "SELECT me.*,ev.*,p.t_name as p_name,p.id FROM #__bl_match_events as me, #__bl_events as ev ,#__bl_teams as p"
				." WHERE me.t_id = p.id AND me.t_id = ".$match->aw_id." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->m_id
				." ORDER BY ev.e_name";
		$this->db->setQuery($query);
		$this->_lists["a_events"] = $this->db->loadObjectList();
		
	}
	
	///betting
	function getMatchBetEvents($idmatch){
        $query = "SELECT bbc.*, bbe.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_coeffs bbc ON bbc.idevent=bbe.id"
                ."\n WHERE bbc.idmatch =".$idmatch;

        $this->db->setQuery($query);
        $matchevents = $this->db->loadObjectList();

        return $matchevents;
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