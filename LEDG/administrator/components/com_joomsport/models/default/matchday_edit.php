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

class matchday_editJSModel extends JSPRO_Models
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
		$mainframe = JFactory::getApplication();;
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$is_id = $cid[0];
		$season_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.s_id', 's_id', 0, 'int' );
		
		$row 	= new JTableMday($this->db);
		$row->load($is_id);
		$lists = array();	
		$s_id = $row->s_id?$row->s_id:$season_id;
		$tourn = $this->getSeasAttr($s_id);
		
		$this->_lists['s_id'] = $s_id;
		if($s_id != -1){
			$this->_lists['t_single'] = $tourn->t_single;
			$this->_lists['t_type'] = $tourn->t_type;
			$this->_lists['tourn'] = $tourn->name;
			$this->_lists['s_enbl_extra'] = $tourn->s_enbl_extra;
		}else{
			$this->_lists['t_single'] = 0;
			$tourn->t_single = 0;
			$this->_lists['tourn'] = JText::_('BLBE_FRIENDLYMATCH');
			$this->_lists['t_type'] = 0;
			$tourn->t_type = 0;
			$this->_lists['s_enbl_extra'] = 1;
		}
		
		$this->getPlList();
		
		$match = $this->getMatches($row,$tourn,$s_id);
		//betting
		$this->_lists['avail_betting'] = $this->isBet();
		if($this->isBet()){
			$this->_lists['betevents'] = $this->getBetEvents($s_id);
			$matchesid = array();
			if ($match) {
				foreach($match as $m) {
					$matchesid[] = $m->id;
				}
			}
			$this->_lists['matchbetevents'] = $this->getMatchesBetEvents($matchesid);

		}
		$this->_lists["match"] = $match;
		
		$is_team = array();
		
		if($tourn->t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
		}else{
			$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
		}
		if($s_id == -1){
			$query = "SELECT * FROM #__bl_teams ORDER BY t_name";
		}
		$this->db->setQuery($query);
		$team = $this->db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, ($tourn->t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' ); 
		$teamis = array_merge($is_team,$team);
		$this->_lists['teams1'] = JHTML::_('select.genericlist',   $teamis, 'teams1', 'class="inputbox" size="1" id="teams1"', 'id', 't_name', 0 );
		$this->_lists['teams2'] = JHTML::_('select.genericlist',   $teamis, 'teams2', 'class="inputbox" size="1" id="teams2"', 'id', 't_name', 0 );
		$this->_lists['is_playoff'] 		= JHTML::_('select.booleanlist',  'is_playoff', 'class="inputbox"', $row->is_playoff );
		
		if(count($match) && $tourn->t_type && $row->k_format){
			$this->getKnock($row,$tourn,$match,$s_id);
		}
		
		$this->getKnockFormat($row);
		
		$this->_data = $row;
		
	}
	//betting
	    protected function getMatchesBetEvents($matchesid){
        $query = "SELECT bbc.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_coeffs bbc ON bbc.idevent=bbe.id"
                ."\n WHERE bbc.idmatch IN (".implode(',', $matchesid).")";

        $this->db->setQuery($query);
        $matchevents = $this->db->loadObjectList();
        $resultme = array();
        if ($matchevents) {
            foreach($matchevents as $me) {
                $resultme[$me->idmatch][$me->idevent] = $me;
            }
        }

        return $resultme;
    }
    
    protected function getBetEvents($s_id) {
        $query = "SELECT bbe.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_templates_events bbte ON bbte.idevent=bbe.id"
                ."\n INNER JOIN #__bl_betting_templates bbt ON bbt.id=bbte.idtemplate"
                ."\n INNER JOIN #__bl_seasons bs ON bs.s_id=bbte.idtemplate AND bs.s_id=$s_id"
                ."\n ORDER BY bbe.type ASC";
        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

	protected function getPlList(){
		$is_pl[] = JHTML::_('select.option',  0, JText::_('BLBE_SELPLAYER'), 'id', 'name' ); 
		$query = "SELECT id,CONCAT(first_name,' ',last_name) as name FROM #__bl_players ORDER BY first_name,last_name";
		$this->db->setQuery($query);
		$pl = $this->db->loadObjectList();
		if(count($pl)){
			$is_pl = array_merge($is_pl,$pl);
		}
		$this->_lists['plmd'] = JHTML::_('select.genericlist',   $is_pl, 'plmd', 'class="inputbox" size="1" id="plmd"', 'id', 'name', 0 );
		$this->_lists['plmd_away'] = JHTML::_('select.genericlist',   $is_pl, 'plmd_away', 'class="inputbox" size="1" id="plmd_away"', 'id', 'name', 0 );
		
	}
	
	protected function getMatches(& $row,& $tourn, $s_id){
		$orderby = $tourn->t_type?"m.k_stage,m.k_ordering":"m.id";
		if($s_id == -1){
			$query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid "
			." FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id"
			." WHERE m.m_id = ".$row->id." AND m.m_single = '1'"
			." ORDER BY ".$orderby;
			$this->db->setQuery($query);
			$match = $this->db->loadObjectList();
			
			$query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid"
						." FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id"
						." WHERE m.m_id = ".$row->id." AND m.m_single = '0'"
						." ORDER BY ".$orderby;
			$this->db->setQuery($query);
			$match_t = $this->db->loadObjectList();
			if(count($match)){
			
				$match_t = array_merge($match_t,$match);
			}
			return $match_t;
		}else{
			
			if($tourn->t_single){
				$query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid "
						." FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id"
						." WHERE m.m_id = ".$row->id
						." ORDER BY ".$orderby;
			
			}else{
				$query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid"
						." FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id"
						." WHERE m.m_id = ".$row->id
						." ORDER BY ".$orderby;
			}
			$this->db->setQuery($query);
			return $this->db->loadObjectList();
		}	
	}
	
	protected function getKnockFormat(& $row){
		$format[] = JHTML::_('select.option',  0, JText::_('BLBE_SELFORM'), 'id', 'name' );
		$format[] = JHTML::_('select.option',  2, 2, 'id', 'name' );
		$format[] = JHTML::_('select.option',  4, 4, 'id', 'name' );
		$format[] = JHTML::_('select.option',  8, 8, 'id', 'name' );
		$format[] = JHTML::_('select.option',  16, 16, 'id', 'name' );
		$format[] = JHTML::_('select.option',  32, 32, 'id', 'name' );
		$format[] = JHTML::_('select.option',  64, 64, 'id', 'name' );
		/*$format[] = JHTML::_('select.option',  128, 128, 'id', 'name' );
		$format[] = JHTML::_('select.option',  256, 256, 'id', 'name' );
		$format[] = JHTML::_('select.option',  512, 512, 'id', 'name' );*/
		$this->_lists['format'] = JHTML::_('select.genericlist',   $format, 'format_post', 'class="inputbox" size="1" id="format_post"', 'id', 'name', $row->k_format );
		
	}
	
	protected function getKnock($row,$tourn,$match,$s_id){
			$is_team = array();
			$cfg = $this->get_kn_cfg();
		
			$wdth = $cfg->wdth;
			$height = $cfg->height;
			$step = $cfg->step; 
			$top_next = $cfg->top_next;
			$zz = 2;
			
			$p=0;
			
			if($tourn->t_single){
				$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
			}else{
				$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
			}
			$this->db->setQuery($query);
		
			$team = $this->db->loadObjectList();
		
			$is_team[] = JHTML::_('select.option',  0, ($tourn->t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' ); 
			
			if(count($team)){
				$teamis = array_merge($is_team,$team);
			}else{
				$teamis = $is_team;
			}
		
			$fid = $row->k_format;
		
			$kl = '';
			
			$kl .= '<div style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #777;">';
			
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
						$match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($match[$i]->id)?($match[$i]->id):'');
						$kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth).'px;"><input type="hidden" name="match_id[]" value="'.$match[$i]->id.'"><a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>':"");
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
						$match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
						if(isset($match[$cur_ind]->id) && isset($match[$firstchld_ind]->winnerid) && isset($match[$firstchld_ind + 1]->winnerid)){
							$kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth).'px;"><input type="hidden" name="matches_'.($p+1).'[]" value="'.(isset($match[$cur_ind]->id)?$match[$cur_ind]->id:0).'"><a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>';
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
			$this->_lists['knock_layout'] = $kl;
	}
	
	public function orderMDay(){
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array(), 'post', 'array' );
		
		$row		= new JTableMday($this->db);;
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
	
	public function saveMday(){
		
		$t_single = JRequest::getVar( 't_single', 0, 'post', 'int' );
		$t_knock = JRequest::getVar( 't_knock', 0, 'post', 'int' );
		
		$post		= JRequest::get( 'post' );
		$post['k_format'] = JRequest::getVar( 'format_post', 0, 'post', 'int' );
		$post['m_descr'] = JRequest::getVar( 'm_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$row 	= new JTableMday($this->db);
		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		// save match
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
					if($_POST['res_kn_1'][$mj]){
						$match->score1 = intval($_POST['res_kn_1'][$mj]);
					}
					if($_POST['res_kn_1_aw'][$mj]){
						$match->score2 = intval($_POST['res_kn_1_aw'][$mj]);
					}
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
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".$match->team1_id;
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
			//$lev = isset($_POST['teams_kn1'])?$_POST['teams_kn1']:0;
			while (isset($_POST['teams_kn_'.$levcount])){
				
				$mj=0;
				foreach($_POST['teams_kn_'.$levcount] as $home_team){
					$match 	= new JTableMatch($this->db);
					
					$match->load(isset($_POST['matches_'.$levcount][$mj])?$_POST['matches_'.$levcount][$mj]:0);
		
					$match->m_id = $row->id;
		
					$match->team1_id = intval($home_team);
		
					$match->team2_id = intval($_POST['teams_kn_aw_'.$levcount][$mj]);
					if($_POST['res_kn_'.$levcount][$mj] != ''){
						$match->score1 = intval($_POST['res_kn_'.$levcount][$mj]);
					}
					if($_POST['res_kn_'.$levcount.'_aw'][$mj] != ''){
						$match->score2 = intval($_POST['res_kn_'.$levcount.'_aw'][$mj]);
					}
					$match->k_ordering = $mj;
					$match->k_stage = $levcount;
					if($_POST['res_kn_'.$levcount][$mj] && $_POST['res_kn_'.$levcount.'_aw'][$mj]){
						$match->m_played = 1;
					}
					if($_POST['res_kn_'.$levcount.'_aw'][$mj] == '' || $_POST['res_kn_'.$levcount][$mj] == ''){
						$match->m_played = 0;
					}
					if(!$match->id){
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".$match->team1_id;
						$this->db->setQuery($query);
						$venue = $this->db->loadResult();
						if($venue){
							$match->venue_id = $venue;
						}	
					}
					
					//echo $_POST['matches_'.($levcount-1)][$mj*2-1];
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
					if($_POST['home_score'][$mj] != ''){
						$match->score1 = intval($_POST['home_score'][$mj]);
					}
					if($_POST['away_score'][$mj] != ''){
						$match->score2 = intval($_POST['away_score'][$mj]);
					}
					$match->is_extra = isset($_POST['extra_time'][$mj])?intval($_POST['extra_time'][$mj]):0;
					$match->published = 1;
		
					$match->m_played = intval($_POST['match_played'][$mj]);
		
					$match->m_date = strval($_POST['match_data'][$mj]);
		
					$match->m_time = strval($_POST['match_time'][$mj]);
					$match->m_single = strval($_POST['matchtype'][$mj]);
					
					//betting
					$match->betavailable = intval($_POST['bet_available'][$mj]);
                    if ($match->betavailable){                    
                        $betfinishdate  = JRequest::getVar( 'betfinishdate', array(), 'post', 'array' );
                        $betfinishtime = JRequest::getVar( 'betfinishtime', array(), 'post', 'array' );
                        $match->betfinishdate = $betfinishdate[$mj];
                        $match->betfinishtime = $betfinishtime[$mj];
                    }
					
		

					
					if(!$match->id){
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".$match->team1_id;
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
					if ($match->betavailable){
                        $bet_coeff_old1 = JRequest::getVar( 'bet_coeff_old1', array(), 'post', 'array' );
                        $bet_coeff_old2 = JRequest::getVar( 'bet_coeff_old2', array(), 'post', 'array' );
                        if ($bet_coeff_old1[$match->id]){
                            foreach($bet_coeff_old1[$match->id] as $idevent=>$coeff1) {
                                $coeff1 = floatval($coeff1);
                                $coeff2 = floatval($bet_coeff_old2[$match->id][$idevent]);
                                $bet = new JTableBettingCoeffs($this->db);
                                $bet->idmatch = $match->id;
                                $bet->idevent = $idevent;
                                $bet->load(array('idmatch'=>$bet->idmatch, 'idevent'=>$bet->idevent));
                                if (!$bet->id) {
                                    $bet->idmatch = $match->id;
                                    $bet->idevent = $idevent;                                    
                                }
                                $bet->coeff1 = $coeff1;
                                $bet->coeff2 = $coeff2;
                                $bet->betfinishdate = $betfinishdate[$mj];
                                $bet->betfinishtime = $betfinishtime[$mj];
                                $bet->store();
                            }
                        }
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
		$this->_id = $row->id;
	}
	
	function deleteMday($cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$this->db->setQuery("DELETE FROM #__bl_matchday WHERE id IN (".$cids.")");
			$this->db->query();
			$this->db->setQuery("DELETE FROM #__bl_match WHERE m_id IN (".$cids.")");
			$this->db->query();
		}
	}
	
	
}