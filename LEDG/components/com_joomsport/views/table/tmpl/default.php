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
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<?php
	if(isset($this->message)){
		$this->display('message');
	}
	$lists = $this->lists;
	$Itemid = JRequest::getInt('Itemid');
?>
<?php
/*if($this->tmpl != 'component'){
	echo $lists["panel"];
	$lnk = "window.open('".JURI::base()."index.php?tmpl=component&option=com_joomsport&amp;view=table&amp;sid=".$lists["s_id"]."','jsmywindow','width=700,height=700');";
}else{
	$lnk = "window.print();";
}*/
?>
<!--div style="float:right">
	<img onClick="<?php //echo $lnk;?>" src="components/com_joomsport/images/printButton.png" border="0" alt="Print" style="cursor:pointer;" title="Print" />
</div-->


<!-- <module middle> >
			<div class="module-middle solid">
				<div style="padding-bottom:7px;"><?php echo $lists["adm_links"];?></div>
				<!-- <back box> -->
				<!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
				<!-- </back box> -->
				
				<!-- <title box> >
				<div class="title-box">
					<div>
						<?php echo $this->lists['socbut'];?>
					</div>
					<h2>
						
						<span itemprop="name"><?php echo $this->escape($this->params->get('page_title')); ?></span>
					</h2>
					
				</div>
				<!-- </div>title box> >
				<?php
				$tLogo = '';
				if($lists["curseas"]->logo && is_file('media/bearleague/'.$lists["curseas"]->logo)){
					$tLogo = "<img itemprop='image' src='".JURI::base()."media/bearleague/".$lists["curseas"]->logo."' width='100' alt='".$this->params->get('page_title')."' style='margin-bottom:10px;' />";
				}
				if($lists['ext_fields'] || $tLogo){
				?>
				<div class="gray-box">
					<?php echo $tLogo;?>
					<table cellpadding="0" cellspacing="0" border="0" class="adf-fields-table">
						<?php echo $lists['ext_fields']?>		
					</table>
					<div class="gray-box-cr tl"></div>
					<div class="gray-box-cr tr"></div>
					<div class="gray-box-cr bl"></div>
					<div class="gray-box-cr br"></div>
				</div>
				<?php }?>
				<div class='jscontent'><span itemprop="description"><?php echo $lists["curseas"]->descr;?></span></div>
				<div style="clear:both;"></div>
				<!-- <tab box> >
				<ul class="tab-box">
					<?php 
						require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
						$etabs = new esTabs();
					  echo $etabs->newTab(JText::_('BL_TAB_TBL'),'etab_main','table',(($lists["unable_reg"] && $lists['season_par']->s_rules)?'hide':'vis'));
					  if($lists['season_par']->s_rules){
						echo $etabs->newTab(JText::_('BL_TAB_RULES'),'etab_rules','tab_flag',($lists["unable_reg"]?'vis':'hide'));
					  }
					  if($lists['season_par']->s_descr){
						echo $etabs->newTab(JText::_('BL_TAB_ABOUTSEAS'),'etab_aboutm','tab_flag');
					  }
					?>
				</ul>
				<!-- </tab box> >
				
			</div>
			<!-- </module middle> -->
		<div class="content-module">

