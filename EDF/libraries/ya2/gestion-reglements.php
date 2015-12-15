<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

?>	
<script type="text/javascript">
	
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
					else document.register.submit();
				}
			}
			else {
				if (lien!='') document.location.href=lien;
				else {
					document.register.Montant.value='';
					document.register.submit();
				}
			}
	}
	function Filtrer() {

		document.form_filtrer.submit()

	}
	
</script>

<?

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

if (test_non_vide($_POST["id_client"]))
	$id_client=$_POST["id_client"];
else $id_client=$_GET["id_client"];


if (test_non_vide($_POST["sc_id"]))
	$sc_id=$_POST["sc_id"];
else $sc_id=$_GET["sc_id"];


if (test_non_vide($_POST["Moyen_paiement"])) $Moyen_paiement=$_POST["Moyen_paiement"];
else $Moyen_paiement=$_GET["Moyen_paiement"];

if (test_non_vide($_POST["indic_annul"])) $indic_annul=$_POST["indic_annul"];
else $indic_annul=$_GET["indic_annul"];

if (test_non_vide($_POST["indic_valid"])) $indic_valid=$_POST["indic_valid"];
else $indic_valid=$_GET["indic_valid"];

if (test_non_vide($_POST["date_fin"])) $date_fin=$_POST["date_fin"];
else $date_fin=$_GET["date_fin"];

if (test_non_vide($_POST["date_deb"])) $date_deb=$_POST["date_deb"];
else $date_deb=$_GET["date_deb"];

if (test_non_vide($_GET["ok"]) and $_GET["ok"]==1) 
	echo "<font color=red>La transaction est valid&eacute;e.<br><br></font>";
else {
	if (test_non_vide($_GET["ok"]) and ($_GET["ok"]==0 or $_GET["ok"]=="")) 
		echo "<font color=red>La transaction a echou&eacute;e.<br><br></font>";
}

menu_acces_rapide($id_client,"Reglements du client : ".recup_1_element("nom","Saison_cotisations","id",$sc_id));
    
echo "<a href=\"index.php/component/content/article?id=61&id_saison=".recup_1_element("id_saison","Saison_cotisations","id",$sc_id)."\">Retour &agrave; la saison</a><br><br>";

    
if (est_min_agent($user) and test_non_vide($_GET["id_regl"])) {

	echo "<font color=red>";
	if (maj_validation_reglement(0,$_GET["id_regl"]))
		echo "reglement supprim&eacute;<br>";	
	else echo "Erreur : reglement inexistant";
	echo "</font>";
}


if ( !isset($_GET["premiere"]) and (!(test_non_vide($_POST["Montant"])) or str_replace(",", ".",$_POST["Montant"])==0) )
	$les_erreurs.="Le montant est obligatoire.<br>";
