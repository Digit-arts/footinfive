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

class venueJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $venue_id = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->venue_id = JRequest::getVar( 'id', 0, '', 'int' );
		
		if(!$this->venue_id){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		
	}

	function getData()
	{
		
		$query = "SELECT * FROM #__bl_venue WHERE id = ".$this->venue_id;
		$this->db->setQuery($query);
		$venue = $this->db->loadObject();
		$this->_lists["venue"] = $venue;
		//title
		$this->_params = $this->JS_PageTitle($venue->v_name);
		
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 5 AND cat_id = ".$this->venue_id;
		$this->db->setQuery($query);
		$photos = $this->db->loadObjectList();
		$this->_lists["photos"] = $photos;
		
		$def_img = '';
		if($venue->v_defimg){
			$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$venue->v_defimg;
			$this->db->setQuery($query);
			$def_img = $this->db->loadResult();
		}else if(isset($photos[0])){
			$def_img = $photos[0]->filename;
		}
		$this->_lists["def_img"] = $def_img;
		
		
		$this->_lists["teams_season"] = $this->teamsToModer();;
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,1);
		
		//social buttons
		$ogimg = '';
		if($this->_lists["def_img"] && is_file('media/bearleague/'.$this->_lists["def_img"])){
			$ogimg = JURI::base().'media/bearleague/'.$this->_lists["def_img"];
		}
		$this->_lists['socbut'] = $this->getSocialButtons('jsbp_venue',$venue->v_name,$ogimg,htmlspecialchars(strip_tags($this->_lists["venue"]->v_descr)));
	}
	
}	