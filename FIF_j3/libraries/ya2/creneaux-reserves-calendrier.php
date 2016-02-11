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

$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {


if (test_non_vide($_POST["date_deb"])) $date_deb=$_POST["date_deb"];
else {
	if (test_non_vide($_GET["date_deb"])) $date_deb=$_GET["date_deb"];
	else header("Location: ../index.php/resa/cal?date_deb=".date("Y-m-d")."");
}

?>
	<FORM id="formulaire" name="filtrer" class="submission box" action="<?php echo JRoute::_('?resa/cal');?>" method="post" >
<center>
	<table border="0" width="100%">
		<tr>	<td align="left"  width="10%"><a href="index.php/resa/cal?date_deb=<? echo decaler_jour($date_deb,-1); ?>"><img src="images/prec-icon.png" title="Jour precedent"></a></td>
			<td align="center"  width="20%"><a href="index.php/resa/cal?date_deb=<? echo date("Y-m-d"); ?>">Aujourd'hui</a></td>
			<td nowrap align="center"  width="20%"><input type="date" name="date_deb" value="<? echo $date_deb;?>" onChange="valider()">
			<td align="center"  width="20%"><a href="index.php/resa/cal?date_deb=<? echo $date_deb; ?>">Rafraichir</a></td>
			<td align="center"  width="20%"><a href="index.php/component/content/article?id=59&date_deb=<? echo $date_deb; ?>">Retour</a></td>
			<td align="right" width="10%"><a href="index.php/resa/cal?date_deb=<? echo decaler_jour($date_deb,1); ?>"><img src="images/suiv-icon.png" title="Jour suivant"></a></td>
		</tr>
	</table>
	</FORM>
</center>
	<hr>
<?





$requete_liste_resa="SELECT r.*,(select man.nom from Motif_annul_resa as man where man.id=r.id_Motif_annul_resa) as motif, c.accompte_necessaire,
	 c.id_client,c.id_user,c.Siret,c.equipe,c.nom,c.prenom,c.mobile1,c.mobile2,c.fixe,t.nom as le_terrain FROM `Reservation` as r, Client as c, Terrain as t where 
	r.id_client=c.id_client and r.id_terrain=t.id and indic_annul=0 ";

if (test_non_vide($date_deb)) {

	if (strpos($date_deb,"/")==2)
		list($jour, $mois, $annee) = explode('/', $date_deb);
	else  list($annee, $mois, $jour) = explode('-', $date_deb);
	
	$date_en_get=$annee."-".$mois."-".$jour;
		
$requete_liste_resa.=" and concat(r.date_debut_resa,' ',r.heure_debut_resa) BETWEEN '".$date_en_get." ".horaire_ouverture()."'
	AND '".decaler_jour($date_en_get,1)." ".horaire_fermeture()."' ";
}
else {
	$requete_liste_resa.=" and r.date_debut_resa=\"".date("Y-m-d")."\"";
}


$requete_liste_resa.=" order by r.date_debut_resa, r.heure_debut_resa asc, t.id_type,t.nom";


$lien="<a href=\"".JRoute::_('index.php/component/content/article/component/content/?id=59&ttes=1')."";
if (test_non_vide($date_deb)) 
	$lien.="&date_deb=".$date_deb;
if (test_non_vide($date_fin)) 
	$lien.="&date_fin=".$date_fin;
$lien.="&nom=".$nom."&prenom=".$prenom."&Siret=".$Siret."&equipe=".$equipe."&joueur_champ=".$joueur_champ."&anniv=".$anniv;
$lien.="&indic_annul=".$indic_annul."&indic-venue1=".$indic_venue1."&indic-venue2=".$indic_venue2."&indic-venue3=";
$lien.=$indic_venue3."&indic-venue4=".$indic_venue4."&indic_impayes=".$indic_impayes;


//echo $requete_liste_resa;

$db->setQuery($requete_liste_resa);	
$resultat_liste_resa= $db->loadObjectList();

if (!$resultat_liste_resa) echo $prb;
else {
	list($heure_ouv,$min_ouv)=explode(":",horaire_ouverture());
	list($heure_ferm,$min_ferm)=explode(":",horaire_fermeture());
	

	
	foreach($resultat_liste_resa as $liste_resa){
		
		$title_resa="Horaires : ".$liste_resa->heure_debut_resa." ".$liste_resa->heure_fin_resa."\n";
		$info_resa="<a href=\"index.php/component/content/article?id=61&premiere=1&id_resa=".$liste_resa->id_resa."\"  title=\"".$title_resa."\" target=\"_blank\"/>".$liste_resa->id_resa."</a> - ";
		
		$info_ledg=existe_joueur_capitaine($liste_resa->id_user);
		if (test_non_vide($info_ledg)){
			if (strcmp(substr($info_ledg,0,9),"capitaine")==0)
				$info_resa.="<img src=\"images/capitaine-icon.png\" width=\"12\" height=\"12\"  title=\"".$info_ledg."\"> ";
					else $info_resa.="<img src=\"images/joueur-icon.png\" width=\"12\" height=\"12\" title=\"".$info_ledg."\"> ";
			}
		if ($liste_resa->accompte_necessaire==1)
			$info_resa.="<img src=\"images/VIP-icon.png\" title=\"VIP\"  HEIGHT=\"12\" WIDTH=\"12\" /> ";
			
		
		$title_client="Num client: ".$liste_resa->id_client;
		if (test_non_vide($liste_resa->mobile1))
			$title_client.="\nMobile1 : ".$liste_resa->mobile1;
		if (test_non_vide($liste_resa->mobile2))
			$title_client.="\nMobile2 : ".$liste_resa->mobile2;
		if (test_non_vide($liste_resa->fixe))
			$title_client.="\nFixe : ".$liste_resa->fixe;	
	
		
		$info_resa.="<a href=\"index.php/component/content/article?id=60&id_client=".$liste_resa->id_client."\" title=\"".$title_client."\"  target=\"_blank\"/>";
		
		$duree_resa_en_nbre_demies_heures=(diff_dates_en_minutes($liste_resa->date_debut_resa,$liste_resa->heure_debut_resa,$liste_resa->date_fin_resa,$liste_resa->heure_fin_resa)/30);
		

		$info_resa.=$liste_resa->nom."</a> ".$liste_resa->prenom;
		if ($liste_resa->equipe<>"")
			$info_resa.="<br>".$liste_resa->equipe;
		
		$info_resa.="##".$duree_resa_en_nbre_demies_heures;
	
		$heure_deb=intval(substr($liste_resa->heure_debut_resa,0,2));
		$min_deb=intval(substr($liste_resa->heure_debut_resa,3,2));
		$heure_fin=intval(substr($liste_resa->heure_fin_resa,0,2));
		
		$compteur_demies_heure=$duree_resa_en_nbre_demies_heures;
		
		if ($heure_fin<=intval($heure_ferm))
			$heure_fin+=24;
			
		if ($heure_deb<=intval($heure_ferm))
			$heure_deb+=24;
			
		if ($min_deb==0){
			for ($i=$heure_deb;$i<=$heure_fin;$i++){
				for ($min=0;$min<=1;$min++){
					$tab_terrains[$min][$i][$liste_resa->id_terrain]="##0";
					$compteur_demies_heure--;
					if ($compteur_demies_heure==0)
						break;					
				}
				if ($compteur_demies_heure==0)
					break;
			}
			$tab_terrains[0][$heure_deb][$liste_resa->id_terrain]=$info_resa;
		}
		else {
			$compteur_demies_heure=$duree_resa_en_nbre_demies_heures;
			for ($i=$heure_deb;$i<=$heure_fin;$i++){
				for ($min=0;$min<=1;$min++){
					if(!($i==$heure_deb and $min==0)){
						$tab_terrains[$min][$i][$liste_resa->id_terrain]="##0";
						$compteur_demies_heure--;
					}
					
					if ($compteur_demies_heure==0)
						break;					
				}
				if ($compteur_demies_heure==0)
					break;
			}
			$tab_terrains[1][$heure_deb][$liste_resa->id_terrain]=$info_resa;
		}
		
	}
	
	$requete_nbre_terrain="select * from Terrain as t where t.is_active=1 ";
	$db->setQuery($requete_nbre_terrain);	
	//echo "<br>".$requete_nbre_terrain."<br>";
	$les_terrains = $db->loadObjectList();
	
	echo "<table style=\"border-collapse: collapse;\" >";
	
	$nbre_terrain=0;
	echo "<tr><td></td>";
	foreach($les_terrains as $terrain){
		$nbre_terrain++;
		echo "<td style=\"border: 1px solid black;\" bgcolor=\"#CCCCCC\" height=\"30\" width=\"100\" valign=\"middle\" align=\"center\">".$terrain->nom."<br>".$terrain->nom_long."</td>";
		
	}
	echo "</tr>";
	
	if (intval($heure_ferm)<intval($heure_ouv))
		$heure_ferm+=24;

	for ($j=intval($heure_ouv);$j<=intval($heure_ferm);$j++){
		for ($min=0;$min<=1;$min++){
			echo "<tr>";
			if ($j>23)
				$heure_affichee="0".($j-24);
			else $heure_affichee=$j;
			if ($min==0)
				echo "<td rowspan=\"2\"  bgcolor=\"#CCCCCC\" style=\"border: 1px solid black;\" height=\"30\" width=\"30\" valign=\"top\" align=\"center\">".$heure_affichee."</td>";
			for ($i=1;$i<=$nbre_terrain;$i++){
				list($texte,$rowspan)=explode("##",$tab_terrains[$min][$j][$i]);
				if (test_non_vide($rowspan) and $rowspan>0)
					echo "<td rowspan=\"".$rowspan."\"  bgcolor=\"#EAEAEA\"  style=\"border: 1px solid black;\">".$texte."</td>";
				if (!test_non_vide($rowspan))
					echo "<td  style=\"border: 1px solid black;\"  height=\"15\"> </td>";
			}
			echo "</tr>";
		}
	}	
	echo "</table>";

}
}

?>