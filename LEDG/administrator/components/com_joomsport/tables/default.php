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
class JTableMaps extends JTable
{
	var $id				= null;
	var $m_name 		= null;
	var $map_descr		= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_maps', 'id', $db );
	}
}
class JTableTourn extends JTable
{
	var $id				= null;
	var $name 			= null;
	var $descr			= null;
	var $published		= null;
	var $t_type			= null;
	var $t_single		= null;
	var $logo			= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_tournament', 'id', $db );
	}
}
class JTableSeason extends JTable
{
	var $s_id				= null;
	var $s_name 			= null;
	var $s_descr			= null;
	var $published			= null;
	var $s_rounds			= null;
	var $t_id				= null;
	var $s_win_point		= null;
	var $s_lost_point		= null;
	var $s_enbl_extra		= null;
	var $s_extra_win		= null;
	var $s_extra_lost		= null;
	var $s_draw_point		= null;
	var $s_groups			= null;
	var $s_win_away			= null;
	var $s_draw_away		= null;
	var $s_lost_away		= null;
	var $s_participant		= null;
	var $s_reg				= null;
	var $reg_start			= null;
	var $reg_end			= null;
	var $s_rules			= null;
	var $idtemplate			= null;

	function __construct( &$db )
	{
		parent::__construct( '#__bl_seasons', 's_id', $db );
	}
}
class JTableTeams extends JTable
{
	var $id					= null;
	var $t_name 			= null;
	var $t_descr			= null;
	var $t_yteam			= null;
	var $def_img			= null;
	var $t_emblem			= null;
	var $t_city				= null;
	var $created_by			= null;
	var $venue_id			= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_teams', 'id', $db );
	}
}
class JTablePos extends JTable
{
	var $p_id				= null;
	var $p_name 			= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_positions', 'p_id', $db );
	}
}
class JTablePlayer extends JTable
{
	var $id						= null;
	var $first_name 			= null;
	var $last_name 				= null;
	var $nick 					= null;
	var $about 					= null;
	var $position_id 			= null;
	var $def_img 				= null;
	var $usr_id					= null;
	var $country_id				= null;
	var $registered				= null;
	var $team_id				= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_players', 'id', $db );
	}
}
class JTablePhotos extends JTable
{
	var $id						= null;
	var $ph_name 				= null;
	var $ph_filename 			= null;
	var $ph_descr 				= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_photos', 'id', $db );
	}
}
class JTableMday extends JTable
{
	var $id						= null;
	var $m_name 				= null;
	var $m_descr 				= null;
	var $s_id					= null;
	var $is_playoff				= null;
	var $k_format				= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_matchday', 'id', $db );
	}
}
class JTableMatch extends JTable
{
	var $id						= null;
	var $m_id 					= null;
	var $team1_id 				= null;
	var $team2_id				= null;
	var $score1 				= null;
	var $score2 				= null;
	var $match_descr			= null;
	var $published				= null;
	var $is_extra				= null;
	var $m_played				= null;
	var $m_date					= null;
	var $m_time					= null;
	var $m_location				= null;
	var $k_ordering				= null;
	var $k_title				= null;
	var $k_stage				= null;
	var $points1				= null;
	var $points2				= null;
	var $new_points				= null;
	var $bonus1					= null;
	var $bonus2					= null;
	var $aet1					= null;
	var $aet2					= null;
	var $p_winner				= null;
	var $venue_id				= null;
	var $m_single				= null;
	var $betavailable           = null;
    var $betfinishdate          = null;
    var $betfinishtime          = null;
	
	function __construct( &$db )
	{
		parent::__construct( '#__bl_match', 'id', $db );
	}
}
class JTableEvents extends JTable
{
	var $id						= null;
	var $e_name 				= null;
	var $e_img					= null;
	var $e_descr				= null;
	var $player_event			= null;
	var $result_type			= null;
	var $sumev1					= null;
	var $sumev2					= null;
	
	function __construct( &$db )
	{
		parent::__construct( '#__bl_events', 'id', $db );
	}
}
class JTableGroups extends JTable
{
	var $id						= null;
	var $group_name				= null;
	var $s_id					= null;
	
	function __construct( &$db )
	{
		parent::__construct( '#__bl_groups', 'id', $db );
	}
}
class JTableFields extends JTable
{
	var $id						= null;
	var $name					= null;
	var $published				= null;
	var $type					= null;
	var $ordering				= null;
	var $e_table_view			= null;
	var $field_type				= null;
	var $reg_exist				= null;
	var $reg_require			= null;
	var $fdisplay				= null;
	var $faccess				= null;
	
	function __construct( &$db )
	{
		parent::__construct( '#__bl_extra_filds', 'id', $db );
	}
}
class JTableLang extends JTable
{
	var $id						= null;
	var $lang_file				= null;
	var $is_default				= null;
	
