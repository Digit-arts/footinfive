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

class moder_editJSModel extends JSPRO_Models
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
		
		$query = "SELECT * FROM #__users ORDER BY username";
		$this->db->setQuery($query);
		$moder = $this->db->loadObjectList();
		
		$this->_lists['moder'] = JHTML::_('select.genericlist',   $moder, 'moder_id', 'class="inputbox" size="1"', 'id', 'username', $is_id );
		
		
		$query = "SELECT t.* FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid=".$is_id." ORDER BY t.t_name";
		
		$this->db->setQuery($query);
		
		$teams_season_ids = $this->db->loadResultArray();
			
		$query = "SELECT * FROM #__bl_teams ".(count($teams_season_ids)?"WHERE id NOT IN (".implode(',',$teams_season_ids).")":"")." ORDER BY t_name";
		
		//$query = "SELECT * FROM #__bl_teams ORDER BY t_name";
		$this->db->setQuery($query);
		$teams = $this->db->loadObjectList();
		$this->_lists['teams'] = JHTML::_('select.genericlist',   $teams, 'teams_id', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		
		$query = "SELECT t.* FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid=".$is_id." ORDER BY t.t_name";
		$this->db->setQuery($query);
		$teams_season = $this->db->loadObjectList();
		
		$this->_lists['teams2'] = JHTML::_('select.genericlist',   $teams_season, 'teams_season[]', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		
		$this->_data = $moder;
		
	}
	
	public function deleteModer($cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$this->db->setQuery("DELETE FROM #__bl_moders WHERE uid IN (".$cids.")");
			$this->db->query();
		}
	}
	
	public function saveModer(){
		
		$moder_id = intval(JRequest::getVar( 'moder_id', '0', 'post', 'int' ));
	
		$query = "DELETE FROM #__bl_moders WHERE uid = ".$moder_id;
		$this->db->setQuery($query);
		$this->db->query();
		$teams_season 		= JRequest::getVar( 'teams_season', array(0), '', 'array' );
		JArrayHelper::toInteger($teams_season, array(0));
		if(count($teams_season)){
			foreach($teams_season as $teams){
				$query = "INSERT INTO #__bl_moders(uid,tid) VALUES(".$moder_id.",".$teams.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		$this->_id = $moder_id;
	}
	
}