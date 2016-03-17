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
	global $Itemid;
	$Itemid = JRequest::getInt('Itemid');
?>

<?php
echo $lists["panel"];
?>
<form action="<?php echo JRoute::_("index.php?option=com_joomsport&task=player&id=".$lists["player"]->id."&sid=0");?>" method="post" name="adminForm" id="adminForm">

<!-- <module middle> -->
	<div class="module-middle solid">
		
		<!-- <back box> -->
			<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div>
		<!-- </back box> -->
		
		<!-- <title box> -->
		<div class="title-box">
			<div>
				<?php echo $this->lists['socbut'];?>
			</div>
			<h2><span itemprop="name"><?php echo $this->escape($this->params->get('page_title')); ?></span></h2>
			<div class="select-wr" <?php if ($this->lists['socbut']){echo "style='top:40px;'";}?>>
			<span class="down"><!-- --></span>
			<?php echo $this->lists['tourn']?>
			</div>
		</div>
		<!-- </div>title box> -->
		
		<!-- <tab box> -->
		<ul class="tab-box">
			<?php 
			 require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
			 $etabs = new esTabs();
			  echo $etabs->newTab(JText::_('BL_TAB_PLAYER'),'etab_player','players','vis');
			  echo $etabs->newTab(JText::_('BL_TAB_STAT'),'etab_stat','statistic');
			  if(count($lists["matches"])){
				 echo  $etabs->newTab(JText::_('BL_TAB_MATCHES'),'etab_match','tab_flag');
			  }
			  if(count($lists["photos"])){
				echo $etabs->newTab(JText::_('BL_TAB_PHOTOS'),'etab_photos','photo');
			  }
			  
			?>
		</ul>
		<!-- </tab box> -->
		
	</div>
	<!-- </module middle> -->
<!-- <content module> -->
		<div class="content-module">
<div id="etab_player_div" class="tabdiv">

				
				<!-- <gray box> -->
				<div class="gray-box">
					
						<?php
						if($lists["def_img"] && is_file('media/bearleague/'.$lists["def_img"])){
							$imgsize = getimagesize('media/bearleague/'.$lists["def_img"]);
							if($imgsize[0] > 200){
								$width = 200;
							}else{
								$width  = $imgsize[0];
							}
							
							echo '<a rel="lightbox-imgsportsingle" href="'.getImgPop($lists["def_img"]).'" class="gray-box-img"><img itemprop="image" src="'.JURI::base().'media/bearleague/'.$lists["def_img"].'" width="'.$width.'" alt="'.$lists["player"]->first_name.' '.$lists["player"]->last_name.'" /></a>';
						}else{
							echo '<img itemprop="image" src="'.JURI::base().'media/bearleague/player_st.png" width="200" alt="" style="margin-bottom:18px;" />';
						}
						
						?>
					
					<table cellpadding="0" cellspacing="0" border="0" class="adf-fields-table">
						<tr>
							<td><?php echo JText::_("BL_NAME");?>:</td>
							<td><?php echo $lists["player"]->first_name.' '.$lists["player"]->last_name ;?></td>
						</tr>
						<?php if($lists["player"]->country){?>
						<tr>
							<td></td>
							<td><img src="<?php echo JURI::base()?>components/com_joomsport/img/flags/<?php echo strtolower($lists["player"]->ccode)?>.png" alt="<?php echo $lists["player"]->country; ?>" title="<?php echo $lists["player"]->country; ?>" />&nbsp;&nbsp;<?php echo $lists["player"]->country; ?></td>
						</tr>
						<?php } ?>
						<?php if($lists["player"]->nick) {?>
						<tr>
							<td>
								<?php echo JText::_("BL_NICK");?> :
							</td>
							<td>
								<?php echo $lists["player"]->nick ;?>
							</td>
						</tr>
						<?php
						}
							echo $lists['ext_fields'];
						?>
						
					</table>
					<div class="gray-box-cr tl"><!-- --></div>
					<div class="gray-box-cr tr"><!-- --></div>
					<div class="gray-box-cr bl"><!-- --></div>
					<div class="gray-box-cr br"><!-- --></div>
				</div>
				<!-- <gray box> -->
				
				<div class="jscontent">
					<?php echo $lists["player"]->about;?>
				</div>
				
			

