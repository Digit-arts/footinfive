<?php

require_once ('fonctions_gestion_user.php');
require_once ('fonctions_module_reservation.php');
nettoyer_resa_non_payees();
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

if (test_non_vide($_POST["Siret"])) $Siret=$_POST["Siret"];
else $Siret=$_GET["Siret"];

if (test_non_vide($_POST["equipe"])) $equipe=$_POST["equipe"];
else $equipe=$_GET["equipe"];

if (test_non_vide($_POST["tel"])) $tel=$_POST["tel"];
else $tel=$_GET["tel"];

if (test_non_vide($_POST["vip"])) $vip=$_POST["vip"];
else $vip=$_GET["vip"];

if (test_non_vide($_POST["joueur_champ"])) $joueur_champ=$_POST["joueur_champ"];
else $joueur_champ=$_GET["joueur_champ"];

if (test_non_vide($_POST["police"])) $police=$_POST["police"];
else $police=$_GET["police"];

if (test_non_vide($_POST["contremarque"])) $contremarque=$_POST["contremarque"];
else $contremarque=$_GET["contremarque"];


if (test_non_vide($_POST["nom_entite"])) $nom_entite=$_POST["nom_entite"];
else $nom_entite=$_GET["nom_entite"];

if (test_non_vide($_POST["Type_Regroupement"]))
	$id_type_regroupement=$_POST["Type_Regroupement"];
else $id_type_regroupement=$_GET["Type_Regroupement"];

menu_acces_rapide("","Liste des clients");

?>
<FORM id="formulaire" name="filtre" class="submission box" action="index.php?option=com_content&view=article&id=57&Itemid=247" method="post" >
<center><table width="100%" border="0">
	<tr>
		<td><input name="id_client" type="text"  value="<? echo $id_client;?>" size="10" placeholder="Num client"></td>
		<!--td><input name="Siret" type="text"  value="<? echo $Siret;?>" size="10"  placeholder="Siret"></td-->
		<td><input name="equipe" type="text"  value="<? echo $equipe;?>" size="10"  placeholder="Equipe"></td>
		<td><input name="nom" type="text"  value="<? echo $nom;?>" size="10"  placeholder="Nom"></td>
		<td><input name="prenom" type="text"  value="<? echo $prenom;?>" size="10"  placeholder="Prenom"></td>
		<td align="right"><INPUT type="checkbox" name="vip" value="1" <? if (test_non_vide($vip)) echo "checked"; ?>>
			<img src="images/VIP-icon.png" title="VIP"/></td>
	</tr>
	<tr>
		<td><input name="tel" type="text"  value="<? echo $tel;?>" maxlenght="10" size="10"  placeholder="Tel"></td>
		<td><input name="email" type="text"  value="<? echo $email;?>" size="10"  placeholder="Email"></td>
		<td><input name="code_postal" type="text"  value="<? echo $code_postal;?>" size="2" maxlenght="5"  placeholder="CP"></td>
		<td ><input name="ville" type="text"  value="<? echo $ville;?>" size="10"  placeholder="Ville"></td>
		<td align="right">
		<INPUT type="checkbox" name="joueur_champ" value="1" <? if (test_non_vide($joueur_champ)) echo "checked"; ?>>
			<img src="images/coupe-icon.png" title="Joueur du championnat"/></td>
	</tr>
		<tr>
		<td><? menu_deroulant("Type_Regroupement",$id_type_regroupement,"enregistrer()"); ?></td>
		<td ><input name="nom_entite" type="text"  value="<? echo $nom_entite;?>" size="10"  placeholder="Soc/Assoc/Mairie"></td>
		
		<td colspan="3" align="right"> <INPUT type="checkbox" name="contremarque" value="1" <? if (test_non_vide($contremarque)) echo "checked"; ?>>
			<img src="images/contremarque-icon.png" title="Contremarque"/>&nbsp;&nbsp;
			<INPUT type="checkbox" name="police" value="1" <? if (test_non_vide($police)) echo "checked"; ?>>
			<img src="images/police-icon.png" title="Police"/>
		</td>
	</tr>
	<tr>
		<td colspan=5 align=center><input name="valide" type="button"  value="Filtrer" onclick="enregistrer()" /></td>
	</tr>