else {
	if ($_POST["type"]!=4 and $_POST["type"]!=2 and !isset($_GET["premiere"])  and !(test_non_vide($_POST["Moyen_paiement"])))
		$les_erreurs.="Le moyen de paiement est obligatoire.<br>";

	if (str_replace(",", ".",$_POST["Montant"])>0 and !test_non_vide($les_erreurs)){
		if ($_POST["Moyen_paiement"]==8){
				$id_regl=ajout_reglement($sc_id,$id_client,$maj_montant,$_POST["Moyen_paiement"],$_POST["info"],$_POST["Remise"],0);				
				//header("Location: http://footinfive.com/FIF/libraries/library/ya2/CMCIC_Paiement_3_0i/Phase1Aller.php?Montant=".str_replace(",",".",$_POST["Montant"])."&ref=T".$id_regl);
		}
		else {		
			if ($_POST["type"]==3 or $_POST["type"]==4 ) $maj_montant=(-1*str_replace(",", ".",$_POST["Montant"]));
			else $maj_montant=$_POST["Montant"];
			ajout_reglement($sc_id,$id_client,$maj_montant,$_POST["Moyen_paiement"],$_POST["info"],$_POST["Remise"]);

		}
	}
}
echo $les_erreurs;
if (!test_non_vide($id_client)){	?>
	
	<FORM id="formulaire" name="form_filtrer" class="submission box" action="article?id=81" method="post" >

	<table  width="100%>
		<tr>
			<td width="50" >Date debut</td>
			<td nowrap><input type="date" name="date_deb" value="<? echo $date_deb;?>"></td>
			<td width="50" >Date fin</td>
			<td nowrap><input type="date" name="date_fin" value="<? echo $date_fin;?>"></td>
			<td  colspan=2>Moyen Paiement</td>
			<td ><? menu_deroulant("Moyen_paiement",$Moyen_paiement); ?></td>
			<td>
				
			<INPUT type="checkbox" name="indic_annul" value="0" 
			<? if (test_non_vide($indic_annul)) echo "checked"; ?>>
				<img src="images/Cancel-resa.png" title="reglement supprim&eacute;e"/>
				<INPUT type="checkbox" name="indic_valid" value="1" 
			<? if (test_non_vide($indic_valid)) echo "checked"; ?>>
				<img src="images/reglement_valide.png" title="reglement valid&eacute;e"/>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="7">
				<input name="valide" type="button"  value="Filtrer" onclick="Filtrer()">
			</td>
		</tr>
	</table>
	</FORM>
	<hr>
<?
}
else {
echo "<table class=\"zebra\">";
	echo "<tr><th>Type</th><th>info</th>";
	if ($_POST["type"]==1 or !test_non_vide($_POST["type"])) echo "<th>Moyen de<br> paiement</th>";
	if ($_POST["type"]==2) echo "<th>Remise</th>";
	if ($_POST["type"]==3) echo "<th >Moyen de<br>remboursement</th>";
	echo "<th>Montant</th>";
	echo "</tr><tr>";
		?><form name="register" class="submission box" action="article?id=81" method="post"  >
		<?
		echo "<td>";
		echo "<select name=\"type\" onChange=\"recharger('','')\">";
			echo "<option value=1 ";
			if ($_POST["type"]==1) echo " selected ";
				echo ">Paiement</option>";
			echo "<option value=2 ";
			if ($_POST["type"]==2) echo " selected ";
			echo ">Remise</option>";
			echo "<option value=3 ";
			if ($_POST["type"]==3) echo " selected ";
				echo ">Remboursement</option>";
		echo "</select>";

		echo "</td><td>";
		echo "<input type=\"text\" name=\"info\" maxlength=\"20\" size=\"8\" value=\"\">";
		echo "</td>";
		if ($_POST["type"]==1 or $_POST["type"]==3 or !test_non_vide($_POST["type"])){
			if (!test_non_vide($_POST["type"]))
				$type_temp=1;
			else $type_temp=$_POST["type"];
			echo "<td>";
			menu_deroulant("Moyen_paiement","","",$type_temp);
			echo "</td>";
		}
		if ($_POST["type"]==2){
			echo "<td>";
			menu_deroulant("Remise",0);
			echo "</td>";
		}
		echo "<td nowrap>";
		echo "<input type=\"text\" name=\"Montant\" maxlength=\"11\" size=\"8\" value=\"";
		if  (test_non_vide($les_erreurs))
			echo $_POST["Montant"];
		echo "\">";
				
		echo "<input name=\"total_versement\" type=\"hidden\"  value=\"".$total_versement."\">";
		echo "<input name=\"id_client\" type=\"hidden\"  value=\"".$id_client."\">";
		echo "<input name=\"sc_id\" type=\"hidden\"  value=\"".$sc_id."\">";

		?><input name="valide" type="button" value="Payer" onclick="recharger('Confirmez ce paiement','')"><?

echo "</td></tr></form></table><br>";
}
$requete_liste_reg="Select m.nom as moy_paie, m.id as id_moy_paie, c.id_client as id_client, c.nom, c.prenom, "
	." (select rem.nom from Remise as rem where reg.id_remise=rem.id) as la_remise, "
	." reg.*, (select name from #__users where id=reg.id_user) as nom_user FROM Client as c, "
	." Reglement as reg left join Moyen_paiement as m on reg.id_moyen_paiement=m.id"
	."  where reg.id_client=c.id_client  ";
