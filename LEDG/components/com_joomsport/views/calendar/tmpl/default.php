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

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();

if(isset($this->message)){
	$this->display('message');
}
$lists = $this->lists;
$Itemid = JRequest::getInt('Itemid');
?>
<?php
/*if($this->tmpl != 'component'){
	echo $lists["panel"];

	$lnk = "window.open('".JURI::base()."index.php?tmpl=component&option=com_joomsport&amp;view=calendar&amp;sid=".$lists["s_id"]."','jsmywindow','width=600,height=700');";
}else{
	$lnk = "window.print();";
}*/
?>
<script type="text/javascript">
    function resetPoints(el){
        if ($(el).get('checked') == true){
            $(el).getAllNext('input').set('disabled', false);
        } else {
            $(el).getAllNext('input').set('disabled', true);
        }
    }
	function js_showfil(){
		if(getObj('js_tblfilt_id').style.display=="block"){
			getObj('js_tblfilt_id').style.display="none";
		}else{
			getObj('js_tblfilt_id').style.display="block";
		}
	}
</script>
<!-- <module middle> -->
<form name="adminForm" id="adminForm" action="" method="post">
<div class="module-middle">
	
	<!-- <back box> >
	<?php if($this->tmpl != 'component'){?>
			<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div>
	<?php } ?>
	<!-- </back box> -->
	
	<!-- <title box> >
	<div class="title-box">
		<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
		<a class="print" href="#" onClick="<?php echo $lnk;?>" title="Print">Print</a>
		
	</div-->
	<div style="padding-bottom:20px;">
		<table id="js_tblfilt_id" border="0" cellspacing="2" cellpadding="0" class="adf-fields-table" style="display:none;">
			<tr>
				<td class="js_filtername"><?php echo JText::_("BLFA_TEAM");?></td>
				<td>		
					<div style="position:relative;float:left">
					<span class="down"><!-- --></span>
					<?php echo $this->lists['teams'];?>
					</div>
					<div style="position:relative;float:left;" class="js_minth">
					<span class="down"><!-- --></span>
					<?php echo $this->lists['teamhm'];?>
					</div>
				</td>	
			
				
			
				<td class="js_filtername"><?php echo JText::_("BLFA_MATCHDAY");?></td>
				<td>
					<div style="position:relative;">
						<span class="down"><!-- --></span>
						<?php echo $this->lists['mdays'];?>
					</div>
				</td>
				<td class="js_filtername"><?php echo JText::_("BLFA_DATE");?></td>
				<td>
					<?php echo $this->lists['fromdate'];?>
					<?php echo $this->lists['todate'];?>
				</td>	
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
				<td align="right">
					<button class="send-button" onclick="document.adminForm.submit();">
						<span>
							<b><?php echo JText::_("BLFA_SEARCH");?></b>
						</span>
					</button>
				</td>
			</tr>
		</table>
			<!--div style="text-align:right;margin-top:10px;margin-bottom:-20px;"><a href="javascript:void(0);" onclick="js_showfil();"><?php echo JText::_("BLFA_SEARCH_MATCHES");?></a></div-->
		</div>
	<!-- </div>title box> -->
	
