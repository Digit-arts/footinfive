<?php
require_once ('fonctions_gestion_user.php');
require_once ('fonctions_module_reservation.php');
?>
<script type="text/javascript">
	
	function enregistrer() {
		document.register_user.submit()
	}
</script>

<?
$db = & JFactory::getDBO();
$user =& JFactory::getUser();


$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {
	
$cal = connection_a_google_cal();

if (test_non_vide($_GET["id_client"])){

	$requete_recup_resas="select r.id_client as le_client, r.commentaire as com_resa, r.*, t.id_cal_google, c.* from Reservation as r, ";
	$requete_recup_resas.="  Terrain as t, Client c where c.id_client=r.id_client and r.id_terrain=t.id and r.id_client=".$_GET["id_client"];
	$requete_recup_resas.=" and r.adresse_resa_google=\"\" ";
	echo "<br>req1: ".$requete_recup_resas."<br>";
	$db->setQuery($requete_recup_resas);
	$db->query();
	$nbre_resas=$db->getNumRows();
	
	
	

	if ($nbre_resas>0){
		
		$resultats_recup_resas = $db->loadObjectList();
		foreach ($resultats_recup_resas as $recup_resas){
			$corps="Créneau réservé par ".$user->name." (id:".$user->id.") ";
			$corps.="\nDate validation resa : ".date_longue()." à ".date("H")."h".date("i")."";
			$corps.="\n\nDate location : ".date_longue($recup_resas->date_debut_resa)." à ".$recup_resas->heure_debut_resa;
			$corps.="\nMontant location : ".$recup_resas->montant_total." euros";
			$corps.="\n\nCommentaire : \n".$recup_resas->com_resa;
			
			
			$adresse_cal_google="http://www.google.com/calendar/feeds/".$recup_resas->id_cal_google."/private/full";
			$corps="RESA WEB\n\nhttp://footinfive.com/FIF/index.php/component/content/article?id=61&id_resa=".$recup_resas->id_resa."\n\n".$corps;
			echo $corps."<br><br>";
			// $rdv=ajout_event_cal_google($recup_resas->id_resa,$cal,$corps,$user,$recup_resas->le_client,$recup_resas->com_resa,$recup_resas->id_terrain,$recup_resas->date_debut_resa,$recup_resas->date_fin_resa,$recup_resas->heure_debut_resa,$recup_resas->heure_fin_resa,$adresse_cal_google);
			
			///////////////////
			// on met à jour la resa avec l'adresse du rdv	
			$requete_maj_resa="UPDATE `Reservation` set adresse_resa_google=\"".$rdv->getEditLink()->href."\" where id_resa=".$recup_resas->id_resa;
			echo "<br>req2: ".$requete_maj_resa."<br><br>";
			// $db->setQuery($requete_maj_resa);	
			// $resultat_maj_resa = $db->query();
		}
	}
	else echo "pas de resas sans id_cal_google vide";
}
}
?>