<div id="etab_main_div" class="tabdiv" <?php echo ($lists["unable_reg"] && $lists['season_par']->s_rules)?"style='display:none;'":"";?>>
<?php
for($zzz=0;$zzz<count($lists["groups"]);$zzz++){
$show = false;
if(!$lists["gr_id"] || $lists["gr_id"] == $lists["groups"][$zzz]){
	$show = true;
}
if(!$lists["enbl_gr"]){
	$show = true;
}
if($show){
	if(isset($lists["groups_name"][$zzz])){
		echo '<h2 class="dotted">'.$lists["groups_name"][$zzz]."</h2>";
	}
?>
<table class="season-list team-list" id="s_table_<?php echo $zzz?>" cellpadding="0" cellspacing="0" border="0">

	<thead>

	<tr>

		<th width="50" class="sort asc down" axis="int">

			<span><?php echo JText::_('BL_TBL_RANK');?></span>

		</th>
		<th class="sort asc" axis="string" style="text-align:left;">

			<span><?php echo $lists["t_single"]?JText::_('BL_PARTICS'):JText::_('BL_TBL_TEAMS');?></span>

		</th>
		<?php if(@$lists["soptions"]['point_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_POINTS');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['played_chk'] == 1) { ?>
		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_PLAYED');?></span></th>
		<?php } ?>
		<?php if(@$lists["soptions"]['win_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_WINS');?></span></th>

		<?php } ?>

		<?php if(@$lists["soptions"]['draw_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_DRAW');?></span></th>

		<?php } ?>

		<?php if(@$lists["soptions"]['lost_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_LOST');?></span></th>

		<?php } ?>

		<?php

		if($lists["enbl_extra"]){

			?>

			<?php if(@$lists["soptions"]['otwin_chk'] == 1) { ?>

			<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_EXTRAWIN');?></span></th>

			<?php } ?>

			<?php if(@$lists["soptions"]['otlost_chk'] == 1) { ?>

			<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_EXTRALOST');?></span></th>

			<?php } ?>

			<?php

		}

		?>

		<?php if(@$lists["soptions"]['diff_chk'] == 1) { ?>

		<th class="sort asc" axis="string"><span><?php echo JText::_('BL_TBL_DIFF');?></span></th>

		<?php } ?>

		<?php if(@$lists["soptions"]['gd_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_GD');?></span></th>

		<?php } ?>
		<!-- 2.0.7  -->
		
		<?php if(@$lists["soptions"]['goalscore_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTGSC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['goalconc_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTGCC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['winhome_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTWHC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['winaway_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTWAC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['drawhome_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTDHC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['drawaway_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTDAC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['losthome_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTLHC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['lostaway_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTLAC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['pointshome_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTPHC');?></span></th>

		<?php

		}?>
		<?php if(@$lists["soptions"]['pointsaway_chk'] == 1) { ?>

		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_TTPAC');?></span></th>

		<?php

		}?>
		
		<?php if(@$lists["soptions"]['grwin_chk'] == 1) { ?>
		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_WINGROUP');?></span></th>
		<?php
		}
		?>
		<?php if(@$lists["soptions"]['grlost_chk'] == 1) { ?>
		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_LOSTGROUP');?></span></th>
		<?php
		}
		?>
		<?php if(@$lists["soptions"]['grwinpr_chk'] == 1) { ?>
		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_PRCGROUP');?></span></th>
		<?php
		}
		?>

		<?php if(@$lists["soptions"]['percent_chk'] == 1) { ?>
		<th class="sort asc" axis="int"><span><?php echo JText::_('BL_TBL_WINPERCENT');?></span></th>
		<?php
		}
		?>
		<?php
		if(!$lists["t_single"]){
			for($i=0;$i<count($lists["ext_fields_name"]);$i++){

			?>

			<th class="sort asc" axis="string"><span><?php echo $lists["ext_fields_name"][$i];?></span></th>	

			<?php	

			}
		}

		?>

	</tr>

	</thead>

	<tbody>

	<?php

	$ranks = 1;

	for($i=0;$i<count($lists["v_table"]);$i++){

		$team = $lists["v_table"][$i];

		

		$color = '';

		if(isset($lists["colors"][$ranks])){

			$color = 'style="background-color:'.$lists["colors"][$ranks].'"';

		}

		if($team['yteam']){

			$color = 'style="background-color:'.$team['yteam'].'"';

		}

		

		if($team['g_id'] == $lists["groups"][$zzz]){

		?>

		<tr class="<?php echo $ranks % 2?"gray":"";?>" <?php echo $color?>>

			<td><?php echo $ranks?></td>
			<?php 
				$teamembl = '';
				if(@$lists["soptions"]['emblem_chk'] == 1) 
				{

					if($team['t_emblem'] && is_file('media/bearleague/'.$team['t_emblem'])){
						$teamembl = '<img src="'.JURI::base().'media/bearleague/'.$team['t_emblem'].'" title="'.$team['name'].'" alt="'.$team['name'].'" class="embl_in_tbl" width="'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'" height="'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'" />';
					}else{
						$teamembl = '<img src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" title="'.$team['name'].'" alt="'.$team['name'].'" />';
					}					
				} 
			
			if($lists["t_single"]){
				$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$team['tid'].'&sid='.$lists["s_id"].'&Itemid='.$Itemid); 
			}else{
				$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$team['tid'].'&sid='.$lists["s_id"].'&Itemid='.$Itemid); 
			}
			?>
			
			<td class="teams" nowrap><?php echo $teamembl;?><p><a href="<?php echo $link;?>"><?php echo $team['name']?></a></p></td>
						<?php if(@$lists["soptions"]['point_chk'] == 1) { ?>

			<td><?php echo $team['points']?></td>

			<?php } ?>
			<?php if(@$lists["soptions"]['played_chk'] == 1) { ?>
			<td><?php echo $team['played']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['win_chk'] == 1) { ?>

			<td><?php echo $team['win']?></td>

			<?php } ?>

			<?php if(@$lists["soptions"]['draw_chk'] == 1) { ?>

			<td><?php echo $team['draw']?></td>

			<?php } ?>

			<?php if(@$lists["soptions"]['lost_chk'] == 1) { ?>

			<td><?php echo $team['lost']?></td>

			<?php } ?>

			<?php

			if($lists["enbl_extra"]){

				?>

				<?php if(@$lists["soptions"]['otwin_chk'] == 1) { ?>

				<td><?php echo $team['extra_win']?></td>

				<?php } ?>

				<?php if(@$lists["soptions"]['otlost_chk'] == 1) { ?>

				<td><?php echo $team['extra_lost']?></td>

				<?php } ?>

				<?php

			}

			?>

			<?php if(@$lists["soptions"]['diff_chk'] == 1) { ?>

			<td nowrap="nowrap"><?php echo $team['goals']?></td>

			<?php } ?>

			<?php if(@$lists["soptions"]['gd_chk'] == 1) { ?>

			<td><?php echo $team['gd']?></td>

			<?php } ?>
			<?php if(@$lists["soptions"]['goalscore_chk'] == 1) { ?>
			<td><?php echo $team['goals_score']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['goalconc_chk'] == 1) { ?>
			<td><?php echo $team['goals_conc']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['winhome_chk'] == 1) { ?>
			<td><?php echo $team['win_home']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['winaway_chk'] == 1) { ?>
			<td><?php echo $team['win_away']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['drawhome_chk'] == 1) { ?>
			<td><?php echo $team['draw_home']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['drawaway_chk'] == 1) { ?>
			<td><?php echo $team['draw_away']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['losthome_chk'] == 1) { ?>
			<td><?php echo $team['lost_home']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['lostaway_chk'] == 1) { ?>
			<td><?php echo $team['lost_away']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['pointshome_chk'] == 1) { ?>
			<td><?php echo $team['points_home']?></td>
			<?php } ?>
			<?php if(@$lists["soptions"]['pointsaway_chk'] == 1) { ?>
			<td><?php echo $team['points_away']?></td>
			<?php } ?>
			
			<?php if(@$lists["soptions"]['grwin_chk'] == 1) { ?>
			<td><?php echo $team['win_gr']?></td>
			<?php
			}?>
			<?php if(@$lists["soptions"]['grlost_chk'] == 1) { ?>
			<td><?php echo $team['loose_gr']?></td>
			<?php
			} ?>
			<?php if(@$lists["soptions"]['grwinpr_chk'] == 1) { ?>
			<td><?php echo (@$team['winperc_gr'] == 1)?1.000:substr(sprintf("%.3f",round(@$team['winperc_gr'],3)),1);?></td>
			<?php
			}?>

			<?php if(@$lists["soptions"]['percent_chk'] == 1) { ?>
			<td><?php echo ($team['winperc'] == 1)?1.000:substr(sprintf("%.3f",round($team['winperc'],3)),1);?></td>
			<?php
			}?>
			
			<?php
			if(!$lists["t_single"]){
				if(isset($team['ext_fields'])){
					for($j=0;$j<count($team['ext_fields']);$j++){

					?>

					<td><?php echo isset($team['ext_fields'][$j])?$team['ext_fields'][$j]:'&nbsp;';?></td>	

					<?php	

					}
				}
			}
			?>

			

		</tr>

		<?php

		$ranks++;

		}

		

	}

	?>

	</tbody>

</table>
<?php if($this->tmpl != 'component'){?>
<script type="text/javascript">

new Grid($('s_table_<?php echo $zzz?>')); 

</script>
<?php } ?>
<?php
}

if($lists["enbl_gr"]){
	echo isset($lists["bonus_not"][$lists["groups"][$zzz]])?('<div class="js_botbonusp">'.JText::_('BLFA_SEASBONUSTABLE').'</div>'.$lists["bonus_not"][$lists["groups"][$zzz]]):'';
}else{
	if(count($lists["bonus_not"])){
		echo '<div class="js_botbonusp">'.JText::_('BLFA_SEASBONUSTABLE').'</div>';
		foreach($lists["bonus_not"] as $bons){
			echo $bons;
		}
	}
}
}
//echo $this->bonus_not[0];
//var_dump($this->bonus_not);
?>

<?php
if(count($lists["playoffs"])){

$prev_mday = 0;

for ($i=0;$i<count($lists["playoffs"]);$i++){

	$playoff_match = $lists["playoffs"][$i];

	if($playoff_match->m_id != $prev_mday){

		
		if($i){
			echo "</table>";
		}
		echo '<h2 class="solid">'.$playoff_match->m_name.'</h2>';
		echo '<table class="match-day" cellpadding="0" cellspacing="0" border="0">';
		$prev_mday = $playoff_match->m_id;

	}

	?>

	<tr class="dotted">

			<td width="25"><!-- --></td>
			
			<td class="team-h"><span><?php echo $playoff_match->home?></span></td>
			<td class="team-ico-h"><!-- -->
			<?php
				if(!$this->lists['t_single']){
					if($playoff_match->emb1 && is_file('media/bearleague/'.$playoff_match->emb1)){
						echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$playoff_match->emb1.'" width="29" height="29" alt="'.$playoff_match->home.'" />';
					}else{
						echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
					}
				}
			?>
			</td>
			<td class="score"><span class="score">

				<a class="plyoff-matches" href="<?php echo JRoute::_('index.php?option=com_joomsport&view=match&id='.$playoff_match->mid.'&Itemid='.$Itemid)?>">
					<b class="score-h">
						<?php echo $playoff_match->score1?>
					</b>
					<b>:</b>
					<b class="score-a">
						<?php echo $playoff_match->score2; ?>
					</b>
					<?php
					if(@$lists["enbl_extra"] && $playoff_match->is_extra)
					{ 
						$class_ext = ($playoff_match->score1 > $playoff_match->score2)?"extra-time-h":"extra-time-g";
						echo '<span class="'.$class_ext.'" title="'.JText::_('BLFA_TEAM_WON_ET').'">'.JText::_('BL_RES_EXTRA').'</span>';
						
					}
					?>
				</a>

			</td>
			<td class="team-ico-a"><!-- -->
					<?php
						if(!$this->lists['t_single']){
							if($playoff_match->emb2 && is_file('media/bearleague/'.$playoff_match->emb2)){
								echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$playoff_match->emb2.'" width="29" height="29" alt="'.$playoff_match->away.'" />';
							}else{
								echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
							}
						}
					?>
					</td>
			<td class="team_a"><span><?php echo $playoff_match->away?></span></td>

			

	</tr>
<?php } ?>
</table>


	<?php

}
?>
</div>
<?php if($lists["season_par"]->s_rules):?>
<div id="etab_rules_div" class="tabdiv" <?php echo $lists["unable_reg"]?"style='display:block;'":"style='display:none;'";?>>
	<?php echo $lists["season_par"]->s_rules;?>
</div>
<?php endif;?>
<?php if($lists["curseas"]->s_descr):?>
<div id="etab_aboutm_div" class="tabdiv" style='display:none;'>
	<?php echo $lists["curseas"]->s_descr;?>
</div>
<?php endif;?>
<br />

</div>

