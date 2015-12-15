<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');
?>
<script type="text/javascript">
	function Filtrer() {

		document.form_filtrer.submit()

	}
</script>


<?
$user =& JFactory::getUser();
$db = & JFactory::getDBO();


$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {

if (est_min_agent($user)){
	if (test_non_vide($_POST["id_client"])) $id_client=$_POST["id_client"];
	else $id_client=$_GET["id_client"];
} else $id_client=idclient_du_user();

$titre="Les r&egrave;glements";

if (test_non_vide($id_client))
	$titre.=" du client";

menu_acces_rapide($id_client,$titre);

if (test_non_vide($_POST["id_cotisation"])) $id_cotisation=$_POST["id_cotisation"];
else $id_cotisation=$_GET["id_cotisation"];

if (test_non_vide($_POST["Saison"])) $Saison=$_POST["Saison"];
else $Saison=$_GET["Saison"];

if (test_non_vide($_POST["nom"])) $nom=$_POST["nom"];
else $nom=$_GET["nom"];

if (test_non_vide($_POST["prenom"])) $prenom=$_POST["prenom"];
else $prenom=$_GET["prenom"];

if (test_non_vide($_POST["Moyen_paiement"])) $Moyen_paiement=$_POST["Moyen_paiement"];
else $Moyen_paiement=$_GET["Moyen_paiement"];

if (test_non_vide($_POST["indic_annul"])) $indic_annul=$_POST["indic_annul"];
else $indic_annul=$_GET["indic_annul"];

if (test_non_vide($_POST["indic_valid"])) $indic_valid=$_POST["indic_valid"];
else $indic_valid=$_GET["indic_valid"];

if (test_non_vide($_POST["remise"])) $remise=$_POST["remise"];
else $remise=$_GET["remise"];

if (test_non_vide($_POST["info"])) $info=$_POST["info"];
else $info=$_GET["info"];

if (test_non_vide($_POST["montant_reglement"])) $montant_reglement=$_POST["montant_reglement"];
else $montant_reglement=$_GET["montant_reglement"];

if (test_non_vide($_POST["date_fin"])) $date_fin=$_POST["date_fin"];
else $date_fin=$_GET["date_fin"];

if (test_non_vide($_POST["date_deb"])) $date_deb=$_POST["date_deb"];
else {
	$temp=$Saison.$id_cotisation.$id_client.$nom.$prenom.$Moyen_paiement.$indic_annul.$indic_valid.$remise;
	if (test_non_vide($_GET["date_deb"])) $date_deb=$_GET["date_deb"];
	else if ($temp=="") 
		header("Location: ../index.php/component/content/article?id=80&date_deb=".date("Y-m-d")."");
}


if (est_register($user)) $complement_req=" and reg1.`id_client`=(select id_client from Client where id_user=".$user->id.")";
else if (test_non_vide($id_client)) $complement_req=" and reg1.`id_client`=".$id_client;

$requete_liste_reg="select (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=reg1.id_user) as gid_user, s.nom as nom_saison, ";
$requete_liste_reg.=" (select name from #__users where id=reg1.id_user) as nom_user, ";
$requete_liste_reg.=" (select nom from Remise where id=reg1.id_remise) as la_remise, reg1.*,c.*, m.nom as moy_paie";
$requete_liste_reg.=" FROM  Client as c, Saison as s,Saison_cotisations as sc, Reglement as reg1 left join Moyen_paiement as m on reg1.id_moyen_paiement=m.id ";
$requete_liste_reg.=" where reg1.id_client=c.id_client and s.id=sc.id_saison and reg1.id_cotisation=sc.id ".$complement_req;

if (test_non_vide($date_deb)) {
	$requete_liste_reg.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(reg1.date_reglement,\" \",reg1.heure_reglement) AS CHAR(22)),";
	$requete_liste_reg.=" CAST(concat(\"".$date_deb."\",\" \",\"08:00:00\") AS CHAR(22)))<0 ";
	$requete_liste_reg.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(reg1.date_reglement,\" \",reg1.heure_reglement) AS CHAR(22)),CAST(concat(\"";
		
	if (test_non_vide($date_fin))
		$requete_liste_reg.=$date_fin;
	else
		$requete_liste_reg.=decaler_jour($date_deb,1);
		
	$requete_liste_reg.="\",\" \",\"05:00:00\") AS CHAR(22)))>0 ";
}

if (test_non_vide($indic_annul))
	$requete_liste_reg.=" and reg1.validation_reglement=".$indic_annul;
	
if (test_non_vide($indic_valid))
	$requete_liste_reg.=" and reg1.validation_reglement=".$indic_valid;
	
if (test_non_vide($remise))
	$requete_liste_reg.=" and reg1.id_remise is not null and reg1.id_remise>0 ";

if (test_non_vide($info))
	$requete_liste_reg.=" and reg1.info like \"%".$info."%\" ";

if (test_non_vide($montant_reglement))
	$requete_liste_reg.=" and reg1.montant_reglement=".$montant_reglement;
	
if (test_non_vide($id_cotisation))
	$requete_liste_reg.=" and reg1.id_reservation=".$id_cotisation;
	
if (test_non_vide($Saison))
	$requete_liste_reg.=" and s.id=".$Saison;

if (test_non_vide($nom)) $requete_liste_reg.=" and c.nom like \"%".$nom."%\"";
if (test_non_vide($prenom)) $requete_liste_reg.=" and c.prenom like \"%".$prenom."%\"";
if (test_non_vide($Moyen_paiement)) $requete_liste_reg.=" and reg1.id_moyen_paiement=".$Moyen_paiement;

if (test_non_vide($_GET["Remb"]))
	$requete_liste_reg.=" and reg1.montant_reglement<0 ";
	
if (test_non_vide($_GET["Paie"]))
	$requete_liste_reg.=" and reg1.montant_reglement>0 ";


$requete_liste_reg.=" order by reg1.date_reglement desc, reg1.heure_reglement desc";
//echo $requete_liste_reg;

	?>
	
	<FORM id="formulaire" name="form_filtrer" class="submission box" action="article?id=80" method="post" >

	<table  width="100%">
		<tr>
			<td><input name="nom" type="text"  value="<? echo $nom;?>" size="12"  placeholder="Nom"></td>
			<td><input name="prenom" type="text"  value="<? echo $prenom;?>" size="12" placeholder="Prenom"></td>
	<? 
			if (test_non_vide($id_client)){
				if (est_min_agent($user)) $compl_req=" id_client=".$id_client." ";
				else $compl_req=" id_user=".$user->id." ";
				
				$requete_recup_client="select id_client, prenom, nom, code_insee from Client where ".$compl_req." order by nom,prenom,code_insee";
				$db->setQuery($requete_recup_client);	
				$resultat_recup_client = $db->loadObjectList();
									
				foreach($resultat_recup_client as $recup_client){
					$nom=$recup_client->nom;
					$prenom=$recup_client->prenom;		
				}
				
			}			
			
			?>
			<td><? menu_deroulant("Saison",$Saison); ?></td>
		<?if (est_min_agent($user)){?>
			<td><input name="id_client" type="text"  value="<? echo $id_client;?>" size="7"  placeholder="Num client"></td>
			<td><INPUT type="checkbox" name="remise" value="1" <? if (test_non_vide($remise)) echo "checked"; ?>> remise</td>
			
		<?}?>
		</tr>
		<tr>
			<td nowrap><input type="date" name="date_deb" value="<? echo $date_deb;?>"></td>
			<td nowrap><input type="date" name="date_fin" value="<? echo $date_fin;?>"></td>
			<td nowrap><input type="text" name="info" value="<? echo $info;?>" size="8" placeholder="info">
			&nbsp;&nbsp;<input type="text" name="montant_reglement" size="3" value="<? echo $montant_reglement;?>"  placeholder="montant"></td>
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
			<td align="center" colspan="5">
				<input name="valide" type="button"  value="Filtre" onclick="Filtrer()">
			</td>
		</tr>
	</table>
	</FORM>
	<hr>
<?

$db->setQuery($requete_liste_reg);	
$resultat_liste_reg= $db->loadObjectList();
if (!$resultat_liste_reg) echo $prb;
else {

	echo "<table class=\"zebra\"><tr>";
	if (est_min_agent($user)) echo "<th>Num client</th><th>Client</th>";

	echo "<th>Montant vers&eacute;</th><th>Etat</th><th>info</th><th>Moyen de paiement</th><th>Saison</th>";
	echo "<th>Date reglement</th><th>Heure reglement</th><th>Effectu&eacute; par</th></tr>";
	$total_versements_page=0;
	$nbre_operations=0;
	foreach($resultat_liste_reg as $liste_reg){
		if  ($liste_reg->validation_reglement==1)
			$nbre_operations++;
			
		echo "<tr>";
		if (est_min_agent($user)) {
			echo "<td>".$liste_reg->id_client."</td>";
			echo "<td><a href=\"index.php/component/content/article?id=60&id_client=".$liste_reg->id_client."\"/>";
			echo $liste_reg->nom." ".$liste_reg->prenom;
			echo "</a></td>";
		}
		echo "<td>";
		//if (number_format($liste_reg->total_versement,2)<number_format($liste_reg->montant_total,2)) echo "<font color=red>";
		if  ($liste_reg->validation_reglement==1)
			$total_versements_page+=$liste_reg->montant_reglement;
		echo str_replace(".", ",",$liste_reg->montant_reglement)." &#8364;";
		//if (number_format($liste_reg->total_versement,2)<number_format($liste_reg->montant_total,2)) echo "</font>";
		echo "</td><td>";
		if  ($liste_reg->validation_reglement==0)
			echo " <img src=\"images/Cancel-resa.png\" title=\"reglement supprim&eacute;\">";
		if  ($liste_reg->validation_reglement==1)
			echo " <img src=\"images/reglement_valide.png\" title=\"reglement valid&eacute;e\">";
		echo "</td><td>";
			echo $liste_reg->info;
		echo "</td><td>";
			if  (test_non_vide($liste_reg->moy_paie)) echo $liste_reg->moy_paie;
			else echo $liste_reg->la_remise;
		echo "</td><td>";
			if  (test_non_vide($liste_reg->nom_saison)) echo $liste_reg->nom_saison;
		echo "</td><td>";
		echo date_longue($liste_reg->date_reglement)."</td><td>";
		echo $liste_reg->heure_reglement."</td>";
		echo "<td>".$liste_reg->nom_user;
		if (est_min_agent($liste_reg->gid_user)) echo " (FIF)";
		echo "</td></tr>";
	}
	echo "<tr>";
		echo "<th nowrap>NBRE : ".$nbre_operations."</th><th>Total vers.</th><th nowrap>".$total_versements_page." &#8364;</th><td colspan=6></th>";
	echo "</tr></table>";
}

}
?>