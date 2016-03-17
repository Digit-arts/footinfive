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

class edit_matchdayJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $mid = null;
	var $season_id = null;
	var $t_single = null;
	var $t_type = null;
	var $id = null;
	function __construct()
	{
		parent::__construct();
		$this->mid = JRequest::getVar( 'mid', 0, '', 'int' );	
	
	}

	function getData()
	{
		$this->_params = $this->JS_PageTitle("");
		$msg = JRequest::getVar( 'msg', '', 'get', 'string', JREQUEST_ALLOWRAW );
		$is_id = 0;
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name"
				." FROM #__bl_tournament as t, #__bl_seasons as s"
				." WHERE s.t_id = t.id"
				." ORDER BY t.name, s.s_name";
		$this->db->setQuery($query);
		$tourns = $this->db->loadObjectList(); 
		JArrayHelper::toInteger($cid, array(0));
		if($cid[0])
		{
			$is_id = $cid[0];
		}
		$this->season_id	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.sid', 'sid', $tourns[0]->id, 'int' );
		
		$row 	= new JTableMday($this->db);
		$row->load($is_id?$is_id:$this->mid);
		
		if($is_id){
			
			$query = "SELECT COUNT(*) FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE  s.s_id = ".($row->s_id?$row->s_id:$this->season_id);
			$this->db->setQuery($query);
			
			if(!$this->db->loadResult()){
				
				JError::raiseError( 403, JText::_('Access Forbidden') );
				return; 
			}
			//$tpl="edit";
		}
		$tourn = $this->getTournOpt($row->s_id?$row->s_id:$this->season_id);
		$this->t_single = $tourn->t_single;
		$this->t_type = $tourn->t_type;
		$this->_lists['s_enbl_extra'] = $tourn->s_enbl_extra;
		$this->_lists['tourn'] = $tourn->name;
		
		$this->_data = $row;
		$this->getMatchesFMD($row->id);
		$this->_lists["is_team"] = $this->getteamsSeas($row->s_id?$row->s_id:$this->season_id);
		
		$this->_lists['is_playoff'] 		= JHTML::_('select.booleanlist',  'is_playoff', 'class="inputbox"', $row->is_playoff );
		if($this->t_type){
			$this->getKformat($row->k_format);
			$this->getKnockView($row,$this->season_id);
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
	}
	function getMatchesFMD($id){
		$orderby = $this->t_type?"m.k_stage,m.k_ordering":"m.id";
		if($this->t_single){
			$query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid"
					." FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id"
					." WHERE m.m_id = ".$id
					."  ORDER BY ".$orderby;
		}else{
			$query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid"
					." FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id"
					." WHERE m.m_id = ".$id
					." ORDER BY ".$orderby;
		}
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
	function getKnockView($row,$season_id){
		$kl = '';
		$match = $this->_lists["match"];
		if(count($match) && $this->t_type && $row->k_format){
			
			$cfg = new stdClass();
			$cfg->wdth = 150;
			$cfg->height = 50;
			$cfg->step = 70; 
			$cfg->top_next = 50;
		
			$wdth = $cfg->wdth;
			$height = $cfg->height;
			$step = $cfg->step; 
			$top_next = $cfg->top_next;

			$zz = 2;
			
			$p=0;
			
			$teamis = $this->_lists["is_team"];
		
			$fid = $row->k_format;
		
			
			$kl .= '<div class="combine-box-new" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #ccc;">';
			
			while(floor($fid/$zz) >= 1){
			
				for($i=0;$i<floor($fid/$zz);$i++){
					$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
					
					if($p==0){
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20).'px; left:'.(20 + ($p)*$wdth).'px;">';
						$kl .= JHTML::_('select.genericlist',   $teamis, 'teams_kn[]', 'class="inputbox" size="1"', 'id', 't_name', isset($match[$i]->team1_id)?$match[$i]->team1_id:0 );
						$kl .= '</div>';
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5).'px; left:'.(20 + ($p)*$wdth).'px;">';
						$kl .= JHTML::_('select.genericlist',   $teamis, 'teams_kn_aw[]', 'class="inputbox" size="1"', 'id', 't_name', isset($match[$i]->team1_id)?$match[$i]->team2_id:0 );
						$kl .= '</div>';
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(20 + ($p)*$wdth).'px;">';
						$kl .= '<input type="text" name="res_kn_1[]" value="'.((($match[$i]->score2 && $match[$i]->score1) || $match[$i]->m_played)?$match[$i]->score1:'').'" size="5" maxlength="5" />';
						$kl .= '</div>';
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(20 + ($p)*$wdth).'px;">';
						$kl .= '<input type="text" name="res_kn_1_aw[]" value="'.((($match[$i]->score2 && $match[$i]->score1) || $match[$i]->m_played)?$match[$i]->score2:'').'" size="5" maxlength="5" />';
						$kl .= '</div>';
						$match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;controller=admin&amp;sid='.$season_id.'&amp;cid[]='.(isset($match[$i]->id)?($match[$i]->id):'');
						$kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-35 + ($p+1)*$wdth).'px;"><input type="hidden" name="match_id[]" value="'.$match[$i]->id.'"><a href="'.$match_link.'"><div class="send-button"><span><img src="components/com_joomsport/img/spacer.gif" width="16" height="16" alt=""></span></div></a></div>':"");
					}else{
						$firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
						$cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20).'px; left:'.(40 + ($p)*$wdth).'px;">';
						
						
						if( isset($match[$firstchld_ind]) && ($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2) && isset($match[$firstchld_ind]->winner)){
						
							if($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
							}elseif($match[$firstchld_ind]->aet1 < $match[$firstchld_ind]->aet2){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
							}else{
								if($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
								}elseif($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team2_id){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
								}else{
									$match[$firstchld_ind]->m_played = 0;
								}
							}
						}
						if( isset($match[$firstchld_ind+1]) && ($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2) && isset($match[$firstchld_ind+1]->winner)){
							if($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
							}elseif($match[$firstchld_ind+1]->aet1 < $match[$firstchld_ind+1]->aet2){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
							}else{
								if($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
								}elseif($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team2_id){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
								}else{
									$match[$firstchld_ind+1]->m_played = 0;
								}
							}
						}
						
						if(isset($match[$firstchld_ind]) && !$match[$firstchld_ind]->home_team && $match[$firstchld_ind]->away_team){
							$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
							$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
							$match[$firstchld_ind]->m_played = 1;
						}
						if(isset($match[$firstchld_ind]) && !$match[$firstchld_ind]->away_team && $match[$firstchld_ind]->home_team){
							$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
							$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
							$match[$firstchld_ind]->m_played = 1;
						}
						
						if(isset($match[$firstchld_ind+1]) && !$match[$firstchld_ind+1]->home_team && $match[$firstchld_ind+1]->away_team){
							$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
							$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
							$match[$firstchld_ind+1]->m_played = 1;
						}
						if(isset($match[$firstchld_ind+1]) && !$match[$firstchld_ind+1]->away_team && $match[$firstchld_ind+1]->home_team){
							$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;
							
							$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
							$match[$firstchld_ind+1]->m_played = 1;
						}
						
						
						$kl .= (isset($match[$firstchld_ind]->winner)  && $match[$firstchld_ind]->m_played)?$match[$firstchld_ind]->winner:"";
						$kl .= (isset($match[$firstchld_ind]->winnerid))?('<input type="hidden" name="teams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->winnerid.'" />'):('<input type="hidden" name="teams_kn_'.($p+1).'[]" value="0" />');
						$kl .= '</div>';
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5).'px; left:'.(40 + ($p)*$wdth).'px;">';
						$kl .= (isset($match[$firstchld_ind + 1]->winner)  && $match[$firstchld_ind + 1]->m_played)?$match[$firstchld_ind + 1]->winner:"";
						$kl .= (isset($match[$firstchld_ind + 1]->winnerid) )?('<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->winnerid.'" />'):('<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="0" />');
						$kl .= '</div>';
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(60 + ($p)*$wdth).'px;">';
						$kl .= '<input type="text" name="res_kn_'.($p+1).'[]" value="'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1:"").'" size="10" maxlength="5" />';
						$kl .= '</div>';
						$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(60 + ($p)*$wdth).'px;">';
						$kl .= '<input type="text" name="res_kn_'.($p+1).'_aw[]" value="'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2:"").'" size="10" maxlength="5" />';
						$kl .= '</div>';
						$match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;controller=admin&amp;sid='.$season_id.'&amp;cid[]='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
						if(isset($match[$cur_ind]->id) && isset($match[$firstchld_ind]->winnerid) && isset($match[$firstchld_ind + 1]->winnerid)){
							$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-35 + ($p+1)*$wdth).'px;"><input type="hidden" name="matches_'.($p+1).'[]" value="'.(isset($match[$cur_ind]->id)?$match[$cur_ind]->id:0).'"><a href="'.$match_link.'"><div class="send-button"><span><img src="components/com_joomsport/img/spacer.gif" width="16" height="16" alt=""></span></div></a></div>';
						}
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
			if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played) 
			{ 
				
				$wiinn = "<div style='margin-left:15px; margin-top:-20px;'>".$match[$winmd_id]->winner."</div>";
			}
			if($fid){
				$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>';
			}	
			$kl .=  '</div>';
		}
		$this->_lists['knock_layout'] = $kl;
	}
	function getKformat($curf){
		$format[] = JHTML::_('select.option',  0, JText::_('BLFA_SELFORM'), 'id', 'name' );
		$format[] = JHTML::_('select.option',  2, 2, 'id', 'name' );
		$format[] = JHTML::_('select.option',  4, 4, 'id', 'name' );
		$format[] = JHTML::_('select.option',  8, 8, 'id', 'name' );
		$format[] = JHTML::_('select.option',  16, 16, 'id', 'name' );
		$format[] = JHTML::_('select.option',  32, 32, 'id', 'name' );
		$format[] = JHTML::_('select.option',  64, 64, 'id', 'name' );
		$format[] = JHTML::_('select.option',  128, 128, 'id', 'name' );
		/*$format[] = JHTML::_('select.option',  256, 256, 'id', 'name' );
		$format[] = JHTML::_('select.option',  512, 512, 'id', 'name' );*/
		$this->_lists['format'] = JHTML::_('select.genericlist',   $format, 'format_fe', 'class="inputbox" size="1" id="format_fe"', 'id', 'name', $curf );
	}
	function AdmMDSave(){
		$t_single = JRequest::getVar( 't_single', 0, 'post', 'int' );
		$t_knock = JRequest::getVar( 't_knock', 0, 'post', 'int' );
		$s_id = JRequest::getVar( 'sid', 0, 'post', 'int' );
		$this->season_id = $s_id;
		$post		= JRequest::get( 'post' );
		$post['s_id'] = $s_id;
		$post['k_format'] = JRequest::getVar( 'format_fe', 0, 'post', 'int' );
		$post['m_descr'] = JRequest::getVar( 'm_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$row 	= new JTableMday($this->db);
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
		
		$mj = 0;

		$prevarr = array();
	
		if($t_knock){
						
			if(isset($_POST['teams_kn']) && count($_POST['teams_kn'])){
		
				foreach($_POST['teams_kn'] as $home_team){
					$match 	= new JTableMatch($this->db);
		
					$match->load(isset($_POST['match_id'][$mj])?$_POST['match_id'][$mj]:0);
		
					$match->m_id = $row->id;
		
					$match->team1_id = intval($home_team);
		
					$match->team2_id = intval($_POST['teams_kn_aw'][$mj]);
		
					$match->score1 = intval($_POST['res_kn_1'][$mj]);
		
					$match->score2 = intval($_POST['res_kn_1_aw'][$mj]);
					$match->k_ordering = $mj;
					$match->k_stage = 1;
					
					if(!isset($_POST['res_kn_1'][$mj]) || !isset($_POST['res_kn_1_aw'][$mj]) || $_POST['res_kn_1'][$mj] == '' || $_POST['res_kn_1_aw'][$mj] == ''){
						$match->m_played = 0;
					}else{
						if(!$match->id){
							$match->m_played = 1;
						}
						
					}
					
					if(!$match->id){
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".intval($home_team);
						$this->db->setQuery($query);
						$venue = $this->db->loadResult();
						if($venue){
							$match->venue_id = $venue;
						}	
					}
					$match->published = 1;
					if (!$match->check()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					if (!$match->store()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					$match->checkin();
					
					$prevarr[] = $match->id;
					
					$mj++;
				}
			}
			
			
			$levcount = 2;
			while (isset($_POST['teams_kn_'.$levcount])){
				
				$mj=0;
				foreach($_POST['teams_kn_'.$levcount] as $home_team){
					$match 	= new JTableMatch($this->db);
					
					$match->load(isset($_POST['matches_'.$levcount][$mj])?$_POST['matches_'.$levcount][$mj]:0);
		
					$match->m_id = $row->id;
		
					$match->team1_id = intval($home_team);
		
					$match->team2_id = intval($_POST['teams_kn_aw_'.$levcount][$mj]);
		
					$match->score1 = intval($_POST['res_kn_'.$levcount][$mj]);
		
					$match->score2 = intval($_POST['res_kn_'.$levcount.'_aw'][$mj]);
					$match->k_ordering = $mj;
					$match->k_stage = $levcount;
					if($_POST['res_kn_'.$levcount][$mj] && $_POST['res_kn_'.$levcount.'_aw'][$mj]){
						$match->m_played = 1;
					}
					if($levcount >2){
						$prev_m = isset($_POST['matches_'.($levcount-1)][$mj*2-1])?$_POST['matches_'.($levcount-1)][$mj*2-1]:0;
						$prev_m2 = isset($_POST['matches_'.($levcount-1)][$mj*2])?$_POST['matches_'.($levcount-1)][$mj*2]:0;
					}else{
						$prev_m = isset($_POST['match_id'][$mj*2-1])?$_POST['match_id'][$mj*2-1]:-1;
						$prev_m2 = isset($_POST['match_id'][$mj*2])?$_POST['match_id'][$mj*2]:-1;
					}
					//echo $prev_m."*";
					if($prev_m  && $prev_m2){
						$match_prev1 	= new JTableMatch($this->db);
						$match_prev2 	= new JTableMatch($this->db);
						$match_prev1->load($prev_m);
						$match_prev2->load($prev_m2);
						//echo $match_prev1->m_played.'-'.$match_prev2->m_played."<br />";
						if((!$match_prev1->m_played && $match_prev1->team1_id && $match_prev1->team2_id) || !$match_prev2->m_played && $match_prev2->team1_id && $match_prev2->team2_id){
							$match->m_played = 0;
						}
						
					}
					
					if(!$_POST['res_kn_'.$levcount][$mj] && !$_POST['res_kn_'.$levcount.'_aw'][$mj]){
						
						$match->m_played = isset($match->m_played)?$match->m_played:1;
					}else{
						$match->m_played = isset($match->m_played)?$match->m_played:1;
					}
					if(!$match->id){
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".intval($home_team);
						$this->db->setQuery($query);
						$venue = $this->db->loadResult();
						if($venue){
							$match->venue_id = $venue;
						}	
					}
					//$match->is_extra = intval($_POST['extra_time'][$mj]);
					$match->published = 1;
					if (!$match->check()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					if (!$match->store()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					$match->checkin();
					$mj++;
					
					$prevarr[] = $match->id;
					
				}
				$levcount++;
			}
			
			$this->db->setQuery("DELETE FROM #__bl_match WHERE m_id = ".$row->id." AND id NOT IN (".implode(',',$prevarr).")");
		
			$this->db->query();
				
		}else{
			
		
			$arr_match = array();
		
			if(isset($_POST['home_team']) && count($_POST['home_team'])){
		
				foreach($_POST['home_team'] as $home_team){
		
					$match 	= new JTableMatch($this->db);
		
					$match->load($_POST['match_id'][$mj]);
		
					$match->m_id = $row->id;
		
					$match->team1_id = intval($home_team);
		
					$match->team2_id = intval($_POST['away_team'][$mj]);
		
					$match->score1 = intval($_POST['home_score'][$mj]);
		
					$match->score2 = intval($_POST['away_score'][$mj]);
		
					$match->is_extra = isset($_POST['extra_time'][$mj])?intval($_POST['extra_time'][$mj]):0;
					$match->published = 1;
		
					$match->m_played = intval($_POST['match_played'][$mj]);
		
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
		
				$this->db->setQuery("DELETE FROM #__bl_match WHERE id NOT IN (".implode(',',$arr_match).") AND m_id = ".$row->id);
		
				$this->db->query();
		
			}else{
		
				$this->db->setQuery("DELETE FROM #__bl_match WHERE m_id = ".$row->id);
		
				$this->db->query();
		
			}
		}
		$this->id = $row->id;
	}
	function delAdmMD(){
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );

		JArrayHelper::toInteger($cid, array(0));
		
		if(count($cid)){
	
			$cids = implode(',',$cid);
	
			$this->db->setQuery("DELETE FROM #__bl_matchday WHERE id IN (".$cids.")");
	
			$this->db->query();
	
		}
	}
}