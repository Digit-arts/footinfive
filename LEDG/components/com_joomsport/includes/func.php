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

function date_bl($date,$time){
	$db			=& JFactory::getDBO(); 
	$format = "%d-%m-%Y %H:%M";
	if($date == '' || $date == '0000-00-00'){
		return '';
	}
	//echo $date.' - '.$time."<br />";
	$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='date_format'";
	$db->setquery($query);
	$format = $db->loadResult();
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
	//echo $time_m[0].','.$time_m[1].','.$date_m[1].','.$date_m[2].','.$date_m[0];
	if(function_exists('date_default_timezone_set')){
		date_default_timezone_set('GMT');
	}
	$tm = @mktime($time_m[0],$time_m[1],'0',$date_m[1],$date_m[2],$date_m[0]);
	jimport('joomla.utilities.date');
	$dt = new JDate($tm,0);
	return $dt->toFormat($format);
	//return JHTML::_('date',@mktime(substr($time,0,2),substr($time,3,2),0,substr($date,5,2),substr($date,8,2),substr($date,0,4)),$format,0);
}
function getePanel($team,$reg,$sid,$lang,$Itemid,$cal = 0){
	$db			=& JFactory::getDBO(); 
	$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='team_reg'";
	$db->setQuery($query);
	$team_reg = $db->loadResult();
	
	?>
	<div class="epanel">
		<?php $link2 = JRoute::_('index.php?option=com_joomsport&amp;view=seasonlist&Itemid='.$Itemid);?>
		<div style="float:left;"><a href="<?php echo $link2;?>"><img src="components/com_joomsport/images/joomsport.gif" alt="JoomSport" /></a></div>
		<?php $link = JRoute::_('index.php?option=com_joomsport&amp;task=regplayer&Itemid='.$Itemid);?>
		<div style="float:left;margin:6px 15px;"><a href="<?php echo $link?>" title="<?php echo JText::_('BLFA_EDITFIPROF');?>"><img src="components/com_joomsport/images/edit_profile.png" alt="<?php echo JText::_('BLFA_EDITFIPROF');?>" /></a></div>
		<?php if(isset($team[0])){
		$link = JRoute::_('index.php?option=com_joomsport&task=edit_team&tid='.$team[0].'&controller=moder&Itemid='.$Itemid);
			?>
		<div style="float:left;margin:6px 15px;"><a href="<?php echo $link?>" title="<?php echo JText::_('BLFA_YTEAM');?>"><img src="components/com_joomsport/images/edit_team.png" alt="<?php echo JText::_('BLFA_YTEAM');?>" /></a></div>
		<?php } ?>
		<?php if($team_reg){?>
		<?php $link = JRoute::_('index.php?option=com_joomsport&amp;task=regteam&Itemid='.$Itemid);?>
		<div style="float:left;margin:5px 15px;"><a href="<?php echo $link?>" title="<?php echo JText::_('BLFA_NTEAM');?>"><img src="components/com_joomsport/images/edit_season.png" alt="<?php echo JText::_('BLFA_NTEAM');?>" /></a></div>
		<?php } ?>
		<?php if($cal){?>
		<div style="float:right; width:30px;margin:10px 5px;"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&amp;task=calendar&amp;sid='.$sid.'&Itemid='.$Itemid)?>" title="<?php echo JText::_('BLFA_CALENDAR');?>"><img src="<?php echo JURI::base()?>components/com_joomsport/images/calendar.png"  alt="<?php echo JText::_('BLFA_CALENDAR');?>" width="24" /></a></div>
		<?php } ?>
		<?php if($reg){?>
		<div style="float:right; width:30px;margin:10px 5px;"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&amp;task=joinseason&amp;sid='.$sid.'&Itemid='.$Itemid)?>" title="<?php echo JText::_('BLFA_REGGG');?>"><img src="<?php echo JURI::base()?>components/com_joomsport/images/add_team.png" alt="<?php echo JText::_('BLFA_REGGG');?>" width="24" /></a></div>
		<?php } ?>
	</div>
	<?php
}

//2.0.1
//type 0 - player, 1 - team, 2-match.
function getAddFields($id,$type){
	$user	=& JFactory::getUser();
	$db			=& JFactory::getDBO();
	$query = "SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$id." WHERE ef.published=1 AND ef.fdisplay = '1' AND ef.type = '".$type."' ".($user->id?"":" AND ef.faccess='0'")." ORDER BY ef.ordering";
	$db->setQuery($query);
	$res = $db->loadObjectList();
	$mj=0;
		if(isset($res)){
			foreach ($res as $extr){
			
				if($extr->field_type == '3'){
					$query = "SELECT sel_value FROM #__bl_extra_select WHERE id='".$extr->fvalue."'";
					$db->setQuery($query);
					$selvals = $db->loadResult();
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
	return $res;	
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
	$img_types = array('png','gif','jpg','jpeg');
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
	$db			=& JFactory::getDBO();
	$Itemid = JRequest::getInt('Itemid');
	$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='unbl_venue'";
	$db->setQuery($query);
	$unbl_venue = $db->loadResult();
	
	$query = "SELECT m_location FROM #__bl_match WHERE id=".$id;
	$db->setQuery($query);
	$loc = $db->loadResult();
	if($unbl_venue){
		$query = "SELECT v.* FROM #__bl_match as m LEFT JOIN #__bl_venue as v ON m.venue_id=v.id WHERE m.id=".$id;
		$db->setQuery($query);
		$ven = $db->loadObject();
		if($ven->v_name){
			$link = JRoute::_("index.php?option=com_joomsport&task=venue&id=".$ven->id."&Itemid=".$Itemid);
			$loc = '<a href="'.$link.'" title="'.$ven->v_name.'">'.$ven->v_name.'</a>';
		}
	}
	return $loc;
	
}

function getJS_Config($val){
	$db			=& JFactory::getDBO();
	$Itemid = JRequest::getInt('Itemid');
	$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='".$val."'";
	$db->setQuery($query);
	return $db->loadResult();
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

?>