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

	$row = $this->row;

	$lists = $this->lists;

	$s_id = $lists["s_id"];

	$Itemid = JRequest::getInt('Itemid');
?>
<?php
echo $lists["panel"];
?>
<!-- <module middle> -->
<div class="module-middle solid">
	
	<!-- <back box> -->
	<!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
	<!-- </back box> -->
	<!-- <title box> -->
		<div class="title-box padd-bot">
			<h2>
			</h2>
			
		</div>
		<!-- </div>title box> -->
	<!-- <tab box> -->
		
		<!-- </tab box> -->
	
</div>
<!-- </module middle> -->
<!-- <control bar> -->
<div class="control-bar-wr dotted">
	<ul class="control-bar">
		<li><a class="save" href="#" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('umatch_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
		<li><a class="apply" href="#" title="<?php echo JText::_('BLFA_APPLY')?>" onclick="javascript:submitbutton('umatch_apply');return false;"><?php echo JText::_('BLFA_APPLY')?></a></li>
		<li><a class="delete" href="<?php echo JRoute::_("index.php?option=com_joomsport&view=moderedit_umatchday&mid=".$row->m_id."&sid=".$s_id."&Itemid=".$Itemid)?>" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
	</ul>
</div>
<!-- </control bar> -->


<?php



		$editor =& JFactory::getEditor();

		JHTML::_('behavior.tooltip');

		?>

		<script language="javascript" type="text/javascript">

		function getObj(name) {

		  if (document.getElementById)  {  return document.getElementById(name);  }

		  else if (document.all)  {  return document.all[name];  }

		  else if (document.layers)  {  return document.layers[name];  }

		}

		</script>

		<script type="text/javascript">

		<!--

		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			
				if(pressbutton == 'match_apply'){
						form.isapply.value='1';
						pressbutton = 'match_save';
					}
					
					var regE = /[0-2][0-9]:[0-5][0-9]/;
					if(!document.adminForm.m_time.value.test(regE) && document.adminForm.m_time.value != ''){
						alert("<?php echo JText::_( 'BLBE_JSMDNOT7' ); ?>");return;
					}else{
					submitform( pressbutton );
					return;
					}
			
		}

		

		function bl_add_event(){

			var cur_event = getObj('event_id');

			

			//var e_count = getObj('e_count').value;

			var e_minutes = getObj('e_minutes').value;

			var e_player = getObj('playerz_id');
			var re_count = getObj('re_count').value;
			

			if (cur_event.value == 0) {

				alert("<?php echo JText::_('BLFA_JSMDNOT4');?>");return;

			}

			if (e_player.value == 0) {

				alert("<?php echo JText::_('BLFA_JSMDNOT5');?>");return;

			}

			

			var tbl_elem = getObj('new_events');

			var row = tbl_elem.insertRow(tbl_elem.rows.length);

			var cell1 = document.createElement("td");

			var cell2 = document.createElement("td");

			var cell3 = document.createElement("td");

			var cell4 = document.createElement("td");

			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			

			var input_hidden = document.createElement("input");

			input_hidden.type = "hidden";

			input_hidden.name = "em_id[]";

			input_hidden.value = 0;



			cell1.appendChild(input_hidden);

			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE')?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';

			

			var input_hidden = document.createElement("input");

			input_hidden.type = "hidden";

			input_hidden.name = "new_eventid[]";

			input_hidden.value = cur_event.value;

			cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;

			cell2.appendChild(input_hidden);

			

			

			var input_hidden = document.createElement("input");

			input_hidden.type = "text";

			input_hidden.name = "e_minuteval[]";

			input_hidden.value = e_minutes;

			//cell4.innerHTML = e_minutes;

			input_hidden.setAttribute("maxlength",5);

			input_hidden.setAttribute("size",5);



			cell4.appendChild(input_hidden);

			

			var input_player = document.createElement("input");

			input_player.type = "hidden";

			input_player.name = "new_player[]";

			input_player.value = e_player.value;

			if(e_player.value != 0){

				cell5.innerHTML = e_player.options[e_player.selectedIndex].text;

			}	

			cell5.appendChild(input_player);
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "e_countval[]";
			input_hidden.value = re_count;
			//cell4.innerHTML = e_minutes;
			input_hidden.setAttribute("maxlength",5);
			input_hidden.setAttribute("size",5);
			cell6.appendChild(input_hidden);
			

			row.appendChild(cell1);

			row.appendChild(cell2);

			row.appendChild(cell5);

			row.appendChild(cell4);
			row.appendChild(cell6);
			



			getObj('event_id').value =  0;

			getObj('playerz_id').value =  0;

			getObj('e_minutes').value = '';

		}

		function bl_add_tevent(){

			var cur_event = getObj('tevent_id');

			

			var e_count = getObj('et_count').value;

			var e_player = getObj('teamz_id');

			

			if (cur_event.value == 0) {

				alert("<?php echo JText::_('BLFA_JSMDNOT4');?>");return;

			}

			if (e_player.value == 0) {

				alert("<?php echo JText::_('BLFA_JSMDNOT6');?>");return;

			}

			

			var tbl_elem = getObj('new_tevents');

			var row = tbl_elem.insertRow(tbl_elem.rows.length);

			var cell1 = document.createElement("td");

			var cell2 = document.createElement("td");

			var cell3 = document.createElement("td");

			var cell4 = document.createElement("td");

			var cell5 = document.createElement("td");

			

			

			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php JText::_('BLFA_DELETE')?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';

			

			var input_hidden = document.createElement("input");

			input_hidden.type = "hidden";

			input_hidden.name = "new_teventid[]";

			input_hidden.value = cur_event.value;

			cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;

			cell2.appendChild(input_hidden);

			

			var input_hidden = document.createElement("input");

			input_hidden.type = "text";

			input_hidden.name = "et_countval[]";

			input_hidden.value = e_count;

			input_hidden.setAttribute("maxlength",5);

			input_hidden.setAttribute("size",5);

			

			//cell3.align = "center";

			//cell3.innerHTML = e_count;

			cell3.appendChild(input_hidden);

			

			

			var input_player = document.createElement("input");

			input_player.type = "hidden";

			input_player.name = "new_tplayer[]";

			input_player.value = e_player.value;

			if(e_player.value != 0){

				cell5.innerHTML = e_player.options[e_player.selectedIndex].text;

			}	

			cell5.appendChild(input_player);

			

			row.appendChild(cell1);

			row.appendChild(cell2);

			row.appendChild(cell5);

			row.appendChild(cell3);

		



			getObj('tevent_id').value =  0;

			getObj('teamz_id').value =  0;

			getObj('et_count').value = '';



		}

		

		function Delete_tbl_row(element) {

			var del_index = element.parentNode.parentNode.sectionRowIndex;

			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;

			element.parentNode.parentNode.parentNode.deleteRow(del_index);

		}
		function enblnp(){
			if(document.adminForm.new_points1.checked){
				getObj("newp1").removeAttribute('readonly');
				getObj("newp2").removeAttribute('readonly');
			}else{
				getObj("newp1").setAttribute('readonly','readonly');
				getObj("newp2").setAttribute('readonly','readonly');
			}
		}
		//-->

		</script>
		<div class="admin-mo-co">
		<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		

		<table class="season-list">

			<tr>

				<td width="100">

					<?php echo JText::_( 'BLFA_MATCHDAYNAME' ); ?>

				</td>

				<td>

					<?php echo $lists['mday'];?>

				</td>

			</tr>

			
			<?php
			if(count($lists['maps'])){
			?>
			
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_MAPS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ABOUTMATCH' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<table border="1" cellpadding="5"><tr>
						<th>Maps</th>
						<th><?php echo $lists['teams1'];?></th>
						<th><?php echo $lists['teams2'];?></th>
						</tr>
					<?php 
					for($i=0;$i<count($lists['maps']);$i++){
						echo "<tr>";
						echo "<td>".$lists['maps'][$i]->m_name."</td>";
						echo "<td><input type='text' name='t1map[]' size='5' value='".(isset($lists['maps'][$i]->m_score1)?$lists['maps'][$i]->m_score1:"")."' /></td>";
						echo "<td><input type='text' name='t2map[]' size='5' value='".(isset($lists['maps'][$i]->m_score2)?$lists['maps'][$i]->m_score2:"")."' /></td>";
						echo "<input type='hidden' name='mapid[]' value='".$lists['maps'][$i]->id."'/>";
						echo "</tr>";
					}
					?>
					</table>
				</td>
			</tr>
			<?php
			}
			?>
			
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_RESULTS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_RESULTS' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php 
					if($lists['t_type'] == 0){
						echo $lists['teams1'].' <input type="text" name="score1" value="'.$row->score1.'" size="5" maxlength="5" />&nbsp;:&nbsp;<input type="text" name="score2" value="'.$row->score2.'" size="5" maxlength="5" /> '.$lists['teams2'];
					}else{
						echo $lists['teams1'].' '.$row->score1."&nbsp;:&nbsp;".$row->score2.' '.$lists['teams2'];
					}
					?>
				</td>
			</tr>
			
			<tr>
				<td width="100">
					<?php echo JText::_( 'BL_TBL_POINTS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BL_TBL_POINTS' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php echo $lists['teams1'].' <input type="text" name="points1" value="'.floatval($row->points1).'" size="5" maxlength="5" id="newp1" '.(!$row->new_points?"readonly='readonly'":"").' />&nbsp;:&nbsp;<input type="text" name="points2" value="'.floatval($row->points2).'" size="5" maxlength="5" id="newp2" '.(!$row->new_points?"readonly='readonly'":"").' /> '.$lists['teams2'].'&nbsp;'.$lists['new_points'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_BONUS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_BONUS' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php echo $lists['teams1'].' <input type="text" name="bonus1" value="'.floatval($row->bonus1).'" size="5" maxlength="5" />&nbsp;:&nbsp;<input type="text" name="bonus2" value="'.floatval($row->bonus2).'" size="5" maxlength="5" /> '.$lists['teams2'];?>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<?php echo JText::_( 'BLFA_PLAYED' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_PLAYED' ); ?>::<?php echo JText::_( 'BLFA_TT_PLAYED' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>					
				</td>
				<Td>
					<?php echo $lists['m_played'];?>
				</Td>
			</tr>
			<tr>

				<td>

					<?php echo JText::_('BLFA_DATE');?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_DATE' ); ?>::<?php echo JText::_( 'BLFA_TT_DATE' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>					

					

				</td>

				<td>

					<?php

						echo JHTML::_('calendar', $row->m_date ? $row->m_date : date("Y-m-d"), 'm_date', 'm_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'10')); 

					?>

				</td>

			</tr>

			<tr>

				<td>

					<?php echo JText::_('BLFA_TIME');?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_TIME' ); ?>::<?php echo JText::_( 'BLFA_TT_TIME' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

					

				</td>

				<td>

					<input type="text" maxlength="5" class="feed-back inp-small" size="10" name="m_time" value="<?php echo substr($row->m_time,0,5);?>" />

					

				</td>

			</tr>

			<tr>

				<td>

					<?php echo JText::_('BLFA_LOCATION');?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_LOCATION' ); ?>::<?php echo JText::_( 'BLFA_TT_LOCATION' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

					

				</td>

				<td>

					<input type="text" maxlength="255" size="20" class="feed-back inp-big" name="m_location" value="<?php echo htmlspecialchars($row->m_location);?>" />

					

				</td>

			</tr>
			<?php //if($lists['unbl_venue']){?>
			<tr>
				<td>
					<?php echo JText::_('BLFA_VENUE');?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_VENUE' ); ?>::<?php echo JText::_( 'BLFA_TT_VENUE' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
					
				</td>
				<td>
					<?php echo $lists["venue"];?>
					
				</td>
			</tr>
			<?php //} ?>
			<?php
			for($p=0;$p<count($lists['ext_fields']);$p++){
			if($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)){
			}else{
			?>
			<tr>
				<td width="100">
					<?php echo $lists['ext_fields'][$p]->name;?>
				</td>
				<td>
					<?php
					
						switch($lists['ext_fields'][$p]->field_type){
								
							case '1':	echo $lists['ext_fields'][$p]->selvals;
										break;
							case '2':	echo $editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore', 'image') ) ;
										break;
							case '3':	echo $lists['ext_fields'][$p]->selvals;
										break;	
							case '0':					
							default:	echo '<input type="text" maxlength="255" class="feed-back inp-big" size="60" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):"").'" />';		
										break;
								
						}
					?>
					<input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->field_type?>" />
					<input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->id?>" />
				</td>
			</tr>
			<?php	
			}
			}
			?>
			<tr>

				<td width="100">

					<?php echo JText::_( 'BLFA_ABOUTMATCH' ); ?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ABOUTMATCH' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

				</td>

				<td>

					<?php echo $editor->display( 'match_descr',  htmlspecialchars($row->match_descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore', 'image') ) ;  ?>

				</td>

			</tr>

			

		</table>

		<br />

<table width="100%">

<tr>

	<td>

	<div>		

		<table class="adminlist" id="new_events">

			<tr>

				<th align="center" colspan="5" class="title">

					<?php echo JText::_('BLFA_PLAYEREVENTS');?>

				</th>

			</tr>

			<tr>

				<th class="title" width="2%">

					#

				</th>

				<th class="title" width="170">

					<?php echo JText::_( 'BLFA_PLAYEREVENT' ); ?>

				</th>

				<th>

					<?php echo JText::_( 'BLFA_PLAYERR' ); ?>

				</th>

				

				<th class="title" width="60">
					<?php echo JText::_( 'BLFA_MINUTES' ); ?>
				</th>
				<th width="60">
					<?php echo JText::_( 'BLFA_COUNT' ); ?>
				</th>

				

			</tr>

			<?php

			if(count($lists['m_events'])){

				foreach($lists['m_events'] as $m_events){

					echo "<tr>";

					echo '<td><input type="hidden" name="em_id[]" value="'.$m_events->id.'" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a></td>';

					echo '<td><input type="hidden" name="new_eventid[]" value="'.$m_events->e_id.'" />'.$m_events->e_name.'</td>';

					echo '<td><input type="hidden" name="new_player[]" value="'.$m_events->player_id.'" />'.$m_events->p_name.'</td>';

					echo '<td><input type="text" size="5" maxlenght="5" name="e_minuteval[]" value="'.$m_events->minutes.'" /></td>';
					echo '<td><input type="text" size="5" maxlength="5" name="e_countval[]" value="'.$m_events->ecount.'" /></td>';

					echo "</tr>";

				}

			}

			?>

		</table>

		<table class="adminlist">

			<tr>

				<th class="title" colspan="4" align="center">

					<?php echo JText::_('BLFA_ADDPLEVENTS');?>

				</td>

			</tr>

			

			<tr>

				<th class="title" width="260">

					<?php echo $lists['events'];?>

				</th>

				<th>

					<?php echo $lists['players'];?>

				</th>

				

				<th class="title" width="110">
					<input name="e_minutes" id="e_minutes" type="text" maxlength="5" size="5" />
					
				</th>
				<th>
					<input name="re_count" id="re_count" type="text" maxlength="5" size="5" />
					<input type="button" value="<?php echo JText::_('BLFA_ADD');?>" onClick="bl_add_event();" />
				</th>

				

			</tr>

		</table>

		<br />

	</div>

	

</td>

</tr>

</table>

	<div style="margin-top:10px;border:1px solid #BBB;">	

		<table style="padding:10px;" class="season-list">

			<tr>

				<td>

					<?php echo JText::_('BLFA_UPLPHTOMTCH');?>

				</td>

			</tr>

			<tr>

				<td>&nbsp;

				

				</td>

			</tr>

			<tr>

				<td>

				<input class="feed-back inp-small" type="file" name="player_photo_1" value="" />

				</td>

			</tr>

			<tr>

				<td>

				<input class="feed-back inp-small" type="file" name="player_photo_2" value="" />

				</td>

			</tr>

			<tr>
				<td>
					<button class="send-button" onclick="javascript:submitbutton('umatch_apply');" ><span><?php echo JText::_( 'BLFA_UPLOAD' ); ?></span></button>
				</td>

			</tr>

			<tr>

				<td>

					<?php echo JText::_('BLFA_ONEPHSEL');?>

				</td>

			</tr>



		</table>

		<?php

		if(count($lists['photos'])){

		?>

		<table class="adminlist">

			<tr>

				<th class="title" width="30"><?php echo JText::_('BLFA_DELETE');?></th>

				

				<th class="title" ><?php echo JText::_('BLFA_TITLE');?></th>

				<th class="title" width="250"><?php echo JText::_('BLFA_IMAGE');?></th>

			</tr>

			<?php

			foreach($lists['photos'] as $photos){

			?>

			<td align="center">

				<a href="javascript:void(0);" title="<?php echo JText::_('BLBE_REMOVE');?>" onClick="javascript:Delete_tbl_row(this);"><img src="<?php echo JURI::base();?>components/com_joomsport/img/ico/close.png" title="Remove" /></a>

				<input type="hidden" name="photos_id[]" value="<?php echo $photos->id;?>"/>

			</td>

			

			<td>

				<input type="text" maxlength="255" size="60" name="ph_names[]" value="<?php echo htmlspecialchars($photos->name)?>" />

			</td>

			<td align="center">

				<?php

				$imgsize = getimagesize('media/bearleague/'.$photos->filename);

				if($imgsize[0] > 200){

					$width = 200;

				}else{

					$width  = $imgsize[0];

				}

				?>

				<a rel="{handler: 'image'}" href="<?php echo JURI::base();?>media/bearleague/<?php echo $photos->filename?>" title="Image" class="modal-button"><img src="<?php echo JURI::base();?>media/bearleague/<?php echo $photos->filename?>" width="<?php echo $width;?>" /></a>

			</td>

			</tr>

			<?php

			}

			?>

		</table>



		<?php

		}

		?>

	</div>	

		

		<input type="hidden" name="task" value="" />

		<input type="hidden" name="id" value="<?php echo $row->id?>" />

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="tid" value="<?php echo $this->tid?>" />
		<input type="hidden" name="isapply" value="0" />

		<?php echo JHTML::_( 'form.token' ); ?>

		</form>
</div>
