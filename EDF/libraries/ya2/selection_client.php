<?php

require_once ('fonctions_gestion_user.php');
require_once ('fonctions_module_reservation.php');
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


maj_connect($user,$_SERVER["REMOTE_ADDR"]);

if (est_register($user)) 
	header("Location: index.php/component/content/article?id=62");

else {

if (test_non_vide($_POST["id_client"])) $id_client = $_POST["id_client"];
else $id_client = $_GET["id_client"];

if (test_non_vide($_POST["nom"])) $nom=$_POST["nom"];
else $nom=$_GET["nom"];
	
if (test_non_vide($_POST["prenom"])) $prenom=$_POST["prenom"];
else $prenom=$_GET["prenom"];

if (test_non_vide($_POST["email"])) $email=$_POST["email"];
else $email=$_GET["email"];

if (test_non_vide($_POST["code_postal"])) $code_postal=$_POST["code_postal"];
else $code_postal=$_GET["code_postal"];

if (test_non_vide($_POST["ville"])) $ville=$_POST["ville"];
else $ville=$_GET["ville"];

if (test_non_vide($_POST["sexe"])) $sexe=$_POST["sexe"];
else $sexe=$_GET["sexe"];

if (test_non_vide($_POST["statut"])) $statut=$_POST["statut"];
else $statut=$_GET["statut"];

if (test_non_vide($_POST["equipe"])) $equipe=$_POST["equipe"];
else $equipe=$_GET["equipe"];

if (test_non_vide($_POST["tel"])) $tel=$_POST["tel"];
else $tel=$_GET["tel"];

if (test_non_vide($_POST["vip"])) $vip=$_POST["vip"];
else $vip=$_GET["vip"];

if (test_non_vide($_POST["contremarque"])) $contremarque=$_POST["contremarque"];
else $contremarque=$_GET["contremarque"];


if (test_non_vide($_POST["nom_entite"])) $nom_entite=$_POST["nom_entite"];
else $nom_entite=$_GET["nom_entite"];


menu_acces_rapide("","Liste des clients");

?>
<FORM id="formulaire" name="filtre" class="submission box" action="index.php?option=com_content&view=article&id=57&Itemid=247" method="post" >
<center><table width="100%" border="0">
	<tr>
		<td><input name="id_client" type="text"  value="<? echo $id_client;?>" size="10" placeholder="Num client"></td>
		<td><input name="nom" type="text"  value="<? echo $nom;?>" size="10"  placeholder="Nom"></td>
		<td><input name="prenom" type="text"  value="<? echo $prenom;?>" size="10"  placeholder="Prenom"></td>
		<td ><input name="ville" type="text"  value="<? echo $ville;?>" size="10"  placeholder="Ville"></td>
		<td  align="right"><INPUT type="checkbox" name="vip" value="1" <? if (test_non_vide($vip)) echo "checked"; ?>>
			<img src="images/VIP-icon.png" title="VIP"/></td>
	</tr>
	<tr>
		<td><img src="images/eleve-edf-icon.png" title="Eleve"/>
			<INPUT type="checkbox" name="statut" value="1" <? if (test_non_vide($statut) and $statut==1) echo "checked"; ?>>
			<INPUT type="checkbox" name="statut" value="0" <? if (test_non_vide($statut) and $statut==0) echo "checked"; ?>>
			<img src="images/parents-eleve-edf-icon.png" title="Relation"/></td>
		<td><input name="code_postal" type="text"  value="<? echo $code_postal;?>" size="2" maxlenght="5"  placeholder="CP"></td>		
		<td><input name="email" type="text"  value="<? echo $email;?>" size="10"  placeholder="Email"></td>
		<td><input name="tel" type="text"  value="<? echo $tel;?>" maxlenght="10" size="10"  placeholder="Tel"></td>
		<td align="right"> 
			<img src="images/m-sexe-icon.png" title="gar&ccedil;on"/>
			<INPUT type="checkbox" name="sexe" value="1" <? if (test_non_vide($sexe) and $sexe==1) echo "checked"; ?>>
			<INPUT type="checkbox" name="sexe" value="0" <? if (test_non_vide($sexe) and $sexe==0) echo "checked"; ?>>
			<img src="images/f-sexe-icon.png" title="fille"/>
		</td>
	</tr>
	<tr>
		<td colspan=5 align=center><input name="valide" type="button"  value="Filtrer" onclick="enregistrer()" /></td>
	</tr>
</table></center>
</form>
<br>
<?
$requete_recup_client="select c.*, u.email as courriel , v.code_postal, v.nom_maj_ville, c.adresse as adresse, date_naissance "
	."From Client as c LEFT JOIN Ville as v ON c.code_insee=v.code_insee "
	." LEFT JOIN #__users as u on u.id=c.id_user where 1 ";
if (test_non_vide($email)) $requete_recup_client.=" and u.email like \"%".$email."%\"";
if (test_non_vide($id_client)) $requete_recup_client.=" and c.id_client=".$id_client;
if (test_non_vide($nom)) $requete_recup_client.=" and c.nom like \"%".$nom."%\"";
if (test_non_vide($prenom)) $requete_recup_client.=" and c.prenom like \"%".$prenom."%\"";
if (test_non_vide($code_postal)) $requete_recup_client.=" and v.code_postal like \"%".$code_postal."%\"";
if (test_non_vide($ville)) $requete_recup_client.=" and v.nom_maj_ville like \"%".mb_strtoupper($ville)."%\"   ";
if (test_non_vide($vip)) $requete_recup_client.=" and c.accompte_necessaire=1 ";
if (test_non_vide($sexe)) $requete_recup_client.=" and c.sexe=".$sexe." ";
if (test_non_vide($statut) and $statut==0) $requete_recup_client.=" and c.id_client in"
				."(SELECT `id_client_contact`  FROM `Relation_enfant_contacts` WHERE id_client_contact<>id_client_enfant) ";
if (test_non_vide($statut) and $statut==1) $requete_recup_client.=" and c.id_client in"
				."(SELECT `id_client_enfant`  FROM `Relation_enfant_contacts` ) ";
if (test_non_vide($tel)) {
	$requete_recup_client.=" and (mobile1 like \"%".$tel."%\" or mobile2 like \"%".$tel."%\"";
	$requete_recup_client.=" or mobile3 like \"%".$tel."%\" or mobile4 like \"%".$tel."%\" or c.fixe like \"%".$tel."%\" )  ";
}


		
if (test_non_vide($_GET["tri_par"]))
	$requete_recup_client.=" order by ".$_GET["tri_par"];
else $requete_recup_client.=" order by date_modif desc, heure_modif desc";
						

	
$lien="<a href=\"index.php?vip=".$vip."&sexe=".$sexe
	."&nom=".$nom."&prenom=".$prenom."&email=".$email."&code_postal=".$code_postal."&ville=".$ville
	."&Type_Regroupement=".$id_type_regroupement;
$requete_recup_client.=pagination($requete_recup_client,$lien."&tri_par=".$_GET["tri_par"]);
//echo $requete_recup_client;
$db->setQuery($requete_recup_client);
$db->query();

$resultat_recup_client = $db->loadObjectList();
?>
<table border="0" class="zebra" width="100%">
		<tr>
			<th><? echo $lien."&tri_par=c.id_client\">";?>Num client</a></th>
			<th><? echo $lien."&tri_par=c.nom\">";?>Nom</a></th>
			<th>T&eacute;l&eacute;phone</th>			

		</tr>				
			<?
			foreach($resultat_recup_client as $recup_client) {					
				echo "<tr>";
				echo "<td>".$recup_client->id_client;
				echo "</td>";
				echo "<td nowrap=nowrap> ";
				if ($recup_client->accompte_necessaire==1)
					echo "<img src=\"images/VIP-icon.png\" title=\"VIP\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				
				if (exist_id_client_eleve($recup_client->id_client))
					echo "<img src=\"images/eleve-edf-icon.png\" title=\"eleve\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				else echo "<img src=\"images/parents-eleve-edf-icon.png\" title=\"relation\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				
				if ($recup_client->sexe==1)
					echo " <img src=\"images/m-sexe-icon.png\" title=\"gar&ccedil;on\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				else echo " <img src=\"images/f-sexe-icon.png\" title=\"fille\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				
				echo "<a href=\"index.php/component/content/article?id=60";
				echo "&id_client=".$recup_client->id_client."\"/>".$recup_client->prenom." ";
				echo $recup_client->nom."</a>";
				$ligne_commentaire=recup_derniere_commentaire("id_client",$recup_client->id_client);
				if ($ligne_commentaire->Commentaire<>"" and est_min_agent($user)){
					echo " <a href=\"index.php/component/content/article?id=75&art=57&id_client=".$recup_client->id_client."\">";
					echo "<img src=\"images/Comment-icon.png\" title=\"".$ligne_commentaire->Commentaire."\"></a>";
				}
				echo "</td>";
				echo "<td nowrap>";
				if (test_non_vide($recup_client->mobile1))
					echo $recup_client->mobile1;
				else echo $recup_client->fixe;
				echo "</td>";

				echo "</tr>";
			}
		?>
</table>	
<?
}
}
?>	