</div>
<div id="etab_stat_div" class="tabdiv" style="display:none;">

				
				<table class="player-statistic" cellpadding="0" cellspacing="0" border="0">
					<?php
					for($i=0;$i<count($lists["stat_array"]);$i++){
						$stats = $lists["stat_array"][$i];
						echo "<tr class='dotted'>";
						echo "<td class='dotted'>";
						echo $stats[2];
						echo $stats[0];
						echo "</td>";
						echo "<td class='dotted'>";
						echo $stats[1];
						echo "</td>";
						echo "</tr>";
					}
					?>
				</table>
				
		

</div>
<?php if (count($lists["matches"])){ ?>
<div id="etab_match_div" class="tabdiv" style="display:none;">
<table id="calendar" cellpadding="0" cellspacing="0" class="match-day">
<?php
	for($i=0;$i<count($lists["matches"]);$i++){
		$match = $lists["matches"][$i];
		?>
		<tr class="<?php echo $i%2?"gray":"";?>">
			<td class="m_name" nowrap="nowrap"><?php echo $match->m_name.":"?></td>
			<td nowrap="nowrap"><?php echo date_bl($match->m_date,$match->m_time);?></td>
			<td class="team_h"><?php echo $match->home?></td>
			<td class="score"><span class="score">
				<?php 
				if($match->m_played == 1){
					echo '<b class="score-h">'.$match->score1?></b>
					<b>:</b>
					<b class="score-a">
					<?php 
					echo $match->score2; 
					echo "</b>";
					if(@$lists["enbl_extra"] && $match->is_extra)
					{ 
						$class_ext = ($match->score1 > $match->score2)?"extra-time-h":"extra-time-g";
						echo '<span class="'.$class_ext.'">'.JText::_('BL_RES_EXTRA').'</span>';
						
					}
					
				}else{
					echo '<b class="score-h">-</b>';?>
					<b>:</b>
					<b class="score-a">-</b>
					<?php
				}
				?>
				</span>
			</td>
			<td class="team-a" style="padding-left:25px;"><?php echo $match->away?></td>
			<?php if($lists["locven"]){?>
			<td><?php echo getJS_Location($match->mid);?></td>
			<?php } else { echo "<td>&nbsp;</td>";} ?>
			<td class="match_details"> 
				<a class="button-details" href="<?php echo JRoute::_('index.php?option=com_joomsport&task=view_match&id='.$match->mid.'&Itemid='.$Itemid).'"><span>'.JText::_('BL_LINK_DETAILMATCH').'</span>'?></a>
			</td>   
		</tr>
		<?php
	}
	?>
</table>
</div>

<?php
}
if(count($lists["photos"])){
echo '<div id="etab_photos_div" class="tabdiv" style="display:none;">';
?>
<!-- <player gallery> -->
<ul class="player-gallery">
<?php					

	for($i=0;$i<count($lists["photos"]);$i++){
		$photo = $lists["photos"][$i];
	?>

			<li><a rel="lightbox-imgsport" href="<?php echo getImgPop($photo->filename)?>" title="<?php echo htmlspecialchars($photo->name)?>"  class="team-images"><img src="<?php echo JURI::base();?>media/bearleague/<?php echo $photo->filename?>" alt="<?php echo htmlspecialchars($photo->name)?>" title="<?php echo htmlspecialchars($photo->name)?>" height="100"  class="allimages" /></a></li>

	<?php
	}
echo "</div></ul>";
}

?>
</form>
</div>
<!-- </content module> -->