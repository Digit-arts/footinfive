<?php
/**
* @version		$Id: router.php 10711 2008-08-21 10:09:03Z eddieajau $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

function joomsportBuildRoute(&$query)
{
	$segments = array();
	
	$menu = &JSite::getMenu();
	if (empty($query['Itemid'])) {
		$menuItem = &$menu->getActive();
	} else {
		$menuItem = &$menu->getItem($query['Itemid']);
	}
	
	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}elseif (isset($query['task'])){
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if(isset($query['sid']))
	{
		$segments[] = $query['sid'];
		unset($query['sid']);
	};
	if(isset($query['tid']))
	{
		$segments[] = $query['tid'];
		unset($query['tid']);
	};
	if(isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	};
	
	if(isset($query['cid'][0]))
	{
		$segments[] = $query['cid'][0];
		unset($query['cid']);
	};
	if(isset($query['mid']))
	{
		$segments[] = $query['mid'];
		unset($query['mid']);
	};
	if(isset($query['controller']))
	{
		$segments[] = $query['controller'];
		unset($query['controller']);
	};
	return $segments;
}

function joomsportParseRoute($segments)
{
	$vars = array();
	ob_start();
	switch($segments[0])
	{
		case 'team':
			$vars['view'] = 'team';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['tid'] = (int)$id[0];
		break;
		case 'player':
		$vars['view'] = 'player';
		$id = explode(':', $segments[1]);
		$vars['sid'] = (int)$id[0];
		$id = explode(':', $segments[2]);
		$vars['id'] = (int)$id[0];
		break;
		case 'calendar':
		$vars['view'] = 'calendar';
		
		$id = explode(':', $segments[1]);
		$vars['sid'] = (int)$id[0];
		break;
		case 'view_match':
		case 'match':
		$vars['view'] = 'match';
		$id = explode(':', $segments[1]);
		$vars['id'] = (int)$id[0];
		break;
		case 'admin_team':
			$vars['view'] = 'admin_team';
			$vars['controller']='admin';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
		break;
		
		case 'admin_matchday':
			$vars['view'] = 'admin_matchday';
			$vars['controller']='admin';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			
		break;
		case 'edit_team':
			$vars['view'] = 'edit_team';
			$vars['controller']='admin';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['cid'][0] = (int)$id[0];
			
		break;
		case 'player_edit':
			$vars['view'] = 'edit_player';
			$vars['controller']='admin';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['cid'][0] = (int)$id[0];
			
		break;
		case 'matchday_edit':
		case 'edit_matchday':
			$vars['view'] = 'edit_matchday';
			$vars['controller']='admin';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['cid'][0] = (int)$id[0];
			
		break;
		case 'match_edit':
		case 'edit_match':
			$vars['view'] = 'edit_match';
			$vars['controller']='admin';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['cid'][0] = (int)$id[0];
			
		break;
		case 'moderedit_team':
			$vars['view'] = 'moderedit_team';
			$vars['controller']='moder';
			$id = explode(':', $segments[1]);
			$vars['tid'] = (int)$id[0];
			
		break;
		
		case 'mplayer_edit':
			$vars['view'] = 'moderedit_player';
			$vars['controller']='moder';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['tid'] = (int)$id[0];
			$id = explode(':', $segments[3]);
			$vars['cid'][0] = (int)$id[0];
			
		break;
		case 'moderedit_matchday':
			$vars['view'] = 'moderedit_matchday';
			$vars['controller']='moder';
			$id = explode(':', $segments[2]);
			if(intval($id[0])){
				$id = explode(':', $segments[3]);
				$vars['mid'] = (int)$id[0];

				$id = explode(':', $segments[1]);

				$vars['sid'] = (int)$id[0];
				$id = explode(':', $segments[2]);
				$vars['tid'] = (int)$id[0];
			}else{

				$id = explode(':', $segments[1]);
				$vars['tid'] = (int)$id[0];
			}
			
		break;
		case 'moderedit_match':
			$vars['view'] = 'moderedit_match';
			$vars['controller']='moder';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['tid'] = (int)$id[0];
			$id = explode(':', $segments[3]);
			$vars['cid'][0] = (int)$id[0];
			
		break;
		
		case 'regplayer':
			$vars['view'] = 'regplayer';
		break;	
		case 'regteam':
			$vars['view'] = 'regteam';
		break;	
		case 'join_season':
			$vars['view'] = 'join_season';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			
		break;
		case 'moderedit_umatchday':
			$vars['view'] = 'moderedit_umatchday';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['mid'] = (int)$id[0];
		break;
		
		case 'moderedit_umatch':
			$vars['view'] = 'moderedit_umatch';
			$id = explode(':', $segments[1]);
			$vars['cid'][0] = (int)$id[0];
			
		break;
		
		case 'table':
			$vars['view'] = 'table';
				$id = explode(':', $segments[1]);
				$vars['sid'] = (int)$id[0];
			break;	
		case 'seasonlist':
			$vars['view'] = 'seasonlist';
			break;	
		case 'admin_player':
			$vars['view'] = 'admin_player';
			$id = explode(':', $segments[1]);
			$vars['tid'] = (int)$id[0];
			$vars['controller']='moder';
			break;	
		case 'mdplayer_edit':
		case 'moderedit_player':
			$vars['view'] = 'moderedit_player';
			$id = explode(':', $segments[1]);
			$vars['tid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['cid'][0] = (int)$id[0];
			break;	
		case 'adlist_player':
			$vars['view'] = 'adlist_player';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			break;	
		case 'adplayer_edit':
			$vars['view'] = 'adplayer_edit';
			$id = explode(':', $segments[1]);
			$vars['sid'] = (int)$id[0];
			$id = explode(':', $segments[2]);
			$vars['cid'][0] = (int)$id[0];
			break;	
		case 'venue':
				$vars['view'] = 'venue';
				$id = explode(':', $segments[1]);
				$vars['id'] = (int)$id[0];
				break;	
		case 'jointeam':
				$vars['task'] = 'jointeam';
				$id = explode(':', $segments[1]);
				$vars['sid'] = (int)$id[0];
				$id = explode(':', $segments[2]);
				$vars['tid'] = (int)$id[0];
				break;	
		///betting
		case 'currentbets':
            $vars['view'] = 'currentbets';
            $vars['task'] = 'currentbets';
            break;
        case 'pastbets':
            $vars['view'] = 'pastbets';
            $vars['task'] = 'pastbets';
            break;
        case 'bet_points_request':
            $vars['view'] = 'bet_points_request';
            $vars['task'] = 'bet_points_request';
            break;
        case 'bet_cash_request':
            $vars['view'] = 'bet_cash_request';
            $vars['task'] = 'bet_cash_request';
            break;
        case 'bet_matches':
            $vars['view'] = 'bet_matches';
            $vars['task'] = 'bet_matches';
            break;
			
		default:
			
				$vars['view'] = 'table';
				$id = explode(':', $segments[1]);
				$vars['sid'] = (int)$id[0];
			
			
		
	}
	ob_clean();
	return $vars;
}
