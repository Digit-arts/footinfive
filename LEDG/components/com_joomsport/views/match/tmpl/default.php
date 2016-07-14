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

	if(isset($this->message)){
		$this->display('message');
	}
	$Itemid = JRequest::getInt('Itemid');
?>
<script type="text/javascript">

function delCom(num){
	if('<?php echo $this->jver?>' >= '1.6'){
		var myRequest = new Request({url:'index.php?tmpl=component&option=com_joomsport&task=del_comment&no_html=1&cid='+num, method: 'post',  onComplete: function(result) { if(result){alert(result);}else{var d = document.getElementById('divcomb_'+num).parentNode; d.removeChild($('divcomb_'+num));}} }).send();
	}else{
		var myRequest = new Ajax('index2.php?option=com_joomsport&amp;task=del_comment&amp;no_html=1&amp;cid='+num, { method: 'post', onComplete: function(result) { if(result){alert(result);}else{var d = document.getElementById('divcomb_'+num).parentNode; d.removeChild($('divcomb_'+num));}} }).request();
	}
}
function resetPoints(el){
    if ($(el).get('checked') == true){
        $(el).getAllNext('input').set('disabled', false);
    } else {
        $(el).getAllNext('input').set('disabled', true);
    }
}
</script>

<?php
require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();


//echo $lists["panel"];
$match = $this->lists["match"];
?>
<!-- <module middle> -->
			<div class="module-middle solid">
				
				<!-- <back box> >
				<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div>
				<!-- </back box> -->
				
				<!-- <title box> -->
				<div class="title-box">
					<div>
						<?php echo $this->lists['socbut'];?>
					</div>
					
					<h2 class="result-box-date">
						<span itemprop="name">
						<?php
						if($match->m_date){
							$info_date=date_bl($match->m_date,$match->m_time);
							echo $info_date;
						}
						?>
						</span>
					</h2>
					<?php
					if($match->m_location || $match->venue_id){
						echo '<h3 class="result-box-stadium">';
						echo getJS_Location($match->id);
						echo '</h3>';
					}	
					?>
					
				</div>
				<!-- </div>title box> -->
				
				<!-- <tab box> -->
				<ul class="tab-box">
					<?php 
					
					 require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
					 $etabs = new esTabs();
					  echo $etabs->newTab(JText::_('BL_TAB_MATCH'),'etab_match','star','vis');
					  $how_rowst_k = (count($this->lists['squard1']) > count($this->lists['squard2']))?count($this->lists['squard1']):count($this->lists['squard2']);
					  if($how_rowst_k){
						echo $etabs->newTab(JText::_('BL_TAB_SQUAD'),'etab_squad','players');
					  }
					  if($match->match_descr){
						echo $etabs->newTab(JText::_('BL_TAB_ABOUT'),'etab_descr');
					  }
					  if(count($this->lists["photos"])){
						echo $etabs->newTab(JText::_('BL_TAB_PHOTOS'),'etab_photos','photo');
					  }
					  
					?>
				</ul>
				<!-- </tab box> -->
				
			</div>
			<!-- </module middle> -->

