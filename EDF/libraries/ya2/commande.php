<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

?>	
<script type="text/javascript">
	function valider() {
		document.filtrer.submit();

	}
	function enregistrer() {
		document.ajout_commande.submit();

	}

	
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
	
</script>

<?

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

maj_connect($user,$_SERVER["REMOTE_ADDR"]);


if (est_min_agent($user)){
	if (test_non_vide($_POST["id_client"])) $id_client=$_POST["id_client"];
	else $id_client=$_GET["id_client"];
} else $id_client=idclient_du_user();

$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {


if (test_non_vide($_POST["id_commande"])) $id_commande=$_POST["id_commande"];
else $id_commande=$_GET["id_commande"];

if (test_non_vide($_POST["date_deb"])) $date_deb=$_POST["date_deb"];
else {
	$temp=$id_saison.$id_client.$nom.$prenom.$_GET["ttes"].$indic_annul;
	if (test_non_vide($_GET["date_deb"])) $date_deb=$_GET["date_deb"];
	//else if ($temp=="") header("Location: ../index.php/component/content/article?id=85&date_deb=".date("Y-m-d")."");
}



if (test_non_vide($_GET["suppr_commande"]))
	suppr_commande($_GET["suppr_commande"]);

if (test_non_vide($_POST["nom_commande"]) and test_non_vide($_POST["nom_fournisseur"])){
	
	if (!test_non_vide($_POST["id_commande"]))
		$c_id=ajout_commande($_POST["nom_commande"],$_POST["nom_fournisseur"]);
	else $c_id=maj_commande($_POST["nom_commande"],$_POST["nom_fournisseur"],$_POST["id_commande"]);
	header("Location: ../../../index.php/component/content/article?id=84&c_id=".$c_id."");
}

if (test_non_vide($_GET["ttes"]))
	$titre="Toutes les commandes";

if (test_non_vide($id_client))
	$titre="Les commandes du client";

menu_acces_rapide($id_client,$titre);

if (test_non_vide($_GET["ajout_commande"]) or test_non_vide($_GET["modif_commande"])){
	
	echo "<h2>Detail commande</h2><br><FORM id=\"formulaire\" name=\"ajout_commande\" action=\"article?id=85\" method=\"post\" >";
	
	if (test_non_vide($_GET["modif_commande"])){
		echo "<input name=\"id_commande\" type=\"hidden\"  value=\"".$_GET["c_id"]."\" />";
		$value_nom_commande=" value=\"".$_GET["nom_com"]."\" ";
		$value_nom_fournisseur=" value=\"".$_GET["nom_four"]."\" ";
	}
	else {
		$value_nom_commande=" placeholder=\"Nom de la commande\" ";
		$value_nom_fournisseur=" placeholder=\"Nom du fournisseur\" ";
	}
	echo "<input name=\"nom_commande\" type=\"text\" ".$value_nom_commande."  />"
		." <input name=\"nom_fournisseur\" type=\"text\"  ".$value_nom_fournisseur."   /><br><br>";
		
	echo "<br><br><input name=\"valide\" type=\"button\"  value=\"Valider cette commande\" onclick=\"enregistrer()\" /></form><br>";
}
else {
	echo "<a href=\"index.php/component/content/article?id=85&ttes=1&ajout_commande=1\"/>Ajouter une commande</a><br><hr>";
	
		
		

?>
	<FORM id="formulaire" name="filtrer" class="submission box" action="<?php echo JRoute::_('?id=85&ttes=1');?>" method="post" >

	<table border="0" width="100%">
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
			<td><input name="id_commande" type="text"  value="<? echo $id_commande;?>" size="7"  placeholder="id commande"></td>
		<?if (est_min_agent($user)){?>
			<td><input name="nom" type="text"  value="<? echo $nom;?>" size="7"  placeholder="Nom"></td>
			<td><input name="prenom" type="text"  value="<? echo $prenom;?>" size="7"  placeholder="Prenom"></td>
		<?}?>
		<?if (est_min_agent($user)){?>	
			<td><input name="id_client" type="text"  value="<? echo $id_client;?>" size="7"  placeholder="Num client"></td>
			<td></td>
			<td></td>
		<?}?>
				<td align="center"  nowrap colspan="2" ></td>
			</tr>
			<tr>
				<td align="center" colspan="12">
				<input name="valide" type="button"  value="Filtrer" onclick="valider()">
			</td>
		</tr>
	</table>
	</FORM>
	<hr>
<?


if ($id_client<>"") $complement_req=" and c.id in (select id_commande from Commande_client where `id_client`=".$id_client.") ";

$requete_liste_commande="SELECT c.*, (SELECT count(id_client) from Commande_client as cc WHERE cc.id_commande=c.id) as nbre_inscrits "	
	." , (SELECT count(id_client) from Commande_client as cc WHERE cc.id_commande=c.id and date_reception<>\"0000-00-00\") as nbre_reception"
	." FROM `Commande` as c WHERE  1 ".$complement_req;

if (test_non_vide($date_deb)) {	
		$requete_liste_commande.=" and c.date_creation=\"".$date_deb."\" ";
}

if (test_non_vide($id_commande)) $requete_liste_commande.=" and c.id=".$id_commande;

if (test_non_vide($_GET["ttes"]))
	$requete_liste_commande.=" group by c.id ";
	
if (test_non_vide($_GET["tri_par"]))
	$requete_liste_commande.=" order by ".$_GET["tri_par"];
else $requete_liste_commande.="  order by c.date_creation desc, c.heure_creation desc ";


$lien="<a href=\"".JRoute::_('index.php/component/content/article/component/content/?id=85&ttes=1')."";
if (test_non_vide($date_deb)) 
	$lien.="&date_deb=".$date_deb;
$lien.="&nom=".$nom."&prenom=".$prenom;


$requete_liste_commande.=pagination($requete_liste_commande,$lien."&tri_par=".$_GET["tri_par"]);

//echo $requete_liste_commande;

$db->setQuery($requete_liste_commande);	
$resultat_liste_commande= $db->loadObjectList();

if (!$resultat_liste_commande) echo $prb;
else {

	echo "<table class=\"zebra\"><tr><th>".$lien."&tri_par=c.id\">Num</a></th>";
				
	echo "<th>".$lien."&tri_par=c.nom\">Commande</a></th>";
	
		echo "<th>".$lien."&tri_par=s.date_debut\">Date commande</a></th>"
			."<th>Nbre inscrits</th><th>Nbre<br>receptions</th><th>Reste &agrave;<br>receptionner</th>"
			."<th>Suppr</th><th>Modif</th>";
	echo "</tr>";
		
	$le_nbre_reception_de_la_page=0;
	$le_reste_de_la_page=0;
	$nbres_inscrits_de_la_page=0;
	foreach($resultat_liste_commande as $liste_commande){
		
			echo "<tr><td>".$liste_commande->id."</td><td><a href=\"index.php/component/content/article?id=84&c_id=".$liste_commande->id."\" />".$liste_commande->nom."</a> </td>";
				
				echo "<td>".inverser_date($liste_commande->date_creation)." &agrave; ".$liste_commande->heure_creation."</td> "
					."<td>".$liste_commande->nbre_inscrits."</td>";
				$nbres_inscrits_de_la_page+=$liste_commande->nbre_inscrits;
				if (est_min_agent($user)) {
					echo "<td nowrap>".$liste_commande->nbre_reception;
					$le_nbre_reception_de_la_page+=$liste_commande->nbre_reception;
					$le_reste_de_la_page+=($liste_commande->nbre_inscrits-$liste_commande->nbre_reception);
					echo "</td><td nowrap>".($liste_commande->nbre_inscrits-$liste_commande->nbre_reception)." </td>";
				}
				echo "<td nowrap align=\"center\">";
				
				if  ($liste_commande->nbre_inscrits==0){
					echo " <a onClick=\"recharger('Voulez-vous supprimer cette commande ?'";
					echo ",'article?id=85&suppr_commande=".$liste_commande->id."')\">";
					echo "<img src=\"images/supprimer.png\" title=\"supprimer cette commande\"></a>";				
				}
				
				echo "</td><td align=\"center\">";
					echo " <a href=\"index.php/component/content/article?id=85&modif_commande=1&c_id="
						.$liste_commande->id."&nom_com=".$liste_commande->nom."&nom_four=".$liste_commande->nom_fournisseur."\" />"
						."<img src=\"images/modifier-icon.png\" title=\"modifier\"></a>";
				echo "</td>";
			echo "</tr>";
	}
	if (est_min_agent($user))
		echo "<tr><td colspan=\"3\" align=\"right\"><b>Totaux</b></td><td><b>".$nbres_inscrits_de_la_page."</b></td><td>"
			.$le_nbre_reception_de_la_page."</td>"
			."<td nowrap=\"nowrap\">".$le_reste_de_la_page."</td><td colspan=\"2\">&nbsp;</td></tr>";
	echo "</table>";

}
}
}

?>