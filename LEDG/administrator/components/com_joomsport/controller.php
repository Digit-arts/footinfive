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

		$doc =& JFactory::getDocument();
		$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/main.js"></script>' );
		$doc->addCustomTag( '<link rel="stylesheet" type="text/css" href="components/com_joomsport/css/be_joomsport.css" />' );
class JoomSportController extends JController
{
	protected $js_prefix = '';
	protected $mainframe = null;
	protected $option = 'com_joomsport';
	
	function __construct(){
		parent::__construct();
		$this->mainframe = JFactory::getApplication();
		$curtask = JRequest::getCmd('task', 'joomsport');
		$this->addSubmenu($curtask);
		$tmpl = JRequest::getVar( 'tmpl', '', 'get', 'string' );
		if($tmpl != 'component' && $curtask != 'getformat'){
			$this->JS_LMenu();
		}
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
		$path = dirname(__FILE__).'/tables/';
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
		$newclass = false;
		$path = dirname(__FILE__).'/models/';
		if($this->js_prefix){
			if(is_file($path.$this->js_prefix."/".$name.".php")){
				require($path.$this->js_prefix."/".$name.".php");
				$newclass = true;
			}else{
				require($path."default/".$name.".php");
			}
		}else{
			require($path."default/".$name.".php");
		}
		return $newclass;
	}
	private function js_Layout($task){
		$path = dirname(__FILE__).'/views/'.$task;
		
		require($path."/view.html.php");
		
	}
	
	//jommsport menu
	function JS_LMenu(){
		?>
		<table width="100%">
			<tr>
				<td width="250" valign="top">
					<div style="width:220px;">	
						
						<div class="jslmenu">
							<div class="jlsm_header">
								<img src="components/com_joomsport/img/topjsmenu.png" />
							</div>
							
							<div class="jlsm_cen">
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=tour_list"><img src="components/com_joomsport/img/tourn16.png" />&nbsp;<?php echo JText::_('BLBE_TOURNAMENT')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=season_list"><img src="components/com_joomsport/img/season16.png" />&nbsp;<?php echo JText::_('BLBE_SEASON')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=team_list"><img src="components/com_joomsport/img/team16.png" />&nbsp;<?php echo JText::_('BLBE_MENTEAMS')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=matchday_list"><img src="components/com_joomsport/img/match16.png" />&nbsp;<?php echo JText::_('BLBE_MENMD')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=player_list"><img src="components/com_joomsport/img/players16.png" />&nbsp;<?php echo JText::_('BLBE_MENPL')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=event_list"><img src="components/com_joomsport/img/events16.png" />&nbsp;<?php echo JText::_('BLBE_MENEV')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=group_list"><img src="components/com_joomsport/img/group16.png" />&nbsp;<?php echo JText::_('BLBE_MENGROUPS')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=moder_list"><img src="components/com_joomsport/img/moder16.png" />&nbsp;<?php echo JText::_('BLBE_MODERATORS')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=map_list"><img src="components/com_joomsport/img/maps16.png" />&nbsp;<?php echo JText::_('BLBE_MAPS')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=fields_list"><img src="components/com_joomsport/img/additional16.png" />&nbsp;<?php echo JText::_('BLBE_MENAF')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=venue_list"><img src="components/com_joomsport/img/match16.png" />&nbsp;<?php echo JText::_('BLBE_VENUE')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=config"><img src="components/com_joomsport/img/config16.png" />&nbsp;<?php echo JText::_('BLBE_MENCONF')?></a>
								</div>
								
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=adons"><img src="components/com_joomsport/img/config16.png" />&nbsp;<?php echo JText::_('BLBE_ADDONS');?></a>
								</div>
								
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=help"><img src="components/com_joomsport/img/about16.png" />&nbsp;<?php echo JText::_('BLBE_MENHLP')?></a>
								</div>
								<div class="jslm_item">
									<a href="index.php?option=com_joomsport&task=about"><img src="components/com_joomsport/img/about16.png" />&nbsp;<?php echo JText::_('BLBE_MENAB')?></a>
								</div>
							</div>
							<div class="jlsm_bot">
								<?php 
								//$lastver =  @file_get_contents('http://joomsport.com/index2.php?option=com_chkversion&id=1&no_html=1');
								$curver  = "2.5.0";
								?>
								<div class="jslm_version">
									<?php echo JText::_("BLBE_CURVERS");?><span class="<?php echo (($lastver == $curver)?"jslatvergreen":"jslatverred");?>"><?php echo $curver;?></span>
								</div>
								<div class="jslm_version" >
									<?php echo JText::_("BLBE_LASTVER");?> <span id="jfm_LatestVersion"><a href="check_now" onclick="return jfm_CheckVersion('<?php echo JUri::base();?>');" style="cursor: pointer; text-decoration:underline;">&nbsp;check now</a></span>
								</div>
							</div>
						</div>
						
					</div>
					<?php
					$db			=& JFactory::getDBO();
					$query = "SELECT name FROM #__bl_addons WHERE published='1' AND name='betting'";
					$db->setQuery($query);
					$is_betting = $db->loadResult();
					?>
					<?php if ($is_betting):?>
						<div style="width:220px; margin-top:10px">
							<div class="jslmenu">
								<div class="jlsm_header"><h3>Betting Menu</h3></div>
								<div class="jlsm_cen">
									<div class="jslm_item">
											<a href="index.php?option=com_joomsport&task=template_list"><?php echo JText::_('BLBE_BET_TMLIST')?></a>
									</div>
									<div class="jslm_item">
											<a href="index.php?option=com_joomsport&task=betting_users"><?php echo JText::_('BLBE_BET_USERS')?></a>
									</div>
									<div class="jslm_item">
											<a href="index.php?option=com_joomsport&task=betting_cash_requests_list"><?php echo JText::_('BLBE_BET_CASH_REQUESTS')?></a>
									</div>
									<div class="jslm_item">
											<a href="index.php?option=com_joomsport&task=betting_points_requests_list"><?php echo JText::_('BLBE_BET_POINTS_REQUEST')?></a>
									</div>                                                
									<div class="jslm_item">
											<a href="index.php?option=com_joomsport&task=betting_log"><?php echo JText::_('BLBE_BET_LOG')?></a>
									</div>
									<div class="jslm_item">
											<a href="index.php?option=com_joomsport&task=betting_config"><?php echo JText::_('BLBE_BET_CONFIG')?></a>
									</div>
								</div>
								<div class="jlsm_bot">
								</div>
							</div>
						</div>
					<?php endif;?>
				</td>	
		<?php
	}
	

