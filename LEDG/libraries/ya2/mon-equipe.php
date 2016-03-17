<?php

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();
?>
<script type="text/javascript">
	
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
				}
			}
	}
	
</script>
<?
/*if (est_capitaine($user->id)) {
	if (test_non_vide($_GET["id_joueur_modif_email"]) and $user->id==capitaine_equipe(equipe_du_joueur($_GET["id_joueur_modif_email"]))){
		echo "<H3>Modifier l'email du joueur : ".prenom_user($_GET["id_joueur_modif_email"])."</H3><br>"
			."<FORM name=\"form\" class=\"submission box\" action=\"mon-equipe\" method=post >";
			
		echo "<input name=\"email_modif_joueur\" placeholder=\"email\"  type=\"text\" />";
		echo "<input name=\"id_modif_joueur\"  type=\"hidden\" value=\"".$_GET["id_joueur_modif_email"]."\"/>";
		
		echo "<input name=\"valide\" type=\"submit\"  value=\"Modifier l'email du joueur\" ></form><hr>";
	}
	if (test_non_vide($_POST["email_modif_joueur"])){
		
		if (verif_si_email_existe_deja($_POST["email_modif_joueur"])){
			echo "<font color=red>Email d&eacute;j&agrave; utilis&eacute;e.</font><br><br><br>";
			$existe_erreur++;
		}
		else{
			if (VerifierAdresseMail($_POST["email_modif_joueur"])){
				$pass_du_joueur=gen_pass();		
				if (test_non_vide($_POST["id_modif_joueur"])){
					maj_email_user($_POST["email_modif_joueur"],$_POST["id_modif_joueur"],$pass_du_joueur);
					mail_inscription_au_site(prenom_user($_POST["id_modif_joueur"]),$_POST["email_modif_joueur"],$pass_du_joueur,equipe_du_joueur($user->id,0,1),"joueur",$_POST["id_modif_joueur"]);
				}
			}
			else echo "Erreur : <font color=red>Email incorrecte.</font><br><br><br>";
		}
	}
	
}*/
if (test_non_vide($_GET["id_joueur_suppr"]))
	supprimer_joueur($_GET["id_joueur_suppr"]);

if (isset($_POST["email_new_joueur"]))
	$email_joueur=$_POST["email_new_joueur"];
if (isset($_POST["prenom_new_joueur"]))
	$prenom_joueur=$_POST["prenom_new_joueur"];

if (isset($_POST["email_joueur"]))
	$email_joueur=$_POST["email_joueur"];
if (isset($_POST["prenom_joueur"]))
	$prenom_joueur=$_POST["prenom_joueur"];
if (isset($_POST["id_joueur"]))
	$id_joueur=$_POST["id_joueur"];

