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
	
	function valider() {
		document.filtrer.submit();

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
	
if (test_non_vide($_POST["date_fin"])) $date_fin=$_POST["date_fin"];
else $date_fin=$_GET["date_fin"];

if (test_non_vide($_POST["date_debut"])) $date_debut=$_POST["date_debut"];
else $date_debut=$_GET["date_debut"];


if (test_non_vide($_POST["etat_user_0"])) $etat_user_0=$_POST["etat_user_0"];
else $etat_user_0=$_GET["etat_user_0"];

if (test_non_vide($_POST["etat_user_1"])) $etat_user_1=$_POST["etat_user_1"];
else $etat_user_1=$_GET["etat_user_1"];


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
				if (test_non_vide($_POST["group_id"])){
					maj_groupe_du_user($_GET["modif_user"],$_POST["group_id"]);
					header("Location: index.php/component/content/article?id=68");
				}
				else {
					echo "<FORM name=\"form\" class=\"submission box\" action=\"".JRoute::_( 'index.php/component/content/article?id=68&modif_user='.$_GET["modif_user"].'')."\" method=post >";
					echo "Nouveau mot de passe <input name=\"new_pass_agent\" type=\"text\">";
					echo "<br>Droits de l'agent <select name=\"group_id\" >";
						echo "<option value=3 ";
							if ($_GET["group_id"]==3)
								echo " selected ";
						echo ">Agent</option>";
						echo "<option value=6 ";
							if ($_GET["group_id"]==6)
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
			echo "<FORM name=\"form\" class=\"submission box\" action=\"".JRoute::_( 'index.php/component/content/article?id=68')."\"  method=post >";
				echo "Prenom <input name=\"ajout_user\" type=\"text\">";
				echo "Mot de passe <input name=\"password\" type=\"text\">";
			echo "<br><input name=\"valide\" type=\"submit\"  value=\"Creer\" ></form>";
		}
		else {
			
				
			echo " <a href=\"index.php/component/content/article?id=68&ajout_user=1\">";
			echo "<img src=\"images/ajout-agent-fif-icon.png\" title=\"Ajouter un nouvel agent\"></a><br><br>";
				
			$liste_agents_actifs="SELECT * FROM #__users as u, #__user_usergroup_map as ugm where ugm.user_id=u.id "
				." and ugm.group_id>2 and ugm.group_id<8 and block=0 ORDER BY username";
				
			$db->setQuery($liste_agents_actifs );
			$resultat_agents_actifs = $db->loadObjectList();
			
			
			$liste_agents_non_actifs="SELECT * FROM #__users as u, #__user_usergroup_map as ugm where ugm.user_id=u.id "
				." and ugm.group_id>2 and ugm.group_id<8 and block=1 ORDER BY username";
				
				
			$db->setQuery($liste_agents_non_actifs );
			$resultat_agents_non_actifs = $db->loadObjectList();	
			
			?>
			<table width="100%">
				<tr>
					<td  align="center" valign="top">
						<table class="zebra">
							<tr>
								<th colspan="2">Actifs</th>
							</tr>
							<?
							foreach ($resultat_agents_actifs as $actifs){
								echo "<tr><td><a onClick=\"recharger('Voulez-vous vraiment desactiver cet agent ?'"
									.",'../../article?id=68&block=1&modif_user=".$actifs->id."')\">"
									."<img src=\"images/valid-agent-fif-icon.png\" title=\"Agent actif\"/></a> "
									."<a href=\"index.php/component/content/article?id=68&group_id=".$actifs->group_id."&modif_user=".$actifs->id."\" />"
									.$actifs->username."</a></td>";
									
								echo "<td>";
									switch ($actifs->group_id){
										case '3'	: echo "Agent";break;
										case '6'	: echo "Gerant";break;
										default	: echo "Inconnu";break;	
									}
								echo "</td></tr>";
							}
							?>
							
						</table>
					</td>
					<td width="50%">&nbsp;</td>
					<td align="center" valign="top">
						<table class="zebra">
							<tr>
								<th colspan="2" >Bloqu&eacute;s</th>
							</tr>
							<?
							foreach ($resultat_agents_non_actifs as $non_actifs){
								echo "<tr><td><a onClick=\"recharger('Voulez-vous vraiment activer cet agent ?'"
									.",'../../article?id=68&block=0&modif_user=".$non_actifs->id."')\">"
									."<img src=\"images/block-agent-fif-icon.png\" title=\"Agent bloqu&eacute;\"></a> "
									."<a href=\"index.php/component/content/article?id=68&group_id=".$non_actifs->group_id."&modif_user=".$non_actifs->id."\" />"
									.$non_actifs->username."</a></td>";
									
								echo "<td>";
									switch ($non_actifs->group_id){
										case '3'	: echo "Agent";break;
										case '6'	: echo "Gerant";break;
										default	: echo "Inconnu";break;	
									}
								echo "</td></tr>";
							}
							?>
						</table>
					</td>
				</tr>
			</table><hr>
			<?
				
				
			$requete_con_user="SELECT * FROM Connect as c,#__users as u, #__user_usergroup_map as ugm where ugm.user_id=u.id "
				." and c.id_user=u.id and ugm.group_id>2 and ugm.group_id<8 ";
			
			if (test_non_vide($date_debut)) {

				if (test_non_vide($date_fin)) {
					
					$requete_con_user.=" and c.date>=\"".$date_debut."\" ";
					$requete_con_user.=" and c.date<=\"".$date_fin."\" ";
				}
				else {
					if (strpos($date_debut,"/")==2)
						list($jour, $mois, $annee) = explode('/', $date_debut);
					else  list($annee, $mois, $jour) = explode('-', $date_debut);
				
					$date_en_get=$annee."-".$mois."-".$jour;
					
					$requete_con_user.=" and DAYOFMONTH(c.date)=".$jour;
					$requete_con_user.=" and MONTH(c.date)=".$mois;
					$requete_con_user.=" and year(c.date)=".$annee;
				}
			}
			else {
				if (est_register($user)) 
					$requete_con_user.=" and c.date>=\"".date("Y-m-d")."\" ";
			}
			
			if (test_non_vide($etat_user_0))
				$requete_con_user.=" and u.id=".$etat_user_0." ";
			
			if (test_non_vide($etat_user_1))
				$requete_con_user.=" and u.id=".$etat_user_1." ";
			
			
			$requete_con_user.=" ORDER BY date DESC,heure DESC";
			
			
			
			//echo $requete_con_user;
			
			$db->setQuery($requete_con_user );
			$resultat_con_user = $db->loadObjectList();
			
				$i=0;
				$jour="";
			
				
				?>
				<FORM id="formulaire" name="filtrer" class="submission box" action="<?php echo JRoute::_('?id=68');?>" method="post" >
					Du <input type="date" name="date_debut" value="<? echo $date_debut;?>"> au 
					<input type="date" name="date_fin" value="<? echo $date_fin;?>">
					Actifs <? menu_deroulant_des_users(0,$etat_user_0);?> Desactiv&eacute;s <? menu_deroulant_des_users(1,$etat_user_1);?>
					<input name="valide" type="button"  value="Filtrer" onclick="valider()"><br><br>
				</FORM>
				<?
				if (test_non_vide($date_debut) or test_non_vide($date_fin)){
					foreach ($resultat_con_user as $con_user){
				
						if ($i==0) echo "<table class=zebra border=1><tr><td colspan=5 align=center>".date_longue($con_user->date)."</td></tr>"
							   ."<th>Nom</th><th>connection</th><th>IP</th>";
						else if ($jour<>$con_user->date)
							   echo "</table><br /><br /><table class=zebra border=1><tr><td colspan=5 align=center>".date_longue($con_user->date)."</td></tr>"
							   ."<th>Nom</th><th>connection</th><th>IP</th>";
		
						echo "<tr><td><a href=\"index.php/component/content/article?id=68&group_id=".$con_user->group_id."&modif_user=".$con_user->id."\" />".$con_user->username."</a></td>";
						
						echo "<td>".Ajout_zero_si_absent($con_user->heure)."</td>"
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