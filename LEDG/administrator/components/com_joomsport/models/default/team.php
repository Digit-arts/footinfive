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

class teamJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $s_id = null;
	var $team_id = null;
	var $limit	  = null;
	var $limitstart = null;
	var $_total		= null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->team_id = JRequest::getVar( 'tid', 0, '', 'int' );
		$this->s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		$this->curcal = JRequest::getVar( 'curcal', 0, '', 'int' );
		$this->limit		= $this->mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $this->mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.qw.limitstart', 'limitstart', 0, 'int' );
		
		if(!$this->team_id){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
	}

	function getData()
	{
		$user	=& JFactory::getUser();
		$Itemid = JRequest::getInt('Itemid');
		$query = "SELECT * FROM #__bl_teams WHERE id = ".$this->team_id;
		$this->db->setQuery($query);
		$team = $this->db->loadObject();
		$this->_lists["team"] = $team;
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 2 AND cat_id = ".$this->team_id;
		$this->db->setQuery($query);
		$this->_lists["photos"] = $this->db->loadObjectList();
		
		$this->_lists["def_img"] = '';
		if($team->def_img){
			$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$team->def_img;
			$this->db->setQuery($query);
			$this->_lists["def_img"] = $this->db->loadResult();
		}else if(isset($this->_lists["photos"][0])){
			$this->_lists["def_img"] = $this->_lists["photos"][0]->filename;
		}
		if($this->s_id){
			$query = "SELECT md.m_name,m.id as mid,m.m_date,m.m_time, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra, m.m_played, m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (m.team1_id = ".$this->team_id." OR m.team2_id = ".$this->team_id.") AND m.team1_id = t1.id AND m.team2_id = t2.id ORDER BY m.m_date";
		}else{
			$query = "SELECT DISTINCT(m.id),md.m_name,m.id as mid,m.m_date,m.m_time, t1.t_name as home, t2.t_name as away, m.score1,m.score2,m.is_extra, m.m_played, m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2, #__bl_seasons as s, #__bl_tournament as tr"
					." WHERE ((tr.id=s.t_id AND tr.t_single = '0' AND md.s_id=s.s_id) OR (md.s_id = -1)) AND m.m_id = md.id AND m.published = 1 AND (m.team1_id = ".$this->team_id." OR m.team2_id = ".$this->team_id.") AND m.team1_id = t1.id AND m.team2_id = t2.id"
					." ORDER BY md.s_id,m.m_date";
		}
		$this->db->setQuery($query, $this->limitstart, $this->limit);
		$this->_lists["matshes"] = $this->db->loadObjectList();
		if(count($this->_lists["matshes"])){
			for($z=0;$z<count($this->_lists["matshes"]);$z++){
                $this->_lists["matshes"][$z]->betevents = $this->getMatchBetEvents($this->_lists["matshes"][$z]->mid);
            }   
        }
		
		$pllist_order = $this->getJS_Config('pllist_order');
		$pl_id = 0;
		$pl_type = 0;
		if($pllist_order){
			$pl_type_m = explode('_',$pllist_order);
			if(count($pl_type_m)){
				$pl_id = $pl_type_m[0];
				$pl_type = $pl_type_m[1];
			}
		}
		
		
		$query = "SELECT * FROM #__bl_players as p, #__bl_players_team as t WHERE p.id=t.player_id AND t.team_id = ".$this->team_id." ".($this->s_id?" AND t.season_id=".$this->s_id:"")." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$players = $this->db->loadObjectList();
		
		
		
		$query = "SELECT DISTINCT(ev.id),ev.* FROM #__bl_events as ev, #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE (ev.id = me.e_id OR (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id)) AND me.match_id = m.id AND m.m_id=md.id ".($this->s_id?"AND md.s_id=".$this->s_id:"")." AND (ev.player_event = 1 OR ev.player_event = 2) ORDER BY ev.ordering";
		$this->db->setQuery($query);
		$events = $this->db->loadObjectList();
		
		
		
		$unbl_matchplayed = $this->getJS_Config('played_matches');
		
		$this->_lists['cntev'] = count($events)+1;
		if($unbl_matchplayed){
			$this->_lists['cntev']++;
		}
		//- -- CREATE OUTPUT TABLE
		$player_table = array();
		
		$query = "SELECT ef.name FROM #__bl_extra_filds as ef  WHERE ef.published=1 AND ef.type = '0' AND ef.fdisplay = '1' AND ef.e_table_view = '1' ".($user->get('guest')?" AND ef.faccess='0'":"")." ORDER BY ef.ordering";
		$this->db->setQuery($query);
		
		$ext_fields_name = $this->db->loadResultArray();
		$sort_arg = 0;
		for($i=0;$i<count($players);$i++){
			if($i == 0){
				$player_table[0][] = JText::_('BL_TBL_PLAYER');
				if($unbl_matchplayed){
					$player_table[0][] = JText::_('BLFA_MATCHPLAYED');
				}
			}
			
			$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$players[$i]->id;
			$this->db->setQuery($query);
			$photos2 = $this->db->loadObjectList();
			
			$def_img2 = '';
			if($players[$i]->def_img){
				$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$players[$i]->def_img;
				$this->db->setQuery($query);
				$def_img2 = $this->db->loadResult();
			}else if(isset($photos2[0])){
				$def_img2 = $photos2[0]->filename;
			}
			$img = '';
			if($def_img2 && is_file('media/bearleague/'.$def_img2)){
				//$img = "<img src='media/bearleague/".$def_img2."' height='50' />";
			}
			
			$player_table[$i+1][] = "<a href='".JRoute::_('index.php?option=com_joomsport&amp;task=player&amp;id='.$players[$i]->id.'&amp;sid='.$this->s_id.'&amp;Itemid='.$Itemid)."'>".$img.$this->selectPlayerName($players[$i])."</a>";
			if($unbl_matchplayed){
				$query = "SELECT COUNT(*) FROM #__bl_squard as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id ".($this->s_id?" AND md.s_id=".$this->s_id:"")." AND m.id=s.match_id AND m.m_played='1' AND s.mainsquard='1' AND s.player_id=".$players[$i]->id;
				$this->db->setQuery($query);
				$gamepl = intval($this->db->loadResult());
				
				$query = "SELECT COUNT(DISTINCT(m.id)) FROM #__bl_subsin as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id ".($this->s_id?" AND md.s_id=".$this->s_id:"")." AND m.id=s.match_id AND m.m_played='1' AND s.player_in=".$players[$i]->id;
				$this->db->setQuery($query);
				$gamepl += intval($this->db->loadResult());
				$player_table[$i+1][] = $gamepl;
			}
			
			
			for($j=0;$j<count($events);$j++){
				if($i==0){
					$ev_tbl = $events[$j]->e_name;
					if($events[$j]->e_img && is_file('media/bearleague/events/'.$events[$j]->e_img)){
						$ev_tbl = '<img src="media/bearleague/events/'.$events[$j]->e_img.'" title="'.$events[$j]->e_name.'" height="20"  alt="'.$events[$j]->e_name.'" />';
					}
					$player_table[0][] = $ev_tbl;
				}
				if($events[$j]->result_type == '1'){
					$this->db->setQuery("SELECT AVG(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE me.e_id = ".$events[$j]->id." AND me.t_id=".$this->team_id." AND me.player_id = ".$players[$i]->id." AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id ".($this->s_id?"AND md.s_id=".$this->s_id:""));
				}else{
					$this->db->setQuery("SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE me.e_id = ".$events[$j]->id." AND me.t_id=".$this->team_id." AND me.player_id = ".$players[$i]->id." AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id ".($this->s_id?"AND md.s_id=".$this->s_id:""));
				}
				if($pl_id == $events[$j]->id && $pl_type == '2'){
					$sort_arg = $j+1;
				}
				if($events[$j]->player_event == '2'){
					$this->db->setQuery("SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE (me.e_id = ".$events[$j]->sumev1." OR me.e_id = ".$events[$j]->sumev2.") AND me.t_id=".$this->team_id." AND me.player_id = ".$players[$i]->id." AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id ".($this->s_id?"AND md.s_id=".$this->s_id:""));
				}
				
				$curcount = $this->db->loadResult();
				$player_table[$i+1][]  = floatval($curcount)?floatval($curcount):0;
			}
			if($i == 0){
				if(count($ext_fields_name)){
					$player_table[0] = array_merge($player_table[0],$ext_fields_name);
				}
			}
			$query = "SELECT * FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$players[$i]->id." WHERE ef.published=1 AND ef.type = '0' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ".($user->get('guest')?" AND ef.faccess='0'":"")." ORDER BY ef.ordering";
			$this->db->setQuery($query);
	
			$ext_pl_z = $this->db->loadObjectList();
			
			$mj=0;
				if(isset($ext_pl_z)){
					foreach ($ext_pl_z as $extr){
					
						if($pl_id == $extr->id && $pl_type == '1'){
							$sort_arg = $mj;
						}
						if($extr->field_type == '3'){
							$query = "SELECT sel_value FROM #__bl_extra_select WHERE id='".$extr->fvalue."'";
							$this->db->setQuery($query);
							$selvals = $this->db->loadResult();
							if(isset($selvals) && $selvals){
								$ext_pl[$mj] = $selvals;
							}else{
								$ext_pl[$mj] = '&nbsp;';
							}
						}else
						if($extr->field_type == '1'){
							$ext_pl[$mj]	= $extr->fvalue?JText::_("Yes"):JText::_("No");
						}else if($extr->field_type == '2'){
							$ext_pl[$mj] = $extr->fvalue_text?$extr->fvalue_text:'&nbsp;';
						
						}else{
							$ext_pl[$mj] = $extr->fvalue?$extr->fvalue:'&nbsp;';
						}
						
						$mj++;
					}
				}
			
			if(isset($ext_pl) && count($ext_pl)){
				if($pl_type == '1'){
					$sort_arg = count($player_table[$i+1]) + $sort_arg;
				}
				$player_table[$i+1] = array_merge($player_table[$i+1],$ext_pl);
			}
		}
		//echo $sort_arg.'--';
		//var_dump($player_table);
		
		
		if(count($player_table) && $pllist_order){
			$new_pl[] = $player_table[0];
			unset($player_table[0]);
			
			$arrss=array();
			$arrss2=array();

			foreach($player_table as $pt){
				$arrss[] = isset($pt[$sort_arg])?$pt[$sort_arg]:"";
				$arrss2[] = strip_tags($pt[0]);
			}
			
			$sort_fu = SORT_DESC;
			array_multisort($arrss,$sort_fu,$arrss2,SORT_ASC,$player_table);
			$player_table = array_merge($new_pl,$player_table);
		}
		$this->_lists["players"] = $player_table;
		if($this->s_id && $this->s_id != -1){
			
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}else{
			$this->_lists["enbl_extra"] = 0;
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		
		//tmp
		$is_tourn = array();
		$is_tourn[] = JHTML::_('select.option',0,  JText::_('BLFA_ALL'), 'id', 's_name' ); 
		
		
		$query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		
		
		$javascript = " onchange='document.adminForm.submit();'";
		$jqre = '<select name="sid" id="sid" class="inputbox" size="1" '.$javascript.'>';
		$jqre .= '<option value="0">'.JText::_('BLFA_ALL').'</option>';
		
		$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id=md.id AND md.s_id= -1 AND m.m_single='0' AND (m.team1_id=".$this->team_id." OR m.team2_id=".$this->team_id.")";
		$this->db->setQuery($query);
		$frm = $this->db->loadResult();
		if($frm){
			$jqre .= '<option value="-1" '.((-1 == $this->s_id)?"selected":"").'>'.JText::_('BLFA_FRIENDLY_MATCHES').'</option>';
		}	
		for($i=0;$i<count($tourn);$i++){
			$is_tourn2 = array();
			$query = "SELECT s.s_id as id,s.s_name as s_name FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id, #__bl_season_teams as st WHERE s.published='1' AND st.team_id=".$this->team_id." AND s.s_id=st.season_id AND t.id=".$tourn[$i]->id."  ORDER BY s.s_name";
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
		$this->_lists['locven'] = $this->getJS_Config("cal_venue");
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1,$this->team_id);
		///-----EXTRAFIELDS---//
		
		$this->_lists['ext_fields'] = $this->getAddFields($this->team_id,'1',"team",$this->s_id);
		
		
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
    
	function getPagination()
	{
		if (empty($this->_pagination))
		{

			$this->_pagination = new MarNewsPagination( $this->getTotal(), $this->limitstart, $this->limit );
			$this->_pagination->setsid($this->s_id);
		
		}

		return $this->_pagination;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
		{
			if($this->s_id){
				$query = "SELECT COUNT(m.id) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (m.team1_id = ".$this->team_id." OR m.team2_id = ".$this->team_id.") AND m.team1_id = t1.id AND m.team2_id = t2.id";
			}else{
				$query = "SELECT COUNT(m.id) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2, #__bl_seasons as s, #__bl_tournament as tr WHERE tr.id=s.t_id AND tr.t_single = '0' AND md.s_id=s.s_id AND m.m_id = md.id AND m.published = 1 AND (m.team1_id = ".$this->team_id." OR m.team2_id = ".$this->team_id.") AND m.team1_id = t1.id AND m.team2_id = t2.id";
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


jimport('joomla.html.pagination');
class MarNewsPagination extends JPagination{
var $s_id = null;
	function __construct($total, $limitstart, $limit){
		parent::__construct($total, $limitstart, $limit);
	}
	function setsid($sid){
		$this->s_id = $sid;
	}
	function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		$data->all	= new JPaginationObject(JText::_('View All'));
		if (!$this->_viewall) {
			$data->all->base	= '0';
			$data->all->link	= JRoute::_("&limitstart=");
		}

		// Set the start and previous data objects
		$data->start	= new JPaginationObject(JText::_('Start'));
		$data->previous	= new JPaginationObject(JText::_('Prev'));

		if ($this->get('pages.current') > 1)
		{
			$page = ($this->get('pages.current') -2) * $this->limit;

			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->link	= JRoute::_("&limitstart=");
			$data->previous->base	= $page;
			$data->previous->link	= JRoute::_("&curcal=1&sid=".$this->s_id."&limitstart=".$page);
		}

		// Set the next and end data objects
		$data->next	= new JPaginationObject(JText::_('Next'));
		$data->end	= new JPaginationObject(JText::_('End'));

		if ($this->get('pages.current') < $this->get('pages.total'))
		{
			$next = $this->get('pages.current') * $this->limit;
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->next->base	= $next;
			$data->next->link	= JRoute::_("&curcal=1&sid=".$this->s_id."&limitstart=".$next);
			$data->end->base	= $end;
			$data->end->link	= JRoute::_("&curcal=1&sid=".$this->s_id."&limitstart=".$end);
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

			$data->pages[$i] = new JPaginationObject($i);
			if ($i != $this->get('pages.current') || $this->_viewall)
			{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= JRoute::_("&curcal=1&sid=".$this->s_id."&limitstart=".$offset);
			}
		}

		return $data;
	}
	function pagination_item_active(&$item) {
		global $Itemid;die();
		$lnk = explode('?',$item->link);
			$item->link .= "&curcal=1&sid=".$this->s_id;
		return "<a href='javascript:return false;' onClick=\"javascript:blpageuri_news('".$item->link."');return false;\" title=\"".$item->text."\">".$item->text."</a>";
	}
	function _item_active(&$item)
	{
		//global $mainframe;//die();
        $mainframe = &JFactory::getApplication();
		if ($mainframe->isAdmin())
		{
			if($item->base>0)
				return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=".$item->base."; submitform();return false;\">".$item->text."</a>";
			else
				return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=0; submitform();return false;\">".$item->text."</a>";
		} else {
			$item->link .= "&curcal=1&sid=".$this->s_id;
			return "<a title=\"".$item->text."\" href=\"".$item->link."\" class=\"pagenav\">".$item->text."</a>";
		}
	}
}
