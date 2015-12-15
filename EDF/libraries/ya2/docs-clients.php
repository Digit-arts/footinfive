<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');
?>	
<script type="text/javascript">
	
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

<?
$user =& JFactory::getUser();
$db = & JFactory::getDBO();


$etat_actuel_acces_apllication=acces_application();

if (($etat_actuel_acces_apllication==1 and est_register($user))
    or ($etat_actuel_acces_apllication==2 and est_agent($user))
    or ($etat_actuel_acces_apllication==3 and est_manager($user)))
	echo "<font color=red>Acc&egrave;s ferm&eacute; pour le moment...</font>";
else {

if (test_non_vide($_POST["id_client"])) $id_client = $_POST["id_client"];
if (est_min_agent($user)){
	if (test_non_vide($_POST["id_client"])) $id_client=$_POST["id_client"];
	else $id_client=$_GET["id_client"];
} else $id_client=idclient_du_user();

menu_acces_rapide($id_client);

if (is_array($_FILES['doc'])){
	$uploaddir = '/var/www/vhosts/footinfive.com/httpdocs/EDF/libraries/ya2/DOCS_CLIENTS/';
	
	foreach ($_FILES["doc"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["doc"]["tmp_name"][$key];
		$name = $_FILES["doc"]["name"][$key];
		if (!nom_fichier_existe($name)){
			if (!is_file($uploaddir."$name")){
                            move_uploaded_file($tmp_name, $uploaddir."$name");
                            ajout_doc($id_client,$_POST["Nom_doc"],$name);
                        }
		}
                else echo "<font color=red>Ce nom de fichier existe deja...</font><br><br>";
	    }
	}
}


if (est_min_agent($user) and test_non_vide($_GET["id_doc"])) {

	echo "<font color=red>";
	if (maj_validation_doc(0,$_GET["id_doc"])) {
		echo "document supprim&eacute;<br>";
	}
	else echo "Erreur : document inexistant";
	echo "</font>";
}


echo "<table class=\"zebra\" >";
	echo "<tr><th>Nom du document</th><th>Fichier</th>";
	echo "</tr><tr>";
	?><form name="register" enctype="multipart/form-data" class="submission box" action="article?id=69&id_client=<? echo $id_client;?>" method="post"  >
	<?
	    echo "<td>";
		echo "<input type=\"hidden\" name=\"id_client\" maxlength=\"30\" size=\"12\" value=\"".$id_client."\">";
                echo "<input type=\"text\" name=\"Nom_doc\" maxlength=\"30\" size=\"12\" value=\"";
		if (test_non_vide($_POST["Nom_doc"]))
		    echo $_POST["Nom_doc"];
		echo "\">";
	    echo "</td>";
		
	    echo "<td>";
		echo "<input type=\"file\" name=\"doc[]\" />";
	    echo "</td>";
						
	    echo "<td >";
            
		?><input name="valide" type="button" value="Ajouter" onclick="recharger('Confirmez ce document','')"><?
	    echo "</td>";
	echo "</tr>";
	echo "</form>";
echo "</table><br>";


$requete_liste_doc="Select d.*, (select name from #__users where id=d.id_user) as nom_user "
    		." FROM `Document` as d Where d.id_client=".$id_client;
//echo $requete_liste_doc;
$db->setQuery($requete_liste_doc);
$db->query();
$nbre_docs=$db->getNumRows();
				
				
if ($nbre_docs>0) {
    echo "<table class=\"zebra\" >";
	echo "<tr><th>Effectuer par</th><th>Date</th><th>Nom doc</th><th>Nom fichier</th><th>suppr</th></tr>";
	    $resultat_liste_docs= $db->loadObjectList();
	    foreach($resultat_liste_docs as $liste_doc){
		echo "<tr>";
		    echo "<td>";
			echo $liste_doc->nom_user;
		    echo "</td>";
		    echo "<td>".date_longue($liste_doc->date_ajout)." &agrave; ".$liste_doc->heure_ajout." </td>";
		    echo "<td>";
			echo $liste_doc->nom_doc;
		    echo "</td>";
		    echo "<td><a href=\"libraries/ya2/DOCS_CLIENTS/$liste_doc->nom_fichier\" target=_blank>";
			echo $liste_doc->nom_fichier;
		    echo "</a></td>";
		    echo "<td>";
			if  ($liste_doc->validation_doc==0) {
			    echo " <img src=\"images/Cancel-resa.png\" title=\"document supprim&eacute;e\">";
			    if (test_non_vide($_GET["reactiver_doc"]) and ($_GET["reactiver_doc"]==$liste_doc->id_doc)){
									
				$resultat_reactiver_doc= maj_validation_doc(1,$_GET["reactiver_doc"]);
				header("Location: article?id=69&id_client=".$id_client."");
			    }
			    else {
				if (est_min_agent($user))
				    echo "<a href=\"article?id=69&reactiver_doc=".$liste_doc->id_doc."&id_client=".$id_client."\" />reactiver</a>";
			    }
			}
			else {
			    if (est_min_agent($user))	{
				echo " <a onClick=\"recharger('Voulez-vous supprimer ce document ?'";
				echo ",'article?id=69";
				echo "&id_client=".$id_client."&id_doc=".$liste_doc->id_doc."')\">";
				echo "<img src=\"images/doc-suppr-icon.png\" title=\"supprimer ce document\">";
			    }
			}
		    echo "</td>";
		    echo "</tr>";
	    }
    echo "</table><br>";
}
		
}
?>