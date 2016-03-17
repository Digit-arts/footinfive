<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class JSPRO_Models{
	
	public $db = null;
	public $uri = null;
	public $mainframe = null;
	public $document = null;
	protected $js_table = null;
	public $jstab = null;
	function __construct()
	{
		$this->db		=& JFactory::getDBO();
		$this->uri		=& JFactory::getURI();
		$this->mainframe = JFactory::getApplication();
		$this->document =& JFactory::getDocument();
		
	}
	
	function JS_PageTitle($p_title){
		$pathway  =& $this->mainframe->getPathway();
		$params	= &$this->mainframe->getParams();
		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();
		if (is_object( $menu )) {
			$menu_params = new JRegistry;
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	JText::_( $p_title ));
			}
		} else {
			$params->set('page_title',	JText::_( $p_title ));
		}
		$this->document->setTitle( $params->get( 'page_title' ) );
		$pathway->addItem( JText::_( $p_title ));
		return $params;
	}
	
	function unblSeasonReg(){
		$unable_reg = 0;
		if($this->s_id == -1) 
		{	
			return 0;
		}	
		$tourn = $this->getTournOpt($this->s_id);
		$season_par = $this->getSParametrs($this->s_id);
		$this->_lists['season_par'] = $season_par;
		$this->_lists["enbl_extra"] = $season_par->s_enbl_extra;
		$reg_start = mktime(substr($season_par->reg_start,11,2),substr($season_par->reg_start,14,2),0,substr($season_par->reg_start,5,2),substr($season_par->reg_start,8,2),substr($season_par->reg_start,0,4));
		$reg_end = mktime(substr($season_par->reg_end,11,2),substr($season_par->reg_end,14,2),0,substr($season_par->reg_end,5,2),substr($season_par->reg_end,8,2),substr($season_par->reg_end,0,4));
		
		if($tourn->t_single){
			$query = "SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".$this->s_id;
		}else{
			$query = "SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".$this->s_id;
		}
		$this->db->setQuery($query);
		$part_count = $this->db->loadResult();
		
		if($season_par->s_reg && ($part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))){
			$unable_reg = 1;
		}
		return $unable_reg;
	}
	
	function teamsToModer(){
		$user	=& JFactory::getUser();
		if($user->id){
			$query = "SELECT t.id FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid=".$user->id." ORDER BY t.t_name";
			$this->db->setQuery($query);	
			$teams_season = $this->db->loadResultArray();
		}else{
			$teams_season = array();
		}
		return $teams_season;
	}
	
	function set_JS_tabs(){
	
		require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
		$this->jstab = new esTabs();
	
	}
	
	function get_db_Table(){
		$this->js_table = '';
	}
	
	public function set($property, $value = null)
	{
		$previous = isset($this->$property) ? $this->$property : null;
		$this->$property = $value;
		return $previous;
	}
	public function get($property, $default=null)
	{
		if (isset($this->$property)) {
			return $this->$property;
		}
		return $default;
	}
	
	function date_bl($date,$time){
		
		$format = "%d-%m-%Y %H:%M";
		if($date == '' || $date == '0000-00-00'){
			return '';
		}

		$format = $this->getJS_Config('date_format');
		switch ($format){
			case "d-m-Y H:i": $format = "%d-%m-%Y %H:%M"; break;
			case "m-d-Y g:i A": $format = "%m-%d-%Y %I:%M %p"; break;
			case "j F, Y H:i": $format = "%m %B, %Y %H:%M"; break;
			case "j F, Y g:i A": $format = "%m %B, %Y %I:%H %p"; break;
			case "d-m-Y": $format = "%d-%m-%Y"; break;
			case "l d F, Y H:i": $format = "%A %d %B, %Y  %H:%M"; break;
		}
		
		if(!$time){
			$time = '00:00';
		}
		$time_m = explode(':',$time);
		$date_m = explode('-',$date);

		if(function_exists('date_default_timezone_set')){
			date_default_timezone_set('GMT');
		}
		$tm = @mktime($time_m[0],$time_m[1],'0',$date_m[1],$date_m[2],$date_m[0]);
		jimport('joomla.utilities.date');
		$dt = new JDate($tm,0);
		return $dt->toFormat($format);
		
	}
	function getePanel($team,$reg,$sid,$cal = 0,$inv=0){
		
		$Itemid = JRequest::getInt('Itemid');
		
		$team_reg = $this->getJS_Config('team_reg');
		
		$link2 = JRoute::_('index.php?option=com_joomsport&amp;view=seasonlist&limitstart=0&Itemid='.$Itemid);
		
		
		$kl ='<div class="module-header">'
			.'<a class="module-logo" href="'.$link2.'" title="LEDG"><img src="components/com_joomsport/img/logo.png" /></a>'
			.'<ul class="module-menu">';
			
		if(isset($team[0])){
			$link = JRoute::_('index.php?option=com_joomsport&view=moderedit_team&tid='.$team[0].'&controller=moder&Itemid='.$Itemid);
			$kl .= '<li><a href="'.$link.'" title="'.JText::_('BLFA_YTEAM').'"><span class="module-menu-manage-team">'.JText::_('BLFA_YTEAM').'</span></a></li>';
		}
		$tr = false;
		$_users	=& JFactory::getUser();
		$query = "Select * FROM #__bl_players WHERE usr_id=".$_users->id;
		$this->db->setQuery($query);
		$usr  = $this->db->loadObject();
		
		if(!$this->getJS_Config('player_reg') && $usr && $_users->id){
			$tr = true;
		}
		if($this->getJS_Config('player_reg')){
			$tr = true;
		}
		if($team_reg && $tr){
			$link = JRoute::_('index.php?option=com_joomsport&amp;task=regteam&Itemid='.$Itemid);
			$kl .= '<li><a href="'.$link.'" title="'.JText::_('BLFA_NTEAM').'"><span class="module-menu-new-team">'.JText::_('BLFA_NTEAM').'</span></a></li>';
		}
		if($cal && $sid > 0){
			$kl .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&amp;task=calendar&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_CALENDAR').'"><span class="module-menu-calendar">'.JText::_('BLFA_CALENDAR').'</span></a></li>';
		} 
		if($reg && $tr){
			$kl .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&amp;task=join_season&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_REGGG').'"<span class="module-menu-join-season">'.JText::_('BLFA_REGGG').'</span></a></li>';
		}
		if($inv && $this->getJS_Config('esport_join_team') && $sid && $tr){
			$kl .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&amp;task=jointeam&amp;sid='.$sid.'&amp;tid='.$inv.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_PLJOINTEAM').'"><span class="module-menu-editor">'.JText::_('BLFA_PLJOINTEAM').'</span></a></li>';
		}
		if($this->getJS_Config('player_reg')){
			$link = JRoute::_('index.php?option=com_joomsport&amp;task=regplayer&Itemid='.$Itemid);
			$kl .= '<li><a href="'.$link.'" title="'.JText::_('BLFA_EDITFIPROF').'"><span class="module-menu-join"><!-- --></span></a><span class="twice-border"></span></li>';
		}
		$kl .= '</ul></div><div class="under-module-header"></div>';
		return $kl;
	}

	//2.0.1
	//type 0 - player, 1 - team, 2-match.
	function getAddFields($id,$type,$suff,$sid=0){
		$user	=& JFactory::getUser();
	
		$query = "SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$id." WHERE ef.published=1 AND ef.fdisplay = '1' AND ef.type = '".$type."' ".($user->id?"":" AND ef.faccess='0'")." ORDER BY ef.ordering";
		if($type == 1){
			$query = "SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?$id:-1)." WHERE ef.published=1 AND ef.fdisplay = '1' AND ef.type='".$type."' ".($user->id?"":" AND ef.faccess='0'")." AND ev.season_id={$sid} ORDER BY ef.ordering";
			$this->db->setQuery($query);
			$ext_fields_teams = $this->db->loadObjectList();
			if(!count($ext_fields_teams)){
				$query = "SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?$id:-1)." WHERE ef.published=1 AND ef.fdisplay = '1' AND ef.type='".$type."' ".($user->id?"":" AND ef.faccess='0'")." AND ev.season_id=0 ORDER BY ef.ordering";
			}
		}
		$this->db->setQuery($query);
		$res = $this->db->loadObjectList();
		
		$mj=0;
			if(isset($res)){
				foreach ($res as $extr){
				
					if($extr->field_type == '3'){
						$query = "SELECT sel_value FROM #__bl_extra_select WHERE id='".$extr->fvalue."'";
						$this->db->setQuery($query);
						$selvals = $this->db->loadResult();
						if(isset($selvals) && $selvals){
							$res[$mj]->selvals = $selvals;
						}else{
							$res[$mj]->fvalue = '';
						}
					}
					if($extr->field_type == '1'){
						$res[$mj]->selvals	= $extr->fvalue?JText::_("Yes"):JText::_("No");
						$res[$mj]->fvalue = $res[$mj]->selvals;
					}
					if($extr->field_type == '2'){
						$res[$mj]->fvalue	= $extr->fvalue_text;
					}
					if($extr->field_type == '4' && $res[$mj]->fvalue){
						$res[$mj]->fvalue	= "<a target='_blank' href='".(substr($extr->fvalue,0,7)=='http://'?$extr->fvalue:"http://".$extr->fvalue)."'>".$extr->fvalue."</a>";
					}
					$mj++;
				}
			}
		if(count($res)){
			
			return $this->_getEFview($res, $suff);
		}		
	}
	function getBEAdditfields($type, $id, $sid=0){
		$query = "SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?$id:-1)." ".($sid?" AND ev.season_id=".$sid:"")." WHERE ef.published=1 AND ef.type='".$type."' ORDER BY ef.ordering";
		$this->db->setQuery($query);
		$ext_fields = $this->db->loadObjectList();
		$mj=0;
		if(isset($ext_fields)){
			foreach ($ext_fields as $extr){
				if($extr->field_type == '3'){
					$tmp_arr = array();
					$query = "SELECT * FROM #__bl_extra_select WHERE fid=".$extr->id;
					$this->db->setQuery($query);
					$selvals = $this->db->loadObjectList();
					if(count($selvals)){
						$tmp_arr[] = JHTML::_('select.option',  0, JText::_('BLBE_SELECTVALUE'), 'id', 'sel_value' ); 
						$selvals = array_merge($tmp_arr,$selvals);
						$ext_fields[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="styled-long" size="1"', 'id', 'sel_value', $extr->fvalue );
					}
				}
				if($extr->field_type == '1'){
					$ext_fields[$mj]->selvals	= JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue );
				}
				$mj++;
			}
		}
		return $ext_fields;
	}
	function _getEFview($res, $suff){
		$view_html = '';
		for ($p=0;$p<count($res);$p++){
			 if($res[$p]->fvalue){
		
				$view_html .= '<tr class="js_eftr_'.$suff.'">';
				$view_html .= '<td class="js_eftd_'.$suff.'" valign="top">';
				$view_html .= $res[$p]->name." :";
				$view_html .= '</td>';
				$view_html .= '<td>';
					
				
					switch($res[$p]->field_type){
							
						case '1':	$view_html .=  $res[$p]->selvals;
									break;
						case '2':	$view_html .=  (isset($res[$p]->fvalue)?($res[$p]->fvalue):"");
									break;
						case '3':	$view_html .=  $res[$p]->selvals;
									break;	
						case '4':	$view_html .=  $res[$p]->fvalue;
									break;	
						case '0':					
						default:	$view_html .=  (isset($res[$p]->fvalue)?htmlspecialchars($res[$p]->fvalue):"");		
									break;
							
					}
			
				$view_html .= '</td>';
				$view_html .= '</tr>';
		
			}
		}
		return $view_html;
	}

	function getVer(){
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		return substr($joomla,0,3);
	}
	function getImgPop($img){
		$max_height = 500;
		$max_width = 600;
		$link = JURI::base().'media/bearleague/'.$img;
		$fileDetails = pathinfo(JURI::base().'media/bearleague/'.$img);
		if(!function_exists('imagecreatefromgif')) {
			$img_types = array('png','gif','jpg','jpeg');
		}else{
			$img_types = array('png','gif','jpg','jpeg');
		}
		
			$ext = strtolower($fileDetails["extension"]);
		
		if (is_file(JPATH_ROOT.'/media/bearleague/'.$img) && in_array(strtolower($ext),$img_types)){
			$size = getimagesize(JPATH_ROOT.'/media/bearleague/'.$img);
			
			if($size[0] > $max_width && $size[0] > $size[1]){
				$link = JURI::base().'components/com_joomsport/includes/imgres.php?src='.$link.'&w='.$max_width;
			}else if($size[1] > $max_height && $size[1] > $size[0]){
				$link =JURI::base().'components/com_joomsport/includes/imgres.php?src='.$link.'&h='.$max_height;
			}
		}
		
		return $link;
	}

	function getJS_Location($id){
	
		$Itemid = JRequest::getInt('Itemid');

		$unbl_venue = $this->getJS_Config('unbl_venue');
		
		$query = "SELECT m_location FROM #__bl_match WHERE id=".$id;
		$this->db->setQuery($query);
		$loc = $this->db->loadResult();
		if($unbl_venue){
			$query = "SELECT v.* FROM #__bl_match as m LEFT JOIN #__bl_venue as v ON m.venue_id=v.id WHERE m.id=".$id;
			$this->db->setQuery($query);
			$ven = $this->db->loadObject();
			if($ven->v_name){
				$link = JRoute::_("index.php?option=com_joomsport&task=venue&id=".$ven->id."&Itemid=".$Itemid);
				$loc = '<a href="'.$link.'" title="'.$ven->v_name.'">'.$ven->v_name.'</a>';
			}
		}
		return $loc;
		
	}

	function getJS_Config($val){
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='".$val."'";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	
	function getAdmLinks(){
		$user	=& JFactory::getUser();
		$Itemid = JRequest::getInt('Itemid');
		$adm_links = '';
		$query = "SELECT s.*,t.name FROM #__users as u, #__bl_feadmins as f, #__bl_seasons as s, #__bl_tournament as t WHERE f.user_id = u.id AND s.s_id = f.season_id AND s.t_id = t.id AND u.id = ".intval($user->id)." ORDER BY s.ordering";
		$this->db->setQuery($query);
		
		$sidsss = $this->db->loadObjectList();
		if(count($sidsss)){
			$vr = 0;
			$adm_links.='<div class="administrations-links"><ul>';
			foreach ($sidsss as $adm_sid){
				if($vr){
					$adm_links .= '<li class="a-l-dash">|</li>';
				}
				$adm_links .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$adm_sid->s_id.'&Itemid='.$Itemid).'">'.$adm_sid->name.' '.$adm_sid->s_name.'</a></li>';
				$vr++;
			}
			$adm_links.='</ul></div>';
		}
		return $adm_links;
	}
	
	function getSParametrs($sid){
		$query = "SELECT * FROM #__bl_seasons WHERE s_id = ".$sid;
		$this->db->setQuery($query);
		return $this->db->LoadObject();
	}
	
	function getTeam($tid){
		$query = "SELECT * FROM #__bl_teams WHERE id = ".$tid;
		$this->db->setQuery($query);
		return $this->db->LoadObject();
	}
	
	function getSOptions($sid,$val){
		$query = "SELECT opt_value FROM #__bl_season_option WHERE s_id = ".$sid." AND opt_name='".$val."'";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	
	function getTournOpt($sid){
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_type,t.t_single,s.s_enbl_extra FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($sid)." AND s.t_id = t.id ORDER BY t.name, s.s_name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObject();
		return $tourn;
	}
	function uploadFile( $filename, $userfile_name, $dir = '') 
	{
		$msg = '';
		if(!$dir){
			$baseDir =  JPATH_ROOT . '/media/bearleague/' ;
		}else{
			$baseDir = $dir;
		}
		jimport('joomla.filesystem.path');
		if (file_exists( $baseDir )) {
			if (is_writable( $baseDir )) {
				if (move_uploaded_file( $filename, $baseDir . $userfile_name )) {
				
					if (JPath::setPermissions( $baseDir . $userfile_name )) {
						return true;
					} else {
						$msg = 'Failed to change the permissions of the uploaded file.';
					}
				} else {
					$msg = 'Failed to move uploaded file to <code>/media</code> directory.';
				}
			} else {
				$msg = 'Upload failed as <code>/media</code> directory is not writable.';
			}
		} else {
			$msg = 'Upload failed as <code>/media</code> directory does not exist.';
		}
		if($msg != ''){
			JError::raiseError(500, $msg );
		}
		return false;
	}
	function selectPlayerName($obj,$fn="first_name",$ln="last_name",$nk="nick"){
		$pln = getJS_Config('player_name');
		$q = '';
		if(isset($obj) && $pln && $obj->$nk){
			$q = $obj->$nk;
		}else{
			if($obj->$fn || ( isset($obj->$ln) && $obj->$ln)){
				$q = $obj->$fn;
				if(isset($obj->$ln) && $obj->$ln){
				//$q .=' '.$obj->$ln;
				}
			}
		}
		return $q;
	}
	
	//// moderators filters
	function getGlobFilters($friend = false){
		$user	=& JFactory::getUser();
		$sid = JRequest::getVar( 'sid', 0, 'request', 'int' );
		$tid = JRequest::getVar( 'tid', 0, 'request', 'int' );
		$Itemid = JRequest::getInt('Itemid'); 
		$query = "SELECT id,t_name FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid=".$user->id." ORDER BY t_name";
		$this->db->setQuery($query);
		
		$m_teams = $this->db->loadObjectList();
		
		$moderseason	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.moderseason', 'moderseason', 0, 'int' );
		
		$query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE  tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$tid." ORDER BY s.s_id desc";
		$this->db->setQuery($query);
		$seass = $this->db->loadObjectList();
		if(!$moderseason) {$this->mainframe->setUserState('com_joomsport.moderseason',$seass[0]->id); $moderseason = $seass[0]->id;};
		$isinseas = false;
		for($j=0;$j<count($seass);$j++){
			if($moderseason == $seass[$j]->id){
				$isinseas = true;
			}
		}
		if($moderseason == -1){
			$isinseas = true;
		}
		if(!$isinseas && count($seass)){
			$this->mainframe->setUserState('com_joomsport.moderseason',$seass[0]->id);
			$moderseason = $seass[0]->id;
		}

		$javascript = "onchange='document.chg_team.submit();'";
		"<form action='index.php?option=com_joomsport&view=moderedit_team&controller=moder&Itemid=".$Itemid."' method='post' name='chg_team'>";
		$this->_lists['tm_filtr'] = JHTML::_('select.genericlist',   $m_teams, 'tid', 'class="styled jfsubmit" size="1"'.$javascript, 'id', 't_name', $tid );
		$friendly[] = JHTML::_('select.option',  -1, JText::_('BLFA_FRIENDLY_MATCHES'), 'id', 't_name' );
		
		if(count($seass)){
			if($friend){
				$seass = array_merge($friendly,$seass);
			}
			$this->_lists['seass_filtr'] = JHTML::_('select.genericlist',   $seass, 'moderseason', 'class="styled jfsubmit" size="1"'.$javascript, 'id', 't_name', $moderseason );
		}

	}
	
	//bettings
	function isBet(){
		$query = "SELECT name FROM #__bl_addons WHERE published='1' AND name='betting'";
		$this->db->setQuery($query);
		$is_betting = $this->db->loadResult();
		return $is_betting;
	}
	function getUserPoints($user) {
        $db = &JFactory::getDbo();
        $q = 'SELECT points FROM #__bl_betting_users WHERE iduser='.$user;
        $db->setQuery($q);
        $points = $db->loadResult();
        $this->_data['points'] = $points;
        return !$points?0:$points;
    }
    
    function saveBet($points, $idmatch, $idevent, $who){
        $user = &JFactory::getUser();
        $userbet = new JTableBettingUsersBets($this->db);
        $userbet->set('iduser', $user->get('id'));
        $userbet->store();
        $bet = new JTableBettingBets($this->db);
        $bet->set('idbet', $userbet->get('id'));
        $bet->set('idmatch', $idmatch);
        $bet->set('idevent', $idevent);
        $bet->set('points', $points);
        $bet->set('who', $who);
        $bet->store();
        $log = new JTableBettingLogs($this->db);
        $log->addToLog($user->get('id'), -$points);
        $betuser = new JTableBettingUsers($this->db);
        $betuser->load(array('iduser'=>$user->get('id')));
        $betuser->changePoints(-$points);
    }
	function getBettingMenu($Itemid){
		$menu = '<div class="betmenu">
					<div>
						<a href="'.JRoute::_("index.php?option=com_joomsport&view=bet_cash_request&Itemid=".$Itemid).'">'.
							JText::_('BLFA_BET_REQUEST_CASH').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_("index.php?option=com_joomsport&view=bet_points_request&Itemid=".$Itemid).'">'.
							JText::_('BLFA_BET_REQUEST_POINTS').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_("index.php?option=com_joomsport&view=currentbets&Itemid=".$Itemid).'">'.
							JText::_('BLFA_BET_CURRENT_BETS').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_("index.php?option=com_joomsport&view=pastbets&Itemid=".$Itemid).'">'.
							JText::_('BLFA_BET_PAST_BETS').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_("index.php?option=com_joomsport&view=bet_matches&Itemid=".$Itemid).'">'.
							JText::_('BLFA_BET_MATCHES').'
						</a>
					</div>
				</div>';
		return $menu;
	}

	function getUserInfo($model, $Itemid){
		$mainmodel = new JSPRO_Models();
		$data = $model->getData();
		$user = JFactory::getUser();    
		if ($data){
			$points = $data['points'];
			$currentBets = count($data['currentbets']);
			$pastBets = count($data['pastbets']);
			$wonBets = count($data['wonbets']);
		} else {
			$points = $mainmodel->getUserPoints($user->get('id'));
			$currentBets = count($model->getCurrentBets());
			$pastBets = count($model->getPastBets());
			$wonBets = count($model->getWonBets());        
		}
		return '
			<span>'.$user->get('username').'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_POINTS').'</span><span>'.$points.'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_CURRENTBETS').'</span><span>'.$currentBets.'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_WINBETS').'</span><span>'.$wonBets.'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_PASTBETS').'</span><span>'.$pastBets.'</span><br/>
		';
	}
	/* page 
		1-season layout
		2-team layout
		3-player layout
		4-match layout
		5-venue layout
	*/
	function getSocialButtons($page,$title='',$img='',$txt=''){
		$doc =& JFactory::getDocument();
		if(!$this->getJS_Config($page)){
			return '';
			
		}
		
		
		$socbut = '';
		if($this->getJS_Config('jsb_twitter')){
			$socbut .= '<div class="jsd_buttons">
							<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" target="_blank">Tweet</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</div>';
		}
		if($this->getJS_Config('jsb_gplus')){
			$socbut .= '<div class="jsd_buttons">
							<g:plusone size="medium"></g:plusone>

							<script type="text/javascript">
							  window.___gcfg = {lang: "en"};

							  (function() {
								var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
								po.src = "https://apis.google.com/js/plusone.js";
								var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
							  })();
							</script>
						</div>';
		}
		
		if($this->getJS_Config('jsb_fbshare') || $this->getJS_Config('jsb_fblike') || $this->getJS_Config('jsb_gplus')){
			if($title){
				$doc->addCustomTag( '<meta property="og:title" content="'.$title.'"/> ' );
			}	
			if($img){
				$doc->addCustomTag( '<meta property="og:image" content="'.$img.'"/> ' );
			}
			//if($txt){
				$doc->addCustomTag( '<meta property="og:description" content="'.($txt?$txt:$title).'"/> ' );
			//}
		}
		
		if($this->getJS_Config('jsb_fbshare')){

			$socbut .= '<div class="jsd_buttons"  style="margin-right:15px;">
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>';

			$socbut .= '<div class="fb-send" data-href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" data-font="verdana"></div>';

			$socbut .= '</div>';
		}
		if($this->getJS_Config('jsb_fblike')){
			
			$socbut .= '<div class="jsd_buttons">	
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>

							<div class="fb-like" data-send="false" data-layout="button_count" data-width="130" data-show-faces="true" data-font="verdana"></div>
						</div>';
		}
		
		return $socbut;
		
	}
	
}

?>