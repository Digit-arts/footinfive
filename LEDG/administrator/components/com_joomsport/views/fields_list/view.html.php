<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewfields_list extends JView
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
		$lists		= $this->_model->_lists;
		$pagination =  $this->_model->_pagination;

		$this->addToolbar();
		$this->assignRef('lists',		$lists);
		$this->assignRef('rows',		$items);
		$this->assignRef('page',	$pagination);

		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'BLBE_FIELDLIST' ), 'additional.png' );
		JToolBarHelper::publishList('fields_publ');
		JToolBarHelper::unpublishList('fields_unpubl');
		JToolBarHelper::deleteList('','fields_del',JText::_('BLBE_DELETE'));
		JToolBarHelper::editListX('fields_edit');
		JToolBarHelper::addNewX('fields_add');
	}
}