if (test_non_vide($id_client))
	$requete_liste_reg.=" and c.id_client=".$id_client;


if (test_non_vide($date_deb)) {
	$requete_liste_reg.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(reg.date_reglement,\" \",reg.heure_reglement) AS CHAR(22)),";
	$requete_liste_reg.=" CAST(concat(\"".$date_deb."\",\" \",\"08:00:00\") AS CHAR(22)))<0 ";
	$requete_liste_reg.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(reg.date_reglement,\" \",reg.heure_reglement) AS CHAR(22)),CAST(concat(\"";
		
	if (test_non_vide($date_fin))
		$requete_liste_reg.=$date_fin;
	else
		$requete_liste_reg.=decaler_jour($date_deb,1);
		
	$requete_liste_reg.="\",\" \",\"05:00:00\") AS CHAR(22)))>0 ";
}


if (test_non_vide($sc_id))
	$requete_liste_reg.=" and reg.id_cotisation=".$sc_id;

if (test_non_vide($indic_annul))
	$requete_liste_reg.=" and reg.validation_reglement=".$indic_annul;
	
if (test_non_vide($indic_valid))
	$requete_liste_reg.=" and reg.validation_reglement=".$indic_valid;


if (test_non_vide($_GET["Remb"]))
	$requete_liste_reg.=" and reg.montant_reglement<0 ";
	
if (test_non_vide($_GET["Paie"]))
	$requete_liste_reg.=" and reg.montant_reglement>0 ";
	
	
$requete_liste_reg.=" order by reg.date_reglement desc, reg.heure_reglement desc";
				
//echo $requete_liste_reg;
$db->setQuery($requete_liste_reg);
$db->query();
$nbre_reg=$db->getNumRows();


echo "<table class=\"zebra\" >";
echo "<tr><th>Effectuer<br>par</th><th>Date du paiement</th>";
if (!test_non_vide($id_client))
	echo "<th>Eleve</th>";
echo "<th>info</th><th>Type</th><th>Op</th><th>Montant</th><th>suppr</th></tr>";
				
if ($nbre_reg>0) {
					
	$resultat_liste_reg= $db->loadObjectList();
	$total_versement=0;
	foreach($resultat_liste_reg as $liste_reg){
		echo "<tr><td>";
		echo $liste_reg->nom_user;
		echo "</td>";
		echo "<td>".date_longue($liste_reg->date_reglement)." &agrave; ".$liste_reg->heure_reglement." </td>";
		if (!test_non_vide($id_client))
			echo "<td>".$liste_reg->nom." ".$liste_reg->prenom." </td>";
			
		echo "<td>";
		echo $liste_reg->info;
		echo "</td><td>";
		if (test_non_vide($liste_reg->moy_paie))
			echo "Paiement";
		else echo "Remise";
		echo "</td><td>";
		if (test_non_vide($liste_reg->moy_paie))
			echo $liste_reg->moy_paie;
		else echo $liste_reg->la_remise;
		echo "</td><td>";
		echo $liste_reg->montant_reglement."&euro; ";
		echo "</td><td>";
		if  ($liste_reg->validation_reglement==0) 
			echo " <img src=\"images/Cancel-resa.png\" title=\"reglement supprim&eacute;\">";
		else{
			if ($liste_reg->id_moyen_paiement<7 and $info_resa->indic_annul<>1)	{
				echo " <a onClick=\"recharger('Voulez-vous supprimer cette ligne de reglement ?'";
				echo ",'article?id=81&sc_id=".$sc_id."&id_client=".$id_client."&id_regl=".$liste_reg->id_reglement."')\">";
				echo "<img src=\"images/coin-delete-icon.png\" title=\"supprimer ce reglement\">";
			}
			if  ($liste_reg->validation_reglement<>0)
				$total_versement+=$liste_reg->montant_reglement;
		}
		echo "</td></tr>";
	}
	echo "<tr><td colspan=5 align=right>Montant total</td>";
	echo "<td><b>".$total_versement."&euro;</b></td><td>&nbsp;</td></tr>";
}
echo "</table><br>";

			
?>