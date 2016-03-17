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

class tableJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $s_id = null;
	var $gr_id = null;
	var $t_single = null;
	var $t_type = null;
	var $_layout = null;
	var $_params = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->gr_id = JRequest::getVar( 'gr_id', 0, '', 'int' );
		$this->s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		
		$query = "SELECT COUNT(*) FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.published='1'  AND t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		if(!$this->s_id || !$this->db->loadResult()){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		
	}

	function getData()
	{
		
		$query = "SELECT CONCAT(t.name,' ',s.s_name) FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		$p_title = $this->db->loadResult();
		$this->_params = $this->JS_PageTitle($p_title);
		//get season options
		$this->SeasOptsAll();
		//get tiurnament type
		$tourn = $this->getTournOpt($this->s_id);
		$this->t_single = $tourn->t_single;
		$this->t_type = $tourn->t_type;
		//season admin links
		$this->_lists['adm_links'] = $this->getAdmLinks();
		$this->_lists['teamlogo_height'] = $this->getJS_Config('teamlogo_height');
		//get matches
		$this->_lists['matches'] = $this->getmatches();
		//unable registration?
		$this->_lists["unable_reg"] = $this->unblSeasonReg();
		//
		if($this->t_type){
			$this->KnockTable();
			$this->_layout = "_knock";
		}else{
			$this->TournTable();
			$this->_layout = "";
		}
		//
		
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],$this->_lists["unable_reg"],$this->s_id,1);
		$query = "SELECT * FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		$this->_lists['curseas'] = $this->db->loadObject();
	
		//tabs
		//$this->getTabsLT();
		
		///-----EXTRAFIELDS---//
		
		$this->_lists['ext_fields'] = $this->getAddFields($this->s_id,'3','season');
		
		//social buttons
		$tLogo = '';
		if($this->_lists["curseas"]->logo && is_file('media/bearleague/'.$this->_lists["curseas"]->logo)){
			$tLogo = JURI::base()."media/bearleague/".$this->_lists["curseas"]->logo;
		}
		$this->_lists['socbut'] = $this->getSocialButtons('jsbp_season',$p_title,$tLogo,htmlspecialchars(strip_tags($this->_lists['season_par']->s_descr)));
		
	}
	function getTabsLT(){
		$this->_lists['jstabs'] = '';
		$this->set_JS_tabs();
		$str = '';
		$divs = array();
		$str .= $this->jstab->newTab(JText::_('BL_TAB_TBL'),'etab_main','tab_star',(($this->_lists["unable_reg"] && $this->_lists['season_par']->s_rules)?'hide':'vis'));
	    $divs[] = "etab_main_div";
		if($this->_lists['season_par']->s_rules){
		  $str .= $this->jstab->newTab(JText::_('BL_TAB_RULES'),'etab_rules','tab_flag',($this->_lists["unable_reg"]?'vis':'hide'));
		  $divs[] = "etab_rules_div";
	    }
	    if($this->_lists['season_par']->s_descr){
		  $str .= $this->jstab->newTab(JText::_('BL_TAB_ABOUTSEAS'),'etab_aboutm','tab_flag');
		  $divs[] = "etab_aboutm_div";
	    }
		
		if($this->jstab->count > 1){
			$this->_lists['jstabs'] = $str;
			$this->_lists['jstabs_divs'] = $divs;
		}
	}
	function SeasOptsAll(){
		$this->_lists["soptions"] = array();	
		$query = "SELECT * FROM #__bl_season_option WHERE s_id = ".$this->s_id." ";
		$this->db->setQuery($query);
		$listsss = $this->db->loadObjectList();
		for($i=0;$i<count($listsss);$i++){
			$vars = get_object_vars( $listsss[$i]  );
			$this->_lists["soptions"][$vars["opt_name"]] = $vars["opt_value"];
		}
	}
	function getmatches(){
		$orderby = $this->t_type?"md.id,m.k_stage,m.k_ordering":"m.m_date,m.m_time,md.id";
		if($this->t_single){
			$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.nick as nick1,t2.nick as nick2, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t1.id,t2.id) as winnerid "
					." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id
					." ORDER BY ".$orderby;
		
		}else{
			$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
					." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id
					." ORDER BY ".$orderby;
		
		}
		
		$this->db->setQuery($query);
    
		return $this->db->loadObjectList();
	}
	
	function TournTable(){
		$groups_exists = array();	
		$table_view = array();
		$user	=& JFactory::getUser();
		
		$teams_your_color = $this->getJS_Config('yteam_color');
		$season_par = $this->_lists['season_par'];
		if($this->t_single){
			$query = "SELECT t.id,bonus_point,t.first_name,t.last_name,'' as t_yteam,t.nick"
					." FROM #__bl_season_players as st, #__bl_players as t"
					." WHERE t.id = st.player_id AND st.season_id = ".$this->s_id;
		}else{
			$query = "SELECT t.id,bonus_point,t.t_yteam,t.t_name,t.t_emblem"
					." FROM #__bl_season_teams as st, #__bl_teams as t"
					." WHERE t.id = st.team_id AND st.season_id = ".$this->s_id;
		}
		$this->db->setQuery($query);
		$teams = $this->db->loadObjectList();
		
		$bonus_not = array();
		
		for ($i=0;$i<count($teams);$i++){
			$tid = $teams[$i]->id;
			if($this->t_single){
				$teams_name = $this->selectPlayerName($teams[$i]);
			}else{
				$teams_name = $teams[$i]->t_name;
			}
			$teams_your = $teams[$i]->t_yteam;
			
			
			if($this->t_single){
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$tid;
				$this->db->setQuery($query);
				$photos = $this->db->loadObjectList();
				
				$query = "SELECT p.*,c.country,c.ccode FROM #__bl_players as p LEFT JOIN #__bl_countries as c ON c.id=p.country_id  WHERE p.id = ".$tid;
				$this->db->setQuery($query);
				$players = $this->db->loadObjectList();
				$player = $players[0];
				
				$emblems = '';
				if($player->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$player->def_img;
					$this->db->setQuery($query);
					$emblems = $this->db->loadResult();
				}else if(isset($photos[0])){
					$emblems = $photos[0]->filename;
				}
			}else{
				$emblems = $teams[$i]->t_emblem;
			}
			
			if($this->t_single){
				$query = "SELECT gr.g_id FROM  #__bl_season_players as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$this->s_id." AND g.id = gr.g_id AND gr.t_id = st.player_id AND st.season_id = ".$this->s_id." AND st.player_id = ".$tid." LIMIT 1";
			
			}else{
				$query = "SELECT gr.g_id FROM  #__bl_season_teams as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$this->s_id." AND g.id = gr.g_id AND gr.t_id = st.team_id AND st.season_id = ".$this->s_id." AND st.team_id = ".$tid." LIMIT 1";
			}
			$this->db->setQuery($query);
			$group_id = $this->db->loadResult();
			if(!in_array($group_id,$groups_exists) && $group_id){
				if($this->gr_id && $season_par->s_groups){	
					if($this->gr_id==$group_id){
						$groups_exists[] = $group_id;
					}
				}else{
					$groups_exists[] = $group_id;
				}
				
			}
			$this->_lists["gr_id"] = $this->gr_id;
			$teams[$i]->bonus_point;
			if($teams[$i]->bonus_point && $teams[$i]->bonus_point != '0.00'){
				if(!isset($bonus_not[$group_id]) || !$bonus_not[$group_id]){
					
					$bonus_not[$group_id] = $teams_name." - ".$teams[$i]->bonus_point."<br />";
				}else{
					$bonus_not[$group_id] .= $teams_name." - ".$teams[$i]->bonus_point."<br />";
				}
			}
			
			
			//var_dump($this->_lists['bonus_not']);
			// in groups
			$query = "SELECT t_id FROM #__bl_grteams WHERE t_id != ".$tid." AND g_id = ".$group_id;
			$this->db->setQuery($query);
			$gtid = $this->db->loadResultArray();
			//var_dump($gtid);
			if(count($gtid)){
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((".$tid." = m.team1_id AND m.score1 > m.score2 AND m.team2_id IN (".implode(",",$gtid).")) OR (".$tid." = m.team2_id AND m.score1 < m.score2 AND m.team1_id IN (".implode(",",$gtid).")) )  AND m.m_played = 1 AND md.is_playoff = 0";
				$this->db->setQuery($query);
				$wins_gr = $this->db->loadResult();
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((".$tid." = m.team1_id AND m.score1 < m.score2 AND m.team2_id IN (".implode(",",$gtid).")) OR (".$tid." = m.team2_id AND m.score1 > m.score2 AND m.team1_id IN (".implode(",",$gtid).")) )  AND md.is_playoff = 0 AND m.m_played = 1";
				$this->db->setQuery($query);
				$loose_gr = $this->db->loadResult();
				
				if(($wins_gr+$loose_gr) > 0){
					$table_view[$i]['winperc_gr'] = ($wins_gr)/($wins_gr+$loose_gr);
				}else{
					$table_view[$i]['winperc_gr'] = 0;
				}
			}
			else{
				$wins_gr = 0;
				$loose_gr = 0;
			}
			
			$query = "SELECT SUM(score1) as sc,SUM(score2) as rc FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND m.team1_id = ".$tid;
			$this->db->setQuery($query);
			$home = $this->db->loadObjectList();
			$query = "SELECT SUM(score1) as rc,SUM(score2) as sc FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND m.team2_id = ".$tid;
			$this->db->setQuery($query);
			$away = $this->db->loadObjectList();
			
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 > m.score2) AND m.is_extra = 0 AND m.m_played = 1 AND md.is_playoff = 0";
			$this->db->setQuery($query);
			$wins = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0";
			$this->db->setQuery($query);
			$drows = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 < m.score2) AND m.is_extra = 0 AND md.is_playoff = 0 AND m.m_played = 1";
			$this->db->setQuery($query);
			$loose = $this->db->loadResult();
			
			$query = "SELECT SUM(bonus1) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team1_id AND md.is_playoff = 0 AND m.m_played = 1";
			$this->db->setQuery($query);
			$bonus1 = $this->db->loadResult();
			$query = "SELECT SUM(bonus2) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team2_id AND md.is_playoff = 0 AND m.m_played = 1";
			$this->db->setQuery($query);
			$bonus2 = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (m.team2_id = ".$tid." AND m.score2 > m.score1) AND m.is_extra = 0 AND m.m_played = 1 AND md.is_playoff = 0";
			$this->db->setQuery($query);
			$wins_away = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0";
			$this->db->setQuery($query);
			$drows_away = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id AND m.score2 < m.score1)  AND m.is_extra = 0 AND md.is_playoff = 0 AND m.m_played = 1";
			$this->db->setQuery($query);
			$loose_away = $this->db->loadResult();
			
			$query = "SELECT SUM(points1) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team1_id AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '1'";
			$this->db->setQuery($query);
			$homebonus = $this->db->loadResult();
			$query = "SELECT SUM(points2) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team2_id AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '1'";
			$this->db->setQuery($query);
			$awabonus = $this->db->loadResult();
			//--// 
			
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 > m.score2) AND m.m_played = 1 AND m.is_extra = 0 AND md.is_playoff = 0 AND m.new_points = '0'";
				$this->db->setQuery($query);
				$wins2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id) AND m.score1 = m.score2  AND m.m_played = 1  AND m.is_extra = 0 AND md.is_playoff = 0 AND m.new_points = '0'";
				$this->db->setQuery($query);
				$drows2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 < m.score2) AND md.is_playoff = 0 AND m.is_extra = 0 AND m.m_played = 1 AND m.new_points = '0'";
				$this->db->setQuery($query);
				$loose2 = $this->db->loadResult();
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (m.team2_id = ".$tid." AND m.score2 > m.score1)  AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0' AND m.is_extra = 0";
				$this->db->setQuery($query);
				$wins_away2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0' AND m.is_extra = 0";
				$this->db->setQuery($query);
				$drows_away2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id AND m.score2 < m.score1)   AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '0' AND m.is_extra = 0";
				$this->db->setQuery($query);
				$loose_away2 = $this->db->loadResult();
			//--//
			
			if($season_par->s_enbl_extra){
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((m.team2_id = ".$tid." AND m.score2 > m.score1) OR (".$tid." = m.team1_id AND m.score1 > m.score2)) AND m.is_extra = 1 AND md.is_playoff = 0 AND m.m_played = 1";
				$this->db->setQuery($query);
				$wins_ext = $this->db->loadResult();
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((".$tid." = m.team2_id AND m.score2 < m.score1) OR (".$tid." = m.team1_id AND m.score1 < m.score2)) AND m.is_extra = 1 AND md.is_playoff = 0 AND m.m_played = 1";
				$this->db->setQuery($query);
				$loose_ext = $this->db->loadResult();
			}
			
			
			$table_view[$i]['g_id'] = $season_par->s_groups ? $group_id : 0;
			$table_view[$i]['tid'] = $tid;
			$table_view[$i]['name'] = $teams_name;
			$table_view[$i]['played'] = $wins + $drows + $loose +$wins_away+$drows_away+$loose_away + (($season_par->s_enbl_extra)?($wins_ext + $loose_ext):0);
			$table_view[$i]['win'] = $wins +$wins_away;
			$table_view[$i]['draw'] = $drows+$drows_away;
			$table_view[$i]['lost'] = $loose+$loose_away;
			if($season_par->s_enbl_extra){
				$table_view[$i]['extra_win'] = $wins_ext;
				$table_view[$i]['extra_lost'] = $loose_ext;
			}
			$table_view[$i]['goals'] = ($home[0]->sc + $away[0]->sc).' - '.($home[0]->rc + $away[0]->rc);
			
			$table_view[$i]['gd'] = ($home[0]->sc + $away[0]->sc) - ($home[0]->rc + $away[0]->rc);
			$table_view[$i]['points'] = ($wins2 * $season_par->s_win_point + $wins_away2 * $season_par->s_win_away) + ($drows2 * $season_par->s_draw_point + $drows_away2 * $season_par->s_draw_away) + ($loose2 * $season_par->s_lost_point + $loose_away2 * $season_par->s_lost_away) + $homebonus + $awabonus + $bonus1 + $bonus2 +$teams[$i]->bonus_point + (($season_par->s_enbl_extra)?($wins_ext*$season_par->s_extra_win + $loose_ext*$season_par->s_extra_lost):0);
			
			$table_view[$i]['goal_score'] = $home[0]->sc + $away[0]->sc;	
			
			$table_view[$i]['yteam'] = $teams_your?$teams_your_color:'';
			
			if($table_view[$i]['played']){
				$table_view[$i]['winperc'] = ($wins + $wins_away + (( $season_par->s_enbl_extra && isset($wins_ext))?$wins_ext:0) + ($table_view[$i]['draw']/2))/($table_view[$i]['played']);
			}else{
				$table_view[$i]['winperc'] = 0;
			}
			
			$query = "SELECT * FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$tid." AND ev.season_id=".$this->s_id." WHERE ef.published=1 AND ef.type = '1' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ".($user->get('guest')?" AND ef.faccess='0'":"")." ORDER BY ef.ordering";
	
			$this->db->setQuery($query);
			$lists['ext_fields'] = $this->db->loadObjectList();
			$mj=0;
			if(isset($lists['ext_fields'])){
				foreach ($lists['ext_fields'] as $extr){
					if($extr->field_type == '3'){
						$query = "SELECT sel_value FROM #__bl_extra_select WHERE id='".$extr->fvalue."'";
						$this->db->setQuery($query);
						$selvals = $this->db->loadResult();
						if(isset($selvals) && $selvals){
							$table_view[$i]['ext_fields'][$mj] = $selvals;
						}else{
							$table_view[$i]['ext_fields'][$mj] = '&nbsp;';
						}
					}else
					if($extr->field_type == '1'){
						$table_view[$i]['ext_fields'][$mj]	= $extr->fvalue?JText::_("Yes"):JText::_("No");
					}else if($extr->field_type == '2'){
						$table_view[$i]['ext_fields'][$mj] = $extr->fvalue_text?$extr->fvalue_text:'&nbsp;';
					
					
					}else{
						$table_view[$i]['ext_fields'][$mj] = $extr->fvalue?$extr->fvalue:"&nbsp;";
					}
					
					$mj++;
				}
			}
			
			
			$table_view[$i]['avulka_v'] = '';
			$table_view[$i]['avulka_cf'] = '';
			$table_view[$i]['avulka_cs'] = '';
			$table_view[$i]['avulka_qc'] = '';
			$table_view[$i]['t_emblem'] = $emblems;
			
			///2.0.7
			$table_view[$i]['goals_score'] = $home[0]->sc + $away[0]->sc;
			$table_view[$i]['goals_conc'] = $home[0]->rc + $away[0]->rc;
			$table_view[$i]['win_home'] = $wins;
			$table_view[$i]['draw_home'] = $drows;
			$table_view[$i]['lost_home'] = $loose;
			$table_view[$i]['win_away'] = $wins_away;
			$table_view[$i]['draw_away'] = $drows_away;
			$table_view[$i]['lost_away'] = $loose_away;
			$table_view[$i]['points_home'] = ($wins2) * $season_par->s_win_point + ($drows2) * $season_par->s_draw_point + ($loose2) * $season_par->s_lost_point + $homebonus +$bonus1;
			$table_view[$i]['points_away'] = ($wins_away2) * $season_par->s_win_away + ($drows_away2) * $season_par->s_draw_away + ($loose_away2) * $season_par->s_lost_away + $awabonus + $bonus2;
			///in groups
			$table_view[$i]['win_gr'] = $wins_gr;
			$table_view[$i]['loose_gr'] = $loose_gr;
		}
			
			//---playeachother---///
		$query = "SELECT opt_value FROM #__bl_season_option WHERE s_id = ".$this->s_id." AND opt_name='equalpts_chk'";
		$this->db->setQuery($query);
		$equalpts_chk = $this->db->loadResult();
		
		if($equalpts_chk){
			$pts_arr = array();
			$pts_equal = array();
			foreach($table_view as $tv){
				if(!in_array($tv['points'],$pts_arr)){
					$pts_arr[] = $tv['points'];
				}else{
					if(!in_array($tv['points'],$pts_equal)){
						$pts_equal[] = $tv['points'];
					}
				}
			}
			$k = 0;
			$team_arr = array();
			foreach ($pts_equal as $pts){
				foreach($table_view as $tv){
					if($tv['points'] == $pts){
						$team_arr[$k][] = $tv['tid'];
						
					}
				}
				$k++;
			}
			
			foreach ($team_arr as $tm){
				
				foreach ($tm as $tm_one){
					
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played=1 AND ((t1.id = ".$tm_one." AND m.score1>m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_one." AND m.score1<m.score2 AND t1.id IN (".implode(',',$tm).")))";
		
					$this->db->setQuery($query);
					
					$matchs_avulsa_win = $this->db->loadResult();
					
					$tm_equal_win = array();
					
					foreach ($tm as $tm_other){
						$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played=1 AND ((t1.id = ".$tm_other." AND m.score1>m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_other." AND m.score1<m.score2 AND t1.id IN (".implode(',',$tm).")))";
			
						$this->db->setQuery($query);
						
						$matchs_avulsa_win_other = $this->db->loadResult();
						
						if($matchs_avulsa_win_other == $matchs_avulsa_win){
							$tm_equal_win[] = $tm_other;
						}
					}
					
					$query = "SELECT SUM(score1) as sh,SUM(score2) as sw FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND m.m_played=1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND ((t1.id = ".$tm_one." AND t2.id IN (".implode(',',$tm_equal_win).")))";
		
					$this->db->setQuery($query);
				
					$matchs_avulsa_score = $this->db->loadRow();
					//var_dump($matchs_avulsa_score);
					
					$query = "SELECT SUM(score2) as sh,SUM(score1) as sw FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND m.m_played=1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND ((t2.id=".$tm_one." AND t1.id IN (".implode(',',$tm_equal_win).")))";
		
					$this->db->setQuery($query);
				
					$matchs_avulsa_rec = $this->db->loadRow();
					
					 $matchs_avulsa_res = intval($matchs_avulsa_score[0]) + intval($matchs_avulsa_rec[0]);
					 $matchs_avulsa_res2 = intval($matchs_avulsa_score[1]) + intval($matchs_avulsa_rec[1]);
					

					for ($b=0;$b<count($table_view);$b++){
						if($table_view[$b]['tid']==$tm_one){
							$table_view[$b]['avulka_v'] = $matchs_avulsa_win;
							$table_view[$b]['avulka_cf'] = $matchs_avulsa_res;
							$table_view[$b]['avulka_cs'] = $matchs_avulsa_res2;
							$table_view[$b]['avulka_qc'] = $matchs_avulsa_res-$matchs_avulsa_res2;
						}
					}
				}	
			}
		}	
		//--/playeachother---///
			
			$sort_arr = array();
		 foreach($table_view AS $uniqid => $row){
	        foreach($row AS $key=>$value){
	            $sort_arr[$key][$uniqid] = $value;
	        }
	    }
	   if(count($groups_exists)){
			$query = "SELECT id FROM #__bl_groups WHERE id IN (".implode(',',$groups_exists).") ORDER BY ordering";
			$this->db->setQuery($query);
			$groups_exists = $this->db->loadResultArray();
			
			//sort($groups_exists, SORT_NUMERIC);
	   }
	  	if(!$season_par->s_groups){
	  		$groups_exists = array(0);
	  	}
		$this->_lists["groups"] = $groups_exists;
		if(count($sort_arr)){
			// sort fields 1-points, 2-wins percent, /*3-if equal between teams*/, 4-goal difference, 5-goal score
			$query = "SELECT * FROM #__bl_ranksort WHERE seasonid=".$this->s_id." ORDER BY ordering";
			$this->db->setQuery($query);
			$savedsort = $this->db->loadObjectList();
			$argsort = array();
			$argsort_way = array();
			if(count($savedsort)){
				foreach($savedsort as $sortop){
					switch($sortop->sort_field){
						case '1': $argsort[][0] = $sort_arr['points'];		break;
						case '2': $argsort[][0] = $sort_arr['winperc'];		break;
						case '3': $argsort[][0] = $sort_arr['points'];		break; /* not used */
						case '4': $argsort[][0] = $sort_arr['gd'];			break;
						case '5': $argsort[][0] = $sort_arr['goal_score'];	break;
						case '6': $argsort[][0] = $sort_arr['played'];		break;
					}
					
					$argsort_way[] = $sortop->sort_way;
				}
				
			}
			//var_dump($argsort);
			if($equalpts_chk){
				//var_dump($sort_arr['avulka_v']);
				array_multisort($sort_arr['g_id'], SORT_ASC,$sort_arr['points'], SORT_DESC,$sort_arr['avulka_v'], SORT_DESC,$sort_arr['avulka_qc'],SORT_DESC,$sort_arr['avulka_cf'],SORT_DESC,$sort_arr['gd'], SORT_DESC,$sort_arr['goal_score'], SORT_DESC, $table_view);
			
			}else{
			
				
				array_multisort($sort_arr['g_id'], SORT_ASC,(isset($argsort[0][0])?$argsort[0][0]:$sort_arr['points']), (isset($argsort_way[0])?($argsort_way[0]?SORT_ASC:SORT_DESC):SORT_DESC),(isset($argsort[1][0])?$argsort[1][0]:$sort_arr['gd']), (isset($argsort_way[1])?($argsort_way[1]?SORT_ASC:SORT_DESC):SORT_DESC),(isset($argsort[2][0])?$argsort[2][0]:$sort_arr['goal_score']), (isset($argsort_way[2])?($argsort_way[2]?SORT_ASC:SORT_DESC):SORT_DESC), $table_view);
			}
		}
		$this->_lists["v_table"] = $table_view;
		
		/////playoffs
		$pln = getJS_Config('player_name');
		if($this->t_single){
			$query = "SELECT m.*,m.id as mid,".($pln?"IF(t.nick<>'',t.nick,CONCAT(t.first_name,' ',t.last_name))":"CONCAT(t.first_name,' ',t.last_name)")." AS home,"
					." ".($pln?"IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name))":"CONCAT(t2.first_name,' ',t2.last_name)")." AS away, md.m_name, m.is_extra "
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t, #__bl_players as t2"
					." WHERE  m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 1 AND t.id = m.team1_id AND t2.id = m.team2_id AND m.m_played = 1"
					." ORDER BY md.id,m.id";
		}else{
			$query = "SELECT m.*,m.id as mid,t.t_name as home, t2.t_name as away, md.m_name, m.is_extra, t.t_emblem as emb1,t2.t_emblem as emb2  FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t, #__bl_teams as t2  WHERE  m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 1 AND t.id = m.team1_id AND t2.id = m.team2_id AND m.m_played = 1  ORDER BY md.id,m.id";
		}
		$this->db->setQuery($query);
		$this->_lists["playoffs"] = $this->db->loadObjectList();
		
		$query = "SELECT group_name FROM #__bl_groups WHERE id IN (".implode(',',$groups_exists).") ORDER BY ordering";
		$this->db->setQuery($query);
		$this->_lists["groups_name"] = $this->db->loadResultArray();
		
		$this->_lists["enbl_gr"] = $season_par->s_groups;
		$this->_lists["enbl_extra"] = $season_par->s_enbl_extra;
		
		$query = "SELECT ef.name FROM #__bl_extra_filds as ef  WHERE ef.published=1 AND ef.type = '1' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ORDER BY ef.ordering";
		$this->db->setQuery($query);
		
		$this->_lists["ext_fields_name"] = $this->db->loadResultArray();
		
		$this->_lists['bonus_not'] = (isset($bonus_not)?$bonus_not:'');
		//----colors----//
		$query = "SELECT * FROM #__bl_tblcolors WHERE s_id=".$this->s_id." ORDER BY place";
		$this->db->setQuery($query);
		$colors = $this->db->loadObjectList();
		$color_mass = array();
		for($j=0;$j<count($colors);$j++){
			
			$tmp_pl = $colors[$j]->place;
			$color_mass[intval($colors[$j]->place)] = $colors[$j]->color;
			$tmp_arr = explode(',',$tmp_pl);
			$tmp_arr2 = explode('-',$tmp_pl);
			if(count($tmp_arr)>1){
				foreach ($tmp_arr as $arr){
					if(intval($arr)){
						$color_mass[intval($arr)] = $colors[$j]->color;
					}
				}
			}
			if(count($tmp_arr2)>1){
				for($zzz=$tmp_arr2[0];$zzz<$tmp_arr2[1]+1;$zzz++){
					$color_mass[$zzz] = $colors[$j]->color;
				}
			}
		}
		$this->_lists["colors"] = $color_mass;
		
	}
	function KnockTable(){
		$Itemid = JRequest::getInt('Itemid');
		$matchs = $this->_lists['matches'];
		$k_format = 0;	
		if(count($matchs))	{
			$k_format = $matchs[0]->k_format;	
		}
		$pln = $this->getJS_Config('player_name');
		if(count($matchs) && $k_format){
			$match = $matchs;
			$orderby = $this->t_type?"md.id,m.k_stage,m.k_ordering":"m.m_date,m.m_time,md.id";
			if($this->t_single){
				$query = "SELECT MAX(LENGTH(CONCAT(t.first_name,' ',t.last_name))) FROM #__bl_season_players as st, #__bl_players as t WHERE t.id = st.player_id AND st.season_id = ".$this->s_id;
			}else{
				$query = "SELECT MAX(LENGTH(t.t_name)) FROM #__bl_season_teams as st, #__bl_teams as t WHERE t.id = st.team_id AND st.season_id = ".$this->s_id;
			}
			$this->db->setQuery($query);
			$mxl = $this->db->loadResult();
			
			if($this->getJS_Config('knock_style')){
				$kl = $this->KnockTableVert($mxl,$match,$k_format, $Itemid, $orderby);
			}else{
				$kl = $this->KnockTableHor($mxl,$match,$k_format,$Itemid, $orderby);
			}
			
			
		}else{	
				if($this->t_single){
					$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.first_name";
				}else{
					$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.t_name";
				}
				$this->db->setQuery($query);
			
				$team = $this->db->loadObjectList();
				
				for($y=0;$y<count($team);$y++){
					$kl .= "<div>".$team[$y]->t_name."</div>";
				}
		}
	
		
		$this->_lists['knock_layout'] = $kl;
	
	}
	
	function KnockTableVert($mxl,$match,$k_format, $Itemid, $orderby){
	
		if($mxl){
			$reslng = ($mxl)*7+20;
		}else{
			$reslng = 120;
		}
		if($reslng<120) $reslng=120;
		$cfg = new stdClass();
		$cfg->wdth = $reslng+50;
		$cfg->height = 20;
		$cfg->step = 70; 
		$cfg->top_next = 50;
		
			
			
			$query = "SELECT * FROM #__bl_matchday WHERE s_id=".$this->s_id." ORDER BY id";
			$this->db->setQuery($query);
			$mdays = $this->db->loadObjectList();
			$kl = '<br />';
			if(count($mdays)){
				foreach ($mdays as $mday){
				$k_format = $mday->k_format;
			
					if($this->t_single){
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,"
								." CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,"
								." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
								." IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,"
								." IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}else{
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id, IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}
					$this->db->setQuery($query);
		    
					$match = $this->db->loadObjectList();
					
					$zz = 2;
					$p=0;
					
					$wdth = $cfg->wdth;
					$height = $cfg->height;
					$step = $cfg->step; 
					$top_next = $cfg->top_next;
					
					
					
					if($this->t_single){
						$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.first_name";
					}else{
						$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.t_name";
					}
					$this->db->setQuery($query);
				
					$team = $this->db->loadObjectList();
				
					$is_team[] = JHTML::_('select.option',  0, ($this->t_single?JText::_('BLFA_SELPLAYER'):JText::_('BLFA_SELTEAM')), 'id', 't_name' ); 
				
					$teamis = array_merge($is_team,$team);
				
					$fid = $k_format;
			$kl .= '<div class="combine-box-vert" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:auto;border:1px solid #777;">';
			
			$bz = 0;
			$vz = 1;
			while(floor($fid/$zz) >= 1){
				
				for($i=0;$i<floor($fid/$zz);$i++){
					
					//$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
					if($this->t_single){
							$match[$i]->home = $this->selectPlayerName($match[$i]);
							$match[$i]->away = $this->selectPlayerName($match[$i],"fn2","ln2","nick2");
						}	
					if($p==0){
						if(isset($match[$i]->hm_id)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}	
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next - 14).'px; left:'.(20*($i+1) + ($i)*$wdth + $bz).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						$kl .= isset($match[$i]->home)?("<a href='".$link."' title='".$match[$i]->home."'>".$match[$i]->home."</a>"):"&nbsp;";
						$kl .= '</div><div>'.((isset($match[$i]->score1) && $match[$i]->m_played)?$match[$i]->score1:'').'</div>';
						$kl .= '</div>';
						if(isset($match[$i]->aw_id)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}	
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next - 14).'px; left:'.(20*($i+2) + ($i+1)*$wdth + $bz).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						$kl .= isset($match[$i]->away)?("<a href='".$link."' title='".$match[$i]->away."'>".$match[$i]->away."</a>"):"&nbsp;";
						$kl .= '</div><div>'.((isset($match[$i]->score2) && $match[$i]->m_played)?$match[$i]->score2:'').'</div><div class="knlink" style="width:'.$reslng.'px;"></div>';
						
						$kl .= '</div>';
						$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;Itemid='.$Itemid;
						$kl .= (isset($match[$i]->id)?'<div class="field-vert" style="position:absolute; top:'.($top_next + 40).'px; left:'.(20*($i+2) + ($i+1)*$wdth + $bz - 20).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
						$bz += $wdth +20;
					}else{
						
						$firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
						//$match[$firstchld_ind]->winner = ($pln && $match[$firstchld_ind]->winner_nick)?($match[$firstchld_ind]->winner_nick):($match[$firstchld_ind]->winner);
						//$match[$firstchld_ind+1]->winner = ($pln && $match[$firstchld_ind+1]->winner_nick)?$match[$firstchld_ind+1]->winner_nick:$match[$firstchld_ind+1]->winner;
						$cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
						if($this->t_single){
							$match[$firstchld_ind]->home = $this->selectPlayerName($match[$firstchld_ind]);
							$match[$firstchld_ind]->winner = $this->selectPlayerName($match[$firstchld_ind],"winner","","winner_nick");
							$match[$firstchld_ind]->away = $this->selectPlayerName($match[$firstchld_ind],"fn2","ln2","nick2");
							$match[$firstchld_ind+1]->home = $this->selectPlayerName($match[$firstchld_ind+1]);
							$match[$firstchld_ind+1]->away = $this->selectPlayerName($match[$firstchld_ind+1],"fn2","ln2","nick2");
							$match[$firstchld_ind+1]->winner = $this->selectPlayerName($match[$firstchld_ind+1],"winner","","winner_nick");
						}
						
						if(($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2) && isset($match[$firstchld_ind]->winner)){
							
							if($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
								
							}elseif($match[$firstchld_ind]->aet1 < $match[$firstchld_ind]->aet2){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
							}else{
								if($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
							
								}elseif($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team2_id){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
								
								}else{
									$match[$firstchld_ind]->m_played = 0;
								}
							}
						}
						if(($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2) && isset($match[$firstchld_ind+1]->winner)){
							if($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
							}elseif($match[$firstchld_ind+1]->aet1 < $match[$firstchld_ind+1]->aet2){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
							}else{
								if($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
								}elseif( $match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team2_id){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
								}else{
									$match[$firstchld_ind+1]->m_played = 0;
								}
							}
						}
						
						if(!$match[$firstchld_ind]->home && $match[$firstchld_ind]->away){
							$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
							$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
							$match[$firstchld_ind]->m_played = 1;
						}
						if(!$match[$firstchld_ind]->away && $match[$firstchld_ind]->home){
							$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
							$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
							$match[$firstchld_ind]->m_played = 1;
						}
					
						if(!$match[$firstchld_ind+1]->home && $match[$firstchld_ind+1]->away){
							$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
							$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
							$match[$firstchld_ind+1]->m_played = 1;
						}
						if(!$match[$firstchld_ind+1]->away && $match[$firstchld_ind+1]->home){
							$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
							$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
							$match[$firstchld_ind+1]->m_played = 1;
						}
						
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next).'px; left:'.(((2*$wdth+20)*(2*$vz -1)*pow(2,$p-1) + (pow(2,$p-1)-1)*20)/2 -$wdth/2 + 20*$vz*$p - 20).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						
						if(isset($match[$firstchld_ind]->winnerid)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}	
						$kl .= (isset($match[$firstchld_ind]->winner) && $match[$firstchld_ind]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind]->winner."'>".$match[$firstchld_ind]->winner."</a>"):"";
						$kl .= '</div><div>'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1:"").'</div>';
						$kl .= '</div>';
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next).'px; left:'.(((2*$wdth+20)*(2*$vz + 1)*pow(2,$p-1) + (pow(2,$p-1)-1)*20)/2 -$wdth/2 + 20*$vz*$p + 20).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						if(isset($match[$firstchld_ind+1]->winnerid)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}
						$kl .= (isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):"";
						$kl .= '</div><div>'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2:"").'</div>';
						
						$kl .= '</div>';
						$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
						$kl .= (isset($match[$cur_ind]->id)?'<div style="position:absolute; top:'.($top_next+20).'px; left:'.((((2*$wdth+20)*(2*$vz)*pow(2,$p-1) + (pow(2,$p-1)-1)*20) - $wdth + 40*$vz*$p)/2 + $wdth/2).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");

					}
					$vz+=2;
				}
				
				$top_next += $height + $step;
				//$height = $height + $step;
				//$step = $height;
				$zz *= 2;
				$p++;
				
				$vz = 1;
				
			}
			$winmd_id = $fid - 2;
			$wiinn = '';
			if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played) 
			{ 
				if($this->t_single){
					$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
				}else{	
					$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
				}
				$wiinn = "<div class='knock_el' style='width:".($reslng+50)."px;margin-left:5px;margin-top:-17px;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id]->winner."'>".$match[$winmd_id]->winner."</a></div></div></div></div></div></div>";
			}
			
			if($fid){
				$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.((((2*$wdth+20)*(2 + 1)*pow(2,$p-2) + (pow(2,$p-2)-1)*20)/2 -$wdth/2 + 20*($p-1) + 20)*2/3).'px;">'.$wiinn.'</div>';
			}	
				$kl .=  '</div>';
				}
			}
		return $kl;	
		
	}
	function KnockTableHor($mxl,$match,$k_format, $Itemid, $orderby){
	
		if($mxl){
				$reslng = ($mxl)*7+20;
			}else{
				$reslng = 120;
			}
			if($reslng<120) $reslng=120;
			$cfg = new stdClass();
			$cfg->wdth = $reslng+70;
			$cfg->height = 60;
			$cfg->step = 70; 
			$cfg->top_next = 50;
		
			
			
			$query = "SELECT * FROM #__bl_matchday WHERE s_id=".$this->s_id." ORDER BY id";
			$this->db->setQuery($query);
			$mdays = $this->db->loadObjectList();
			$kl = '<br />';
			if(count($mdays)){
				foreach ($mdays as $mday){
				$k_format = $mday->k_format;
			
					if($this->t_single){
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,"
								." CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,"
								." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
								." IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,"
								." IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}else{
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id, IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}
					$this->db->setQuery($query);
		    
					$match = $this->db->loadObjectList();
					
					$zz = 2;
					$p=0;
					
					$wdth = $cfg->wdth;
					$height = $cfg->height;
					$step = $cfg->step; 
					$top_next = $cfg->top_next;
					
					
					
					if($this->t_single){
						$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.first_name";
					}else{
						$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.t_name";
					}
					$this->db->setQuery($query);
				
					$team = $this->db->loadObjectList();
				
					$is_team[] = JHTML::_('select.option',  0, ($this->t_single?JText::_('BLFA_SELPLAYER'):JText::_('BLFA_SELTEAM')), 'id', 't_name' ); 
				
					$teamis = array_merge($is_team,$team);
				
					$fid = $k_format;
			//	echo count($match);die();
					$kl .= $mday->m_name;
					$kl .= '<div class="combine-box-new" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #ccc;">';
					
				
				while(floor($fid/$zz) >= 1){
					
					for($i=0;$i<floor($fid/$zz);$i++){
						
						if($this->t_single){
							$match[$i]->home = $this->selectPlayerName($match[$i]);
							$match[$i]->away = $this->selectPlayerName($match[$i],"fn2","ln2","nick2");
						}
						
						$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
							
						if($p==0){
							if(isset($match[$i]->hm_id)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}	
							$kl .= '<div class="field-comb" style="position:absolute; top:'.($i*($height+$step) + $top_next - 14).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score1) && $match[$i]->m_played)?$match[$i]->score1:'').'</span>';
							$kl .= isset($match[$i]->home)?("<a href='".$link."' title='".$match[$i]->home."'>".$match[$i]->home."</a>"):"&nbsp;";
							$kl .= '</div>';
							if(isset($match[$i]->aw_id)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}	
							$kl .= '<div class="field-comb" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 13).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score2) && $match[$i]->m_played)?$match[$i]->score2:'').'</span>';
							$kl .= isset($match[$i]->away)?("<a href='".$link."' title='".$match[$i]->away."'>".$match[$i]->away."</a>"):"&nbsp;";
							$kl .= '</div>';
							$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;Itemid='.$Itemid;
							$kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-20 + ($p+1)*$wdth).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
						}else{
							
							$firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
							//$match[$firstchld_ind]->winner = ($pln && $match[$firstchld_ind]->winner_nick)?($match[$firstchld_ind]->winner_nick):($match[$firstchld_ind]->winner);
							//$match[$firstchld_ind+1]->winner = ($pln && $match[$firstchld_ind+1]->winner_nick)?$match[$firstchld_ind+1]->winner_nick:$match[$firstchld_ind+1]->winner;
							$cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
							
							if($this->t_single){
								if(isset($match[$firstchld_ind])){
									$match[$firstchld_ind]->home = $this->selectPlayerName($match[$firstchld_ind]);
									$match[$firstchld_ind]->winner = $this->selectPlayerName($match[$firstchld_ind],"winner","","winner_nick");
									$match[$firstchld_ind]->away = $this->selectPlayerName($match[$firstchld_ind],"fn2","ln2","nick2");
								}
								if(isset($match[$firstchld_ind+1])){
									$match[$firstchld_ind+1]->home = $this->selectPlayerName($match[$firstchld_ind+1]);
									$match[$firstchld_ind+1]->away = $this->selectPlayerName($match[$firstchld_ind+1],"fn2","ln2","nick2");
									$match[$firstchld_ind+1]->winner = $this->selectPlayerName($match[$firstchld_ind+1],"winner","","winner_nick");
								}
							}
							if(isset($match[$firstchld_ind]) &&($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2) && isset($match[$firstchld_ind]->winner)){
								
								if($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
									
								}elseif($match[$firstchld_ind]->aet1 < $match[$firstchld_ind]->aet2){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
								}else{
									if($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id){
										$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
										$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
								
									}elseif($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team2_id){
										$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
										$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
									
									}else{
										$match[$firstchld_ind]->m_played = 0;
									}
								}
							}
							if(isset($match[$firstchld_ind +1]) && ($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2) && isset($match[$firstchld_ind+1]->winner)){
								if($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
								}elseif($match[$firstchld_ind+1]->aet1 < $match[$firstchld_ind+1]->aet2){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
								}else{
									if($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id){
										$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
										$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
									}elseif( $match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team2_id){
										$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
										$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
									}else{
										$match[$firstchld_ind+1]->m_played = 0;
									}
								}
							}
							if(isset($match[$firstchld_ind])){
								if(!$match[$firstchld_ind]->home && $match[$firstchld_ind]->away){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
									$match[$firstchld_ind]->m_played = 1;
								}
								if(!$match[$firstchld_ind]->away && $match[$firstchld_ind]->home){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
									$match[$firstchld_ind]->m_played = 1;
								}
							
								if(!$match[$firstchld_ind+1]->home && $match[$firstchld_ind+1]->away){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
									$match[$firstchld_ind+1]->m_played = 1;
								}
								if(!$match[$firstchld_ind+1]->away && $match[$firstchld_ind+1]->home){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
									$match[$firstchld_ind+1]->m_played = 1;
								}
							}
							
							$kl .= '<div class="field-comb" style="position:absolute; top:'.($i*($height+$step) + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1:"").'</span>';
							if(isset($match[$firstchld_ind]->winnerid)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}	
							$kl .= (isset($match[$firstchld_ind]->winner) && $match[$firstchld_ind]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind]->winner."'>".$match[$firstchld_ind]->winner."</a>"):"";
							$kl .= '</div>';
							$kl .= '<div class="field-comb" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2:"").'</span>';
							if(isset($match[$firstchld_ind+1]->winnerid)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}
							$kl .= (isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):"";
							$kl .= '</div>';
							$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
							$kl .= (isset($match[$cur_ind]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-20 + ($p+1)*$wdth).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
						}
					
					}
					$top_next += $height/2;
					$height = $height + $step;
					$step = $height;
					$zz *= 2;
					$p++;
					
				}
				$winmd_id = $fid - 2;
				$wiinn = '';
				if($this->t_single){
					if(isset($match[$winmd_id])){

						$match[$winmd_id]->winner = $this->selectPlayerName($match[$winmd_id],"winner","","winner_nick");
					}
					
				}
				if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played) 
				{ 
					if($this->t_single){
						$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
					}else{	
						$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
					}
					$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id]->winner."'>".$match[$winmd_id]->winner."</a></div></div></div></div></div></div>";
				}
				
				if($fid){
					$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>';
				}	
				$kl .=  '</div>';
				}
			}
			return $kl;
		
	}
	
}	