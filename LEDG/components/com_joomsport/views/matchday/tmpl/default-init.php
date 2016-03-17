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
<?php
echo $lists["panel"];
?>
<div class="module-middle">
	
	<!-- <back box> -->
		<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div>
	<!-- </back box> -->
	
	<!-- <title box> -->
	<div class="title-box">
		<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>		
	</div>
	<!-- </div>title box> -->
	
</div>
<!-- </module middle> -->
<!-- <content module> -->
<div class="content-module padd-off">
<table class="match-day" cellpadding="0" cellspacing="0" border="0">
	<?php
		for($i=0;$i<count($this->lists["match"]);$i++){
			$match = $this->lists["match"][$i];
		?>
		<tr class="<?php echo $i % 2?"":"gray";?>">
			<td class="match-day-date">
				<?php echo date_bl($match->m_date,$match->m_time);?>
			</td>
			<td class="team_h"><?php echo $match->home?></td>
			<td class="team-ico-h-l">
				<?php
					if(!$this->lists['t_single'] && $match->m_single!='1'){
						if($match->emb1 && is_file('media/bearleague/'.$match->emb1)){
							echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$match->emb1.'" width="29" height="29" alt="'.$match->home.'" />';
						}else{
							echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
						}
					}
				?>
			</td>
			<td class="score" nowrap="nowrap">
				<span class="score">
				<?php 
				if($match->m_played == 1){
					echo '<b class="score-h">'.$match->score1?></b><b>:</b>
					<b class="score-a">
					<?php echo $match->score2;
					echo '</b>';
					
					if(@$lists["enbl_extra"] && $match->is_extra)
					{ 
						$class_ext = ($match->score1 > $match->score2)?"extra-time-h":"extra-time-g";
						echo '<span class="'.$class_ext.'">'.JText::_('BL_RES_EXTRA').'</span>';
						
					}
					
				}else{
					echo '<b class="score-h">-</b>';
					echo '<b>:</b>';
					echo '<b class="score-a">-</b>';
				}
				?>
				</span>
			</td>
			<td class="team-ico-a"><!-- -->
			<?php
				if(!$this->lists['t_single'] && $match->m_single!='1'){
				
					if($match->emb2 && is_file('media/bearleague/'.$match->emb2)){
						echo '<img class="team-embl" src="'.JURI::base().'media/bearleague/'.$match->emb2.'" width="29" height="29" alt="'.$match->away.'" />';
					}else{
						echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
					}
				}
			?>
			</td>
			<td class="team_a"><?php echo $match->away?></td>
			<td class="match_details"> 
				<a class="button-details" href="<?php echo JRoute::_('index.php?option=com_joomsport&view=match&id='.$match->id)?>"><span><?php echo JText::_('BL_LINK_DETAILMATCH');?></span></a>
			</td>   
		</tr>		
		<?php
		}
	?>
	
</table>
</div>