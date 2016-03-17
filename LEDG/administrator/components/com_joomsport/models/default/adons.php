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

class adonsJSModel extends JSPRO_Models
{
	
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();
	
	}
	function getData(){
		$query = "SELECT * FROM #__bl_addons";
		$this->db->setQuery($query);
		$this->_data = $this->db->loadObjectList();
	}
	function addonInstall(){
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.path');
		$filename = $_FILES['addon_installer']['name'];
		$baseDir = JPATH_ROOT."/tmp/";;
		if (file_exists( $baseDir )) {
			if (is_writable( $baseDir )) {
				if (move_uploaded_file( $_FILES['addon_installer']['tmp_name'], $baseDir . $filename )) {
				
					if (JPath::setPermissions( $baseDir . $filename )) {
						$msg = '';
					} else {
						$msg = 'Failed to change the permissions of the uploaded file.';
					}
				} else {
					$msg = 'Failed to move uploaded file to <code>/tmp</code> directory.';
				}
			} else {
				$msg = 'Upload failed as <code>/tmp</code> directory is not writable.';
			}
		} else {
			$msg = 'Upload failed as <code>/tmp</code> directory does not exist.';
		}
		if($msg != ''){
			JError::raiseError(500, $msg );
		} 
		$retval = JInstallerHelper::unpack($baseDir . $filename);
		if(count($retval)){
			if(is_dir($retval['dir']."/BE/models/")){
				$this->_copy_directory($retval['dir']."/BE/models/",JPATH_ROOT."/administrator/components/com_joomsport/models");
			}
			if(is_dir($retval['dir']."/BE/views/")){
				$this->_copy_directory($retval['dir']."/BE/views/",JPATH_ROOT."/administrator/components/com_joomsport/views");
			}
			if(is_dir($retval['dir']."/BE/tables/")){
				$this->_copy_directory($retval['dir']."/BE/tables/",JPATH_ROOT."/administrator/components/com_joomsport/tables");
			}
			if(is_dir($retval['dir']."/FE/models/")){
				$this->_copy_directory($retval['dir']."/FE/models/",JPATH_ROOT."/components/com_joomsport/models");
			}
			if(is_dir($retval['dir']."/FE/views/")){
				$this->_copy_directory($retval['dir']."/FE/views/",JPATH_ROOT."/components/com_joomsport/views");
			}
		
			$xml = JFactory::getXML($retval['dir']."/addon.xml");
			
			
			if (file_exists($retval['dir']."/addon.sql.xml"))  {
				$sql = JFactory::getXML($retval['dir']."/addon.sql.xml");
				$queries = $sql->query;
				if ($queries) {
					foreach($queries as $q) {
						$this->db->setQuery($q);
						$this->db->query();
					}                            
				}
			}
			$query = "INSERT INTO #__bl_addons(name,title,description,version,published) VALUES('{$xml->name}','{$xml->title}','{$xml->description}','{$xml->version}','0')";
			$this->db->setQuery($query);
			$this->db->query();
			
		}
	}
	function _copy_directory( $source, $destination ) {
	if ( is_dir( $source ) ) {
		@mkdir( $destination );
		$directory = dir( $source );
		while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
			if ( $readdirectory == '.' || $readdirectory == '..' ) {
				continue;
			}
			$PathDir = $source . '/' . $readdirectory; 
			if ( is_dir( $PathDir ) ) {
				$this->_copy_directory( $PathDir, $destination . '/' . $readdirectory );
				continue;
			}
			copy( $PathDir, $destination . '/' . $readdirectory );
		}
 
		$directory->close();
	}else {
		copy( $source, $destination );
	}
}
	function addonDel(){
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `#__bl_addons` WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			
		}	
	}
}