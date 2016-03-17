<?php
/**
* @version		$Id: mod_stats.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$season_id = $params->get( 'season_id' );
$match_count 	= $params->get( 'match_count' );
$team_id = $params->get( 'team_id' );
$embl_is = $params->get( 'embl_is' );
$list = modBlNextHelper::getList($params);
$single = modBlNextHelper::getStype($params);
require(JModuleHelper::getLayoutPath('mod_js_next_matches'));