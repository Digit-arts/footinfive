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
class JElementMatch extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'match';
	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mainframe;
		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name.'['.$name.']';
		$article->title='';
		if ($value) {
			$query = "SELECT s_id FROM #__bl_matchday as md, #__bl_match as m  WHERE md.id=m.m_id AND m.id = ".$value;
			$db->setQuery($query);
			$season_id = $db->loadResult();
			
			
			$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_type,t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($season_id)." AND s.t_id = t.id ORDER BY t.name, s.s_name";
			$db->setQuery($query);
			$tourn = $db->loadObjectList();
			
			$lists['t_single'] = $tourn[0]->t_single;
			$lists['t_type'] = $tourn[0]->t_type;
			if($lists['t_single']){
				$query = "SELECT m.*, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id WHERE m.m_id = md.id AND m.published = 1 AND m.id = ".$value;
			}else{
				$query = "SELECT m.*,t1.t_name as home,t2.t_name as away FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id WHERE m.m_id = md.id AND m.published = 1  AND m.id = ".$value;
			}
			$db->setQuery($query);
		
			$rows = $db->loadObjectList();
			if(isset($rows[0])){
				$row = $rows[0];
				$article->title = $row->home." ".$row->score1.":".$row->score2." ".$row->away;
			}
		} else {
			$article->title = JText::_('BLBE_SELMATCHY');
		}
		$js = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);
		$link = 'index.php?option=com_joomsport&amp;task=match_menu&amp;tmpl=component&amp;object='.$name;
		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
//		$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('Select')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('BLBE_SELMATCHY').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';
		return $html;
	}
}