<!-- <content module> -->
<div class="content-module padd-off">
	<form name="adminForm" id="adminForm" action="" method="post">
	<div id="etab_match_div" class="tabdiv">
			
				
				<!-- <Result box> -->
				<div class="result-box">
					<table class="match-day" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td class="team-h-l"><span><?php echo $match->home?></span></td>
							<td class="team-ico-h-l">
								<?php
								if(!$this->lists['t_single']){
									/*if($match->emb1 && is_file('media/bearleague/'.$match->emb1)){
										echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$match->emb1.'" width="29" height="29" alt="'.$match->home.'" />';
									}else{
										echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
									}*/
									if (est_agent($user)){
										echo "<a href=\"".JURI::base()
											."index.php/ep/inserer-resultats?id_equipe_suppr_score="
											.$match->hm_id."&id_match_suppr_score="
											.$match->id."\"><img class=\"team-embl\" src=\""
											.JURI::base()."images/stories/effacer_resultats_equipe.png\" "
											."title=\"effacer les resultats de cette equipe\" /></a>";
										if ($match->m_played){
											echo " <a href=\"".JURI::base()
											."index.php/ep/inserer-resultats?id_equipe_bonus_retard="
											.$match->hm_id."&id_match_bonus_retard="
											.$match->id."&score=".$match->score1."\"><img class=\"team-embl\" src=\""
											.JURI::base()."images/stories/retard-icon.png\" "
											."title=\"Retard de l'equipe adverse : Accorder 5 buts d'avance &agrave; cette equipe\" /></a>";
											
											if ($match->bonus1==0)
												$icon="icon-fairplay.png";
											else $icon="icon-no-fairplay.png";
											
											echo " ";
											
										}
									}
							
								}
								
								?>
							</td>
							<td class="score">
								<span class="score">
									<b class="score-h">
									<?php echo ($match->m_played?$match->score1:'-')?>
									</b>
									<b>:</b>
									<b class="score-a">
									<?php echo ($match->m_played?$match->score2:'-');?>
									</b>
									<?php
									$etclass="extra-time-g";
									if($match->score1 > $match->score2){
										$etclass="extra-time-h";
									}
									?>
									<?php if($this->lists["enbl_extra"] && $match->is_extra){ echo "<div class='".$etclass."' title='".JText::_('BLFA_TEAM_WON_ET')."'>".JText::_('BL_RES_EXTRA')."</div>";}?>
								</span>
								<div style="text-align:center;">
									<?php
										echo (($match->bonus1!= '0.00' || $match->bonus2 != '0.00')?"<font style='font-size:75%;'>".floatval($match->bonus1).":</font>":"");
										echo (($match->bonus1!= '0.00' || $match->bonus2 != '0.00')?"<font style='font-size:75%;'>".floatval($match->bonus2)."</font>":"");
									?>
								</div>
								<?php
								if($lists["s_enbl_extra"] && ($match->aet1 != $match->aet2 || $match->p_winner) && $match->m_played){
								?>
								<span class="score" style="margin-top:15px;">
									<b class="score-h">
									<?php echo $match->aet1;?>
									</b>
									<b>:</b>
									<b class="score-a">
									<?php echo $match->aet2;?>
									</b>
									<?php
									$etclass="extra-time-g";
									if($match->p_winner == $match->aw_id){
										$etclass="extra-time-h";
									}
									echo "<div class='extra-time-aet' title='".JText::_('AET')."'>".JText::_('AET')."</div>";
									echo "<div class='".$etclass."' title='".JText::_('W')."'>".JText::_('W')."</div>";?>
								</span>
								<?php } ?>
							</td>
							<td class="team-ico-a">
								<?php
								if(!$this->lists['t_single']){
									/*if($match->emb2 && is_file('media/bearleague/'.$match->emb2)){
										echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$match->emb2.'" alt="'.$match->away.'" />';
									}else{
										echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
									}*/
									if (est_agent($user)){
										if ($match->m_played){
											echo "<a href=\"".JURI::base()
											."index.php/ep/inserer-resultats?id_equipe_bonus_retard="
											.$match->aw_id."&id_match_bonus_retard="
											.$match->id."&score=".$match->score2."\"><img class=\"team-embl\" src=\""
											.JURI::base()."images/stories/retard-icon.png\" "
											."title=\"Retard de l'equipe adverse : Accorder 5 buts d'avance &agrave; cette equipe\" /></a>";
											
										}
										echo " <a href=\"".JURI::base()
											."index.php/ep/inserer-resultats?id_equipe_suppr_score="
											.$match->aw_id."&id_match_suppr_score="
											.$match->id."\"><img class=\"team-embl\" src=\""
											.JURI::base()."images/stories/effacer_resultats_equipe.png\" "
											."title=\"effacer les resultats de cette equipe\" /></a>";
										
										if ($match->bonus2==0)
												$icon="icon-fairplay.png";
										else $icon="icon-no-fairplay.png";
											
										echo " ";
									}
							
								}
								
								?>
							</td>
							<td class="team-a"><span><?php echo $match->away;?></span></td>
							
						</tr>
						<tr>
							<td colspan="4">
								<?php
									if (isset($match->betavailable) && isset($match->betfinish) && $match->betavailable && $match->betfinish && !$match->m_played){
									?>
									<table class="bettable" width="100%">
										<tr>
											<th align="right"><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
											<th></th>
											<th><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
										</tr>
										<?php foreach($this->lists["betevents"] as $event):?>
										<?php if ($event->coeff1 || $event->coeff2):?>
										<tr>
											<td align="right">
											<?php 
											if ($event->coeff1){
												?>
												<input type="radio" name="betevents_radio[<?php echo $match->id?>][<?php echo $event->id?>]" onChange="resetPoints(this)"/>
												<?php echo $event->coeff1?>
												<input type="text" disabled="true" size="3" name="betevents_points1[<?php echo $match->id?>][<?php echo $event->id?>]"/>
												<?php
											}
											?>
											</td>
											<td align="center">
												<?php if ($event->type=='simple' || $event->type=='default'):?>
													<?php echo $event->name?>
												<?php else:?>
													<?php echo $event->difffrom?$event->difffrom.' < ':'' ?>
													DIFF
													<?php echo $event->diffto?' < '.$event->diffto:'' ?>
												<?php endif;?>
											</td>
											<td>
											<?php 
											if ($event->coeff2){
												?>
												<input type="radio" name="betevents_radio[<?php echo $match->id?>][<?php echo $event->id?>]" onChange="resetPoints(this)"/>
												<?php echo $event->coeff2?>
												<input type="text" disabled="true" name="betevents_points2[<?php echo $match->id?>][<?php echo $event->id?>]"/>
												<?php
											}
											?>                                    
											</td>
										</tr>
										<?php endif;?>
										<?php endforeach;?>
										<tr>
											<td colspan="3" align="center">
												<input type="hidden" name="bet_match[]" value="<?php echo $match->id?>"/>
												<input type="button" value="<?php echo JText::_("BLFA_BET_SUBMIT_BET");?>" onClick="document.adminForm.task.value = 'bet_match_save';document.adminForm.submit();"/>
											</td>
										</tr>
									</table>
									<?php
									}
									?>
							</td>
						</tr>
						
						<!-- MAPS -->
						<?php
						if(count($this->lists['maps'])){
							for($i=0;$i<count($this->lists['maps']);$i++){
								if(isset($this->lists['maps'][$i]->m_score1) && isset($this->lists['maps'][$i]->m_score2)){
									$mpz = "<b>".$this->lists['maps'][$i]->m_name."</b>";
									if($this->lists['maps'][$i]->map_img && is_file('media/bearleague/'.$this->lists['maps'][$i]->map_img)){
										$mpz = '<a rel="lightbox-mapsport" title="'.htmlspecialchars($this->lists['maps'][$i]->map_descr).'" href="'.getImgPop($this->lists['maps'][$i]->map_img).'" class="team-images"><b>'.$this->lists['maps'][$i]->m_name.'</b></a>';
									}
									echo "<tr>";
									echo "<td align='right'>".$mpz."</td>";
									?>
										<td class="team-ico-h-l"><!-- --></td>
										<td class="score">
											<span class="score-small" style="cursor:default;">
												<b class="score-h">
												<?php echo (isset($this->lists['maps'][$i]->m_score1)?$this->lists['maps'][$i]->m_score1."":"")?>
												</b>
												<b>:</b>
												<b class="score-a">
												<?php echo (isset($this->lists['maps'][$i]->m_score2)?$this->lists['maps'][$i]->m_score2:"");?>
												</b>
											</span>
											
										</td>
										
									</tr>
								<?php
								}
							}
						}
						?>
						
					</table>
					
				</div>
				<!-- </Result box> -->
				
				
				
						
					<table border="0" cellpadding="5" cellspacing="0" width="100%" class="season-list">	
					<?php
					$prev_id = 0;
					$ev_count = (count($this->lists["m_events_home"]) > count($this->lists["m_events_away"])) ? (count($this->lists["m_events_home"])) : (count($this->lists["m_events_away"]));
					for($i=0;$i<$ev_count;$i++){
					?>
					<tr class="<?php echo $i%2?"gray":"yellow";?>">
						<?php
						if(isset($this->lists["m_events_home"][$i])){
							echo '<td class="home_event" width="40%">';
							if($this->lists["m_events_home"][$i]->e_img && is_file('media/bearleague/events/'.$this->lists["m_events_home"][$i]->e_img)){
								echo '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$this->lists["m_events_home"][$i]->e_img.'" title="'.$this->lists["m_events_home"][$i]->e_name.'" alt="'.$this->lists["m_events_home"][$i]->e_name.'" />';
							}else{ 
								echo "<span class='js_event_name'>".$this->lists["m_events_home"][$i]->e_name."</span>";
							}
							if(!$this->lists['t_single']){
								echo "&nbsp;&nbsp;".$this->lists["m_events_home"][$i]->p_name;
							}
							echo '</td>';
							?>
							<td class="home_event_count" width="5%">
							<?php
							if($this->lists["m_events_home"][$i]->ecount){
								echo $this->lists["m_events_home"][$i]->ecount;
							}else echo "0";
							?>
							</td>
							<td class="home_event_minute" width="3%" style="padding-right:35px;">
							<?php
							if($this->lists["m_events_home"][$i]->minutes){
								echo $this->lists["m_events_home"][$i]->minutes."'";
							}else echo "&nbsp;";
							?>
							</td>
							<?php
						}else{
							echo '<td style="padding:0px" colspan="3">&nbsp;</td>';
						}
						if(isset($this->lists["m_events_away"][$i])){
							echo '<td class="away_event" width="40%" style="padding-left:35px;">';
							if(isset($this->lists["m_events_away"][$i]->e_img) && $this->lists["m_events_away"][$i]->e_img && is_file('media/bearleague/events/'.$this->lists["m_events_away"][$i]->e_img)){
								echo '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$this->lists["m_events_away"][$i]->e_img.'" title="'.$this->lists["m_events_away"][$i]->e_name.'" alt="'.$this->lists["m_events_away"][$i]->e_name.'" />';
							}else{ 
								echo "<span class='js_event_name'>".$this->lists["m_events_away"][$i]->e_name."</span>";
							}
							if(!$this->lists['t_single']){
								echo "&nbsp;&nbsp;".$this->lists["m_events_away"][$i]->p_name;
							}	
							echo '</td>';
							?>
							<td class="away_event_count" width="5%">
							<?php
							if($this->lists["m_events_away"][$i]->ecount){
								echo $this->lists["m_events_away"][$i]->ecount;
							}else echo "0";
							?>
							</td>
							<td class="away_event_minute" width="3%">
							<?php
							if($this->lists["m_events_away"][$i]->minutes){
								echo $this->lists["m_events_away"][$i]->minutes."'";
							}else echo "&nbsp;";
							?>
							</td>
							
							<?php
							
						}else{
							echo '<td style="padding:0px" colspan="3">&nbsp;</td>';
						}
						?>
					</tr>
					<?php
					}
					$how_rows = (count($this->lists["h_events"]) > count($this->lists["a_events"]))?count($this->lists["h_events"]):count($this->lists["a_events"]);
					for($p=0;$p<$how_rows;$p++){
						if($p==0){
							echo '</table><table class="season-list" style="margin-top:40px;" border="0" cellpadding="5" cellspacing="0" width="100%"><tr><th colspan="4" class="teams_stats"><h3>'.JText::_('BL_TBL_STAT')."</h3></th></tr>";
						}
						echo "<tr class='".($p%2?"gray":"yellow")."'>";
						echo "<td width='40%'>";
						if(isset($this->lists["h_events"][$p])){
							if($this->lists["h_events"][$p]->e_img && is_file('media/bearleague/events/'.$this->lists["h_events"][$p]->e_img)){
								echo '<div style="float:left"><img height="20" src="'.JURI::base().'media/bearleague/events/'.$this->lists["h_events"][$p]->e_img.'" title="'.$this->lists["h_events"][$p]->e_name.'" alt="'.$this->lists["h_events"][$p]->e_name.'" /></div>';
							}else{ 
							}
						echo '<div style="float:left;padding:5px;">'.$this->lists["h_events"][$p]->e_name."</div>";	
						}else echo "&nbsp;";
						echo "</td>";
						echo "<td class='home_stats_minute' width='100'>";	
						if(isset($this->lists["h_events"][$p])){
								echo $this->lists["h_events"][$p]->ecount;
						}else echo "&nbsp;";
						echo "</td>";
						
						echo "<td width='40%' style='padding-left:30px;'>";
						if(isset($this->lists["a_events"][$p])){
							if($this->lists["a_events"][$p]->e_img && is_file('media/bearleague/events/'.$this->lists["a_events"][$p]->e_img)){
								echo '<div style="float:left"><img height="20" src="'.JURI::base().'media/bearleague/events/'.$this->lists["a_events"][$p]->e_img.'" title="'.$this->lists["a_events"][$p]->e_name.'" alt="'.$this->lists["a_events"][$p]->e_name.'" /></div>';
							}else{ 
							}
						echo '<div style="float:left;padding:5px;">'.$this->lists["a_events"][$p]->e_name."</div>";	
						}else echo "&nbsp;";
						echo "</td>";
						echo "<td class='away_stats_minute' width='50'>";	
						if(isset($this->lists["a_events"][$p])){
								echo $this->lists["a_events"][$p]->ecount;
						}else echo "&nbsp;";
						echo "</td>";
						echo "</tr>";
					}
					?>
				</table>
				<table border="0" cellpadding="5" cellspacing="0" class="adf-fields-table first-bold">
				<?php
				
					echo $this->lists["ext_fields"];
					?>
				</table>
				
				
			
			
