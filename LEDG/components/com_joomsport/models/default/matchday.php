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

class matchdayJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $s_id = null;
	var $t_single = null;
	var $t_type = null;
	var $m_id = null;
	var $_layout = '';
	
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
		
		$query = "SELECT m_name FROM #__bl_matchday WHERE id=".$this->m_id;
		$this->db->setQuery($query);
		$md_title = $this->db->loadResult();
		//title
		$this->_params = $this->JS_PageTitle($md_title);

		$query = "SELECT s_id FROM #__bl_matchday WHERE id=".$this->m_id;
		$this->db->setQuery($query);
		$this->s_id = $this->db->loadResult();
		
		$this->_lists["season_par"] = $this->getSParametrs($this->s_id);
		
		//get tiurnament type
		$tourn = $this->getTournOpt($this->s_id);
		if($tourn){
			$this->t_single = $tourn->t_single;
			$this->t_type = $tourn->t_type;
		}

		if($this->t_type){
			$this->getKnockMd();
		}else{
			$this->getGroupMd();
		}
		
		
		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1);
		
		
	}
	function getGroupMd(){
		$pln = $this->getJS_Config('player_name');
		if($this->t_single){
			$query = "SELECT m.*,md.m_name,md.id as mdid,md.s_id, ".($pln?"IF(t1.nick<>'',t1.nick,CONCAT(t1.first_name,' ',t1.last_name))":"CONCAT(t1.first_name,' ',t1.last_name)")." AS home,"
					." ".($pln?"IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name))":"CONCAT(t2.first_name,' ',t2.last_name)")." AS  away,t1.id as hm_id,t2.id as aw_id"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.id = ".$this->m_id;
		}else{
			$query = "SELECT md.m_name,m.*, t1.t_name as home, t2.t_name as away,md.s_id,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.id = ".$this->m_id
					." ORDER BY m.m_date,m.m_time";
		}
		
		
		
		
		$this->db->setQuery($query);
		$this->_lists["match"] = $this->db->loadObjectList();
		//friendly
		
		if($this->s_id == -1){
			$query = "SELECT m.*,md.m_name,md.id as mdid,md.s_id, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='1' AND md.id = ".$this->m_id;
			$this->db->setQuery($query);
			$friendly_single = $this->db->loadObjectList();		
			$query = "SELECT md.m_name,m.*, t1.t_name as home, t2.t_name as away,md.s_id,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='0' AND md.id = ".$this->m_id;		
			$this->db->setQuery($query);
			$friendly_team = $this->db->loadObjectList();
			$this->_lists["match"] = @array_merge($friendly_single,$friendly_team);
		}
		
	}
	function getKnockMd(){
		$Itemid = JRequest::getInt('Itemid');
		if($this->t_single){
			$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,"
					." CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,"
					." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
					." IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,"
					." IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
					." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.id=".$this->m_id
					."  ORDER BY m.k_stage,m.k_ordering";
		
			}else{
				$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
				." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
				." WHERE m.m_id = md.id AND m.published = 1 AND md.id=".$this->m_id
				." ORDER BY m.k_stage,m.k_ordering";
			
			}
			
			$this->db->setQuery($query);
		
			$matchs = $this->db->loadObjectList();

			$k_format = $matchs[0]->k_format;	
			
			if(count($matchs) && $k_format){
				$match = $matchs;

				if($this->t_single){
					$query = "SELECT MAX(LENGTH(CONCAT(t.first_name,' ',t.last_name)))"
							." FROM #__bl_season_players as st, #__bl_players as t"
							." WHERE t.id = st.player_id AND st.season_id = ".$this->s_id;
				}else{
					$query = "SELECT MAX(LENGTH(t.t_name))"
							." FROM #__bl_season_teams as st, #__bl_teams as t"
							." WHERE t.id = st.team_id AND st.season_id = ".$this->s_id;
				}
				$this->db->setQuery($query);
				$mxl = $this->db->loadResult();
				
				if($this->getJS_Config('knock_style')){
					$kl = $this->VertKnView($mxl,$match,$k_format, $Itemid);
				}else{
					$kl = $this->HorKnView($mxl,$match,$k_format,$Itemid);
				}
				
				
				
			$this->_lists['knock_layout'] = $kl;
			
			$this->_layout = '_knock';
		}	
	}
	
	function VertKnView($mxl,$match,$k_format,$Itemid){
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
		

		$kl = '<br />';
		
		$zz = 2;
		$p=0;
		
		$wdth = $cfg->wdth;
		$height = $cfg->height;
		$step = $cfg->step; 
		$top_next = $cfg->top_next;
		

		$fid = $k_format;
		
		$kl .= '<div class="combine-box-vert" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:auto;border:1px solid #777;">';
			
		$bz = 0;
		$vz = 1;
		while(floor($fid/$zz) >= 1){
			
			for($i=0;$i<floor($fid/$zz);$i++){
				
				//$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
					
				
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
					$kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($top_next + 40).'px; left:'.(20*($i+2) + ($i+1)*$wdth + $bz - 20).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
					$bz += $wdth +20;
				}else{
					
					$firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
					//$match[$firstchld_ind]->winner = ($pln && $match[$firstchld_ind]->winner_nick)?($match[$firstchld_ind]->winner_nick):($match[$firstchld_ind]->winner);
					//$match[$firstchld_ind+1]->winner = ($pln && $match[$firstchld_ind+1]->winner_nick)?$match[$firstchld_ind+1]->winner_nick:$match[$firstchld_ind+1]->winner;
					$cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
					
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
		return $kl;
	}
	
	function HorKnView($mxl,$match,$k_format,$Itemid){
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
	

		$kl = '<br />';
		
		$zz = 2;
		$p=0;
		
		$wdth = $cfg->wdth;
		$height = $cfg->height;
		$step = $cfg->step; 
		$top_next = $cfg->top_next;

		$fid = $k_format;
		
		$kl .= '<div class="combine-box-new" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #ccc;">';
			
		
		while(floor($fid/$zz) >= 1){
			
			for($i=0;$i<floor($fid/$zz);$i++){
				
				$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
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
						echo $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
						$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
						$match[$firstchld_ind+1]->m_played = 1;
					}
					if(!$match[$firstchld_ind+1]->away && $match[$firstchld_ind+1]->home){
						$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
						$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
						$match[$firstchld_ind+1]->m_played = 1;
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
		return $kl;
	}
	
}	