</div>
<!-- </module middle> -->
<!-- <content module> -->
			<div class="content-module padd-off" style="overflow:visible !important;">
				<?php
				$old_md = 0;
				if (count($lists["matchs"])<=0)
					echo "Une fois le tirage au sort effectu&eacute;, le calendrier des rencontres sera affich&eacute;.";
				else {
				for($i=0;$i<count($lists["matchs"]);$i++){
				
					$match = $lists["matchs"][$i];
					
					if( $old_md != $match->mdid){
						if($i){
							echo "</table>";
						}
						echo "<h3 class='solid'>".$match->m_name."</h3>";
						echo '<table class="match-day" cellpadding="0" cellspacing="0" border="0">';
					}
					$old_md = $match->mdid;
				?>
				<tr class="<?php echo $i % 2?"":"gray";?>">
					
					<td class="match-day-date" nowrap>
					<?
						if ($match->k_title<>"")
							echo "".$match->k_title." FINALE : ";
						echo date_bl($match->m_date,$match->m_time)." - ".$match->m_location;
						
					?>
					</td>
					<td class="team-ico-h"><!-- -->
					<?php
						/*if(!$this->lists['t_single']){
							if($match->emb1 && is_file('media/bearleague/'.$match->emb1)){
								echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$match->emb1.'" width="29" height="29" alt="'.$match->home.'" />';
							}else{
								echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
							}
						}*/
					?>
					</td>
					<td class="team-h" nowrap="nowrap">	
					<?php
						 if($lists["t_single"]){
							$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->hm_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
						 }else{
							$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->hm_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
						 }
						?>
						<a href="<?php echo $link;?>"><?php echo $match->home?></a>
						<?
						if($match->m_played == 1 and $match->nbre_jours>5) 
							echo ($match->bonus1 && $match->bonus1 != '0.00')?"(<font style='font-size:75%;'>".floatval($match->bonus1)."</font>)":"(0)";
						?>
					</td>
					<td class="score" nowrap="nowrap">
						<span class="score">
						<?php $lnks =  JRoute::_('index.php?option=com_joomsport&task=view_match&id='.$match->id.'&Itemid='.$Itemid);?>
						
						<?php
							if($match->m_played == 1){  
								$thismat =  '<b class="score-h">'.$match->score1; 
								
								$thismat .=  '</b>';
								$thismat .= "<b>:</b>";
								$thismat .= '<b class="score-a">'.$match->score2;
								
								$thismat .=  '</b>';
								$thismat2 = '';
								$tmpmat = '';
								if(@$lists["enbl_extra"] && $match->is_extra)
								{ 
									$class_ext = ($match->score1 > $match->score2)?"extra-time-h":"extra-time-g";
									$thismat2 = '<div class="'.$class_ext.'">'.JText::_('BL_RES_EXTRA').'</div>';
									$tmpmat = '&nbsp;<font style="font-size:80%;">('.JText::_('BL_RES_EXTRA').')</font>';
								}
								
								//match info tooltip
								
								$ev_count = (count($match->m_events_home) > count($match->m_events_away)) ? (count($match->m_events_home)) : (count($match->m_events_away));
								$tbl_info = "<div class='tooltip-wr'><table class='tooltptbl' border='0' cellspacing='0' cellpadding='0'>";
								$tbl_info .= "<tr class='gray' style='border:0px;'><td class='tool-home-td' align='right'><b>".$match->home."</b></td><td colspan='4' align='center' width='80' nowrap='nowrap'>".($match->score1>$match->score2?$tmpmat." ":"").$match->score1." : ".$match->score2.($match->score1<=$match->score2?$tmpmat:"")."</td><td class='tool-home-td' align='left'><b>".$match->away."</b></td></tr>";
								$thismat .= $thismat2;
								for($j=0;$j<$ev_count;$j++){
									$tbl_info .= "<tr class='gray'>";
									if(isset($match->m_events_home[$j])){
									
										$tbl_info .= "<td class='home_event' width='42%'>";
										if($match->m_events_home[$j]->e_img && is_file('media/bearleague/events/'.$match->m_events_home[$j]->e_img)){
											$tbl_info .= '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$match->m_events_home[$j]->e_img.'" title="'.$match->m_events_home[$j]->e_name.'" alt="'.$match->m_events_home[$j]->e_name.'" />';
										}else{ 
											$tbl_info .= $match->m_events_home[$j]->e_name;
										}
										if(!$this->lists['t_single']){
											$tbl_info .= '&nbsp;'.$match->m_events_home[$j]->p_name;
										}
										$tbl_info .= '</td>';
										
										$tbl_info .= '<td class="home_event_count" width="20">';
										
										if($match->m_events_home[$j]->ecount){
											$tbl_info .= $match->m_events_home[$j]->ecount;
										}else {$tbl_info .= "0";}
										
										$tbl_info .= '</td>';
										$tbl_info .= '<td class="home_event_minute" width="20">';
									
										if($match->m_events_home[$j]->minutes){
											$tbl_info .= $match->m_events_home[$j]->minutes."&rsquo;";
										}else{ $tbl_info .= "&nbsp;";}
										
										$tbl_info .= '</td>';
										
									}else{
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= '<td style="padding:0px">&nbsp;</td>';
									}
									if(isset($match->m_events_away[$j])){
										
										$tbl_info .= '<td class="away_event_minute" width="20">';
										
										if($match->m_events_away[$j]->minutes){
											$tbl_info .= $match->m_events_away[$j]->minutes."&rsquo;";
										}else{ $tbl_info .= "&nbsp;";}
										
										$tbl_info .= "</td>";
										$tbl_info .= '<td class="away_event_count" width="20">';
									
										if($match->m_events_away[$j]->ecount){
											$tbl_info .= $match->m_events_away[$j]->ecount;
										}else{ $tbl_info .= "0";}
										
										$tbl_info .= "</td>";
										
										$tbl_info .= '<td class="away_event" width="42%">';
										if($match->m_events_away[$j]->e_img && is_file('media/bearleague/events/'.$match->m_events_away[$j]->e_img)){
											$tbl_info .= '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$match->m_events_away[$j]->e_img.'" title="'.$match->m_events_away[$j]->e_name.'" alt="'.$match->m_events_away[$j]->e_name.'" />';
										}else{ 
											$tbl_info .= $match->m_events_away[$j]->e_name;
										}
										if(!$this->lists['t_single']){
											$tbl_info .= "&nbsp;".$match->m_events_away[$j]->p_name;
										}	
										$tbl_info .= "</td>";
									}else{
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= "<td style='padding:0px'>&nbsp;</td>";
									}
									$tbl_info .= '</tr>';
								}
								
								$tbl_info .= '</table></div>';
								$tbl_title = $tbl_info;
								//echo JHTML::tooltip($tbl_info,$tbl_title,"",$thismat,$lnks);
								echo '<a href="'.$lnks.'" class="bdtooltip"><span>'.$tbl_title.'</span>'.$thismat.'</a>';
							}else{
								echo "<a href='".$lnks."' class='so_not_played'>";

								echo '<b class="score-h">-</b>';?>
								<b>:</b>
								<b class="score-a">-</b>
								<?php
								echo "</a>";
								if (isset($match->betavailable) && isset($match->betfinish) && $match->betavailable && $match->betfinish){
									?>
									<table style="display:none" class="bettable">
										<tr>
											<th><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
											<th></th>
											<th><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
										</tr>
										<?php foreach($match->betevents as $event):?>
										<?php if ($event->coeff1 || $event->coeff2):?>
										<tr>
											<td>
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
											<td>
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
											<td colspan="3">
												<input type="hidden" name="bet_match[]" value="<?php echo $match->id?>"/>
												<input type="button" value="<?php echo JText::_("BLFA_BET_SUBMIT_BET");?>" onClick="document.adminForm.task.value = 'bet_calendar_save';document.adminForm.submit();"/>
											</td>
										</tr>
									</table>
									<?php
								}
							}
							
							?>
						</span>	
					</td>
					<?php
					 if($lists["t_single"]){
						$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->aw_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
					 }else{
						$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->aw_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
					 }
					?>
					<td class="team-ico-a"><!-- -->
					<?php
						/*if(!$this->lists['t_single']){
							if($match->emb2 && is_file('media/bearleague/'.$match->emb2)){
								echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$match->emb2.'" width="29" height="29" alt="'.$match->away.'" />';
							}else{
								echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
							}
						}*/
					?>
					</td>
					<td class="team-a" nowrap>
					<?
					if($match->m_played == 1 and $match->nbre_jours>5)
						echo ($match->bonus2 && $match->bonus2 != '0.00')?"(<font style='font-size:75%;'>".floatval($match->bonus2)."</font>)":"(0)";?>
						
						<a href="<?php echo $link;?>"><?php echo $match->away?></a></td>
					
					<td nowrap="nowrap">
					<?
					if($match->m_played <> 1){?>
						<a href="<?php echo "index.php/fm?tmpl=component&print=1&page=&Num_Match=".$match->id; ?>"  target="_blank"/><img src="images/stories/fm-icon.png" title="Feuille de match vierge"></a>
					<?php
					}
                                        $feuile_match=recup_nom_feuille_match($match->id);
                                        if (test_non_vide($feuile_match)){?>
						<a  title="Scan feuille de match" href="<? echo $feuile_match;?>" target="_blank"><img src="images/stories/feuille-de-match-scan.png" alt="Scan feuille de match"></a>
					<?}
                                        
                                        if (est_agent($user)){?>
                                             <a  title="Resa sur FIF" href="http://footinfive.com/FIF/index.php/component/content/article?id=61&id_resa=<? echo recup_id_resa_fif($match->id);?>" target="_blank">
                                                <img src="images/stories/icon-creneau-reserver.png" alt="Resa sur FIF"></a>
                                        <?}
                                        
					if (($match->nbre_jours)<6 and ($match->nbre_jours)>=0 and $match->m_played == 1 ) {

						?> 
						<?
					}
					if (($match->nbre_jours)>=-7 and ($match->nbre_jours)<0 and $match->m_played == 0) {
						?><a href="index.php/ep/info-presence"/><img src="images/stories/present.png" title="Je serais present"  />
						<img src="images/stories/absent.png" title="je serais absent" /></a>&nbsp;&nbsp;&nbsp;
						<a href="index.php/ep/declarer-forfait"/><img src="images/stories/declarer-forfait.png" title="declarer forfait"  /></a>
							
					<?}
					
					
					 if($lists["locven"]){?>
					<?php echo getJS_Location($match->id);?>
					<?php } ?>
					</td>
					<?php if ($match->betavailable && $match->betfinish):?>
						<td>
							<a href="#bet" onClick="$(this).getParent().getParent().getElements('.bettable').set('styles', {display: ''})"><?php echo JText::_('BLFA_BET_BETME');?></a>
						</td>
					<?php else:?>
						<td></td>
					<?php endif;?>
				</tr>		
				<?php
				}
				}
			?>

			<tr>
				<td colspan="13" align="center" style="padding-top:10px;">
					<?php echo $this->page->getListFooter(); ?>
				</td>
			</tr>
			
	</table>
	</div>
	<!-- </content module> -->
</form>