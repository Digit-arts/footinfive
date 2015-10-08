<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

?>	
<script type="text/javascript">
	function valider() {
		document.filtrer.submit();

	}

	
	function recharger(texte_a_afficher,lien) {
		if (texte_a_afficher!='')
			if (confirm(texte_a_afficher))
				if (lien!='') document.location.href=lien;
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
///suppresion d'une resa
if (test_non_vide($_GET["suppr"]) ) {

	proprietaire_resa($_GET["suppr"]);
	
	if ((test_non_vide($_GET["caution_suppr"]) and !test_non_vide($_POST["veto_gerant"]))
	    or (test_non_vide($_GET["caution_suppr"]) and test_non_vide($_POST["veto_gerant"]) and ($_POST["veto_gerant"]<>1)))
		supprimer_caution($_GET["caution_suppr"],$_GET["suppr"]);
	
	if (test_non_vide($_POST["Motif_annul_resa"]))
		$motif=$_POST["Motif_annul_resa"];
	else $motif=7;
	$mess_retour=annuler_resa($_GET["suppr"],$motif);	
	
	$corps=texte_annul_resa($id_client,$_GET["suppr"]);
	$objet="Annulation resa (".$_GET["suppr"].")";
	
	if (!sendMail($id_client,$objet,$corps))
		echo "\n<p><center>D&eacute;sol&eacute;, nous avons eu un probl&egrave;me lors de l'&eacute;mission du mail</center></p>\n";
	
	maj_commentaire("id_resa",$_GET["suppr"],$_POST["commentaire_annul_resa"]);
	
	if ((test_non_vide($_GET["caution_suppr"]) and !test_non_vide($_POST["veto_gerant"]))
	    or (test_non_vide($_GET["caution_suppr"]) and test_non_vide($_POST["veto_gerant"]) and ($_POST["veto_gerant"]<>1)))
		header("Location: article?id=59&id_client=".$_GET["caution_suppr"]."");
}

///suppresion definitive d'une resa
if (test_non_vide($_GET["dead"]) ) {

	proprietaire_resa($_GET["dead"]);
	$les_versements=versements_sans_remise_et_avec_validation($_GET["dead"]);
	$l_adresse_google=recup_1_element("adresse_resa_google","Reservation","id_resa",$_GET["dead"]);
	$verif_annul_resa=recup_1_element("indic_annul","Reservation","id_resa",$_GET["dead"]);
	
	echo "<font color=red>";
	if ($verif_annul_resa==1){
		if ($l_adresse_google==""){
			if ($les_versements=="" or $les_versements==0){
				supprimer_1_element("Reservation","id_resa",$_GET["dead"]);
				supprimer_1_element("Hist_Reservation","id_resa",$_GET["dead"]);
				supprimer_1_element("Reglement","id_reservation",$_GET["dead"]);
				supprimer_1_element("Commentaires","id_resa",$_GET["dead"]);
			}
			else echo "impossible de supprimer definitivement cette resa : ".$_GET["dead"]." car elle a des reglements valides.";
		}
		else echo "impossible de supprimer definitivement cette resa : ".$_GET["dead"]." car elle est encore presente sur Google Agenda.";
	}
	else echo "impossible de supprimer definitivement cette resa : ".$_GET["dead"]." car elle n'est pas encore annul&eacute;e.";
	
	echo "</font><br>";
}

if (est_min_manager($user) and test_non_vide($_GET["bloquer_optimisation"]) and test_non_vide($_GET["id_resa_bloquer_optimisation"])) {
	if ($_GET["bloquer_optimisation"]==0)
		$maj_bloquer_optimisation=1;
	else $maj_bloquer_optimisation=0;
	
	$requete_maj_bloquer_optimisation="UPDATE Reservation SET bloquer_optimisation=".$maj_bloquer_optimisation." where id_resa=".$_GET["id_resa_bloquer_optimisation"];
	//echo "<br>reqsuppr: ".$requete_maj_bloquer_optimisation;
	$db->setQuery($requete_maj_bloquer_optimisation);	
	$db->query();
}

if (test_non_vide($_POST["id_resa"])) $id_resa=$_POST["id_resa"];
else $id_resa=$_GET["id_resa"];


if (test_non_vide($_POST["date_fin"])) $date_fin=$_POST["date_fin"];
else $date_fin=$_GET["date_fin"];

if (test_non_vide($_POST["nom"])) $nom=$_POST["nom"];
else $nom=$_GET["nom"];

if (test_non_vide($_POST["prenom"])) $prenom=$_POST["prenom"];
else $prenom=$_GET["prenom"];

if (test_non_vide($_POST["equipe"])) $equipe=$_POST["equipe"];
else $equipe=$_GET["equipe"];

if (test_non_vide($_POST["Siret"])) $Siret=$_POST["Siret"];
else $Siret=$_GET["Siret"];

if (test_non_vide($_POST["Terrain"])) $Terrain=$_POST["Terrain"];
else $Terrain=$_GET["Terrain"];

if (test_non_vide($_POST["Type_terrain"])) $Type_terrain=$_POST["Type_terrain"];
else $Type_terrain=$_GET["Type_terrain"];


if (test_non_vide($_POST["indic-venue1"])) $indic_venue1=$_POST["indic-venue1"];
else $indic_venue1=$_GET["indic-venue1"];

if (test_non_vide($_POST["indic-venue2"])) $indic_venue2=$_POST["indic-venue2"];
else $indic_venue2=$_GET["indic-venue2"];

if (test_non_vide($_POST["indic-venue3"])) $indic_venue3=$_POST["indic-venue3"];
else $indic_venue3=$_GET["indic-venue3"];

if (test_non_vide($_POST["indic-venue4"])) $indic_venue4=$_POST["indic-venue4"];
else $indic_venue4=$_GET["indic-venue4"];

if (test_non_vide($_POST["indic_annul"])) $indic_annul=$_POST["indic_annul"];
else $indic_annul=$_GET["indic_annul"];

if (test_non_vide($_POST["indic_impayes"])) $indic_impayes=$_POST["indic_impayes"];
else $indic_impayes=$_GET["indic_impayes"];

if (isset($_POST["heure_debut_resa"])) $heure_debut_resa = $_POST["heure_debut_resa"];
else $heure_debut_resa = $_GET["heure_debut_resa"];

if (test_non_vide($_POST["joueur_champ"])) $joueur_champ=$_POST["joueur_champ"];
else $joueur_champ=$_GET["joueur_champ"];

if (test_non_vide($_POST["anniv"])) $anniv=$_POST["anniv"];
else $anniv=$_GET["anniv"];

if (test_non_vide($_POST["graph"])) $graph=$_POST["graph"];
else $graph=$_GET["graph"];


if (test_non_vide($_POST["date_deb"])) $date_deb=$_POST["date_deb"];
else {
	$temp=$id_resa.$id_client.$nom.$prenom.$equipe.$Siret.$Terrain.$Type_terrain.$heure_debut_resa.$_GET["ttes"].$indic_venue1.$indic_venue2.$indic_venue3.$indic_venue4.$indic_annul.$indic_impayes.$joueur_champ.$graph.$anniv.$_GET["suppr"];
	if (test_non_vide($_GET["date_deb"])) $date_deb=$_GET["date_deb"];
	else if ($temp=="") header("Location: ../index.php/component/content/article?id=59&date_deb=".date("Y-m-d")."");
}
$titre="Les r&eacute;servations";

if (!test_non_vide($_GET["ttes"]) and !test_non_vide($id_client))
	$titre.=" du jour";

if (test_non_vide($id_client))
	$titre.=" du client";

menu_acces_rapide($id_client,$titre);
nettoyer_resa_non_payees();
maj_resa_ledg();

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
			<td><input name="id_resa" type="text"  value="<? echo $id_resa;?>" size="7"  placeholder="Num résa"></td>
		<?if (est_min_agent($user)){?>
			<td><input name="nom" type="text"  value="<? echo $nom;?>" size="7"  placeholder="Nom"></td>
			<td><input name="prenom" type="text"  value="<? echo $prenom;?>" size="7"  placeholder="Prenom"></td>
		<?}?>
			<td width="50" align="right">&nbsp;</td>
			<td nowrap><input type="date" name="date_deb" value="<? echo $date_deb;?>">
			<input type="date" name="date_fin" value="<? echo $date_fin;?>"></td>
			<td nowrap><? menu_deroulant_simple("Type_terrain",$Type_terrain,"","valider()");
			if (test_non_vide($Type_terrain))
				$compl_req2=" and id_type=".$Type_terrain;
			else $compl_req2="";
			 menu_deroulant_simple("Terrain",$Terrain,$compl_req2,"valider()");
			?></td>
			
			<?/*<td><!--select name="heure_debut_resa">
			<?
			echo "<option value=\"\"></option>";			
			list($heure_resa,$minutes_resa) = explode(':', $heure_debut_resa );

			for ($i=9;$i<=23;$i++) {
					  $select_heure="";
					  $select_demie=""; 
						if ($heure_resa==$i) {
						  if ($minutes_resa=="30") $select_demie=" selected ";
						  else $select_heure=" selected ";
						}
					  echo "<option value=\"".$i.":00\" \"".$select_heure."\">".$i."h00</option>";
					  if ($i<>9) echo "<option value=\"".$i.":30\" \"".$select_demie."\">".$i."h30</option>";
			}
			if (($heure_resa<>"") and ($heure_resa==0)) $select=" selected ";
			echo "<option value=\"00:00\" \"".$select."\">00h00</option>";
			</select--> */?>
			
			</td>
		<?if (est_min_agent($user)){?>	
			</tr>
			<tr>
			<td><input name="id_client" type="text"  value="<? echo $id_client;?>" size="7"  placeholder="Num client"></td>
			<td><input name="equipe" type="text"  value="<? echo $equipe;?>" size="7"  placeholder="Equipe"></td>
			<td><input name="Siret" type="text"  value="<? echo $Siret;?>" size="7"  placeholder="Siret"></td>
		<?}?>
				<td align="center"  nowrap colspan="2" ><INPUT type="checkbox" name="indic-venue1" value="1" 
				<? if (test_non_vide($indic_venue1)) echo "checked"; ?>><img src="images/indic-venue1.png" title="presence client: inconnue"/>
				<INPUT type="checkbox" name="indic-venue2" value="2" 
				<? if (test_non_vide($indic_venue2)) echo "checked"; ?>><img src="images/indic-venue2.png" title="presence client: valid&eacute;e"/>
				<INPUT type="checkbox" name="indic-venue3" value="3" 
				<? if (test_non_vide($indic_venue3)) echo "checked"; ?>><img src="images/indic-venue3.png" title="presence client: absent"/>
				<INPUT type="checkbox" name="indic-venue4" value="4" 
				<? if (test_non_vide($indic_venue4)) echo "checked"; ?>><img src="images/indic-venue4.png" title="presence client: non renseign&eacute;e"/>
				<INPUT type="checkbox" name="indic_annul" value="1" 
				<? if (test_non_vide($indic_annul)) echo "checked"; ?>><img src="images/Cancel-resa.png" title="resa supprim&eacute;e"/>
				<INPUT type="checkbox" name="indic_impayes" value="1" 
				<? if (test_non_vide($indic_impayes)) echo "checked"; ?>><img src="images/impayes-icon.png" title="les impay&eacute;s"/>
				<INPUT type="checkbox" name="joueur_champ" value="1" <? if (test_non_vide($joueur_champ)) echo "checked"; ?>>
				<img src="images/coupe-icon.png" title="Joueur du championnat"/>
				<INPUT type="checkbox" name="anniv" value="1" <? if (test_non_vide($anniv)) echo "checked"; ?>>
				<img src="images/anniv-icon.png" title="Anniversaire"/>
				</td>
				<? if (est_min_manager($user)){?>
				<td><INPUT type="checkbox" name="graph" value="1" <? if (test_non_vide($graph)) echo "checked"; ?>>
				<img src="images/statistics-icon.png" title="Graphique"/></td>
				<?}
		
		/*if (est_min_agent($user)){?>	
				<td align="right"  >Ter.</td>
				<td><? menu_deroulant("Terrain",$Terrain,"valider()");?></td>
		<?}*/?>		
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


if ($id_client<>"") $complement_req=" and r.`id_client`=".$id_client;

$requete_liste_resa="SELECT r.id_match, r.anniv, r.date_debut_resa,r.date_fin_resa, r.heure_debut_resa, r.heure_fin_resa,r.indic_annul,r.indic_venue,r.id_Motif_annul_resa,
	 (select man.nom from Motif_annul_resa as man where man.id=r.id_Motif_annul_resa) as motif,r.id_resa, r.cautionnable, r.montant_total, c.accompte_necessaire,
	 c.id_client,c.id_user,c.Siret,c.equipe,c.nom,c.prenom,t.nom as le_terrain, 
	(select sum(reg.montant_reglement) from Reglement as reg where reg.validation_reglement=1 and reg.id_reservation=r.id_resa)  as total_versement 
	FROM `Reservation` as r, Client as c, Terrain as t where 
	r.id_client=c.id_client and r.id_terrain=t.id ".$complement_req;

if (test_non_vide($date_deb)) {

	if (test_non_vide($date_fin)) {
		
		$requete_liste_resa.=" and r.date_debut_resa>=\"".$date_deb."\"";
		$requete_liste_resa.=" and r.date_debut_resa<=\"".$date_fin."\"";
	}
	else {
		if (strpos($date_deb,"/")==2)
			list($jour, $mois, $annee) = explode('/', $date_deb);
		else  list($annee, $mois, $jour) = explode('-', $date_deb);
	
		$date_en_get=$annee."-".$mois."-".$jour;
		
		$requete_liste_resa.=" and concat(r.date_debut_resa,' ',r.heure_debut_resa) BETWEEN '".$date_en_get." ".horaire_ouverture()."'
			AND '".decaler_jour($date_en_get,1)." ".horaire_fermeture()."' ";
	}
}
else {
	if (est_register($user)) 
		$requete_liste_resa.=" and r.date_debut_resa>=\"".date("Y-m-d")."\"";
}

if (test_non_vide($id_resa)) $requete_liste_resa.=" and r.id_resa=".$id_resa;
if (test_non_vide($Terrain)) $requete_liste_resa.=" and r.id_terrain=".$Terrain;
if (test_non_vide($Type_terrain)) $requete_liste_resa.=" and t.id_type=".$Type_terrain;
if (test_non_vide($indic_annul)) $requete_liste_resa.=" and r.indic_annul=".$indic_annul;
	
if (test_non_vide($_POST["heure_debut_resa"])) $requete_liste_resa.=" and r.heure_debut_resa>=\"".Ajout_zero_si_absent($_POST["heure_debut_resa"])."\"";
if (test_non_vide($nom)) $requete_liste_resa.=" and c.nom like \"%".$nom."%\"";
if (test_non_vide($prenom)) $requete_liste_resa.=" and c.prenom like \"%".$prenom."%\"";
if (test_non_vide($equipe)) $requete_liste_resa.=" and c.equipe like \"%".$equipe."%\"";
if (test_non_vide($Siret)) $requete_liste_resa.=" and c.Siret like \"%".$Siret."%\"";
if (test_non_vide($joueur_champ)) $requete_liste_resa.=" and c.id_user in ".liste_users_ledg();
if (test_non_vide($anniv)) $requete_liste_resa.=" and r.anniv=1 ";

$liste_indic_venue="";
if (test_non_vide($indic_venue1)) $liste_indic_venue.="1,";
if (test_non_vide($indic_venue2)) $liste_indic_venue.="2,";
if (test_non_vide($indic_venue3)) $liste_indic_venue.="3,";
if (test_non_vide($indic_venue4)) $liste_indic_venue.="4,";

if (test_non_vide($liste_indic_venue)) $requete_liste_resa.=" and r.indic_venue in (".$liste_indic_venue."0)";

if (test_non_vide($indic_impayes))
	$requete_liste_resa.=" and (SELECT IFNULL((select format(sum(reg2.montant_reglement),2) "
			." from Reglement as reg2 where reg2.validation_reglement=1 and reg2.id_reservation=r.id_resa),0)"
			."<(SELECT IFNULL((SELECT format(sum(p.`Montant_TTC`),2)  FROM `Prestation` as p where p.prestation_validation=1 "
			." and p.id_resa=r.id_resa),0)+r.montant_total)) and r.indic_annul<>1 ";
	
if (test_non_vide($_GET["tri_par"]))
	$requete_liste_resa.=" order by ".$_GET["tri_par"];
else {
	if (test_non_vide($date_deb))
		$requete_liste_resa.=" order by r.date_debut_resa, r.heure_debut_resa, t.nom";
	else $requete_liste_resa.=" order by r.date_valid desc, r.heure_valid desc, t.nom";
}

$lien="<a href=\"".JRoute::_('index.php/component/content/article/component/content/?id=59&ttes=1')."";
if (test_non_vide($date_deb)) 
	$lien.="&date_deb=".$date_deb;
if (test_non_vide($date_fin)) 
	$lien.="&date_fin=".$date_fin;
$lien.="&nom=".$nom."&prenom=".$prenom."&Siret=".$Siret."&equipe=".$equipe."&joueur_champ=".$joueur_champ."&graph=".$graph."&anniv=".$anniv;
$lien.="&indic_annul=".$indic_annul."&indic-venue1=".$indic_venue1."&indic-venue2=".$indic_venue2."&indic-venue3=";
$lien.=$indic_venue3."&indic-venue4=".$indic_venue4."&indic_impayes=".$indic_impayes;

if (!test_non_vide($graph) and (!test_non_vide($date_deb) or test_non_vide($date_fin)))
	$requete_liste_resa.=pagination($requete_liste_resa,$lien."&tri_par=".$_GET["tri_par"]);

//echo $requete_liste_resa;

$db->setQuery($requete_liste_resa);	
$resultat_liste_resa= $db->loadObjectList();
$jour_fr = jours_en_fr();
if (!$resultat_liste_resa) echo $prb;
else {
	$tableau_des_resultats="<table class=\"zebra\"><tr>";
				
	if (est_min_agent($user))
		$tableau_des_resultats.="<th>".$lien."&tri_par=r.id_client\">Num<br>client</a></th><th>Client</th>";
	$tableau_des_resultats.="<th>".$lien."&tri_par=r.id_resa\">Num résa</a></th><th>".$lien."&tri_par=r.date_debut_resa\">Date résa</a></th><th>heure résa</th><th>".$lien."&tri_par=r.indic_venue\">Indic venue</a></th><th>Ter.</th>";
	if (est_min_agent($user))
		$tableau_des_resultats.="<th>Montant total</th><th>Reste &agrave pay&eacute;</th>";
		
	$tableau_des_resultats.="<th>Annuler</th><th>Modifier</th></tr>";
	$le_montant_au_total_de_la_page=0;
	$le_reste_a_payer_de_la_page=0;
	foreach($resultat_liste_resa as $liste_resa){
		$montant_total_presta=montant_total_presta($liste_resa->id_resa);
		$le_montant_au_total=$liste_resa->montant_total+$montant_total_presta;
			$tableau_des_resultats.="<tr>";
			if (est_min_agent($user)) {
				$tableau_des_resultats.="<td>".$liste_resa->id_client." </td>";
				$tableau_des_resultats.="<td nowrap valign=middle>";
				$info_ledg=existe_joueur_capitaine($liste_resa->id_user);
				if (test_non_vide($info_ledg)){
					if (strcmp(substr($info_ledg,0,9),"capitaine")==0)
						$tableau_des_resultats.="<img src=\"images/capitaine-icon.png\" title=\"".$info_ledg."\">";
					else $tableau_des_resultats.="<img src=\"images/joueur-icon.png\" title=\"".$info_ledg."\">";
				}
				if ($liste_resa->accompte_necessaire==1)
					$tableau_des_resultats.="<img src=\"images/VIP-icon.png\" title=\"VIP\"  HEIGHT=\"12\" WIDTH=\"12\" /> ";
				$tableau_des_resultats.="<a href=\"index.php/component/content/article?id=60&id_client=".$liste_resa->id_client."\"/>";
				if ($liste_resa->equipe<>"") $tableau_des_resultats.=$liste_resa->equipe."</a>"; 
				else $tableau_des_resultats.=$liste_resa->nom."</a> ".$liste_resa->prenom;
				
				if ($liste_resa->id_client==3586 and test_non_vide($liste_resa->id_match) and  $liste_resa->id_match<>"" and est_min_agent($user) )
					$tableau_des_resultats.="<br>".recup_rencontre($liste_resa->id_match);
				
				$tableau_des_resultats.="</td>";
			}
			$tableau_des_resultats.="<td nowrap><a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$liste_resa->id_resa."\" />";
			$tableau_des_resultats.=$liste_resa->id_resa."</a>";
			if (($liste_resa->cautionnable==1) and $liste_resa->indic_annul<>1) $tableau_des_resultats.="<font color=red>(C)</font>";
			$ligne_commentaire=recup_derniere_commentaire("id_resa",$liste_resa->id_resa);
			if ($ligne_commentaire->Commentaire<>"" and est_min_agent($user)){
				$tableau_des_resultats.=" <a href=\"index.php/component/content/article?id=75&art=59&id_resa=".$liste_resa->id_resa."\">";
				$tableau_des_resultats.="<img src=\"images/Comment-icon.png\" title=\"".$ligne_commentaire->Commentaire."\"></a>";
			}
			if ($liste_resa->anniv==1) 
				$tableau_des_resultats.="<img src=\"images/anniv-icon.png\" title=\"Anniversaire\">";
			

			$bloquer_optimisation=recup_1_element("bloquer_optimisation","Reservation","id_resa",$liste_resa->id_resa);
			if (est_min_manager($user))
				$tableau_des_resultats.=" <a href=\"".$_SERVER["REQUEST_URI"]."&id_resa_bloquer_optimisation=".$liste_resa->id_resa."&bloquer_optimisation=".$bloquer_optimisation."\">";
			
			if ($bloquer_optimisation==1)
				$tableau_des_resultats.="<img src=\"images/bloquer_optimisation_1.png\" title=\"Resa non-déplaçable\">";
			//else $tableau_des_resultats.="<img src=\"images/bloquer_optimisation_0.png\" title=\"Resa déplaçable\">";

			$tableau_des_resultats.="</a>";
			if ($liste_resa->id_client==3586 and $liste_resa->id_match<>"" and est_min_agent($user)){
				$tableau_des_resultats.="<br><a href=\"http://footinfive.com/LEDG/index.php/fm?tmpl=component&print=1&page=&Num_Match=".$liste_resa->id_match."\" target=\"_blank\">";
				$tableau_des_resultats.="<img src=\"http://footinfive.com/LEDG/images/stories/fm-icon.png\" title=\"Feuille de match vierge\"></a>";
			}
			$feuile_match=recup_nom_feuille_match($liste_resa->id_match);
			if (test_non_vide($feuile_match))
				$tableau_des_resultats.="<a  title=\"Scan feuille de match\" href=\"http://footinfive.com/LEDG/Feuilles-de-matchs/".$feuile_match."\"  target=\"_blank\">"
					."<img src=\"http://footinfive.com/LEDG/images/stories/feuille-de-match-scan.png\" alt=\"Scan feuille de match\"></a>";
			if ($liste_resa->id_client==3586 and $liste_resa->id_match<>"" and est_min_agent($user) and diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa)>=0){
				$tableau_des_resultats.=" <a href=\"http://www.footinfive.com/LEDG/index.php/component/joomsport/view_match/".$liste_resa->id_match."?Itemid=0\" target=\"_blank\">";
				$tableau_des_resultats.="<img src=\"images/les-buteurs.png\" title=\"Les buteurs du match\"></a>";
			}
			
			$tableau_des_resultats.="</td><td>";
			$tableau_des_resultats.=date_longue($liste_resa->date_debut_resa)."</td><td> ";
			$tableau_des_resultats.=$liste_resa->heure_debut_resa."-";
			$tableau_des_resultats.=$liste_resa->heure_fin_resa."</td><td>";
			
		
			if  ($liste_resa->indic_annul<>1 and est_min_agent($user)){
				$tableau_des_resultats.="<a href=\"index.php/component/content/article?id=59&venue=".$liste_resa->id_resa."&id_resa=".$id_resa."&id_client=".$id_client;
				$tableau_des_resultats.="&nom=".$nom."&prenom=".$prenom."&Type_terrain=".$Type_terrain."&Terrain=".$Terrain."&date_fin=".$date_fin."&date_deb=".$date_deb."&heure_debut_resa=".$heure_debut_resa."\">";
			}	
				if ((diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa)>0) and ($liste_resa->indic_venue==1) and ($liste_resa->indic_venue<>4) ){
					if (!test_non_vide($_GET["venue"])){
						$requete_maj_venue="update Reservation set `indic_venue`=4 where `id_resa`=".$liste_resa->id_resa;
						//$tableau_des_resultats.=$requete_maj_venue;
						$db->setQuery($requete_maj_venue);
						$resultat_maj_venue = $db->query();
					}
					$tableau_des_resultats.="<img src=\"images/indic-venue4.png\" title=\"presence client : non renseign&eacute;e\" />";
				}
				else { 
					if (diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa)>-120){
						$tableau_des_resultats.="<img src=\"images/indic-venue";
						$tableau_des_resultats.=$liste_resa->indic_venue.".png\" title=\"";
						switch ($liste_resa->indic_venue) {
							case '1' : $tableau_des_resultats.="presence client: inconnue";break;
							case '2' : $tableau_des_resultats.="presence client: valid&eacute;e";break;
							case '3' : $tableau_des_resultats.="presence client: absent";break;
							default :  break;
						}
						$tableau_des_resultats.="\" />";
					}
				if  ($liste_resa->indic_annul<>1)
					$tableau_des_resultats.="</a>";
			}
			
			$tableau_des_resultats.="</td><td>";
			$tableau_des_resultats.=$liste_resa->le_terrain;
			$tableau_des_resultats.="</td>";
			if (est_min_agent($user)) {
				$tableau_des_resultats.="<td>";
				
				$tableau_des_resultats.=format_fr($le_montant_au_total)."€ ";
				$le_montant_au_total_de_la_page+=$le_montant_au_total;
				
				if ($montant_total_presta>0)
					$tableau_des_resultats.="<font color=blue><br>(P : ".format_fr($montant_total_presta).")</font>";
				$tableau_des_resultats.="</td><td>";
				if ($liste_resa->total_versement<$le_montant_au_total) $tableau_des_resultats.="<font color=red>";
				$tableau_des_resultats.=format_fr($le_montant_au_total-$liste_resa->total_versement)."€";
				if ($liste_resa->indic_annul<>1)
					$le_reste_a_payer_de_la_page+=($le_montant_au_total-$liste_resa->total_versement);
				if ($liste_resa->total_versement<$le_montant_au_total) $tableau_des_resultats.="</font>";
				$tableau_des_resultats.="</td>";
			}
			$tableau_des_resultats.="<td nowrap align=\"center\">";
			if  (($liste_resa->indic_annul<>1) and ($liste_resa->cautionnable==2) and ($liste_resa->total_versement<$le_montant_au_total))
				$tableau_des_resultats.="<img src=\"images/resa-en-sursis-icon.png\" title=\"R&eacute;sa en sursis pendant 24h (sans caution)\">";
			
			if  (($liste_resa->indic_annul<>1) and  (diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa)<0 and ($liste_resa->indic_venue==1))){
				$tableau_des_resultats.=" <a href=\"index.php/component/content/article?id=61&annul=1&id_resa=".$liste_resa->id_resa."\" />";
					$tableau_des_resultats.="<img src=\"images/annuler-resa.png\" title=\"supprimer cette réservation\"></a> ";
			}
			if  ($liste_resa->indic_annul==1) {
				$tableau_des_resultats.="<img src=\"images/Cancel-resa.png\" title=\"Réservation supprimée (".$liste_resa->motif.")\">";
				$les_versements=versements_sans_remise_et_avec_validation($liste_resa->id_resa);
				if (est_min_manager($user) and ($les_versements=="" or $les_versements==0)) //and test_non_vide($liste_resa->id_Motif_annul_resa) and $liste_resa->id_Motif_annul_resa==7)
					$tableau_des_resultats.=" &nbsp;&nbsp;<a onClick=\"recharger('Voulez-vous supprimer definitvement cette resa ?'"
						.",'article/?id=59&ttes=1&dead=".$liste_resa->id_resa."')\">"
						."<img src=\"images/supprimer-definitivement-resa.png\" title=\"Supprimer definitivement cette réservation ?\"></a>";
			}
			$tableau_des_resultats.="</td><td align=\"center\">";
			if  (($liste_resa->indic_annul<>1) and (diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa)<-2880 or (est_min_agent($user) and diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa)<120) )) {
				// plus de modif à moins 48h
				$tableau_des_resultats.=" <a href=\"index.php/component/content/article?id=62";
				$tableau_des_resultats.="&id_client=".$liste_resa->id_client;
				$tableau_des_resultats.="&modif=1&num_resa=".$liste_resa->id_resa."&date_debut_resa=".$liste_resa->date_debut_resa."&heure_fin_resa=".$liste_resa->heure_fin_resa."&heure_debut_resa=".$liste_resa->heure_debut_resa."\" />";
				$tableau_des_resultats.="<img src=\"images/modifier-resa.png\" title=\"modifier cette réservation\"></a> ";
				
			}
			
			$duree_resas_en_heure=diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa,$liste_resa->date_fin_resa,$liste_resa->heure_fin_resa)/60;
			list($annee_temp, $mois_temp, $jour_temp) = explode('-', $liste_resa->date_debut_resa);
			$date_longue = mktime (0, 0, 0, $mois_temp, $jour_temp, $annee_temp);
			
			$tableau_des_resultats.="</td>";
			$tableau_des_resultats.="</tr>";

			$tab_pour_graph[$jour_fr[date("w", $date_longue)]]+=$duree_resas_en_heure;
			
	}
	if (est_min_agent($user) and test_non_vide($date_deb)) 
		$tableau_des_resultats.="<tr><td colspan=\"7\" align=\"right\"><b>Totaux</b></td><td><b>".format_fr($le_montant_au_total_de_la_page)."€</b></td>"
			."<td nowrap=\"nowrap\"><b>".format_fr($le_reste_a_payer_de_la_page)."€</b></td><td colspan=\"2\">&nbsp;</td></tr>"
			."<tr><td colspan=\"7\" align=\"right\"><b>Diff</b></td><td><b>".format_fr($le_montant_au_total_de_la_page-$le_reste_a_payer_de_la_page)."€</b></td>"
			."<td colspan=\"3\">&nbsp;</td></tr>"; 
	$tableau_des_resultats.="</table>";
	$tableau_des_resultats.="<br><font color=red>".$mess_retour."</font><br><br>";
	
	if (test_non_vide($graph)){
		set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries/ya2');
			
		require_once('libchart/libchart/classes/libchart.php');
			
			
		$chemin="libraries/ya2/Graphs-freq/";
		$nom_fichier="pif.png";
			$chart = new VerticalBarChart();
	
		$dataSet = new XYDataSet();
		foreach ($jour_fr as $un_elt){
			//echo $un_elt."--".$tab_pour_graph[$un_elt];
			$dataSet->addPoint(new Point($un_elt, $tab_pour_graph[$un_elt]));
		}

		$chart->setDataSet($dataSet);
	
		$chart->setTitle("Nbre heures d'occupation par jour de semaine");
		$chart->render($chemin.$nom_fichier);
	?>
	<img alt="Line chart" src="<? echo $chemin.$nom_fichier; ?>" style="border: 1px solid gray;"/>
	<?
	}
	else echo $tableau_des_resultats;
}
}

?>