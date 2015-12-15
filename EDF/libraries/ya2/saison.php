<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

?>	
<script type="text/javascript">
	function valider() {
		document.filtrer.submit();

	}
	function enregistrer() {
		document.ajout_saison.submit();

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

if (test_non_vide($_POST["nom"])) $nom=$_POST["nom"];
else $nom=$_GET["nom"];

if (test_non_vide($_POST["prenom"])) $prenom=$_POST["prenom"];
else $prenom=$_GET["prenom"];

if (test_non_vide($_POST["id_saison"])) $id_saison=$_POST["id_saison"];
else $id_saison=$_GET["id_saison"];

if (test_non_vide($_POST["date_deb"])) $date_deb=$_POST["date_deb"];
else {
	$temp=$id_saison.$id_client.$nom.$prenom.$_GET["ttes"].$indic_annul;
	if (test_non_vide($_GET["date_deb"])) $date_deb=$_GET["date_deb"];
	else if ($temp=="") header("Location: ../index.php/component/content/article?id=59&date_deb=".date("Y-m-d")."");
}

if (test_non_vide($_GET["suppr_cotisation"]) )
	delete_cotisation($_GET["suppr_cotisation"]);

if (test_non_vide($_POST["date_debut_saison"]) and test_non_vide($_POST["date_fin_saison"])){
	if (diff_dates_en_minutes($_POST["date_debut_saison"],"",$_POST["date_fin_saison"],"")>0){
		if (test_non_vide($_POST["nom_saison"]) and test_non_vide($_POST["montant_cotisation"])){
			$new_s_id=ajout_saison($_POST["nom_saison"],$_POST["date_debut_saison"],$_POST["date_fin_saison"]);
			if (test_non_vide($new_s_id)){
				$sc_id=ajout_saison_cotisation($new_s_id,$_POST["montant_cotisation"]);
				
				for($i=1;$i<=7;$i++)
					if ($_POST["jours_semaine_$i"]==1)
					    ajout_jour_semaine_dans_cotisation($i,$sc_id);
			}
			header("Location: /EDF/index.php/component/content/article?id=61&premiere=1&id_saison=".$new_s_id."");
		}
	}
	else echo "<font color=red>la date de fin est anterieur à la date de d&eacute;but</font>";
}

if (test_non_vide($_GET["ttes"]))
	$titre="Toutes les saisons";

if (!test_non_vide($_GET["ttes"]) and !test_non_vide($id_client))
	$titre="La saison actuelle";

if (test_non_vide($id_client))
	$titre="Les saisons du client";

menu_acces_rapide($id_client,$titre);

if (test_non_vide($_GET["ajout_saison"])){
	echo "<h2>Ajouter une saison</h2><br><FORM id=\"formulaire\" name=\"ajout_saison\" action=\"article?id=59\" method=\"post\" >"
		."<input name=\"nom_saison\" type=\"text\"  placeholder=\"Nom saison\" />"
		." du <input name=\"date_debut_saison\" type=\"date\"   />"
		." au <input name=\"date_fin_saison\" type=\"date\"  /><br>"
		." <input name=\"montant_cotisation\" type=\"text\"  placeholder=\"Montant premiere cotisation\" /><br><br>";
	afficher_check_tous_les_jours_de_semaine($sc_id);
	echo "<br><br><input name=\"valide\" type=\"button\"  value=\"Cr&eacute;er cette saison avec sa premiere cotisation\" onclick=\"enregistrer()\" /></form><br>";
}
else {
	echo "<a href=\"index.php/component/content/article?id=59&ttes=1&ajout_saison=1\"/>Ajouter une saison</a><br><hr>";
	
		
		

?>
	<FORM id="formulaire" name="filtrer" class="submission box" action="<?php echo JRoute::_('?id=59&ttes=1');?>" method="post" >

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
			<td><input name="id_saison" type="text"  value="<? echo $id_saison;?>" size="7"  placeholder="id saison"></td>
		<?if (est_min_agent($user)){?>
			<td><input name="nom" type="text"  value="<? echo $nom;?>" size="7"  placeholder="Nom"></td>
			<td><input name="prenom" type="text"  value="<? echo $prenom;?>" size="7"  placeholder="Prenom"></td>
		<?}?>
			<td width="50" align="right">&nbsp;</td>
			<td nowrap><input type="date" name="date_deb" value="<? echo $date_deb;?>"></td>
			<td></td>
			<td></td>
		<?if (est_min_agent($user)){?>	
			</tr>
			<tr>
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


if ($id_client<>"") $complement_req=" and sc.id in (select id_cotisation from Saison_inscription where `id_client`=".$id_client.") ";

$requete_liste_saison="SELECT s.*, s.id as s_id,sc.id as sc_id,s.nom as s_nom,sc.*, "
	." (SELECT count(id_client) from Saison_inscription as si WHERE si.id_cotisation=sc.id) as nbre_inscrits,"
	." (select sum(reg.montant_reglement) from Reglement as reg where reg.validation_reglement=1 and reg.id_cotisation=sc.id)  as total_versement "	
	." FROM `Saison_cotisations` as sc,Saison as s WHERE  sc.id_saison=s.id ".$complement_req;

if (test_non_vide($date_deb)) {
		
		$requete_liste_saison.=" and s.date_debut<=\"".$date_deb."\"";
		$requete_liste_saison.=" and s.date_fin>=\"".$date_deb."\"";
}

if (test_non_vide($id_saison)) $requete_liste_saison.=" and s.id=".$id_saison;

if (test_non_vide($_GET["ttes"]))
	$requete_liste_saison.=" group by s.id ";
	
if (test_non_vide($_GET["tri_par"]))
	$requete_liste_saison.=" order by ".$_GET["tri_par"];
else $requete_liste_saison.=" order by s.nom desc ";


$lien="<a href=\"".JRoute::_('index.php/component/content/article/component/content/?id=59&ttes=1')."";
if (test_non_vide($date_deb)) 
	$lien.="&date_deb=".$date_deb;
$lien.="&nom=".$nom."&prenom=".$prenom;


$requete_liste_saison.=pagination($requete_liste_saison,$lien."&tri_par=".$_GET["tri_par"]);

//echo $requete_liste_saison;

$db->setQuery($requete_liste_saison);	
$resultat_liste_saison= $db->loadObjectList();

if (!$resultat_liste_saison) echo $prb;
else {

	echo "<table class=\"zebra\"><tr>";
				
	echo "<th ";
	if (test_non_vide($_GET["ttes"]))
		echo " colspan=2";
	echo ">".$lien."&tri_par=s.id\">Saison</a></th>";
	
	if (!test_non_vide($_GET["ttes"]))
		echo "<th>".$lien."&tri_par=sc.id\">Jour</a></th><th>"
			.$lien."&tri_par=s.date_debut\">Date<br>debut</a></th><th>"
			.$lien."&tri_par=s.date_fin\">Date<br>fin</a></th>"
			."<th>Nbre inscrits</th><th>Montant cotisation</th><th>Total</th><th>Montant<br>versements</th>"
			."<th>Reste &agrave; payer</th><th>Suppr</th><th>Modif</th>";
	echo "</tr>";
		
	$le_montant_au_total_de_la_page=0;
	$le_montant_des_versements_au_total_de_la_page=0;
	$le_reste_a_payer_de_la_page=0;
	?><form name="register" class="submission box" action="article?id=81" method="post"  >
		<?
	$nbres_inscrits_dans_saison=0;
	foreach($resultat_liste_saison as $liste_saison){
		
			echo "<tr><td><a href=\"index.php/component/content/article?id=59&id_saison=".$liste_saison->s_id."\" />".$liste_saison->s_nom."</a> </td>";
				
			if (!test_non_vide($_GET["ttes"])){
				echo "<td nowrap valign=middle><a href=\"index.php/component/content/article?id=61&premiere=1&sc_id=".$liste_saison->sc_id
					."&id_saison=".$liste_saison->s_id."\" />".recup_liste_nom_jours_semaine_cotisation($liste_saison->sc_id)."</a></td>"
					."<td>".inverser_date($liste_saison->date_debut)."</td><td>".inverser_date($liste_saison->date_fin)."</td> "
					."<td>".$liste_saison->nbre_inscrits."</td>";
				$nbres_inscrits_dans_saison+=$liste_saison->nbre_inscrits;
				$montant_total_des_cotisations=$liste_saison->nbre_inscrits*$liste_saison->montant_cotisation;
				if (est_min_agent($user)) {
					echo "<td nowrap>";
					
					echo format_fr($liste_saison->montant_cotisation)."€ ";
					$le_montant_au_total_de_la_page+=$montant_total_des_cotisations;
					
					echo "</td><td nowrap>".format_fr($montant_total_des_cotisations)."€ ";
					echo "</td><td nowrap>".format_fr($liste_saison->total_versement)."€ ";
					$le_montant_des_versements_au_total_de_la_page+=$liste_saison->total_versement;
					echo "</td><td nowrap>";
					if ($liste_saison->total_versement<$montant_total_des_cotisations) echo "<font color=red>";
					echo format_fr($montant_total_des_cotisations-$liste_saison->total_versement)."€";
					$le_reste_a_payer_de_la_page+=($montant_total_des_cotisations-$liste_saison->total_versement);
					if ($liste_saison->total_versement<$montant_total_des_cotisations) echo "</font>";
					echo "</td>";
				}
				echo "<td nowrap align=\"center\">";
				
				if  ($liste_saison->total_versement==0){
					echo " <a onClick=\"recharger('Voulez-vous supprimer cette cotisation ?'";
					echo ",'article?id=59&suppr_cotisation=".$liste_saison->sc_id."')\">";
					echo "<img src=\"images/supprimer.png\" title=\"supprimer cette cotisation\"></a>";				
				}
				
				echo "</td><td align=\"center\">";
					echo " <a href=\"index.php/component/content/article?id=61&modif_cotisation=1&sc_id="
						.$liste_saison->sc_id."&id_saison=".$liste_saison->s_id."\" />"
						."<img src=\"images/modifier-icon.png\" title=\"modifier\"></a>";
				echo "</td>";
			}
			else echo "<td>".liste_categories_avec_feuille("",$liste_saison->s_id)."</td>";
			echo "</tr>";
	}
	if (est_min_agent($user) and (test_non_vide($date_deb) or test_non_vide($id_saison)))
		echo "<tr><td colspan=\"4\" align=\"right\"><b>Totaux</b></td><td><b>".$nbres_inscrits_dans_saison."</b></td><td></td><td><b>".format_fr($le_montant_au_total_de_la_page)."€</b></td>"
			."<td><b>".format_fr($le_montant_des_versements_au_total_de_la_page)."€</b></td><td nowrap=\"nowrap\"><b>".format_fr($le_reste_a_payer_de_la_page)."€</b></td><td colspan=\"2\">&nbsp;</td></tr>"; 
	echo "</table></form>";

}
}
}

?>