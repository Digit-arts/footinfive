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

class season_editJSModel extends JSPRO_Models
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
		$mainframe = JFactory::getApplication();;
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$is_id = $cid[0];
		$t_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.tfilt_id', 'tfilt_id', 0, 'int' );
		$this->db			=& JFactory::getDBO();
		$row 	= new JTableSeason($this->db);
		$row->load($is_id);
		$this->_data = $row;
		$tour_row 	= new JTableTourn( $this->db);
		$tour_row->load($is_id?$row->t_id:$t_id);
		$published = ($row->s_id) ? $row->published : 1;
		
		$this->_lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $published );
		$s_reg = ($row->s_id) ? $row->s_reg : 0;
		$this->_lists['enbl_reg'] 		= JHTML::_('select.booleanlist',  's_reg', 'class="inputbox"', $s_reg );
		$this->_lists['t_single'] = $tour_row->t_single;
		if($tour_row->t_single == 1){
			
			$query = "SELECT t.id as id FROM #__bl_season_players as st, #__bl_players as t WHERE st.season_id = ".intval($row->s_id)." AND t.id = st.player_id ORDER BY t.first_name";
		
			$this->db->setQuery($query);
		
			$teams_season_ids = $this->db->loadResultArray();
			
			
			$query = "SELECT id,CONCAT(first_name,' ',last_name) as t_name FROM #__bl_players ".(count($teams_season_ids)?"WHERE id NOT IN (".implode(',',$teams_season_ids).")":"")." ORDER BY first_name";
		
			$this->db->setQuery($query);
		
			$teams = $this->db->loadObjectList();
		
			$this->_lists['teams'] = @JHTML::_('select.genericlist',   $teams, 'teams_id', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		
			$query = "SELECT t.id as id, CONCAT(t.first_name,' ',t.last_name) as t_name FROM #__bl_season_players as st, #__bl_players as t WHERE st.season_id = ".$row->s_id." AND t.id = st.player_id ORDER BY t.first_name";
		
			$this->db->setQuery($query);
		
			$teams_season = $this->db->loadObjectList();
		
			$this->_lists['teams2'] = @JHTML::_('select.genericlist',   $teams_season, 'teams_season[]', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
			
			//
			$query = "SELECT t.id as id, CONCAT(t.first_name,' ',t.last_name) as t_name FROM #__bl_season_players as st, #__bl_players as t WHERE st.season_id = ".$row->s_id." AND t.id = st.player_id AND st.regtype='1' ORDER BY t.first_name";
		
			$this->db->setQuery($query);
		
			$this->_lists["teams_regs"] = $this->db->loadObjectList();
			
		}else{
			
			$query = "SELECT t.id as id FROM #__bl_season_teams as st, #__bl_teams as t WHERE st.season_id = ".$row->s_id." AND t.id = st.team_id ORDER BY t.t_name";
		
			$this->db->setQuery($query);
		
			$teams_season_ids = $this->db->loadResultArray();
			
			$query = "SELECT * FROM #__bl_teams ".(count($teams_season_ids)?"WHERE id NOT IN (".implode(',',$teams_season_ids).")":"")." ORDER BY t_name";
		
			$this->db->setQuery($query);
		
			$teams = $this->db->loadObjectList();
		
			$this->_lists['teams'] = @JHTML::_('select.genericlist',   $teams, 'teams_id', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		
			$query = "SELECT t.id as id, t.t_name as t_name FROM #__bl_season_teams as st, #__bl_teams as t WHERE st.season_id = ".$row->s_id." AND t.id = st.team_id ORDER BY t.t_name";
		
			$this->db->setQuery($query);
		
			$teams_season = $this->db->loadObjectList();
		
			$this->_lists['teams2'] = @JHTML::_('select.genericlist',   $teams_season, 'teams_season[]', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		
			$query = "SELECT t.id as id, t.t_name as t_name FROM #__bl_season_teams as st, #__bl_teams as t WHERE st.season_id = ".$row->s_id." AND t.id = st.team_id AND st.regtype='1' ORDER BY t.t_name";
		
			$this->db->setQuery($query);
		
			$this->_lists["teams_regs"] = $this->db->loadObjectList();
		}
		
		
		$this->_lists['tourn'] = '<input type="hidden" name="t_id" value="'.$tour_row->id.'" />'.$tour_row->name;//JHTML::_('select.genericlist',   $tourn_is, 't_id', 'class="inputbox" size="1" readonly="readonly"', 'id', 'name', $tour_row->id );
		
		$this->_lists['s_groups'] 		= JHTML::_('select.booleanlist',  's_groups', 'class="inputbox"', $row->s_groups );
		
		
		//
		$this->_lists["soptions"] = array();	
			$query = "SELECT * FROM #__bl_season_option WHERE s_id = ".$row->s_id."";
			$this->db->setQuery($query);
			$listsss = $this->db->loadObjectList();
			for($i=0;$i<count($listsss);$i++){
				$vars = get_object_vars( $listsss[$i]  );
				$this->_lists["soptions"][$vars["opt_name"]] = $vars["opt_value"];
			}
		//

		//----colors----//
		$query = "SELECT * FROM #__bl_tblcolors WHERE s_id=".$row->s_id." ORDER BY place";
		$this->db->setQuery($query);
		$this->_lists['colors'] = $this->db->loadObjectList();
		//===access====//
		
		$this->getSeasmoder($row->s_id);
		//maps
		$this->getMaps($row->s_id);
		
		
		$this->_lists['tourntype'] = $tour_row->t_single;
		$this->_lists['t_type'] = $tour_row->t_type;
		
		///----list of ranking criteria----///
		$this->getSortWay($row->s_id);
		
		///-----EXTRAFIELDS---//
		$this->_lists['ext_fields'] = $this->getAdditfields(3,$row->s_id);
		///--Betting
		$this->_lists['is_betting'] = $this->addonexist('betting');
		if($this->_lists['is_betting']){
			$this->_lists['templates'] = $this->getTemplateList($row->idtemplate);
		}
		
		
	}
        
	public function getTemplateList($id) {
		$q = "SELECT id, name FROM #__bl_betting_templates blbt";
		$this->db->setQuery($q);
		$templates = $this->db->loadObjectList();
		$is_template[] = JHTML::_('select.option', 0, JText::_('BLBE_BET_TEMPLATES'), 'id', 'name');
		$templates = array_merge($is_template, $templates);
		return JHTML::_('select.genericlist', $templates, 'idtemplate', 'class="inputbox" size="1"' . '', 'id', 'name', $id);
	}

	public function orderSeason(){
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array(), 'post', 'array' );
		
		$row		= new JTableSeason($this->db);;
		$total		= count( $cid );
		
		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			
				
			}
		}
	}
	function saveSeason(){
		
		
		$post		= JRequest::get( 'post' );
		$post['s_enbl_extra'] = JRequest::getVar( 's_enbl_extra', 0, 'post', 'int' );
		$post['s_descr'] = JRequest::getVar( 's_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['s_rules'] = JRequest::getVar( 's_rules', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$row 	= new JTableSeason($this->db);
		$soptions = JRequest::getVar( 'soptions', array(0), 'post', 'array' );
		
		
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
		
		$tour_row 	= new JTableTourn( $this->db);
		$tour_row->load($row->t_id);
		
		
		$teams_season 		= JRequest::getVar( 'teams_season', array(0), '', 'array' );
		
		JArrayHelper::toInteger($teams_season, array(0));
		
		if($tour_row->t_single){
			
			$arr_new = array();
		
			if(count($teams_season)){
				foreach($teams_season as $teams){
					$query = "INSERT INTO #__bl_season_players(season_id,player_id) VALUES(".$row->s_id.",".$teams.")";
					$this->db->setQuery($query);
					$this->db->query();
					$arr_new[] = $teams;
				}
			}
			if(count($arr_new)){
				$query = "DELETE FROM #__bl_season_players WHERE season_id = ".$row->s_id."  AND player_id NOT IN (".implode(',',$arr_new).")";
				$this->db->setQuery($query);
			
				$this->db->query();
			}
		}else{
			//$query = "DELETE FROM #__bl_season_teams WHERE season_id = ".$row->s_id;
			//$this->db->setQuery($query);
		
			//$this->db->query();
		
			$arr_new = array();
		
			if(count($teams_season)){
				foreach($teams_season as $teams){
					$query = "INSERT INTO #__bl_season_teams(season_id,team_id) VALUES(".$row->s_id.",".$teams.")";
					$this->db->setQuery($query);
					$this->db->query();
					
					$arr_new[] = $teams;
				}
			}
			if(count($arr_new)){
				$query = "DELETE FROM #__bl_season_teams WHERE season_id = ".$row->s_id."  AND team_id NOT IN (".implode(',',$arr_new).")";
				$this->db->setQuery($query);
			
				$this->db->query();
			}
		
		}
		
		
	///------------access---
		$query = "DELETE FROM #__bl_feadmins WHERE season_id = ".$row->s_id;
		$this->db->setQuery($query);
		$this->db->query();
		$usr_admins 		= JRequest::getVar( 'usr_admins', array(0), '', 'array' );
		JArrayHelper::toInteger($usr_admins, array(0));
		if(count($usr_admins)){
			foreach($usr_admins as $usrz){
				$query = "INSERT INTO #__bl_feadmins(season_id,user_id) VALUES(".$row->s_id.",".$usrz.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}	
	///------------MAPS---
		$query = "DELETE FROM #__bl_seas_maps WHERE season_id = ".$row->s_id;
		$this->db->setQuery($query);
		$this->db->query();
		$maps 		= JRequest::getVar( 'maps_s', array(0), '', 'array' );
		JArrayHelper::toInteger($maps, array(0));
		if(count($maps)){
			foreach($maps as $map){
				$query = "INSERT INTO #__bl_seas_maps(season_id,map_id) VALUES(".$row->s_id.",".$map.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}			
		//season params
		$query = "DELETE FROM #__bl_season_option WHERE s_id = ".$row->s_id;
		$this->db->setQuery($query);
		$this->db->query();
		
		if(count($soptions)){
			foreach($soptions as $key=>$value){
				$query = "INSERT INTO #__bl_season_option(s_id,opt_name,opt_value) VALUES({$row->s_id},".stripslashes($key).",'".($value)."')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	
		
		///-------------colors---------//
		$query = "DELETE FROM #__bl_tblcolors WHERE s_id=".$row->s_id;
		$this->db->setQuery($query);
		$this->db->query();
		$rowcount = JRequest::getVar( 'col_count', 0, 'post', 'int' );
		for($z=1;$z<$rowcount+1;$z++){
			if($_POST['place_'.$z] && $_POST['input_field_'.$z]){
				$query = "INSERT INTO #__bl_tblcolors(s_id,place,color) VALUES(".$row->s_id.",'".$_POST['place_'.$z]."','".$_POST['input_field_'.$z]."')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		///--------------Ranking sort---------------////
		
		$query = "SELECT opt_value FROM #__bl_season_option WHERE s_id = ".$s_id." AND opt_name='equalpts_chk'";
		$this->db->setQuery($query);
		
		$query = "DELETE FROM #__bl_ranksort WHERE seasonid = ".$row->s_id;
		$this->db->setQuery($query);
		$this->db->query();
		$i=0;
		$sortfield 		= JRequest::getVar( 'sortfield', array(0), '', 'array' );
		JArrayHelper::toInteger($sortfield, array(0));
		if(count($sortfield)){
			foreach($sortfield as $usrz){
				if($usrz){
					$query = "INSERT INTO #__bl_ranksort(seasonid,sort_field,sort_way,ordering) VALUES(".$row->s_id.",'".$usrz."','".intval($_POST['sortway'][$i])."',".$i.")";
					$this->db->setQuery($query);
					$this->db->query();
					$i++;
				}
			}
		}
		
		//-------extra fields-----------//
		if(isset($_POST['extraf']) && count($_POST['extraf'])){
			foreach($_POST['extraf'] as $p=>$dummy){
				$query = "DELETE FROM #__bl_extra_values WHERE f_id = ".$_POST['extra_id'][$p]." AND uid = ".$row->s_id;
				$this->db->setQuery($query);
				$this->db->query();
				if($_POST['extra_ftype'][$p] == '2'){
					$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text) VALUES(".$_POST['extra_id'][$p].",".$row->s_id.",'".addslashes($_POST['extraf'][$p])."')";
				}else{
					$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue) VALUES(".$_POST['extra_id'][$p].",".$row->s_id.",'".$_POST['extraf'][$p]."')";
				}
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		$this->_id = $row->s_id;
	}
	
	protected function getSeasmoder($s_id)
	{
		$query = "SELECT u.id as id FROM #__users as u, #__bl_feadmins as f WHERE f.user_id = u.id AND f.season_id=".$s_id." ORDER BY u.username";
		$this->db->setQuery($query);
		$f_admins_ids = $this->db->loadResultArray();
		
		$query = "SELECT * FROM #__users ".(count($f_admins_ids)?"WHERE id NOT IN (".implode(',',$f_admins_ids).")":"")." ORDER BY username";
		$this->db->setQuery($query);
		$f_users = $this->db->loadObjectList();
		$this->_lists['usrlist'] = @JHTML::_('select.genericlist',   $f_users, 'usracc_id', 'class="inputbox" size="10" multiple', 'id', 'username', 0 );
		$query = "SELECT u.id as id, u.username as t_name FROM #__users as u, #__bl_feadmins as f WHERE f.user_id = u.id AND f.season_id=".$s_id." ORDER BY u.username";
		$this->db->setQuery($query);
		$f_admins = $this->db->loadObjectList();
		$this->_lists['usrlist_vyb'] = @JHTML::_('select.genericlist',   $f_admins, 'usr_admins[]', 'class="inputbox" size="10" multiple', 'id', 't_name', 0 );
		
	}
	protected function getMaps($s_id)
	{
		$maps[] = JHTML::_('select.option',  0, JText::_('BLBE_SELMAP'), 'id', 'm_name' ); 
		$query = "SELECT * FROM #__bl_maps ORDER BY m_name";
		$this->db->setQuery($query);
		$dbmaps = $this->db->loadObjectList();
		if(count($dbmaps)){
			$maps = array_merge($maps,$dbmaps);
		}
		$this->_lists['maps'] = JHTML::_('select.genericlist',   $maps, 'maps_id', 'class="inputbox" size="1"', 'id', 'm_name', 0 );
		
		$query = "SELECT m.* FROM #__bl_seas_maps as s, #__bl_maps as m WHERE s.map_id=m.id AND s.season_id = ".$s_id." ORDER BY m.id";
		$this->db->setQuery($query);
		$this->_lists['cur_maps'] = $this->db->loadObjectList();
	}
	protected function getSortWay($s_id){
		$sortway = array();
		$sortway[] = JHTML::_('select.option',  0, JText::_('desc'), 'id', 'name' );
		$sortway[] = JHTML::_('select.option',  1, JText::_('asc'), 'id', 'name' ); 
		 
		
		$this->_lists['sortway'] = $sortway;//JHTML::_('select.genericlist',   $sortway, 'sortway[]', 'class="inputbox"', 'id', 'name', 0 );
		
		$sortfield[] = JHTML::_('select.option',  0, JText::_('No'), 'id', 'name' ); 
		$sortfield[] = JHTML::_('select.option',  1, JText::_('BLBE_SELPOINTS'), 'id', 'name' ); 
		$sortfield[] = JHTML::_('select.option',  2, JText::_('BLBE_SELWPC'), 'id', 'name' ); 
		//$sortfield[] = JHTML::_('select.option',  3, JText::_('If equal points/win percent games between teams'), 'id', 'name' ); 
		$sortfield[] = JHTML::_('select.option',  4, JText::_('BLBE_SELGD'), 'id', 'name' );
		$sortfield[] = JHTML::_('select.option',  5, JText::_('BLBE_SELGS'), 'id', 'name' ); 
		$sortfield[] = JHTML::_('select.option',  6, JText::_('BLBE_PLAYED'), 'id', 'name' ); 
		
		$this->_lists['sortfield'] = $sortfield;//JHTML::_('select.genericlist',   $sortfield, 'sortfield[]', 'class="inputbox"', 'id', 'name', 0 );
		
		$query = "SELECT * FROM #__bl_ranksort WHERE seasonid=".$s_id." ORDER BY ordering";
		$this->db->setQuery($query);
		$this->_lists['savedsort'] = $this->db->loadObjectList();
		
		$query = "SELECT opt_value FROM #__bl_season_option WHERE s_id = ".$s_id." AND opt_name='equalpts_chk'";
		$this->db->setQuery($query);
		$this->_lists['equalpts_chk'] = $this->db->loadResult();
	}
}