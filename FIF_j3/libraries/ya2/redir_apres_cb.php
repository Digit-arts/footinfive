<?

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_recup_redir="SELECT id_client,page_retour FROM Client where page_retour like \"".$user->id."#R%\" or page_retour like \"".$user->id."#C%\" ";
	$db->setQuery($requete_recup_redir);	
	echo $requete_recup_redir;
	
	$infos_retour=$db->loadObject();
	
	
	list($user_id,$ref,$Montant,$date_transac,$code_retour,$num_auto)=explode('#',$infos_retour->page_retour);
	$id_client=$infos_retour->id_client;
	$type=substr($ref, 0, 1);
	$id=substr($ref, 1);
	
	echo $infos_retour->page_retour;
	
	if ($type=="R") {
		$requete_recup_id_resa="SELECT id_reservation FROM Reglement where id_reglement=".$id;
		$db->setQuery($requete_recup_id_resa);
		$id_resa=$db->loadResult();
		
		$var="id_resa=".$id_resa;
		$art=64;
		
	}
	else{
		$var="id_client=".$id_client;
		$art=65;
		if (est_min_agent($user)) 
			maj_cautionnement_des_resas($id_client,1);
	}

	if ($code_retour<>"Annulation"){
		$corps=texte_paiement($id_client,$Montant,$date_transac);
		$objet="Confirmation paiement par CB : ".$num_auto;
		if (!sendMail($id_client,$objet,$corps))
			echo "\n<p><center>D&eacute;sol&eacute;, nous avons eu un probl&egrave;me lors de l'&eacute;mission du mail</center></p>\n";
	}
	else {
		$requete_RAZ_TimeOut="UPDATE Reservation set`date_valid`=\"".date("Y-m-d")."\", `heure_valid`=\"".Ajout_zero_si_absent(date("H:i"))."\" where id_resa=".$id_resa;
		$db->setQuery($requete_RAZ_TimeOut);
		$db->Query();
	}
	
	$requete_maj_client="UPDATE  Client set page_retour=NULL  where id_client=".$id_client;
	$db->setQuery($requete_maj_client);	
	
	$db->Query();
	
	
	header("Location: index.php/component/content/article?id=".$art."&ok=".$_GET["ok"]."&".$var."");
?>