</div>
<input type="hidden" name="m_id" value="<?php echo $match->id?>"/>
<input type="hidden" name="task" value="" />
</form>
<?php

if($match->match_descr){
echo '<div id="etab_descr_div" class="tabdiv" style="display:none;">';
?>
<div>
	<span itemprop="description">
	<?php 
		echo $match->match_descr;
	?>
	</span>
</div>
<?php
echo '</div>';
}


if($how_rowst_k){
echo '<div id="etab_squad_div" class="tabdiv" style="display:none;">';
?>
<!-- <content module> -->
			<div class="content-module">
			<table class="season-list">
				<tr>
					<td width="50%" style="text-align:center;font-size:18px;">
						<?php echo $match->home;?>
					</td>
					<td width="50%" style="text-align:center;font-size:18px;">
						<?php echo $match->away;?>
					</td>
				</tr>
			</table>
				<h3 class="solid"><?php echo JText::_('BLFA_LINEUP');?></h3>
<?php 
$how_rows = (count($lists['squard1']) > count($lists['squard2']))?count($lists['squard1']):count($lists['squard2']);
if($how_rows){
	echo '<table class="season-list" cellpadding="0" cellspacing="0" border="0">';
	for($p=0;$p<$how_rows;$p++){
	echo "<tr class='".($p % 2?"":"gray")."'>";
	echo "<td width='50%'>".(isset($this->lists['squard1'][$p]->name)?$this->lists['squard1'][$p]->photo."<p class='player-name'>".$this->lists['squard1'][$p]->name."</p>":"&nbsp;")."</td>";
	echo "<td width='50%'>".(isset($this->lists['squard2'][$p]->name)?$this->lists['squard2'][$p]->photo."<p class='player-name'>".$this->lists['squard2'][$p]->name."</p>":"&nbsp;")."</td>";
	echo "</tr>";
	}
	echo '</table>';
}	
?>
<?php 

$how_rows = (count($this->lists['squard1_res']) > count($this->lists['squard2_res']))?count($this->lists['squard1_res']):count($this->lists['squard2_res']);
if($how_rows){
	echo "<h3 class='solid'>".JText::_('BLFA_SUBSTITUTES')."</h3>";
	echo '<table class="season-list" cellpadding="0" cellspacing="0" border="0">';
	for($p=0;$p<$how_rows;$p++){
		echo "<tr class='".($p % 2?"":"gray")."'>";
		echo "<td width='50%'>".(( isset($this->lists['squard1_res'][$p]->name) && $this->lists['squard1_res'][$p]->name)?$this->lists['squard1_res'][$p]->photo."<p class='player-name'>".$this->lists['squard1_res'][$p]->name."</p>":"&nbsp;")."</td>";
		echo "<td width='50%'>".((isset($this->lists['squard2_res'][$p]->name) && $this->lists['squard2_res'][$p]->name)?$this->lists['squard2_res'][$p]->photo."<p class='player-name'>".$this->lists['squard2_res'][$p]->name."</p>":"&nbsp;")."</td>";
		echo "</tr>";
	}
	echo '</table>';
}	

//subs in
$how_rows = (count($this->lists['subsin1']) > count($this->lists['subsin2']))?count($this->lists['subsin1']):count($this->lists['subsin2']);
$arrow_in = '<img src="'.JUri::Base().'components/com_joomsport/img/ico/old-edit-redo.png" class="sub-player-ico" title="" alt="" />';
$arrow_out = '<img src="'.JUri::Base().'components/com_joomsport/img/ico/old-edit-undo.png" class="sub-player-ico" title="" alt="" />';
if($how_rows){
	echo "<h3 class='solid'>".JText::_('BLFA_SUBSIN')."</h3>";
	echo '<table class="season-list" cellpadding="0" cellspacing="0" border="0">';
	for($p=0;$p<$how_rows;$p++){
		echo "<tr class='".($p % 2?"":"gray")."'>";
		echo "<td width='50%' align='right' style='padding-right:20px;'>";
			echo "<table width='100%' cellpadding='2' border='0' class='season-list'>";
				echo '<tr>';
					echo '<td>';
						echo (isset($this->lists['subsin1'][$p]->plin) && $this->lists['subsin1'][$p]->plin)?$arrow_in."<p class='sub-player-name'>".$this->lists['subsin1'][$p]->plin."</p><br />":"&nbsp;";
						
						echo (isset($this->lists['subsin1'][$p]->plout) && $this->lists['subsin1'][$p]->plout)?$arrow_out."<p class='sub-player-name'>".$this->lists['subsin1'][$p]->plout."</p>":"&nbsp;";
					echo '</td>';
					echo '<td width="50" valign="middle">';
						echo (isset($this->lists['subsin1'][$p]->minutes) && $this->lists['subsin1'][$p]->minutes)?$this->lists['subsin1'][$p]->minutes."'":"&nbsp;";
					echo '</td>';
				echo '</tr>';
			echo '</table>';
		echo "</td>";
		echo "<td>";
			echo "<table width='100%' cellpadding='2' border='0' class='season-list'>";
				echo '<tr>';
					echo '<td>';
						echo (isset($this->lists['subsin2'][$p]->plin) && $this->lists['subsin2'][$p]->plin)?$arrow_in."<p class='sub-player-name'>".$this->lists['subsin2'][$p]->plin."</p><br />":"&nbsp;";
					
						echo (isset($this->lists['subsin2'][$p]->plout) && $this->lists['subsin2'][$p]->plout)?$arrow_out."<p class='sub-player-name'>".$this->lists['subsin2'][$p]->plout."</p>":"&nbsp;";
					echo '</td>';
					echo '<td width="50" valign="middle">';
						echo (isset($this->lists['subsin2'][$p]->minutes) && $this->lists['subsin2'][$p]->minutes)?$this->lists['subsin2'][$p]->minutes."'":"&nbsp;";
					echo '</td>';
				echo '</tr>';
			echo '</table>';
		echo "</td>";
		echo "</tr>";
	}
	echo '</table>';
}	
echo "</div>";
echo "</div>";
}

