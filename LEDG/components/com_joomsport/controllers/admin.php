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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

$mainframe = JFactory::getApplication();
function itgetVer(){
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		return substr($joomla,0,3);
	}
if($task != 'get_format'){
	$doc =& JFactory::getDocument();
	if(itgetVer() >= '1.6'){
		JHtml::_('behavior.framework', true);
	}else{
		JHtml::_('behavior.mootools');
	}

	$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/admin_bl.css" />' );
$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/joomsport.css" />' );
	$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/joomsport.js"></script>' );
	$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/styled-long.js"></script>' );

}

?>
<?php

$db			=& JFactory::getDBO();
$user	=& JFactory::getUser();
 $sid = JRequest::getVar( 'sid', 0, 'request', 'int' );
	

	if ( $user->get('guest')) {

			$return_url = $_SERVER['REQUEST_URI'];
			$return_url = base64_encode($return_url);
			
			if(itgetVer() >= '1.6'){
				$uopt = "com_users";
			}else{
				$uopt = "com_user";
			}
			$return	= 'index.php?option='.$uopt.'&view=login&return='.$return_url;

			// Redirect to a login form
			$mainframe->redirect( $return, JText::_('BLMESS_NOT_LOGIN') );
			
		} 

	$query = "SELECT COUNT(*) FROM #__users as u, #__bl_feadmins as f WHERE f.user_id = u.id AND f.season_id=".$sid." AND u.id = ".intval($user->id);

	$db->setQuery($query);

	if(!$db->loadResult()){
		JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
	}
	
	

class JoomsportControllerAdmin extends JController
{

	protected $js_prefix = '';
	protected $mainframe = null;
	protected $option = 'com_joomsport';
	
	function __construct(){
		parent::__construct();
		$this->mainframe = JFactory::getApplication();
		$this->js_SetPrefix();
		$this->js_GetDBTables();
	}
	private function js_SetPrefix(){
		$this->js_prefix = '';
		$db			=& JFactory::getDBO();
		$query = "SELECT name FROM #__bl_addons WHERE published='1'";
		$db->setQuery($query);
		$addon = $db->loadResult();
		if($addon){
			$this->js_prefix = $addon;
		}
		
	}
	private function js_GetDBTables(){
		$path = JPATH_SITE.'/administrator/components/com_joomsport/tables/';
		if($this->js_prefix){
			if(is_file($path.$this->js_prefix.".php")){
				require($path.$this->js_prefix.".php");
			}else{
				require($path."default.php");
			}
		}else{
			require($path."default.php");
		}
	}
	private function js_Model($name){
		$path = dirname(__FILE__).'/../models/';
		if($this->js_prefix){
			if(is_file($path.$this->js_prefix."/".$name.".php")){
				require($path.$this->js_prefix."/".$name.".php");
			}else{
				require($path."default/".$name.".php");
			}
		}else{
			require($path."default/".$name.".php");
		}
	}
	private function js_Layout($task){
		$path = dirname(__FILE__).'/../views/'.$task;
		
		require($path."/view.html.php");
		
	}
	
	function display()
	{
		$view = JRequest::getCmd( 'view' );
		$task = JRequest::getCmd( 'task' );
		if(!$view) {
			//if($task){
				//$view = $task;
			//}else{
				$view = 'admin_matchday';
			//}	
		}
		
		
		$vName		= JRequest::getCmd('view', 'admin_matchday');
		
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		
		$this->js_Layout($vName);
		$classname_l = "JoomsportView".$vName;
		
		$layout = new $classname_l($model);
		
		$layout->display();
	
		
		return $this;
		
	}
	
	
	///---------------Matchday--------------------------/
	function admin_matchday()
	{
		JRequest::setVar( 'view', 'admin_matchday' );
		$this->display();
	}
	function edit_matchday()
	{
		JRequest::setVar( 'view', 'edit_matchday' );
		JRequest::setVar( 'edit', true );
		$this->display();
	}
	
	function matchday_add()
	{
		JRequest::setVar( 'view', 'edit_matchday' );
		JRequest::setVar( 'edit', false );
		$this->display();
	}
	
	function matchday_save(){

		$vName = 'edit_matchday';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->AdmMDSave();
		
		$msg = JText::_('BLFA_MSG_ADDSCHED');

		$Itemid = JRequest::getInt('Itemid'); 
		$isapply = JRequest::getVar( 'isapply', 0, '', 'int' );
		if(!$isapply){
		
			$link = "index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid=".$model->season_id."&Itemid=".$Itemid;
		}else{
			$link = "index.php?option=com_joomsport&controller=admin&view=edit_matchday&sid=".$model->season_id."&cid[]=".$model->id."&Itemid=".$Itemid;
		}
		
		$this->setRedirect( $link );
	}
	
