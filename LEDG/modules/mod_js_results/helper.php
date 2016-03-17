<?php
/**
* @version		$Id: helper.php 11074 2008-10-13 04:54:12Z ian $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modBlResHelper
{
	function &getList( &$params )
	{
		global $mainframe;

		$db =& JFactory::getDBO();
		$rows = array();

		$match_count 	= $params->get( 'match_count' );
		$ssss_id = $params->get( 'sidgid' );
		if($ssss_id == '0'){
			$s_id=0;
			$gr_id=0;
		}else{
			$ex = explode('|',$ssss_id );
			$s_id = $ex[0];
			$gr_id = $ex[1];
		}
		$gr_sql = '';
		if($gr_id){
			$query = "SELECT t_id FROM  #__bl_grteams WHERE g_id=".$gr_id;
			$db->setQuery($query);
			$grtm = $db->loadResultArray();
			if(count($grtm) == 1){
				$gr_sql = " AND (t1.id = {$grtm[0]} OR t2.id = {$grtm[0]} ) ";
			}else if(count($grtm) > 1){
				$gr_sql = " AND (t1.id IN (".implode(',',$grtm).") OR t2.id IN (".implode(',',$grtm)."))";
			}
		}
		$t_id = $params->get( 'team_id' );
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_name'";
		$db->setQuery($query);
		$pln = $db->loadResult();
		
		if($s_id){
			$query = "SELECT t.t_single FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE s.s_id=".$s_id;
			$db->setQuery($query);
			if($db->loadResult()){
				
				if(!$pln){
					$nname = "CONCAT(t1.first_name,' ',t1.last_name) as home";
					$nname2 = "CONCAT(t2.first_name,' ',t2.last_name) as away";
				}else{
					$nname = "IF(t1.nick<>'',t1.nick,CONCAT(t1.first_name,' ',t1.last_name)) as home";
					$nname2 = "IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name)) as away";
				}
				$query = "SELECT m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, ".$nname.", ".$nname2.", score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,'' as emb1, '' as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played = 1 ".($t_id?(" AND (t1.id=".$t_id." OR t2.id=".$t_id.")"):"").$gr_sql
						." ORDER BY m.m_date desc,m.m_time desc,md.id LIMIT ".$match_count;
			}else{
				$query = "SELECT m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1, t2.t_emblem as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played = 1 ".($t_id?(" AND (t1.id=".$t_id." OR t2.id=".$t_id.")"):"").$gr_sql
						." ORDER BY m.m_date desc,m.m_time desc,md.id LIMIT ".$match_count;
			}	
		}else{
				$query = "SELECT DISTINCT(m.id),m.m_date,m.m_time,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1, t2.t_emblem as emb2"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2, #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id"
					." WHERE t.t_single='0' AND t.published=1 AND s.published=1 AND m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played = 1 ".($t_id?(" AND (t1.id=".$t_id." OR t2.id=".$t_id.")"):"")
					." ORDER BY m.m_date desc,m.m_time desc,md.id LIMIT ".$match_count;
				
				if(!$t_id){
					$query = "SELECT DISTINCT(m.id),t.t_single"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id"
						." WHERE m.m_id = md.id AND md.s_id=s.s_id AND m.published = 1 AND m.m_played = 1 AND t.published=1 AND s.published=1"
						." ORDER BY m.m_date desc,m.m_time desc,md.id LIMIT ".$match_count;
				}	
		}		
		$db->setQuery($query);

		$rows = $db->loadObjectList();
	
		if(!$s_id && !$t_id){
			$new_row = array();
			for($i=0;$i<count($rows);$i++){

				$row = $rows[$i];
				if($row->t_single){
					if(!$pln){
						$nname = "CONCAT(t1.first_name,' ',t1.last_name) as home";
						$nname2 = "CONCAT(t2.first_name,' ',t2.last_name) as away";
					}else{
						$nname = "IF(t1.nick<>'',t1.nick,CONCAT(t1.first_name,' ',t1.last_name)) as home";
						$nname2 = "IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name)) as away";
					}
					$query = "SELECT 1 as ssingle,m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, ".$nname.", ".$nname2.", score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,'' as emb1, '' as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND m.id=".$row->id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played = 1 ";
					
				}else{
					$query = "SELECT 0 as ssingle,m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1, t2.t_emblem as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND m.id=".$row->id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played = 1 ";

				}
				$db->setQuery($query);

				$new_row = @array_merge($new_row,$db->loadObjectList());
				
			}

			return $new_row;
		}
		
		return $rows;
	}
	function &getET(&$params){
		$db =& JFactory::getDBO();
		$ssss_id = $params->get( 'sidgid' );
		if($ssss_id){
			$ex = explode('|',$ssss_id );
			$s_id = $ex[0];
			$query = "SELECT * FROM #__bl_seasons WHERE s_id = ".$s_id;

			$db->setQuery($query);

			$season_par = $db->LoadObjectList();

			$season_par = $season_par[0];
		}else{
			$season_par->s_enbl_extra = 0;
		}		
		return $season_par->s_enbl_extra;	
	}
	function &getStype(&$params){
		$db =& JFactory::getDBO();
		$ssss_id = $params->get( 'sidgid' );
		$ex = explode('|',$ssss_id );
		$s_id = $ex[0];
		$query = "SELECT t.t_single FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE s.s_id=".$s_id;
		$db->setQuery($query);
		$res = $db->loadResult();
		return $res;
	}
	function &getPhoto($plid){
		$db =& JFactory::getDBO();
		
		$query = "SELECT * FROM #__bl_players WHERE id=".$plid;
		$db->setQuery($query);
		$player = $db->loadObject();
		
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$player->id;
		$db->setQuery($query);
		$photos = $db->loadObjectList();
		
		$def_img = '';
		if($player->def_img){
			$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$player->def_img;
			$db->setQuery($query);
			$def_img = $db->loadResult();
		}else if(isset($photos[0])){
			$def_img = $photos[0]->filename;
		}
		return $def_img;
	}
}
