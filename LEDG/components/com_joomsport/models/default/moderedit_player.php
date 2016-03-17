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

class moderedit_playerJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $id = null;
	var $tid = null;
	function __construct()
	{
		parent::__construct();
		$this->tid = JRequest::getVar( 'tid', 0, '', 'int' );
		
	
	}

	function getData()
	{
		$this->getGlobFilters();
		$this->_params = $this->JS_PageTitle("");
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$is_id = 0;
		JArrayHelper::toInteger($cid, array(0));
		if($cid[0])
		{
			$is_id = $cid[0];
			$this->id = $is_id;
		}
		//----checking for rights----//
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='moder_addplayer'";
		$this->db->setQuery($query);
		$this->_lists['moder_addplayer'] = $this->db->loadResult();
		if(!$this->_lists['moder_addplayer']){
				
			JError::raiseError( 403, JText::_('Access Forbidden') );

			return; 
		}
		
		$row 	= new JTablePlayer($this->db);
		$row->load($is_id);
		$this->getCanMore($is_id);
		$this->getPCountry($row->country_id);
		$this->getUsers($row->usr_id);
		//extra fields
		$this->_lists['ext_fields'] = $this->getAddFields($row->id,0,"player");
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename"
				." FROM #__bl_assign_photos as ap, #__bl_photos as p"
				." WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$row->id."";
		$this->db->setQuery($query);
		$this->_lists['photos'] = $this->db->loadObjectList();
		
		
		$this->_data = $row;
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
		$this->_lists['ext_fields'] = $this->getBEAdditfields('0',$row->id);
	}
	function getCanMore($is_id){
		$user	=& JFactory::getUser();
		$canmore = $is_id?true:false;
		$query = "SELECT COUNT(*) FROM #__bl_players WHERE created_by=".$user->id;
		$this->db->setQuery($query);
		$curcap = $this->db->loadResult();
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='players_per_account'";
		$this->db->setQuery($query);
		$teams_per_account = $this->db->loadResult();
		
		if($curcap < $teams_per_account){
			$canmore = true;
		}
		$this->_lists["canmore"] = $canmore;
	}
	function getPCountry($country_id){
		$query = "SELECT * FROM #__bl_countries ORDER BY country";
		$this->db->setQuery($query);
		$country = $this->db->loadObjectList();
		
		$cntr[] = JHTML::_('select.option',  0, JText::_('BLFA_SELCOUNTRY'), 'id', 'country' ); 
		$countries = array_merge($cntr,$country);
		$this->_lists['country'] = JHTML::_('select.genericlist',   $countries, 'country_id', 'class="styled-long" size="1"', 'id', 'country', $country_id );
		
	}
	function getUsers($usr_id){
		$query = "SELECT usr_id FROM #__bl_players WHERE usr_id != ".$usr_id;
		$this->db->setQuery($query);
		$ex_users = $this->db->loadResultArray();
		
		
		$query = "SELECT * FROM #__users ".(count($ex_users)?"WHERE id NOT IN (".implode(',',$ex_users).")":"")." ORDER BY username";
		$this->db->setQuery($query);
		$f_users = $this->db->loadObjectList();
		$is_player[] = JHTML::_('select.option',  0, JText::_('BLFA_SELUSR'), 'id', 'username' ); 
		$f_users = array_merge($is_player,$f_users);
		$this->_lists['usrid'] = JHTML::_('select.genericlist',   $f_users, 'usr_id', 'class="styled-long" size="1"', 'id', 'username', $usr_id );
	}
	function savModerPlayer(){
		$user	=& JFactory::getUser();

		$post		= JRequest::get( 'post' );
		$post['about'] = JRequest::getVar( 'about', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['def_img'] = JRequest::getVar( 'ph_default', 0, 'post', 'int' ); 
		$tid 		= JRequest::getVar( 'tid', 0, '', 'int' );
		$row 	= new JTablePlayer($this->db);
		$row->created_by = $user->id;
		
		$canmore = $post['id']?true:false;
		$query = "SELECT COUNT(*) FROM #__bl_players WHERE created_by=".$user->id;
		$this->db->setQuery($query);
		$curcap = $this->db->loadResult();
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='players_per_account'";
		$this->db->setQuery($query);
		$teams_per_account = $this->db->loadResult();
		
		if($curcap < $teams_per_account){
			$canmore = true;
		}
		if(!$canmore){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}
		
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
		$this->id = $row->id;
		$this->tid = $tid;
	}
	function delAdmPlayer(){
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		
		if(count($cid)){
	
			$cids = implode(',',$cid);
	
			$this->db->setQuery("DELETE FROM #__bl_players WHERE id IN (".$cids.")");
	
			$this->db->query();
	
		}
	}
}