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

class configJSModel extends JSPRO_Models
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
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='date_format'";
		$this->db->setQuery($query);
		$this->_lists['date_format'] = $this->db->loadResult();
		
		$is_data = array();
		
		$is_data[] = JHTML::_('select.option', "%d-%m-%Y %H:%M", "d-m-Y H:M", 'id', 'name' ); 
		 $is_data[] = JHTML::_('select.option', "%m-%d-%Y %I:%M %p", "m-d-Y I:M p", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%m %B, %Y %H:%M", "m B, Y H:M", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%m %B, %Y %I:%H %p", "m B, Y I:H p", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%d-%m-%Y", "d-m-Y", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%A %d %B, %Y  %H:%M", "A d B, Y  H:M", 'id', 'name' ); 
		$this->_lists['data_sel'] = JHTML::_('select.genericlist',   $is_data, 'date_format', 'class="inputbox" size="1"', 'id', 'name', $this->_lists['date_format'] );
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='yteam_color'";
		$this->db->setQuery($query);
		$this->_lists['yteam_color'] = $this->db->loadResult();
		
		$query = "SELECT * FROM #__bl_extra_filds WHERE type='0' ORDER BY ordering";
		$this->db->setQuery($query);
		$this->_lists['adf_player'] = $this->db->loadObjectList();
		
		$query = "SELECT * FROM #__bl_extra_filds WHERE type='1' ORDER BY ordering";
		$this->db->setQuery($query);
		$this->_lists['adf_team'] = $this->db->loadObjectList();
		
		//Player Country registration
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='country_reg'";
		$this->db->setQuery($query);
		$this->_lists['country_reg'] = $this->db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='country_reg_rq'";
		$this->db->setQuery($query);
		$this->_lists['country_reg_rq'] = $this->db->loadResult();
		//Nick registration
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='nick_reg'";
		$this->db->setQuery($query);
		$this->_lists['nick_reg'] = $this->db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='nick_reg_rq'";
		$this->db->setQuery($query);
		$this->_lists['nick_reg_rq'] = $this->db->loadResult();
		//Match comments
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='mcomments'";
		$this->db->setQuery($query);
		$this->_lists['mcomments'] = $this->db->loadResult();
		//Player registration
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_reg'";
		$this->db->setQuery($query);
		$this->_lists['player_reg'] = $this->db->loadResult();
		//team registration
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='team_reg'";
		$this->db->setQuery($query);
		$this->_lists['team_reg'] = $this->db->loadResult();
		
		//
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='moder_addplayer'";
		$this->db->setQuery($query);
		$this->_lists['moder_addplayer'] = $this->db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='pllist_order'";
		$this->db->setQuery($query);
		$pllist_order = $this->db->loadResult();
		
		$query = "SELECT name, CONCAT(id,'_1') as id FROM #__bl_extra_filds WHERE type='0' AND (field_type = 0 OR field_type = 3) ORDER BY ordering";
		$this->db->setQuery($query);
		$adf = $this->db->loadObjectList();
		$alltmp[] = JHTML::_('select.option',0,JTEXT::_('Name'),'id','name');
		if(count($adf)){
			$alltmp = array_merge($alltmp,$adf);
		}
		$query = "SELECT CONCAT(ev.id,'_2') as id,ev.e_name as name FROM #__bl_events as ev WHERE ev.player_event = 1  ORDER BY ev.e_name";
		$this->db->setQuery($query);
		$events_cd = $this->db->loadObjectList();
		
		if($events_cd){
			$alltmp = array_merge($alltmp,$events_cd);
		}
		
		$this->_lists['pllist_order'] = JHTML::_('select.genericlist',   $alltmp, 'pllist_order', 'class="inputbox" size="1"', 'id', 'name', $pllist_order );
		
		//width logo
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='teamlogo_height'";
		$this->db->setQuery($query);
		$this->_lists['teamlogo_height'] = $this->db->loadResult();
		
		//account limits
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='teams_per_account'";
		$this->db->setQuery($query);
		$this->_lists['teams_per_account'] = $this->db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='players_per_account'";
		$this->db->setQuery($query);
		$this->_lists['players_per_account'] = $this->db->loadResult();
		
		//venue
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='unbl_venue'";
		$this->db->setQuery($query);
		$this->_lists['unbl_venue'] = $this->db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='cal_venue'";
		$this->db->setQuery($query);
		$this->_lists['cal_venue'] = $this->db->loadResult();
		
		//played matches
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='played_matches'";
		$this->db->setQuery($query);
		$this->_lists['played_matches'] = $this->db->loadResult();
		//display name - nick
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_name'";
		$this->db->setQuery($query);
		$player_name = $this->db->loadResult();
		
		$is_data = array();
		
		$is_data[] = JHTML::_('select.option', "0", JText::_("BLBE_LANGVIEWSP_FN"), 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "1", JText::_("BLBE_NICKNAME"), 'id', 'name' ); 

		$this->_lists['player_name'] = JHTML::_('select.genericlist',   $is_data, 'player_name', 'class="inputbox" size="1"', 'id', 'name', $player_name );
		///esport invites
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='esport_invite_player'";
		$this->db->setQuery($query);
		$esport_invite_player = $this->db->loadResult();
		
		$is_data = array();
		
		$is_data[] = JHTML::_('select.option', "0", JText::_("BLBE_MODERADDPL"), 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "1", JText::_("BLBE_MODERINVITEPL"), 'id', 'name' ); 

		$this->_lists['esport_invite_player'] = JHTML::_('select.genericlist',   $is_data, 'esport_invite_player', 'class="inputbox" size="1"', 'id', 'name', $esport_invite_player );
		//invite confirm
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='esport_invite_confirm'";
		$this->db->setQuery($query);
		$this->_lists["esport_invite_confirm"] = $this->db->loadResult();
		//invite unregistered
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='esport_invite_unregister'";
		$this->db->setQuery($query);
		$this->_lists["esport_invite_unregister"] = $this->db->loadResult();
		//
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='esport_join_team'";
		$this->db->setQuery($query);
		$this->_lists["esport_join_team"] = $this->db->loadResult();
		//invite to match
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='esport_invite_match'";
		$this->db->setQuery($query);
		$this->_lists["esport_invite_match"] = $this->db->loadResult();
		///admin rights
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='jssa_editplayer'";
		$this->db->setQuery($query);
		$this->_lists["jssa_editplayer"] = $this->db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='jssa_deleteplayers'";
		$this->db->setQuery($query);
		$this->_lists["jssa_deleteplayers"] = $this->db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='knock_style'";
		$this->db->setQuery($query);
		$knock_style = $this->db->loadResult();
		$is_data_v[] = JHTML::_('select.option', "0", JText::_("BLBE_VIEWHOR"), 'id', 'name' ); 
		$is_data_v[] = JHTML::_('select.option', "1", JText::_("BLBE_VIEWVER"), 'id', 'name' ); 

		$this->_lists['knock_style'] = JHTML::_('select.genericlist',   $is_data_v, 'knock_style', 'class="inputbox" size="1"', 'id', 'name', $knock_style );
		
		//social buttons
		$this->_lists['jsb_twitter'] = $this->getJS_Config('jsb_twitter');
		$this->_lists['jsb_gplus'] = $this->getJS_Config('jsb_gplus');
		$this->_lists['jsb_fbshare'] = $this->getJS_Config('jsb_fbshare');
		$this->_lists['jsb_fblike'] = $this->getJS_Config('jsb_fblike');
		$this->_lists['jsbp_season'] = $this->getJS_Config('jsbp_season');
		$this->_lists['jsbp_team'] = $this->getJS_Config('jsbp_team');
		$this->_lists['jsbp_player'] = $this->getJS_Config('jsbp_player');
		$this->_lists['jsbp_match'] = $this->getJS_Config('jsbp_match');
		$this->_lists['jsbp_venue'] = $this->getJS_Config('jsbp_venue');
	}

	public function saveConfig(){
		
		$date_format = JRequest::getVar( 'date_format', '', 'post', 'string' );
		$yteam_color = JRequest::getVar( 'yteam_color', '', 'post', 'string' );
		$nick_reg = JRequest::getVar( 'nick_reg', 0, 'post', 'int' );
		$nick_reg_rq = JRequest::getVar( 'nick_reg_rq', 0, 'post', 'int' );
		$country_reg = JRequest::getVar( 'country_reg', 0, 'post', 'int' );
		$country_reg_rq = JRequest::getVar( 'country_reg_rq', 0, 'post', 'int' );
		$mcomments = JRequest::getVar( 'mcomments', 0, 'post', 'int' );
		$player_reg = JRequest::getVar( 'player_reg', 0, 'post', 'int' );
		$team_reg = JRequest::getVar( 'team_reg', 0, 'post', 'int' );
		$moder_addplayer = JRequest::getVar( 'moder_addplayer', 0, 'post', 'int' );
		$pllist_order = JRequest::getVar( 'pllist_order', 0, 'post', 'string' );
		$teamlogo_height = JRequest::getVar( 'teamlogo_height', 0, 'post', 'int' );
		$teams_per_account = JRequest::getVar( 'teams_per_account', 0, 'post', 'int' );
		$players_per_account = JRequest::getVar( 'players_per_account', 0, 'post', 'int' );
		$unbl_venue = JRequest::getVar( 'unbl_venue', 0, 'post', 'int' );
		$cal_venue = JRequest::getVar( 'cal_venue', 0, 'post', 'int' );
		$played_matches = JRequest::getVar( 'played_matches', 0, 'post', 'int' );
		$player_name = JRequest::getVar( 'player_name', 0, 'post', 'int' );
		$esport_invite_player = JRequest::getVar( 'esport_invite_player', 0, 'post', 'int' );
		$esport_invite_confirm = JRequest::getVar( 'esport_invite_confirm', 0, 'post', 'int' );
		$esport_invite_unregister = JRequest::getVar( 'esport_invite_unregister', 0, 'post', 'int' );
		$esport_join_team = JRequest::getVar( 'esport_join_team', 0, 'post', 'int' );
		$jssa_editplayer = JRequest::getVar( 'jssa_editplayer', 0, 'post', 'int' );
		$jssa_deleteplayers = JRequest::getVar( 'jssa_deleteplayers', 0, 'post', 'int' );
		$esport_invite_match = JRequest::getVar( 'esport_invite_match', 0, 'post', 'int' );
		$knock_style = JRequest::getVar( 'knock_style', 0, 'post', 'int' );
		
		$jsb_twitter = JRequest::getVar( 'jsb_twitter', 0, 'post', 'int' );
		$jsb_gplus = JRequest::getVar( 'jsb_gplus', 0, 'post', 'int' );
		$jsb_fbshare = JRequest::getVar( 'jsb_fbshare', 0, 'post', 'int' );
		$jsb_fblike = JRequest::getVar( 'jsb_fblike', 0, 'post', 'int' );
		$jsbp_season = JRequest::getVar( 'jsbp_season', 0, 'post', 'int' );
		$jsbp_team = JRequest::getVar( 'jsbp_team', 0, 'post', 'int' );
		$jsbp_player = JRequest::getVar( 'jsbp_player', 0, 'post', 'int' );
		$jsbp_match = JRequest::getVar( 'jsbp_match', 0, 'post', 'int' );
		$jsbp_venue = JRequest::getVar( 'jsbp_venue', 0, 'post', 'int' );
		
		
		
		$query = "UPDATE #__bl_config SET cfg_value='".$date_format."' WHERE cfg_name='date_format'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$yteam_color."' WHERE cfg_name='yteam_color'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$nick_reg."' WHERE cfg_name='nick_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$nick_reg_rq."' WHERE cfg_name='nick_reg_rq'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$country_reg."' WHERE cfg_name='country_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$country_reg_rq."' WHERE cfg_name='country_reg_rq'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$mcomments."' WHERE cfg_name='mcomments'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$player_reg."' WHERE cfg_name='player_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$team_reg."' WHERE cfg_name='team_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$moder_addplayer."' WHERE cfg_name='moder_addplayer'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$pllist_order."' WHERE cfg_name='pllist_order'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$teamlogo_height."' WHERE cfg_name='teamlogo_height'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$teams_per_account."' WHERE cfg_name='teams_per_account'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$players_per_account."' WHERE cfg_name='players_per_account'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$unbl_venue."' WHERE cfg_name='unbl_venue'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$cal_venue."' WHERE cfg_name='cal_venue'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$played_matches."' WHERE cfg_name='played_matches'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$player_name."' WHERE cfg_name='player_name'";
		$this->db->setquery($query);
		$this->db->query();
		//esport invite
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_player."' WHERE cfg_name='esport_invite_player'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_confirm."' WHERE cfg_name='esport_invite_confirm'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_unregister."' WHERE cfg_name='esport_invite_unregister'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_join_team."' WHERE cfg_name='esport_join_team'";
		$this->db->setquery($query);
		$this->db->query();
		///admin rights
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_editplayer."' WHERE cfg_name='jssa_editplayer'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_deleteplayers."' WHERE cfg_name='jssa_deleteplayers'";
		$this->db->setquery($query);
		$this->db->query();
		//invite to match
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_match."' WHERE cfg_name='esport_invite_match'";
		$this->db->setquery($query);
		$this->db->query();
		
		//knock_style
		$query = "UPDATE #__bl_config SET cfg_value='".$knock_style."' WHERE cfg_name='knock_style'";
		$this->db->setquery($query);
		$this->db->query();
		
		//social buttons
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_twitter."' WHERE cfg_name='jsb_twitter'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_gplus."' WHERE cfg_name='jsb_gplus'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_fbshare."' WHERE cfg_name='jsb_fbshare'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_fblike."' WHERE cfg_name='jsb_fblike'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_season."' WHERE cfg_name='jsbp_season'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_team."' WHERE cfg_name='jsbp_team'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_player."' WHERE cfg_name='jsbp_player'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_match."' WHERE cfg_name='jsbp_match'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_venue."' WHERE cfg_name='jsbp_venue'";
		$this->db->setquery($query);
		$this->db->query();
		
		
		$adf_pl 		= JRequest::getVar( 'adf_pl', array(0), '', 'array' );
		JArrayHelper::toInteger($adf_pl, array(0));
		if(count($adf_pl)){
			$counter = 0;
			foreach($adf_pl as $map){
				$query = "UPDATE #__bl_extra_filds SET reg_exist='".((isset($_POST['adfpl_reg_'.$map]) && $_POST['adfpl_reg_'.$map] == 1)?1:0)."',reg_require='".((isset($_POST['adfpl_rq_'.$map]) && $_POST['adfpl_rq_'.$map] == 1)?1:0)."' WHERE id=".$map;
				$this->db->setQuery($query);
				$this->db->query();
				$counter++;
			}
		}
		
		$adf_pl 		= JRequest::getVar( 'adf_tm', array(0), '', 'array' );
		JArrayHelper::toInteger($adf_pl, array(0));
		if(count($adf_pl)){
			$counter = 0;
			foreach($adf_pl as $map){
				$query = "UPDATE #__bl_extra_filds SET reg_exist='".((isset($_POST['adf_reg_'.$map]) && $_POST['adf_reg_'.$map] == 1)?1:0)."',reg_require='".((isset($_POST['adf_rq_'.$map]) && $_POST['adf_rq_'.$map] == 1)?1:0)."' WHERE id=".$map;
				$this->db->setQuery($query);
				$this->db->query();
				$counter++;
			}
		}
	}
	
}