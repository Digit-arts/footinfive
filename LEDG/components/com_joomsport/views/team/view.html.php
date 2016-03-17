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
		$s_id = $this->_model->s_id;
		$curcal = $this->_model->curcal;
		
		$db		=& JFactory::getDBO();
		
		$query = "SELECT pt.team_id,ev.f_id,ef.name, ev.fvalue,es.sel_value, count(ev.uid) as vote_equipe FROM #__bl_extra_values as ev, #__bl_players_team as pt, #__bl_extra_select as es, #__bl_extra_filds as ef  WHERE  ef.id=ev.f_id and es.id=ev.fvalue and ev.uid=pt.player_id and pt.season_id=".$s_id." and ev.f_id=6 and pt.team_id=".$tid." group by pt.team_id,ev.f_id,ev.fvalue  order by pt.team_id,ev.f_id,ev.fvalue";
		//echo $query;
		$db->setQuery($query);
		$vote_horaires = $db->loadObjectList();
		
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
		$this->assignRef('vote_horaires', $vote_horaires);
		
		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	
}
