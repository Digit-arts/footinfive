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

class getparcipJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;

	function __construct()
	{
		parent::__construct();
	
		$this->getData();
	}

	function getData()
	{
		$s_id = JRequest::getVar( 'sid', 0, 'GET', 'int' );
		if($s_id){
			$query = "SELECT t.t_single FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE s.s_id=".$s_id;
			$this->db->setQuery($query);
			$t_single = $this->db->loadResult();
		}else{
			$t_single=0;
		}
		if($t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
		}else{
			if($s_id){
				$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
			}else{
				$query = "SELECT * FROM #__bl_teams as t ORDER BY t_name";
			}
		}
		$this->db->setQuery($query);
		$team = $this->db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, ($t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' );
		if(count($team)){
			$is_team = array_merge($is_team,$team);
		}
		echo JHTML::_('select.genericlist',   $is_team, 'jform[params][team_id]', 'class="inputbox" size="1"', 'id', 't_name', 0 );
		
		die();
		
		
	}

	
	
}