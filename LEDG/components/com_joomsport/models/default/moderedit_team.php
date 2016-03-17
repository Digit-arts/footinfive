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

class moderedit_teamJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $season_id = null;
	var $id = null;
	function __construct()
	{
		parent::__construct();
		$this->id 		= JRequest::getVar( 'tid', 0, '', 'int' );	
	}

	function getData()
	{
		$this->getGlobFilters();
		$this->season_id	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.moderseason', 'moderseason', 0, 'int' );
		$query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$this->id." ORDER BY s.s_id desc";
		$this->db->setQuery($query);
		$seass = $this->db->loadObjectList();
		if(!$this->season_id) {$this->season_id = $seass[0]->id;};
		$isinseas = false;
		for($j=0;$j<count($seass);$j++){
			if($this->season_id == $seass[$j]->id){
				$isinseas = true;
			}
		}
		if(!$isinseas && count($seass)){
			
			$this->season_id = $seass[0]->id;
		}
	
		$this->_params = $this->JS_PageTitle("");

		$row 	= new JTableTeams($this->db);
		$row->load($this->id);
		//extra fields
		$this->_lists['ext_fields'] = $this->getAddFields($row->id,1,"team");
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename"
				." FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 2 AND cat_id = ".$row->id."";
		$this->db->setQuery($query);
		$this->_lists['photos'] = $this->db->loadObjectList();
		$query = "SELECT COUNT(*) FROM #__bl_season_teams as sp, #__bl_matchday as m WHERE m.s_id=sp.season_id AND sp.team_id = ".$row->id;
		$this->db->setQuery($query);
		$this->_lists["enmd"] = $this->db->loadResult();
		
		$query = "SELECT m.* FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_matchday as m"
				." WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.team_id=".$row->id." AND s.s_id=".$this->season_id
				." ORDER BY m.ordering";
		$this->db->setQuery($query);
		$mdays = $this->db->loadObjectList();
		
		if(!count($mdays)){
			$this->_lists["enmd"] = 0;
		}
		
		$this->getInviteOptions();
		
		$this->getPlayersT($row->id,$this->season_id);
		
		$this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');
		$this->_data = $row;
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
		$this->_lists['ext_fields'] = $this->getBEAdditfields('1',$row->id,$this->season_id);
	}
	function getPlayersT($id,$s_id){
		
		$query = "SELECT p.id FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_id=p.id AND t.team_id=".$id." AND t.season_id=".$s_id;
		$this->db->setQuery($query);
		$plint = $this->db->loadResultArray();
		
		if(!$this->_lists['esport_invite_player']){
			$query = "SELECT CONCAT(first_name,' ',last_name) as name,id FROM #__bl_players ".(count($plint)?" WHERE id NOT IN (".implode(',',$plint).")":"")." ORDER BY first_name,last_name";
		}else{
			$query = "SELECT CONCAT(p.first_name,' ',p.last_name) as name,p.id FROM #__bl_players as p, #__users as u WHERE u.id=p.usr_id ".(count($plint)?" AND p.id NOT IN (".implode(',',$plint).")":"")." ORDER BY p.first_name,p.last_name";
		}		
		$this->db->setQuery($query);
		$is_pl = $this->db->loadObjectList();
		$playerz[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 'name' ); 
		if(count($is_pl)){
			$playerz = array_merge($playerz,$is_pl);
		}

		$this->_lists['player'] = JHTML::_('select.genericlist',   $playerz, 'playerz_id', 'class="styled" size="1" id="playerz"', 'id', 'name', 0 );
	
		$query = "SELECT p.id,CONCAT(p.first_name,' ',p.last_name) as name,t.confirmed FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_join='0' AND t.player_id=p.id AND t.team_id=".$id." AND t.season_id=".$s_id;
		$this->db->setQuery($query);
		$this->_lists['team_players'] = $this->db->loadObjectList();
		
		$query = "SELECT p.id,CONCAT(p.first_name,' ',p.last_name) as name,t.confirmed FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_join='1' AND t.player_id=p.id AND t.team_id=".$id." AND t.season_id=".$s_id;
		$this->db->setQuery($query);
		$this->_lists['waiting_players'] = $this->db->loadObjectList();
	}
	function getInviteOptions(){
		$this->_lists['esport_invite_player'] = $this->getJS_Config('esport_invite_player');
		//$this->_lists['esport_invite_confirm'] = $this->getJS_Config('esport_invite_confirm');
		$this->_lists['esport_invite_unregister'] = $this->getJS_Config('esport_invite_unregister');
		$this->_lists['esport_join_team'] = $this->getJS_Config('esport_join_team');
		
		$arr = array();
		$arr[] = JHTML::_('select.option',  0, JText::_('BLFA_NOACTION'), 'id', 'name' ); 
		$arr[] = JHTML::_('select.option',  1, JText::_('BLFA_PLAPPROVE'), 'id', 'name' ); 
		$arr[] = JHTML::_('select.option',  2, JText::_('BLFA_PLREJECT'), 'id', 'name' );
		$this->_lists["arr_action"] = $arr;
	}
	
	function InvitePlayer($player_id,$teamname,$seasid, $gen){
		$config	= JFactory::getConfig();
		$fromname = $config->get('fromname');
		$mailfrom = $config->get('mailfrom');
		$sitename = $config->get('sitename');
		if($player_id){
			$query = "SELECT u.email FROM #__users as u, #__bl_players as p WHERE p.usr_id = u.id AND p.id = ".$player_id;
			$this->db->setQuery($query);
			$mail = $this->db->loadResult();
			if($mail){
				$emailSubject = JText::_('BLFA_MAIL_INVPLTITLE');
				
				$link = JUri::base()."index.php?option=com_joomsport&task=confirm_invitings&key=".$gen;
				$emailBody = JText::_('BLFA_MAIL_INVPLBODY');
				$emailBody = str_replace('{link}',"<a href='".$link."'>",$emailBody);
				$emailBody = str_replace('{/link}',"</a>",$emailBody);
				$emailBody = str_replace('{team}',$teamname,$emailBody);
				$return = JUtility::sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);

				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('ERROR'));
					return false;
				}
			}	
		}
	}
	function InviteUnreg($mail,$teamname,$seasid, $gen){
		$config	= JFactory::getConfig();
		$fromname = $config->get('fromname');
		$mailfrom = $config->get('mailfrom');
		$sitename = $config->get('sitename');
		
			
			if($mail){
				$emailSubject = JText::_('BLFA_MAIL_INVPLTITLE');
				
				$link = JUri::base()."index.php?option=com_joomsport&task=unreg_inviting&key=".$gen;
				$emailBody = JText::_('BLFA_MAIL_INVPLBODY');
				$emailBody = str_replace('{link}',"<a href='".$link."'>",$emailBody);
				$emailBody = str_replace('{/link}',"</a>",$emailBody);
				$emailBody = str_replace('{team}',$teamname,$emailBody);
				$return = JUtility::sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);

				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('ERROR'));
					return false;
				}
			}	
		
	}
	
	function Pl_Approve($player_id, $team_name, $team_id, $s_id){
		$config	= JFactory::getConfig();
		$fromname = $config->get('fromname');
		$mailfrom = $config->get('mailfrom');
		$sitename = $config->get('sitename');
		if($player_id && $team_id){
			$query = "SELECT u.email FROM #__users as u, #__bl_players as p WHERE p.usr_id = u.id AND p.id = ".$player_id;
			$this->db->setQuery($query);
			$mail = $this->db->loadResult();
			if($mail){
				$emailSubject = JText::_('BLFA_MAIL_APPROVEDPLTITLE');
				
				
				$emailBody = JText::_('BLFA_MAIL_APPROVEDPLBODY');
				$emailBody = str_replace('{team}',$team_name,$emailBody);
				$return = JUtility::sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);
				$query = "UPDATE #__bl_players_team SET player_join = '0', confirmed = '0' WHERE team_id = ".$team_id." AND player_id = ".$player_id."  AND season_id = ".$s_id;
				$this->db->setQuery($query);
				$this->db->query();				
				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('ERROR'));
					return false;
				}
			}	
		}
	}
	function Pl_Reject($player_id, $team_name, $team_id, $s_id){
		$config	= JFactory::getConfig();
		$fromname = $config->get('fromname');
		$mailfrom = $config->get('mailfrom');
		$sitename = $config->get('sitename');
		if($player_id && $team_id){
			$query = "SELECT u.email FROM #__users as u, #__bl_players as p WHERE p.usr_id = u.id AND p.id = ".$player_id;
			$this->db->setQuery($query);
			$mail = $this->db->loadResult();
			if($mail){
				$emailSubject = JText::_('BLFA_MAIL_REJECTPLTITLE');
				
				
				$emailBody = JText::_('BLFA_MAIL_REJECTPLBODY');
				$emailBody = str_replace('{team}',$team_name,$emailBody);
				$return = JUtility::sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);
				
				$query = "DELETE FROM #__bl_players_team WHERE team_id = ".$team_id." AND player_id = ".$player_id."  AND season_id = ".$s_id;
				$this->db->setQuery($query);
				$this->db->query();	
				
				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('ERROR'));
					return false;
				}
			}	
		}
	}
	
	function SaveModerTeam(){
		$msg = '';
		$post		= JRequest::get( 'post' );
		$post['t_descr'] = JRequest::getVar( 't_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['def_img'] = JRequest::getVar( 'ph_default', 0, 'post', 'int' );
		$post['id'] = JRequest::getVar( 'tid', 0, 'post', 'int' ); 
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
		if(!$row->id){
			die();
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

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
		$seasf_id	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.moderseason', 'moderseason', 0, 'int' );
		$query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$row->id." ORDER BY s.s_id desc";
		$this->db->setQuery($query);
		$seass = $this->db->loadObjectList();
		if(!$seasf_id) {$seasf_id = $seass[0]->id;}
		//-------extra fields-----------//
		if(isset($_POST['extraf']) && count($_POST['extraf'])){
	
			foreach($_POST['extraf'] as $p=>$dummy){
	
				$query = "DELETE FROM #__bl_extra_values WHERE f_id = ".$_POST['extra_id'][$p]." AND uid = ".$row->id;
				$this->db->setQuery($query);
				$this->db->query();
				$fld = ($_POST['extra_ftype'][$p] == 2)?'fvalue_text':'fvalue';
				$query = "INSERT INTO #__bl_extra_values(f_id,uid,`".$fld."`,season_id) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".$_POST['extraf'][$p]."',".$seasf_id.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		//players
		//-------Players----//
		$inviteoradd = $this->getJS_Config('esport_invite_player');
		
		if($seasf_id){
			
			$plzold = array();
			
			if(isset($_POST['teampl']) && count($_POST['teampl'])){
				for($p=0;$p<count($_POST['teampl']);$p++){
					if(intval($_POST['teampl'][$p])){
						$query = "SELECT player_id FROM #__bl_players_team WHERE player_id = ".intval($_POST['teampl'][$p])." AND team_id = {$row->id} AND season_id = {$seasf_id}";
						$this->db->setQuery($query);
						$plzold[] = intval($_POST['teampl'][$p]);
						if(!$this->db->loadResult()){
							$query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id,confirmed) VALUES(".$row->id.",".intval($_POST['teampl'][$p]).",".$seasf_id.",".($inviteoradd?1:0).")";
							$this->db->setQuery($query);
							$this->db->query();
							
							//invite
							if($inviteoradd){
								mt_srand((double)(microtime()));
								$gen = mt_rand()."prI".microtime();
								$gen = str_replace(" ","",$gen);
								$this->InvitePlayer(intval($_POST['teampl'][$p]),$row->t_name,$seasf_id, $gen);
								$query = "UPDATE #__bl_players_team SET invitekey='".$gen."' WHERE team_id = ".$row->id." AND player_id = ".intval($_POST['teampl'][$p])."  AND season_id = ".$seasf_id;
								$this->db->setQuery($query);
								$this->db->query();
							}
						}	
					}
				}
			}else{
				$query = "DELETE FROM #__bl_players_team WHERE team_id=".$row->id." AND season_id=".$seasf_id." AND player_join='0'";
				$this->db->setQuery($query);
				$this->db->query();	
			}
			
			if(count($plzold)){
				$sql = (count($plzold) > 1)?("player_id NOT IN (".implode(",",$plzold).")"):("player_id != ".$plzold[0]);
				$query = "DELETE FROM #__bl_players_team WHERE ".$sql." AND team_id=".$row->id." AND season_id=".$seasf_id." AND player_join='0'";
				$this->db->setQuery($query);
				$this->db->query();	
			}
		
		}
		//invite unregs
		if(isset($_POST['emlinv']) && count($_POST['emlinv']) && $row->id){
			for($p=0;$p<count($_POST['emlinv']);$p++){
				mt_srand((double)(microtime()));
				$gen = mt_rand()."prI".microtime();
				$gen = str_replace(" ","",$gen);
				$query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id,confirmed,invitekey) VALUES(".$row->id.",0,".$seasf_id.",'1','".$gen."')";
				$this->db->setQuery($query);
				$this->db->query();
				$this->InviteUnreg($_POST['emlinv'][$p],$row->t_name,$seasf_id, $gen);
			}
		}	
		//action with players join team
		if(isset($_POST['appr_pl']) && count($_POST['appr_pl']) && $row->id){
			for($p=0;$p<count($_POST['appr_pl']);$p++){
				$ids = $_POST['appr_pl'][$p];
				switch($_POST['action_'.$ids]){
					case 1: $this->Pl_Approve($ids, $row->t_name, $row->id, $seasf_id);
					break;
					case 2: $this->Pl_Reject($ids, $row->t_name, $row->id, $seasf_id);
					break;
				}
				$query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id,confirmed,invitekey) VALUES(".$row->id.",0,".$seasf_id.",'1','".$gen."')";
				$this->db->setQuery($query);
				$this->db->query();
				$this->InviteUnreg($_POST['emlinv'][$p],$row->t_name,$seasf_id, $gen);
			}
		}	
		
		$this->id = $row->id;
	}
	
	
}