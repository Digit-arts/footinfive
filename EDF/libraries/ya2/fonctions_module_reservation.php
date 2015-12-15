<?php

function est_manager($user){

    if (is_integer($user)){
	if ($user==6)
	    return true;
	return false;
    }
    else{
	foreach ($user->groups as $paf)
	    if ($paf==6)
		return true;
	return false;
    }

}

function est_agent($user){

    if (is_integer($user)){
	if ($user==3)
	    return true;
	return false;
    }
    else{
	foreach ($user->groups as $paf)
	    if ($paf==3)
		return true;
	return false;
    }

}

function est_register($user){

    if (is_integer($user)){
	if ($user==2)
	    return true;
	return false;
    }
    else{
	foreach ($user->groups as $paf)
	    if ($paf==2)
		return true;
	return false;
    }

}

function est_min_manager($user){

    if (is_integer($user)){
	if ($user>=6)
	    return true;
	return false;
    }
    else{
	foreach ($user->groups as $paf)
	    if ($paf>=6)
		return true;
	return false;
    }

}

function est_min_agent($user){

    if (is_integer($user)){
	if ($user>=3)
	    return true;
	return false;
    }
    else{
	foreach ($user->groups as $paf)
	    if ($paf>=3)
		return true;
	return false;
    }

}

function est_min_register($user){

    if (is_integer($user)){
	if ($user>=2)
	    return true;
	return false;
    }
    else{
	foreach ($user->groups as $paf)
	    if ($paf>=2)
		return true;
	return false;
    }

}

function recup_derniere_ip($id_user,$date=""){
$db = & JFactory::getDBO();

   if ($date=="")
	$date=date("Y-m-d");
	
    $requete_recup_IP="Select IP from Connect where id_user=".$id_user." and date=\"".$date."\" order by heure desc LIMIT 0,1 ";
    $db->setQuery($requete_recup_IP);	
    return($db->LoadResult());
}

function max_tab($table,$id="id"){
$db = & JFactory::getDBO();
	
    $requete_max_tab="Select max(".$id.") from ".$table." where 1";
    $db->setQuery($requete_max_tab);	
    return($db->LoadResult());
}

function maj_connect($user,$IP){
$db = & JFactory::getDBO();

    $derniere_ip=recup_derniere_ip($user->id);

    if (!test_non_vide($derniere_ip) or (test_non_vide($derniere_ip) and $IP<>$derniere_ip)){
	$requete_maj_connect="INSERT INTO `Connect`(`id_user`, `date`, `heure`, `IP`) "
	    ." VALUES (".$user->id.",\"".date("Y-m-d")."\",\"".date("H:i")."\",\"".$IP."\") ";
	$db->setQuery($requete_maj_connect);	
	$db->Query();
    }

}

function recup_derniere_commentaire($champs,$valeur_champs){
$db = & JFactory::getDBO();

    $requete_recup_infos="select * from Commentaires  where  ".$champs."=".$valeur_champs." order by date desc, heure desc LIMIT 0,1 ";
    //echo $requete_recup_infos;
    $db->setQuery($requete_recup_infos);
    return($db->LoadObject());

}

function maj_commentaire($champs,$valeur_champs,$commentaire){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();

    $dernier_commentaire=recup_derniere_commentaire($champs,$valeur_champs);

    if ((!test_non_vide($dernier_commentaire->Commentaire) and test_non_vide($commentaire))
	or (test_non_vide($dernier_commentaire->Commentaire) and $dernier_commentaire->Commentaire<>$commentaire)){
	$requete_insert_commentaire="INSERT INTO `Commentaires`(`id_user`, `date`, `heure`, ".$champs.", `Commentaire`) "
			." VALUES (".$user->id.",\"".date("Y-m-d")."\",\"".date("H:i")."\",".$valeur_champs.",\"".$commentaire."\")";
		// echo $requete_insert_commentaire;
		$db->setQuery($requete_insert_commentaire);
		$resultat_maj_commentaire = $db->query();
    }

}




function ajout_user($prenom,$courriel,$password){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_insert_user="INSERT INTO #__users(name, username, email, password, usertype, block, sendEmail, registerDate, params, `resetCount`) ";
		$requete_insert_user.="VALUES (\"".premiere_lettre_maj($prenom)."\",Trim(\"".Trim($courriel)."\"),";
		$requete_insert_user.="Trim(\"".Trim($courriel)."\"),MD5(\"".$password."\"),\"\",\"0\",\"0\",";
		$requete_insert_user.="\"".date("Y-m-d")." ".date("H:i").":00\",\"\",0);";
		//echo "<br>".$requete_insert_user;				
		$db->setQuery($requete_insert_user);
		$db->query();

return ($db->insertid());

}

function ajout_user_au_groupe($user_id,$groupe){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

    $requete_insert_user_group="INSERT INTO #__user_usergroup_map (`user_id`, `group_id`)"
						."VALUES (".$user_id.",".$groupe.");";
    //echo "<br>".$requete_insert_user_group;
    $db->setQuery($requete_insert_user_group);
    $db->query();

}

function maj_groupe_du_user($user_id,$groupe){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

    $requete_maj_user_group="UPDATE #__user_usergroup_map SET `group_id`=".$groupe." where user_id=".$user_id;
    //echo "<br>".$requete_maj_user_group;
    $db->setQuery($requete_maj_user_group);
    $db->query();

}

function maj_user($id_user,$prenom="",$courriel="",$password=""){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();
    
    if (test_non_vide($prenom)){	
	$requete_update_user="UPDATE `#__users` SET `name`=\"".premiere_lettre_maj($prenom)."\" where id=".$id_user;
	//echo "<br>".$requete_update_user;				
	$db->setQuery($requete_update_user);
	$db->query();
    }
	
    if (test_non_vide($password)){	
	$requete_update_user="UPDATE `#__users` SET `password`=MD5(\"".$password."\") where id=".$id_user;
	//echo "<br>".$requete_update_user;				
	$db->setQuery($requete_update_user);
	$db->query();
    }
    
    if (test_non_vide($courriel)){	
	$requete_update_user="UPDATE `#__users` SET `username`=Trim(\"".Trim($courriel)."\"), `email`=Trim(\"".Trim($courriel)."\") where id=".$id_user;
	//echo "<br>".$requete_update_user;				
	$db->setQuery($requete_update_user);
	$db->query();
    }
		   

}



function idclient_du_user(){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_idclient_du_user="Select id_client from Client where id_user=".$user->id;
$db->setQuery($requete_idclient_du_user);	
return ($db->LoadResult());

}

function format_fr($montant){

    return (number_format($montant,2,"."," "));

}

function prenom_du_client($id_client){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_prenom_du_client="Select prenom from Client where id_client=".$id_client;
	$db->setQuery($requete_prenom_du_client);	
	return ($db->LoadResult());

}

function nom_du_client($id_client){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_nom_du_client="Select nom from Client where id_client=".$id_client;
	$db->setQuery($requete_nom_du_client);	
	return ($db->LoadResult());

}


function recup_1_element($nom_element,$table,$nom_id,$id){
$db = & JFactory::getDBO();

	$requete_recup_1_element="select ".$nom_element." FROM ".$table." where ".$nom_id."=\"".$id."\" LIMIT 0,1";
	//echo "<br>".$requete_recup_1_element;
	$db->setQuery($requete_recup_1_element);		
	return ($db->LoadResult());

}



function supprimer_1_element($table,$nom_id,$id){
$db = & JFactory::getDBO();

	$requete_supprimer_1_element="DELETE FROM ".$table." where ".$nom_id."=".$id;
	//echo "<br>".$requete_supprimer_1_element;
	$db->setQuery($requete_supprimer_1_element);		
	return ($db->Query());

}

function ajouter_1_element($table,$champs,$valeurs){
$db = & JFactory::getDBO();

	$requete_ajouter_1_element="INSERT INTO ".$table." (".$champs.") values (".$valeurs.")";
	//echo "<br>".$requete_ajouter_1_element;
	$db->setQuery($requete_ajouter_1_element);		
	return ($db->Query());

}

function maj_1_element($table,$champs_maj,$id){
$db = & JFactory::getDBO();

	$requete_maj_1_element="UPDATE ".$table." SET ".$champs_maj." Where id=".$id;
	//echo "<br>".$requete_maj_1_element;
	$db->setQuery($requete_maj_1_element);		
	return ($db->Query());

}



function recup_insee($code_insee="",$cp="",$ville=""){
$db = & JFactory::getDBO();

    if (test_non_vide($code_insee))
        $compl_req=" and code_insee=Trim(\"".Trim($code_insee)."\") ";
    
    if (test_non_vide($cp))
        $compl_req.=" and code_postal=Trim(\"".Trim($cp)."\") ";
    
    if (test_non_vide($ville))
        $compl_req.=" and nom_maj_ville=Trim(\"".mb_strtoupper(Trim($ville))."\")  ";
    

    $requete_recup_insee="SELECT  code_insee, code_postal, nom_maj_ville, departement FROM Ville where 1 ".$compl_req;
    $db->setQuery($requete_recup_insee);
    
    //echo $requete_recup_insee;
    
    return($db->loadObject());

}

        
function verif_si_cp_existe($cp){
$db = & JFactory::getDBO();

    $requete_verif_si_cp_existe="SELECT count(code_insee) FROM Ville where code_postal=Trim(\"".Trim($cp)."\");";
    $db->setQuery($requete_verif_si_cp_existe);		
    return($db->loadResult());
}

