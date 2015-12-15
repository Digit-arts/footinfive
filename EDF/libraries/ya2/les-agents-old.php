<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');
?>
<script>
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
<?php

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {
	
menu_acces_rapide($id_client,"Gestion des agents");


if (est_min_manager($user)){
	if (test_non_vide($_POST["ajout_user"]) and test_non_vide($_POST["password"])){
		$user_id = ajout_user($_POST["ajout_user"],$_POST["ajout_user"],$_POST["password"]);														
		ajout_user_au_groupe($user_id,3);
		header("Location: index.php/component/content/article?id=68");
	}

	if (test_non_vide($_GET["modif_user"])){
		
		if (test_non_vide($_POST["new_pass_agent"])){
			maj_user($_GET["modif_user"],"","",$_POST["new_pass_agent"]);
			header("Location: index.php/component/content/article?id=68");
		}
		else {
			if (test_non_vide($_GET["block"])){
				$requete_modif_block="UPDATE  #__users set `block`=".$_GET["block"]." where id=".$_GET["modif_user"];
				$db->setQuery($requete_modif_block);	
				$db->query();
				header("Location: index.php/component/content/article?id=68");
			}
			else {
				if (test_non_vide($_POST["gid"])){
					maj_groupe_du_user($_GET["modif_user"],$_POST["gid"]);
					header("Location: index.php/component/content/article?id=68");
				}
				else {
					echo "<FORM name=\"form\" class=\"submission box\" action=\"index.php/component/content/article?id=68&modif_user=".$_GET["modif_user"]."\" method=post >";
					echo "Nouveau mot de passe <input name=\"new_pass_agent\" type=\"text\">";
					echo "<br>Droits de l'agent <select name=\"gid\" >";
						echo "<option value=3 ";
							if ($_GET["gid"]==3)
								echo " selected ";
						echo ">Agent</option>";
						echo "<option value=6 ";
							if ($_GET["gid"]==6)
								echo " selected ";
						echo " >Archive</option>";
						echo "<option value=8 ";
							if ($_GET["gid"]==8)
								echo " selected ";
						echo " >Gerant</option>";
					echo "</select>";
					echo "<br><input name=\"valide\" type=\"submit\"  value=\"Changer\" ></form>";
				}
			}
		}
		
	}
	else {
		if (test_non_vide($_GET["ajout_user"])){
			echo "<FORM name=\"form\" class=\"submission box\" action=\"index.php/component/content/article?id=68&modif_user=".$_GET["modif_user"]."\" method=post >";
				echo "Prenom <input name=\"ajout_user\" type=\"text\">";
				echo "Mot de passe <input name=\"password\" type=\"text\">";
			echo "<br><input name=\"valide\" type=\"submit\"  value=\"Creer\" ></form>";
		}
		else {
			$requete_con_user="SELECT *, concat(year(lastvisitDate),\"-\", month(lastvisitDate),\"-\",day(lastvisitDate)) as date, ";
			$requete_con_user.="concat(hour(lastvisitDate)+1,\":\", minute(lastvisitDate)) as heure ";
			$requete_con_user.=" FROM #__users, #__user_usergroup_map where user_id=id and group_id>2 and group_id<8 ORDER BY lastvisitDate DESC";
			
			//echo $requete_con_user;
			
			$db->setQuery($requete_con_user );
			$resultat_con_user = $db->loadObjectList();
			
			if (!$resultat_con_user) echo $prb;
			else {
				$i=0;
				$jour="";
				
				
				echo " <a href=\"index.php/component/content/article?id=68&ajout_user=1\">";
				echo "<img src=\"images/ajout-agent-fif-icon.png\" title=\"Ajouter un nouvel agent\"></a><br><br>";
				
				foreach ($resultat_con_user as $con_user){
			
					if ($i==0) echo "<table class=zebra border=1><tr><td colspan=5 align=center>".date_longue($con_user->date)."</td></tr>"
						   ."<th>Nom</th><th>droits</th><th>Etat</th><th>connection</th><th>IP</th>";
					else if ($jour<>$con_user->date)
						   echo "</table><br /><br /><table class=zebra border=1><tr><td colspan=5 align=center>".date_longue($con_user->date)."</td></tr>"
						   ."<th>Nom</th><th>droits</th><th>Etat</th><th>connection</th><th>IP</th>";
	
					echo "<tr><td><a href=\"index.php/component/content/article?id=68&gid=".$con_user->gid."&modif_user=".$con_user->id."\" />".$con_user->username."</a></td>";
					
					echo "<td>";
					switch ($con_user->group_id){
						case '3'	: echo "Agent";break;
						case '6'	: echo "Gerant";break;
						default	: echo "Inconnu";break;	
					}
					echo "</td><td>";
					if ($con_user->block==1){
						echo " <a onClick=\"recharger('Voulez-vous vraiment activer cet agent ?'";
						echo ",'index.php/component/content/article?id=68&block=0&modif_user=".$con_user->id."')\">";
						echo "<img src=\"images/block-agent-fif-icon.png\" title=\"Agent bloqu&eacute;\"></a>";
					}
					else{
						echo " <a onClick=\"recharger('Voulez-vous vraiment desactiver cet agent ?'";
						echo ",'index.php/component/content/article?id=68&block=1&modif_user=".$con_user->id."')\">";
						echo "<img src=\"images/valid-agent-fif-icon.png\" title=\"Agent actif\"/></a>";
					}
					echo "</td><td>".Ajout_zero_si_absent($con_user->heure)."</td>"
						."<td>";
					if (test_non_vide($con_user->id))
						echo recup_derniere_ip($con_user->id,$con_user->date);
						
					if (recup_derniere_ip($con_user->id,$con_user->date)=="109.1.98.236")
						echo " (FIF) ";
					echo "</td></tr>";
					
					$jour=$con_user->date;
					$i++;
				}
				echo "</table><br />";
			}
		}
	}
}
}
?>