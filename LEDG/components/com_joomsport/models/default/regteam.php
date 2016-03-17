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

class regteamJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $_user = null;
	var $_usrjs = null;
	var $_enmd = null;
	function __construct()
	{
		parent::__construct();
		$this->_user	=& JFactory::getUser();
		$this->_enmd = 0;
	}

	function getData()
	{
		//title
		$this->_params = $this->JS_PageTitle(JText::_('BLFA_NTEAM'));
		$team_reg = $this->getJS_Config('team_reg');
			
			if(!$team_reg){
				echo JText::_('BLFA_OPTDISAB');
				exit();
			}
		//return
		if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
			$return = $return;
			if (!JURI::isInternal($return)) {
				$return = '';
			}
		}
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
			$this->mainframe->redirect( $return, JText::_('BLMESS_NOT_LOGIN') );
			
		}
		$this->_lists["return"] = $return;
		$this->getJSreg();

		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,0,1);
		
	}
	
	function getJSreg(){
		$Itemid = JRequest::getInt('Itemid');
		$query = "Select * FROM #__bl_players WHERE usr_id=".$this->_user->id;
		$this->db->setQuery($query);
		$usr  = $this->db->loadObject();
		
		if(!$usr || $usr->registered != '1'){
			$return_url = $_SERVER['REQUEST_URI'];
			$return_url = base64_encode($return_url);
			$return = 'index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid.'&return='.$return_url;
			$this->mainframe->redirect( $return, JText::_('BLMESS_FILL_PROFILE') );
		}
		
		$query = "SELECT ef.*,ev.fvalue,ev.fvalue_text"
				." FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid = ".(isset($usr->id)?$usr->id:0)
				."  WHERE reg_exist='1' AND type='1'";
		$this->db->setQuery($query);
		$adf = $this->db->loadObjectList();
			
		$this->_lists["canmore"] = false;
		$query = "SELECT COUNT(*) FROM #__bl_teams WHERE created_by=".$this->_user->id;
		$this->db->setQuery($query);
		$curcap = $this->db->loadResult();
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='teams_per_account'";
		$this->db->setQuery($query);
		$teams_per_account = $this->db->loadResult();
		
		if($curcap < $teams_per_account){
			$this->_lists["canmore"] = true;
		}
		
		$mj=0;
		if(isset($adf)){
			foreach ($adf as $extr){
				if($extr->field_type == '3'){
					$query = "SELECT * FROM #__bl_extra_select WHERE fid=".$extr->id;
					$this->db->setQuery($query);
					$selvals = $this->db->loadObjectList();
					if(count($selvals)){
						$adf[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="inputbox'.($extr->reg_require?' required':'').'" size="1"', 'id', 'sel_value', $extr->fvalue );
					}
				}
				if($extr->field_type == '1'){
					$adf[$mj]->selvals	= JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue );
				}
				$mj++;
			}
		}
		
		$this->_lists["cap"] = $this->_user->username;

		
		$this->_lists["adf"] = $adf;
	}
	function regTeamSave(){
		
		$post		= JRequest::get( 'post' );
		$user	=& JFactory::getUser();
		$row 	= new JTableTeams($this->db);
		$row->created_by = $user->id;
		
		$canmore = false;
		$query = "SELECT COUNT(*) FROM #__bl_teams WHERE created_by=".$user->id;
		$this->db->setQuery($query);
		$curcap = $this->db->loadResult();
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='teams_per_account'";
		$this->db->setQuery($query);
		$teams_per_account = $this->db->loadResult();
		
		if($curcap < $teams_per_account){
			$canmore = true;
		}
		
		if ( $user->get('guest') && !$canmore) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		} 
		//$post['captain_id'] = $user->id;
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
		$query = "INSERT INTO #__bl_moders(uid,tid) VALUES({$user->id},{$row->id})";
		$this->db->setQuery($query);
		$this->db->query();
	}
	
}