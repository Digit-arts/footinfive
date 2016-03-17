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
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the Registration component
 *
 * @package		Joomla
 * @subpackage	Registration
 * @since 1.0
 */
class joomsportViewteamlist extends JView
{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
	}
	function display($tpl = null)
	{
		$lists = $this->_model->_lists;
		$data = $this->_model->_data;
		$page = $this->_model->_pagination;
		$params = $this->_model->_params;
		$lists["s_id"] = $this->_model->s_id;

		$this->assignRef('params',		$params); 
		$this->assignRef('row',		$data); 
		$this->assignRef('page',		$page); 
		$this->assignRef('lists', $lists);
		$this->assignRef('s_id', $s_id);

		require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
	
	
}