if(count($this->lists["photos"])){
echo '<div id="etab_photos_div" class="tabdiv" style="display:none;">';
echo "<table class='jsnoborders'><tr><td>";
	for($i=0;$i<count($this->lists["photos"]);$i++){
		$photo = $this->lists["photos"][$i];
	?>
		<div style="float:left; padding:10px; height:120px;">
			<a rel="lightbox-imgsport" title="<?php echo htmlspecialchars($photo->name)?>" href="<?php echo getImgPop($photo->filename)?>" class="team-images"><img src="<?php echo JURI::base();?>media/bearleague/<?php echo $photo->filename?>"  height="100" class="allimages" title="<?php echo htmlspecialchars($photo->name)?>" alt="<?php echo htmlspecialchars($photo->name)?>" /></a>
		</div>
	<?php
	}
echo "</td></tr></table>";	
echo '</div>';
}
?>
				<?php
				if($this->lists['mcomments']){
				?>
				<!-- <Comments box> -->
				<div class="dv_comments"><?php echo JText::_("BLFA_COMMENTS");?></div>
				<ul class="comments-box" id="all_comments">
				
				
					
				<?php

				for($i=0;$i<count($this->lists["comments"]);$i++){	
					?>
					<li id="divcomb_<?php echo $this->lists["comments"][$i]->id?>">
						<img src="<?php echo JURI::base();?>components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="" />
						<div class="comments-box-inner">
							<span class="date" nowrap="nowrap">
								<?php
								if(($this->lists["comments_adm"]) || ($this->lists["usera"]->id == $this->lists["comments"][$i]->usrid)){
									echo "<img src='".JURI::base()."components/com_joomsport/img/ico/close.png' width='15' border=0 style='cursor:pointer;' onClick='javascript:delCom(".$this->lists["comments"][$i]->id.");' />";
								}
								?>
								<?php 
									jimport('joomla.utilities.date');
										if(getVer() > '1.6'){
											$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
											$jdate = new JDate($this->lists["comments"][$i]->date_time);
										
											$jdate->setTimezone($tz);
										}else{
											
											$jdate = new JDate($this->lists["comments"][$i]->date_time,JFactory::getApplication()->getCfg('offset'));
										
										
										}
									//$jdate = new JDate($this->lists["comments"][$i]->date_time,JFactory::getApplication()->getCfg('offset'));
									
									//$jdate->setTimezone($tz);
									
									echo $jdate->toMySQL(true);
									//echo $this->lists["comments"][$i]->date_time;
								?>
								
							</span>
							<h4 class="nickname"><?php echo $this->lists["comments"][$i]->nick?></h4>
							<p><?php echo str_replace("\n",'<br />',htmlspecialchars($this->lists["comments"][$i]->comment));?></p>
						</div>
					</li>
				<?php	
				}
				?>
				</ul>
				<?php 
				if($this->jver >= '1.6'){
					$link = JUri::base().'index.php?option=com_joomsport&task=add_comment&no_html=1&tmpl=component';
				}else{
					$link = 'index2.php?option=com_joomsport&task=add_comment&no_html=1';
				}
				?>
				<form action="<?php echo $link;?>" method="POST" id="comForm" name="comForm">
				<!-- <Post comment> -->
				<div class="post-comment">
					<textarea name="addcomm" id="addcomm"></textarea>
					<button class="send-button" id="submcom"><span><b><?php echo JText::_("BLFA_POSTCOMMENT");?></b></span></button>
					<input type="hidden" name="mid" value="<?php echo $match->id;?>" />
				</div>
				</form>
				<!-- </Post comment> -->
				

				<?php if($this->jver  >= '1.6') {?>
				<script type="text/javascript">
				//<![CDATA[ 
				window.addEvent('domready', function(){
				$('comForm').addEvent('submit', function(e) {
					
					new Event(e).stop();
						if($('addcomm').value){
							var submcom = $('submcom');
							//submcom.disabled = true;
							
								 var req = new Request.HTML({
									 url: '<?php echo $link;?>',
									 data: $('comForm'),
								onSuccess: function(qw,ew,result) {
									if(result){
										
										var allc = $('all_comments');
										allc.innerHTML = allc.innerHTML + result;
										
										submcom.disabled = false;
										$('addcomm').value='';
										
									}else{
										alert('Not registered');
									}
									$('comForm').reset();
									
								}
								
								
							}).send();
							
							
							
						}
						

					});
				});
				//]]> 
				</script>
				<?php }else{ ?>
				<script type="text/javascript">
				//<![CDATA[ 
				$('comForm').addEvent('submit', function(e) {
					
					new Event(e).stop();
						if($('addcomm').value){
							var submcom = $('submcom');
							//submcom.disabled = true;
							
							this.send({
								onComplete: function(result) {
									if(result){
										var ndiv = document.createElement('div');
										ndiv.addClass('com_block');
										var allc = $('all_comments');
										allc.appendChild(ndiv);
										ndiv.innerHTML = result;
										submcom.disabled = false;
										$('addcomm').value='';
									}else{
										alert('Not registered');
									}
								}
							});
						}
						

					
				});

				//]]> 
				</script>
				<?php } ?>
				<?php }?>
				
				<!-- </Comments box> -->
				
