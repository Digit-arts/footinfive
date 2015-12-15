<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');
?>	
<script type="text/javascript">
	
	function enregistrer() {
		document.ajout_eleve.submit()
	}
	function enregistrer2() {
		document.ajout_cotisation.submit()
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

	if (test_non_vide($_POST["c_id"])) $c_id=$_POST["c_id"];
	else $c_id=$_GET["c_id"];
	
	if (!test_non_vide($c_id)) echo "Num&eacute;ro de commande absent...";
	else {
		$nom_champ="ajout_eleve_".$c_id;
		if (test_non_vide($_POST["$nom_champ"]))
			ajout_eleve_commande($_POST["$nom_champ"],$c_id);
				
		if (test_non_vide($_GET["suppr_commande_eleve"]))
			suppr_commande_eleve($_GET["id_client"],$c_id);
			
		if (test_non_vide($_GET["suppr_recept_eleve_commande"]))
			maj_reception_commande_eleve($_GET["id_client"],$c_id,true);
			
		if (test_non_vide($_GET["maj_recept_eleve_commande"]))
			maj_reception_commande_eleve($_GET["id_client"],$c_id);
				
		$requete_info_commande="SELECT u_val.name as prenom_val,u_rec.name as prenom_rec,cl.prenom,cl.nom,cl.id_client,
					date_validation as date_val, heure_validation as heure_val, id_user_validation,
					date_reception as date_rec, heure_reception as heure_rec, id_user_reception
				FROM `Commande_client` as cc LEFT JOIN Client as cl on cl.id_client=cc.id_client
				LEFT JOIN #__users as u_val on u_val.id=cc.id_user_validation
				LEFT JOIN #__users as u_rec on u_rec.id=cc.id_user_reception
				LEFT JOIN Commande as com on cc.`id_commande`=com.`id` WHERE cc.id_commande=".$c_id."
				order by date_reception,heure_reception, date_validation , heure_validation ";
		
		//echo $requete_info_commande;
		
		$db->setQuery($requete_info_commande);	
		$info_commande= $db->loadObjectList();
		
		$requete_detail_commande="SELECT c.*,u_crea.name FROM Commande as c LEFT JOIN #__users as u_crea on u_crea.id=c.id_user WHERE c.id=".$c_id;
		
		//echo $requete_detail_commande;
		
		$db->setQuery($requete_detail_commande);	
		$detail_commande= $db->loadObject();
		
		menu_acces_rapide("",$detail_commande->nom);
		echo "<u>num_commande:</u> ".$detail_commande->id." <u>Fournisseur:</u> ".$detail_commande->nom_fournisseur
			." - <u>Date cr&eacute;ation:</u> ".inverser_date($detail_commande->date_creation)
			." &agrave; ".$detail_commande->heure_creation." par ".$detail_commande->name."<br><hr><br>";
		echo "<table class=\"zebra\" >";
		echo "<th>Eleve</th><th>Ajout&eacute; par</th><th>R&eacute;ceptionn&eacute; par</th><th>Suppr</th></tr>";
		foreach($info_commande as $une_ligne_commande){
				
			echo "<tr>";
				echo "<td><a href=\"index.php/component/content/article?id=60&id_client=".$une_ligne_commande->id_client."\"/>
					".$une_ligne_commande->nom." ".$une_ligne_commande->prenom."</a></td>";
				echo "<td>";
				if ($une_ligne_commande->id_user_validation>0)
					echo $une_ligne_commande->prenom_val." le "
						.inverser_date($une_ligne_commande->date_val)." &agrave; ".$une_ligne_commande->heure_val;
				echo "</td><td>";
				if ($une_ligne_commande->id_user_reception>0)
					echo $une_ligne_commande->prenom_rec." le "
						.inverser_date($une_ligne_commande->date_rec)." &agrave; ".$une_ligne_commande->heure_rec;
				else echo "<a href=\"index.php/component/content/article?id=84&c_id=".$c_id."&id_client="
						.$une_ligne_commande->id_client."&maj_recept_eleve_commande=1\"/><center><img src=\"images/truck-icon.png\" "
						." title=\"En cours de livraison\"  /></center></a>";
				echo "</td><td>";
				if ($une_ligne_commande->id_user_reception>0)
					echo "<a href=\"index.php/component/content/article?id=84&suppr_recept_eleve_commande=1&c_id=".$c_id
						."&id_client=".$une_ligne_commande->id_client."\"/>"
						."<img src=\"images/supprimer.png\" title=\"Annuler la reception de la commande de cet eleve\"/></a>";
				else echo "<a href=\"index.php/component/content/article?id=84&suppr_commande_eleve=1&c_id=".$c_id
						."&id_client=".$une_ligne_commande->id_client."\"/>"
						."<img src=\"images/supprimer.png\" title=\"Retirer cet eleve de la commande\"/></a>";
				echo "</td>";
			echo "</tr>";
		
		}
		echo "</table>";
		?>
		<br><FORM id="formulaire" name="ajout_eleve" class="submission box" action="article?id=84&c_id=<? echo $c_id; ?>" method="post" >
		<?
		echo menu_deroulant_eleve("Commande_client","id_commande",$c_id)
			." <input name=\"valide\" type=\"button\"  value=\"Ajouter cet eleve\" onclick=\"enregistrer()\" />"
			."<hr><br></form>";
	}
}
?>