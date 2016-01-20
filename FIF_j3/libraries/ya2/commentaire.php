<?

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
	
	
if (test_non_vide($_GET["id_resa"])) {
	$table="Reservation";
	$champ="id_resa";
	$valeur_champ=$_GET["id_resa"];
	$art=$_GET["art"];
	$var="&id_resa=".$_GET["id_resa"];
}
if (test_non_vide($_GET["id_client"])) {
	$table="Client";
	$champ="id_client";
	$valeur_champ=$_GET["id_client"];
	$art=$_GET["art"];
	$var="&id_client=".$_GET["id_client"];
}

if(!isset($champ) &&isset($_POST["champ"])) {
	$table=$_POST["table"];
	$champ=$_POST["champ"];
	$valeur_champ=$_POST["valeur_champ"];
	$art=$_POST["art"];
	$var=$_POST["var"];
}

if (test_non_vide($table) and test_non_vide($champ) and test_non_vide($valeur_champ)){
	$requete_recup_infos="select *, t.* from Commentaires as c, ".$table." as t where  c.".$champ."=t.".$champ." and "
		."c.".$champ."=".$valeur_champ." order by date desc, heure desc LIMIT 0,1 ";
	//echo $requete_recup_infos;
	$db->setQuery($requete_recup_infos);
	$resultat_recup_infos = $db->LoadObject();
}
if (test_non_vide($_POST["table"]) and test_non_vide($_POST["champ"]) and test_non_vide($_POST["valeur_champ"])){

	maj_commentaire($_POST["champ"],$_POST["valeur_champ"],$_POST["commentaire"]);	
	header("Location: article?id=".$_POST["art"]."".$_POST["var"]."");

}
?>
<script type="text/javascript">
	
	function enregistrer() {
		document.register_comment.submit()
	}
</script>

<form name="register_comment" class="submission box" action="article?id=75" method="post"  >
			<?
			echo "<input name=\"table\" type=\"hidden\"  value=\"".$table."\">";
			echo "<input name=\"champ\" type=\"hidden\"  value=\"".$champ."\">";
			echo "<input name=\"valeur_champ\" type=\"hidden\"  value=\"".$valeur_champ."\">";
			echo "<input name=\"art\" type=\"hidden\"  value=\"".$art."\">";
			echo "<input name=\"var\" type=\"hidden\"  value=\"".$var."\">";
		?>
		<table class="zebra" border="0"  >
		<tr>
			<th><? echo $table; ?></th>
			<td align="left"><? 
		
			if ($table=="Client") echo $resultat_recup_infos->prenom." ".$resultat_recup_infos->nom;
			 else echo $resultat_recup_infos->id_resa;
			?></td>
		</tr>
		<tr>
			<th>Commentaire</th>
			<td align="left">
			<textarea rows="4" cols="100" name="commentaire"><?php echo $resultat_recup_infos->Commentaire;?></textarea>
			<td>
		</tr>
		</table>
<input name="valide" type="button" value="Enregistrer" onclick="enregistrer()">
</form>
<?
$requete_recup_hist_com="select * from Commentaires as c, #__users as u where c.id_user=u.id and ".$champ."=".$valeur_champ." order by date desc, heure desc ";
	//echo $requete_recup_hist_com;
	$db->setQuery($requete_recup_hist_com);
	$resultat_recup_hist_com = $db->LoadObjectList();?>
	<hr>
	<h2>historique</h2>
	<br>
		<table class="zebra" border="0"  >
		<tr>
			<th>Effectu&eacute; par</th><th>date</th><th>heure</th><th>Commentaire</th>
		</tr>
		<?
		foreach($resultat_recup_hist_com as $les_resultats){
		?><tr>
			<td><?php echo $les_resultats->name;?></td>
			<td><?php echo date_longue($les_resultats->date);?></td>
			<td><?php echo $les_resultats->heure;?></td>
			<td><?php echo $les_resultats->Commentaire;?></td>
		</tr>
		<?
		}
		?>
	</table>
	<?
}
?>