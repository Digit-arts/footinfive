<?php/*------------------------------------------------------------------------# JoomSport Professional # ------------------------------------------------------------------------# BearDev development company # Copyright (C) 2011 JoomSport.com. All Rights Reserved.# @license - http://joomsport.com/news/license.html GNU/GPL# Websites: http://www.JoomSport.com # Technical Support:  Forum - http://joomsport.com/helpdesk/-------------------------------------------------------------------------*/// no direct accessdefined( '_JEXEC' ) or die( 'Restricted access' );class JElementSeason extends JElement{	/**	 * Element name	 *	 * @access	protected	 * @var		string	 */	var	$_name = 'Season';	function fetchElement($name, $value, &$node, $control_name)	{		global $mainframe;		$db			=& JFactory::getDBO();		$doc 		=& JFactory::getDocument();		$template 	= $mainframe->getTemplate();		$fieldName	= $control_name.'['.$name.']';		$article->title = '';		if($name == 'sid'){			if ($value) {				$query = "SELECT CONCAT(t.name,' ',s.s_name) as name FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.t_id = t.id AND s.s_id = ".$value;				$db->setQuery($query);							$rows = $db->loadObjectList();				if(isset($rows[0])){					$row = $rows[0];					$article->title = $row->name;				}			} else {				$article->title = JText::_('BLBE_SELSEASN');			}			$task = 'season_menu';		}else{			if ($value) {				$query = "SELECT group_name FROM #__bl_groups  WHERE id = ".$value;				$db->setQuery($query);							$rows = $db->loadObjectList();				if(isset($rows[0])){					$row = $rows[0];					$article->title = $row->group_name;				}			} else {				$article->title = JText::_('BLBE_SELGROUP');			}			$task = 'group_menu';		}		$js = "		function jSelectArticle(id, title, object) {			document.getElementById(object + '_id').value = id;			document.getElementById(object + '_name').value = title;			document.getElementById('sbox-window').close();		}";		$doc->addScriptDeclaration($js);		$link = 'index.php?option=com_joomsport&amp;task='.$task.'&amp;tmpl=component&amp;object='.$name;		JHTML::_('behavior.modal', 'a.modal');		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';//		$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('Select')."\" />";		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.$article->title.'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';		return $html;	}}