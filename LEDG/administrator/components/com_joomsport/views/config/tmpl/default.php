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
defined('_JEXEC') or die;
JHTML::_('behavior.tooltip');
$lists = $this->lists;
		
		require_once(JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php');
		$etabs = new esTabs();
		
		?>
		
		<script type="text/javascript" src="components/com_joomsport/color_piker/201a.js"></script>
		
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
		<!-- <tab box> -->
		<ul class="tab-box">
			<?php
			echo $etabs->newTab(JText::_('BLBE_GENERAL'),'main_cfg','','vis');
			echo $etabs->newTab(JText::_('BLBE_REGISTR'),'reg_cfg','');
			echo $etabs->newTab(JText::_('BLBE_ADMRIGHTS'),'admrigh_cfg','');
			echo $etabs->newTab(JText::_('BLBE_ESPORTCONF'),'esport_cfg','');
			echo $etabs->newTab(JText::_('BLBE_SOCIALCONF'),'social_cfg','');
			?>
		</ul>
		<div style="clear:both"></div>
		<div id="main_cfg_div" class="tabdiv">
		<table class="adminlist">
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_DATECONFIG' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_DATECONFIG' ); ?>::<?php echo JText::_( 'BLBE_TT_DATECONFIG' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $lists['data_sel'] ?>
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_YTEAMCOLOR' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_YTEAMCOLOR' ); ?>::<?php echo JText::_( 'BLBE_TT_YTEAMCOLOR' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<div id="colorpicker201" class="colorpicker201"></div>
					<input type="button" style="cursor:pointer;" onclick="showColorGrid2('yteam_color','sample_1');" value="...">&nbsp;<input type="text" name="yteam_color" id="yteam_color" size="9" maxlength="30" value="<?php echo $lists['yteam_color'];?>" /><input type="text" id="sample_1" size="1" value="" style="background-color:<?php echo $lists['yteam_color']?>" />
				</td>
			
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_UNABMATCHCOM");?></td>
				<td><input type="checkbox" name="mcomments" value="1" <?php echo ($lists['mcomments']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_UNABLEPLREG");?></td>
				<td><input type="checkbox" name="player_reg" value="1" <?php echo ($lists['player_reg']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_UNBTEAMREG");?></td>
				<td><input type="checkbox" name="team_reg" value="1" <?php echo ($lists['team_reg']=='1')?" checked":"";?> /></td>
			</tr>
			
			<tr>
				<td><?php echo JText::_("BLBE_PLLISTORDER");?></td>
				<td><?php echo $lists['pllist_order'];?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_TEAMLOGOTBL");?></td>
				<td><input type="text" name="teamlogo_height" value="<?php echo $lists['teamlogo_height'];?>" /></td>
			</tr>
			
			<tr>
				<td><?php echo JText::_("BLBE_UNABVENUE");?></td>
				<td><input type="checkbox" name="unbl_venue" value="1" <?php echo ($lists['unbl_venue']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_CALVENUE");?></td>
				<td><input type="checkbox" name="cal_venue" value="1" <?php echo ($lists['cal_venue']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_SHOWPLAYEDMATCHES");?></td>
				<td><input type="checkbox" name="played_matches" value="1" <?php echo ($lists['played_matches']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_NAMEDONFE");?></td>
				<td><?php echo $lists['player_name'];?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_KNOCKSTYLE");?></td>
				<td><?php echo $lists['knock_style'];?></td>
			</tr>
			
		</table>
		</div>
		<div id="reg_cfg_div" class="tabdiv" style="display:none;">
		<table width="100%" class="adminlist">
			<tr>
				<th width="50%"><?php echo JText::_('BLBE_REGPLFLD');?></th>
				<th width="50%"><?php echo JText::_('BLBE_REGTEAMFLD');?></th>
			</tr>
			<tr>
				<td width="50%" valign="top">
					<table>
						<tr>
							<th width="50%"><?php echo JText::_('BLBE_FIELD');?></th>
							<th><?php echo JText::_('BLBE_ONREGPAGE');?></th>
							<th><?php echo JText::_('BLBE_REQUIRED');?></th>
						</tr>
						<tr>
							<td><?php echo JText::_("BLBE_NICKNAME");?></td>
							<td><input type="checkbox" name="nick_reg" value="1" <?php echo ($lists['nick_reg']=='1')?" checked":"";?> /></td>
							<td><input type="checkbox" name="nick_reg_rq" value="1" <?php echo ($lists['nick_reg_rq']=='1')?" checked":"";?> /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("BLBE_COUNTRY");?></td>
							<td><input type="checkbox" name="country_reg" value="1" <?php echo ($lists['country_reg']=='1')?" checked":"";?> /></td>
							<td><input type="checkbox" name="country_reg_rq" value="1" <?php echo ($lists['country_reg_rq']=='1')?" checked":"";?> /></td>
						</tr>
					
						<?php
						for($i=0;$i<count($lists['adf_player']);$i++){
							$regpl = $lists['adf_player'][$i];
							
							echo '<tr><td><input type="hidden" name="adf_pl[]" value="'.$regpl->id.'" />'.$regpl->name.'</td>';
							echo '<td><input type="checkbox" name="adfpl_reg_'.$regpl->id.'" value="1" '.($regpl->reg_exist?" checked":"").' /></td>';
							echo '<td><input type="checkbox" name="adfpl_rq_'.$regpl->id.'" '.(($regpl->field_type == 2)?"readonly='readonly'":"").' value="1" '.($regpl->reg_require?" checked":"").' /></td></tr>';
						}
						?>
					</table>
				</td>
				<td width="50%" valign="top">
					<table>
						<tr>
							<th width="50%"><?php echo JText::_('BLBE_FIELD');?></th>
							<th><?php echo JText::_('BLBE_ONREGPAGE');?></th>
							<th><?php echo JText::_('BLBE_REQUIRED');?></th>
						</tr>
						<?php
						for($i=0;$i<count($lists['adf_team']);$i++){
							$regpl = $lists['adf_team'][$i];
							
							echo '<tr><td><input type="hidden" name="adf_tm[]" value="'.$regpl->id.'" />'.$regpl->name.'</td>';
							echo '<td><input type="checkbox" name="adf_reg_'.$regpl->id.'" value="1" '.($regpl->reg_exist?" checked":"").' /></td>';
							echo '<td><input type="checkbox" '.(($regpl->field_type == 2)?"readonly='readonly'":"").' name="adf_rq_'.$regpl->id.'" value="1" '.($regpl->reg_require?" checked":"").' /></td></tr>';
						}
						if(!count($lists['adf_team'])){
							echo '<tr><td colspan="3">'.JText::_('BLBE_NOTEAMEXTRA').'</td></tr>';
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		</div>
		<div id="admrigh_cfg_div" class="tabdiv" style="display:none;overflow:hidden;">
			<div style="width:50%;float:left;">
				<table class="adminlist">
					<tr>
						<th colspan="2">
							<?php echo JText::_( 'BLBE_SEASADMRIGHTS' ); ?>
						</th>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMEDITPL");?></td>
						<td><input type="checkbox" name="jssa_editplayer" value="1" <?php echo ($lists['jssa_editplayer']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMDELPL");?></td>
						<td><input type="checkbox" name="jssa_deleteplayers" value="1" <?php echo ($lists['jssa_deleteplayers']=='1')?" checked":"";?> /></td>
					</tr>
				</table>
			</div>
			<div style="width:50%;float:left;">
				<table class="adminlist">
					<tr>
						<th colspan="2">
							<?php echo JText::_( 'BLBE_MODERRIGHTS' ); ?>
						</th>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_MODEREDITPLAYER");?></td>
						<td><input type="checkbox" name="moder_addplayer" value="1" <?php echo ($lists['moder_addplayer']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_TEAMPERACCOUNT");?></td>
						<td><input type="text" name="teams_per_account" value="<?php echo $lists['teams_per_account'];?>" /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_PLAYERSRPEACCOUNT");?></td>
						<td><input type="text" name="players_per_account" value="<?php echo $lists['players_per_account'];?>" /></td>
					</tr>
				</table>
			</div>
		</div>
		<div id="esport_cfg_div" class="tabdiv" style="display:none;">
		<table class="adminlist">
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_INVITEPL' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_INVITEPL' ); ?>::<?php echo JText::_( 'BLBE_TT_INVITEPL' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $lists['esport_invite_player']; ?>
				</td>
			
			</tr>

			<tr>
				<td>
					<?php echo JText::_( 'BLBE_INVITEPLUNREG' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_INVITEPLUNREG' ); ?>::<?php echo JText::_( 'BLBE_TT_INVITEPLUNREG' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="checkbox" name="esport_invite_unregister" value="1" <?php echo ($lists['esport_invite_unregister']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_PLAYERCANJOIN' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLAYERCANJOIN' ); ?>::<?php echo JText::_( 'BLBE_TT_PLAYERCANJOIN' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="checkbox" name="esport_join_team" value="1" <?php echo ($lists['esport_join_team']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_INVITEMATCH' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_INVITEMATCH' ); ?>::<?php echo JText::_( 'BLBE_TT_INVITEMATCH' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<input type="checkbox" name="esport_invite_match" value="1" <?php echo ($lists['esport_invite_match']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			
		</table>	
		</div>
		<div id="social_cfg_div" class="tabdiv" style="display:none;">
		<table class="adminlist">
			<tr>
				<th colspan="2">
					<?php echo JText::_( 'BLBE_UNBLBUTTONS' ); ?>
				</th>
			</tr>
			<tr>
				<td width="200">
					<?php echo JText::_( 'BLBE_TWITBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_twitter" value="1" <?php echo ($lists['jsb_twitter']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_GPLUSBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_gplus" value="1" <?php echo ($lists['jsb_gplus']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_FBSHAREBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_fbshare" value="1" <?php echo ($lists['jsb_fbshare']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_FBLIKEBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_fblike" value="1" <?php echo ($lists['jsb_fblike']=='1')?" checked":"";?> />
				</td>
			
			</tr>
		</table>
		<br />
		<table class="adminlist">
			<tr>
				<th colspan="2">
					<?php echo JText::_( 'BLBE_CHECKPAGES' ); ?>
				</th>
			</tr>
			<tr>
				<td width="200">
					<?php echo JText::_( 'BLBE_TABLE_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_season" value="1" <?php echo ($lists['jsbp_season']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_TEAM_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_team" value="1" <?php echo ($lists['jsbp_team']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_PLAYER_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_player" value="1" <?php echo ($lists['jsbp_player']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_MATCH_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_match" value="1" <?php echo ($lists['jsbp_match']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_VENUE_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_venue" value="1" <?php echo ($lists['jsbp_venue']=='1')?" checked":"";?> />
				</td>
			
			</tr>
		</table>
		</div>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="config" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>