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

class joomsportViewmoderedit_matchday extends JView

{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
	}
	function display($tpl = null)
	{
		$this->_model->getData();
		$lists = $this->_model->_lists;
		$editor =& JFactory::getEditor(); 
		$params = $this->_model->_params;
		$row = $this->_model->_data;
		$lists["s_id"] = $this->_model->season_id;
		$lists["tid"] = $this->_model->tid;
		$lists["mid"] = $this->_model->mid;
		$lists["t_type"] = $this->_model->t_type;
		
		$this->assignRef('params',		$params); 
		$this->assignRef('row',		$row); 
		$this->assignRef('lists', $lists);
		$this->assignRef('editor', $editor);

		require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
}

