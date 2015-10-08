<?

if (isset($_GET["id_client"]) and (!isset($_GET["sortie"]))) {
require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();


$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {
	
if (test_non_vide($_POST["id_client"]))
	$id_client=$_POST["id_client"];
else $id_client=$_GET["id_client"];

$avoir_client=recup_credit_total_client($id_client);

if (test_non_vide($_POST["montant"]) and $avoir_client>=$_POST["montant"] and test_non_vide($_POST["nbre_contremarques"]) and test_non_vide($_POST["id_client"])
    and test_non_vide($_POST["date_limite"]) and test_non_vide($_POST["id_tarif"]) and test_non_vide($_POST["confirm_gen"])){

	for ($i=0;$i<$_POST["nbre_contremarques"];$i++){
		$la_clef="";
		while (clef_existe($la_clef)>0 or (!test_non_vide($la_clef)))
			$la_clef=gen_clef_contremarque();
	
		if (test_non_vide($la_clef)){
			$requete_insert_contremarque="INSERT INTO `Contremarque`(`Clef`, `id_client`, `date_limite`, `id_tarif`, `id_user`, `date_crea`, `heure_crea`) "
				." VALUES (\"".$_POST["id_tarif"].$la_clef."\",".$_POST["id_client"].",\"".$_POST["date_limite"]."\",".$_POST["id_tarif"]
				.",".$user->id.",\"".date("Y-m-d")."\",\"".Ajout_zero_si_absent(date("H:i"))."\")";
			//echo "<br><br>".$requete_insert_contremarque;
			$db->setQuery($requete_insert_contremarque);
			$resultat_insert_contremarque = $db->query();

		}
	}
	ajout_credit($id_client,(-1*$_POST["montant"]),1,"Achat_CM",2);
	$avoir_client=recup_credit_total_client($id_client);
	
}

menu_acces_rapide($id_client,"Gestion des contremarques");
?>
<script type="text/javascript">
	
	function enregistrer() {
		document.register_contremarque.submit()
	}
</script>
<?


if (est_min_agent($user)){


?>
<form name="register_contremarque" class="submission box" action="<?php echo JRoute::_( 'article?id=67&id_client='.$id_client.''); ?>" method="post"  >
			<?
			echo "<input name=\"id_client\" type=\"hidden\"  value=\"".$id_client."\">";
		?>
		<table border="0"  >
		<tr>
			<td>Client</td>
			<td align="right"><? 
			$compl_criteres=" and c.id_client=".$id_client." ";

			$resultat_recup_client = recup_recup_client($compl_criteres);
	
			echo "<b>".$resultat_recup_client->societe."</b> (".$resultat_recup_client->prenom." ".$resultat_recup_client->nom.")";
			
			?></td>
		</tr>
		<tr>
			<td>Avoir dispo</td>
			<td align="right"><? 
			echo format_fr($avoir_client);
			?></td>
		</tr>
		<?
		if ($avoir_client>0){
		?><tr>
			<td nowrap>Nbre de contremarques</td>
			<td align="right">
			<select name="nbre_contremarques" OnChange="enregistrer()">
				<option value=""></option>
				<?php
				if (est_min_manager($user))
					$max=1500;
				else $max=100;
				for ($i=1;$i<=$max;$i++){
					if (test_non_vide($_POST["nbre_contremarques"]) and $_POST["nbre_contremarques"]==$i)
						$select_nbre_contre=" SELECTED ";
					else $select_nbre_contre=" ";	
					echo "<option value=\"".$i."\" ".$select_nbre_contre.">".$i."</option>";	
				}
				?>
			</select>
			<td>
		</tr>
		<tr>
			<td>Heures</td>
			<td align="right">
			<select name="id_tarif" OnChange="enregistrer()">
				<option value=""></option>
				<option value="1" <? if (test_non_vide($_POST["id_tarif"]) and $_POST["id_tarif"]==1) echo " SELECTED ";?>>creuses</option>	
				<option value="2" <? if (test_non_vide($_POST["id_tarif"]) and $_POST["id_tarif"]==2) echo " SELECTED ";?>>pleines</option>
			</select>
			<td>
		</tr>
		<?
		if (test_non_vide($_POST["id_tarif"]) and test_non_vide($_POST["nbre_contremarques"])){
		?>
		<tr>
			<td>Montant total</td>
			<td align="right" nowrap>
			<?
			if ($_POST["id_tarif"]==1)
				$tarif=60;
			else $tarif=90;
			$montant_sans_remises=$_POST["nbre_contremarques"]*$tarif;
			$nbre_contremarques=$_POST["nbre_contremarques"];
			switch($nbre_contremarques){
				case ($nbre_contremarques<10) : $montant=$montant_sans_remises;break;
				case ($nbre_contremarques<25) : $montant=$_POST["nbre_contremarques"]*($tarif-10);break;
				case ($nbre_contremarques<50) : $montant=$_POST["nbre_contremarques"]*($tarif-20);break;
				case ($nbre_contremarques>49) : $montant=$_POST["nbre_contremarques"]*($tarif-30);break;
				default :  $montant=0;break;
			}
			echo "<input type=\"hidden\" name=\"montant\" value=\"".$montant."\">( Remise de ".format_fr($montant_sans_remises-$montant)." &euro; ) <b>".format_fr($montant)."&euro;</b>";
			?>
			<td>
		</tr>
		<?
		}
		?>
		<tr>
			<td width="50">Date limite</td>
			<td  align="right">
				<?
				if (est_min_manager($user)){?>
					<input type="date" name="date_limite" value="<? echo $_POST["date_limite"];?>" OnChange="enregistrer()">
				<?}
				else echo "<input type=\"hidden\" name=\"date_limite\" value=\"".decaler_jour(date("Y-m-d"),365)."\">"
					.date_longue(decaler_jour(date("Y-m-d"),365));
				?>
			</td>
		</tr>		<?
		}
		?>
		</table>
		<br><center>
		<?
		if ($avoir_client>=$montant and test_non_vide($_POST["id_tarif"])
		    and test_non_vide($_POST["date_limite"]) and test_non_vide($_POST["nbre_contremarques"])){
		?>
		<input type="checkbox" name="confirm_gen" value="1"> confirmer votre choix<br>
		<input name="valide" type="button" value="G&eacute;n&eacute;rer les contremarques" onclick="enregistrer()">
		<?
		}
if (test_non_vide($montant) and $avoir_client<$montant)
	echo "<font color=red>Avoir insuffisant</font><br>";
		?>
</center></form>
<?
}
$requete_contremarque_generees="SELECT count(c.`Clef`) as nbre_contremarque, c.*, (select name from #__users where id=c.id_user) as nom_user  FROM `Contremarque` as c "
		." WHERE c.`id_client`=".$id_client." group by c.date_crea, c.heure_crea, c.`id_tarif` order by c.date_crea desc, c.heure_crea desc";

	
//echo "<br>reqtrouve: ".$requete_contremarque_generees;
$db->setQuery($requete_contremarque_generees);	
$resultat_contremarque_generees = $db->loadObjectList();
?>
<br><hr><br><table class="zebra" border="0"  >
	<tr>
		<th>Date cr&eacute;ation</th>
		<th>Cr&eacute;er par</th>
		<th>Horaire</th>
		<th>Nombre<br>g&eacute;n&eacute;r&eacute;es</th>
		<th>Nombre<br>utilis&eacute;es</th>
		<th>Nombre<br>restantes</th>
		<th>Date limite</th>
		
	</tr>
<?
	foreach($resultat_contremarque_generees as $contremarque_generees){
		echo "<tr><td>".date_longue($contremarque_generees->date_crea)." &agrave; ".$contremarque_generees->heure_crea."</td>"
			."<td>".$contremarque_generees->nom_user."</td>"
			."<td>".recup_1_element("libelle_tarif","Tarif","id_tarif",$contremarque_generees->id_tarif)."</td>"
			."<td>";
		if (est_min_manager($user))
			echo "<a target=blank href=\"http://www.footinfive.com//FIF/libraries/ya2/contremarque.php?sortie=I&id_tarif="
			.$contremarque_generees->id_tarif."&date_limite=".$contremarque_generees->date_limite."&heure_crea=".$contremarque_generees->heure_crea
			."&date_crea=".$contremarque_generees->date_crea."&id_client=".$id_client."\" />";
		$nbre_contremarques_utlisees=nbre_contremarques_utlisees($contremarque_generees->id_tarif,$contremarque_generees->date_limite,$contremarque_generees->date_crea,$contremarque_generees->heure_crea,$id_client);
		echo $contremarque_generees->nbre_contremarque."</a></td>"
			."<td>".$nbre_contremarques_utlisees."</td><td>";
		if (est_min_manager($user))
			echo "<a target=blank href=\"http://www.footinfive.com//FIF/libraries/ya2/contremarque.php?sortie=I&id_tarif="
			.$contremarque_generees->id_tarif."&date_limite=".$contremarque_generees->date_limite."&heure_crea=".$contremarque_generees->heure_crea
			."&restantes=1&date_crea=".$contremarque_generees->date_crea."&id_client=".$id_client."\" />";
		echo ($contremarque_generees->nbre_contremarque-$nbre_contremarques_utlisees)."</td>"
			."<td>".date_longue($contremarque_generees->date_limite)."</td></tr>";
		
	}
echo "</table>";


}
}
if (isset($_GET["id_client"]) and isset($_GET["id_tarif"]) and isset($_GET["date_limite"]) and isset($_GET["heure_crea"])) {
	
set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries');
    require_once('../tcpdf/config/lang/fra.php');
    require_once('../tcpdf/tcpdf.php');
    require_once ('../ya2/fonctions_module_reservation.php');
    require_once ('../ya2/fonctions_gestion_user.php');
  
	$mysqli = new mysqli("localhost", "Cyclople", "MixMax123", "MySql_FIF");
    
	// Vérification de la connexion 
	if (mysqli_connect_errno()) {
	    printf("Échec de la connexion : %s\n", mysqli_connect_error());
	    exit();
	}
	$requete_liste_contremarque="SELECT cm.*, c.societe, (select libelle_tarif FROM Tarif Where `id_tarif`=".$_GET["id_tarif"].") as lib_tarif "
		."  FROM `Contremarque` as cm, Client as c "
		." WHERE cm.`id_client`=".$_GET["id_client"]." and cm.`id_client`=c.`id_client` and cm.`id_tarif`=".$_GET["id_tarif"]
		." and cm.`date_limite`=\"".$_GET["date_limite"]."\" and cm.`heure_crea`=\"".$_GET["heure_crea"]."\" and cm.`date_crea`=\"".$_GET["date_crea"]."\" ";
	if (isset($_GET["restantes"]))
	    $requete_liste_contremarque.=" and cm.Clef not in (SELECT r.`Clef` FROM `Reglement` as r WHERE validation_reglement=1 )";
		
	//echo "<br>reqtrouve: ".$requete_liste_contremarque;	
	$result = $mysqli->query($requete_liste_contremarque);

	$tab_page="<table  border=0 width=\"100%\" height=\"770\" >";
	$petite_police="1";
	$partie_pdf=$tab_page;
	$i=1;
	while ($row = $result->fetch_row()) {
		$partie_pdf.="<tr><td >"
			."<table cellpadding=\"5px\" style=\"border-radius:30px;border-style:solid;border-width:12px;border-color:#2D6174;\" width=\"100%\">"
			."<tr><td width=\"100%\"><font size=\"5px\"><b>COUPON CESSION 1 ".mb_strtoupper($row[8])."</b></font><br><br>"
			."Ce bon est valable jusqu'au <b>".inverser_date($row[2])."</b><br><br>"
			."Pour utiliser ce coupon, il faut nous contacter au <b>01 49 51 27 04</b> ou venir sur place. Pour valider votre r&eacute;servation, vous devez nous communiquer le num&eacute;ro de contremarque.<br>"
			."En cas d'annulation, vous avez jusqu'&agrave; 48 heures avant la date de r&eacute;servation, sinon votre contremarque ne sera plus valide."
			."<td align=\"right\"><img src=\"../../images/FIF.png\" title=\"FOOT IN FIVE\" width=\"60\" height=\"130\" ></td></tr>"
			."<tr><td width=\"100%\" colspan=\"2\" >"
			."<table width=\"100%\">"
			."<tr><td width=\"30%\"><table cellpadding=\"5px\" style=\"border-radius:30px;border-style:solid;border-width:5px;border-color:#557D8D;\">"
			."<tr><td align=\"center\"><font size=\"".$petite_police."\">FOOT IN FIVE<br>187 ROUTE DE SAINT LEU<br>"
			."93800 EPINAY SUR SEINE<br>Tel : 01 49 51 27 04<br>Mail : contact@footinfive.com<br>www.footinfive.fr</font></td></tr></table>"
			."</td><td width=\"2%\">&nbsp;</td><td  width=\"68%\">Num&eacute;ro contremarque : <b>".$row[0]."</b> (".$row[7].")<hr/>"
			."<font size=\"".$petite_police."\">Non remboursable. Valeur maximale : 1 cession d'une heure pour un terrain<br>";
		if (substr_count($row[8], 'creuse')>0)
			$partie_pdf.="Valable du lundi au vendredi de 10H &agrave; 18H (hors vacances et jours f&eacute;ri&eacute;s)";
		else	$partie_pdf.="Valable du lundi au vendredi de 18H &agrave; la fermeture et tous les week-end de 9h &agrave; la fermeture";
		
		$partie_pdf.="</font></td></tr></table></td></tr></table></td></tr><tr><td ><hr></td></tr>";
		
		if ($i%3==0)
			$partie_pdf.="</table><hr>".$tab_page."";
		$i++;
	}
	$partie_pdf.="</table>";
	echo $partie_pdf;
	
	//generer_pdf($partie_pdf,'Contremarques Air France','Contremarques Air France','Contremarques','Contremarques',$_GET["sortie"]);
	
}
?>