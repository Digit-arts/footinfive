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

class venue_editJSModel extends JSPRO_Models
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
		
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$is_id = $cid[0];
		
		$row 	= new JTableVenue($this->db);
		$row->load($is_id);
		
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 5 AND cat_id = ".$row->id."";
		$this->db->setQuery($query);
		$this->_lists['photos'] = $this->db->loadObjectList();
		
		$this->_data = $row;
		
	}

	public function saveVenue(){
		
		$post		= JRequest::get( 'post' );
		$post['v_descr'] = JRequest::getVar( 'v_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['v_defimg'] = JRequest::getVar( 'ph_default', 0, 'post', 'int' );
		
		$row 	= new JTableVenue($this->db);

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
		$query = "DELETE FROM #__bl_assign_photos WHERE cat_type = 5 AND cat_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		if(isset($_POST['photos_id']) && count($_POST['photos_id'])){
			for($i = 0; $i < count($_POST['photos_id']); $i++){
				$photo_id = intval($_POST['photos_id'][$i]);
				$photo_name = addslashes(strval($_POST['ph_names'][$i]));
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$photo_id.",".$row->id.",5)";
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
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",5)";
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
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img2->id.",".$row->id.",5)";
				$this->db->setQuery($query);
				$this->db->query();
			 }
		}
		
		$this->_id = $row->id;
	}
	
}