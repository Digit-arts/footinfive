<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewgroup_edit extends JView
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
		$lists		= $this->_model->_lists;
	
		$this->addToolbar($this->_model->_mode);
		
		
		$editor =& JFactory::getEditor();
		$this->assignRef('editor',		$editor);
		$this->assignRef('lists',		$lists);
		$this->assignRef('row',		$items);
		

		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	protected function addToolbar($edit)
	{
		$text = ( $edit ? JText::_( 'BLBE_EDIT' ) : JText::_( 'BLBE_NEW' ) );
		JToolBarHelper::title( JText::_( 'BLBE_GROUP' ).': <small><small>[ '. $text.' ]</small></small>', 'group.png' );
		JToolBarHelper::apply('group_apply');
		JToolBarHelper::save('group_save');
		
		$version = new JVersion;
		$joomla_v = $version->getShortVersion();
		if(substr($joomla_v,0,3) >= '1.7'){
			JToolBarHelper::save2new('group_save_new');
		}else{
			JToolBarHelper::save('group_save_new',JText::_("JSTOOL_SAVE_NEW"));
		}
		if ( $edit ) {
			JToolBarHelper::cancel( 'group_list', JText::_('BLBE_CLOSE') );
		} else {
			JToolBarHelper::cancel('group_list');
		}
		
	}
}