function trouve_ville($cp){
$db = & JFactory::getDBO();

    $requete_trouve_ville="SELECT code_insee, code_postal, nom_maj_ville, departement FROM Ville where ";
    $requete_trouve_ville.=" code_postal=Trim(\"".Trim($cp)."\");";
    $db->setQuery($requete_trouve_ville);		
    return($db->loadObjectList());
}

function trouve_cp($ville){
$db = & JFactory::getDBO();

    $requete_trouve_cp="SELECT code_insee, code_postal, nom_maj_ville, departement FROM Ville where ";
    $requete_trouve_cp.=" nom_maj_ville like \"%".Trim(mb_strtoupper($ville))."%\"  ;";
    //echo $requete_trouve_cp;
    $db->setQuery($requete_trouve_cp);		
	
    return($db->loadObjectList());
}

function verif_si_ville_existe($cp,$ville){
$db = & JFactory::getDBO();

    $requete_verif_si_ville_existe="SELECT code_insee FROM Ville where ";
    $requete_verif_si_ville_existe.="nom_maj_ville=Trim(\"".mb_strtoupper(Trim($ville))."\") ";
    $requete_verif_si_ville_existe.=" and code_postal=Trim(\"".Trim($cp)."\");";
    $db->setQuery($requete_verif_si_ville_existe);		
    return($db->loadResult());
}

function verif_cp_ville(&$code_insee,$cp,$ville,&$tab_villes,&$tab_cp,&$nbre_villes,&$nbre_cp,&$existe_erreur){
$db = & JFactory::getDBO();    
    		if (test_non_vide($cp) and !(VerifierCP($cp))){
			echo "<font color=red>Code postal incorrect.<br></font>";
			$existe_erreur++;
		}
		else {
			$resultat_verif_si_cp_existe =1;
			if (test_non_vide($cp)) 	
				$resultat_verif_si_cp_existe = verif_si_cp_existe($cp);
			
			if ($resultat_verif_si_cp_existe==0 or $resultat_verif_si_cp_existe=="") {
				echo "<font color=red>Code postal inconnu.<br></font>";
				$existe_erreur++;	
			}
			else {
				if ((test_non_vide($cp)) and !(test_non_vide($ville))){		
					$resultat_trouve_ville =trouve_ville($cp);
					$nbre_villes=0;
					foreach($resultat_trouve_ville as $ville_bdd) {
						$tab_villes[$nbre_villes]["CP"]=$ville_bdd->code_postal;
						$tab_villes[$nbre_villes]["INSEE"]=$ville_bdd->code_insee;
						$tab_villes[$nbre_villes]["VILLE"]=$ville_bdd->nom_maj_ville;
						$tab_villes[$nbre_villes]["DEP"]=$ville_bdd->departement;
						$nbre_villes++;
					}
					if ($nbre_villes==1) $code_insee=$tab_villes[0]["INSEE"];
					$existe_erreur++;
				}
				if (!(test_non_vide($cp)) and (test_non_vide($ville))){		
					$resultat_trouve_cp = trouve_cp($ville);
					$nbre_cp=0;
					foreach($resultat_trouve_cp as $ville_bdd) {
						$tab_cp[$nbre_cp]["CP"]=$ville_bdd->code_postal;
						$tab_cp[$nbre_cp]["INSEE"]=$ville_bdd->code_insee;
						$tab_cp[$nbre_cp]["VILLE"]=$ville_bdd->nom_maj_ville;
						$tab_cp[$nbre_cp]["DEP"]=$ville_bdd->departement;
						$nbre_cp++;
					}
					if ($nbre_cp==1) $code_insee=$tab_cp[0]["INSEE"];
					else if ($nbre_cp==0) echo "<font color=red>Ville inconnue.<br></font>";
					$existe_erreur++;	
				}
			}
		}
		
		if (test_non_vide($ville) and !(VerifierNomVille($ville))) {
			echo "<font color=red>Nom de ville incorrect.<br></font>";
			$existe_erreur++;
		}
		else {	
			$resultat_verif_si_ville_existe="";
			if ((test_non_vide($cp)) and (test_non_vide($ville)) ){	
				$resultat_verif_si_ville_existe = verif_si_ville_existe($cp,$ville);
		
				if ($resultat_verif_si_ville_existe=="") {
					echo "<font color=red>La ville et le code postal ne correspondent pas.<br></font>";
					$existe_erreur++;
				}
				else $code_insee=$resultat_verif_si_ville_existe;
				
			}
		}
}

function input_cp_ville (&$code_insee,$cp,$ville,&$nbre_cp,&$nbre_villes,&$tab_villes,&$tab_cp,$modif,$compl_nom="",$new=""){
$db = & JFactory::getDBO();

    if ($code_insee<>""){					
	$recup_insee = recup_insee($code_insee);
					
        $select_insee="";
        echo "<select name=\"code_insee".$compl_nom."\">";
    
        if ($code_insee==$recup_insee->code_insee)
            $select_insee=" selected ";
    
        echo "<option value=\"".$recup_insee->code_insee."\" ".$select_insee.">";
        echo $recup_insee->departement." (".$recup_insee->nom_maj_ville." - ".$recup_insee->code_postal.")</option>";
                                            
        echo "</select>";
    }

    else {
	$i=$nbre_cp;
        if (test_non_vide($new) and $new==2){
            $cp="";
            $ville="";
        }
	if ($nbre_villes<2){
	    switch ($i) {
		case 0	: {
                        if (isset($modif))
                            echo "<input type=\"text\"  name=\"cp".$compl_nom."\" size=\"2\" maxlength=\"5\" value=\"".$cp."\">";
                        else echo $cp." ";
                        break;
                }
		
                case 1 	: {
                        if (isset($modif))
                            echo "<input type=\"text\"  name=\"cp".$compl_nom."\" size=\"2\" maxlength=\"5\" value=\"".$tab_cp[0]["CP"]."\">";
                        else echo $tab_cp[0]["CP"]." ";
                        break;
                }
							
                case ($i>1) : 	{
                                    $select_insee="";
				    echo "<select name=\"code_insee".$compl_nom."\">";
				    while ($i>0){
					$i--;
					echo "<option value=\"".$tab_cp[$i]["INSEE"]."\" ".$select_insee.">";
					echo $tab_cp[$i]["DEP"]." (".$tab_cp[$i]["VILLE"]." - ".$tab_cp[$i]["CP"].")</option>";
				    }
				    echo "</select>";
				    break;
                }
            }
        }
	if ($nbre_cp<2){
	    $j=$nbre_villes;
	    switch ($j) {
		case 0	: {
                        if (isset($modif))
                            echo "<input type=\"text\"  name=\"ville".$compl_nom."\" size=\"29\" maxlength=\"100\" value=\"".$ville."\">";
                        else echo $ville;
                        break;
                }
                        
		case 1 	: {
                        if (isset($modif))
                            echo "<input type=\"text\"  name=\"ville".$compl_nom."\" size=\"29\" maxlength=\"100\" value=\"".$tab_villes[0]["VILLE"]."\">";
                        else echo $tab_villes[0]["VILLE"];
                        break;
                }

		case ($j>1) : 	{
                                    $select_insee="";
				    echo "<select name=\"code_insee".$compl_nom."\">";
				    while ($j>0){
					$j--;
					echo "<option value=\"".$tab_villes[$j]["INSEE"]."\" ".$select_insee.">";
					echo $tab_villes[$j]["DEP"]." (".$tab_villes[$j]["VILLE"]." - ".$tab_villes[$j]["CP"].")</option>";
				    }
				    echo "</select>";
				    break;
                }
            }
        }
    }
    
}



function recup_recup_client($compl_criteres,$id_client=""){
$db = & JFactory::getDBO();

	$requete_recup_client="select c.nom as nom_client, c.*,v.nom_maj_ville,v.code_postal,"
            ." (select u.name from #__users as u where u.id=c.id_user_modif) as name_user_modif,"
	    ." (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=c.id_user_modif) as group_user_modif,"
            ." (select u.email from #__users as u where u.id=c.id_user) as courriel FROM Client as c "
            ." LEFT JOIN Ville as v on c.code_insee=v.code_insee where 1 ".$compl_criteres." order by c.nom,prenom,c.code_insee";
	//echo "<br>".$requete_recup_client;
	$db->setQuery($requete_recup_client);		
	return ($db->loadObject());

}




function nbre_docs($id_client){
$db = & JFactory::getDBO();

	$requete_recup_nbre_docs="SELECT count(`id_doc`) as nbre_docs FROM `Document` where validation_doc=1 and id_client=".$id_client;
	//echo $requete_recup_nbre_docs;
	$db->setQuery($requete_recup_nbre_docs);		
	return ($db->LoadResult());

}

