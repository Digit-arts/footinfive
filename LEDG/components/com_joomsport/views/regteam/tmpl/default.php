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

JHTML::_('behavior.formvalidation');

$Itemid = JRequest::getInt('Itemid');
?>
<?php
echo $lists["panel"];
?>
<!-- <module middle> -->
	<div class="module-middle solid">
		
		<!-- <back box> -->
		<!--<div class="back dotted"><a href="#" title="Back">&larr; Back</a></div>-->
		<!-- </back box> -->
		
		<!-- <title box> -->
		<div class="title-box">
			<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
		</div>
		<!-- </div>title box> -->
		
	</div>
	<!-- </module middle> -->
<!-- <content module> -->
<div class="content-module">	
<?php if($this->lists["canmore"]){?>
<form method="POST" action="<?php echo 'index.php?option=com_joomsport&task=teamreg_save&Itemid='.$Itemid; ?>" enctype="multipart/form-data" id="regteam-form" class="form-validate" >
<table class="season-list" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><?php echo JText::_('BLFA_TEAMNAME');?> *</td>
		<td><input type="text" maxlength="255" class="feed-back inp-big required" size="60" name="t_name" value="" /></td>
	</tr>
	
	<tr>
		<td><?php echo JText::_('BL_TEAMCAP');?></td>
		<td><?php echo $this->lists["cap"]?></td>
	</tr>
	<?php
	for($i=0;$i<count($this->lists["adf"]);$i++){
		$adfs = $this->lists["adf"][$i];
		echo '<tr><td><input type="hidden" name="extra_id['.$adfs->id.']" value="'.$adfs->id.'" /><input type="hidden" name="extra_ftype['.$adfs->id.']" value="'.$adfs->field_type.'" />'.$adfs->name.($adfs->reg_require?' *':"").'</td>';
		echo '<td>';
		switch($adfs->field_type){
									
			case '1':	echo $adfs->selvals;
						break;
			case '2':	echo $this->editor->display( 'extraf['.$adfs->id.']',  htmlspecialchars(isset($adfs->fvalue_text)?($adfs->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore', 'image') ) ;
						break;
			case '3':	echo $adfs->selvals;
						break;	
			case '0':					
			default:	echo '<input type="text" class="feed-back inp-big'.($adfs->reg_require?' required':'').'" maxlength="255" size="60" name="extraf['.$adfs->id.']" value="" />';		
						break;
				
		}
		echo '</td></tr>';
	}
	?>
	<tr>
		<td class="dotted" colspan="2"><!-- --></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center"><button class="send-button validate button" type="submit" onClick="return document.formvalidator.isValid(document.id('regteam-form'));"><span><b><?php echo JText::_('BL_SEND');?></b></span></button></td>
	</tr>
	</table>
	<input type="hidden" name="id" value="0" />
</form>
<?php }else{ echo "<div>".JText::_('BLFA_TEAMLIMITIS')."</div>"; }?>
</div>
<!-- </content module> -->
