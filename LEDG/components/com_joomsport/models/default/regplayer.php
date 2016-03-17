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

class regplayerJSModel extends JSPRO_Models
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
		$this->_params = $this->JS_PageTitle(JText::_('BLFA_EDITFIPROF'));
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
		
		//Player Country registration
		$this->_lists['country_reg'] = $this->getJS_Config('country_reg');
		$this->_lists['country_reg_rq'] = $this->getJS_Config('country_reg_rq');
		$this->getCountries();
		//Nick registration
		$this->_lists['nick_reg'] = $this->getJS_Config('nick_reg');
		$this->_lists['nick_reg_rq'] = $this->getJS_Config('nick_reg_rq');
		
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,0,1);
		
	}
	function getCountries(){
		$query = "SELECT * FROM #__bl_countries ORDER BY country";
		$this->db->setQuery($query);
		$country = $this->db->loadObjectList();
		
		$cntr[] = JHTML::_('select.option',  '', JText::_('BLFA_SELCOUNTRY'), 'id', 'country' ); 
		$countries = array_merge($cntr,$country);

		$this->_lists["country"] = JHTML::_('select.genericlist',   $countries, 'country_id', 'class="styled-long'.($this->_lists['country_reg_rq']?" required":"").'" size="1"', 'id', 'country', isset($this->_lists["usr"]->country_id)?$this->_lists["usr"]->country_id:0 );
	}
	function getJSreg(){
		$query = "Select * FROM #__bl_players WHERE usr_id=".$this->_user->id;
		$this->db->setQuery($query);
		$usr  = $this->db->loadObject();
		$this->_lists["usr"] = $usr;
		if($usr){
			$query = "SELECT ef.*,ev.fvalue,ev.fvalue_text"
					." FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid = ".$usr->id
					."  WHERE reg_exist='1' AND type='0'";
			$this->db->setQuery($query);
			$adf = $this->db->loadObjectList();
			
			$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename"
					." FROM #__bl_assign_photos as ap, #__bl_photos as p, #__bl_players as pl"
					." WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$usr->id." AND pl.id=cat_id AND pl.def_img = p.id";
			$this->db->setQuery($query);
			$this->_lists['photos'] = $this->db->loadObject();
			
			$query = "SELECT COUNT(*) FROM #__bl_season_players as sp, #__bl_matchday as m, #__bl_tournament as t, #__bl_seasons as s"
					." WHERE t.id=s.t_id AND s.s_id=m.s_id AND s.published='1' AND t.published='1' AND m.s_id=sp.season_id AND sp.player_id = ".$usr->id;
			$this->db->setQuery($query);
			$this->_enmd = $this->db->loadResult();
			
		}else{

			$player_reg = $this->getJS_Config('player_reg');
			
			if(!$player_reg){
				echo JText::_('BLFA_OPTDISAB');
				exit();
			}
			
			$query = "SELECT *,'' as fvalue,'' as fvalue_text  FROM #__bl_extra_filds WHERE reg_exist='1' AND type='0'";
			$this->db->setQuery($query);
			$adf = $this->db->loadObjectList();
		}
		$mj=0;
		if(isset($adf)){
			foreach ($adf as $extr){
				if($extr->field_type == '3'){
					$query = "SELECT * FROM #__bl_extra_select WHERE fid=".$extr->id;
					$this->db->setQuery($query);
					$selvals = $this->db->loadObjectList();
					if(count($selvals)){
						$adf[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="styled-long'.($extr->reg_require?' required':'').'" size="1"', 'id', 'sel_value', $extr->fvalue );
					}
				}
				if($extr->field_type == '1'){
					$adf[$mj]->selvals	= JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue );
				}
				$mj++;
			}
		}
		$this->_lists["adf"] = $adf;
	}
	
	function SaveRegPlayer(){

		$post		= JRequest::get( 'post' );
		$row 	= new JTablePlayer($this->db);
		$row->registered = 1;
		$user	=& JFactory::getUser();
		$istlogo = JRequest::getVar( 'istlogo', 0, 'post', 'int' );
		if ( $user->get('guest')) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		} 
		$row->usr_id = $user->id;
		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		$curid = $row->id;
		
		if(!$istlogo){
			$query = "DELETE FROM #__bl_assign_photos WHERE cat_type='1' AND cat_id=".$curid;
			$this->db->setQuery($query);
			$this->db->query();
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
				
				
				
				$row->def_img = $img1->id;
			 }
		}
		
		
		
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		// if new item order last in appropriate group
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		
		if(isset($img1)){
			$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",1)";
		 	$this->db->setQuery($query);
			$this->db->query();
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
		
	}
	
}