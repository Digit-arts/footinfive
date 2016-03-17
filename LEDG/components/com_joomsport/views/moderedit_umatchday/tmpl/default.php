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

	$row = $this->row;

	$lists = $this->lists;

	$s_id = $this->lists["s_id"];

	$match = $this->lists["match"];


$Itemid = JRequest::getInt('Itemid');
?>

<?php if($this->lists["msg"]){ ?>

<div class="message"><?php echo $this->lists["msg"];?></div>

<?php }?>
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
			<div class="select-wr">
				<form action="<?php echo "index.php?option=com_joomsport&task=moderedit_umatchday&Itemid=".$Itemid;?>" method="post" name="filtrForm">
					<div style="position:relative;"><span class='down'><!-- --></span><?php echo JText::_('BLFA_FILTERS')?> :<?php echo $lists['seas_filtr'];?></div>
					<?php if($lists['md_filtr']) {?>
					<div style="position:relative;text-align:right;"><span class='down'><!-- --></span><?php echo $lists['md_filtr'];?></div>
					<?php } ?>
				</form>
			</div>
		</div>
		<!-- </div>title box> -->
	<!-- <tab box> -->
		<ul class="tab-box">
			<li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid);?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" class="star" /><?php echo JText::_('BLFA_PLAYER')?></span></a></li>
			<li  class="active"><a href="#" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_MATCHDAY')?></span></a></li>

			
		</ul>
		<!-- </tab box> -->
	
