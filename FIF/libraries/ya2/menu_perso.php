<?php
require_once ('fonctions_gestion_user.php');

require_once ('fonctions_module_reservation.php');
// nettoyer_resa_non_payees();
?>

<script type="text/javascript">
	
	function enregistrer() {
		document.filtre.submit()
	}
</script>
<?php
$user =& JFactory::getUser();
$db = & JFactory::getDBO();


$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {

if (est_register($user)) 
	header("Location: index.php?option=com_content&view=article&id=39&Itemid=274");

else {
?>
<div class="module mod-box  deepest">
	<ul class="menu menu-sidebar"><li class="level1 item207 parent active">
		<span class="separator level1 parent active">
			<span>
				<span class="icon" style="background-image: url('http://footinfive.com/FIF/images/man-icon.png');">
					
				</span>
				Client
			</span>
		</span>
		<div style="display: block; height: 87px;">
			<ul class="level2">
				<li class="level2 item302">
					<a href="http://footinfive.com/FIF/" class="level2">
						<span>
							<span class="icon" style="background-image: url('http://footinfive.com/FIF/images/user-group-icon.png');">
							</span>
							Tous
						</span>
					</a>
				</li>
				<li class="level2 item304">
					<a href="/FIF/index.php?option=com_content&amp;view=article&amp;id=65&amp;Itemid=304" class="level2"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/credit-icon.png');"> </span>Crédit</span></a></li><li class="level2 item278"><a href="/FIF/index.php?option=com_content&amp;view=article&amp;id=56&amp;Itemid=278&amp;modif=1" class="level2"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/Add-Male-User-icon.png');"> </span>Créer</span></a></li></ul></div></li><li class="level1 item274 parent"><span class="separator level1 parent"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/icon-creneau-reserver.png');"> </span>RESA</span></span><div style="display: none; height: 0px;"><ul class="level2"><li class="level2 item276"><a href="/FIF/index.php?option=com_content&amp;view=article&amp;id=45&amp;ttes=1&amp;Itemid=276" class="level2"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/les-resas.png');"> </span>Toutes</span></a></li><li class="level2 item305"><a href="/FIF/index.php?option=com_content&amp;view=article&amp;id=45&amp;Itemid=305" class="level2"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/today-Calendar-icon.png');"> </span>Today</span></a></li><li class="level2 item301"><a href="/FIF/index.php?option=com_content&amp;view=article&amp;id=61&amp;Itemid=301" class="level2"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/coins-icon.png');"> </span>Règlements</span></a></li></ul></div></li><li class="level1 item306 parent"><span class="separator level1 parent"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/caisse-icon.png');"> </span>CAISSE</span></span><div style="display: none; height: 0px;"><ul class="level2"><li class="level2 item307"><a href="/FIF/index.php?option=com_content&amp;view=article&amp;id=70&amp;Itemid=307" class="level2"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/feuille-caisse-icon.png');"> </span>Today</span></a></li><li class="level2 item308"><a href="/FIF/index.php?option=com_content&amp;view=article&amp;id=71&amp;Itemid=308" class="level2"><span><span class="icon" style="background-image: url('http://footinfive.com/FIF/images/liste-feuille-caisse-icon.png');"> </span>Toutes</span></a></li></ul></div></li></ul>		
</div>	
<?
}
}
?>	