	function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('BLBE_TOURNAMENT'),
			'index.php?option=com_joomsport&task=tour_list',
			$vName == 'tour_list' || $vName == 'tour_edit' || $vName == ''
		);

		JSubMenuHelper::addEntry(
			JText::_('BLBE_SEASON'),
			'index.php?option=com_joomsport&task=season_list',
			$vName == 'season_list' || $vName == 'season_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENTEAMS'),
			'index.php?option=com_joomsport&task=team_list',
			$vName == 'team_list' || $vName == 'team_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENMD'),
			'index.php?option=com_joomsport&task=matchday_list',
			$vName == 'matchday_list' || $vName == 'matchday_edit' || $vName == 'match_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENPL'),
			'index.php?option=com_joomsport&task=player_list',
			$vName == 'player_list' || $vName == 'player_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENEV'),
			'index.php?option=com_joomsport&task=event_list',
			$vName == 'event_list' || $vName == 'event_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENGROUPS'),
			'index.php?option=com_joomsport&task=group_list',
			$vName == 'group_list' || $vName == 'group_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MODERATORS'),
			'index.php?option=com_joomsport&task=moder_list',
			$vName == 'moder_list' || $vName == 'moder_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENAF'),
			'index.php?option=com_joomsport&task=fields_list',
			$vName == 'fields_list' || $vName == 'fields_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MAPS'),
			'index.php?option=com_joomsport&task=map_list',
			$vName == 'map_list' || $vName == 'map_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_VENUE'),
			'index.php?option=com_joomsport&task=venue_list',
			$vName == 'venue_list' || $vName == 'venue_edit'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENCONF'),
			'index.php?option=com_joomsport&task=config',
			$vName == 'config'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_ADDONS'),
			'index.php?option=com_joomsport&task=adons',
			$vName == 'adons'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENHLP'),
			'index.php?option=com_joomsport&task=help',
			$vName == 'help'
		);
		JSubMenuHelper::addEntry(
			JText::_('BLBE_MENAB'),
			'index.php?option=com_joomsport&task=about',
			$vName == 'about'
		);
		
		
	}
	
	
	public function display()
	{
		//require_once JPATH_COMPONENT.'/helpers/joomsport.php';

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName		= JRequest::getCmd('task', 'tour_list');
		$vFormat	= $document->getType();
		//$lName		= JRequest::getCmd('layout', 'default');
		$editmodes = 1;
		if(substr_count($vName,'add')){
			$vName = str_replace('add','edit',$vName);
			$editmodes = 0;
		}
		$newclass = $this->js_Model($vName);
		
		$classname = $vName."JSModel";
		if($this->js_prefix && $newclass){
			$classname.="_".$this->js_prefix;
		}
		$model = new $classname();
		if(!$editmodes){
			$model->_mode = 0;
		}
		$this->js_Layout($vName);
		$classname_l = "JoomsportView".$vName;
		
		$layout = new $classname_l($model);
		//view name
		$tpl = '';
		$path = dirname(__FILE__).'/views/'.$vName.'/tmpl/default_'.$this->js_prefix.'.php';
		if(is_file($path)){
			$tpl = $this->js_prefix;
		}
		echo "<td valign='top'>";
		$layout->display($tpl);
		echo "</td></tr></table>";
		
		return $this;
	}
	public function tour_unpubl(){
		$vName = 'tour_list';
		$task = 'tour_list';
		$table = '#__bl_tournament';
		$this->contr_unpublish($vName,$task,$table);
	}
	public function tour_publ(){
		$vName = 'tour_list';
		$task = 'tour_list';
		$table = '#__bl_tournament';
		$this->contr_publish($vName,$task,$table);
	}
	public function tour_del(){
		$vName = 'tour_list';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->delTourn($cid);
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=tour_list');
	}
	function tour_save(){
		$vName = 'tour_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTourn();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=tour_list');
	}
	function tour_save_new(){
		$vName = 'tour_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTourn();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=tour_edit&cid[]=0');
	}
	function tour_apply(){
		$vName = 'tour_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTourn();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=tour_edit&cid[]='.$model->_id);
	}
	
	public function season_unpubl(){
		$vName = 'season_list';
		$task = 'season_list';
		$table = '#__bl_seasons';
		$this->contr_unpublish($vName,$task,$table);
	}
	public function season_publ(){
		$vName = 'season_list';
		$task = 'season_list';
		$table = '#__bl_seasons';
		$this->contr_publish($vName,$task,$table);
	}
	public function season_del(){
		$vName = 'season_list';
		$task = 'season_list';
		$table = '#__bl_seasons';
		$this->contr_del($vName,$task,$table);
	}
	function season_save(){
		$vName = 'season_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveSeason();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=season_list');
	}
	function season_save_new(){
		$vName = 'season_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveSeason();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=season_edit&cid[]=0');
	}
	function season_apply(){
		$vName = 'season_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveSeason();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=season_edit&cid[]='.$model->_id);
	}
	function season_ordering(){
		$vName = 'season_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->orderSeason();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=season_list');
	}
	//teams
	
	public function team_del(){
		$vName = 'team_list';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->delTeam($cid);
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=team_list');
	}
	function team_save(){
		$vName = 'team_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTeam();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=team_list');
	}
	function team_save_new(){
		$vName = 'team_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTeam();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=team_edit&cid[]=0');
	}
	function team_apply(){
		$vName = 'team_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTeam();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=team_edit&cid[]='.$model->_id);
	}
	//players
	
	public function player_del(){
		$vName = 'player_list';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->delPlayer($cid);
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=player_list');
	}
	function player_save(){
		$vName = 'player_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->savePlayer();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=player_list');
	}
	function player_save_new(){
		$vName = 'player_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->savePlayer();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=player_edit&cid[]=0');
	}
	function player_apply(){
		$vName = 'player_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->savePlayer();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=player_edit&cid[]='.$model->_id);
	}
	//matchday
	
	public function matchday_del(){
		$this->js_Model("matchday_edit");
		$classname = "matchday_editJSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->deleteMday($cid);
		
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=matchday_list');
	}
	function matchday_save(){
		$vName = 'matchday_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMday();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=matchday_list');
	}
	function matchday_save_new(){
		$vName = 'matchday_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMday();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=matchday_edit&cid[]=0');
	}
	function matchday_apply(){
		$vName = 'matchday_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMday();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=matchday_edit&cid[]='.$model->_id);
	}
	function matchday_ordering(){
		$vName = 'matchday_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->orderMDay();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=matchday_list');
	}
	//match
	function match_save(){
		$vName = 'match_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMatch();
		$mdid = $model->getMdID();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=matchday_edit&cid[]='.$mdid);
	}
	function match_apply(){
		$vName = 'match_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMatch();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=match_edit&cid[]='.$model->_id);
	}
	//events
	
	public function event_del(){
		$vName = 'event_list';
		$task = 'event_list';
		$table = '#__bl_events';
		$this->contr_del($vName,$task,$table);
	}
	function event_save(){
		$vName = 'event_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveEvent();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=event_list');
	}
	function event_save_new(){
		$vName = 'event_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveEvent();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=event_edit&cid[]=0');
	}
	function event_apply(){
		$vName = 'event_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveEvent();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=event_edit&cid[]='.$model->_id);
	}
	function event_ordering(){
		$vName = 'event_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->orderEvent();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=event_list');
	}
	//group
	
	public function group_del(){
		$vName = 'group_list';
		$task = 'group_list';
		$table = '#__bl_groups';
		$this->contr_del($vName,$task,$table);
	}
	function group_save(){
		$vName = 'group_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveGroup();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=group_list');
	}
	function group_save_new(){
		$vName = 'group_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveGroup();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=group_edit&cid[]=0');
	}
	function group_apply(){
		$vName = 'group_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveGroup();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=group_edit&cid[]='.$model->_id);
	}
	function group_ordering(){
		$vName = 'group_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->orderGroup();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=group_list');
	}
	//moderators
	
	public function moder_del(){
		$this->js_Model("moder_edit");
		$classname = "moder_editJSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->deleteModer($cid);
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=moder_list');
	}
	function moder_save(){
		$vName = 'moder_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveModer();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=moder_list');
	}
	function moder_save_new(){
		$vName = 'moder_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveModer();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=moder_edit&cid[]=0');
	}
	function moder_apply(){
		$vName = 'moder_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveModer();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=moder_edit&cid[]='.$model->_id);
	}
	
	//map
	
	public function map_del(){
		$vName = 'map_list';
		$task = 'map_list';
		$table = '#__bl_maps';
		$this->contr_del($vName,$task,$table);
	}
	function map_save(){
		$vName = 'map_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMap();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=map_list');
	}
	function map_save_new(){
		$vName = 'map_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMap();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=map_edit&cid[]=0');
	}
	function map_apply(){
		$vName = 'map_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveMap();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=map_edit&cid[]='.$model->_id);
	}
	//fields
	
	public function fields_del(){
		$vName = 'fields_list';
		$task = 'fields_list';
		$table = '#__bl_extra_filds';
		$this->contr_del($vName,$task,$table);
	}
	function fields_save(){
		$vName = 'fields_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveFields();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=fields_list');
	}
	function fields_save_new(){
		$vName = 'fields_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveFields();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=fields_edit&cid[]=0');
	}
	function fields_apply(){
		$vName = 'fields_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveFields();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=fields_edit&cid[]='.$model->_id);
	}
	function saveorder(){
		$vName = 'fields_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->orderFields();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=fields_list');
	}
	public function fields_unpubl(){
		$vName = 'fields_list';
		$task = 'fields_list';
		$table = '#__bl_extra_filds';
		$this->contr_unpublish($vName,$task,$table);
	}
	public function fields_publ(){
		$vName = 'fields_list';
		$task = 'fields_list';
		$table = '#__bl_extra_filds';
		$this->contr_publish($vName,$task,$table);
	}
	
	//venue
	
	public function venue_del(){
		$vName = 'venue_list';
		$task = 'venue_list';
		$table = '#__bl_venue';
		$this->contr_del($vName,$task,$table);
	}
	function venue_save(){
		$vName = 'venue_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveVenue();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=venue_list');
	}
	function venue_save_new(){
		$vName = 'venue_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveVenue();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=venue_edit&cid[]=0');
	}
	function venue_apply(){
		$vName = 'venue_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveVenue();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=venue_edit&cid[]='.$model->_id);
	}
	//config
	function save_config(){
		$vName = 'config';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveConfig();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=config');
	}
	function template_save(){
		$vName = 'template_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTemplate();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=template_list');
	}

	function template_apply(){
		$vName = 'template_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveTemplate();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=template_edit&cid[]='.$model->_id);
	}
    
    function template_delete(){
        $vName = 'template_edit';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->deleteTemplate();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=template_list');
    }
    
	//config
	function save_betting_config(){
		$vName = 'betting_config';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->saveConfig();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_config');
	}
        
    function betting_edit_points_save(){
        $vName = 'betting_users';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->savePoints();
        $this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_users' );
    }
    
    function betting_cash_requests_doapprove(){
        $vName = 'betting_cash_requests_approve';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->approve();
        $this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_cash_requests_list' );        
    }
    
    function betting_cash_requests_doreject(){
        $vName = 'betting_cash_requests_reject';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->reject();
        $this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_cash_requests_list' );        
    }
    
    function betting_cash_requests_dopostpone(){
        $vName = 'betting_cash_requests_postpone';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->postpone();
        $this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_cash_requests_list' );        
    }
    
    function betting_points_requests_doapprove(){
        $vName = 'betting_points_requests_approve';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->approve();
        $this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_points_requests_list' );
    }
    
    function betting_points_requests_doreject(){
        $vName = 'betting_points_requests_reject';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->reject();
        $this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_points_requests_list' );        
    }
    
    function betting_points_requests_dopostpone(){
        $vName = 'betting_points_requests_postpone';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->postpone();
        $this->mainframe->redirect( 'index.php?option='.$this->option.'&task=betting_points_requests_list' );        
    }
	//about
	function about(){
		JToolBarHelper::title( JText::_( 'BLBE_MENAB' ), 'about.png' );
		?>
			<td valign='top'>
				<?php include_once(JPATH_COMPONENT.DS.'jbl_about.php'); ?>
				</td>
			</tr>
		</table>
		<?php
	}
	//help
	function help(){
		JToolBarHelper::title( JText::_( 'BLBE_MENHLP' ), 'about.png' );
		?>
			<td valign='top'>
				<?php include_once(JPATH_COMPONENT.DS.'jbl_help.php'); ?>
				</td>
			</tr>
		</table>
		<?php
	}
	
	/////Menu/////
	function season_menu(){
		$this->menubox("season_menu");
	}
	function team_menu(){
		$this->menubox("team_menu");
	}
	function match_menu(){
		$this->menubox("match_menu");
	}
	function matchday_menu(){
		$this->menubox("matchday_menu");
	}
	function player_menu(){
		$this->menubox("player_menu");
	}
	function group_menu(){
		$this->menubox("group_menu");
	}
	function venue_menu(){
		$this->menubox("venue_menu");
	}
	
	//addons
	function save_adons(){
		$vName = 'adons';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->addonInstall();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=adons');
	}
	function del_adons(){
		$vName = 'adons';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->addonDel();
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task=adons');
	}
	public function adons_unpubl(){
		$vName = 'adons';
		$task = 'adons';
		$table = '#__bl_addons';
		$this->contr_unpublish($vName,$task,$table);
	}
	public function adons_publ(){
		$vName = 'adons';
		$task = 'adons';
		$table = '#__bl_addons';
		$this->contr_publish($vName,$task,$table);
	}
	//getparcip
	function getparcip(){
		$vName = 'getparcip';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
	}
	//getformat
	function getformat(){
		$vName = 'getformat';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
	}
	
	
	///mains///
	public function menubox($vName){
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		
		$this->js_Layout($vName);
		$classname_l = "JoomsportView".$vName;
		
		$layout = new $classname_l($model);
		echo "<td valign='top'>";
		$layout->display();
		echo "</td></tr></table>";
	}
	public function contr_publish($vName,$task,$table){
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->js_publish($table,$cid);
		
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task='.$task);
	}
	public function contr_unpublish($vName,$task,$table){
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->js_unpublish($table,$cid);
		
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task='.$task);
	}
	public function contr_del($vName,$task,$table){
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$model->js_delete($table,$cid);
		
		$this->mainframe->redirect( 'index.php?option='.$this->option.'&task='.$task);
	}
	public function chkvers(){
		echo  @file_get_contents('http://joomsport.com/index2.php?option=com_chkversion&id=1&no_html=1&tmpl=component');
	}
}