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

class modBlTableHelper
{
	function &getList( &$params )
	{
		global $mainframe;

		$db =& JFactory::getDBO();
		$rows = array();
		
		$match_count 	= $params->get( 'match_count' );
		$ssss_id = $params->get( 'sidgid' );
		$ex = explode('|',$ssss_id );
		$s_id = $ex[0];
		$gr_id = $ex[1];
		// table league
		
		$query = "SELECT * FROM #__bl_seasons WHERE s_id = ".$s_id;
			$db->setQuery($query);
			$season_par = $db->LoadObjectList();
			$season_par = $season_par[0];
		$groups_exists = array();	
		$table_view = array();
		
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_type,t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($s_id)." AND s.t_id = t.id ORDER BY t.name, s.s_name";
		$db->setQuery($query);
		$tourn = $db->loadObjectList();
		
		$t_single = $tourn[0]->t_single;
		
		//$query = "SELECT t.id FROM #__bl_season_teams as st, #__bl_teams as t WHERE t.id = st.team_id AND st.season_id = ".$s_id;
		if($t_single){
			$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_name'";
			$db->setQuery($query);
			$pln = $db->loadResult();
			if(!$pln){
				$nname = "CONCAT(t.first_name,' ',t.last_name) as t_name";
			}else{
				$nname = "IF(t.nick<>'',t.nick,CONCAT(t.first_name,' ',t.last_name)) as t_name";
			}
		
			$query = "SELECT t.id,bonus_point,".$nname.",'' as t_yteam FROM #__bl_season_players as st, #__bl_players as t WHERE t.id = st.player_id AND st.season_id = ".$s_id;
		}else{
			$query = "SELECT t.id,bonus_point,t.t_yteam,t.t_name,t.t_emblem FROM #__bl_season_teams as st, #__bl_teams as t WHERE t.id = st.team_id AND st.season_id = ".$s_id;
		}
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		for ($i=0;$i<count($teams);$i++){
				$tid = $teams[$i]->id;
				
				$teams_name = $teams[$i]->t_name;

				$teams_your = $teams[$i]->t_yteam;
			
			$query = "SELECT bonus_point FROM #__bl_season_teams WHERE team_id = ".$tid." AND season_id=".$s_id;
			$db->setQuery($query);
			$bonus_point = $db->loadResult();
			
			
			
			if($t_single){
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$tid;
				$db->setQuery($query);
				$photos = $db->loadObjectList();
				
				$query = "SELECT p.*,c.country,c.ccode FROM #__bl_players as p LEFT JOIN #__bl_countries as c ON c.id=p.country_id  WHERE p.id = ".$tid;
				$db->setQuery($query);
				$players = $db->loadObjectList();
				$player = $players[0];
				
				$emblems = '';
				if($player->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$player->def_img;
					$db->setQuery($query);
					$emblems = $db->loadResult();
				}else if(isset($photos[0])){
					$emblems = $photos[0]->filename;
				}
			}else{
				$emblems = $teams[$i]->t_emblem;
			}
			
			if($t_single){
				$query = "SELECT gr.g_id FROM  #__bl_season_players as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$s_id." AND g.id = gr.g_id AND gr.t_id = st.player_id AND st.season_id = ".$s_id." AND st.player_id = ".$tid." LIMIT 1";
			
			}else{
				$query = "SELECT gr.g_id FROM  #__bl_season_teams as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$s_id." AND g.id = gr.g_id AND gr.t_id = st.team_id AND st.season_id = ".$s_id." AND st.team_id = ".$tid." LIMIT 1";
			}
			
			//$query = "SELECT gr.g_id FROM  #__bl_season_teams as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$s_id." AND g.id = gr.g_id AND gr.t_id = st.team_id AND st.season_id = ".$s_id." AND st.team_id = ".$tid." LIMIT 1";
			$db->setQuery($query);
			$group_id = $db->loadResult();
			if(!in_array($group_id,$groups_exists) && $group_id){
				if($gr_id && $season_par->s_groups){	
					if($gr_id==$group_id){
						$groups_exists[] = $group_id;
					}
				}else{
					$groups_exists[] = $group_id;
				}
				
			}
			
		if(($season_par->s_groups && $group_id) || !$season_par->s_groups){
			
			$query = "SELECT cfg_value FROM #__bl_config  WHERE cfg_name = 'yteam_color'";
			$db->setQuery($query);
			$teams_your_color = $db->loadResult();
	
			
			if($t_single){
				$query = "SELECT gr.g_id FROM  #__bl_season_players as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$s_id." AND g.id = gr.g_id AND gr.t_id = st.player_id AND st.season_id = ".$s_id." AND st.player_id = ".$tid." LIMIT 1";
			
			}else{
				$query = "SELECT gr.g_id FROM  #__bl_season_teams as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$s_id." AND g.id = gr.g_id AND gr.t_id = st.team_id AND st.season_id = ".$s_id." AND st.team_id = ".$tid." LIMIT 1";
			}
			$db->setQuery($query);
			$group_id = $db->loadResult();
			if(!in_array($group_id,$groups_exists) && $group_id){
				$groups_exists[] = $group_id;
			}
			
			$query = "SELECT SUM(score1) as sc,SUM(score2) as rc FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND m.team1_id = ".$tid;
			$db->setQuery($query);
			$home = $db->loadObjectList();
			$query = "SELECT SUM(score1) as rc,SUM(score2) as sc FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND m.team2_id = ".$tid;
			$db->setQuery($query);
			$away = $db->loadObjectList();
			
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 > m.score2) AND m.is_extra = 0 AND m.m_played = 1 AND md.is_playoff = 0";
			$db->setQuery($query);
			$wins = $db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team1_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0";
			$db->setQuery($query);
			$drows = $db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 < m.score2) AND m.is_extra = 0 AND md.is_playoff = 0 AND m.m_played = 1";
			$db->setQuery($query);
			$loose = $db->loadResult();
			
			$query = "SELECT SUM(bonus1) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND ".$tid." = m.team1_id AND md.is_playoff = 0 AND m.m_played = 1";
			$db->setQuery($query);
			$bonus1 = $db->loadResult();
			$query = "SELECT SUM(bonus2) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND ".$tid." = m.team2_id AND md.is_playoff = 0 AND m.m_played = 1";
			$db->setQuery($query);
			$bonus2 = $db->loadResult();
			
			$query = "SELECT SUM(points1) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND ".$tid." = m.team1_id AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '1'";
			$db->setQuery($query);
			$homebonus = $db->loadResult();
			$query = "SELECT SUM(points2) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND ".$tid." = m.team2_id AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '1'";
			$db->setQuery($query);
			$awabonus = $db->loadResult();
			
			//--// 
				
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 > m.score2) AND m.is_extra = 0 AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0'";
					$db->setQuery($query);
					$wins2 = $db->loadResult();
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team1_id) AND m.score1 = m.score2 AND m.is_extra = 0  AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0'";
					$db->setQuery($query);
					$drows2 = $db->loadResult();
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 < m.score2) AND m.is_extra = 0 AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '0'";
					$db->setQuery($query);
					$loose2 = $db->loadResult();
					
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (m.team2_id = ".$tid." AND m.score2 > m.score1)  AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0' AND m.is_extra = 0";
					$db->setQuery($query);
					$wins_away2 = $db->loadResult();
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team2_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0' AND m.is_extra = 0";
					$db->setQuery($query);
					$drows_away2 = $db->loadResult();
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team2_id AND m.score2 < m.score1)   AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '0' AND m.is_extra = 0";
					$db->setQuery($query);
					$loose_away2 = $db->loadResult();
				//--//
			
			$table_view[$i]['t_single'] = $t_single;
			
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (m.team2_id = ".$tid." AND m.score2 > m.score1) AND m.is_extra = 0 AND m.m_played = 1 AND md.is_playoff = 0";
			$db->setQuery($query);
			$wins_away = $db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team2_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0";
			$db->setQuery($query);
			$drows_away = $db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND (".$tid." = m.team2_id AND m.score2 < m.score1)  AND m.is_extra = 0 AND md.is_playoff = 0 AND m.m_played = 1";
			$db->setQuery($query);
			$loose_away = $db->loadResult();
			
			$wins_ext = 0;
			if($season_par->s_enbl_extra){
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND ((m.team2_id = ".$tid." AND m.score2 > m.score1) OR (".$tid." = m.team1_id AND m.score1 > m.score2)) AND m.is_extra = 1 AND md.is_playoff = 0 AND m.m_played = 1";
				$db->setQuery($query);
				$wins_ext = $db->loadResult();
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$s_id." AND m.published = 1 AND ((".$tid." = m.team2_id AND m.score2 < m.score1) OR (".$tid." = m.team1_id AND m.score1 < m.score2)) AND m.is_extra = 1 AND md.is_playoff = 0 AND m.m_played = 1";
				$db->setQuery($query);
				$loose_ext = $db->loadResult();
			}
			
			
			$table_view[$i]['g_id'] = $season_par->s_groups ? $group_id : 0;
			if($table_view[$i]['g_id']){
				$query = "SELECT group_name FROM #__bl_groups WHERE id=".intval($table_view[$i]['g_id']);
				$db->setQuery($query);
				$table_view[$i]['g_name'] = $db->loadResult();
			}else{
				$table_view[$i]['g_name'] = '';
			}
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
			$table_view[$i]['points'] = $wins2 * $season_par->s_win_point + $drows2 * $season_par->s_draw_point + $loose2 * $season_par->s_lost_point + $wins_away2 * $season_par->s_win_away + $drows_away2 * $season_par->s_draw_away + $loose_away2 * $season_par->s_lost_away + (($season_par->s_enbl_extra)?($wins_ext * $season_par->s_extra_win + $loose_ext * $season_par->s_extra_lost):0) + $bonus_point + $bonus1 + $bonus2 + $homebonus +$awabonus;
			
			$table_view[$i]['goal_score'] = $home[0]->sc + $away[0]->sc;	
			
			$table_view[$i]['yteam'] = $teams_your?$teams_your_color:'';
			if($table_view[$i]['played']){
				$table_view[$i]['winperc'] = ($wins + $wins_away + $wins_ext + ($table_view[$i]['draw']/2))/($table_view[$i]['played']);
			}else{
				$table_view[$i]['winperc'] = 0;
			}
			$query = "SELECT ev.fvalue as fvalue FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$tid." WHERE ef.published=1 AND ef.type = '1' AND ef.e_table_view = '1' ORDER BY ef.ordering";
			$db->setQuery($query);
	
			$table_view[$i]['ext_fields'] = $db->loadResultArray();
			
			$table_view[$i]['avulka_v'] = '';
			$table_view[$i]['avulka_cf'] = '';
			$table_view[$i]['avulka_cs'] = '';
			$table_view[$i]['avulka_qc'] = '';
			$table_view[$i]['t_emblem'] = $emblems;
			$table_view[$i]['goals_score'] = $home[0]->sc + $away[0]->sc;
				$table_view[$i]['goals_conc'] = $home[0]->rc + $away[0]->rc;
				$table_view[$i]['win_home'] = $wins;
				$table_view[$i]['draw_home'] = $drows;
				$table_view[$i]['lost_home'] = $loose;
				$table_view[$i]['win_away'] = $wins_away;
				$table_view[$i]['draw_away'] = $drows_away;
				$table_view[$i]['lost_away'] = $loose_away;
				$table_view[$i]['points_home'] = ($wins) * $season_par->s_win_point + ($drows) * $season_par->s_draw_point + ($loose) * $season_par->s_lost_point + $bonus1 + $homebonus;
				$table_view[$i]['points_away'] = ($wins_away2) * $season_par->s_win_away + ($drows_away2) * $season_par->s_draw_away + ($loose_away2) * $season_par->s_lost_away + $awabonus + $bonus2;
				
		}	
		}
		
		//---playeachother---///
		$query = "SELECT opt_value FROM #__bl_season_option WHERE s_id = ".$s_id." AND opt_name='equalpts_chk'";
		$db->setQuery($query);
		$equalpts_chk = $db->loadResult();
		
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
		
					$db->setQuery($query);
					
					$matchs_avulsa_win = $db->loadResult();
					
					$tm_equal_win = array();
					
					foreach ($tm as $tm_other){
						$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played=1 AND ((t1.id = ".$tm_other." AND m.score1>m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_other." AND m.score1<m.score2 AND t1.id IN (".implode(',',$tm).")))";
			
						$db->setQuery($query);
						
						$matchs_avulsa_win_other = $db->loadResult();
						
						if($matchs_avulsa_win_other == $matchs_avulsa_win){
							$tm_equal_win[] = $tm_other;
						}
					}
					
					$query = "SELECT SUM(score1) as sh,SUM(score2) as sw FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND m.m_played=1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND ((t1.id = ".$tm_one." AND t2.id IN (".implode(',',$tm_equal_win).")))";
		
					$db->setQuery($query);
				
					$matchs_avulsa_score = $db->loadRow();
					//var_dump($matchs_avulsa_score);
					
					$query = "SELECT SUM(score2) as sh,SUM(score1) as sw FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND m.m_played=1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND ((t2.id=".$tm_one." AND t1.id IN (".implode(',',$tm_equal_win).")))";
		
					$db->setQuery($query);
				
					$matchs_avulsa_rec = $db->loadRow();
					
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
	   	sort($groups_exists, SORT_NUMERIC);
	   }
	  	if(!$season_par->s_groups){
	  		$groups_exists = array(0);
	  	}
		if(count($sort_arr)){
			// sort fields 1-points, 2-wins percent, /*3-if equal between teams*/, 4-goal difference, 5-goal score
			$query = "SELECT * FROM #__bl_ranksort WHERE seasonid=".$s_id." ORDER BY ordering";
			$db->setQuery($query);
			$savedsort = $db->loadObjectList();
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
		
		
		
		
		return $table_view;
	}
	function &getET(&$params){
		$db =& JFactory::getDBO();
		$ssss_id = $params->get( 'sidgid' );
		$ex = explode('|',$ssss_id );
		$s_id = $ex[0];
		$query = "SELECT * FROM #__bl_seasons WHERE s_id = ".$s_id;

			$db->setQuery($query);

			$season_par = $db->LoadObjectList();

			$season_par = $season_par[0];
		return $season_par->s_groups;	
	}
	
}