</div>
<!-- </content module> -->

<table class="zebra">	
		<tr>
			<td colspan="2" align="left" valign="center">
			<?php
					switch ($match->m_location){
							case "T1": $lien_terrain="0100217.mp4"; break;
							case "T2": $lien_terrain="0200217.mp4"; break;
							case "T3": $lien_terrain="0300217.mp4"; break;
							case "T4": $lien_terrain="0400217.mp4"; break;
					}			
					$site="http://www.mysportconnect.net/download/video/club00008/place0";
					//$site="http://les-etoiles-de-galilee.com/Videos-2012-2013/";
					$date_video=substr($info_date,6,4)."_".substr($info_date,3,2)."_".substr($info_date,0,2)."_".substr($info_date,11,2)."-";
					$date_video2="_id=".substr($info_date,8,2).substr($info_date,3,2).substr($info_date,0,2).substr($info_date,11,2);
					$terrain_FIF=substr($match->m_location, -1,1)."/video_";

					$video1=$site.$terrain_FIF.$date_video."00_".substr($info_date,11,2)."-15".$date_video2."00".$lien_terrain;
					$video2=$site.$terrain_FIF.$date_video."15_".substr($info_date,11,2)."-30".$date_video2."15".$lien_terrain;
					$video3=$site.$terrain_FIF.$date_video."30_".substr($info_date,11,2)."-45".$date_video2."30".$lien_terrain;
					$video4=$site.$terrain_FIF.$date_video."45_".(substr($info_date,11,2)+1)."-00".$date_video2."45".$lien_terrain;

					$video5=$site.$terrain_FIF;
					$video5.=substr($info_date,6,4)."_".substr($info_date,3,2)."_".substr($info_date,0,2)."_".(substr($info_date,11,2)+1)."-";
					$video5.="00_".(substr($info_date,11,2)+1)."-15";
					$video5.="_id=".substr($info_date,8,2).substr($info_date,3,2).substr($info_date,0,2).(substr($info_date,11,2)+1);
					$video5.="00".$lien_terrain;
					
					$taille_video=" width=\"240\" height=\"171\"";
					
				echo "<center><a href=\"http://www.mysportconnect.net/clubs/footinfive\" target=\"_blank\"><br>Demander les vid&eacute;os</a><br></center>";
				echo "Afin de pouvoir visualiser les vid&eacute;os de vos matchs ci-dessous, il suffit qu'un joueur les demande sur le site ";
				echo "de MySportConnect pour qu'elles apparaissent automaiquement dans les fenetres ci-dessous.<hr>";


					?>
				</td>
				</tr>
				<tr>
								<td align="left">
									<?php if (remote_file_exists($video1)) {?>
										<video src=<?php echo "\"".$video1."\" ".$taille_video."";?>></video>
									<?php
									}
									?>
								</td>
								<td align="left">
									<?php if (remote_file_exists($video1)) {?>
										<a href="<?php echo $video1; ?>">T&eacute;l&eacute;charger la vid&eacute;o de <?php echo substr($info_date,11,2).":00 - ".substr($info_date,11,2).":15"; ?></a>
									<?php
									}
									else echo "Vid&eacute;o ".substr($info_date,11,2).":00 - ".substr($info_date,11,2).":15"." non demand&eacute;e";
									?>
								</td>
				</tr>
				<tr>
								<td align="left">
									<?php if (remote_file_exists($video2)) {?>
										<video src=<?php echo "\"".$video2."\" ".$taille_video."";?>></video>
								<?php
									}
									?>
								</td>
								<td align="left">
									<?php if (remote_file_exists($video2)) {?>
										<a href="<?php echo $video2; ?>">T&eacute;l&eacute;charger la vid&eacute;o de <?php echo substr($info_date,11,2).":15 - ".substr($info_date,11,2).":30"; ?></a>
									<?php
									}
									else echo "Vid&eacute;o ".substr($info_date,11,2).":15 - ".substr($info_date,11,2).":30"." non demand&eacute;e";
									?>
								</td>
				</tr>
				<tr>
								<td align="left">
									<?php if (remote_file_exists($video3)) {?>
										<video src=<?php echo "\"".$video3."\" ".$taille_video."";?>></video>
								<?php
									}
									?>
								</td>
								<td align="left">
									<?php if (remote_file_exists($video3)) {?>
										<a href="<?php echo $video3; ?>">T&eacute;l&eacute;charger la vid&eacute;o de <?php echo substr($info_date,11,2).":30 - ".substr($info_date,11,2).":45"; ?></a>
									<?php
									}
									else echo "Vid&eacute;o ".substr($info_date,11,2).":30 - ".substr($info_date,11,2).":45"." non demand&eacute;e";
									?>
								</td>
				</tr>
				<tr>
								<td align="left">
									<?php if (remote_file_exists($video4)) {?>
										<video src=<?php echo "\"".$video4."\" ".$taille_video."";?>></video>
								<?php
									}
									?>
								</td>
								<td align="left">
									<?php if (remote_file_exists($video4)) {?>
										<a href="<?php echo $video4; ?>">T&eacute;l&eacute;charger la vid&eacute;o de <?php echo substr($info_date,11,2).":45 - ".(substr($info_date,11,2)+1).":00"; ?></a>
									<?php
									}
									else echo "Vid&eacute;o ".substr($info_date,11,2).":45 - ".(substr($info_date,11,2)+1).":00"." non demand&eacute;e";
									?>
								</td>
				</tr>
				<tr>
								<td align="left">
									<?php if (remote_file_exists($video5)) {?>
										<video src=<?php echo "\"".$video5."\" ".$taille_video."";?>></video>
								<?php
									}
									?>
								</td>
								<td align="left">
									<?php if (remote_file_exists($video5)) {?>
										<a href="<?php echo $video5; ?>">T&eacute;l&eacute;charger la vid&eacute;o de <?php echo (substr($info_date,11,2)+1).":00 - ".(substr($info_date,11,2)+1).":15"; ?></a>
									<?php
									}
									else echo "Vid&eacute;o ".(substr($info_date,11,2)+1).":00 - ".(substr($info_date,11,2)+1).":15"." non demand&eacute;e";
									?>
								</td>
				</tr>
				<tr>
								<td align="left" colspan="2">
									&nbsp;
								</td>
				</tr>
	</table>
