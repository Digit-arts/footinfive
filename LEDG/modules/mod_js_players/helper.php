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

class modBlPlayersHelper
{
	function &getList( &$params )
	{
		global $mainframe;

		$db =& JFactory::getDBO();
		$rows = array();

		
		$event_id = $params->get( 'event_id' );
		$team_id = $params->get( 'team_id' );
		$display_count 	= $params->get( 'display_count' );
		$displ_team = $params->get( 'displ_team' );
		
		$ssss_id = $params->get( 'sidgid' );
		$ex = explode('|',$ssss_id );
		$s_id = $ex[0];
		$gr_id = $ex[1];
		
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_type,t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($s_id)." AND s.t_id = t.id ORDER BY t.name, s.s_name";
		$db->setQuery($query);
		$tourn = $db->loadObjectList();
		
		$t_single = $tourn[0]->t_single;
		
		$query = "SELECT result_type FROM #__bl_events WHERE id = ".$event_id;
		$db->setQuery($query);
		$restype = $db->loadResult();
		
		if($restype){
			$rest = " AVG(me.ecount) as cnt ";
		}else{
			$rest = " SUM(me.ecount) as cnt ";
		}
		
		$query = "SELECT player_event FROM #__bl_events WHERE id = ".$event_id;
		$db->setQuery($query);
		$plev = $db->loadResult();
		
		if($plev == 2){
			$rest = " SUM(me.ecount) as cnt ";
			
			$evcnt = " (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id) AND ev.id = ".$event_id;
		}else{
			$evcnt = " ev.id=me.e_id AND me.e_id = ".$event_id;
		}
		$gr_sql = '';
		if($gr_id){
			$query = "SELECT t_id FROM  #__bl_grteams WHERE g_id=".$gr_id;
			$db->setQuery($query);
			$grtm = $db->loadResultArray();
			if(count($grtm) == 1){
				$gr_sql = " AND t.id = {$grtm[0]} ";
			}else if(count($grtm) > 1){
				$gr_sql = " AND t.id IN (".implode(',',$grtm).") ";
			}
		}
		
		if(!$t_single){
			$query = "SELECT ".$rest.",CONCAT(pl.first_name,' ',pl.last_name) as name,pl.nick,".($team_id?"t.t_name,t.id as tid":"'' as t_name,'' as tid")." ,ev.e_img,ev.e_name,pl.id,pl.def_img"
					." FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md, #__bl_players as pl, #__bl_events as ev".(($team_id || $gr_sql)?",#__bl_teams as t, #__bl_players_team as tp":"")
					." WHERE ".(($team_id || $gr_sql)?"tp.player_id=pl.id AND me.t_id=t.id AND t.id=tp.team_id AND tp.season_id = ".$s_id." AND":"")." ".$evcnt." AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id AND md.s_id=".$s_id." AND me.player_id = pl.id  ".($team_id?' AND t.id='.$team_id:'').$gr_sql
					." GROUP BY me.player_id ORDER BY cnt desc LIMIT ".$display_count;
		}else{
			$query = "SELECT ".$rest.",CONCAT(t.first_name,' ',t.last_name) as name,t.nick,'' as t_name, ev.e_img,ev.e_name,t.id,t.def_img"
					." FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md, #__bl_players as t, #__bl_events as ev"
					." WHERE  ".$evcnt." AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id ".($team_id?" AND t.id=".$team_id:"")." AND md.s_id=".$s_id." AND me.player_id = t.id ".$gr_sql
					." GROUP BY me.player_id ORDER BY cnt desc LIMIT ".$display_count;
		}
		$db->setQuery($query);

		$rows = $db->loadObjectList();
		
		return $rows;
	}
	function &getPhoto($player){
		$db =& JFactory::getDBO();
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
	
	function &getTeamName($player ,$params){
		$ssss_id = $params->get( 'sidgid' );
		$ex = explode('|',$ssss_id );
		$s_id = $ex[0];
		$gr_id = $ex[1];
		$cItemId = $params->get('customitemid');
		$Itemid = JRequest::getInt('Itemid');
		if(!$cItemId){
			$cItemId = $Itemid;
		}
		$db =& JFactory::getDBO();
		$query = "SELECT t.id,t.t_name FROM #__bl_teams as t, #__bl_players_team as p ".($s_id?",#__bl_season_teams as pt":"")." WHERE p.team_id=t.id AND p.player_id=".$player->id." ".($s_id?" AND pt.season_id=".$s_id." AND pt.team_id=t.id AND p.season_id=".$s_id:"")." ORDER BY t.t_name";
		$db->setQuery($query);
		$teamzar = $db->loadObjectList();
		$teams = '';
		$mx = 0;
		if(count($teamzar)){
			foreach($teamzar as $tmz){
				$link2 = JRoute::_("index.php?option=com_joomsport&task=team&tid=".$tmz->id."&sid=".$s_id."&Itemid=".$cItemId);
				if($mx){
				$teams .= ",";
				}
				$teams .= '<a href="'.$link2.'">'.$tmz->t_name.'</a> ';
				$mx++;
			}
		}
		if($teams){
			$teams = " (".$teams.")";
		}
		return $teams;
	}
	function getPlName(){
		$db =& JFactory::getDBO();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_name'";
		$db->setQuery($query);
		return $db->loadResult();
	}
}
