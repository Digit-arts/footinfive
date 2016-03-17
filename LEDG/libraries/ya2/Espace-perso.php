<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();
?>
<script type="text/javascript">
	
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
					//else document.register_versement.submit();
				}
			}
			else {
				if (lien!='') document.location.href=lien;
				else {
					document.form.submit();
				}
			}
	}
	
</script>
<?

if (est_agent($user)){
	$le_user_id=$_GET["id_joueur"];
}
else $le_user_id=$user->id;

if (test_non_vide($_GET["id_joueur"]) or !(est_agent($user)) ){
$equipe_user=equipe_du_joueur($le_user_id);
$modif=0;

if (is_array($_FILES['doc'])){
	$uploaddir = '/var/www/vhosts/footinfive.com/httpdocs/LEDG/media/bearleague/';
	
	foreach ($_FILES["doc"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["doc"]["tmp_name"][$key];
		$name = $_FILES["doc"]["name"][$key];
		
		if (!is_file($uploaddir."$name")){
                            $name=$le_user_id."-".$name;
                            if (move_uploaded_file($tmp_name, $uploaddir."$name")){
                                ajout_photo_user($name,$le_user_id);
				$modif=1;
                            }
                }
                else echo "<font color=red>Ce nom de fichier existe deja...</font><br><br>";
	    }
	}
}

if (test_non_vide($_GET["suppr_photo"])){
	supprimer_photo_joueur($le_user_id);
	$modif=1;
}


if (test_non_vide($_POST["mobile1_saisie"]) or test_non_vide($_POST["nom_saisie"])){
	
	if ((isset($_POST["nom_saisie"]) and (!(VerifierNom($_POST["nom_saisie"])) or $_POST["nom_saisie"]=="" ))
	    or (isset($_POST["mobile1_saisie"]) and (!(VerifierNumMob($_POST["mobile1_saisie"])) or $_POST["mobile1_saisie"]=="" ))){
		if (isset($_POST["nom_saisie"]) and ((!(VerifierNom($_POST["nom_saisie"])) or $_POST["nom_saisie"]=="" )))
			echo "<font color=red>Le nom est incorrect.<br></font>";
		if (isset($_POST["mobile1_saisie"]) and ((!(VerifierNumMob($_POST["mobile1_saisie"]))or $_POST["mobile1_saisie"]=="" )))
			echo "<font color=red>Numero de Tel mobile incorrect.<br></font>";
	}
	else {
		ajout_info_fif($le_user_id,$_POST["nom_saisie"],$_POST["mobile1_saisie"]);
		$modif=1;		
	}

}

if (test_non_vide($_POST["nick"]) or test_non_vide($_POST["prenom"])){
	maj_joueur($_POST["prenom"],$_POST["nick"],$le_user_id);
	$modif=1;
}

for($i=1;$i<=30;$i++)
    if (test_non_vide($_POST["select_fid_$i"])){
        ajout_info_suppl_user($_POST["select_fid_$i"],$i,$le_user_id);
	$modif=1;
    }
    

if (est_agent($le_user_id) and $modif==1)
	header("Location: index.php/accueil/gestion-des-equipes?id_equipe=".$equipe_user."");
  
// COMPTE
if (1){//forcer_saisie($le_user_id)){
$resultat_nbre_photo_user = nbre_photo_user($le_user_id);
echo "<FORM id=formulaire name=formulaire action=\"ep?id_joueur=".$le_user_id."\" method=post>";
echo "<table class=\"zebra\">";
	
echo "<tr><th>Equipe</th><td>".equipe_du_joueur($le_user_id,0,1)."</td>";
echo "<td rowspan=5 align=center valign=center><img src=\"media/bearleague/".photo_user($le_user_id)."\" width=100 height=125 >";
//if (test_non_vide(photo_user($le_user_id)) and est_agent($user))
	echo " <a title=\"Supprimer la photo\" onclick=\"recharger('Confirmez la suppression de votre photo','ep?suppr_photo=1&id_joueur=".$le_user_id."')\">"
		."<img src=\"images/stories/supprimer.png\" ></a>";
echo "</td></tr>";
$input=0;
echo "<tr><th>Pr&eacute;nom</th><td>";
if (!est_agent($user) and test_non_vide(prenom_user($le_user_id)))
    echo prenom_user($le_user_id);
else {
    echo "<input type=\"text\" name=\"prenom\" value=\"".prenom_user($le_user_id)."\">";
    $input++;
}
"</td></tr>";

echo "<tr><th>Surnom au dos du maillot</th><td>";
/*if (!est_agent($user) and test_non_vide(flocage_user($le_user_id)))
    echo flocage_user($le_user_id);
else {*/
    echo "<input type=\"text\" name=\"nick\" value=\"".flocage_user($le_user_id)."\">";
    $input++;
//}
echo "</td></tr>";

echo "<tr><th>Num au dos du maillot</th><td>";
/*if (!est_agent($user) and test_non_vide(champs_suppl_select_du_user($le_user_id,4)))
    echo champs_suppl_select_du_user($le_user_id,4);
else {*/
    menu_deroulant_extra_select(4,champs_suppl_select_du_user($le_user_id,4,1));//$_POST["select_fid_4"]
    $input++;
//}
echo "</td></tr>";

echo "<tr><th>Date Naissance</th><td>";
if (!est_agent($user) and test_non_vide(champs_suppl_select_du_user($le_user_id,5)))
    echo champs_suppl_select_du_user($le_user_id,5);
else {
    menu_deroulant_extra_select(5,champs_suppl_select_du_user($le_user_id,5,1));//$_POST["select_fid_5"]
    $input++;
}
echo "</td></tr>";

if (test_non_vide($_GET["id_joueur"])){
	echo "<tr><th>Email</th><td><input name=\"email_modif_joueur\" placeholder=\"nouvel email\"  type=\"text\" /></td></tr>";
	
	if (test_non_vide($_POST["email_modif_joueur"]) and $_POST["email_modif_joueur"]<>recup_1_element("email","#__users","id",$le_user_id)){
		
		if (verif_si_email_existe_deja($_POST["email_modif_joueur"]))
			echo "<font color=red>Email d&eacute;j&agrave; utilis&eacute;e.</font><br><br><br>";
		else{
			if (VerifierAdresseMail($_POST["email_modif_joueur"])){
				$pass_du_joueur=gen_pass();		
				maj_email_user($_POST["email_modif_joueur"],$_GET["id_joueur"],$pass_du_joueur);
			}
			else echo "Erreur : <font color=red>Email incorrecte.</font><br><br><br>";
		}
	}
}

if ($input>0)
    echo "<tr><th> </th><td><input name=\"valide\" type=\"submit\" value=\"Enregistrer mes infos\"></td>"
		."<td><a href=\"http://footinfive.com/FIF/index.php/component/content/article?id=60&modif=1&id_client="
			.recup_id_client_fif($_GET["id_joueur"])."\" target=_blank><img src=\"images/stories/Fiche-client-icon.png\" title=\"La fiche  de ce client\"></a></td></tr>";

echo "</table>";
echo "</form>";

if ($resultat_nbre_photo_user>0){
    echo "<br /><font color=red>Attention</font> : tu n&apos;as pas mis ta photo sur le site, "
        ." ton &eacute;quipe risque de voir une de ses rencontres perdue &agrave; cause de toi "
        ." en cas de r&eacute;clamation de l&apos;&eacute;quipe adverse.<br />La r&eacute;gle est la suivante :"
        ." La photo sur la feuille de match permet d&apos;identifier les joueurs "
        ." (1 joueur = 1 maillot, toute l ann&eacute;e).<br />";
        ?><form name="register" enctype="multipart/form-data" class="submission box" action="ep?id_joueur=<?echo $le_user_id;?>" method="post"  >
	<?
	    echo "<input type=\"file\" name=\"doc[]\" />";

	    ?><input name="valide" type="submit" value="Ajouter ma photo" ><?
	echo "</form>";
}

$jour_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$mois_fr = array("Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");



// Info presence

$query = "SELECT m.m_time as heure, m.m_date as jour_match ,t.t_name as nom_equipe_adv, t.id, m.team1_id, m.team2_id FROM `#__bl_match` as m, #__bl_teams as t   ";
$query .= " where t.id<>".$equipe_user ." and (t.id=m.`team1_id` or t.id=m.`team2_id`) and ";
$query .= " m.`published`=1 and (m.`team1_id`=".$equipe_user."  or m.`team2_id`=".$equipe_user.")  and ";
$query .= " TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))<=10080 and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))>0 ";
$db->setQuery($query);
$resultat_equipe_adv = $db->loadObjectList();

if (empty($resultat_equipe_adv)) echo "<h3>Info pr&eacute;sence</h3>Aucun match n'est pr&eacute;vu pour dimanche prochain.";
else {

    foreach ($resultat_equipe_adv as $equipe_adv ){
    
        $query = "SELECT u.email, p.id, p.first_name as Prenom, p.nick,(select es.id from #__bl_extra_select as es, #__bl_extra_values as ev where es.fid=13 and p.id=ev.uid and es.id=ev.fvalue) as vote "
            ." FROM  #__bl_players as p LEFT OUTER JOIN #__users u ON p.usr_id = u.id, #__bl_teams as t "
            ." where t.id=".$equipe_user." and t.id in (select pt.team_id FROM #__bl_players_team as pt Where p.id=pt.player_id and pt.season_id in (2,3,5,6)) "
            ." and p.nick<>\"CSC\" order by p.id";
        //echo $query;
        $db->setQuery($query);
        $vote_presence = $db->loadObjectList();
        
        
        list( $annee, $mois,$jour) = explode('-',$equipe_adv->jour_match);
        $date_longue = mktime (0, 0, 0, $mois, $jour, $annee);
        
        echo "<br><hr><h3>Info pr&eacute;sence</h3><a href=\"index.php/ep/info-presence\" >Match contre ".$equipe_adv->nom_equipe_adv." pr&eacute;vu le ".$jour_fr[date("w", $date_longue)]." ".$jour." ".$mois_fr[date("n", $date_longue)-1]." ".$annee." &agrave; ".$equipe_adv->heure."<br />";
        
        $requete_liste_etat="SELECT * FROM #__bl_extra_select where fid=13 ORDER BY id";
        
        $db->setQuery($requete_liste_etat);	
        $resultat_liste_etat = $db->loadObjectList();
        
                $present=0;$absent=0;$present=0;$indecis=0;$depannage=0;$blesse=0;$malade=0;$pasrepondu=0;
                $rep=JURI::base();
                foreach ($vote_presence as $presence ){
                        
                        switch ($presence->vote){
                                                                        
                        case '185':	$present++;break;
                        case '186':	$absent++;break;
                        case '187':	$indecis++;break;	
                        case '188':	$depannage++;break;		
                        case '189':	$blesse++;break;
                        case '190':	$malade++;break;
                        default:	$pasrepondu++;break;
                        }
        
                }
        echo "<font size=\"5\">";
        if ($present>0)
            echo $present." <img src=\"".JURI::base()."images/stories/present.png\" title=\"pr&eacute;sent\" /> - ";
        if ($absent>0)
            echo $absent." <img src=\"".JURI::base()."images/stories/absent.png\" title=\"absent\" /> - ";
        if ($indecis>0)
            echo $indecis." <img src=\"".JURI::base()."images/stories/indecis.png\" title=\"ind&eacute;cis\"/> - ";
        if ($depannage>0)
            echo $depannage." <img src=\"".JURI::base()."images/stories/depannage.png\" title=\"d&eacute;panneur\"/> - ";
        if ($blesse>0)
            echo $blesse." <img src=\"".JURI::base()."images/stories/blesse.png\" title=\"bless&eacute;\" /> - ";
        if ($malade>0)
            echo $malade." <img src=\"".JURI::base()."images/stories/malade.png\" title=\"malade\" /> - ";
        if ($pasrepondu>0)
            echo $pasrepondu." <img src=\"".JURI::base()."images/stories/pas-repondu.png\" title=\"pas encore r&eacute;pondu\" />";
        
        echo "</font><br /><br /></a>";

    }
}

// Les Points FAIRPLAY

$query = "SELECT m.m_time as heure, m.m_date as jour_match ,t.t_name as nom_equipe_adv, t.id, m.team1_id, m.team2_id "
        ." FROM `#__bl_match` as m, #__bl_teams as t where t.id<>".$equipe_user ." and (t.id=m.`team1_id` or t.id=m.`team2_id`) "
        ." and m.`published`=1 and (m.`team1_id`=".$equipe_user."  or m.`team2_id`=".$equipe_user.")  "
        ." and TIMESTAMPDIFF(MINUTE,CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)),NOW())<=10080 "
        ." and TIMESTAMPDIFF(MINUTE,CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)),NOW())>0";

$db->setQuery($query);
$resultat_equipe_adv = $db->loadObjectList();

if (empty($resultat_equipe_adv)) echo "<h3>Vote point FairPlay</h3>Aucun match n&apos;a &eacute;t&eacute; jou&eacute; dimanche dernier.<br /><hr>";
else {

foreach ($resultat_equipe_adv as $equipe_adv ){

$query = "SELECT u.email, p.id, p.first_name as Prenom, p.nick,(select es.sel_value from #__bl_extra_select as es, #__bl_extra_values as ev "
    ." where es.fid=10 and p.id=ev.uid and es.id=ev.fvalue) as vote  "
    ." FROM  #__bl_players as p LEFT OUTER JOIN #__users u ON p.usr_id = u.id, #__bl_teams as t "
    ." where  t.id=".$equipe_user." and t.id in (select pt.team_id FROM #__bl_players_team as pt Where p.id=pt.player_id and pt.season_id in (2,3,5,6)) "
    ." and p.nick<>\"CSC\" order by p.id";

$db->setQuery($query);
$vote_joueurs = $db->loadObjectList();

list( $annee, $mois,$jour) = explode('-',$equipe_adv->jour_match);
$date_longue = mktime (0, 0, 0, $mois, $jour, $annee);

        $pour=0;
	$contre=0;
	$rep=JURI::base();
	foreach ($vote_joueurs as $joueur){
		
		if (($joueur->vote=="") or ($joueur->vote==1))	$pour++;
		if ($joueur->vote=="0") $contre++;
	}
        echo "<br><hr><h3>Vote point FairPlay</h3>";
        
echo "<a href=\"index.php/ep/point-fairplay\" >Match contre ".$equipe_adv->nom_equipe_adv." du ".$jour_fr[date("w", $date_longue)]." ".$jour." ".$mois_fr[date("n", $date_longue)-1]." ".$annee." &agrave; ".$equipe_adv->heure."";
echo "<br /><font size=\"5\"> <img src=\"".$rep."images/stories/icon-fairplay.png\"  title=\"Accorder le point FairPlay\" /> ".$pour." - ";
        echo $contre." <img src=\"".$rep."images/stories/icon-no-fairplay.png\" title=\"Refuser le point FairPlay\" /></font></a><hr>";
}
}


}
}

			
?>