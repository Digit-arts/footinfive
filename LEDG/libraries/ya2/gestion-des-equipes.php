<?php


defined('_JEXEC') or die( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

?>
<script type="text/javascript">
	
	function recharger2() {
		document.nouveau_capitaine.submit();
	}
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
					else document.register_versement.submit();
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

if (isset($_POST["liste_tourn"]) and $_POST["liste_tourn"]<>0)
        list($id_tourn,$id_saison,$id_groupe,$nbre_equipes_du_tourn,$nbre_equipes_par_tourn)=explode('_',$_POST["liste_tourn"]);

if (VerifierNom($_POST["prenom_new_joueur"]) and test_non_vide($_GET["id_equipe"])){
				$id_du_new_user=ajout_user($_POST["prenom_new_joueur"],"agent@footinfive.com",gen_pass());
				
				if (test_non_vide($id_du_new_user)){
					ajout_user_au_groupe($id_du_new_user);
					ajout_joueur($_POST["prenom_new_joueur"],$id_du_new_user,$_GET["id_equipe"]);
						$liste_saisons=saison_en_cours_de_l_equipe($_POST["id_capitaine"]);
						
						foreach ($liste_saisons as $saison)
							ajout_joueurs_dans_saison($_GET["id_equipe"],$id_du_new_user,$saison->sid);
				}
				else echo "<font color=red>Pb insertion du joueur.</font><br><br><br>";
				header("Location: gestion-des-equipes?id_equipe=".$_GET["id_equipe"]." ");
}


foreach ($user->groups as $groups)
    $user_group=$groups;
    

    
if ($user_group==3 or $user_group==8 ){

	if (test_non_vide($_GET["suppr_equipe"]))
		supprimer_equipe($_GET["suppr_equipe"]);
		
	if (test_non_vide($_POST["new_cap"])){
		$id_equipe_min=id_table_min_max("#__bl_teams","id","min");
		$id_equipe_max=id_table_min_max("#__bl_teams","id","max");
		$id_joueur_min=id_table_min_max("#__bl_players","id","min");
		$id_joueur_max=id_table_min_max("#__bl_players","id","max");
		//echo $id_equipe_min."--".$id_equipe_max."--".$id_joueur_min."--".$id_joueur_max;
		for($id_equipe_temp=$id_equipe_min;$id_equipe_temp<=$id_equipe_max;$id_equipe_temp++)
			for($id_joueur_temp=$id_joueur_min;$id_joueur_temp<=$id_joueur_max;$id_joueur_temp++) 
				if (test_non_vide($_POST["$id_equipe_temp"]) and $id_joueur_temp==$_POST["$id_equipe_temp"])
					ajout_capitaine($id_equipe_temp,$id_joueur_temp);
		
	}
	if (test_non_vide($_POST["ajout_equipe"])){
	    if (test_non_vide($_POST["ajout_prenom_capitaine"])){
		    if (test_non_vide($_POST["ajout_email_capitaine"])){
			    if (VerifierAdresseMail($_POST["ajout_email_capitaine"])){
				    if (verif_si_email_existe_deja($_POST["ajout_email_capitaine"])==0){
					    //if (test_non_vide($id_saison) and $id_saison>0){
						$id_equipe=ajout_equipe($_POST["ajout_equipe"]);
						if ($id_equipe>0){
							$pass_du_capitaine=gen_pass();
							$prenom_capitaine=$_POST["ajout_prenom_capitaine"];
							$email_capitaine=$_POST["ajout_email_capitaine"];
					    
							$id_du_new_user=ajout_user($prenom_capitaine,$email_capitaine,$pass_du_capitaine);
							ajout_user_au_groupe($id_du_new_user);
							$id_joueur=ajout_joueur($prenom_capitaine,$id_du_new_user,$id_equipe);
							if (ajout_capitaine($id_equipe,$id_joueur))
								mail_inscription_au_site($prenom_capitaine,$email_capitaine,$pass_du_capitaine,$_POST["ajout_equipe"],"capitaine",$id_du_new_user);
							//echo "Un mail a &eacute;t&eacute; envoy&eacute; au capitaine pour qu'il invite ses joueurs<br><br>";
							echo "L'equipe \"".$_POST["ajout_equipe"]."\" a &eacute;t&eacute; cr&eacute;&eacute;e avec succ&egrave;s<br><br>";	
								/*if (test_non_vide($id_du_new_user)){
									
									
									ajout_equipe_dans_saison($id_saison,$id_equipe);
									ajout_equipe_dans_groupe($id_groupe,$id_equipe);
									ajout_joueurs_dans_saison($id_equipe,$id_joueur,$id_saison);
									
							    }
							    else echo "<font color=red>Pb insertion du capitaine.</font><br><br><br>";*/
						}
						else echo "<b>Pb:</b> Equipe \"".$_POST["ajout_equipe"]."\" n'est pas cr&eacute;&eacute;e<br><br>";
						    
					    //}
					    //else echo "Vous devez selectionner un tournoi<br>";
				    }
				    else echo "<b>Pb:</b> Email capitaine existe d&eacute;j&agrave;<br><br>";
			    }
			    else echo "<b>Pb:</b> Email capitaine incorrecte<br><br>";
		    }
		    else echo "<b>Pb:</b> Email capitaine obligatoire<br><br>";
	    }
	    else echo "<b>Pb:</b> Prenom capitaine obligatoire<br><br>";
	}

if (test_non_vide($_POST["new_change_nom_equipe"])){
	
	if (exist_nom_equipe($_POST["new_change_nom_equipe"],$_POST["id_change_nom_equipe"])==false){
		$query ="UPDATE #__bl_teams SET t_name=\"".$_POST["new_change_nom_equipe"]."\" where id=".$_POST["id_change_nom_equipe"];
		
		$db->setQuery($query);
		$db->query();
	}else echo "<font color=red>Ce nom d'equipe existe d&eacute;j&agrave;</font>";
	
	if (test_non_vide($_POST["new_email_capitaine"]) and test_non_vide($_POST["new_prenom_capitaine"])){
		if (VerifierAdresseMail($_POST["new_email_capitaine"])){
			if (verif_si_email_existe_deja($_POST["new_email_capitaine"])==0){
				$pass_du_capitaine=gen_pass();
				$prenom_capitaine=$_POST["new_prenom_capitaine"];
				$email_capitaine=$_POST["new_email_capitaine"];
				supprimer_joueur(capitaine_equipe($_POST["id_change_nom_equipe"]));			    
				$id_du_new_user=ajout_user($prenom_capitaine,$email_capitaine,$pass_du_capitaine);
				ajout_user_au_groupe($id_du_new_user);
				$id_joueur=ajout_joueur($prenom_capitaine,$id_du_new_user,$_POST["id_change_nom_equipe"]);
				if (ajout_capitaine($_POST["id_change_nom_equipe"],$id_joueur))
					mail_inscription_au_site($prenom_capitaine,$email_capitaine,$pass_du_capitaine,$_POST["new_change_nom_equipe"],"capitaine",$id_du_new_user);
				
			}else echo "<font color=red>Email existe d&eacute;j&agrave;</font>";
		}else echo "<font color=red>Adresse email incorrecte</font>";
			
	}
}
echo "<FORM name=\"nouvelle_equipe\"  class=\"submission box\" action=\"gestion-des-equipes\" method=post >";
	
	if (!test_non_vide($_GET["id_change_nom_equipe"])){	
		echo "Ajouter une &eacute;quipe<input type=text name=ajout_equipe placeholder=\"Nom equipe\" value=\"".$_POST["ajout_equipe"]."\">"
			."<input type=text name=ajout_prenom_capitaine placeholder=\"Prenom capitaine\"  value=\"".$_POST["ajout_prenom_capitaine"]."\">"
			."<input type=text name=ajout_email_capitaine placeholder=\"Email capitaine\"  value=\"\">";
		    
		/*echo "<select name=\"liste_tourn\" onChange=\"recharger('','')\">";
		echo "<option value=\"\" SELECTED DISABLED>tournoi</option>";
		foreach (liste_tourn(" and s.s_name=\"Championnat\" ") as $liste_tourn) {
			$nbre_equipes_par_groupe=(nbre_equipes_dans_saison($liste_tourn->id_saison)/nbre_groupes_dans_saison($liste_tourn->id_saison));
			if ($liste_tourn->nbre_equipes<$nbre_equipes_par_groupe){
				echo "<option value=\"".$liste_tourn->id_tourn."_".$liste_tourn->id_saison."_".$liste_tourn->gid
					."_".$liste_tourn->nbre_equipes."_".$nbre_equipes_par_groupe."\" ";
				if (($id_tourn."_".$id_groupe)==($liste_tourn->id_tourn."_".$liste_tourn->gid))
					echo " SELECTED ";
			    
				echo ">".$liste_tourn->nom_tourn." (".$liste_tourn->group_name.")</option>";
			    }
		}
	
		echo "</select>";*/
		$bouton="Cr&eacute;er";
	}
	else {
		echo "Nouveau nom de l'&eacute;quipe<input type=text name=new_change_nom_equipe  value=\"".nom_equipe($_GET["id_change_nom_equipe"])."\" >"
			."<input type=text name=new_prenom_capitaine   placeholder=\"New prenom capitaine\">"
			."<input type=text name=new_email_capitaine   placeholder=\"New Email capitaine\">"
			."<input type=hidden name=id_change_nom_equipe   value=\"".$_GET["id_change_nom_equipe"]."\">";
		$bouton="Modifier";
	    
	}
echo "<input name=\"valide\" type=\"submit\"  value=\"".$bouton."\" ></form><hr>";
	

$total_versement_des_equipes=0;
$nbre_equipes=0;
echo "<FORM name=\"nouveau_capitaine\"  class=\"submission box\" action=\"gestion-des-equipes\" method=post >";
echo "<input type=\"hidden\" name=\"new_cap\" value=\"1\">";

foreach (liste_equipes_du_groupe() as $liste_equipes) {
	if (!test_non_vide($_GET["id_equipe"]) or (test_non_vide($_GET["id_equipe"]) and $_GET["id_equipe"]==$liste_equipes->id_equipe)){
	    echo "<font size=5><a href=\"index.php/accueil/gestion-des-equipes?id_equipe=$liste_equipes->id_equipe\"/>".$liste_equipes->nom_equipe."</a></font> ";
		
		if (nbre_match_d_une_equipe($liste_equipes->id_equipe)==0 and (test_non_vide($_GET["id_equipe"]) and $_GET["id_equipe"]==$liste_equipes->id_equipe))
			echo " <a title=\"Supprimer cette equipe\" onclick=\"recharger('Confirmez la suppression de lequipe','gestion-des-equipes?suppr_equipe=$liste_equipes->id_equipe')\">"
				."<img src=\"images/stories/supprimer.png\" ></a>";
	}
	if (test_non_vide($_GET["id_equipe"]) and $_GET["id_equipe"]==$liste_equipes->id_equipe)
		echo " <a title=\"changer le nom de cette equipe\" onclick=\"recharger('Confirmez cette action','gestion-des-equipes?id_change_nom_equipe=".$liste_equipes->id_equipe."')\">"
				."<img src=\"images/stories/changer_nom_equipe.png\" ></a>";
	
	$tab_joueurs="<br><table class=\"zebra\">";
	    
	$tab_joueurs.="<thead><tr><th align=\"center\">Photo</th>"
			."<th align=\"center\">Prenom</th>"
			."<th align=\"center\">Email</th>"
			."<th align=\"center\">R&egrave;glements</th>"
			."<th align=\"center\">FIF</th></tr></thead>";
	$liste_joueurs = liste_joueurs_d_une_equipe($liste_equipes->id_equipe,1);
	$total_versement_equipe=0;
	$nbre_equipes++;
	$select_capitaine="<select name=\"".$liste_equipes->id_equipe."\" onChange=\"recharger2()\"><option SELECTED></option>";
	foreach ($liste_joueurs as $joueur ){
		$tab_joueurs.="<tr><td >";
		$tab_joueurs.="<img src=\"media/bearleague/".photo_user($joueur->id)."\" width=\"30\" height=\"38\" >";
		$tab_joueurs.="</td><td ><a href=\"index.php/ep?id_joueur=".$joueur->id."\" >";
		$tab_joueurs.=$joueur->first_name." (".$joueur->nick.") </a>";
		$select_capitaine.="<option value=\"".$joueur->id."\" ";
		if (est_capitaine($joueur->id)){
			$id_capitaine=$joueur->id;
		    $tab_joueurs.=" <font color=red><b>(C)</b></font>";
		    $select_capitaine.=" SELECTED ";
		}
		$select_capitaine.=">".$joueur->first_name." (".$joueur->nick.")</option>";
		$tab_joueurs.="</td><td >"; 
		if (test_non_vide($joueur->email))
			$tab_joueurs.=$joueur->email;
		$tab_joueurs.="</td><td >";
		$versements=str_replace(",","",number_format(str_replace(",","",versements_sans_remise_et_avec_validation($joueur->id,date("Y"),date("m"))),2));
		$total_versement_equipe+=$versements;
		$tab_joueurs.="<a href=\"index.php/accueil/gestion-des-reglements?id_joueur=".$joueur->id."\" alt=\"voir les reglements de ce joueur\">";
		if (test_non_vide($versements))
			$tab_joueurs.=$versements."&euro;";
		else $tab_joueurs.="<img src=\"images/stories/coin-add-icon.png\" title=\"Ajouter un reglement\">";
		$tab_joueurs.="</a></td ><td><a href=\"http://footinfive.com/FIF/index.php/component/content/article?id=60&id_client="
			.recup_id_client_fif($joueur->id)."\" target=_blank><img src=\"images/stories/Fiche-client-icon.png\" title=\"La fiche  de ce client\"></a></td></tr>";
			    
	}
	
	$select_capitaine.="</select>";
		
	$tab_joueurs.="<tr><td  colspan=\"2\">";
	$tab_joueurs.="Capitaine ".$select_capitaine;

	$tab_joueurs.="</td><td  align=\"right\"><b>Total &eacute;quipe</b></td><td><b>".$total_versement_equipe."&euro;</b></td><td></td></tr>";
	$tab_joueurs.="</table>";
	$total_versement_des_equipes+=$total_versement_equipe;

	if (test_non_vide($_GET["id_equipe"]) and $_GET["id_equipe"]==$liste_equipes->id_equipe)
		echo $tab_joueurs;
	if (!test_non_vide($_GET["id_equipe"])) {
		if ($total_versement_equipe>0)
			echo "<font size=4 color=red> - ".$total_versement_equipe."&euro;</font>";
		echo "<br>";
	}
}
}
if (!test_non_vide($_GET["id_equipe"]))
	echo "<center><font size=4 color=yellow><b>".$nbre_equipes." equipes - Total des versements : ".$total_versement_des_equipes."&euro;</b></font></center></form>";
else {
	echo "</form><FORM name=\"form\" class=\"submission box\" action=\"gestion-des-equipes?id_equipe=".$_GET["id_equipe"]."\" method=post >";
		
	echo "<br>Ajouter un joueur <input type=\"text\" name=\"prenom_new_joueur\" placeholder=\"prenom\" value=\"";
	
	if (test_non_vide($_POST["prenom_new_joueur"]))
	    echo $_POST["prenom_new_joueur"];
	
	echo "\"><input name=\"id_capitaine\"  type=\"hidden\" value=\"".$id_capitaine."\" >";
	
	echo "<input name=\"valide\" type=\"submit\"  value=\"Enregistrer\" ></form>";
	
}
?>








