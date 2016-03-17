<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$document		=& JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'modules/mod_js_table/css/mod_js_table.css'); 
$old_gid = '-1';
$z=0;
$place_display 	= $params->get( 'place_display' );
$ssss_id = $params->get( 'sidgid' );
	if($ssss_id){
		$ex = explode('|',$ssss_id );
		$gr_id = $ex[1];
		$s_id = $ex[0];
	}else{
		$gr_id=0;
	}	
$cItemId = $params->get('customitemid');
$Itemid = JRequest::getInt('Itemid');
if(!$cItemId){
	$cItemId = $Itemid;
}
?>

<?php foreach ($list as $team){
	$show = false;
	if(!$gr_id || $gr_id == $team['g_id']){
		$show = true;
	}
	if(!$et){
		$show = true;
	}
	
	if($show){
	
	$z++;
	if($place_display && ($place_display < $z) && $old_gid == $team['g_id']){
		
	}else{
	
if($old_gid != $team['g_id']){
	if($z != 1){
		echo '</table><br />';
	}
	$z=1;
	//echo '</table><br />';
	if(isset($team['g_name']) && $team['g_name']){  echo '<h4>'.$team['g_name'].'</h4>';}
	echo '<table width="100%" class="tblview'.$params->get( 'moduleclass_sfx' ).'">';
	?>
	<tr>
		<th width="20">№</th>
		<?php if($params->get('emblem_chk') == 1) { ?>
		<th style="border-right:0px;"></th>
		<?php } ?>
		<th style="border-left:0px;">
		<div <?php if($params->get('emblem_chk') == 1) { ?>style="margin-left:-15px;text-align:left;"<?php }?>><?php echo $team['t_single']?JText::_('MTBL_PARTICS'):JText::_('MTBL_TEAM');?></div>
		</th>
		<?php if($params->get('played_chk') == 1) { ?>
		<th>
		<?php echo JText::_('MTBL_PLAYED');?>
		</th>
		<?php } ?>
		<?php if($params->get('win_chk') == 1) { ?>
		<th  ><?php echo JText::_('BL_TBL_WINS');?></th>
		<?php } ?>
		<?php if($params->get('draw_chk') == 1) { ?>
		<th  ><?php echo JText::_('BL_TBL_DRAW');?></th>
		<?php } ?>
		<?php if($params->get('lost_chk') == 1) { ?>
		<th  ><?php echo JText::_('BL_TBL_LOST');?></th>
		<?php } ?>
		
		<?php if($params->get('diff_chk') == 1) { ?>
		<th><?php echo JText::_('BL_TBL_DIFF');?></th>
		<?php } ?>
		<?php if($params->get('gd_chk') == 1) { ?>
		<th  ><?php echo JText::_('BL_TBL_GD');?></th>
		<?php } ?>
		<?php if($params->get('goalscore_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTGSC');?></th>

		<?php

		}?>
		<?php if($params->get('goalconc_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTGCC');?></th>

		<?php

		}?>
		<?php if($params->get('winhome_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTWHC');?></th>

		<?php

		}?>
		<?php if($params->get('winaway_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTWAC');?></th>

		<?php

		}?>
		<?php if($params->get('drawhome_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTDHC');?></th>

		<?php

		}?>
		<?php if($params->get('drawaway_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTDAC');?></th>

		<?php

		}?>
		<?php if($params->get('losthome_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTLHC');?></th>

		<?php

		}?>
		<?php if($params->get('lostaway_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTLAC');?></th>

		<?php

		}?>
		<?php if($params->get('pointshome_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTPHC');?></th>

		<?php

		}?>
		<?php if($params->get('pointsaway_chk') == 1) { ?>

		<th  ><?php echo JText::_('MTBL_TTPAC');?></th>

		<?php

		}?>
		<?php if($params->get('point_chk') == 1):?>
		<th>
		<?php echo JText::_('MTBL_POINTS')?>
		</th>
		<?php endif;?>
		<?php if($params->get('percent_chk') == 1) { ?>
		<th  ><?php echo JText::_('BL_TBL_WINPERCENT');?></th>
		<?php
		}?>
	</tr>
	<?php
	//echo "<tr><td colspan=4 class='tblclear".$params->get( 'moduleclass_sfx' )."'><hr /></td></tr>";
}
?>
<tr class="tblro<?php echo $z%2?><?php echo $params->get( 'moduleclass_sfx' ) ?>">
	<td width="20"><?php echo $z;?></td>
	<?php if($params->get('emblem_chk') == 1) { ?>
	<td style="border-right:0px;">
		<?php
		
		if($team['t_emblem'] && is_file('media/bearleague/'.$team['t_emblem'])){
			echo '<img src="'.JURI::base().'media/bearleague/'.$team['t_emblem'].'" class="embl_in_tblmod">';
		}
		?>
	</td>
	<?php } ?>
	<td width="80%" style="text-align:left;border-left:0px; padding:0px 10px;" nowrap='nowrap' class="blteamname<?php echo $params->get( 'moduleclass_sfx' ) ?>">
		
		<?php 
			if($team['t_single']){
				$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$team['tid'].'&sid='.$s_id.'&Itemid='.$cItemId); 
			}else{
				$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$team['tid'].'&sid='.$s_id.'&Itemid='.$cItemId); 
			}
			?>
		
		<a href="<?php echo $link;?>"><?php echo $team['name']?></a>
		<?php
			
			//echo $team['name'];
		?>
	</td>
	<?php if($params->get('played_chk') == 1) { ?>
	<td >
		<?php
			echo $team['played'];
		?>
	</td>
	<?php } ?>
	<?php if($params->get('win_chk') == 1) { ?>
			<td><?php echo $team['win']?></td>
			<?php } ?>
	<?php if($params->get('draw_chk') == 1) { ?>
	<td><?php echo $team['draw']?></td>
	<?php } ?>
	<?php if($params->get('lost_chk') == 1) { ?>
	<td><?php echo $team['lost']?></td>
	<?php } ?>
	
	<?php if($params->get('diff_chk') == 1) { ?>
	<td nowrap><?php echo $team['goals']?></td>
	<?php } ?>
	<?php if($params->get('gd_chk') == 1) { ?>
	<td><?php echo $team['gd']?></td>
	<?php } ?>
	<?php if($params->get('goalscore_chk') == 1) { ?>
	<td><?php echo $team['goals_score']?></td>
	<?php } ?>
	<?php if($params->get('goalconc_chk') == 1) { ?>
	<td><?php echo $team['goals_conc']?></td>
	<?php } ?>
	<?php if($params->get('winhome_chk') == 1) { ?>
	<td><?php echo $team['win_home']?></td>
	<?php } ?>
	<?php if($params->get('winaway_chk') == 1) { ?>
	<td><?php echo $team['win_away']?></td>
	<?php } ?>
	<?php if($params->get('drawhome_chk') == 1) { ?>
	<td><?php echo $team['draw_home']?></td>
	<?php } ?>
	<?php if($params->get('drawaway_chk') == 1) { ?>
	<td><?php echo $team['draw_away']?></td>
	<?php } ?>
	<?php if($params->get('losthome_chk') == 1) { ?>
	<td><?php echo $team['lost_home']?></td>
	<?php } ?>
	<?php if($params->get('lostaway_chk') == 1) { ?>
	<td><?php echo $team['lost_away']?></td>
	<?php } ?>
	<?php if($params->get('pointshome_chk') == 1) { ?>
	<td><?php echo $team['points_home']?></td>
	<?php } ?>
	<?php if($params->get('pointsaway_chk') == 1) { ?>
	<td><?php echo $team['points_away']?></td>
	<?php } ?>
	<?php if($params->get('point_chk') == 1) { ?>
	<td><?php echo $team['points']?></td>
	<?php
	}?>
	<?php if($params->get('percent_chk') == 1) { ?>
	<td><?php echo ($team['winperc'] == 1)?1.000:substr(sprintf("%.3f",round($team['winperc'],3)),1);?></td>
	<?php
	}?>
	
</tr>
<?php 
$old_gid = $team['g_id'];
	}
}
} ?>
</table>