if (est_capitaine($user->id) and isset($email_joueur)){
	$existe_erreur=0;
	
	if (!test_non_vide($email_joueur) or !test_non_vide($prenom_joueur) or $prenom_joueur=="prenom" or $email_joueur=="email" ) {
		echo "<font color=red>Le prenom et l'email sont obligatoires.</font><br><br><br>";
		$existe_erreur++;
	}
	
	if (verif_si_email_existe_deja($email_joueur)){
		echo "<font color=red>Email d&eacute;j&agrave; utilis&eacute;e.</font><br><br><br>";
		$email_joueur="email";
		$existe_erreur++;
	}
	
	if ($existe_erreur==0){
		if (VerifierAdresseMail($email_joueur)){
			if (VerifierNom($prenom_joueur)){
				
				$pass_du_joueur=gen_pass();
				
				if (isset($id_joueur)){
					$id_du_new_user=$id_joueur;
					$comp_req1=" id, ";
					$comp_req2=" ".$id_joueur.", ";
				}
				if (!isset($id_joueur))
					$id_du_new_user=ajout_user($prenom_joueur,$email_joueur,$pass_du_joueur,$comp_req1,$comp_req2);
				
				if (test_non_vide($id_du_new_user)){
					ajout_user_au_groupe($id_du_new_user);
					
					if (isset($id_joueur))
						maj_user_du_joueur($id_du_new_user,$id_joueur);
					else {
						ajout_joueur($prenom_joueur,$id_du_new_user,equipe_du_joueur($user->id,1,1));
						$liste_saisons=saison_en_cours_de_l_equipe($user->id);
						
						foreach ($liste_saisons as $saison)
							ajout_joueurs_dans_saison(equipe_du_joueur($user->id),$id_du_new_user,$saison->sid);
					}					
					mail_inscription_au_site($prenom_joueur,$email_joueur,$pass_du_joueur,equipe_du_joueur($user->id,0,1),"joueur",$id_du_new_user);
				}
				else echo "<font color=red>Pb insertion du joueur.</font><br><br><br>";
				
			}
			else echo "Erreur : <font color=red>Prenom incorrect.</font><br><br><br>";
		}
		else echo "Erreur : <font color=red>Email incorrecte.</font><br><br><br>";
	}
}
if (!test_saisie($user->id)){
$liste_joueurs = liste_joueurs_d_une_equipe(equipe_du_joueur($user->id,1,1));

echo "<table class=\"zebra\">";
	
echo "<thead><tr><th align=\"center\">Photo</th>"
	."<th align=\"center\">Flocage</th>"
	."<th align=\"center\">Prenom</th>"
	."<th align=\"center\">Email</th>"
	."<th align=\"center\">Dernier<br>Acc&egrave;s</th>";
if (est_capitaine($user->id))
	echo "<th align=\"center\">Action</th>";
echo "</tr></thead>";
$nbre_joueurs=0;
$mails_equipe="";
foreach ($liste_joueurs as $joueur ){
		
	echo "<tr><td >";
        echo "<img src=\"media/bearleague/".photo_user($joueur->id)."\" width=\"30\" height=\"38\" >";
	echo "</td><td >";
	echo $joueur->nick;
	echo "</td><td >";
        echo $joueur->first_name;
	echo "</td><td >"; 
        if (test_non_vide($joueur->email))
		echo $joueur->email;
	else {
		if (est_capitaine($user->id)){
			echo "<FORM name=\"form_".$joueur->id."\" class=\"submission box\" action=\"mon-equipe\" method=post >";
			echo "<input type=\"hidden\" name=\"id_joueur\" value=\"".$joueur->id."\">";
			echo "<input type=\"hidden\" name=\"prenom_joueur\" value=\"".$joueur->first_name."\">";
			echo "<input name=\"email_joueur\" ";
			if (test_non_vide($_POST["email_joueur"]))
				if (VerifierAdresseMail($_POST["email_joueur"]))
					echo " type=\"hidden\" value=\"".$_POST["email_joueur"]."\">".$_POST["email_joueur"];
				else echo " type=\"text\" value=\"".$_POST["email_joueur"]."\">";
			else echo " type=\"text\" value=\"email\">";
			echo "<input name=\"valide\" type=\"submit\"  value=\"Envoyer ses identifiants par email &agrave; ".$joueur->first_name."\" ></form>";
		}
		
	}

	echo "</td><td >";		
	if ($joueur->lastvisitDate<>"0000-00-00 00:00:00")
		echo inverser_date(substr($joueur->lastvisitDate,0,10));
	else echo "jamais connect&eacute;";
	echo "</td><td >";
	if (!test_non_vide(saisons_en_cours_avec_buts($joueur->id)) and est_capitaine($user->id) and !est_capitaine($joueur->id))
		echo "<a onclick=\"recharger('Confirmez la suppression de ce joueur','mon-equipe?id_joueur_suppr=$joueur->id')\""
			."title=\"supprimer ce joueur\" ><img src=\"images/stories/supprimer.png\" ></a>";
	if (!est_capitaine($joueur->id) and est_capitaine($user->id))
		echo " <a onclick=\"recharger('Modifier son email ?','mon-equipe?id_joueur_modif_email=$joueur->id')\""
			."title=\"Modfier le mail de ce joueur\" ><img src=\"images/stories/modif-email-icon.png\" ></a>";
	echo "</td></tr>";
	$mails_equipe.=$joueur->email.";";
	$nbre_joueurs++;
		
}
echo "</table><br />";

echo "Ton &eacute;quipe est constiu&eacute;e de ".$nbre_joueurs." joueurs.";// Ton capitaine peut encore ajouter ".(12-$nbre_joueurs)." joueur(s)";

/*if ((12-$nbre_joueurs)>0 and est_capitaine($user->id)){
	echo "<br><br><H3>Ajouter un joueur</H3>Un joueur ajout&eacute; ne pourra pas &ecirc;tre remplac&eacute; par la suite.<br><br>"
		."<FORM name=\"form\" class=\"submission box\" action=\"mon-equipe\" method=post >";
		
	echo "<input type=\"text\" name=\"prenom_new_joueur\" placeholder=\"prenom\" value=\"";
	
	if (test_non_vide($_POST["prenom_new_joueur"]))
	    echo $_POST["prenom_new_joueur"];
	
	echo "\"><input name=\"email_new_joueur\" placeholder=\"email\" ";
	
	if (test_non_vide($_POST["email_new_joueur"]))
		if (VerifierAdresseMail($_POST["email_new_joueur"]))
			echo " type=\"hidden\" value=\"".$_POST["email_new_joueur"]."\">".$_POST["email_new_joueur"];
		else echo " type=\"text\" value=\"".$_POST["email_new_joueur"]."\">";
	else echo " type=\"text\" >";
	
	echo "<input name=\"valide\" type=\"submit\"  value=\"Envoyer ses identifiants par email au nouveau joueur\" ></form>";
}*/
}
else header("Location: ../ep");

?>
