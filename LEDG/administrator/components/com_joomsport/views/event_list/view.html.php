<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewevent_list extends JView
{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
	}
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		// Get data from the model
		$items		= $this->_model->_data;
		$total		= $this->_model->_total;
		$pagination =  $this->_model->_pagination;

		$this->addToolbar();

		$this->assignRef('rows',		$items);
		$this->assignRef('page',	$pagination);

		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'BLBE_EVENTLIST' ), 'event.png' );
		JToolBarHelper::deleteList('','event_del',JText::_( 'BLBE_DELETE'));
		JToolBarHelper::editListX('event_edit');
		JToolBarHelper::addNewX('event_add');
	}
}