function nom_fichier_existe($fichier){
$db = & JFactory::getDBO();

	$requete_recup_nbre_docs="SELECT count(`id_doc`) as nbre_docs FROM `Document` where nom_fichier=\"".$fichier."\"";
	//echo $requete_recup_nbre_docs;
	$db->setQuery($requete_recup_nbre_docs);		
	if($db->LoadResult()>0)
		return (true);
	else return(false);

}

function nbre_reglements($id_cotisation){
$db = & JFactory::getDBO();

	$requete_recup_nbre_reglements="SELECT count(`id_reglement`) as nbre_regl FROM `Reglement` where validation_reglement=1 and id_cotisation=".$id_cotisation;
	//echo $requete_recup_nbre_reglements;
	$db->setQuery($requete_recup_nbre_reglements);		
	return ($db->LoadResult());

}


function ajout_doc($id_client,$nom_doc,$nom_fichier){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();

	$requete_ajout_doc="INSERT INTO `Document`(`nom_doc`, `nom_fichier`, `id_client`, `id_user`, `date_ajout`, `heure_ajout`) "
				." VALUES (\"".$nom_doc."\",\"".$nom_fichier."\",".$id_client.",".$user->id.",\"".date("Y-m-d")."\",\"".date("H:i")."\") ";
	//echo $requete_ajout_doc;
	$db->setQuery($requete_ajout_doc);		
	return($db->Query());

}


function maj_validation_reglement($validation_reglement,$id_regl,$id_cotisation=""){
$db = & JFactory::getDBO();
		
	if (test_non_vide($id_cotisation))
		$compl_req=" id_cotisation=".$id_cotisation;
	
	if (test_non_vide($id_regl))
		$compl_req=" id_reglement=".$id_regl;
	 
	$requete_maj_regl="UPDATE Reglement set  validation_reglement=".$validation_reglement." WHERE ".$compl_req;
	//echo "<br>reqsuppr: ".$requete_maj_regl;
	$db->setQuery($requete_maj_regl);	
		
	return ($db->query());

}


function maj_validation_doc($validation_doc,$id_doc,$id_client=""){
$db = & JFactory::getDBO();
		
	if (test_non_vide($id_client))
		$compl_req=" id_client=".$id_client;
	
	if (test_non_vide($id_doc))
		$compl_req=" id_doc=".$id_doc;
	 
	$requete_maj_doc="UPDATE Document set  validation_doc=".$validation_doc." WHERE ".$compl_req;
	//echo "<br>reqsuppr: ".$requete_maj_doc;
	$db->setQuery($requete_maj_doc);	
		
	return ($db->query());

}


function texte_paiement($id_client,$Montant,$date_transac){
$user =& JFactory::getUser();

$corps="Bonjour ".prenom_du_client($id_client).", (num client :".$id_client.")";
$corps.="\n\nVotre paiement par CB d'un montant de ".number_format((str_replace("EUR","",$Montant)/1.196),2)."EUR HT soit ".$Montant." TTC a &eacute;t&eacute; valid&eacute; le ".str_replace("_"," ",$date_transac).".";
$corps.="\n\n L'&eacute;quipe du Foot In Five vous remercie de votre confiance !\n A bientot sur nos terrains...";
$corps.="\n\nFOOT IN FIVE\n\n187 Route de Saint-Leu\n93800 Epinay-sur-Seine\n\nTel : 01 49 51 27 04";

return ($corps);
}




function recup_taux_TVA_d_une_date($date){
    
	$db = & JFactory::getDBO();
	
	$requete_recup_taux_TVA_d_une_date="SELECT taux_TVA FROM Tarif_periode WHERE "
							    ."  \"".$date."\"<=`date_fin_periode` and \"".$date."\">=`date_debut_periode` LIMIT 0,1 ";
	//echo "req88: ".$requete_recup_taux_TVA_d_une_date;
	
	$db->setQuery($requete_recup_taux_TVA_d_une_date);	
	$db->query();
        
        return($db->LoadResult());
	
}

function ajout_reglement($id_cotisation,$id_client,$montant,$moyen_paiement,$info="",$remise="",$validation=1,$clef=0){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();
	
	$requete_ajout_reglement="INSERT INTO `Reglement`(id_user,`id_cotisation`, `montant_reglement`, `date_reglement`,info,";
	$requete_ajout_reglement.=" `heure_reglement`, `id_moyen_paiement`, `id_type_reglement`, `taux_TVA`, `id_client`, `validation_reglement`,id_remise,Clef)";
	$requete_ajout_reglement.=" VALUES (".$user->id.",".$id_cotisation.",\"".str_replace(",", ".", $montant)."\",\"".date("Y-m-d")."\",\"".$info."\",";
	$requete_ajout_reglement.=" \"".date("H:i")."\",\"".$moyen_paiement."\",\"\",\"\",".$id_client.",".$validation.",\"".$remise."\",\"".$clef."\")";
	//echo "req88: ".$requete_ajout_reglement;
	$db->setQuery($requete_ajout_reglement);	
	$resultat_ajout_reglement = $db->query();
	$id_regl=$db->insertid();
	
	return($id_regl);
}


function versements_sans_remise_et_avec_validation($id_cotisation,$exclure_moyens_paiement=""){
$db = & JFactory::getDBO();

if (test_non_vide($exclure_moyens_paiement))
    $compl_req=" and reg.id_moyen_paiement not in (0,".$exclure_moyens_paiement.") ";
else $compl_req="";

$requete_versement="select format(sum(reg.montant_reglement),2) as total_versement from Reglement as reg "
    ."  where reg.validation_reglement=1 ".$compl_req." and reg.id_cotisation=".$id_cotisation." and (reg.id_remise is null or reg.id_remise=0)"; 
					
	// echo $requete_versement;
$db->setQuery($requete_versement);	

return ($db->loadResult());
}

function remises_accordees($complement_date_requete){
$db = & JFactory::getDBO();

$requete_remises_accordees="select format(sum(reg.montant_reglement),2) as total_versement from Reglement as reg ";
$requete_remises_accordees.="  where reg.validation_reglement=1 ".$complement_date_requete." and (reg.id_remise is not null and reg.id_remise<>0)"; 
					
	// echo $requete_remises_accordees;
$db->setQuery($requete_remises_accordees);	

return ($db->loadResult());
}


function date_desactive_notif_email(){

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_date_desactive_notif_email="SELECT valeur_parametre FROM Parametres WHERE nom_parametre=\"date_desactive_notif_email\"";
	// echo "<br>$requete_date_desactive_notif_email: ".$requete_date_desactive_notif_email;
	$db->setQuery($requete_date_desactive_notif_email);	
	$db->query();
	return($db->loadResult());
}

function heure_desactive_notif_email(){

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_heure_desactive_notif_email="SELECT valeur_parametre FROM Parametres WHERE nom_parametre=\"heure_desactive_notif_email\"";
	// echo "<br>$requete_heure_desactive_notif_email: ".$requete_heure_desactive_notif_email;
	$db->setQuery($requete_heure_desactive_notif_email);	
	$db->query();
	return($db->loadResult());
}


function notification_email($etat){

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

    if (test_non_vide($etat) and est_min_manager($user)) {
      
	    $requete_maj_notification_email="UPDATE Parametres SET valeur_parametre=\"".$etat."\" WHERE nom_parametre=\"notification_email\"";
	    //echo "<br>requete_notification_email: ".$requete_maj_notification_email;
	    $db->setQuery($requete_maj_notification_email);	
	    $db->query();
		
	    if ($etat==0){
		$requete_maj_date_desactive_notif_email="UPDATE Parametres SET valeur_parametre=\"".date("Y-m-d")."\" WHERE nom_parametre=\"date_desactive_notif_email\"";
		//echo "<br>$requete_maj_date_desactive_notif_email: ".$requete_maj_date_desactive_notif_email;
		$db->setQuery($requete_maj_date_desactive_notif_email);	
		$db->query();
		
		$requete_maj_heure_desactive_notif_email="UPDATE Parametres SET valeur_parametre=\"".date("H:i")."\" WHERE nom_parametre=\"heure_desactive_notif_email\"";
		//echo "<br>$requete_maj_heure_desactive_notif_email: ".$requete_maj_heure_desactive_notif_email;
		$db->setQuery($requete_maj_heure_desactive_notif_email);	
		$db->query();
		
		$heure_coupure=heure_desactive_notif_email();
		
		sendMail(1,"alerte coupure messagerie","Messagerie coupee depuis ".date_longue($date_coupure)." a ".$heure_coupure."");
                sendMail(267,"alerte coupure messagerie","Messagerie coupee depuis ".date_longue($date_coupure)." a ".$heure_coupure."");
		
	    }
    }
    else {
	    $requete_notification_email="SELECT valeur_parametre FROM Parametres WHERE nom_parametre=\"notification_email\"";
	    // echo "<br>requete_notification_email: ".$requete_notification_email;
	    $db->setQuery($requete_notification_email);	
	    $db->query();
	    return($db->loadResult());
	    
    }




}

