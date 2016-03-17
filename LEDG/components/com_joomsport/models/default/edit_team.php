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

class edit_teamJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $season_id = null;
	var $id = null;
	function __construct()
	{
		parent::__construct();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		
		JArrayHelper::toInteger($cid, array(0));
		if($cid[0])
		{
			$is_id = $cid[0];
			$this->id = $is_id;
		}
	
	}

	function getData()
	{
		$this->_params = $this->JS_PageTitle("");
		//----checking for rights----//
		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		$this->season_id = $s_id;
		if($this->id){
			
			$query = "SELECT COUNT(*) FROM #__bl_teams as t, #__bl_seasons as s, #__bl_season_teams as st, #__bl_tournament as tr"
					." WHERE s.s_id=st.season_id AND st.team_id = t.id AND s.t_id = tr.id AND s.s_id=".$s_id." AND t.id=".$this->id;
			$this->db->setQuery($query);
			
			if(!$this->db->loadResult()){
				
				JError::raiseError( 403, JText::_('Access Forbidden') );
				return; 
			}
		}

		//---------------------------//
		$row 	= new JTableTeams($this->db);
		$row->load($this->id);
		//extra fields
		$this->_lists['ext_fields'] = $this->getAddFields($row->id,1,"team");
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename"
				." FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 2 AND cat_id = ".$row->id."";
		$this->db->setQuery($query);
		$this->_lists['photos'] = $this->db->loadObjectList();
		
		$this->getPlayersT($row->id,$s_id);
		$this->_data = $row;
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
		$this->_lists['ext_fields'] = $this->getBEAdditfields('1',$row->id,$s_id);
	}
	function getPlayersT($id,$s_id){
	
		$query = "SELECT p.id FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_id=p.id AND t.team_id=".$id." AND t.season_id=".$s_id;
		$this->db->setQuery($query);
		$plint = $this->db->loadResultArray();
	
		$query = "SELECT CONCAT(first_name,' ',last_name) as name,id FROM #__bl_players ".(count($plint)?" WHERE id NOT IN (".implode(',',$plint).")":"")." ORDER BY first_name,last_name";
		$this->db->setQuery($query);
		$playerz = $this->db->loadObjectList();
		$is_pl[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 'name' ); 
		$playerz = array_merge($is_pl,$playerz);

		$this->_lists['player'] = JHTML::_('select.genericlist',   $playerz, 'playerz_id', 'class="styled" size="1" id="playerz"', 'id', 'name', 0 );
	
		$query = "SELECT p.id,CONCAT(p.first_name,' ',p.last_name) as name FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_id=p.id AND t.team_id=".$id." AND t.season_id=".$s_id;
		$this->db->setQuery($query);
		$this->_lists['team_players'] = $this->db->loadObjectList();
	}
	function SaveAdmTeam(){
		$msg = '';
		$post		= JRequest::get( 'post' );	
		$post['t_descr'] = JRequest::getVar( 't_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['def_img'] = JRequest::getVar( 'ph_default', 0, 'post', 'int' );
		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );	
		$row 	= new JTableTeams($this->db);
		$istlogo = JRequest::getVar( 'istlogo', 0, 'post', 'int' );
		if(!$istlogo){
			$post['t_emblem'] = '';
		}
		if(isset($_FILES['t_logo']['name']) && $_FILES['t_logo']['tmp_name'] != '' && isset($_FILES['t_logo']['tmp_name'])){
			$bl_filename = strtolower($_FILES['t_logo']['name']);
			$ext = pathinfo($_FILES['t_logo']['name']);
			$bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
			$bl_filename = str_replace(" ","",$bl_filename);
			 if($this->uploadFile($_FILES['t_logo']['tmp_name'], $bl_filename)){
			 	$post['t_emblem'] = $bl_filename;
			 }
		}

		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}

		$pzt = 1;
		if(!$row->id){
			$pzt = 0;
		}
	
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		if(!$pzt){
			$query = "INSERT INTO #__bl_season_teams(season_id,team_id) VALUES(".$s_id.",".$row->id.")";
				$this->db->setQuery($query);
				$this->db->query();
		}
	
		$query = "DELETE FROM #__bl_assign_photos WHERE cat_type = 2 AND cat_id = ".$row->id;
		$this->db->setQuery($query);	
		$this->db->query();
	
		if(isset($_POST['photos_id']) && count($_POST['photos_id'])){
			for($i = 0; $i < count($_POST['photos_id']); $i++){
				$photo_id = intval($_POST['photos_id'][$i]);
				$photo_name = addslashes(strval($_POST['ph_names'][$i]));
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$photo_id.",".$row->id.",2)";
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
				if (!$img1->store()) {
					JError::raiseError(500, $img1->getError() );
				}
				$img1->checkin();

				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",2)";
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
				if (!$img2->store()) {
					JError::raiseError(500, $img2->getError() );
				}
				$img2->checkin();

				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img2->id.",".$row->id.",2)";
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
				$fld = ($_POST['extra_ftype'][$p] == 2)?'fvalue_text':'fvalue';
				$query = "INSERT INTO #__bl_extra_values(f_id,uid,`".$fld."`,season_id) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".$_POST['extraf'][$p]."',".$s_id.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		//-------Players----//
		if($s_id && $row->id){
			$query = "DELETE FROM #__bl_players_team WHERE team_id=".$row->id." AND season_id=".$s_id;
			$this->db->setQuery($query);
			$this->db->query();
			if(isset($_POST['teampl']) && count($_POST['teampl'])){
				for($p=0;$p<count($_POST['teampl']);$p++){
					$query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id) VALUES(".$row->id.",".intval($_POST['teampl'][$p]).",".$s_id.")";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}	
		}
		$this->id = $row->id;
		$this->season_id = $s_id;
	}
	function delAdmTeam(){
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `#__bl_teams` WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			
			$query = "DELETE FROM `#__bl_match_events` WHERE t_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			
			
			$query = "SELECT s.s_id FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.t_id=t.id AND t_single = 0";
			$this->db->setQuery($query);
			$sid = $this->db->loadResultArray();
			if(count($sid)){
				$sids = implode(',',$sid);
				$query = "SELECT id FROM #__bl_matchday WHERE s_id IN (".$sids.")";
				$this->db->setQuery($query);
				$mdid = $this->db->loadResultArray();
				
				if(count($mdid)){
					$mdids = implode(',',$mdid);
						$query = "DELETE FROM `#__bl_match` WHERE m_id IN (".$mdids.") AND (team1_id IN (".$cids.") OR team2_id IN (".$cids."))";
						$this->db->setQuery($query);
						$this->db->query();
				}
			}	
			
		}
	}
}