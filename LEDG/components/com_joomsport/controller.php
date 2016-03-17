<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');
$mainframe = JFactory::getApplication();
require_once('includes/func.php');
$tmpl = JRequest::getVar( 'tmpl', '', 'get', 'string' );

if($task != 'add_comment' && $task != 'del_comment' && $tmpl != 'component'){
$doc =& JFactory::getDocument();

if(getVer() >= '1.6'){
	JHtml::_('behavior.framework', true);
	$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/includes/slimbox/js/slimbox.js"></script>' );
}else{
	JHtml::_('behavior.mootools');
	if(isset($mainframe->MooToolsVersion) && $mainframe->MooToolsVersion){
		$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/includes/slimbox/js/slimbox.js"></script>' );
	}else{
		$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/includes/slimbox/js15/slimbox.js"></script>' );
	}
}

$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/admin_bl.css" />' );
$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/joomsport.css" />' );
$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/joomsport.js"></script>' );
$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/styled-long.js"></script>' );



$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/includes/slimbox/css/slimbox.css" />' );
$doc->addCustomTag( '<script type="text/javascript" src="'.JURI::base().'components/com_joomsport/js/grid.js"></script>');
}

//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomsport'.DS.'admin.joomsport.class.php');

class JoomsportController extends JController
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
		$path = dirname(__FILE__).'/models/';
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
		$path = dirname(__FILE__).'/views/'.$task;
		
		require($path."/view.html.php");
		
	}
	
	function display()
	{
		$view = JRequest::getCmd( 'view' );
		$task = JRequest::getCmd( 'task' );

		if(!$view) {
			$view = 'table';
		}
		$la = JRequest::getCmd( 'layout' );
		if($la == 'calendar'){
			$view = 'calendar';
			JRequest::setVar( 'layout', 'default' );
		}
		
		
		$vName		= JRequest::getCmd('view', 'table');
		
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		
		$this->js_Layout($vName);
		$classname_l = "JoomsportView".$vName;
		
		$layout = new $classname_l($model);
		
		$layout->display();
	
		
		return $this;
		
	}
	
	function team()
	{
		JRequest::setVar( 'view', 'team' );
		$this->display();
	}
	function player()
	{
		JRequest::setVar( 'view', 'player' );
		$this->display();
	}
	function venue()
	{
		JRequest::setVar( 'view', 'venue' );
		$this->display();
	}
	function view_match()
	{
		JRequest::setVar( 'view', 'match' );
		$this->display();
	}
	
	function calendar()
	{
		JRequest::setVar( 'view', 'calendar' );
		
		
		$this->display();
	}
	function regplayer()
	{	
		
		JRequest::setVar( 'view', 'regplayer' );
		$this->display();
	}
	function playerreg_save(){
		$Itemid = JRequest::getInt('Itemid'); 
		
		$vName = 'regplayer';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->SaveRegPlayer();
		
		$link = "index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid;
		
		if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
			$return = base64_decode($return);
			if (!JURI::isInternal($return)) {
				$return = '';
			}
		}
		
		$message = $curid?JText::_('BLMESS_UPDSUCC'):JText::_('BLFA_REGSUCC');
		$this->setRedirect($return?$return:$link,$message);
	}
	
	function regteam()
	{
		JRequest::setVar( 'view', 'regteam' );
		$this->display();
	}
	
	function teamreg_save(){
		$Itemid = JRequest::getInt('Itemid'); 
		$vName = 'regteam';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->regTeamSave();
		
		$link = "index.php?option=com_joomsport&task=regteam&Itemid=".$Itemid;
		$msg = JText::_('BLFA_NEWTEAMMSG');
		
		$this->setRedirect($link,$msg);
	}
	
	function add_comment(){
		$addcomm = JRequest::getVar( 'addcomm', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$addcomm = strip_tags($addcomm);
		
		$math_id = JRequest::getVar('mid',0,'post','int');
		$user	=& JFactory::getUser();
		$db = &JFactory::getDBO();
		if ( $user->get('guest')) {
			return false;
			//return;
		} 
		$query = "INSERT INTO `#__bl_comments` ( `id` , `user_id` , `match_id` , `date_time` , `comment` ) VALUES(0,".$user->id.",".$math_id.",'".gmdate("Y-m-d H:i:s")."','".addslashes($addcomm)."')";
		$db->setQuery($query);
		$db->query();
		$curid = $db->insertid();
		
		$query = "SELECT IF(pl.nick <> '',pl.nick,p.name) FROM #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id WHERE p.id=".$user->id;
		$db->setQuery($query);
		$name = $db->loadResult();
		?>
		<li id="divcomb_<?php echo $curid?>">
			<img src="<?php echo JURI::base();?>components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="" />
			<div class="comments-box-inner">
				<span class="date">
					<?php

					echo "<img src='".JURI::base()."components/com_joomsport/img/ico/close.png' width='15' border=0 style='cursor:pointer;' onClick='javascript:delCom(".$curid.");' />";
					?>
					<?php 
					jimport('joomla.utilities.date');
					if(getVer() > '1.6'){
						$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
						$jdate = new JDate(time());
					
						$jdate->setTimezone($tz);
					}else{
						
						$jdate = new JDate('now',JFactory::getApplication()->getCfg('offset'));
					
					
					}
					
					
					echo $jdate->toMySQL(true);
					?>
				</span>
				<h4 class="nickname"><?php echo $name;?></h4>
				<p><?php echo str_replace("\n",'<br />',htmlspecialchars($addcomm));?></p>
			</div>
		</li>
		<?php
	}
	function del_comment(){
		$c_id = JRequest::getVar('cid',0,'get','int');
		$user	=& JFactory::getUser();
		$dend = false;
		$db = &JFactory::getDBO();
		if(getVer() >= '1.6'){
			$query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id=".$user->id;
			$db->setQuery($query);
			if($db->loadresult() == 8){
				$dend = true;
			}
			$query = "SELECT user_id FROM  `#__bl_comments` WHERE `id` = ".$c_id;
			$db->setQuery($query);
			if($db->loadResult() == $user->id){
				$dend = true;
			}
		}else{
			if($user->gid == 25){
				$dend = true;
			}
		}
		
		if ( $user->get('guest') || !$dend) {
			echo 'Denide';
			return false;
			//return;
		} 
		$query = "DELETE FROM  `#__bl_comments` WHERE `id` = ".$c_id;
		$db->setQuery($query);
		$db->query();
		
		
	}
	
	
	
function join_season()
	{
		JRequest::setVar( 'view', 'join_season' );
		$this->display();
	}
function joinme(){	
	
	$vName = 'join_season';
	$this->js_Model($vName);
	$classname = $vName."JSModel";
	$model = new $classname();
	$message = $model->joinSave();
	$Itemid = JRequest::getInt('Itemid'); 
	$this->setRedirect('index.php?option=com_joomsport&view=table&sid='.$model->s_id.'&Itemid='.$Itemid, $message); 
}
	
	
	///---------------Matchday--------------------------/
	
	function moderedit_umatchday()
	{
		$user	=& JFactory::getUser();
		
		JRequest::setVar( 'view', 'moderedit_umatchday' );
		JRequest::setVar( 'edit', true );
		$this->display();
	}
	
	
	
	function matchday_save(){
		
		$vName = 'moderedit_umatchday';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$message = $model->SaveMdUmod();

		$msg = JText::_('BLFA_MSG_ADDSCHED');
		
		$Itemid = JRequest::getInt('Itemid'); 

		$link = "index.php?option=com_joomsport&task=moderedit_umatchday&mid=".$model->mid."&sid=".$model->sid."&Itemid=".$Itemid;

		$this->setRedirect( $link );
	}
	
	
	
	
	///---------------Match--------------------------/
	
	
	function moderedit_umatch()
	{
		JRequest::setVar( 'view', 'moderedit_umatch' );
		JRequest::setVar( 'edit', true );
		$this->display();
	}
	
	
	function umatch_save(){
		$vName = 'moderedit_umatch';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->SaveUMatch();

		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );


		$Itemid = JRequest::getInt('Itemid'); 

			$this->setRedirect("index.php?option=com_joomsport&task=moderedit_umatchday&mid=".$model->m_id."&sid=".$s_id."&Itemid=".$Itemid);

	} 
	function umatch_apply(){
	
		$vName = 'moderedit_umatch';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->SaveUMatch();

		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );


		$Itemid = JRequest::getInt('Itemid'); 

			$this->setRedirect("index.php?option=com_joomsport&task=moderedit_umatch&cid[]=".$model->id."&Itemid=".$Itemid);
		
	} 
	
	//inviting confirm
	function confirm_invitings(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->getData();
		$Itemid = JRequest::getInt('Itemid'); 
		$this->setRedirect("index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid,$messaga);
	}
	function unreg_inviting(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->unregInvite();
		$Itemid = JRequest::getInt('Itemid'); 
		$this->setRedirect("index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid,$messaga);
	}
	function match_inviting(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->matchInvite();
		$Itemid = JRequest::getInt('Itemid'); 
		$mid = JRequest::getVar( 'mid', 0, '', 'int' );
		$this->setRedirect("index.php?option=com_joomsport&view=match&id=".$mid."Itemid=".$Itemid,$messaga);
	}
	
	function jointeam(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->JoinTeam();
		$Itemid = JRequest::getInt('Itemid'); 
		$team_id = JRequest::getVar( 'tid', 0, '', 'int' );
		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		
		$this->setRedirect("index.php?option=com_joomsport&task=team&tid=".$team_id."&sid=".$s_id."&Itemid=".$Itemid,$messaga);
	}
	
	function get_js_version(){
		$js_version = '2.1.2';
		echo $js_version;
		exit();
		
	}
	
	///betting
	function bet_cash_request(){
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();
		
        $view = 'bet_cash_request';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;
		
		$layout = new $classname_l($model);
		
		$layout->display();
	
		
		return $this;
    }
    
    function bet_request_cash_submit(){
		$Itemid = JRequest::getInt('Itemid'); 
		$vName = 'userarea';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->submitCashRequest();
		$msg = JText::_('BLFA_BET_REQUEST_SUBMITTED');
        $link = "index.php?option=com_joomsport&task=userarea&Itemid=".$Itemid;
        
        $this->setRedirect($link,$msg);
    }
    
    function bet_request_points_submit(){
		$Itemid = JRequest::getInt('Itemid'); 
		$vName = 'userarea';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->submitPointsRequest();
		$msg = JText::_('BLFA_BET_REQUEST_SUBMITTED');
        $link = "index.php?option=com_joomsport&task=userarea&Itemid=".$Itemid;
        
        $this->setRedirect($link,$msg);
    }    

    function bet_points_request(){
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();
		
        $view = 'bet_points_request';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;
		
		$layout = new $classname_l($model);
		
		$layout->display();
		
		return $this;
    }
    
    function currentbets()
    {
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();
		
        $view = 'currentbets';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;
		
		$layout = new $classname_l($model);
		
		$layout->display();
	
		
		return $this;
    }
    
    function pastbets()
    {
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();
		
        $view = 'pastbets';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;
		
		$layout = new $classname_l($model);
		
		$layout->display();
	
		
		return $this;
    }
    
    function bet_matches()
    {
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();
		
        $view = 'bet_matches';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;
		
		$layout = new $classname_l($model);
		
		$layout->display();
	
		
		return $this;
    }
    
    function bet_calendar_save(){
		$Itemid = JRequest::getInt('Itemid'); 
		$vName = 'calendar';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->saveBets();
		$msg = JText::_('BLFA_BET_BETSSAVED');
		if ($result != 1){
            $msg = $result;
        }
        $s_id = JRequest::getVar( 'sid', 0, '', 'int' );        
		$link = "index.php?option=com_joomsport&task=calendar&sid=".$s_id."&Itemid=".$Itemid;

		$this->setRedirect($link,$msg);
    }
    
    function bet_team_save(){
		$Itemid = JRequest::getInt('Itemid'); 
		$vName = 'team';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->saveBets();
		$msg = JText::_('BLFA_BET_BETSSAVED');        
		if ($result != 1){
            $msg = $result;
        }
        $s_id = JRequest::getVar( 'sid', 0, '', 'int' );
        $tid = JRequest::getVar( 'tid', 0, '', 'int' );
		$link = "index.php?option=com_joomsport&task=team&tid=".$tid."&sid=".$s_id."&Itemid=".$Itemid;

		$this->setRedirect($link,$msg);
    }
    
    function bet_match_save(){
		$Itemid = JRequest::getInt('Itemid'); 
		$vName = 'match';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->saveBets();
		$msg = JText::_('BLFA_BET_BETSSAVED');        
		if ($result != 1){
            $msg = $result;
        }
        $mid = JRequest::getVar( 'm_id', 0, '', 'int' );
		$link = 'index.php?option=com_joomsport&task=view_match&id='.$mid.'&Itemid='.$Itemid;

		$this->setRedirect($link,$msg);
    }
	function userarea(){
		JRequest::setVar( 'view', 'userarea' );
		$this->display();        
    }
	public function chkvers(){
		echo  @file_get_contents('http://joomsport.com/index2.php?option=com_chkversion&id=1&no_html=1&tmpl=component');
	}
	
}