function acces_application($etat){

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

    if (test_non_vide($etat) and est_min_manager($user)) {
      
	if ($etat==3 or ($etat==2 and est_manager($user)))
	    $etat=0;
	else $etat++;
	
	
	$requete_maj_acces_application="UPDATE Parametres SET valeur_parametre=\"".$etat."\" WHERE nom_parametre=\"acces_application\"";
	//echo "<br>requete_maj_acces_application: ".$requete_maj_acces_application;
	$db->setQuery($requete_maj_acces_application);	
	$db->query();
    }
    else {
	    $requete_acces_application="SELECT valeur_parametre FROM Parametres WHERE nom_parametre=\"acces_application\"";
	    // echo "<br>requete_acces_application: ".$requete_acces_application;
	    $db->setQuery($requete_acces_application);	
	    $db->query();
	    return($db->loadResult());
	    
    }




}


function menu_acces_rapide($id_client="",$titre=""){
$user =& JFactory::getUser();


	if (est_min_agent($user)) $pour_les_agents=" de ce client";

	echo "<table width=\"100%\" border=0>";
                    echo "<tr><td align=\"left\" width=\"50%\"  valign=\"middle\">";
			
			if (test_non_vide($_GET["acces_application"])){
				acces_application($_GET["acces_application"]);

				//header("Location: ".$_SERVER['REQUEST_URI'].""); 
			}
			$etat_actuel_acces_apllication=acces_application();
			if (est_min_manager($user))
				echo " <a href=\"".$_SERVER['REQUEST_URI']."&acces_application=".$etat_actuel_acces_apllication."\">";
			echo " <img src=\"images/acces_application_".$etat_actuel_acces_apllication.".png\" title=\"Etat acces application : ".$etat_actuel_acces_apllication."\" width=\"24\" height=\"24\">";
			if (est_min_manager($user))
				echo "</a>";
			
			if (test_non_vide($_GET["mess"])){
				if ($_GET["mess"]=="ok")
					notification_email("0");
				if ($_GET["mess"]=="ko")
					notification_email("1");
				//header("Location: ".$_SERVER['REQUEST_URI'].""); 
			}
			
			if (notification_email()=="1")
				$image="ok";
			else $image="ko";
			
			if (est_min_manager($user))
				echo " <a href=\"".$_SERVER['REQUEST_URI']."&mess=".$image."\">";
			echo " <img src=\"images/email-".$image."-icon.png\" title=\"Etat messagerie: ".$image."\" width=\"24\" height=\"24\">";
			if (est_min_manager($user))
				echo "</a>";

		if (est_min_manager($user)){
			echo " <a href=\"index.php?option=com_content&view=article&id=65\"\">";
			echo " <img src=\"images/publipostage-icon.png\" title=\"publipostage\" width=\"24\" height=\"24\"></a>";
			
			echo " <a href=\"index.php?option=com_content&view=article&id=68\">";
			echo " <img src=\"images/agent-fif-icon.png\" title=\"Les agents\" width=\"24\" height=\"24\"></a>";
		}

echo "</td><td align=\"right\" width=\"50%\"  valign=\"middle\">";
if (test_non_vide($id_client) and est_min_register($user)){
			echo " <a href=\"index.php/component/content/article?id=60";
			if (!est_min_register($user)) echo "&modif=1";
			echo "&id_client=".$id_client."\">";
			echo " <img src=\"images/Fiche-client-icon.png\" title=\"La fiche ".$pour_les_agents."\"></a>";
			
			if (exist_id_client_eleve($id_client)){
			    echo " <a href=\"index.php/component/content/article?id=59&id_client=".$id_client."\"/>";
			    echo " <img src=\"images/icon-creneau-reserver.png\" title=\"les saisons ".$pour_les_agents."\" width=\"24\" height=\"24\"></a>";
			}

			echo " <a href=\"index.php/component/content/article?id=80&id_client=".$id_client."\"/>";
			echo " <img src=\"images/coins-icon.png\" title=\"les r&egrave;glements ".$pour_les_agents."\" width=\"24\" height=\"24\"></a>";
			
			echo " <a href=\"".$_SERVER['REQUEST_URI']."&tmpl=component&print=1&page=\" target=\"_blank\">";
			echo " <img src=\"images/imprimante-icon.png\" title=\"Imprimer\" width=\"24\" height=\"24\"></a>";

		$nbre_docs=nbre_docs($id_client);
		
		if ($nbre_docs>0 or est_min_agent($user)){
			echo " <a href=\"index.php/component/content/article?id=69&id_client=".$id_client."\" />";
			
			if ($nbre_docs>0)
				echo " <img src=\"images/doc-icon.png\" title=\"Documents de ce client\"></a> </td>";
			else 
				echo " <img src=\"images/doc-ajout-icon.png\" title=\"Ajouter un document &agrave; ce client\">";

			echo "</a>";
		}
	
}
else echo "&nbsp;";
echo "</td></tr>";
	echo "<tr><td colspan=\"2\"><hr></td></tr><tr><td width=\"50%\" align=\"left\" >";
	if (test_non_vide($titre))
	    echo "<h2>".$titre."</h2>";
	else echo "&nbsp;";
	echo "</td><td align=\"right\" nowrap width=\"50%\">";
			echo prenom_du_client($id_client)." ".nom_du_client($id_client);
	echo "</td></tr>";	
echo "</table><hr><br>";
}

function menu_deroulant($Table,$old_select,$fonction="",$type="",$is_detail_credit_client=0,$date_deb_resa="",$date_fin_resa="",$heure_fin_resa="",$id_type_regroupement=-1){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select * from ".$Table." order by id";
	//echo $requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	echo "<select name=".$Table;
	if (test_non_vide($fonction))
		echo " onChange=\"".$fonction."\"";
	echo ">";
	echo "<option value=\"\" ></option>";
	foreach($resultat_recup_liste as $recup_liste){
		if (est_min_manager($user) and $recup_liste->id==5){
			echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom."</option>";
		}
		else {
			if ($recup_liste->id<>5){
			    if ($old_select==$recup_liste->id) $select=" selected ";
			    else $select="";
			    //$type_credit==2 c'est caution, // $type==3 c'est remboursement
			    if (!((test_non_vide($type) and $type==3 and $recup_liste->id>3) ))
				if (!(est_register($user) and ($recup_liste->id<>2 and $recup_liste->id<>8) and $Table<>"Terrain"))
				    if ($recup_liste->is_active==1 or ($type==1 and $recup_liste->id<>7) )
					if ($is_detail_credit_client==0 or ($is_detail_credit_client==1 and ($type<>1 or ($recup_liste->id<>10 and $recup_liste->id<>2))))
					    echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom."</option>";
			}
		}
	}
	echo "</select>";
}

function menu_deroulant_simple($Table,$old_select,$criteres,$complement_nom="",$fonction="enregistrer()"){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select * from ".$Table." where 1 ".$criteres." order by id asc ";
	//echo "<br><br>".$requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	echo "<select name=".$Table."".$complement_nom." \" onChange=\"".$fonction."\" >";
	echo "<option value=\"\" disabled selected></option>";
	foreach($resultat_recup_liste as $recup_liste){
		if ($old_select==$recup_liste->id) $select=" selected ";
		else $select="";
		
                echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom."</option>";
        }
	echo "</select>";
}

function ajout_eleve_saison_cotisation($id_client,$sc_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_insert_eleve_Saison_inscription="INSERT INTO `Saison_inscription`(`id_cotisation`, `id_client`) VALUES (".$sc_id.",".$id_client.")";
	//echo "<br><br>".$requete_insert_eleve_Saison_inscription;
	$db->setQuery($requete_insert_eleve_Saison_inscription);
	$db->Query();
}

function ajout_eleve_commande($id_client,$c_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_ajout_eleve_commande="INSERT INTO `Commande_client`(`id_commande`, `id_client`,"
		." `date_validation`, `heure_validation`, `id_user_validation`) "
		." VALUES (".$c_id.",".$id_client.",\"".date("Y-m-d")."\",\"".date("H:i")."\",".$user->id.")";
	//echo "<br><br>".$requete_ajout_eleve_commande;
	$db->setQuery($requete_ajout_eleve_commande);
	$db->Query();
}

function suppr_commande_eleve($id_client,$c_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_suppr_commande_eleve="DELETE FROM `Commande_client` WHERE `id_commande`=".$c_id." and `id_client`=".$id_client;
	//echo "<br><br>".$requete_suppr_commande_eleve;
	$db->setQuery($requete_suppr_commande_eleve);
	$db->Query();
}


