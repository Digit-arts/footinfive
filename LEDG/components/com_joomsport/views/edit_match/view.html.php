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
class joomsportViewedit_match extends JView
{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
	}
	function display($tpl = null)
	{
		$this->_model->getData();
		$lists = $this->_model->_lists;
		
		$params = $this->_model->_params;
		$row = $this->_model->_data;
		$lists["s_id"] = $this->_model->season_id;
		$lists["t_single"] = $this->_model->t_single;
		$lists["t_type"] = $this->_model->t_type;
		
		$this->assignRef('params',		$params); 
		$this->assignRef('row',		$row); 
		$this->assignRef('lists', $lists);

		require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
}
