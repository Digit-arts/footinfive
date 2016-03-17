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

class moderedit_umatchJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $_data = null;
	var $sid = null;
	var $_user = null;
	var $id = null;
	var $m_id = null;
	
	function __construct()
	{
		parent::__construct();
		$this->_user	=& JFactory::getUser();
		if ( $this->_user->get('guest')) {

			$return_url = $_SERVER['REQUEST_URI'];
			$return_url = base64_encode($return_url);
			if(getVer() >= '1.6'){
				$uopt = "com_users";
			}else{
				$uopt = "com_user";
			}
			$return	= 'index.php?option='.$uopt.'&view=login&return='.$return_url;

			// Redirect to a login form
			$mainframe->redirect( $return, JText::_('BLFA_MSGLOGIN') );
			
		}
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if($cid[0])
		{
			$this->id = $cid[0];
		}else{
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}
	}

	function getData()
	{
		
		$row 	= new JTableMatch($this->db);

		$row->load($this->id);
		$lists = array();	
		$query = "SELECT m_name FROM #__bl_matchday  WHERE id = ".$row->m_id;
		$this->db->setQuery($query);
		$this->_lists['mday'] = $this->db->loadResult();
		
		$query = "SELECT s_id FROM #__bl_matchday  WHERE id = ".$row->m_id;
		$this->db->setQuery($query);
		$sid = $this->db->loadResult();
		$js = 'onchange="enblnp();"';
		$this->_lists['new_points'] 	= JHTML::_('select.booleanlist',  'new_points', 'class="inputbox" '.$js, $row->new_points );
		
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_type,t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($sid)." AND s.t_id = t.id";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObject();

		$this->_lists['t_type'] = $tourn->t_type;
		
		$query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team FROM #__bl_match as m, #__bl_teams as t, #__bl_teams as t2  WHERE m.id = ".$row->id." AND t.id = m.team1_id AND t2.id = m.team2_id  ORDER BY m.id";
		$this->db->setQuery($query);
		$match = $this->db->loadObjectList();
		$is_event = array();
		$query = "SELECT * FROM #__bl_events WHERE player_event = '1' ORDER BY e_name";
		$this->db->setQuery($query);
		$events = $this->db->loadObjectList();
		$is_event[] = JHTML::_('select.option',  0, JText::_('BLFA_SELEVENT'), 'id', 'e_name' ); 
		$ev_pl = array_merge($is_event,$events);
		$this->_lists['events'] = JHTML::_('select.genericlist',   $ev_pl, 'event_id', 'class="inputbox" size="1"', 'id', 'e_name', 0);
		$is_event = array();
		$query = "SELECT * FROM #__bl_events WHERE player_event = '0' ORDER BY e_name";
		$this->db->setQuery($query);
		$events = $this->db->loadObjectList();
		$is_event[] = JHTML::_('select.option',  0, JText::_('BLFA_SELEVENT'), 'id', 'e_name' ); 
		$ev_pl = array_merge($is_event,$events);
		$this->_lists['team_events'] = JHTML::_('select.genericlist',   $ev_pl, 'tevent_id', 'class="inputbox" size="1"', 'id', 'e_name', 0);
		$query = "SELECT CONCAT(first_name,' ',last_name) FROM #__bl_players WHERE id= ".$row->team1_id;
		$this->db->setQuery($query);
		$team_1 = $this->db->loadResult();
		$query = "SELECT CONCAT(first_name,' ',last_name) FROM #__bl_players WHERE id= ".$row->team2_id;
		$this->db->setQuery($query);
		$team_2 = $this->db->loadResult();
		$this->_lists['teams1'] = $team_1;
		$this->_lists['teams2'] = $team_2;

		$is_player[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 'p_name' ); 
		$is_player[] = JHTML::_('select.option',  $row->team1_id,$team_1, 'id', 'p_name' ); 
		$is_player[] = JHTML::_('select.option',  $row->team2_id,$team_2, 'id', 'p_name' ); 
		//$is2_player[] = '</optgroup>';$lists['players']
		$ev_pl = array_merge($is_player);
		$this->_lists['players'] = JHTML::_('select.genericlist',   $ev_pl, 'playerz_id', 'class="inputbox" size="1"', 'id', 'p_name', 0);
		
		$this->_lists['m_played'] 		= JHTML::_('select.booleanlist',  'm_played', 'class="inputbox"', $row->m_played );
		$query = "SELECT me.*,ev.e_name,CONCAT(p.first_name,' ',p.last_name) as p_name FROM  #__bl_events as ev , #__bl_players as p, #__bl_match_events as me WHERE me.player_id = p.id AND ev.player_event = '1' AND  me.e_id = ev.id AND me.match_id = ".$row->id." ORDER BY CAST(me.minutes AS UNSIGNED),p.first_name,p.last_name";
		$this->db->setQuery($query);
		//echo mysql_error();die();
		$this->_lists['m_events'] = $this->db->loadObjectList();
		$query = "SELECT me.*,ev.e_name,p.t_name as p_name,p.id as pid FROM  #__bl_events as ev, #__bl_teams as p , #__bl_match_events as me WHERE me.t_id = p.id AND ev.player_event = '0' AND  me.e_id = ev.id AND me.match_id = ".$row->id." ORDER BY p.t_name";
		$this->db->setQuery($query);
		//echo mysql_error();die();
		$this->_lists['t_events'] = $this->db->loadObjectList();
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 3 AND cat_id = ".$row->id."";
		$this->db->setQuery($query);
		$this->_lists['photos'] = $this->db->loadObjectList();
		
		$this->_data = $row;
		///-----EXTRAFIELDS---//
		$query = "SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$row->id." WHERE ef.published=1 AND ef.type='2' ORDER BY ef.ordering";
		$this->db->setQuery($query);
		$this->_lists['ext_fields'] = $this->db->loadObjectList();
		$mj=0;
		if(isset($this->_lists['ext_fields'])){
			foreach ($this->_lists['ext_fields'] as $extr){
				if($extr->field_type == '3'){
					$tmp_arr = array();
					$query = "SELECT * FROM #__bl_extra_select WHERE fid=".$extr->id;
					$this->db->setQuery($query);
					$selvals = $this->db->loadObjectList();
					if(count($selvals)){
						$tmp_arr[] = JHTML::_('select.option',  0, JText::_('BLBE_SELECTVALUE'), 'id', 'sel_value' ); 
						$selvals = array_merge($tmp_arr,$selvals);
						$this->_lists['ext_fields'][$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf[]', 'class="inputbox" size="1"', 'id', 'sel_value', $extr->fvalue );
					}
				}
				if($extr->field_type == '1'){
					$this->_lists['ext_fields'][$mj]->selvals	= JHTML::_('select.booleanlist',  'extraf[]', 'class="inputbox"', $extr->fvalue );
				}
				$mj++;
			}
		}
		///--------MAPS--------------///
		$query = "SELECT m.*,mp.m_score1,mp.m_score2 FROM #__bl_seas_maps as sm, #__bl_maps as m LEFT JOIN #__bl_mapscore as mp ON m.id=mp.map_id AND mp.m_id=".$row->id." WHERE m.id=sm.map_id AND sm.season_id=".$sid." ORDER BY m.id";
		$this->db->setQuery($query);
		$this->_lists['maps'] = $this->db->loadObjectList();
		
		
		//venue
		$is_venue[] = JHTML::_('select.option',  0, JText::_('BLFA_SELVENUE'), 'id', 'v_name' ); 
		$query = "SELECT * FROM #__bl_venue ORDER BY v_name";
		$this->db->setQuery($query);
		$venue = $this->db->loadObjectList();
		if(count($venue)){
			$is_venue = array_merge($is_venue,$venue);
		}
		$this->_lists['venue'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id', 'class="inputbox" size="1"', 'id', 'v_name', $row->venue_id);

		
		$this->_lists["teams_season"] = $this->teamsToModer();;
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
	}
	function SaveUMatch(){
		$post		= JRequest::get( 'post' );
	
		$post['match_descr'] = JRequest::getVar( 'match_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		unset($post['m_id']);
		$row 	= new JTableMatch($this->db);
		$row->load($_POST['id']);
		if (!$row->bind( $post )) {
	
			JError::raiseError(500, $row->getError() );
	
		}
	
		if (!$row->check()) {
	
			JError::raiseError(500, $row->getError() );
	
		}
		if (!$row->store()) {
	
			JError::raiseError(500, $row->getError() );
	
		}
		$this->m_id = $row->m_id;
		$query = "SELECT s_id FROM #__bl_matchday as md, #__bl_match as m  WHERE md.id=m.m_id AND m.id = ".$row->id;
		$this->db->setQuery($query);
		$season_id = $this->db->loadResult();

		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_type,t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($season_id)." AND s.t_id = t.id ORDER BY t.name, s.s_name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();

		$lt_type = $tourn[0]->t_type;

		if($lt_type == 1){
			
			$team_win = ($row->score1 > $row->score2)?$row->team1_id:$row->team2_id;
			$team_loose = ($row->score1 > $row->score2)?$row->team2_id:$row->team1_id;
			
			$query = "UPDATE #__bl_match SET team1_id=".$team_win."  WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND team1_id = ".$team_loose;
			$this->db->setQuery($query);
			$this->db->query();
			$query = "UPDATE #__bl_match SET team2_id=".$team_win."  WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND team2_id = ".$team_loose;
			$this->db->setQuery($query);
			$this->db->query();	
				
			if($row->m_played == 0){
				$query = "UPDATE #__bl_match SET m_played = '0' WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND (team1_id = ".$row->team1_id." OR team2_id = ".$row->team1_id." OR team1_id = ".$row->team2_id." OR team2_id = ".$row->team2_id.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
			
		}

		$me_arr = array();
		if(isset($_POST['new_eventid']) && count($_POST['new_eventid'])){
			for ($i=0; $i< count($_POST['new_eventid']); $i++){
				if(!intval($_POST['em_id'][$i])){
		
					$new_event = $_POST['new_eventid'][$i];
					
					
					$query = "SELECT team_id FROM #__bl_players WHERE id=".intval($_POST['new_player'][$i]);
					$this->db->setQuery($query);
					$teamid = $this->db->loadResult();
					
		
					$query = "INSERT INTO #__bl_match_events(e_id,player_id,match_id,ecount,minutes,t_id) VALUES(".$new_event.",".$_POST['new_player'][$i].",".$row->id.",".intval($_POST['e_countval'][$i]).",".intval($_POST['e_minuteval'][$i]).",".intval($teamid).")";
					$this->db->setQuery($query);
					$this->db->query();
					
					$me_arr[] = $this->db->insertid();
				}else{
					$query = "SELECT * FROM #__bl_match_events WHERE id=".intval($_POST['em_id'][$i]);
					$this->db->setQuery($query);
					$event_bl = $this->db->loadObjectList();
					
					if(count($event_bl)){
						$query = "UPDATE #__bl_match_events SET minutes=".intval($_POST['e_minuteval'][$i]).", ecount=".intval($_POST['e_countval'][$i])." WHERE id=".intval($_POST['em_id'][$i]);
						$this->db->setQuery($query);
						$this->db->query();
						
						$me_arr[] = intval($_POST['em_id'][$i]);
					}
				}
			}
			
		
		}
		$query = "DELETE FROM #__bl_match_events WHERE match_id = ".$row->id;
		if(count($me_arr)){ $query.=" AND id NOT IN (".implode(',',$me_arr).")";}
	 
		$this->db->setQuery($query);
		$this->db->query();
		
		$query = "DELETE FROM #__bl_assign_photos WHERE cat_type = 3 AND cat_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		if(isset($_POST['photos_id']) && count($_POST['photos_id'])){
			for($i = 0; $i < count($_POST['photos_id']); $i++){
				$photo_id = intval($_POST['photos_id'][$i]);
				$photo_name = addslashes(strval($_POST['ph_names'][$i]));
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$photo_id.",".$row->id.",3)";
				$this->db->setQuery($query);
				$this->db->query();
				$query = "UPDATE #__bl_photos SET ph_name = '".($photo_name)."' WHERE id = ".$photo_id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		if(isset($_FILES['player_photo_1']['name']) && $_FILES['player_photo_1']['tmp_name'] != '' && isset($_FILES['player_photo_1']['tmp_name'])){
			$bl_filename = strtolower($_FILES['player_photo_1']['name']);
			$ext = pathinfo($_FILES['player_photo_1']['name']);
			$bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
			$bl_filename = str_replace(" ","",$bl_filename);
			//echo $bl_filename;
			 if($this->uploadFile($_FILES['player_photo_1']['tmp_name'], $bl_filename)){
				$post1['ph_filename'] = $bl_filename;
				$img1 = new JTablePhotos($this->db);
				$img1->id = 0;
				if (!$img1->bind( $post1 )) {
					JError::raiseError(500, $img1->getError() );
				}
				if (!$img1->check()) {
					JError::raiseError(500, $img1->getError() );
				}
				// if new item order last in appropriate group
				if (!$img1->store()) {
					JError::raiseError(500, $img1->getError() );
				}
				$img1->checkin();
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",3)";
				$this->db->setQuery($query);
				$this->db->query();
			 }
		}
		if(isset($_FILES['player_photo_2']['name']) && $_FILES['player_photo_2']['tmp_name'] != ''  && isset($_FILES['player_photo_2']['tmp_name'])){
			 $bl_filename = strtolower($_FILES['player_photo_2']['name']);
			$ext = pathinfo($_FILES['player_photo_2']['name']);
			$bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
			$bl_filename = str_replace(" ","",$bl_filename);
			 if($this->uploadFile($_FILES['player_photo_2']['tmp_name'], $bl_filename)){
				$post2['ph_filename'] = $bl_filename;
				$img2 = new JTablePhotos($this->db);
				$img2->id = 0;
				if (!$img2->bind( $post2 )) {
					JError::raiseError(500, $img2->getError() );
				}
				if (!$img2->check()) {
					JError::raiseError(500, $img2->getError() );
				}
				// if new item order last in appropriate group
				
				if (!$img2->store()) {
					JError::raiseError(500, $img2->getError() );
				}
				$img2->checkin();
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img2->id.",".$row->id.",3)";
				$this->db->setQuery($query);
				$this->db->query();
			 }
		}
		
		//-------extra fields-----------//
		if(isset($_POST['extraf']) && count($_POST['extraf'])){
			foreach($_POST['extraf'] as $p=>$dummy){
				$query = "DELETE FROM #__bl_extra_values WHERE f_id = ".$_POST['extra_id'][$p]." AND uid = ".$row->id;
				$this->db->setQuery($query);
				$this->db->query();
				if($_POST['extra_ftype'][$p] == '2'){
					$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".addslashes($_POST['extraf'][$p])."')";
				}else{
					$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".$_POST['extraf'][$p]."')";
				}
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	
	
		$query = "DELETE  FROM #__bl_mapscore WHERE m_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		if(isset($_POST['mapid']) && count($_POST['mapid'])){
			for ($i=0; $i< count($_POST['mapid']); $i++){
				$new_event = $_POST['mapid'][$i];
				$query = "INSERT INTO #__bl_mapscore(m_id,map_id,m_score1,m_score2) VALUES(".$row->id.",".$new_event.",".$_POST['t1map'][$i].",".intval($_POST['t2map'][$i]).")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		$this->id = $row->id;
	}
	
}