function maj_reception_commande_eleve($id_client,$c_id,$raz=false){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	if ($raz){
	    $id_user_reception=0;
	    $heure_reception="";
	    $date_reception="0000-00-00";
	}
	else {
	    $id_user_reception=$user->id;
	    $heure_reception=date("H:i");
	    $date_reception=date("Y-m-d");
	}
	
	$requete_ajout_eleve_commande="UPDATE `Commande_client` SET `date_reception`=\"".$date_reception."\", "
	    ." `heure_reception`=\"".$heure_reception."\", `id_user_reception`=".$id_user_reception
	    ." WHERE id_commande=".$c_id." and id_client=".$id_client;
	//echo "<br><br>".$requete_ajout_eleve_commande;
	$db->setQuery($requete_ajout_eleve_commande);
	$db->Query();
}


function ajout_saison($nom,$date_debut,$date_fin){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_insert_Saison="INSERT INTO `Saison`(`nom`, `date_debut`, `date_fin`) "
	    ."VALUES (\"".$nom."\",\"".$date_debut."\",\"".$date_fin."\")";
	//echo "<br><br>".$requete_insert_Saison;
	$db->setQuery($requete_insert_Saison);
	$db->Query();
	return($db->insertid());
	
	
}

function ajout_commande($nom,$nom_fournisseur){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_insert_commande="INSERT INTO `Commande`( `nom`, `id_user`, `date_creation`, `heure_creation`, `is_active`, `nom_fournisseur`) "
	    ." VALUES (\"".$nom."\",".$user->id.",\"".date("Y-m-d")."\",\"".date("H:i")."\",1,\"".$nom_fournisseur."\")";
	//echo "<br><br>".$requete_insert_commande;
	$db->setQuery($requete_insert_commande);
	$db->Query();
	return($db->insertid());
	
	
}

function maj_commande($nom,$nom_fournisseur,$c_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_maj_commande="UPDATE `Commande` SET `nom`=\"".$nom."\",`nom_fournisseur`=\"".$nom_fournisseur."\" WHERE id=".$c_id;
	//echo "<br><br>".$requete_maj_commande;
	$db->setQuery($requete_maj_commande);
	$db->Query();
	return($c_id);
}

function suppr_commande($c_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_suppr_commande=" DELETE FROM `Commande` WHERE id=".$c_id;
	//echo "<br><br>".$requete_suppr_commande;
	$db->setQuery($requete_suppr_commande);
	$db->Query();

}


function RAZ_jour_semaine_dans_cotisation($sc_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_RAZ_jour_semaine_dans_cotisation="DELETE FROM `Saison_cotisations_jours_semaine` WHERE id_Saison_cotisations=".$sc_id;
	//echo "<br><br>".$requete_RAZ_jour_semaine_dans_cotisation;
	$db->setQuery($requete_RAZ_jour_semaine_dans_cotisation);
	$db->Query();
}

function ajout_jour_semaine_dans_cotisation($id_jour,$sc_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_insert_jour_semaine_dans_cotisation="INSERT INTO `Saison_cotisations_jours_semaine`(`id_Saison_cotisations`, `id_jours_semaine`) "
	    ."VALUES (".$sc_id.",".$id_jour.")";
	//echo "<br><br>".$requete_insert_jour_semaine_dans_cotisation;
	$db->setQuery($requete_insert_jour_semaine_dans_cotisation);
	$db->Query();
}

function recup_liste_nom_jours_semaine_cotisation($sc_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();

    	$requete_liste_nom_jours_semaine_cotisation="SELECT js.nom as le_nom_du_jour FROM `Saison_cotisations_jours_semaine`, jours_semaine as js "
	    ." WHERE `id_jours_semaine`=js.id and `id_Saison_cotisations`=".$sc_id;
	//echo "<br><br>".$requete_liste_nom_jours_semaine_cotisation;
	$db->setQuery($requete_liste_nom_jours_semaine_cotisation);
	$resultat=$db->loadObjectList();
	
	foreach($resultat as $jour)
	    $liste_jours_semaine.=$jour->le_nom_du_jour."<br>";
	
	return($liste_jours_semaine);
}


function afficher_check_tous_les_jours_de_semaine($sc_id=""){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();

	if (test_non_vide($sc_id))
	    $compl_req=", (SELECT IFNULL((SELECT id_jours_semaine FROM  `Saison_cotisations_jours_semaine` "
		." WHERE  `id_jours_semaine`=js.id  and `id_Saison_cotisations`=".$sc_id."),0))  as jour_sem ";
	else $compl_req="";

    	$requete_afficher_check_tous_les_jours_de_semaine="SELECT * ".$compl_req
	    ." FROM  jours_semaine as js WHERE 1 ";
	//echo "<br><br>".$requete_afficher_check_tous_les_jours_de_semaine;
	$db->setQuery($requete_afficher_check_tous_les_jours_de_semaine);
	$resultat=$db->loadObjectList();
	
	foreach($resultat as $jour){
	    echo "<input type=\"checkbox\" name=\"jours_semaine_".$jour->id."\" value=\"1\" ";
	    if (test_non_vide($sc_id) and $jour->jour_sem>0)
		echo " checked ";
	    echo "/> ".$jour->nom." ";
	}
}

function delete_cotisation($sc_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_delete_Reglement="DELETE FROM `Reglement` WHERE `id_cotisation`=".$sc_id." )";
	//echo "<br><br>".$requete_delete_Reglement;
	$db->setQuery($requete_delete_Reglement);
	$db->Query();
	
	$requete_delete_Saison_inscription="DELETE FROM `Saison_inscription` WHERE id_cotisation=".$sc_id;
	//echo "<br><br>".$requete_delete_Saison_inscription;
	$db->setQuery($requete_delete_Saison_inscription);
	$db->Query();
	
	$id_saison=recup_1_element("id_saison","Saison_cotisations","id",$sc_id);
	
	$requete_delete_Saison_cotisations="DELETE FROM `Saison_cotisations` WHERE id=".$sc_id;
	//echo "<br><br>".$requete_delete_Saison_cotisations;
	$db->setQuery($requete_delete_Saison_cotisations);
	$db->Query();
	
	$requete_delete_Saison_cotisations_jours_semaine="DELETE FROM `Saison_cotisations_jours_semaine` WHERE id_Saison_cotisations=".$sc_id;
	//echo "<br><br>".$requete_delete_Saison_cotisations_jours_semaine;
	$db->setQuery($requete_delete_Saison_cotisations_jours_semaine);
	$db->Query();

	$requete_reste_Saison_cotisations="SELECT * FROM `Saison_cotisations` WHERE id_saison=".$id_saison;
	//echo "<br><br>".$requete_reste_Saison_cotisations;
	$db->setQuery($requete_reste_Saison_cotisations);
	$db->Query();
	
	if ($db->getNumRows()==0){
	    $requete_delete_Saison="DELETE FROM `Saison` WHERE id=".$id_saison;
	    //echo "<br><br>".$requete_delete_Saison;
	    $db->setQuery($requete_delete_Saison);
	    $db->Query();
	}
	
}


function ajout_saison_cotisation($id_saison,$montant_cotisation){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	
	$requete_insert_Saison="INSERT INTO `Saison_cotisations`( `is_active`, `id_saison`, `montant_cotisation`)"
	    ."VALUES (1,".$id_saison.",".$montant_cotisation.")";
	//echo "<br><br>".$requete_insert_Saison;
	$db->setQuery($requete_insert_Saison);
	$db->Query();
	return($db->insertid());
}

function maj_infos_cotisation($sc_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	
	$requete_maj_Saison_cotisations="UPDATE `Saison_cotisations` SET `montant_cotisation`=".$montant_cotisation." WHERE id=".$sc_id;
	//echo "<br><br>".$requete_maj_Saison_cotisations;
	$db->setQuery($requete_maj_Saison_cotisations);
	$db->Query();
}

function maj_infos_saison($id_saison,$nom_saison,$date_debut_saison,$date_fin_saison){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	
	$requete_maj_Saison="UPDATE `Saison` SET `nom`=\"".$nom_saison."\", `date_debut`=\"".$date_debut_saison."\", `date_fin`=\"".$date_fin_saison."\" "
	    ." WHERE id=".$id_saison;
	//echo "<br><br>".$requete_maj_Saison;
	$db->setQuery($requete_maj_Saison);
	$db->Query();
}

function liste_Type_equipement(){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_type_equipement="Select * from Type_equipement where is_active=1 order by id asc";
	//echo "<br><br>".$requete_recup_type_equipement;
	$db->setQuery($requete_recup_type_equipement);
	return($db->loadObjectList());
}

function delete_eleve_saison_cotisation($id_client,$sc_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_suppr_Saison_inscription="DELETE FROM `Saison_inscription` WHERE `id_cotisation`=".$sc_id." and `id_client`=".$id_client;
	//echo "<br><br>".$requete_suppr_Saison_inscription;
	$db->setQuery($requete_suppr_Saison_inscription);
	$db->Query();
}

function supprimer_photo_client($id_client){

$db = & JFactory::getDBO();
	
	/*chmod("/var/www/vhosts/footinfive.com/httpdocs/LEDG/media/bearleague/".photo_user($id_user)."\"", 777);
	if (unlink("/var/www/vhosts/footinfive.com/httpdocs/LEDG/media/bearleague/".photo_user($id_user)."\""))
		echo "supprimee";
	else echo "non";*/
	
	$query ="UPDATE Client SET fichier_photo=\"\" where id_client=".$id_client;	
	$db->setQuery($query);
	$db->query();

}