	function matchday_del(){
		
		$vName = 'edit_matchday';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->delAdmMD();
		
		$Itemid = JRequest::getInt('Itemid'); 
		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );	
		$this->setRedirect("index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid=".$s_id."&Itemid=".$Itemid);
	}
	
	
	///---------------Match--------------------------/

	function admin_match()
	{
		$mainframe = JFactory::getApplication();;	
		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );	
		$mid = JRequest::getVar( 'm_id', 0, '', 'int' );	
		$Itemid = JRequest::getInt('Itemid'); 
		$this->setRedirect("index.php?option=com_joomsport&controller=admin&task=edit_matchday&sid=".$s_id."&mid=".$mid."&Itemid=".$Itemid);

	}
	function edit_match()
	{
		JRequest::setVar( 'view', 'edit_match' );
		
		$this->display();
	}
	function match_save(){
		$vName = 'edit_match';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveAdmmatch();
		$isapply = JRequest::getVar( 'isapply', 0, '', 'int' );
		$Itemid = JRequest::getInt('Itemid'); 
		if(!$isapply){
			$this->setRedirect("index.php?option=com_joomsport&controller=admin&view=edit_matchday&cid[]=".$model->m_id."&sid=".$model->season_id."&Itemid=".$Itemid);
		}else{
			$this->setRedirect("index.php?option=com_joomsport&controller=admin&view=edit_match&cid[]=".$model->id."&sid=".$model->season_id."&Itemid=".$Itemid);
		}
	} 
	
	//----FORMAT---/
	function get_format(){
		$fid = JRequest::getVar( 'fr_id', 0, 'GET', 'int' );
		$t_single = JRequest::getVar( 't_single', 0, 'GET', 'int' );
		$s_id = JRequest::getVar( 'sid', 0, 'GET', 'int' );
		
		$db			=& JFactory::getDBO();
		
		if($t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
		}else{
			$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
		}
		$db->setQuery($query);
		$team = $db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, ($t_single?JText::_('BLFA_SELPLAYER'):JText::_('BLFA_SELTEAM')), 'id', 't_name' ); 
		$teamis = array_merge($is_team,$team);
		$lists['teams_kn'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn[]', 'class="inputbox" size="1"', 'id', 't_name', 0 );
		$lists['teams_kn_aw'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn_aw[]', 'class="inputbox" size="1"', 'id', 't_name', 0 );
		
		$cfg = new stdClass();
			$cfg->wdth = 150;
			$cfg->height = 50;
			$cfg->step = 70; 
			$cfg->top_next = 50;
		
			$wdth = $cfg->wdth;
			$height = $cfg->height;
			$step = $cfg->step; 
			$top_next = $cfg->top_next;
		$zz = 2;
		
		$p=0;
		
		echo '<div style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;border:1px solid #777;">';
		
		while(floor($fid/$zz) >= 1){
			
			for($i=0;$i<floor($fid/$zz);$i++){
				echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
				if($p==0){
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo $lists['teams_kn'];
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo $lists['teams_kn_aw'];
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_1[]" value="" size="5" maxlength="5" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_1_aw[]" value="" size="5" maxlength="5" />';
					echo '</div>';
				}else{
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(60 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_'.($p+1).'[]" value="" size="10" maxlength="5" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(60 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_'.($p+1).'_aw[]" value="" size="10" maxlength="5" />';
					echo '</div>';
				}
			}
			$top_next += $height/2;
			$height = $height + $step;
			$step = $height;
			$zz *= 2;
			$p++;
			
		}
		if($fid){
			echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
		}	
		echo '</div>';
	}
	
	function admin_team()
	{
		JRequest::setVar( 'view', 'admin_team' );
		$this->display();
	}
	
	function edit_team()
	{
		JRequest::setVar( 'view', 'edit_team' );
		JRequest::setVar( 'edit', true );
		$this->display();
	}
	
	function team_add()
	{
		JRequest::setVar( 'view', 'edit_team' );
		JRequest::setVar( 'edit', false );
		$this->display();
	}
	
	function team_apply(){
		$this->team_save(1);
	}
	
	function team_save($apl = 0){

		$vName = 'edit_team';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->SaveAdmTeam();
		
		$Itemid = JRequest::getInt('Itemid'); 
		
		if($apl){
			$link = "index.php?option=com_joomsport&controller=admin&view=edit_team&cid[]=".$model->id."&sid=".$model->season_id."&Itemid=".$Itemid;
		}else{
			$link = "index.php?option=com_joomsport&controller=admin&view=admin_team&sid=".$model->season_id."&Itemid=".$Itemid;
		}
		
		$this->setRedirect($link);
	}
	
	function team_del(){
		
		$vName = 'edit_team';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->delAdmTeam();
		
		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );	

		$Itemid = JRequest::getInt('Itemid');
		$this->setRedirect("index.php?option=com_joomsport&controller=admin&view=admin_team&sid=".$s_id."&Itemid=".$Itemid);
	}
	
	
	
	///---------------Players--------------------------/
	function adlist_player()
	{
		JRequest::setVar( 'view', 'adlist_player' );
		$this->display();
	}
	function adplayer_edit()
	{
		JRequest::setVar( 'view', 'adplayer_edit' );
		JRequest::setVar( 'edit', true );
		$this->display();
	}
	
	function adplayer_add()
	{
		JRequest::setVar( 'view', 'adplayer_edit' );
		JRequest::setVar( 'edit', false );
		$this->display();
	}
	
	function adplayer_apply(){
		$this->adplayer_save(1);
	}
	
	function adplayer_save($apl = 0){
		
		$vName = 'adplayer_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->savAdmPlayer();
		
		$Itemid = JRequest::getInt('Itemid');
		if($apl){
			$link = "index.php?option=com_joomsport&controller=admin&view=adplayer_edit&cid[]=".$model->id."&sid=".$model->season_id."&Itemid=".$Itemid;
		}else{
			$link = "index.php?option=com_joomsport&controller=admin&view=adlist_player&sid=".$model->season_id."&Itemid=".$Itemid;
		}
		$this->setRedirect($link);
	
	}
	
	function adplayer_del(){
		
		$vName = 'adplayer_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->delAdmPlayer();

		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );	
		$Itemid = JRequest::getInt('Itemid');
		$this->setRedirect("index.php?option=com_joomsport&controller=admin&view=adlist_player&sid=".$s_id."&Itemid=".$Itemid);
	}

}	

?>