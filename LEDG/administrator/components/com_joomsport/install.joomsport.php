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

function com_install() {

$database =& JFactory::getDBO();
$database->debug(0);
  $dirname = dirname(__FILE__);

	$dirnameExploded = explode(DIRECTORY_SEPARATOR, $dirname);

	$jBasePath = "";

	$omitLast = 3;

	for ($i = 0; $i < sizeof($dirnameExploded) - $omitLast; $i++) {

		$jBasePath .= $dirnameExploded[$i];

		if ($i < (sizeof($dirnameExploded) - ($omitLast +1)))

			$jBasePath .= DIRECTORY_SEPARATOR;

	}

	if (!@ defined('DS'))

		define('DS', DIRECTORY_SEPARATOR); 

		
	///joomla 1.6
	$version = new JVersion;
	$joomla_v = $version->getShortVersion();
	if(substr($joomla_v,0,3) >= '1.6'){
		$query = "SELECT `extension_id` FROM #__extensions WHERE `element` = 'com_joomsport'";
		$database->setQuery( $query );
		$exid = $database->loadResult();
		
		$query = "UPDATE #__menu SET component_id = ".$exid." WHERE link LIKE 'index.php?option=com_joomsport%'";
		$database->setQuery( $query );
		$database->query();
		$query = "UPDATE #__extensions SET name='com_joomsport' WHERE `element` = 'com_joomsport'";
		$database->setQuery( $query );
		$database->query();
	}else{
		$query = "SELECT `id` FROM #__components WHERE `option` = 'com_joomsport'";
		$database->setQuery( $query );
		$id = $database->loadResult();
		$query = "UPDATE #__components SET name='JoomSport' WHERE name='COM_JOOMSPORT'";
		$database->setQuery( $query );
		$database->query();
		$query = "UPDATE #__menu SET componentid = {$id} WHERE link LIKE 'index.php?option=com_joomsport%'";
		$database->setQuery( $query );
		$database->query();
		$jlang =& JFactory::getLanguage();
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomsport';
		$jlang->load('com_joomsport.sys', $path, 'en-GB', true);
		$jlang->load('com_joomsport.sys', $path, $jlang->getDefault(), true);
		$jlang->load('com_joomsport.sys', $path, null, true); 
	}
	
	///update views
	$query = "UPDATE #__menu SET link = replace(link, 'view=ltable', 'view=table') WHERE link LIKE 'index.php?option=com_joomsport%'";
	$database->setQuery( $query );
	$database->query();
	$query = "UPDATE #__menu SET link = replace(link, 'view=blteam', 'view=team') WHERE link LIKE 'index.php?option=com_joomsport%'";
	$database->setQuery( $query );
	$database->query();
	$query = "UPDATE #__menu SET link = replace(link, 'view=view_match', 'view=match') WHERE link LIKE 'index.php?option=com_joomsport%'";
	$database->setQuery( $query );
	$database->query();
	

  $adminDir = dirname(__FILE__);

	@mkdir($jBasePath .DS. "media".DS."bearleague");

	@chmod($jBasePath .DS. "media".DS."bearleague", 0777);

	@mkdir($jBasePath .DS. "media".DS."bearleague".DS."events");

	@chmod($jBasePath .DS. "media".DS."bearleague".DS."events", 0777);

	

	@copy( $adminDir. DS."bearleague".DS."events".DS."red_card.png", $jBasePath . DS."media".DS."bearleague".DS."events".DS."red_card.png");

	@copy( $adminDir. DS."bearleague".DS."events".DS."yellow_card.png", $jBasePath . DS."media".DS."bearleague".DS."events".DS."yellow_card.png"); 

  	@copy( $adminDir. DS."bearleague".DS."events".DS."yellow-red_card.png", $jBasePath . DS."media".DS."bearleague".DS."events".DS."yellow-red_card.png");

	@copy( $adminDir. DS."bearleague".DS."events".DS."boot.png", $jBasePath . DS."media".DS."bearleague".DS."events".DS."boot.png"); 

	@copy( $adminDir. DS."bearleague".DS."events".DS."ball.png", $jBasePath . DS."media".DS."bearleague".DS."events".DS."ball.png"); 


  	@copy( $adminDir. DS."bearleague".DS."player_st.png", $jBasePath . DS."media".DS."bearleague".DS."player_st.png");

	@copy( $adminDir. DS."bearleague".DS."teams_st.png", $jBasePath . DS."media".DS."bearleague".DS."teams_st.png"); 

	

    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/beardev.gif' WHERE admin_menu_link LIKE '%option=com_joomsport'";

    $database->setQuery($sql);

    $database->query();



    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/tourn16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=tour_list'";

    $database->setQuery($sql);

    $database->query();

  

    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/players16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=player_list'";

    $database->setQuery($sql);

    $database->query();



    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/blleg.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=pos_list'";

    $database->setQuery($sql);

    $database->query();

  

    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/match16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=matchday_list'";

    $database->setQuery($sql);

    $database->query();



    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/events16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=event_list'";

    $database->setQuery($sql);

    $database->query();



    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/team16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=team_list'";

    $database->setQuery($sql);

    $database->query();

	

    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/season16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=season_list'";

    $database->setQuery($sql);

    $database->query();

	

    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/group16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=group_list'";

    $database->setQuery($sql);

    $database->query();

	$sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/moder16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=moder_list'";

    $database->setQuery($sql);

    $database->query();

    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/additional16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=fields_list'";

    $database->setQuery($sql);

    $database->query();



  
	

	$sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/config16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=config'";

	$database->setQuery($sql);

	$database->query();

	
    $sql = "UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/maps16.png' WHERE admin_menu_link LIKE '%option=com_joomsport&task=map_list'";

    $database->setQuery($sql);

    $database->query();	


    $database->setQuery("UPDATE #__components SET admin_menu_img='../includes/js/ThemeOffice/help.png' WHERE admin_menu_link LIKE 'option=com_joomsport&task=help'");

	$database->query();

	

	$database->setQuery("UPDATE #__components SET admin_menu_img='../administrator/components/com_joomsport/img/about16.png' WHERE admin_menu_link LIKE 'option=com_joomsport&task=about'");

	$database->query();

	

	$database->setQuery("ALTER TABLE `#__bl_match` ADD `m_played` VARCHAR(1) DEFAULT '1' NOT NULL");

	$database->query();

	

	$database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `e_table_view` VARCHAR(1) DEFAULT '0' NOT NULL");

	$database->query();





//john changes

	$database->setQuery("ALTER TABLE `#__bl_match` ADD `m_date` date default '0000-00-00' NOT NULL");

	$database->query();



	$database->setQuery("ALTER TABLE `#__bl_match` ADD `m_time` varchar(10) default '' NOT NULL ");

	$database->query();



	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_win_away` int(11) default '0' NOT NULL");

	$database->query();



	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_draw_away` int(11) default '0' NOT NULL");

	$database->query();



	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_lost_away` int(11) default '0' NOT NULL ");

	$database->query();



	$database->setQuery("ALTER TABLE `#__bl_tblcolors` CHANGE `place` `place` VARCHAR( 35 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");

	$database->query();



	

	

//end of john changes 



/* 1.0.8 */

	$database->setQuery("ALTER TABLE `#__bl_match` ADD `m_location` VARCHAR( 255 ) NOT NULL");

	$database->query();

	$database->setQuery("ALTER TABLE `#__bl_match_events` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY");

	$database->query();

	

	$query = "SELECT cfg_value FROM `#__bl_config` WHERE cfg_name='date_format'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('date_format', '%d-%m-%Y %H:%M')");

		$database->query();

	}

	$query = "SELECT cfg_value FROM `#__bl_config` WHERE cfg_name='yteam_color'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('yteam_color', '#FFFFFF')");

		$database->query();

	}

	

	//----events patch-----//

	$query = "SELECT * FROM #__bl_match_events WHERE t_id=0";

	$database->setQuery($query);

	$t_ev = $database->LoadObjectList();

	

	for($z=0;$z<count($t_ev);$z++){

		$ev = $t_ev[$z];

		$query = "SELECT team_id FROM #__bl_players WHERE id=".$ev->player_id;

		$database->setQuery($query);

		$tid = $database->loadResult();

		

		if($tid){

			$query = "UPDATE #__bl_match_events SET t_id=".$tid." WHERE id=".$ev->id;

			$database->setQuery($query);

			$database->query();

			

		}

	}
	// version 1.0.10
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `bonus1` decimal(10,2) NOT NULL DEFAULT '0.00'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `bonus2` decimal(10,2) NOT NULL DEFAULT '0.00'");
	$database->query();
	
	$database->setQuery("ALTER TABLE `#__bl_tournament` ADD `logo` VARCHAR(255) NOT NULL");
	$database->query();
	
	//
	$database->setQuery("ALTER TABLE `#__bl_season_teams` ADD `bonus_point` int(11) default '0' NULL");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_groups` ADD `ordering` INT NOT NULL");
	$database->query();


	
	//--- ad countries------//
	$query = "SELECT COUNT(*) FROM `#__bl_countries`";
	$database->setQuery($query);
	
	if(!$database->loadResult()){
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (1, 'AF', 'Afghanistan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (2, 'AX', 'Åland Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (3, 'AL', 'Albania')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (4, 'DZ', 'Algeria')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (5, 'AS', 'American Samoa')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (6, 'AD', 'Andorra')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (7, 'AO', 'Angola')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (8, 'AI', 'Anguilla')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (9, 'AQ', 'Antarctica')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (10, 'AG', 'Antigua and Barbuda')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (11, 'AR', 'Argentina')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (12, 'AM', 'Armenia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (13, 'AW', 'Aruba')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (14, 'AU', 'Australia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (15, 'AT', 'Austria')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (16, 'AZ', 'Azerbaijan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (17, 'BS', 'Bahamas')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (18, 'BH', 'Bahrain')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (19, 'BD', 'Bangladesh')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (20, 'BB', 'Barbados')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (21, 'BY', 'Belarus')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (22, 'BE', 'Belgium')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (23, 'BZ', 'Belize')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (24, 'BJ', 'Benin')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (25, 'BM', 'Bermuda')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (26, 'BT', 'Bhutan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (27, 'BO', 'Bolivia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (28, 'BA', 'Bosnia and Herzegovina')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (29, 'BW', 'Botswana')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (30, 'BV', 'Bouvet Island')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (31, 'BR', 'Brazil')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (32, 'IO', 'British Indian Ocean Territory')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (33, 'BN', 'Brunei Darussalam')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (34, 'BG', 'Bulgaria')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (35, 'BF', 'Burkina Faso')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (36, 'BI', 'Burundi')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (37, 'KH', 'Cambodia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (38, 'CM', 'Cameroon')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (39, 'CA', 'Canada')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (40, 'CV', 'Cape Verde')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (41, 'KY', 'Cayman Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (42, 'CF', 'Central African Republic')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (43, 'TD', 'Chad')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (44, 'CL', 'Chile')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (45, 'CN', 'China')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (46, 'CX', 'Christmas Island')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (47, 'CC', 'Cocos (Keeling) Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (48, 'CO', 'Colombia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (49, 'KM', 'Comoros')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (50, 'CG', 'Congo')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (51, 'CD', 'Congo, The Democratic Republic of the')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (52, 'CK', 'Cook Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (53, 'CR', 'Costa Rica')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (54, 'CI', 'Côte D''Ivoire')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (55, 'HR', 'Croatia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (56, 'CU', 'Cuba')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (57, 'CY', 'Cyprus')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (58, 'CZ', 'Czech Republic')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (59, 'DK', 'Denmark')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (60, 'DJ', 'Djibouti')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (61, 'DM', 'Dominica')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (62, 'DO', 'Dominican Republic')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (63, 'EC', 'Ecuador')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (64, 'EG', 'Egypt')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (65, 'SV', 'El Salvador')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (66, 'GQ', 'Equatorial Guinea')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (67, 'ER', 'Eritrea')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (68, 'EE', 'Estonia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (69, 'ET', 'Ethiopia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (70, 'FK', 'Falkland Islands (Malvinas)')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (71, 'FO', 'Faroe Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (72, 'FJ', 'Fiji')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (73, 'FI', 'Finland')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (74, 'FR', 'France')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (75, 'GF', 'French Guiana')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (76, 'PF', 'French Polynesia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (77, 'TF', 'French Southern Territories')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (78, 'GA', 'Gabon')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (79, 'GM', 'Gambia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (80, 'GE', 'Georgia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (81, 'DE', 'Germany')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (82, 'GH', 'Ghana')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (83, 'GI', 'Gibraltar')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (84, 'GR', 'Greece')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (85, 'GL', 'Greenland')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (86, 'GD', 'Grenada')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (87, 'GP', 'Guadeloupe')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (88, 'GU', 'Guam')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (89, 'GT', 'Guatemala')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (90, 'GG', 'Guernsey')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (91, 'GN', 'Guinea')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (92, 'GW', 'Guinea-Bissau')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (93, 'GY', 'Guyana')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (94, 'HT', 'Haiti')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (95, 'HM', 'Heard Island and McDonald Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (96, 'VA', 'Holy See (Vatican City State)')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (97, 'HN', 'Honduras')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (98, 'HK', 'Hong Kong')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (99, 'HU', 'Hungary')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (100, 'IS', 'Iceland')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (101, 'IN', 'India')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (102, 'ID', 'Indonesia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (103, 'IR', 'Iran, Islamic Republic of')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (104, 'IQ', 'Iraq')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (105, 'IE', 'Ireland')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (107, 'IL', 'Israel')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (108, 'IT', 'Italy')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (109, 'JM', 'Jamaica')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (110, 'JP', 'Japan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (111, 'JE', 'Jersey')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (112, 'JO', 'Jordan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (113, 'KZ', 'Kazakhstan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (114, 'KE', 'Kenya')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (115, 'KI', 'Kiribati')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (116, 'KP', 'Korea, Democratic People''s Republic of')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (117, 'KR', 'Korea, Republic of')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (118, 'KW', 'Kuwait')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (119, 'KG', 'Kyrgyzstan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (120, 'LA', 'Lao People''s Democratic Republic')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (121, 'LV', 'Latvia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (122, 'LB', 'Lebanon')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (123, 'LS', 'Lesotho')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (124, 'LR', 'Liberia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (125, 'LY', 'Libyan Arab Jamahiriya')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (126, 'LI', 'Liechtenstein')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (127, 'LT', 'Lithuania')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (128, 'LU', 'Luxembourg')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (129, 'MO', 'Macao')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (130, 'MK', 'Macedonia, The Former Yugoslav Republic of')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (131, 'MG', 'Madagascar')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (132, 'MW', 'Malawi')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (133, 'MY', 'Malaysia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (134, 'MV', 'Maldives')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (135, 'ML', 'Mali')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (136, 'MT', 'Malta')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (137, 'MH', 'Marshall Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (138, 'MQ', 'Martinique')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (139, 'MR', 'Mauritania')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (140, 'MU', 'Mauritius')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (141, 'YT', 'Mayotte')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (142, 'MX', 'Mexico')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (143, 'FM', 'Micronesia, Federated States of')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (144, 'MD', 'Moldova, Republic of')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (145, 'MC', 'Monaco')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (146, 'MN', 'Mongolia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (147, 'ME', 'Montenegro')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (148, 'MS', 'Montserrat')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (149, 'MA', 'Morocco')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (150, 'MZ', 'Mozambique')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (151, 'MM', 'Myanmar')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (152, 'NA', 'Namibia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (153, 'NR', 'Nauru')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (154, 'NP', 'Nepal')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (155, 'NL', 'Netherlands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (156, 'AN', 'Netherlands Antilles')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (157, 'NC', 'New Caledonia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (158, 'NZ', 'New Zealand')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (159, 'NI', 'Nicaragua')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (160, 'NE', 'Niger')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (161, 'NG', 'Nigeria')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (162, 'NU', 'Niue')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (163, 'NF', 'Norfolk Island')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (164, 'MP', 'Northern Mariana Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (165, 'NO', 'Norway')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (166, 'OM', 'Oman')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (167, 'PK', 'Pakistan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (168, 'PW', 'Palau')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (169, 'PS', 'Palestinian Territory, Occupied')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (170, 'PA', 'Panama')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (171, 'PG', 'Papua New Guinea')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (172, 'PY', 'Paraguay')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (173, 'PE', 'Peru')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (174, 'PH', 'Philippines')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (175, 'PN', 'Pitcairn')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (176, 'PL', 'Poland')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (177, 'PT', 'Portugal')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (178, 'PR', 'Puerto Rico')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (179, 'QA', 'Qatar')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (180, 'RE', 'Reunion')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (181, 'RO', 'Romania')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (182, 'RU', 'Russian Federation')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (183, 'RW', 'Rwanda')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (185, 'SH', 'Saint Helena')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (186, 'KN', 'Saint Kitts and Nevis')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (187, 'LC', 'Saint Lucia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (188, 'MF', 'Saint Martin')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (189, 'PM', 'Saint Pierre and Miquelon')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (190, 'VC', 'Saint Vincent and the Grenadines')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (191, 'WS', 'Samoa')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (192, 'SM', 'San Marino')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (193, 'ST', 'Sao Tome and Principe')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (194, 'SA', 'Saudi Arabia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (195, 'SN', 'Senegal')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (196, 'RS', 'Serbia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (197, 'SC', 'Seychelles')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (198, 'SL', 'Sierra Leone')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (199, 'SG', 'Singapore')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (200, 'SK', 'Slovakia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (201, 'SI', 'Slovenia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (202, 'SB', 'Solomon Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (203, 'SO', 'Somalia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (204, 'ZA', 'South Africa')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (205, 'GS', 'South Georgia and the South Sandwich Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (206, 'ES', 'Spain')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (207, 'LK', 'Sri Lanka')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (208, 'SD', 'Sudan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (209, 'SR', 'Suriname')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (210, 'SJ', 'Svalbard and Jan Mayen')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (211, 'SZ', 'Swaziland')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (212, 'SE', 'Sweden')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (213, 'CH', 'Switzerland')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (214, 'SY', 'Syrian Arab Republic')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (215, 'TW', 'Taiwan, Province Of China')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (216, 'TJ', 'Tajikistan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (217, 'TZ', 'Tanzania, United Republic of')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (218, 'TH', 'Thailand')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (219, 'TL', 'Timor-Leste')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (220, 'TG', 'Togo')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (221, 'TK', 'Tokelau')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (222, 'TO', 'Tonga')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (223, 'TT', 'Trinidad and Tobago')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (224, 'TN', 'Tunisia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (225, 'TR', 'Turkey')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (226, 'TM', 'Turkmenistan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (227, 'TC', 'Turks and Caicos Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (228, 'TV', 'Tuvalu')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (229, 'UG', 'Uganda')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (230, 'UA', 'Ukraine')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (231, 'AE', 'United Arab Emirates')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (232, 'GB', 'United Kingdom')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (233, 'US', 'United States')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (234, 'UM', 'United States Minor Outlying Islands')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (235, 'UY', 'Uruguay')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (236, 'UZ', 'Uzbekistan')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (237, 'VU', 'Vanuatu')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (238, 'VE', 'Venezuela')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (239, 'VN', 'Viet Nam')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (240, 'VG', 'Virgin Islands, British')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (241, 'VI', 'Virgin Islands, U.S.')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (242, 'WF', 'Wallis And Futuna')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (243, 'EH', 'Western Sahara')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (244, 'YE', 'Yemen')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (245, 'ZM', 'Zambia')"); $database->query();
		$database->setQuery("INSERT INTO `#__bl_countries` VALUES (246, 'ZW', 'Zimbabwe')"); $database->query();
	}
	
	$query = "ALTER TABLE `jos_bl_season_teams` ADD `bonus_point` INT DEFAULT '0' NOT NULL";
	$database->setQuery($query);
	$database->query();
	$query = "ALTER TABLE `jos_bl_season_players` ADD `bonus_point` INT DEFAULT '0' NOT NULL";
	$database->setQuery($query);
	$database->query();
	
	$query = "ALTER TABLE `jos_bl_tournament` ADD `logo` VARCHAR( 255 ) NOT NULL";
	$database->setQuery($query);
	$database->query();
	//reg config
	$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='nick_reg'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('nick_reg', '0')");

		$database->query();

	}
	$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='nick_reg_rq'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('nick_reg_rq', '0')");

		$database->query();

	}
	$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='country_reg'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('country_reg', '0')");

		$database->query();

	}
	$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='country_reg_rq'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('country_reg_rq', '0')");

		$database->query();

	}
	$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='mcomments'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('mcomments', '0')");

		$database->query();

	}
	
	$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='player_reg'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('player_reg', '0')");

		$database->query();

	}
	
	$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='team_reg'";

	$database->setQuery($query);

	if(!$database->loadResult()){

		$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('team_reg', '0')");

		$database->query();

	}


//conversion//
	
	$database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `field_type` char(1) NOT NULL default '0',ADD `reg_exist` char(1) NOT NULL default '0',ADD `reg_require` char(1) NOT NULL default '0',ADD  `fdisplay` char(1) NOT NULL default '1'");

	$database->query();
	
	$database->setQuery("ALTER TABLE `#__bl_extra_values` ADD `fvalue_text` text NOT NULL");

	$database->query();
	
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `k_ordering` int(11) NOT NULL DEFAULT '0'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `k_title` varchar(255) NOT NULL DEFAULT ''");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `k_stage` int(11) NOT NULL DEFAULT '1'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `points1` decimal(10,2) NOT NULL DEFAULT '0.00'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `points2` decimal(10,2) NOT NULL DEFAULT '0.00'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_match` ADD `new_points` char(1) NOT NULL DEFAULT '0'");
	
	$database->query();
	
	$database->setQuery("ALTER TABLE `#__bl_matchday` ADD `k_format` int(11) NOT NULL default '0'");

	$database->query();	

	$database->setQuery("ALTER TABLE `#__bl_players` ADD `usr_id` int(11) NOT NULL default '0',ADD `country_id` int(11) NOT NULL default '0',ADD `registered` char(1) NOT NULL default '0'");

	$database->query();	
	
	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_participant` int(11) NOT NULL DEFAULT '0'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_reg` char(1) NOT NULL DEFAULT '0'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD  `reg_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD  `reg_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
	$database->query();
	$database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_rules` text NOT NULL");
	$database->query();
		
	$database->setQuery("ALTER TABLE `#__bl_tournament` ADD `t_type` int(1) NOT NULL default '0', ADD `t_single` char(1) NOT NULL default '0'");

	$database->query();	
	
	$database->setQuery("ALTER TABLE  `#__bl_extra_filds` ADD  `faccess` VARCHAR( 1 ) DEFAULT  '0' NOT NULL");
	$database->query();	
	
	$database->setQuery("SELECT COUNT(*) FROM #__bl_players_team");
	if(!$database->loadResult()){
		$query = "SELECT * FROM `#__bl_players`";
		$database->setQuery($query);
		$pl = $database->loadObjectList();
		for($i=0;$i<count($pl);$i++){
			$mp = $pl[$i];
			$database->setQuery("INSERT INTO `#__bl_players_team`(team_id,player_id) VALUES({$mp->team_id},{$mp->id})");
			$database->query();
		}
	}
	
	// Positions to additional fields
		$query = "SELECT * FROM #__bl_positions";
		$database->setQuery($query);
		$pos = $database->loadObjectList();
		
		if(count($pos)){
			$query = "INSERT INTO #__bl_extra_filds(id,name,e_table_view,field_type) VALUES('','Position','1','3')";
			$database->setQuery($query);
			$database->query();
			$iid=$database->insertid();
			foreach($pos as $ps){
				$query = "INSERT INTO #__bl_extra_select(id,fid,sel_value) VALUES('','".$iid."','".$ps->p_name."')";
				$database->setQuery($query);
				$database->query();
				$ffid=$database->insertid();
				
				$query = "SELECT id FROM #__bl_players WHERE position_id=".$ps->p_id;
				$database->setQuery($query);
				$plid = $database->loadResultArray();
				if(count($plid)){
					foreach($plid as $pld){
						$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue) VALUES('".$iid."','".$pld."','".$ffid."')";
						$database->setQuery($query);
						$database->query();
					}
				}
			}
		}
		$query = "TRUNCATE TABLE #__bl_positions";
		$database->setQuery($query);
		$database->query();
	// 2.0.8.1
	
		$query = "ALTER TABLE  `#__bl_events` ADD  `result_type` VARCHAR( 1 ) NOT NULL DEFAULT  '0'";
		$database->setQuery($query);
		$database->query();
		
		$query = "ALTER TABLE  `#__bl_players` ADD  `created_by` INT NOT NULL DEFAULT  '62'";
		$database->setQuery($query);
		$database->query();
		
		//add player function moderator
		$query = "SELECT cfg_value FROM `#__bl_config` WHERE cfg_name='moder_addplayer'";

		$database->setQuery($query);

		if(!$database->loadResult()){

			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('moder_addplayer', '0')");

			$database->query();

		}
		
		//add player default ordering
		$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='pllist_order'";

		$database->setQuery($query);

		if(!$database->loadResult()){

			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('pllist_order', '0')");

			$database->query();

		}
		//add width logo team
		$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='teamlogo_height'";

		$database->setQuery($query);

		if(!$database->loadResult()){

			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('teamlogo_height', '30')");

			$database->query();

		}
		
		//account limits
		$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='teams_per_account'";

		$database->setQuery($query);

		if(!$database->loadResult()){

			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('teams_per_account', '5')");

			$database->query();

		}
		$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='players_per_account'";

		$database->setQuery($query);

		if(!$database->loadResult()){

			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('players_per_account', '10')");

			$database->query();

		}
		//for venue
		$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='unbl_venue'";

		$database->setQuery($query);

		if(!$database->loadResult()){

			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('unbl_venue', '0')");

			$database->query();

		}
		$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='cal_venue'";

		$database->setQuery($query);

		if(!$database->loadResult()){

			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('cal_venue', '0')");

			$database->query();

		}
		$query = "ALTER TABLE  `#__bl_match` ADD  `venue_id` INT NOT NULL";
		$database->setQuery($query);
		
		///for maps
		$database->setQuery("ALTER TABLE `#__bl_maps` ADD  `map_img` VARCHAR( 255 ) NOT NULL");
		$database->query();
		
		//team created by
		$database->setQuery("ALTER TABLE  `#__bl_teams` ADD  `created_by` INT NOT NULL DEFAULT '0'");
		$database->query();
		
		//players in teams by season
		$database->setQuery("ALTER TABLE  `#__bl_players_team` ADD  `season_id` INT NOT NULL");
		$database->query();
		
		$database->setQuery("ALTER TABLE  `#__bl_players_team` DROP PRIMARY KEY , ADD PRIMARY KEY (  `team_id` ,  `player_id` ,  `season_id` )");
		$database->query();

	$database->setQuery("SELECT COUNT(*) FROM `#__bl_players_team` WHERE season_id != 0");
	$scount = $database->loadResult();
		if(!$scount){
			$database->setQuery("SELECT DISTINCT(team_id) FROM `#__bl_players_team`");
			$teamsseas = $database->loadResultArray();
			
			for($i=0;$i<count($teamsseas);$i++){
				$teamid = $teamsseas[$i];
				
				$database->setQuery("SELECT season_id FROM `#__bl_season_teams` WHERE team_id = ".$teamid);
				$teamsseas_q = $database->loadResultArray();
				$database->setQuery("SELECT player_id FROM `#__bl_players_team` WHERE team_id = ".$teamid);
				$teampl = $database->loadResultArray();
				//var_dump($teampl);
				//var_dump($teamsseas);
				if(count($teamsseas_q) && count($teampl)){
					foreach($teamsseas_q as $seas){
						$query = "INSERT INTO `#__bl_players_team` (`team_id`, `player_id`, `season_id`) VALUES";
						$tr = 0;
						foreach($teampl as $pl){
							if($tr) { $query .= ",";}
							$query .= "(".$teamid.",".$pl.",".$seas.")";
							$tr++;
						}
						
						$database->setQuery($query);
						$database->query();
					}
				}
			}
		}	
		$query = "DELETE FROM `#__bl_players_team` WHERE season_id = 0";
		$database->setQuery($query);
		$database->query();
		
		$database->setQuery("ALTER TABLE `#__bl_match` ADD `venue_id` INT NOT NULL");

		$database->query();
		
		//new events type sum
		$database->setQuery("ALTER TABLE  `#__bl_events` ADD  `sumev1` INT NOT NULL ,ADD  `sumev2` INT NOT NULL");
		$database->query();
		//match played
		$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='played_matches'";
		$database->setQuery($query);
		if(!$database->loadResult()){
			$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('played_matches', '1')");
			$database->query();
		}
		//
		$database->SetQuery("ALTER TABLE  `#__bl_extra_values` ADD  `season_id` INT NOT NULL");
		$database->query();
		
		$database->SetQuery("ALTER TABLE `#__bl_extra_values` DROP PRIMARY KEY ,ADD PRIMARY KEY (  `f_id` ,  `uid` ,  `season_id` )");
		$database->query();
		//ordering
		$database->SetQuery("ALTER TABLE  `#__bl_seasons` ADD  `ordering` INT NOT NULL");
		$database->query();
		$database->SetQuery("ALTER TABLE  `#__bl_matchday` ADD  `ordering` INT NOT NULL");
		$database->query();
		$database->SetQuery("ALTER TABLE  `#__bl_events` ADD  `ordering` INT NOT NULL");
		$database->query();
		//	nick or name	
		$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('player_name',  '0')");
		$database->query();
		//esport config
		$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_invite_player',  '0')");
		$database->query();
		$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_invite_confirm',  '0')");
		$database->query();
		$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_invite_unregister',  '0')");
		$database->query();
		$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_join_team',  '0')");
		$database->query();
		$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('esport_invite_match', '0')");
		$database->query();
		
		//__bl_players_team
		$database->SetQuery("ALTER TABLE  `#__bl_players_team` ADD  `invitekey` VARCHAR( 255 ) NOT NULL");
		$database->query();
		$database->SetQuery("ALTER TABLE  `#__bl_players_team` ADD  `player_join` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
		$database->query();
		
		//home venue
		$database->SetQuery("ALTER TABLE  `#__bl_teams` ADD  `venue_id` INT NOT NULL");
		$database->query();
		
		//admin rights
		$database->SetQuery("INSERT INTO `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('jssa_editplayer',  '1')");
		$database->query();
		$database->SetQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jssa_deleteplayers', '1')");
		$database->query();
		
		//extra time for knockout
		$database->setQuery("ALTER TABLE  `#__bl_match` ADD  `aet1` INT NOT NULL");

		$database->query();
		
		$database->setQuery("ALTER TABLE  `#__bl_match` ADD  `aet2` INT NOT NULL");

		$database->query();
		
		$database->setQuery("ALTER TABLE  `#__bl_match` ADD  `p_winner` INT NOT NULL");

		$database->query();
		
		///show registered to season from FE
		
		$database->setQuery("ALTER TABLE  `#__bl_season_players` ADD  `regtype` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
		$database->query();
		$database->setQuery("ALTER TABLE  `#__bl_season_teams` ADD  `regtype` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
		$database->query();
		
		//accepted match squard
		$database->setQuery("ALTER TABLE  `#__bl_squard` ADD  `accepted` VARCHAR( 1 ) NOT NULL DEFAULT  '1'");
		$database->query();
		
		//knock_style
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('knock_style', '0')");
		$database->query();
		
		$database->setQuery("ALTER TABLE  `#__bl_match` ADD  `m_single` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
		$database->query();
		
		//templates
		
		$database->setQuery("INSERT INTO  `#__bl_templates` (`id` ,`name` ,`isdefault`) VALUES ('1',  'default',  '1')");
		$database->query();
		
		
		////////BETTING

		$database->setQuery("ALTER TABLE `#__bl_seasons` ADD `idtemplate` INT NOT NULL");
		$database->query();
		$database->setQuery("ALTER TABLE `#__bl_match` ADD `betavailable` TINYINT(4) NOT NULL");
		$database->query();
		$database->setQuery("ALTER TABLE `#__bl_match` ADD `betfinishdate` DATE NOT NULL DEFAULT '0000-00-00'");
		$database->query();
		$database->setQuery("ALTER TABLE `#__bl_match` ADD `betfinishtime` VARCHAR(10) NOT NULL");
		$database->query();
		
		//social buttons
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_twitter', '0')");
		$database->query();
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_gplus', '0')");
		$database->query();
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_fbshare', '0')");
		$database->query();
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_fblike', '0')");
		$database->query();
		
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_season', '0')");
		$database->query();
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_team', '0')");
		$database->query();
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_player', '0')");
		$database->query();
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_match', '0')");
		$database->query();
		$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_venue', '0')");
		$database->query();
		
	
include_once($adminDir.DS.'jbl_start.php');


}