function ajout_photo_client($fichier,$id_client){
	
	$db = & JFactory::getDBO();
	
	$query ="UPDATE Client SET fichier_photo=\"".$fichier."\" where id_client=".$id_client;	
	$db->setQuery($query);
	$db->query();
}

function photo_client($id_client){
	
	$db = & JFactory::getDBO();
	
	$query ="SELECT fichier_photo FROM Client where id_client=".$id_client;	
	$db->setQuery($query);
	$db->query();
	return($db->loadResult());
}

function menu_deroulant_eleve($table,$nom_id,$valeur_id){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	
	
	$requete_recup_liste="SELECT * FROM `Client` WHERE `id_client` not in "
	    ." (SELECT distinct(`id_client_contact`) FROM `Relation_enfant_contacts` WHERE `id_client_enfant`<>`id_client_contact`) "
	    ." and id_client not in (SELECT `id_client` FROM ".$table." WHERE ".$nom_id."=".$valeur_id.") order by nom asc ";
	//echo "<br><br>".$requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	$le_select="<select name=ajout_eleve_".$valeur_id." \" >";
	$le_select.="<option value=\"\" disabled selected></option>";
	foreach($resultat_recup_liste as $recup_liste)		
                $le_select.="<option value=\"".$recup_liste->id_client."\" >".$recup_liste->nom." ".$recup_liste->prenom
		    ." - ".inverser_date($recup_liste->date_naissance)."</option>";
	$le_select.="</select>";
	return($le_select);
}

function menu_deroulant_des_users($etat,$old_select){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select u.* from #__users as u, #__user_usergroup_map as ugm where ugm.user_id=u.id and ugm.group_id>2 and ugm.group_id<8 "
			    ." and block=".$etat." order by username asc";
	//echo "<br><br>".$requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	echo "<select name=etat_user_".$etat." >";
	echo "<option value=\"\" selected></option>";
	foreach($resultat_recup_liste as $recup_liste){
		if ($old_select==$recup_liste->id) $select=" selected ";
		else $select="";
		
                echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->username."</option>";
        }
	echo "</select>";
}

function liste_check_box($Table,$old_select,$criteres,$extension_nom="",$separateur="<br>"){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select * from ".$Table." where 1 ".$criteres." order by nom asc";
	//echo "<br><br>".$requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();

	foreach($resultat_recup_liste as $recup_liste){
		echo "<input type=\"checkbox\" name=".$Table."_".$recup_liste->id.$extension_nom;
		if ($old_select==$recup_liste->id) $select=" checked ";
		else $select="";
		
                echo " value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom.$separateur;
        }
}

function liste_des_champs($Table,$deb_fin,$test_erreurs,$old_saisie="",$recup_id_journee="",$test_si_saisie_qqchose="",$modifier=""){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select id, valeur from ".$Table." order by id";
	//echo $requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	
	$requete_recup_decompo="select count(id) as nbre_lignes from decompo_caisse where id_journee=".$recup_id_journee." and ouv1_ferm0=".$deb_fin;
	//echo $requete_recup_decompo;
	$db->setQuery($requete_recup_decompo);	
	$resultat_decompo = $db->loadObject();
	
	if ((!$test_erreurs) and test_non_vide($resultat_decompo->nbre_lignes) and $resultat_decompo->nbre_lignes>0 and !test_non_vide($modifier))
		$readonly= " readonly=\"readonly\" ";
	
	foreach($resultat_recup_liste as $recup_liste){
		
		if (test_non_vide($recup_id_journee)){
			$requete_recup_decompo_deb="select nbre from decompo_caisse where id_journee=".$recup_id_journee;
			$requete_recup_decompo_deb.=" and id_monetaire=".$recup_liste->id;
			$requete_recup_decompo_deb.=" and ouv1_ferm0=".$deb_fin;
			//echo $requete_recup_decompo_deb;
			$db->setQuery($requete_recup_decompo_deb);	
			$resultat_decompo_deb = $db->loadObject();
		}
		echo "<tr><td align=\"center\">".$recup_liste->valeur." &#8364;</td>";
		echo "<td align=center><input name=".$Table."_".$deb_fin."_".$recup_liste->id;
		echo " size=\"5\" type=\"number\" min=\"0\" max=\"150\" step=1 ";
		echo $readonly." value=\""; 
		if (test_non_vide($resultat_decompo_deb->nbre) and !test_non_vide($test_si_saisie_qqchose["$deb_fin"]))
			echo $resultat_decompo_deb->nbre;
		else echo $old_saisie["$deb_fin"][$recup_liste->id];
		echo "\"></td><td align=\"right\">";
		if (test_non_vide($resultat_decompo_deb->nbre) and !test_non_vide($test_si_saisie_qqchose["$deb_fin"]))
			echo ($recup_liste->valeur*$resultat_decompo_deb->nbre);
		else echo ($recup_liste->valeur*$old_saisie["$deb_fin"][$recup_liste->id]);
		echo " &#8364;</td></tr>";
	}
	

}

function age($date_naissance){
    
    return(floor((diff_dates_en_jours($date_naissance,date("Y-m-d"))/365)));
 
}
function liste_categories_avec_feuille($sc_id="",$id_saison="",$jour_sem=""){
 $db = & JFactory::getDBO();
 
$requete_recup_categorie="SELECT * from Type_Regroupement order by id";
	//echo $requete_info_inscription_client_saison;
	$db->setQuery($requete_recup_categorie);	
	$recup_categorie= $db->loadObjectList();

	if (test_non_vide($sc_id))
	    $compl_lien.="&amp;sc_id=".$sc_id;
	
	if (test_non_vide($jour_sem))
	    $compl_lien.="&amp;jour_sem=".$jour_sem;
	    
	if (test_non_vide($id_saison))
	    $compl_lien.="&amp;id_saison=".$id_saison;
	
	foreach($recup_categorie as $la_categorie)
		$resultat.=$la_categorie->nom." <a href=\"index.php/fm?tmpl=component&amp;print=1&amp;page=".$compl_lien."&amp;categorie="
		    .$la_categorie->id."\" target=\"_blank\">"
		    ."<img src=\"images/fm-icon.png\" title=\"Feuille de presence (".$la_categorie->nom.") \"></a> ";
	return($resultat);
}

function age_du_client($id_client){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_age_du_patient="Select date_naissance from Client where id_client=".$id_client;
	$db->setQuery($requete_age_du_patient);	
	return (age($db->LoadResult()));

}

function Ajout_zero_si_absent($horaire) {
		
		list($heures,$minutes) = explode(':', $horaire);
		
		$zero_heures="";
		$zero_minutes="";
		
		$valeur_heures=$heures+0;
		$valeur_minutes=$minutes+0;
		
		if ($valeur_heures<10) $zero_heures="0"; 
		else $zero_heures="";
		
		if ($valeur_minutes<10) $zero_minutes="0"; 
		else $zero_minutes="";
		
		return ($zero_heures.$valeur_heures.":".$zero_minutes.$valeur_minutes);
}

