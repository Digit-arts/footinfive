<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$document		=& JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'modules/mod_js_results/css/mod_js_results.css'); 
$cItemId = $params->get('customitemid');
$Itemid = JRequest::getInt('Itemid');
if(!$cItemId){
	$cItemId = $Itemid;
}
$old_date = '';
$ssss_id = $params->get( 'sidgid' );
$ex = explode('|',$ssss_id );
$s_id = $ex[0];
require_once('components/com_joomsport/includes/func.php');
?>

<table align="center" cellpadding="3" border="0" class="jsm_restable">
<?php 
if(count($list)){
foreach ($list as $match) : 

if($old_date != $match->m_date.' '.$match->m_time){
?>
<tr>
	<td colspan="3" class="match_date">
		<?php
			echo date_bl($match->m_date,$match->m_time);
		?>
	</td>
</tr>
<?php
}
 $old_date = $match->m_date.' '.$match->m_time;
?>
<?php

if($single || (isset($match->ssingle) && $match->ssingle)){
	$match->emb1 = modBlResHelper::getPhoto($match->hm_id);
	$match->emb2 = modBlResHelper::getPhoto($match->aw_id);
}
if($embl_is && (is_file('media/bearleague/'.$match->emb1) || is_file('media/bearleague/'.$match->emb2))){
	echo "<tr><td class='emblem_thome'>";
	if($match->emb1 && is_file('media/bearleague/'.$match->emb1) && $embl_is){

		echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb1.'" width="40">';

	}
	echo "</td>";
	echo "<td>&nbsp;</td>";
	echo "<td class='emblem_taway'>";
	if($match->emb2 && is_file('media/bearleague/'.$match->emb2) && $embl_is){

		echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb2.'" width="40">';

	}
	echo "</td></tr>";
}
?>
<tr>
			
			<td class="team_thome"><?php 
				if($single || (isset($match->ssingle) && $match->ssingle)){
					echo '<a href="'.JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->hm_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->home.'</a>';
				}else{
					echo '<a href="'.JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->hm_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->home.'</a>';
				}
			?></td>
			<td class="match_result" nowrap="nowrap">
				<a href="index.php?option=com_joomsport&task=view_match&id=<?php echo $match->id?>&Itemid=<?php echo $cItemId;?>">
				<?php
					echo $match->score1?> : <?php echo $match->score2; //if($et && $match->is_extra){ echo " (".$bl_lang['BL_RES_EXTRA'].")";}
					echo '</a>';
				?>	
			</td>
			<td class="team_taway"><?php 
			if($single || (isset($match->ssingle) && $match->ssingle)){
				echo '<a href="'.JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->aw_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->away.'</a>';
			}else{
				echo '<a href="'.JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->aw_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->away.'</a>';
			}
			?></td>
			 
		</tr>
<?php endforeach; 
}
?>
</table>
