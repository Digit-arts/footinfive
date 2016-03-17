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

class joomsportViewplayerlist extends JView
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
		$pagination =  $this->_model->_pagination;
		$lists["t_single"] = $this->_model->t_single;
		$lists["s_id"] = $this->_model->s_id;
		$lists["field"] = $this->_model->sortfield;
		$lists["dest"] = $this->_model->sortdest;
		$lists["limit"] = $this->_model->limit;
		$lists["limitstart"] = $this->_model->limitstart;
		
		$this->assignRef('params',		$params); 
		$this->assignRef('page',	$pagination);
		$this->assignRef('lists', $lists);
		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
		
		
	}
	
		
}