function jours_en_fr(){

return (array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"));

}

function mois_en_fr(){

return (array("Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"));

}

function date_longue($date_mysql=""){

if ($date_mysql=="") $date_mysql=date("Y-m-d");
$jour_fr = jours_en_fr();
$mois_fr = mois_en_fr();

list($annee, $mois, $jour) = explode('-', $date_mysql);
$date_longue = mktime (0, 0, 0, $mois, $jour, $annee);
		
return($jour_fr[date("w", $date_longue)]." ".$jour." ".$mois_fr[date("n", $date_longue)-1]." ".$annee);
}

function test_valid_existence_date($date){

if (strpos($date,"/")==2) {
	$Syntaxe='/^([0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4})/'; 
	if(!(preg_match($Syntaxe,$date))) return false;
	list($jour,$mois,$annee ) = explode('/', $date);
}
if (strpos($date,"-")==4) {
	$Syntaxe='/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})/'; 
	if(!(preg_match($Syntaxe,$date))) return false;
	list($annee, $mois, $jour) = explode('-', $date);
}

if (($annee<>"") and ($mois<>"") and ($jour<>"") and checkdate($mois,$jour,$annee)) return(true);
else return(false);
}



function decaler_heure($heure,$ajout)
{
$timestamp = strtotime("$heure");
$heure_ajout = strtotime("+$ajout minutes", $timestamp);
return (Ajout_zero_si_absent(date('H:i', $heure_ajout)));
}

function decaler_jour($date,$nbreJours){
	
	$date_new = new DateTime($date);
	
	date_add($date_new, date_interval_create_from_date_string(''.$nbreJours.' days'));
	
	return  $date_new->format('Y-m-d');
}

function duree_en_minutes($duree){
	
	list($heure,$minutes) = explode(':', $duree);
	return($heure*60+$minutes);

}
function duree_en_horaire($duree_en_minute){
	
	$heure=floor($duree_en_minute/60);
	$minutes=$duree_en_minute-($heure*60);
	return(Ajout_zero_si_absent($heure.":".$minutes));

}

function inverser_date($date){
	
if (strpos($date,"/")==2) {
	list($jour,$mois,$annee ) = explode('/', $date);
	return($annee."-".$mois."-".$jour);
}
if (strpos($date,"-")==4) {
	list($annee, $mois, $jour) = explode('-', $date);
	return($jour."/".$mois."/".$annee);
}

}

function coller_jma($date){
	
if (strpos($date,"/")==2) 
	list($jour,$mois,$annee ) = explode('/', $date);

if (strpos($date,"-")==4) 
	list($annee, $mois, $jour) = explode('-', $date);
	
return($jour.$mois.$annee);


}

function diff_dates_en_jours($date1="", $date2=""){
	$db = & JFactory::getDBO();
	
	if ($date2=="") $date2=date("Y-m-d");
	if ($date1=="") $date1=date("Y-m-d");
	
	$requete_diff_2_dates="SELECT TIMESTAMPDIFF(DAY,\"".$date1."\",\"".$date2."\") as diff ;";
	
	//echo "req=".$requete_diff_2_dates."<br>";
	$db->setQuery($requete_diff_2_dates);	
	return($db->loadResult());	
}

function diff_dates_en_minutes($date1="", $horaire1="", $date2="", $horaire2=""){
	$db = & JFactory::getDBO();
	
	if ($date2=="") $date2=date("Y-m-d");
	if ($date1=="") $date1=date("Y-m-d");
	if ($horaire1=="") $horaire1=date("H").":".date("i");
	if ($horaire2=="") $horaire2=date("H").":".date("i");
	
	$requete_diff_2_dates="SELECT TIMESTAMPDIFF(MINUTE,";
	$requete_diff_2_dates.=" CAST(concat(\"".$date1."\",\" \",\"".$horaire1.":00\") AS CHAR(22)),";
	$requete_diff_2_dates.=" CAST(concat(\"".$date2."\",\" \",\"".$horaire2.":00\") AS CHAR(22)))";
	$requete_diff_2_dates.=" as diff ;";
	
	//echo "req=".$requete_diff_2_dates."<br>";
	$db->setQuery($requete_diff_2_dates);	
	return($db->loadResult());	
}


// Interface PHP pour mail()
function sendMail($id_client,$objet,$corps,$email="") {
$user =& JFactory::getUser();
$db = & JFactory::getDBO();


   // l'&eacute;metteur
   $tete = 'MIME-Version: 1.0' . "\n";
   $tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
   $tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
   $tete .= "Reply-To: contact@footinfive.com\n";
   $tete .= "Return-Path: contact@footinfive.com\n";
   
    $notif_email=notification_email();
    
    if ($notif_email==0){
	$date_coupure=date_desactive_notif_email();
	$heure_coupure=heure_desactive_notif_email();
	
	if (diff_dates_en_minutes($date_coupure,$heure_coupure,date("Y-m-d"),date("H:i"))>30){
	    //mail("<ya2-95@hotmail.fr>","alerte coupure messagerie","Messagerie coupee depuis ".date_longue($date_coupure)." a ".$heure_coupure."",$tete);
	    mail("<lefloch.g@gmail.com>","alerte coupure messagerie","Messagerie coupee depuis ".date_longue($date_coupure)." a ".$heure_coupure."",$tete);
	}
    }	
    else {
	    if (!test_non_vide($email)){
		$requete_client="Select * from Client as c, #__users as u where u.id=c.id_user and c.id_client=".$id_client;
		$db->setQuery($requete_client);	
		$le_client=$db->LoadObject();
		$email=$le_client->email;
	    }
	    // et zou... false si erreur d'&eacute;mission
	    $corps=str_replace("\n", "<br>", $corps);
	    
	    if ($notif_email=="1" and ($email<>"agent@footinfive.com"))
		 return mail("<$email>","$objet",$corps,$tete);
	    else return 1;
    }
}


function test_heure_ete($date){
    
    $db = & JFactory::getDBO();
	
	if (strpos($date,"/")==2) 
            list($jour,$mois,$annee ) = explode('/', $date);

        if (strpos($date,"-")==4) 
            list($annee, $mois, $jour) = explode('-', $date);
        

            
	$requete_recup_dernier_dimanche_octobre="SELECT date FROM  `dernier_dimanche_octobre` WHERE  `annee` =".$annee;
        $db->setQuery($requete_recup_dernier_dimanche_octobre);	
	$dernier_dimanche_octobre=$db->loadResult();
        
        $requete_recup_dernier_dimanche_mars="SELECT date FROM  `dernier_dimanche_mars` WHERE  `annee` =".$annee;
        $db->setQuery($requete_recup_dernier_dimanche_mars);	
	$dernier_dimanche_mars=$db->loadResult();

	if (diff_dates_en_jours($date, $dernier_dimanche_octobre)>0 and diff_dates_en_jours($date, $dernier_dimanche_mars)<=0)
            return(true);
        else return(false);
        
		
}

function recup_infos_nbre_reglements($id_moyen_paiement,$complement_date_requete,$signe="") {

    $db = & JFactory::getDBO();

	
	if ($signe=="") $complement_requete_signe="";
	else $complement_requete_signe=" and montant_reglement".$signe."0 ";
	
	$requete_recup_infos_nbre_reglements="SELECT count(`id_reglement`) as le_nbre FROM `Reglement` where `id_moyen_paiement`=".$id_moyen_paiement." ";
        $requete_recup_infos_nbre_reglements.= $complement_requete_signe."  and `validation_reglement`=1  ".$complement_date_requete;
        //echo $requete_recup_infos_nbre_reglements;
        
	$db->setQuery($requete_recup_infos_nbre_reglements);	
        $recup_infos_nbre_reglements = $db->loadObject();
        
	return ($recup_infos_nbre_reglements->le_nbre);


}


function recup_infos_montant_reglements($id_moyen_paiement,$complement_date_requete,$signe="") {

    $db = & JFactory::getDBO();


    
	if ($signe=="") $complement_requete_signe="";
	else $complement_requete_signe=" and montant_reglement".$signe."0 ";
	
	$requete_recup_infos_montant_reglements="SELECT sum(`montant_reglement`) as total_reglement FROM `Reglement` where `id_moyen_paiement`=".$id_moyen_paiement." ";
        $requete_recup_infos_montant_reglements.= $complement_requete_signe." and `validation_reglement`=1  ".$complement_date_requete;
	//echo $requete_recup_infos_montant_reglements;
        $db->setQuery($requete_recup_infos_montant_reglements);	
        $recup_infos_montant_reglements = $db->loadObject();
        return ($recup_infos_montant_reglements->total_reglement);

}

function recup_accompte_necessaire($id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	$requete_recup_accompte_necessaire="select accompte_necessaire from Client where id_client=".$id_client;
	//echo $requete_recup_accompte_necessaire;
	$db->setQuery($requete_recup_accompte_necessaire);	
	return($db->loadResult());
}

function exist_id_client($id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_recup_client="select * from Client where id_client=".$id_client;
	//echo $requete_recup_client;
	$db->setQuery($requete_recup_client);
	$db->Query();
	if ($db->getNumRows()==0)
	    return(false);
	else return(true);
}

function liste_contacts($id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	if (exist_id_client_eleve($id_client)){
	$requete_recup_client_contact="SELECT `id_client_contact`, ts.nom as nom_Type_statut FROM `Relation_enfant_contacts`, Type_statut as ts"
		." where id_client_enfant=".$id_client." and Type_statut=ts.id and Type_statut>1 ";
	//echo $requete_recup_client_contact;
	$db->setQuery($requete_recup_client_contact);	
	$resultats=$db->loadObjectList();
	
	foreach($resultats as $contact)
	    $la_liste.="<a href=\"index.php/component/content/article?id=60&id_client="
		.$contact->id_client_contact."\"  target=\"_blank\"/>".prenom_du_client($contact->id_client_contact)
		." ".nom_du_client($contact->id_client_contact)."</a> (".$contact->nom_Type_statut.") - ";
	return($la_liste);
	}
	else return("ce n'est pas un Eleve");
	
}

function liste_eleves($id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	if (!exist_id_client_eleve($id_client)){
	$requete_recup_client_contact="SELECT id_client_enfant, ts.nom as nom_Type_statut FROM `Relation_enfant_contacts`, Type_statut as ts"
		." where id_client_contact=".$id_client." and Type_statut=ts.id and Type_statut>1 ";
	//echo $requete_recup_client_contact;
	$db->setQuery($requete_recup_client_contact);	
	$resultats=$db->loadObjectList();
	
	foreach($resultats as $relation)
	    $la_liste.=$relation->nom_Type_statut." de <a href=\"index.php/component/content/article?id=60&id_client="
		.$relation->id_client_enfant."\"  target=\"_blank\"/>".prenom_du_client($relation->id_client_enfant)
		." ".nom_du_client($relation->id_client_enfant)."</a> - ";
	return($la_liste);
	}
	else return("c'est un Eleve");
	
}

function ajouter_relation($id_client_enfant,$id_client_contact,$Type_statut) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_insert_relation="INSERT INTO `Relation_enfant_contacts`(`id_client_enfant`, `id_client_contact`,"
	    ."`Type_statut`) VALUES (".$id_client_enfant.",".$id_client_contact
	    .",  \"".$Type_statut."\")";
	//echo "<br>".$requete_insert_relation;
					
	$db->setQuery($requete_insert_relation);
	$db->query();

	
}

