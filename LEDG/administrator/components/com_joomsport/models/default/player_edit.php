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

class player_editJSModel extends JSPRO_Models
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
		$row 	= new JTablePlayer($this->db);
		$row->load($is_id);
		
		$seasf_id	= $mainframe->getUserStateFromRequest('com_joomsport.seasf_id', 'seasf_id', 0, 'int' );
		$query = "SELECT CONCAT(t.name,' ',s.s_name) as name,s.s_id as id FROM #__bl_seasons as s, #__bl_tournament as t WHERE s.t_id = t.id AND t.t_single='0' ORDER BY s.s_id";
		
		$this->db->setQuery($query);
		$seaspl = $this->db->LoadObjectList();
		$javascripts = 'onchange="javascript:document.adminForm.submit();"';
		$this->_lists['seasf'] = JHTML::_('select.genericlist',   $seaspl, 'seasf_id', 'class="inputbox" size="1" id="seasf_id" '.$javascripts, 'id', 'name', $seasf_id );
		
		if(count($seaspl) && !$seasf_id){
			$seasf_id = $seaspl[0]->id;
		}
		
		///country
		$this->getPCountry($row);
		
		$query = "SELECT usr_id FROM #__bl_players WHERE usr_id != ".$row->usr_id;
		$this->db->setQuery($query);
		$ex_users = $this->db->loadResultArray();
		
		
		$query = "SELECT * FROM #__users ".(count($ex_users)?"WHERE id NOT IN (".implode(',',$ex_users).")":"")." ORDER BY username";
		$this->db->setQuery($query);
		$f_users = $this->db->loadObjectList();
		$is_player[] = JHTML::_('select.option',  0, JText::_('BLBE_SELUSR'), 'id', 'username' ); 
		$f_users = array_merge($is_player,$f_users);
		$this->_lists['usrid'] = JHTML::_('select.genericlist',   $f_users, 'usr_id', 'class="inputbox" size="1"', 'id', 'username', $row->usr_id );
		
		if(!$row->team_id && $row->id){
			$query = "SELECT team_id FROM #__bl_players_team WHERE player_join='0' AND player_id=".$row->id;
			$this->db->setQuery($query);
			$rrr = $this->db->loadResultArray();
			if(count($rrr)){
				$row->team_id = $rrr[0];
			}
		}
		
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$row->id."";
		$this->db->setQuery($query);
		$this->_lists['photos'] = $this->db->loadObjectList();
		
		
		$query = "SELECT st.*,CONCAT(t.name,' ',s.s_name) as name FROM  #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id, #__bl_season_players as st WHERE s.s_id = st.season_id AND st.player_id=".$row->id;
		$this->db->setQuery($query);
		$this->_lists["bonuses"] = $this->db->loadObjectList();
		///teamss
		
		$this->getPTeams($row,$seasf_id);	
		
		///-----EXTRAFIELDS---//
		$this->_lists['ext_fields'] = $this->getAdditfields(0,$row->id);
		$this->_data = $row;
		
	}
	
	protected function getPTeams($row,$seasf_id){
		$query = "SELECT DISTINCT(team_id) FROM #__bl_players_team WHERE player_id=".$row->id." AND season_id=".$seasf_id;
		$this->db->setQuery($query);
		$plars = $this->db->loadResultArray();
		
		$query = "SELECT * FROM #__bl_teams ".(count($plars)?"WHERE id NOT IN (".implode(',',$plars).")":"")." ORDER BY t_name";
		$this->db->setQuery($query);
		$f_teams= $this->db->loadObjectList();
		$this->_lists['allteams'] = @JHTML::_('select.genericlist',   $f_teams, 'allteams', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		$query = "SELECT t.* FROM #__bl_players_team as p,#__bl_teams as t WHERE p.player_join='0' AND t.id=p.team_id AND p.player_id=".$row->id." AND p.season_id=".$seasf_id;
		$this->db->setQuery($query);
		$f_inteams = $this->db->loadObjectList();
		$this->_lists['in_teams'] = @JHTML::_('select.genericlist',   $f_inteams, 'in_teams[]', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		
	}
	
	
	protected function getPCountry($row){
		$query = "SELECT * FROM #__bl_countries ORDER BY country";
		$this->db->setQuery($query);
		$country = $this->db->loadObjectList();
		
		$cntr[] = JHTML::_('select.option',  0, JText::_('BLBE_SELCOUNTRY'), 'id', 'country' ); 
		$countries = array_merge($cntr,$country);
		$this->_lists['country'] = JHTML::_('select.genericlist',   $countries, 'country_id', 'class="inputbox" size="1"', 'id', 'country', $row->country_id );
		
	}
	
	public function savePlayer(){
		$post		= JRequest::get( 'post' );
		$post['about'] = JRequest::getVar( 'about', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['def_img'] = JRequest::getVar( 'ph_default', 0, 'post', 'int' ); 
		$seasf_id	= JRequest::getVar( 'seasf_id', 0, 'post', 'int' );
		$usr_admins 		= JRequest::getVar( 'in_teams', array(0), '', 'array' );
		JArrayHelper::toInteger($usr_admins, array(0));
		if(count($usr_admins)){
			$post['team_id'] = $usr_admins[0];
		}else{
			$post['team_id'] = 0;
		}
		
		$row 	= new JTablePlayer($this->db);
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
		$query = "DELETE FROM #__bl_assign_photos WHERE cat_type = 1 AND cat_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		if(isset($_POST['photos_id']) && count($_POST['photos_id'])){
			for($i = 0; $i < count($_POST['photos_id']); $i++){
				$photo_id = intval($_POST['photos_id'][$i]);
				$photo_name = addslashes(strval($_POST['ph_names'][$i]));
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$photo_id.",".$row->id.",1)";
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
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",1)";
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
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img2->id.",".$row->id.",1)";
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
		//-------Bonuses points----//
		if(isset($_POST['sids']) && count($_POST['sids'])){
			for($p=0;$p<count($_POST['sids']);$p++){
				$query = "UPDATE #__bl_season_players SET bonus_point = ".intval($_POST['bonuses'][$p])." WHERE season_id=".$_POST['sids'][$p]." AND player_id=".$row->id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}	
		
		if($seasf_id){
			$query = "DELETE FROM #__bl_players_team WHERE player_id = ".$row->id." AND season_id=".$seasf_id;
			$this->db->setQuery($query);
			$this->db->query();
			
			
			if(count($usr_admins)){
				foreach($usr_admins as $usrz){
					$query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id) VALUES(".$usrz.",".$row->id.",".$seasf_id.")";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}	
		}
		$this->_id = $row->id;
	}
	
}