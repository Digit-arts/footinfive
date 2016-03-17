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

class joomsportViewteam extends JView
{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
	}
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$Itemid = JRequest::getInt('Itemid'); 
   
	JHTML::_('behavior.modal', 'a.team-images');
		$pathway  =& $mainframe->getPathway();
		$document =& JFactory::getDocument();
		$params	= &$mainframe->getParams();
	 	// Page Title
		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();
		
		$this->_model->getData();
		$lists = $this->_model->_lists;
		$tid = $this->_model->team_id;
		$curcal = $this->_model->curcal;
		
		$db		=& JFactory::getDBO();
		
		
		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JRegistry;//new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	$lists["team"]->t_name);
			}
		} else {
			$params->set('page_title',	$lists["team"]->t_name);
		}
		$document->setTitle( $params->get( 'page_title' ) );
		$pathway->addItem( JText::_( $lists["team"]->t_name ));
		// table league

		$pagination =  $this->_model->_pagination;
		
		
		$this->assignRef('params',		$params); 
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('tid',		$tid);
		$this->assignRef('page',	$pagination);
		$this->assignRef('s_id',		$s_id);
		$this->assignRef('curcal',		$curcal);
		
		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	
}