	function __construct( &$db )
	{
		parent::__construct( '#__bl_languages', 'id', $db );
	}
	
	function check() {
		$db			=& JFactory::getDBO();
		$query = "SELECT lang_file FROM #__bl_languages WHERE id = '".$this->id."'";
		$db->SetQuery( $query );
		$old_name = $db->LoadResult();
		if (isset($old_name) && $old_name == 'default') {
			$this->setError('Could not modify DEFAULT Language');
			return false;
		} 
		$query = "SELECT count(*) FROM #__bl_languages WHERE id <> '".$this->id."' and lang_file = '".$this->lang_file."'";
		$db->SetQuery( $query );
		$items_count = $db->LoadResult();
		if ($items_count > 0) {
			$this->setError('This name for Language is already exist');
			return false;
		} 
		if ((trim($this->lang_file == '')) || (preg_match("/[0-9a-z]/", $this->lang_file ) == false)) {
			$this->setError('Please enter valid Language name');
			return false;
		} 
		return true;
	}
}

class JTableVenue extends JTable
{
	var $id					= null;
	var $v_name 			= null;
	var $v_descr			= null;
	var $v_defimg			= null;
	var $v_address			= null;
	var $v_coordx			= null;
	var $v_coordy			= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_venue', 'id', $db );
	}
}

class JTableTempl extends JTable
{
	var $id					= null;
	var $name 				= null;
	var $isdefault			= null;
	var $variable1			= null;
	var $variable2			= null;
	var $variable3			= null;
	var $variable4			= null;
	var $variable5			= null;
	var $variable6			= null;
	var $variable7			= null;
	function __construct( &$db )
	{
		parent::__construct( '#__bl_templates', 'id', $db );
	}
}

class JTableTemplates extends JTable{
    var $id             = null;
    var $name           = null;
    var $description    = null;
    var $isdeleted      = null;
    
    function __construct( &$db ){
            parent::__construct( '#__bl_betting_templates', 'id', $db );
    }    
}

class JTableBettingEvents extends JTable{
    var $id             = null;
    var $name           = null;
    var $type           = null;
    var $difffrom       = null;
    var $diffto         = null;
    var $isdeleted      = null;
    
    function __construct( &$db ){
            parent::__construct( '#__bl_betting_events', 'id', $db );
    }    
}

class JTableBettingLogs extends JTable{
    var $id             = null;
    var $iduser           = null;
    var $points         = null;
    var $date      = null;
    
    function __construct( &$db ){
            parent::__construct( '#__bl_betting_logs', 'id', $db );
    }
    
    public function addToLog($iduser, $points){
        $this->iduser = $iduser;
        $this->points = $points;
        $this->date = date('Y-m-d H:i:s');
        $this->store();
    }
}

class JTableBettingUsers extends JTable{
    var $id             = null;
    var $iduser           = null;
    var $points         = null;
    
    function __construct(&$db) {
        parent::__construct('#__bl_betting_users', 'id', $db);
    }
    
    
    public function changePoints($points) {
        $this->points += $points;
        $this->store();
    }
}

class JTableBettingCashRequests extends JTable{
    var $id = null;
    var $iduser = null;
    var $points = null;
    var $status = null;
    
    function __construct(&$db) {
        parent::__construct('#__bl_betting_requests_cash', 'id', $db);
    }
    
    public function getStatuses(){
        return array('pending', 'approved', 'rejected', 'postponed');
    }
}

class JTableBettingPointsRequests extends JTable{
    var $id = null;
    var $iduser = null;
    var $points = null;
    var $status = null;
    
    function __construct(&$db) {
        parent::__construct('#__bl_betting_requests_points', 'id', $db);
    }
    
    public function getStatuses(){
        return array('pending', 'approved', 'rejected', 'postponed');
    }    
}

class JTableBettingCoeffs extends JTable{
    var $id = null;
    var $idmatch = null;
    var $idevent = null;
    var $coeff1 = null;
    var $coeff2 = null;
    var $betfinishdate = null;
    var $betfinishtime = null;
    
    function __construct(&$db) {
        parent::__construct('#__bl_betting_coeffs', 'id', $db);
    }
}

class JTableBettingUsersBets extends JTable{
    var $id = null;
    var $iduser = null;
    var $won = null;
    
    function __construct(&$db) {
        parent::__construct('#__bl_betting_users_bets', 'id', $db);
    }
}

class JTableBettingBets extends JTable{
    var $id = null;
    var $idbet = null;
    var $idmatch = null;
    var $idevent = null;
    var $who = null;
    var $points = null;
    
    function __construct(&$db) {
        parent::__construct('#__bl_betting_bets', 'id', $db);
    }
}
?>