</table></center>
</form>
<br>
<?
$requete_recup_client="select c.*, tr.nom as nom_type,  "
	."  u.email as courriel , v.code_postal, v.nom_maj_ville, c.adresse as adresse, date_naissance "
	."From Type_Regroupement as tr , Client as c LEFT JOIN Ville as v ON c.code_insee=v.code_insee "
	." LEFT JOIN #__users as u on u.id=c.id_user where 1  and tr.id=c.id_type_regroupement ";
if (test_non_vide($email)) $requete_recup_client.=" and u.email like \"%".$email."%\"";
if (test_non_vide($id_client)) $requete_recup_client.=" and c.id_client=".$id_client;
if (test_non_vide($nom)) $requete_recup_client.=" and c.nom like \"%".$nom."%\"";
if (test_non_vide($prenom)) $requete_recup_client.=" and c.prenom like \"%".$prenom."%\"";
if (test_non_vide($code_postal)) $requete_recup_client.=" and v.code_postal like \"%".$code_postal."%\"";
if (test_non_vide($ville)) $requete_recup_client.=" and v.nom_maj_ville like \"%".mb_strtoupper($ville)."%\"   ";
if (test_non_vide($Siret)) $requete_recup_client.=" and c.Siret like \"%".$Siret."%\"   ";
if (test_non_vide($equipe)) $requete_recup_client.=" and c.equipe like \"%".$equipe."%\"   ";
if (test_non_vide($vip)) $requete_recup_client.=" and c.accompte_necessaire=1 ";
if (test_non_vide($police)) $requete_recup_client.=" and c.police=1 ";
if (test_non_vide($contremarque)) $requete_recup_client.=" and c.id_client in (SELECT id_client FROM `Contremarque` ) ";
if (test_non_vide($nom_entite)) $requete_recup_client.=" and (c.nom_entite like \"%".$nom_entite."%\" or c.nom_service like \"%".$nom_entite."%\") ";
if (test_non_vide($joueur_champ)) $requete_recup_client.=" and u.id in ".liste_users_ledg();
if (test_non_vide($tel)) {
	$requete_recup_client.=" and (mobile1 like \"%".$tel."%\" or mobile2 like \"%".$tel."%\"";
	$requete_recup_client.=" or mobile3 like \"%".$tel."%\" or mobile4 like \"%".$tel."%\" or c.fixe like \"%".$tel."%\" )  ";
}
if (test_non_vide($id_type_regroupement)) $requete_recup_client.=" and tr.id=".$id_type_regroupement."   ";

		
if (test_non_vide($_GET["tri_par"]))
	$requete_recup_client.=" order by ".$_GET["tri_par"];
else $requete_recup_client.=" order by date_modif desc, heure_modif desc";
						

	
$lien="<a href=\"index.php?vip=".$vip."&joueur_champ=".$joueur_champ."&police=".$police."&contremarque=".$contremarque
	."&nom=".$nom."&nom_entite=".$nom_entite."&prenom=".$prenom."&email=".$email."&code_postal=".$code_postal."&ville=".$ville."&Siret=".$Siret
	."&equipe=".$equipe."&Type_Regroupement=".$id_type_regroupement;
$requete_recup_client.=pagination($requete_recup_client,$lien."&tri_par=".$_GET["tri_par"]);
//echo $requete_recup_client;
$db->setQuery($requete_recup_client);
$db->query();