function recup_id_statut_client($id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_recup_client_eleve="SELECT `id_client_enfant`, `id_client_contact`, `Type_statut` FROM `Relation_enfant_contacts`"
		." where id_client_enfant=".$id_client." and Type_statut=1 and id_client_contact=".$id_client;
	//echo $requete_recup_client_eleve;
	$db->setQuery($requete_recup_client_eleve);	
	$db->Query();
	if ($db->getNumRows()==0){
	    $requete_recup_client_relation="SELECT min(`Type_statut`) FROM `Relation_enfant_contacts`"
		." where Type_statut>1 and id_client_contact=".$id_client." LIMIT 0,1 ";
	    //echo $requete_recup_client_relation;
	    $db->setQuery($requete_recup_client_relation);	
	    return($db->loadResult());
	}
	else return(1);
	
}

function ajouter_equipement_du_eleve($id_saison,$id_client,$id_equipement,$id_taille) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_insert_Saison_equipement="INSERT INTO `Saison_equipement`(`id_saison`, `id_client`, `id_equipement`, `id_taille`) VALUES ("
	    .$id_saison.",".$id_client.",".$id_equipement.",".$id_taille.")";
	//echo $requete_insert_Saison_equipement;
	$db->setQuery($requete_insert_Saison_equipement);	
	$db->Query();
}


function supprimer_equipement_du_eleve($id_saison,$id_client,$id_equipement="") {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_suppr_Saison_equipement="DELETE FROM `Saison_equipement` WHERE `id_saison`="
	    .$id_saison." and `id_client`=".$id_client;
	    
	if (test_non_vide($id_equipement))
	    $requete_suppr_Saison_equipement.=" and `id_equipement`=".$id_equipement;
	    
	//echo $requete_suppr_Saison_equipement;
	$db->setQuery($requete_suppr_Saison_equipement);	
	$db->Query();
}



function taille_Saison_equipement($id_saison,$id_client,$id_equipement) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_recup_Saison_equipement="SELECT IFNULL((SELECT id_taille FROM `Saison_equipement` WHERE `id_saison`="
	    .$id_saison." and `id_client`=".$id_client." and `id_equipement`=".$id_equipement."),0) ";
	//echo $requete_recup_Saison_equipement;
	$db->setQuery($requete_recup_Saison_equipement);	
	$db->Query();
	return($db->loadResult());
}

function ajouter_regroupement_du_eleve($id_saison,$id_client,$id_type_regroupement) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_insert_regroupement_du_eleve="INSERT INTO `Saison_regroupement`(`id_saison`, `id_client`, `id_type_regroupement`) VALUES ("
	    .$id_saison.",".$id_client.",".$id_type_regroupement.")";
	//echo $requete_insert_regroupement_du_eleve;
	$db->setQuery($requete_insert_regroupement_du_eleve);	
	$db->Query();
}


function supprimer_regroupement_du_eleve($id_saison,$id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_suppr_regroupement_du_eleve="DELETE FROM `Saison_regroupement` WHERE `id_saison`="
	    .$id_saison." and `id_client`=".$id_client;
	    
	//echo $requete_suppr_regroupement_du_eleve;
	$db->setQuery($requete_suppr_regroupement_du_eleve);	
	$db->Query();
}

function recup_type_regroupement_client_Saison($id_saison,$id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_recup_type_regroupement_client_Saison="SELECT IFNULL((SELECT id_type_regroupement FROM `Saison_regroupement` WHERE `id_saison`="
	    .$id_saison." and `id_client`=".$id_client."),0) ";
	//echo $requete_recup_type_regroupement_client_Saison;
	$db->setQuery($requete_recup_type_regroupement_client_Saison);	
	$db->Query();
	return($db->loadResult());
}



function exist_id_client_eleve($id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	if (!exist_id_client($id_client))
	    return(false);
	else {
	    $requete_recup_client_eleve="SELECT `id_client_enfant`, `id_client_contact`, `Type_statut` FROM `Relation_enfant_contacts`"
		." where id_client_enfant=".$id_client." and Type_statut=1 and id_client_contact=".$id_client;
	    //echo $requete_recup_client_eleve;
	    $db->setQuery($requete_recup_client_eleve);	
	    $db->Query();
	    if ($db->getNumRows()==0)
		return(false);
	    else return(true);
	}
}

function recup_dest_publipostage($id_pub){
$db = & JFactory::getDBO();

    $recup_dest_publipostage="SELECT * FROM `Publipostage_type_destinataires` WHERE `id_pub`=".$id_pub;
    $db->setQuery($recup_dest_publipostage);		
    $resultat=$db->loadObjectList();
    $liste_destinatires="";
    foreach($resultat as $elt){
	switch ($elt->id_type_regroupement){
	    case 10000 : $liste_destinatires.="POLICE, ";break;
	    case 10001 : $liste_destinatires.="LEDG, ";break;
	    case 0 : $liste_destinatires.="Particulier, ";break;
	    case ($elt->id_type_regroupement<10000) : $liste_destinatires.=recup_1_element("nom","Type_Regroupement","id",$elt->id_type_regroupement).", ";break;
	    default : $liste_destinatires.="pb requete(".$elt->id_type_regroupement."),";break;
	}
	
    }
    return(substr($liste_destinatires,0,-2));
    
}

function generer_pdf($partie_html,$nom_du_fichier,$Title,$Subject,$Keywords,$sortie){
    // create new PDF document

    class MYPDF extends TCPDF {
    
	//Page header
	public function Header() {
	    // Logo
	    $image_file = K_PATH_IMAGES.'logo.jpg';
	    $this->Image($image_file, 180, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	    // Set font
	    $this->SetFont('times', 'B', 8);

	}
    
	// Page footer
	public function Footer() {
	    // Position at 15 mm from bottom
	    $this->SetY(-20);
	    // Set font
	    $this->SetFont('times', 'I', 8);
	    $pied_page="FOOT IN FIVE - Centre de Football indoor 5vs5 - 187, route de Saint Leu 93800 EPINAY SUR SEINE\n"
	       ."Tel. 01 49 51 27 04 - E-mail : contact@footinfive.com - Site : http://www.footinfive.fr\n"
	       ."Num; : 518 999 776 RCS BOBIGNY - TVA intra. : FR 57 518 999 776";
	    // Page number
	    $this->MultiCell(180, 5, $pied_page, 0, 'C', 0, 0, '', '', true);

	}
    }
    
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IFbi.fr');
$pdf->SetTitle($Title);
$pdf->SetSubject($Subject);
$pdf->SetKeywords($Keywords);

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData($tc=array(0,64,0), $lc=array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('times', '', 10);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));



// Set some content to print
$html = <<<EOD
    $partie_html
EOD;



// Print text using writeHTMLCell()
//$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.


$pdf->Output($nom_du_fichier.'.pdf', $sortie);

//============================================================+
// END OF FILE
//============================================================+
    
    
}



function conditions_generales($fact_ou_dev){
    
    if ($fact_ou_dev=="FACTURE")
        $texte="<tr><td height=\"100%\"><b><u>Conditions de r&egrave;glements :</u></b><br>Par ch&egrave;que &agrave; r&eacute;ception de la facture<br><br></td></tr>"
            ."<tr><td><br>Pas d'escompte en cas de paiement anticip&eacute;"
            ."<br>Int&eacute;r&ecirc;ts de retard pour paiement tardif 3 fois<br>"
            ."le taux de l'int&eacute;r&ecirc;t l&eacute;gal (Loi LME num 2008-776 du 4 ao&ucirc;t 2008).</td></tr>";
    else 
        $texte="<tr><td height=\"100%\"><b><u>Conditions g&eacute;n&eacute;rales :</u></b><br>Un acompte de 30&#37; doit &ecirc;tre d&eacute;pos&eacute; afin de confirmer"
		." votre r&eacute;servation.<br>Le solde se fera le jour de l'&eacute;v&egrave;nement.<br>"
                ."En cas de d&eacute;sistement 48 heures avant, tout acompte ne pourra faire l'objet d'un remboursement.<br>"
                ."Les modes de paiement accept&eacute;s : CB, esp&eacute;ces et ch&egrave;que</td></tr>";
    
    
    return("<br><br><table cellspacing=\"5\" cellpadding=\"5\" border=\"0\" height=\"100%\" >".$texte."</table><br>");
}


?>