</div>
<!-- </module middle> -->
<!-- <control bar> -->
<?php 	if($this->lists["t_type"] != 1){?>
<div class="control-bar-wr dotted">
	
	<ul class="control-bar">
		<li><a class="save" href="#" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('matchday_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
	</ul>
	
</div>
<?php }?>
<!-- </control bar> -->



<?php



		JHTML::_('behavior.tooltip');

		

		?>

		<script type="text/javascript">

		<!--

		function getObj(name) {

		  if (document.getElementById)  {  return document.getElementById(name);  }

		  else if (document.all)  {  return document.all[name];  }

		  else if (document.layers)  {  return document.layers[name];  }

		}

		function submitbutton(pressbutton) {

			var form = document.adminForm;

			if (pressbutton == 'matchday_save' || pressbutton == 'matchday_apply') {

			
					if(pressbutton == 'matchday_apply'){
						form.isapply.value='1';
						pressbutton = 'matchday_save';
					}
					var errortime = '';
					var mt_time = eval("document.adminForm['match_time[]']");
					if(mt_time){
						if(mt_time.length){
							for(i=0;i<mt_time.length;i++){
								var regE = /[0-2][0-9]:[0-5][0-9]/;
								if(!mt_time[i].value.test(regE) && mt_time[i].value != ''){
									errortime = '1';
									mt_time[i].style.border = "1px solid red";
								}else{
									mt_time[i].style.border = "1px solid #C0C0C0";
								}
							}
						}else{
							var regE = /[0-2][0-9]:[0-5][0-9]/;
								if(!mt_time.value.test(regE) && mt_time.value != ''){
									errortime = '1';
									mt_time.style.border = "1px solid red";
								}else{
									mt_time.style.border = "1px solid #C0C0C0";
								}
						}
					}
					
					if(errortime){
						alert("<?php echo JText::_( 'BLBE_JSMDNOT7' ); ?>");return;
					}else{
						submitform( pressbutton );
						return;
					}

				

			}else{

				submitform( pressbutton );

					return;

			}

		}	

		

		function bl_add_match(){

			var team1 = getObj('teams1');

			var team2 = getObj('teams2');

			var score1 = getObj('add_score1').value;

			var score2 = getObj('add_score2').value;
			//var tm_played = getObj('tm_played').checked;
			

			
			if (team1.value == 0 || team2.value == 0) {

				alert("<?php echo JText::_('BLFA_JSMDNOT1')?>");return;

			}
			//if (((score1) == '' || (score2) == '') && tm_played){
				//alert("Please enter text to the field.");return;
			//}

			if ( team1.value == team2.value){ 

				alert("<?php echo JText::_('BLFA_JSMDNOT2')?>");return;

			}
			
			var regE = /[0-2][0-9]:[0-5][0-9]/;
			if(!getObj('match_time_new').value.test(regE) && getObj('match_time_new').value != ''){
				alert("<?php echo JText::_('BLFA_JSMDNOT7')?>");return;
			}

			var tbl_elem = getObj('new_matches');

			var row = tbl_elem.insertRow(tbl_elem.rows.length);

			var cell1 = document.createElement("td");

			var cell2 = document.createElement("td");

			var cell3 = document.createElement("td");

			var cell4 = document.createElement("td");

			var cell5 = document.createElement("td");

			var cell6 = document.createElement("td");

			

			var input_hidden = document.createElement("input");

			input_hidden.type = "hidden";

			input_hidden.name = "match_id[]";

			input_hidden.value = 0;

			cell1.appendChild(input_hidden);

			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE');?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';

			cell1.setAttribute("rowspan",2);

			var input_hidden = document.createElement("input");

			input_hidden.type = "hidden";

			input_hidden.name = "home_team[]";

			input_hidden.value = team1.value;

			cell2.innerHTML = team1.options[team1.selectedIndex].text;

			cell2.appendChild(input_hidden);

			

			var input_hidden = document.createElement("input");

			input_hidden.type = "text";

			input_hidden.name = "home_score[]";

			input_hidden.value = score1;

			input_hidden.size = 3;

			input_hidden.setAttribute("maxlength",5);

			cell3.align = "center";

			//cell3.innerHTML = score1 + ' : ' + score2;

			cell3.appendChild(input_hidden);

			var txtnode = document.createTextNode(" : ");

			cell3.appendChild(txtnode);

			var input_hidden = document.createElement("input");

			input_hidden.type = "text";

			input_hidden.name = "away_score[]";

			input_hidden.value = score2;

			input_hidden.size = 3;

			input_hidden.setAttribute("maxlength",5);



			cell3.appendChild(input_hidden);

			var input_hidden = document.createElement("input");

			input_hidden.type = "hidden";

			input_hidden.name = "away_team[]";

			input_hidden.value = team2.value;

			cell4.innerHTML = team2.options[team2.selectedIndex].text;

			cell4.appendChild(input_hidden);

			cell5.innerHTML = '';

			cell5.setAttribute("rowspan",2);

			

			////-------------new---------------////

			

			var cell7 = document.createElement("td");

			var cell8 = document.createElement("td");

			

			var input_hidden = document.createElement("input");



			input_hidden.type = "text";



			input_hidden.name = "match_data[]";



			input_hidden.value = getObj('tm_date').value;



			input_hidden.size = 10;



			input_hidden.setAttribute("maxlength",10);

			

			cell6.appendChild(input_hidden);



			cell6.align = "left";

			

			

			var input_hidden = document.createElement("input");



			input_hidden.type = "text";



			input_hidden.name = "match_time[]";



			input_hidden.value = getObj('match_time_new').value;



			input_hidden.size = 5;



			input_hidden.setAttribute("maxlength",5);

			

			cell7.appendChild(input_hidden);

			

			

			cell7.align = "left";

			

			

			/*var input_hidden = document.createElement("input");



			input_hidden.type = "checkbox";



			input_hidden.name = "match_played[]";



			if(getObj('tm_played').checked){



				input_hidden.checked = true;



				input_hidden.value = '1';



			}else{



				input_hidden.value = '0';



			}

			cell8.appendChild(input_hidden);*/

			

			////------------/new---------------////

			

			row.appendChild(cell1);

			row.appendChild(cell2);

			row.appendChild(cell3);

			

			row.appendChild(cell4);

			row.appendChild(cell5);

			var row2 = tbl_elem.insertRow(tbl_elem.rows.length);

			row2.appendChild(cell6);

			

			row2.appendChild(cell8);

			row2.appendChild(cell7);

			
			var row3 = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell_f = document.createElement("td");
			cell_f.setAttribute("colspan",5);
			row3.appendChild(cell_f);

			getObj('teams1').value =  0;

			getObj('teams2').value = 0;

			getObj('add_score1').value = '';

			getObj('add_score2').value = '';


		}

		

		function Delete_tbl_row(element) {

			var del_index = element.parentNode.parentNode.sectionRowIndex;

			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;

			element.parentNode.parentNode.parentNode.deleteRow(del_index+1);
			element.parentNode.parentNode.parentNode.deleteRow(del_index+1);
			element.parentNode.parentNode.parentNode.deleteRow(del_index);

		}

		//-->

		</script>
		
		<div class="admin-mo-co">
		<form action="" method="post" name="adminForm" id="adminForm">

		

		<br />

		<table class="adminlist" id="new_matches" border="0">

			<tr>

				<th class="title" style="padding-left:250px;" colspan="8">

					<?php echo JText::_('BLFA_MATCHRESULTS');?>

				</th>

			</tr>

			<tr>

				<th class="title" width="20">

					#

				</th>

				<th class="title" width="170">

					<?php echo JText::_( 'BLFA_HOMETEAM' ); ?>



				</th>

				<th width="140">

					<?php echo JText::_( 'BLFA_SCORE' ); ?>

				</th>

				

				<th class="title" width="170">

					<?php echo JText::_( 'BLFA_AWAYTEAM' ); ?>

				</th>

				

				<th class="title">

					<?php echo JText::_( 'BLFA_MATCHDETAILS' ); ?>

				<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_MATCHDETAILS' ); ?>::<?php echo JText::_( 'BLFA_TT_MATCHDETAILS' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span> 

				</th>

			</tr>

			<?php

			if(count($match)){

				foreach($match as $curmatch){

					$match_link = 'index.php?option=com_joomsport&amp;task=moderedit_umatch&amp;cid[]='.$curmatch->id."&Itemid=".$Itemid;

					echo "<tr>";

					echo '<td rowspan="2">';
					if($this->lists["t_type"] != 1){
						echo '<input type="hidden" name="match_id[]" value="'.$curmatch->id.'" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';
					}else{
						echo '<input type="hidden" name="match_id[]" value="'.$curmatch->id.'" />';
					
					}
					echo '</td>';
					echo '<td><input type="hidden" name="home_team[]" value="'.$curmatch->team1_id.'" />'.$curmatch->home.'</td>';
					if($this->lists["t_type"] == 1){
						echo '<td align="center"><input disabled="true" type="text" readonly="true" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" /> : <input disabled="true" type="text" readonly="true" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" /></td>';
					}else{
						echo '<td align="center"><input type="text" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" /> : <input type="text" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" /></td>';
					}
					echo '<td><input type="hidden" name="away_team[]" value="'.$curmatch->team2_id.'" />'.$curmatch->away.'</td>';

					echo '<td rowspan=2><a href="'.$match_link.'">'.JText::_( 'BLFA_MATCHDETAILS' ).'</a></td>';

					

					echo '</tr>';

					echo '<tr>';

					

					echo '<td>'.JText::_( 'BLFA_DATEE' ).'';

						echo JHTML::_('calendar', $curmatch->m_date, 'match_data[]', 'match_data_'.$curmatch->id, '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10')); 

					

					echo '</td>';	

					//echo '<td>'.JText::_( 'BLFA_ISPLAYED' ).'<input type="checkbox" name="match_played[]" value="'.($curmatch->m_played?1:0).'" '.($curmatch->m_played?"checked":"").' /></td>';					
					echo "<td>&nbsp;</td>";
						

					echo '<td>'.JText::_( 'BLFA_TIMEE' ).'<input type="text" name="match_time[]" maxlength="5" size="12" value="'.substr($curmatch->m_time,0,5).'" />';

					echo "</tr>";

					echo '<tr><td colspan="5"></td></tr>';

				}

			}

			?>

		</table>
		<?php 	if($this->lists["t_type"] != 1){?>
		<table class="adminlist">

			<tr >

				<th  class="title" colspan="3" style=" padding-left:200px;">

					<?php echo JText::_('BLFA_ADDMATCHRESULTS');?>

				</th>

			</tr>



			

			<tr>

				<th width="200">

					<?php echo $lists['teams1']?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_HOMETEAM' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span> 

				</th>
				<th width="140">
					<input name="add_score1" id="add_score1" type="text" maxlength="5" size="5" />:
					<input name="add_score2" id="add_score2" type="text" maxlength="5" size="5" />
				</th>
				<th>	                   
					 <?php echo $lists['teams2']?>
					 <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_AWAYTEAM' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span> 
				</th>
			</tr>
			<tr>
				
				<th>
					<?php
						echo JText::_( 'BLFA_DATEE' );
						echo JHTML::_('calendar', date("Y-m-d"), 'tm_date', 'tm_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10')); 

					?>

				</th>
				<th>	
					
				</th>
				<th>
					<?php echo JText::_( 'BLFA_TIMEE' );?>
					<input type="text" name="match_time_new" id="match_time_new" maxlength="5" size="12" value="00:00" />
					
				</th>

				

				

			</tr>

		</table>
		<div style="float:right;"><button class="send-button" onClick="bl_add_match();return false;" ><span><b><?php echo JText::_( 'BLFA_ADD' ); ?></b></span></button></div>
		<?php } ?>
		

		<input type="hidden" name="return_sh" value="0" />

		<input type="hidden" name="task" value="admin_matchday" />

		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		
	
		<input type="hidden" name="sid" value="<?php echo $this->s_id?>" />
	

		<input type="hidden" name="boxchecked" value="0" />

		<input type="hidden" name="isapply" value="0" />


		<?php echo JHTML::_( 'form.token' ); ?>

		</form>
	</div>