$resultat_recup_client = $db->loadObjectList();
?>
<table border="0" class="zebra" width="100%">
		<tr>
			<!--th>Réserver</th>
			<th>Résas</th-->
			<th ><? echo $lien."&tri_par=tr.nom,c.nom_entite\">";?>Type</a></th>
			<th><? echo $lien."&tri_par=c.equipe\">";?>Equipe</a></th>
			<th><? echo $lien."&tri_par=c.id_client\">";?>Num client</a></th>
			<th><? echo $lien."&tri_par=c.nom\">";?>Nom</a></th>
			<th>T&eacute;l&eacute;phone</th>
			<!--th >R&egrave;gl.</th>
			<th>Avoirs</th>	
			<th>Cautions</th>	
			<th>Email </th>
			<th>Date Naiss. </th>
			<th>Adresse </th>
			<th>Ville </th>
			<th>Code Postal</th-->
			

		</tr>				
			<?
			foreach($resultat_recup_client as $recup_client) {					
				echo "<tr>";
				// echo "<td nowrap><a href=\"index.php/component/content/article?id=62";
				// echo "&id_client=".$recup_client->id_client."\"/>";
				// echo "<img src=\"images/creer-resa.png\" title=\"réserver pour ce client\"></a> ";
				// echo "</td>";
				/*echo "<td>";
				if ($recup_client->diff>0) {
					echo "<a href=\"index.php?option=com_content&view=article&id=45&Itemid=276";
					echo "&id_client=".$recup_client->id_client."\"/>";
					echo "<img src=\"images/icon-creneau-reserver.png\" title=\"les réservations de ce client\" width=\"24\" height=\"24\"></a>";
				}
				echo "</td>";*/

				echo "<td>".$recup_client->nom_type." ".$recup_client->nom_entite;
				if (test_non_vide($recup_client->nom_service))
					echo "<br>".$recup_client->nom_service;
				echo "</td>";
				echo "<td>".$recup_client->equipe."</td>";
								
				echo "<td>".$recup_client->id_client;
				echo "</td>";
				echo "<td nowrap=nowrap> ";
				if ($recup_client->accompte_necessaire==1)
					echo "<img src=\"images/VIP-icon.png\" title=\"VIP\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				if ($recup_client->police==1)
					echo "<img src=\"images/police-icon.png\" title=\"Police\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				if (client_a_contremarque($recup_client->id_client))
					echo "<img src=\"images/contremarque-icon.png\" title=\"Contremarque\"  HEIGHT=\"12\" WIDTH=\"12\"  /> ";
				$info_ledg=existe_joueur_capitaine($recup_client->id_user);
				if (test_non_vide($info_ledg)){
					if (strcmp(substr($info_ledg,0,9),"capitaine")==0)
						echo "<img src=\"images/capitaine-icon.png\" title=\"".$info_ledg."\">";
					else echo "<img src=\"images/joueur-icon.png\" title=\"".$info_ledg."\">";
				}
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
				/*echo "<td nowrap><a href=\"index.php/component/content/article?id=80";
				echo "&id_client=".$recup_client->id_client."\"/>";
				if ($recup_client->solde<>"") echo $recup_client->solde." € </a></td>";
				echo "<td nowrap>";
				echo " <a href=\"index.php?option=com_content&view=article&id=65&Itemid=304";
				echo "&credit=1&id_client=".$recup_client->id_client."\">";
				if ($recup_client->credit_client=="") echo 0;
				echo $recup_client->credit_client." €";
				echo "</a>";
				echo "</td>";
				echo "<td nowrap>";
				echo " <a href=\"index.php?option=com_content&view=article&id=65&Itemid=304";
				echo "&credit=1&id_client=".$recup_client->id_client."\">";
				if ($recup_client->caution_client=="") echo 0;
				echo $recup_client->caution_client." €";
				echo "</a>";
				echo "</td>";*/
				// echo "<td>".$recup_client->courriel."</td>";
				// echo "<!--td nowrap>";
				// if ($recup_client->date_naissance<>"0000-00-00") echo inverser_date($recup_client->date_naissance);
				// echo "</td-->";
				// echo "<!--td>".$recup_client->adresse."</td>
				// echo "<td>".$recup_client->nom_maj_ville."</td-->";
				//echo "<td>".$recup_client->code_postal."</td>";
				echo "</tr>";
			}
		?>
</table>	
<?
}
}
?>	