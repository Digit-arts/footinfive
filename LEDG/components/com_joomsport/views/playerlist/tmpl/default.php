<?php/*------------------------------------------------------------------------# JoomSport Professional # ------------------------------------------------------------------------# BearDev development company # Copyright (C) 2011 JoomSport.com. All Rights Reserved.# @license - http://joomsport.com/news/license.html GNU/GPL# Websites: http://www.JoomSport.com # Technical Support:  Forum - http://joomsport.com/helpdesk/-------------------------------------------------------------------------*/// no direct accessdefined( '_JEXEC' ) or die( 'Restricted access' );$Itemid = JRequest::getInt('Itemid');?><?php//echo $this->lists["panel"];?><script>function js_newsort(sort,th){	document.adminForm.sortfield.value = sort;	if(th.hasClass('desc')){ 		document.adminForm.sortdest.value = 0;	}else{		document.adminForm.sortdest.value = 1;	}	document.adminForm.submit();}</script><form action="<?php echo JRoute::_('index.php?option=com_joomsport&view=playerlist&sid='.$this->lists["s_id"].'')?>" method="post" name="adminForm" id="adminForm"><!-- <module middle> -->	<!--div class="module-middle solid"-->				<!-- <back box> >		<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div>		<!-- </back box> -->				<!-- <title box> 		<div class="title-box padd-bot">			<h2>				<?php					echo ' '.($this->escape($this->params->get('page_title')));								?>			</h2>			<div class="select-wr">				<span class="down"></span>				<?php echo $this->lists['tourn'];?>			</div>		</div>		</div><title box> -->	<!--/div-->	<!-- </module middle> -->			<!-- <content module> --><div class="content-module">	<table class="season-list team-list" cellpadding="0" cellspacing="0" border="0">		<thead>			<tr>								<th class="sort<?php echo ($this->lists["field"] == "name")?($this->lists["dest"]?" desc down":" asc"):("");?>" axis="string" onclick="js_newsort('name',this);"><span><?php echo JText::_('BL_TAB_PLAYER');?></span></th>				<?php if(!$this->lists["t_single"] && $this->lists["s_id"]){?>				<th class="sort <?php echo ($this->lists["field"] == "sortteams")?($this->lists["dest"]?" desc down":" asc"):("");?>" axis="string" onclick="js_newsort('sortteams',this);"><span><?php echo JText::_('BL_TAB_TEAM');?></span></th>				<?php } ?>				<?php				if(isset($this->lists["events"])){					for($i=0;$i<count($this->lists["events"]);$i++){						echo "<th class='sort".(($this->lists["field"] == $this->lists["events"][$i]->id)?($this->lists["dest"]?" desc down":" asc"):(""))."' axis='int' onclick='js_newsort(".($this->lists["events"][$i]->id).",this);'><span>".(isset($this->lists["events"][$i]->e_imgth)?$this->lists["events"][$i]->e_imgth:$this->lists["events"][$i]->e_name)."</span></th>";					}				}				?>				<th>Ratio<br>buts/match</th>			</tr>		</thead>	<tbody>	<?php	if($this->lists["limit"] == 0){		$this->lists["limit"] = count($this->lists["players"])-$this->lists["limitstart"];	}		for($i=0;$i<count($this->lists["total_matchs_joues"]);$i++){		$total_matchs_joues = $this->lists["total_matchs_joues"][$i];		$bly_tab_equipe[$total_matchs_joues->equipe]=$total_matchs_joues->nbre_matchs;	}						for($i = $this->lists["limitstart"];$i<$this->lists["limit"]+$this->lists["limitstart"];$i++){		if(isset($this->lists["players"][$i])){			$player = $this->lists["players"][$i];			$link = JRoute::_("index.php?option=com_joomsport&view=player&id=".$player["id"]."&sid=".$this->lists["s_id"]."&Itemid=".$Itemid);			if(!$this->lists["t_single"]){				$link2 = JRoute::_("index.php?option=com_joomsport&task=team&tid=".$player->tid."&sid=".$this->lists["s_id"]."&Itemid=".$Itemid);			}			?>			<tr class="<?php echo $i % 2?"":"gray";?>">								<td style="text-align:left;">					<?php						if($player['photo'] && is_file('media/bearleague/'.$player['photo'])){							echo '<img class="team-embl  player-ico" src="'.JURI::base().'media/bearleague/'.$player['photo'].'" width="29" height="29" alt="'.$player['name'].'" />';						}else{							echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">';						}					?>															<p class='player-name'>						<a href="<?php echo $link?>"><?php echo $player["name"]?></a>					</p>					</td>				<?php if(!$this->lists["t_single"] && $this->lists["s_id"]){?>				<td><a href="<?php echo $link2?>"></a>				<?php echo $player["teams"];?></td>				<?php } ?>				<?php					if(isset($this->lists["events"])){						for($j=0;$j<count($this->lists["events"]);$j++){							echo "<td>".$player[$this->lists["events"][$j]->id]."</td>";						}					}				?>				<td style="text-align:center;">					<?php					if (($bly_tab_equipe[$player["id_teams"]]-$player[$this->lists["events"][1]->id])<>0)						$ratio=number_format(($player[$this->lists["events"][0]->id])/($bly_tab_equipe[$player["id_teams"]]-$player[$this->lists["events"][1]->id]),2);					else $ratio=0;										echo $ratio;					?>				</td>			</tr>		<?php } ?>	<?php } ?>	</tbody>	<!--tfoot>	<tr>		<td colspan="13" style="text-align:center;">			<?php echo $this->page->getListFooter(); ?>		</td>	</tr>	</tfoot--></table></div><input type="hidden" name="option" value="com_joomsport" /><input type="hidden" name="view" value="playerlist" /><input type="hidden" name="Itemid" value="<?php echo $Itemid?>" /><input type="hidden" name="sortfield" value="<?php echo $this->lists["field"]?>" /><input type="hidden" name="sortdest" value="<?php echo $this->lists["dest"]?>" /><input type="hidden" name="limitstart" value="0" /></form>