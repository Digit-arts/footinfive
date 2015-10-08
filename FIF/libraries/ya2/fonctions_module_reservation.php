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



function connect_ledg(){
	
	/* exemple
	$mysql_fif=connect_fif();

	$result = $mysql_fif->query("select * from Client where id_client=313");
	
	while ($row = $result->fetch_row()) {
		echo $row[0]."--".$row[1]."----".$row[2]."--".$row[3];
	}
	$result->close();
	
	$mysql_fif->close();*/

	$mysqli = new mysqli("localhost", "LEDG20132014TEST", "4lulu9", "LEDG-2013-2014");
	
	/* V�rification de la connexion */
	if (mysqli_connect_errno()) {
	    printf("�chec de la connexion : %s\n", mysqli_connect_error());
	    exit();
	}
	return($mysqli);
}

function recup_rencontre($id_match){

    $mysql_ledg=connect_ledg();
	$query_recup_rencontre = "SELECT CONCAT( `k_title` ,\"<br>\", (SELECT t.t_name FROM vlxhj_bl_teams AS t WHERE  `team1_id` = t.id),\": \", "
	    ." (IF(`score1`>0,`score1`,\"-\")) ,\"<br>\",(SELECT t.t_name FROM vlxhj_bl_teams AS t WHERE  `team2_id` = t.id ),\": \",(IF(`score2` >0,score2,\"-\")) )"
	    ."   as rencontre FROM  `vlxhj_bl_match`  WHERE  `id`=".$id_match;
	//echo $query_recup_rencontre;
	
	$resultat=$mysql_ledg->query($query_recup_rencontre);

        $recup_rencontre = $resultat->fetch_row();
        $resultat->close();
	$mysql_ledg->close();
	
        return ($recup_rencontre[0]);

}

function photo_user($id_user){

    $mysql_ledg=connect_ledg();
	$query_def_img = "select def_img from vlxhj_bl_players where id=".$id_user;
	//echo $query_def_img;
	
	$resultat=$mysql_ledg->query($query_def_img);
        $def_img = $resultat->fetch_row();
        $resultat->close();

	$query ="select ph.ph_filename from vlxhj_bl_photos as ph where ph.id=(if(".$def_img[0].">0,".$def_img[0].",(select max(photo_id) "
		." from vlxhj_bl_assign_photos as ass_ph where ass_ph.cat_type = 1 AND cat_id =".$id_user."))) ";	
	//echo $query;
        $resultat=$mysql_ledg->query($query);
        $row = $resultat->fetch_row();
        $resultat->close();
	$mysql_ledg->close(); 
        return ($row[0]);
}

function existe_joueur_capitaine($id_user){

	$mysql_ledg=connect_ledg();

	$requete_existe_joueur="SELECT u.id, t_name FROM  `vlxhj_users` AS u LEFT JOIN vlxhj_bl_moders ON uid = u.id "
            ." LEFT JOIN vlxhj_bl_teams AS t ON tid = t.id WHERE u.`id` =".$id_user;
	//echo "req4: ".$requete_existe_joueur;
	
	$resultat=$mysql_ledg->query($requete_existe_joueur);
        $row = $resultat->fetch_row();
        $resultat->close();

        if (test_non_vide($row[1]))
            $retour="capitaine de l'&eacute;quipe ".$row[1];
        else if (test_non_vide($row[0])){
            $requete_equipe_joueur="SELECT t_name  FROM `vlxhj_bl_players` as p, vlxhj_bl_teams as t WHERE `team_id`=t.id and p.`id`=".$id_user;
            //echo "req4: ".$requete_equipe_joueur;
            
            $resultat2=$mysql_ledg->query($requete_equipe_joueur);
            $row2 = $resultat2->fetch_row();
            $resultat2->close();
            
            $retour="joueur de l'&eacute;quipe ".$row2[0];
        }
	$mysql_ledg->close();        
        return($retour);
					
}

function liste_users_ledg(){

	$mysql_ledg=connect_ledg();

	$requete_liste_users_ledg="SELECT id FROM `vlxhj_users` WHERE 1 ";
	//echo "req4: ".$requete_liste_users_ledg;
	
	$resultat=$mysql_ledg->query($requete_liste_users_ledg);
        $liste_id_ledg="(";
        while ($row = $resultat->fetch_row())
            $liste_id_ledg.=$row[0].",";
        $liste_id_ledg.="0)";
        
        $resultat->close();
	$mysql_ledg->close();
        return($liste_id_ledg);
					
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

function entite_du_client($id_client){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_nom_du_client="Select concat(\"<b>\",(select tr.nom FROM Type_Regroupement as tr WHERE tr.id=id_type_regroupement),\"</b> \",nom_entite,\"<br>\",nom_service) "
	    ." from Client where id_client=".$id_client;
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

function recup_client_de_resa($id_resa){
$db = & JFactory::getDBO();

	$requete_recup_client="select id_client from Reservation where id_resa=".$id_resa;
	//echo $requete_recup_client;
	$db->setQuery($requete_recup_client);		
	return ($db->LoadResult());

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

function recup_id_match_de_resa($id_resa){
$db = & JFactory::getDBO();

	$requete_recup_id_match_de_resa="select id_match from Reservation where id_resa=".$id_resa;
	//echo $requete_recup_id_match_de_resa;
	$db->setQuery($requete_recup_id_match_de_resa);		
	return ($db->LoadResult());

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
        echo "<tr><th>Ville :	</th><td><select name=\"code_insee".$compl_nom."\">";
    
        if ($code_insee==$recup_insee->code_insee)
            $select_insee=" selected ";
    
        echo "<option value=\"".$recup_insee->code_insee."\" ".$select_insee.">";
        echo $recup_insee->departement." (".$recup_insee->nom_maj_ville." - ".$recup_insee->code_postal.")</option>";
                                            
        echo "</select></td></tr>";
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
                            echo "<tr><th>Code postal :	</th><td><input type=\"text\"  name=\"cp".$compl_nom."\" size=\"2\" maxlength=\"5\" value=\"".$cp."\">*</td></tr>";
                        else echo "<tr><th>Code postal :	</th><td>".$cp."</td></tr> ";
                        break;
                }
		
                case 1 	: {
                        if (isset($modif))
                            echo "<tr><th>Code postal :	</th><td><input type=\"text\"  name=\"cp".$compl_nom."\" size=\"2\" maxlength=\"5\" value=\"".$tab_cp[0]["CP"]."\">*</td></tr>";
                        else echo "<tr><th>Code postal :	</th><td>".$tab_cp[0]["CP"]."</td></tr> ";
                        break;
                }
							
                case ($i>1) : 	{
                                    $select_insee="";
				    echo "<tr><th>Ville :	</th><td><select name=\"code_insee".$compl_nom."\">";
				    while ($i>0){
					$i--;
					echo "<option value=\"".$tab_cp[$i]["INSEE"]."\" ".$select_insee.">";
					echo $tab_cp[$i]["DEP"]." (".$tab_cp[$i]["VILLE"]." - ".$tab_cp[$i]["CP"].")</option>";
				    }
				    echo "</select></td></tr>";
				    break;
                }
            }
        }
	if ($nbre_cp<2){
	    $j=$nbre_villes;
	    switch ($j) {
		case 0	: {
                        if (isset($modif))
                            echo "<tr><th>Ville :	</th><td><input type=\"text\"  name=\"ville".$compl_nom."\" size=\"29\" maxlength=\"100\" value=\"".$ville."\"></td></tr>";
                        else echo "<tr><th>Ville :	</th><td>".$ville."</td></tr>";
                        break;
                }
                        
		case 1 	: {
                        if (isset($modif))
                            echo "<tr><th>Ville :	</th><td><input type=\"text\"  name=\"ville".$compl_nom."\" size=\"29\" maxlength=\"100\" value=\"".$tab_villes[0]["VILLE"]."\"></td></tr>";
                        else echo "<tr><th>Ville :	</th><td>".$tab_villes[0]["VILLE"]."</td></tr>";
                        break;
                }

		case ($j>1) : 	{
                                    $select_insee="";
				    echo "<tr><th>Ville :	</th><td><select name=\"code_insee".$compl_nom."\">";
				    while ($j>0){
					$j--;
					echo "<option value=\"".$tab_villes[$j]["INSEE"]."\" ".$select_insee.">";
					echo $tab_villes[$j]["DEP"]." (".$tab_villes[$j]["VILLE"]." - ".$tab_villes[$j]["CP"].")</option>";
				    }
				    echo "</select></td></tr>";
				    break;
                }
            }
        }
    }
    
}



function recup_recup_client($compl_criteres,$id_client=""){
$db = & JFactory::getDBO();

	$requete_recup_client="select c.nom as nom_client, c.*,v.nom_maj_ville,v.code_postal,Ville_facturation,"
            ." Code_postal_facturation, (select u.name from #__users as u where u.id=c.id_user_modif) as name_user_modif,"
	    ." (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=c.id_user_modif) as group_user_modif,"
            ." (select u.email from #__users as u where u.id=c.id_user) as courriel FROM Client as c "
            ." LEFT JOIN Ville as v on c.code_insee=v.code_insee where 1 ".$compl_criteres." order by c.nom,prenom,c.code_insee";
	//echo "<br>".$requete_recup_client;
	$db->setQuery($requete_recup_client);		
	return ($db->loadObject());

}



function nbre_presta($id_resa){
$db = & JFactory::getDBO();

	$requete_recup_nbre_presta="SELECT count(`id_prestation`) as nbre_presta FROM `Prestation` where prestation_validation=1 and id_resa=".$id_resa;
	//echo $requete_recup_nbre_presta;
	$db->setQuery($requete_recup_nbre_presta);		
	return ($db->LoadResult());

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

function montant_total_presta($id_resa,$complement_date_requete="",$type_tva=""){
$db = & JFactory::getDBO();

	if (test_non_vide($id_resa))
            $complement_req.=" and id_resa=".$id_resa;
            
        if (test_non_vide($type_tva))
            $complement_req.=" and id_TVA=".$type_tva;
            
        if (test_non_vide($complement_date_requete))
            $complement_req.=$complement_date_requete;
        
        $requete_recup_montant_total_presta="SELECT sum(`Montant_TTC`) as montant_total_presta FROM `Prestation` where prestation_validation=1 ".$complement_req;
	//echo $requete_recup_montant_total_presta;
	$db->setQuery($requete_recup_montant_total_presta);		
	return ($db->LoadResult());

}

function nbre_reglements($id_resa){
$db = & JFactory::getDBO();

	$requete_recup_nbre_presta="SELECT count(`id_reglement`) as nbre_regl FROM `Reglement` where validation_reglement=1 and id_reservation=".$id_resa;
	//echo $requete_recup_nbre_presta;
	$db->setQuery($requete_recup_nbre_presta);		
	return ($db->LoadResult());

}

function ajout_presta($id_resa,$type_presta,$montant,$tva){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();

	$requete_ajout_presta="INSERT INTO `Prestation`(`type_prestation`, `id_TVA`, `Montant_TTC`,id_resa,`id_user`, `date_creation`, `heure_creation`) "
				." VALUES (\"".$type_presta."\",".$tva.",".str_replace(",", ".", $montant).",".$id_resa.",".$user->id.",\"".date("Y-m-d")."\",\"".date("H:i")."\") ";
	//echo $requete_ajout_presta;
	$db->setQuery($requete_ajout_presta);		
	return($db->Query());

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


function maj_validation_reglement($validation_reglement,$id_regl,$id_resa=""){
$db = & JFactory::getDBO();
		
	if (test_non_vide($id_resa))
		$compl_req=" id_reservation=".$id_resa;
	
	if (test_non_vide($id_regl))
		$compl_req=" id_reglement=".$id_regl;
	 
	$requete_maj_regl="UPDATE Reglement set  validation_reglement=".$validation_reglement." WHERE ".$compl_req;
	//echo "<br>reqsuppr: ".$requete_maj_regl;
	$db->setQuery($requete_maj_regl);	
		
	return ($db->query());

}

function maj_validation_prestation($validation_prestation,$id_presta,$id_resa=""){
$db = & JFactory::getDBO();
		
	if (test_non_vide($id_resa))
		$compl_req=" id_resa=".$id_resa;
	
	if (test_non_vide($id_presta))
		$compl_req=" id_prestation=".$id_presta;
	 
	$requete_maj_presta="UPDATE Prestation set  prestation_validation=".$validation_prestation." WHERE ".$compl_req;
	//echo "<br>reqsuppr: ".$requete_maj_presta;
	$db->setQuery($requete_maj_presta);	
		
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


function maj_cal_dans_resa($id_resa,$lien){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_maj_cal_dans_resa="UPDATE `Reservation` set indic_annul=0, adresse_resa_google=\"".$lien."\",";
$requete_maj_cal_dans_resa.=" date_valid=\"".date("Y-m-d")."\", heure_valid=\"".date("H:i")."\", id_user=".$user->id." where id_resa=".$id_resa;
//echo "req4: ".$requete_maj_cal_dans_resa;
$db->setQuery($requete_maj_cal_dans_resa);	
$resultat_maj_resa = $db->query();
					
}

function texte_resa($nom_client, $date_debut_resa,$heure_debut_resa,$heure_fin_resa,$montant_total,$pourmail=""){
$user =& JFactory::getUser();

if (test_non_vide($pourmail)) {
	$corps="Bonjour ".$nom_client.",";
	$accent_e="&eacute;";
	$accent_a="&agrave;";
}
else {
	$accent_e="e";
	$accent_a="a";
	
}


$corps.="\n\nVotre r".$accent_e."servation est pr".$accent_e."vue pour le : ".date_longue($date_debut_resa)
        ." \nCr".$accent_e."neau horaire : ".$heure_debut_resa."-".$heure_fin_resa
        ."\nMontant location : ".$montant_total." euros \n(ce montant ne tient pas compte des ".$accent_e."ventuelles r".$accent_e."ductions ou versements effectu".$accent_e."s) ";

if (test_non_vide($pourmail)){
	$corps.="\n\nL'".$accent_e."quipe du Foot In Five vous remercie de votre confiance !"
                ."\n\nA bient&ocirc;t sur nos terrains..."
                ."\n\nFOOT IN FIVE"
                ."\nCentre de FOOT en salle 5vs5"
                ."\n187 Route de Saint-Leu"
                ."\n93800 Epinay-sur-Seine"
                ."\nTel : 01 49 51 27 04"
                ."\nMail : contact@footinfive.com";
	$corps.="<font color=red>\n\n<u>ATTENTION :</u>";
	$corps.="\n - Toute annulation pass".$accent_e."e dans le d".$accent_e."lai des 48h avant votre r".$accent_e."servation ne permettra pas de pouvoir r".$accent_e."cup".$accent_e."rer votre acompte ou caution.";
	$corps.="\n - Si vous etes amen".$accent_e." ".$accent_a." ne pas pouvoir honorer un cr".$accent_e."neau, merci de nous contacter afin de le d".$accent_e."placer ou de l'annuler.";
	$corps.="\n - Tout acompte ne peut etre rembours".$accent_e.". Il sera utilis".$accent_e." comme avoir en cas d'annulation dans le d".$accent_e."lais respectif.</font>";
}
$corps.="\n<hr>Reservation enregistr".$accent_e."e par ".$user->name." (id_user:".$user->id.") ";
$corps.="\nDate validation resa : ".date_longue(date("Y-m-d"))." ".$accent_a." ".date("H:i")."";

return ($corps);
}

function texte_paiement($id_client,$Montant,$date_transac){
$user =& JFactory::getUser();

$corps="Bonjour ".prenom_du_client($id_client).", (num client :".$id_client.")";
$corps.="\n\nVotre paiement par CB d'un montant de ".number_format((str_replace("EUR","",$Montant)/1.196),2)."EUR HT soit ".$Montant." TTC a &eacute;t&eacute; valid&eacute; le ".str_replace("_"," ",$date_transac).".";
$corps.="\n\n L'&eacute;quipe du Foot In Five vous remercie de votre confiance !\n A bientot sur nos terrains...";
$corps.="\n\nFOOT IN FIVE\n\n187 Route de Saint-Leu\n93800 Epinay-sur-Seine\n\nTel : 01 49 51 27 04";

return ($corps);
}

function texte_annul_resa ($id_client, $id_resa){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_recup_resa="select * from Reservation as r where r.id_resa=".$id_resa;
//echo "req1: ".$requete_recup_resa;
$db->setQuery($requete_recup_resa);	
$recup_resa = $db->loadObject();


$corps="Bonjour ".prenom_du_client($id_client)." ".nom_du_client($id_client).","; // (num client :".$id_client.")


$corps.="\n\nVotre r&eacute;servation &eacute;tait pr&eacute;vue pour le : ".date_longue($recup_resa->date_debut_resa)
        ."\nCreneau horaire : ".$recup_resa->heure_debut_resa."-".$recup_resa->heure_fin_resa
        ."\nMontant location : ".$recup_resa->montant_total." euros"
        ."\n\n(ce montant ne tient pas compte des &eacute;ventuelles r&eacute;ductions ou versements effectu&eacute;s)";


$corps.="\n\nL'&eacute;quipe du Foot In Five vous remercie de votre confiance !"
        ."\n\nA bient&ocirc;t sur nos terrains..."
        ."\n\nFOOT IN FIVE"
        ."\nCentre de FOOT en salle 5vs5"
        ."\n187 Route de Saint-Leu"
        ."\n93800 Epinay-sur-Seine"
        ."\nTel : 01 49 51 27 04"
        ."\nMail : contact@footinfive.com";
$corps.="\n<hr>Reservation annulee par ".$user->name." (id_user:".$user->id.") le ".date_longue(date("Y-m-d"))." a ".date("H:i")."";


return ($corps);
}

function gen_clef_contremarque($chars = 4) {
   $letters = 'abcefghjkpqrstwxyz23456789';
   return ("-".substr(str_shuffle($letters), 0, $chars)."-".substr(str_shuffle($letters), 0, $chars));
}

function clef_existe($la_clef){
    
	$db = & JFactory::getDBO();
	
	$requete_clef_existe="SELECT id_client FROM `Contremarque` WHERE `Clef`=\"".$la_clef."\"";
	//echo "req88: ".$requete_clef_existe;
	
	$db->setQuery($requete_clef_existe);	
	$resultat_clef_existe = $db->query();
        $nbre_resultats=$db->getNumRows();
	if ($nbre_resultats>1)
            sendMail(1,"plus de 1 clef generee contremarque : ".$la_clef,"alerte !!!!");
	return($nbre_resultats);
	
}

function nbre_contremarques_utlisees($id_tarif,$date_limite,$date_crea,$heure_crea,$id_client){
    $db = & JFactory::getDBO();
	
	$requete_nbre_clef_deja_utilisee="SELECT `id_reservation` FROM `Reglement` WHERE `Clef` in (SELECT Clef FROM `Contremarque` "
	    ." WHERE `id_tarif`=\"".$id_tarif."\" and `date_limite`=\"".$date_limite."\"  and `date_crea`=\"".$date_crea."\" "
	    ." and `heure_crea`=\"".$heure_crea."\" and `id_client`=\"".$id_client."\" ) ";
	//echo "req88: ".$requete_nbre_clef_deja_utilisee;
	
	$db->setQuery($requete_nbre_clef_deja_utilisee);	
	$db->query();
        return ($db->getNumRows());
    
}

function client_a_contremarque($id_client){
    $db = & JFactory::getDBO();
	
	$requete_client_a_contremarque="SELECT * FROM `Contremarque` WHERE `id_client`=\"".$id_client."\"";
	//echo "req88: ".$requete_client_a_contremarque;
	
	$db->setQuery($requete_client_a_contremarque);	
	$db->query();
        if ($db->getNumRows()>0)
	    return (true);
	else return (false);
    
}

function clef_deja_utilisee($la_clef){
    
	$db = & JFactory::getDBO();
	
	$requete_clef_deja_utilisee="SELECT `id_reservation` FROM `Reglement` WHERE `Clef`=\"".$la_clef."\"";
	//echo "req88: ".$requete_clef_deja_utilisee;
	
	$db->setQuery($requete_clef_deja_utilisee);	
	$db->query();
        $nbre_resultats=$db->getNumRows();
	if ($nbre_resultats>1)
            sendMail(1,"plus de 1 utilisation d'une meme clef contremarque : ".$la_clef,"alerte !!!!");
	
        $requete_clef_deja_utilisee.=" LIMIT 0,1";
        $db->setQuery($requete_clef_deja_utilisee);	
	$db->query();
        
        return($db->LoadResult());
	
}


function recup_id_tarif_d_une_resa($id_resa){
    
	$db = & JFactory::getDBO();
	
	$requete_recup_id_tarif_d_une_resa="SELECT tp.id_tarif FROM `Reservation` as r, Tarif_periode as tp, Tarif_periode_type_terrain as tptt, Terrain as t "
			    ." WHERE r.`id_resa`=".$id_resa." and r.id_terrain=t.id "
			    ." and r.tarif_horaire=tptt.`montant_horaire` and tptt.id_periode=tp.id_periode and tptt.id_type_terrain=t.id_type "
			    ." and date_debut_resa<=tp.`date_fin_periode` and date_debut_resa>=tp.`date_debut_periode` ";
	//echo "req88: ".$requete_recup_id_tarif_d_une_resa;
	
	$db->setQuery($requete_recup_id_tarif_d_une_resa);	
	$db->query();
        
        return($db->LoadResult());
	
}

function recup_taux_TVA_d_une_resa($id_resa){
    
	$db = & JFactory::getDBO();
	
	$requete_recup_taux_TVA_d_une_resa="SELECT tp.taux_TVA FROM `Reservation` as r, Tarif_periode as tp, Tarif_periode_type_terrain as tptt, Terrain as t "
			    ." WHERE r.`id_resa`=".$id_resa." and r.id_terrain=t.id "
			    ." and r.tarif_horaire=tptt.`montant_horaire` and tptt.id_periode=tp.id_periode and tptt.id_type_terrain=t.id_type "
			    ." and date_debut_resa<=tp.`date_fin_periode` and date_debut_resa>=tp.`date_debut_periode` LIMIT 0,1 ";
	//echo "req88: ".$requete_recup_taux_TVA_d_une_resa;
	
	$db->setQuery($requete_recup_taux_TVA_d_une_resa);	
	$db->query();
        
        return($db->LoadResult());
	
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

function ajout_reglement($id_resa,$id_client,$montant,$moyen_paiement,$info="",$remise="",$validation=1,$clef=0){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();
	
	$requete_ajout_reglement="INSERT INTO `Reglement`(id_user,`id_reservation`, `montant_reglement`, `date_reglement`,info,";
	$requete_ajout_reglement.=" `heure_reglement`, `id_moyen_paiement`, `id_type_reglement`, `taux_TVA`, `id_client`, `validation_reglement`,id_remise,Clef)";
	$requete_ajout_reglement.=" VALUES (".$user->id.",".$id_resa.",\"".str_replace(",", ".", $montant)."\",\"".date("Y-m-d")."\",\"".$info."\",";
	$requete_ajout_reglement.=" \"".date("H:i")."\",\"".$moyen_paiement."\",\"\",\"\",".$id_client.",".$validation.",\"".$remise."\",\"".$clef."\")";
	// echo "req88: ".$requete_ajout_reglement;
	$db->setQuery($requete_ajout_reglement);	
	$resultat_ajout_reglement = $db->query();
	$id_regl=$db->insertid();
	
	if ((!test_non_vide($remise)) or $remise==0){
		$requete_maj_resa_a_supprimer="UPDATE Reservation SET a_supprimer=0 where id_resa=".$id_resa;
		$db->setQuery($requete_maj_resa_a_supprimer);	
		$resultat_maj_resa_a_supprimer = $db->query();
	}
	
	return($id_regl);
}

function maj_resa_a_supprimer($id_resa){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();
	
	$requete_maj_resa_a_supprimer="UPDATE Reservation as r SET a_supprimer=1 where (r.indic_annul<>1) and r.id_resa=".$id_resa." and "
	    ." ((SELECT c.`accompte_necessaire` FROM `Client` as c WHERE c.`id_client`=r.`id_client`)=0)  and "
	    ." ( (select count(`id_reservation`) from Reglement where validation_reglement=1 "
	    ." and id_reservation=".$id_resa." and (id_remise is NULL or id_remise=0) )=0 "
	    ." and (TIMESTAMPDIFF(MINUTE,CAST(concat(r.date_valid,\" \",r.heure_valid) AS CHAR(22)),NOW())>5) "
	    ." and r.accompte_necessaire=0 and r.cautionnable=0)"
	    ." OR (r.indic_venue=3 and r.cautionnable in (1,2)) "
	    ." OR ((TIMESTAMPDIFF(MINUTE,CAST(concat(r.date_suppr_caution,\" \",r.heure_suppr_caution) AS CHAR(22)),NOW())>1440) "
	    ." AND r.cautionnable=2) ";
	//echo "req88: ".$requete_maj_resa_a_supprimer;
	
	$db->setQuery($requete_maj_resa_a_supprimer);	
	$resultat_maj_resa_a_supprimer = $db->query();
	
	return($resultat_maj_resa_a_supprimer);
	
}


function annuler_resa($id_resa,$motif=""){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();
	
	
	
	$requete_recup_resa="select * from Reservation as r where r.indic_annul=0 and r.id_resa=".$id_resa;
	//echo "req1: ".$requete_recup_resa;
	$db->setQuery($requete_recup_resa);	
	$recup_resa = $db->loadObject();
	
	    save_resa($id_resa);
	    suppr_event_cal_google($id_resa);
	    
    
	    
	    if (diff_dates_en_minutes($recup_resa->date_debut_resa,$recup_resa->heure_debut_resa)<=-2880) {
		    //on recredites le compte si plus de 48h avant la resa
		$total_versement=versements_sans_remise_et_avec_validation($id_resa,"5,9,10");	    
		    if ($total_versement>0){
			    if (test_non_vide(ajout_credit($recup_resa->id_client,$total_versement,1,"Annul_resa_".$id_resa,2))){
				ajout_reglement($id_resa,$recup_resa->id_client,(-1*$total_versement),7,"Annul_resa");
				$mess_retour="<a href=\"index.php/component/content/article?id=77&id_client=".$recup_resa->id_client."\">Votre compte </a> a &eacute;t&eacute; cr&eacute;dit&eacute; de <font color=red>".$total_versement." euros</font>.<br>";
			    }
		    }
	    }	
	    
	    $requete_annul_resa="UPDATE Reservation set indic_annul=1,adresse_resa_google=\"\" , id_user=".$user->id.", date_valid=\"".date("Y")."-".date("m")."-".date("d")."\"";
	    $requete_annul_resa.=", heure_valid=\"".Ajout_zero_si_absent(date("H:i"))."\", id_Motif_annul_resa=".$motif." WHERE id_resa=".$id_resa;
	    // echo "req3: ".$requete_annul_resa;
	    $db->setQuery($requete_annul_resa);	
	    $resultat_annul_resa = $db->query();
	    $nbre_resultats=$db->getNumRows();
    
	    if ($nbre_resultats>0)
		    sendMail(267,"Annul resa : ".$id_resa." ","");
		    
	    //$resultat_annul_regl = maj_validation_reglement(0,"",$id_resa); //ne pas activer car sinon pas comptabilis� dans la feuille de caisse...
	    $resultat_annul_presta = maj_validation_prestation(0,"",$id_resa);

return($mess_retour);	

}

function versements_sans_remise_et_avec_validation($id_resa,$exclure_moyens_paiement=""){
$db = & JFactory::getDBO();

if (test_non_vide($exclure_moyens_paiement))
    $compl_req=" and reg.id_moyen_paiement not in (0,".$exclure_moyens_paiement.") ";
else $compl_req="";

$requete_versement="select format(sum(reg.montant_reglement),2) as total_versement from Reglement as reg "
    ."  where reg.validation_reglement=1 ".$compl_req." and reg.id_reservation=".$id_resa." and (reg.id_remise is null or reg.id_remise=0)"; 
					
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

function supprimer_erreurs_saisie_credit($id_cred){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();
	
	$requete_annul_credit="UPDATE  Credit_client set  validation_credit=0 WHERE id_credit_client=".$id_cred;
	//echo "<br>reqsuppr: ".$requete_annul_credit;
	$db->setQuery($requete_annul_credit);	
	$resultat_annul_credit = $db->query();
	

		
	echo "<font color=red>";
	if ($resultat_annul_credit)
		echo "credit supprim&eacute;";
	else echo "Erreur : credit inexistant";
	echo "<br></font>";	
	
	$requete_recup_infos_credit="select * from Credit_client where id_credit_client=".$id_cred;
	//echo "req1: ".$requete_recup_infos_credit;
	$db->setQuery($requete_recup_infos_credit);	
	$infos_credit = $db->loadObject();
	
	save_credit_client($infos_credit->id_client,"",$id_cred);
	suppr_credit_client($infos_credit->id_client,"",$id_cred);
	
	if ($infos_credit->type_credit==2)		
		maj_cautionnement_des_resas($infos_credit->id_client,2);

}

function proprietaire_resa($id_resa){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	if (est_register($user) and test_non_vide($id_resa)) {
		///////// Si qqun a saisit directement le num resa dans la barre adresse sauf pour les agents
		$requete_recup_resa="select * from Reservation as r, Client as c where r.id_resa=".$id_resa." and r.id_client=c.id_client and c.id_user=".$user->id;
		$db->setQuery($requete_recup_resa);	
		$db->query();
		$nbre_resas=$db->getNumRows();
		if ($nbre_resas==0) {echo "Vous n &ecirc;tes pas propri&eacute;taire de cette r&eacute;servation.";exit;}
		else return (true);
	}
}

function avoir_deja_attribuer($id_resa){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	if (test_non_vide($id_resa)) {
		$requete_recup_id_credit="SELECT id_credit_client FROM  `Credit_client` WHERE `origine_credit`=\"Reactiv-".$id_resa."\"";
		$db->setQuery($requete_recup_id_credit);	
		$db->query();
		if ($db->getNumRows()>0)
			return (true);
		else return (false);
	}
}

function supprimer_caution($id_client,$id_resa){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	$requete_recup_resa="select * from Reservation as r where r.cautionnable=1 and r.id_resa=".$id_resa." and r.id_client=".$id_client;
	//echo "req1: ".$requete_recup_resa;
	$db->setQuery($requete_recup_resa);	
	$recup_resa = $db->loadObject();
	
	$les_versements=versements_sans_remise_et_avec_validation($id_resa);
	
	if (test_non_vide($recup_resa->id_resa) and ($les_versements=="" or $les_versements==0)){
		$montant_a_deduire_de_la_caution_total=calcul_acompte($recup_resa->date_debut_resa,$recup_resa->heure_debut_resa,$recup_resa->montant_total);

		$caution_avant_suppr=recup_caution_total_client($id_client);

		save_credit_client($id_client,2);
		suppr_credit_client($id_client,2);
		
		$reste=$caution_avant_suppr-str_replace(",", ".",$montant_a_deduire_de_la_caution_total);
		if (test_non_vide($reste) and $reste>0)
			ajout_credit($id_client,$reste,2,"Reste_sup_caut_resa_".$id_resa,2);
			
		maj_cautionnement_des_resas($id_client,2);
	}
}

function recup_caution_total_client($id_client){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_recup_montant_caution_total="SELECT IFNULL((SELECT sum(credit) FROM Credit_client where type_credit=2 and validation_credit=1 and id_client="
    .$id_client."),0) ";
// echo $requete_recup_montant_caution_total;
$db->setQuery($requete_recup_montant_caution_total);	
return($db->loadResult());

}

function recup_credit_total_client($id_client){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_recup_montant_credit_total="SELECT IFNULL((SELECT sum(credit) FROM Credit_client where type_credit=1 and validation_credit=1 and id_client="
    .$id_client."),0) ";
// echo $requete_recup_montant_credit_total;
$db->setQuery($requete_recup_montant_credit_total);	
return($db->loadResult());

}

function maj_cautionnement_des_resas($id_client,$cautionnable,$id_resa="",$date_resa=""){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();
	
	$resultat_recup_montant_caution_total = recup_caution_total_client($id_client);
	
	if ($cautionnable==1) {
		
		if (test_non_vide($id_resa) and test_non_vide($date_resa) and $resultat_recup_montant_caution_total>0){
			if (test_limite_nbre_resa_par_caution($date_resa,$id_client))
				$juste_cette_resa="and id_resa=".$id_resa;
			else return (1);
		}
		else {
			$date_heure_suppr=", date_suppr_caution=\"\", heure_suppr_caution=\"\"";
			$signe="<=";
		}
	}
	if ($cautionnable==2) {
		$date_heure_suppr=", date_suppr_caution=\"".date("Y-m-d")."\", heure_suppr_caution=\"".date("H:i")."\" ";
		$pas_de_regl_passer= " and id_resa not in (select distinct(`id_reservation`) from Reglement as reg where ";
		$pas_de_regl_passer.= " validation_reglement=1 and (id_remise is NULL or id_remise=0) and reg.id_client=".$id_client.")";
		$signe=">";
	}
	
	$requete_maj_caution_resa="UPDATE Reservation set cautionnable=".$cautionnable." ".$date_heure_suppr." where indic_annul<>1 ";
	if (test_non_vide($juste_cette_resa)){
		$requete_maj_caution_resa.=$juste_cette_resa;
	}
	else {
		$requete_maj_caution_resa.=" and montant_total".$signe.ceil($resultat_recup_montant_caution_total*4)." ".$pas_de_regl_passer;
		$requete_maj_caution_resa.=" and not(date_debut_resa<\"".date("Y-m-d")."\") and id_client=".$id_client;
	}
	// echo $requete_maj_caution_resa;
	$db->setQuery($requete_maj_caution_resa);	
	$db->Query();
	
	if ($cautionnable==2) {
		$requete_maj_caution_resa="UPDATE Reservation set cautionnable=0 , date_suppr_caution=\"\", heure_suppr_caution=\"\" where indic_annul<>1 ";
		$requete_maj_caution_resa.=" and montant_total".$signe.ceil($resultat_recup_montant_caution_total*4);
		$requete_maj_caution_resa.=" and id_resa in (select distinct(`id_reservation`) from Reglement as reg where ";
		$requete_maj_caution_resa.= " validation_reglement=1 and (id_remise is NULL or id_remise=0) and reg.id_client=".$id_client.")";
		$requete_maj_caution_resa.=" and not(date_debut_resa<\"".date("Y-m-d")."\") and id_client=".$id_client;
		// echo $requete_maj_caution_resa;
		$db->setQuery($requete_maj_caution_resa);	
		$db->Query();
	}

}

function save_resa($num_resa,$new_date_debut_resa="",$new_date_fin_resa="",$new_heure_debut_resa="",$new_heure_fin_resa="",$new_montant_total=""){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_recup_resa="select * from Reservation as r where r.id_resa=".$num_resa;
	//echo "req1: ".$requete_recup_resa;
	$db->setQuery($requete_recup_resa);	
	$recup_resa = $db->loadObject();
	
	if (!$recup_resa) echo "pb base";
	else {
		$requete_hist_resa="INSERT INTO `Hist_Reservation`(`id_resa`, `id_user`, id_client,`id_user_old`, ";
		$requete_hist_resa.=" `date_debut_old_resa`, `date_fin_old_resa`, `heure_deb_old_resa`,`heure_fin_old_resa`,old_adresse_resa_google, ";
		$requete_hist_resa.=" `date_debut_new_resa`,`date_fin_new_resa`, `heure_deb_new_resa`, ";
		$requete_hist_resa.=" `heure_fin_new_resa`, `date_modif_resa`, `heure_modif_resa`, `montant_old_resa`, `montant_new_resa`)";
		$requete_hist_resa.=" VALUES (".$recup_resa->id_resa.",".$user->id.", ".$recup_resa->id_client.", ".$recup_resa->id_user.",\"".$recup_resa->date_debut_resa."\",";
		$requete_hist_resa.=" \"".$recup_resa->date_fin_resa."\",\"".Ajout_zero_si_absent($recup_resa->heure_debut_resa)."\",";
		$requete_hist_resa.=" \"".Ajout_zero_si_absent($recup_resa->heure_fin_resa)."\",\"".$recup_resa->adresse_resa_google."\",\"".$new_date_debut_resa."\",";
		$requete_hist_resa.=" \"".$new_date_fin_resa."\",\"".Ajout_zero_si_absent($new_heure_debut_resa)."\",";
		$requete_hist_resa.=" \"".Ajout_zero_si_absent($new_heure_fin_resa)."\",";
		$requete_hist_resa.=" \"".$recup_resa->date_valid."\",\"".$recup_resa->heure_valid."\",";
		$requete_hist_resa.=" ".$recup_resa->montant_total.",".$new_montant_total.")";
		//echo "req2: ".$requete_hist_resa;
		$db->setQuery($requete_hist_resa);	
		$resultat_hist_resa = $db->query();
	}
}

function suppr_credit_client($id_client,$type_credit="",$id_credit=""){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

    $requete_suppr_credit="DELETE FROM `Credit_client` WHERE  id_client=".$id_client;
    
    if (test_non_vide($type_credit))
            $requete_suppr_credit.=" and type_credit=".$type_credit;
            
    if (test_non_vide($id_credit))
            $requete_suppr_credit.=" and id_credit_client=".$id_credit;
            
    // echo "<br>reqsuppr: ".$requete_suppr_credit;
    $db->setQuery($requete_suppr_credit);	
    return($db->query());

}

function save_credit_client($id_client,$type_credit="",$id_credit=""){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_trouve_credit="select * from `Credit_client` WHERE id_client=".$id_client;

if (test_non_vide($type_credit))
	$requete_trouve_credit.=" and type_credit=".$type_credit;
	
if (test_non_vide($id_credit))
	$requete_trouve_credit.=" and id_credit_client=".$id_credit;
	
//echo "<br>reqtrouve: ".$requete_trouve_credit;
$db->setQuery($requete_trouve_credit);	
$resultat_trouve_credit = $db->loadObjectList();
												
	foreach($resultat_trouve_credit as $save_credit){
		$requete_save_credit="INSERT INTO `Hist_Credit_client`(`id_user_suppr`, `date_suppr`, `heure_suppr`, ";
		$requete_save_credit.=" `id_credit_client`, `id_client`, `id_user_creation`, `type_credit`,id_moyen_paiement, `credit`, `unite_credit`, ";
		$requete_save_credit.=" `date_credit`, `heure_credit`, `origine_credit`,validation_credit)";
		$requete_save_credit.=" VALUES (".$user->id.",\"".date("Y")."-".date("m")."-".date("d")."\",\"".date("H").":".date("i")."\",";
		$requete_save_credit.=" ".$save_credit->id_credit_client.",".$save_credit->id_client.",".$save_credit->id_user_creation.",";
		$requete_save_credit.=" ".$save_credit->type_credit.",".$save_credit->id_moyen_paiement.",\"".$save_credit->credit."\",";
		$requete_save_credit.=" ".$save_credit->unite_credit.",\"".$save_credit->date_credit."\",";
		$requete_save_credit.=" \"".$save_credit->heure_credit."\",\"".$save_credit->origine_credit."\",\"".$save_credit->validation_credit."\")";
		// echo "<br>reqsave: ".$requete_save_credit;
		$db->setQuery($requete_save_credit);	
		$resultat_save_credit = $db->query();
	}
}

function ajout_credit($id_client,$montant,$type_credit,$origine_credit,$id_moyen_paiement,$validation=1,$info_credit=""){

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

$requete_trouve_credit="select id_credit_client from `Credit_client` "
                    ." WHERE id_client=".$id_client
                    ." AND id_user_creation=".$user->id
                    ." AND type_credit=\"".$type_credit."\""
                    ." AND credit=".str_replace(",", ".",$montant)
                    ." AND date_credit=\"".date("Y-m-d")."\""
                    ." AND heure_credit=\"".date("H:i")."\""
                    ." AND origine_credit=\"".$origine_credit."\""
                    ." AND id_moyen_paiement=".$id_moyen_paiement
                    ." AND validation_credit=".$validation
                    ." AND info_credit=\"".$info_credit."\"";
                    
$db->setQuery($requete_trouve_credit);	
$db->query();
$nbre_resultats=$db->getNumRows();

if ($nbre_resultats==0){
    $requete_ajout_credit="INSERT INTO `Credit_client`( ";
    $requete_ajout_credit.=" `id_client`, `id_user_creation`, `type_credit`, `credit`, `unite_credit`, ";
    $requete_ajout_credit.=" `date_credit`, `heure_credit`, `origine_credit`,id_moyen_paiement,validation_credit,info_credit)";
    $requete_ajout_credit.=" VALUES (".$id_client.",".$user->id.",\"".$type_credit."\",".str_replace(",", ".",$montant).",1,";
    $requete_ajout_credit.=" \"".date("Y-m-d")."\",\"".date("H:i")."\",";
    $requete_ajout_credit.=" \"".$origine_credit."\",".$id_moyen_paiement.",".$validation.",\"".$info_credit."\")";
    //echo "<br>reqajout_cred: ".$requete_ajout_credit;
    $db->setQuery($requete_ajout_credit);	
    $db->query();
    
    return($db->insertid());
}


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
		if (est_min_agent($user)) {
			echo " <a href=\"index.php?option=com_content&view=article&id=64\"\">";
			echo " <img src=\"http://footinfive.com/LEDG/images/stories/feuille-de-match-scan.png\" title=\"Scan Feuille de match\" width=\"24\" height=\"24\"></a>";
		}
		if (est_min_manager($user)){
                        echo " <a href=\"http://footinfive.com/FIF/libraries/ya2/extraction-client.php\" target=\"_blank\">";
                        echo " <img src=\"images/Excel-icon.png\" title=\"Extraction Client Excel\" width=\"24\" height=\"24\"></a>";
			
			

			echo " <a href=\"index.php?option=com_content&view=article&id=68\">";
			echo " <img src=\"images/agent-fif-icon.png\" title=\"Les agents\" width=\"24\" height=\"24\"></a>";

			echo " <a href=\"index.php?option=com_content&view=article&id=65\"\">";
			echo " <img src=\"images/publipostage-icon.png\" title=\"publipostage\" width=\"24\" height=\"24\"></a>";

                        echo " <a href=\"index.php?option=com_content&view=article&id=66\">";
                        echo " <img src=\"images/gestion-tarif.png\" title=\"gestion des horaires et tarifs\" width=\"24\" height=\"24\"></a>";
			
			echo " <a href=\"http://footinfive.com/FIF/libraries/ya2/extraction-credit.php\" target=\"_blank\">";
                        echo " <img src=\"images/Excel-icon.png\" title=\"Extraction Credit Excel\" width=\"24\" height=\"24\"></a>";
		}

echo "</td><td align=\"right\" width=\"50%\"  valign=\"middle\">";
if (test_non_vide($id_client) and est_min_register($user)){
			echo " <a href=\"index.php/component/content/article?id=62&id_client=".$id_client."\"/>";
			echo " <img src=\"images/creer-resa.png\" title=\"r&eacute;server\"></a>";

			echo " <a href=\"index.php/component/content/article?id=59&id_client=".$id_client."\"/>";
			echo " <img src=\"images/icon-creneau-reserver.png\" title=\"les r&eacute;servations ".$pour_les_agents."\" width=\"24\" height=\"24\"></a>";

			echo " <a href=\"index.php/component/content/article?id=80&id_client=".$id_client."\"/>";
			echo " <img src=\"images/coins-icon.png\" title=\"les r&egrave;glements ".$pour_les_agents."\" width=\"24\" height=\"24\"></a>";

			echo " <a href=\"index.php/component/content/article?id=77&credit=1&id_client=".$id_client."\">";
			echo " <img src=\"images/credit-icon.png\" title=\"les cr&eacute;dits ".$pour_les_agents."\" width=\"24\" height=\"24\"></a>";
                    
                        echo " <a href=\"index.php?option=com_content&view=article&id=67&id_client=".$id_client."\">";
			echo " <img src=\"images/contremarque-icon.png\" title=\"Contremarque\" width=\"24\" height=\"24\"></a>";
                    
			echo " <a href=\"index.php/component/content/article?id=60";
			if (!est_min_register($user)) echo "&modif=1";
			echo "&id_client=".$id_client."\">";
			echo " <img src=\"images/Fiche-client-icon.png\" title=\"La fiche ".$pour_les_agents."\"></a>";

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
			if (test_non_vide(entite_du_client($id_client)))
			    echo "<br>".entite_du_client($id_client)."";
	echo "</td></tr>";	
echo "</table><hr><br>";
}

function recup_nom_feuille_match($id_match){
	$chemin="/var/www/vhosts/footinfive.com/httpdocs/LEDG/Feuilles-de-matchs/";
	if (is_file($chemin.$id_match.'.pdf'))
		return($id_match.'.pdf');
	
	if (is_file($chemin.$id_match.'.jpg'))
		return($id_match.'.jpg');

	if (is_file($chemin.$id_match.'.png'))
		return($id_match.'.png');
	
	if (is_file($chemin.$id_match.'.jpeg'))
		return($id_match.'.jpeg');
	
	if (is_file($chemin.$id_match.'.PDF'))
		return($id_match.'.PDF');
	
	if (is_file($chemin.$id_match.'.JPG'))
		return($id_match.'.JPG');

	if (is_file($chemin.$id_match.'.PNG'))
		return($id_match.'.PNG');
	
	if (is_file($chemin.$id_match.'.JPEG'))
		return($id_match.'.JPEG');
return("");
}

function calcul_acompte($date_mysql,$horaire,$montant){

	/*if (diff_dates_en_minutes($date_mysql,$horaire)>-2880)  //moins de 48h
		$caution=$montant;
	else */
	$temp=((str_replace(",","",$montant))/4)/10;
	if ($temp<2)
	    $acompte=ceil($temp)*10;
	else $acompte=floor($temp)*10;
	return($acompte);							

}

function recup_fin_heure_remise(){
  $db = & JFactory::getDBO();
  
    $requete_recup_heure_fin_creuse_avec_remises="SELECT heure_fin FROM `Plage_tarif` WHERE id_plage_tarif=1 ";
	//echo "req88: ".$requete_recup_heure_fin_creuse_avec_remises;
		
	$db->setQuery($requete_recup_heure_fin_creuse_avec_remises);	
	$db->query();
	return($db->loadResult());
}

function menu_deroulant($Table,$old_select,$fonction="",$type="",$is_detail_credit_client=0,$date_deb_resa="",$date_fin_resa="",$heure_fin_resa="",$id_type_regroupement=-1){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select * from ".$Table;
	//echo $requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	echo "<select name=".$Table;
	if (test_non_vide($fonction))
		echo " onChange=\"".$fonction."\"";
	echo ">";
	echo "<option value=\"\" ></option>";
	foreach($resultat_recup_liste as $recup_liste){
		/*if (est_min_manager($user) and $recup_liste->id==5){
			echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom."</option>";
		}
		else {*/
			/*if ($recup_liste->id<>5){*/
			    if ($old_select==$recup_liste->id) $select=" selected ";
			    else $select="";
			    //$type_credit==2 c'est caution, // $type==3 c'est remboursement
			    if (!((test_non_vide($type) and $type==3 and $recup_liste->id>3) ))
				if (!(est_register($user) and ($recup_liste->id<>2 and $recup_liste->id<>8) and $Table<>"Terrain"))
				    if ($recup_liste->is_active==1 or ($type==1 and $recup_liste->id<>7) )
					if ($is_detail_credit_client==0 or ($is_detail_credit_client==1 and ($type<>1 or ($recup_liste->id<>10 and $recup_liste->id<>2))))
					    if (($Table=="Remise" and $recup_liste->id==2  and (recup_date_tarif($date_deb_resa,1,0)<6) and $id_type_regroupement==0 
						and diff_dates_en_minutes($date_deb_resa,$heure_fin_resa,$date_fin_resa,recup_fin_heure_remise())>=0
						and diff_dates_en_minutes($date_deb_resa,$heure_fin_resa,$date_fin_resa,recup_fin_heure_remise())<720)
					        or $recup_liste->id<>2 or $Table<>"Remise" or est_min_manager($user))
						    if (($Table=="Remise" and in_array($recup_liste->id, array(3,7,10)) and ($id_type_regroupement==3 or $id_type_regroupement==0))
							or $recup_liste->id==2 or $Table<>"Remise" or est_min_manager($user))
							    echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom."</option>";
			/*}
		}*/
	}
	echo "</select>";
}

function menu_deroulant_simple($Table,$old_select,$criteres,$fonction="enregistrer()"){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select * from ".$Table." where 1 ".$criteres." order by nom asc";
	//echo "<br><br>".$requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	echo "<select name=".$Table." \" onChange=\"".$fonction."\" >";
	echo "<option value=\"\" selected></option>";
	foreach($resultat_recup_liste as $recup_liste){
		if ($old_select==$recup_liste->id) $select=" selected ";
		else $select="";
		
                echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom."</option>";
        }
	echo "</select>";
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

function requete_resas_a_supprimer($id_resa=""){

	if (test_non_vide($id_resa)) $comp_req=" and  r.id_resa=".$id_resa;
	else $comp_req="";
	
	$requete_recup_resa="Select r.adresse_resa_google, r.id_resa, r.id_client, r.indic_venue, r.cautionnable, r.indic_annul from `Reservation` as r "
	    ." where ((SELECT c.`accompte_necessaire` FROM `Client` as c WHERE c.`id_client`=r.`id_client`)=0) "
	    ." and (r.indic_annul<>1) ".$comp_req." and ((r.a_supprimer=1 "
	    ." and (TIMESTAMPDIFF(MINUTE,CAST(concat(r.date_valid,\" \",r.heure_valid) AS CHAR(22)),NOW())>5)"
	    ." and r.accompte_necessaire=0"
	    ." and r.cautionnable=0 ) "
	    ." or (r.indic_venue=3 and r.cautionnable in (1,2))"
	    ." or ((TIMESTAMPDIFF(MINUTE,CAST(concat(r.date_suppr_caution,\" \",r.heure_suppr_caution) AS CHAR(22)),NOW())>1440) "
	    ." and r.cautionnable=2 and (r.montant_total>(select sum(reg1.montant_reglement) from Reglement as reg1 "
	    ." where reg1.id_reservation=r.id_resa and reg1.validation_reglement=1))))";
		//si la resa a plus de 24h = 1440 mins 
		// echo $requete_recup_resa;
	//echo $requete_recup_resa."<br>";
	
	return ($requete_recup_resa);
}

function maj_resa_ledg(){

$db = & JFactory::getDBO();

    	//creation des rdv google pour les resas sans adresse google
        $requete_recup_resa_sans_adresse_resa_google="SELECT * FROM  `Reservation` WHERE  `id_client` =3586 AND  `adresse_resa_google`=\"\" ";
	$db->setQuery($requete_recup_resa_sans_adresse_resa_google);
	$db->query();
        $resultat_recup_resa_sans_adresse_resa_google = $db->loadObjectList();

        /*if (test_non_vide($resultat_recup_resa_sans_adresse_resa_google))
            foreach($resultat_recup_resa_sans_adresse_resa_google as $resa_sans_adresse_resa_google){
                $rdv=ajout_event_cal_google($resa_sans_adresse_resa_google->id_resa);
                //maj_cal_dans_resa($resa_sans_adresse_resa_google->id_resa,$rdv->getEditLink()->href);
            }*/
        
        //suppression des resa sans _id_match
        $requete_recup_resa_annulees_par_ledg="SELECT * FROM  `Reservation` WHERE  `id_client` =3586 AND  `id_match`=\"\" ";
	$db->setQuery($requete_recup_resa_annulees_par_ledg);
	$db->query();
        $resultat_recup_resa_annulees_par_ledg = $db->loadObjectList();

        if (test_non_vide($resultat_recup_resa_annulees_par_ledg))
            foreach($resultat_recup_resa_annulees_par_ledg as $resa_annulees_par_ledg){
                /*if ($resa_annulees_par_ledg->adresse_resa_google<>"")
                    suppr_event_cal_google($resa_annulees_par_ledg->id_resa);*/
                supprimer_1_element("Reservation","id_resa",$resa_annulees_par_ledg->id_resa);
                supprimer_1_element("Hist_Reservation","id_resa",$resa_annulees_par_ledg->id_resa);
            }    
        

}

function nettoyer_resa_non_payees(){

$db = & JFactory::getDBO();

	if (test_non_vide($_GET["venue"])) {
		$requete_recup_venue_actuelle="SELECT `indic_venue` FROM `Reservation` where `id_resa`=".$_GET["venue"];
		//echo $requete_recup_venue_actuelle."<br>";
		$db->setQuery($requete_recup_venue_actuelle);
		$resultat_recup_venue_actuelle = $db->loadResult();
		
		if ($resultat_recup_venue_actuelle<3) $new_etat_venue=$resultat_recup_venue_actuelle+1;
		else $new_etat_venue=1;
		
		$requete_maj_venue="update Reservation set `indic_venue`=".$new_etat_venue." where `id_resa`=".$_GET["venue"];
		//echo $requete_maj_venue;
		$db->setQuery($requete_maj_venue);
		$resultat_maj_venue = $db->query();
		
	}

	$db->setQuery(requete_resas_a_supprimer());
	$db->query();
	$nbre_resultats=$db->getNumRows();
	

	if ($nbre_resultats>0){
		$resultat_recup_resa = $db->loadObjectList();
		
		foreach($resultat_recup_resa as $recup_resa){ 
			if ($recup_resa->indic_venue==3 and $recup_resa->cautionnable==1){
				echo "Vous avez indiqu&eacute; que le client de la resa (<font color=red>".$recup_resa->id_resa."</font>) n'est pas venu.<br>";
				echo "Confirmez-vous cette information ? ";
				echo "<a href=\"index.php/component/content/article?id=59&ttes=1";
				echo "&caution_suppr=".$recup_resa->id_client."&suppr=".$recup_resa->id_resa."\" />oui</a> ou ";
				echo "<a href=\"index.php/component/content/article?id=59&ttes=1";
				echo "&venue=".$recup_resa->id_resa."&id_resa=".$recup_resa->id_resa."\" />non</a>";
				echo "<br> <font color=red>En cliquant sur oui, sa caution sera perdue et toutes ses reservations";
				echo " &agrave; venir seront en sursis pendant 24h pour laisser le temps au client de reverser une caution.</font><hr>";
			}
			else annuler_resa($recup_resa->id_resa,7);
		}
	}
}

function horaire_ouverture(){
$db = & JFactory::getDBO();

    $requete_recup_horaire_ouverture="SELECT `valeur_parametre` FROM `Parametres` WHERE `nom_parametre`=\"heure_ouverture\" ";
	//echo $requete_recup_horaire_ouverture."<br>";
    $db->setQuery($requete_recup_horaire_ouverture);
    return($db->loadResult());

}

function horaire_fermeture(){
$db = & JFactory::getDBO();

    $requete_recup_horaire_fermeture="SELECT `valeur_parametre` FROM `Parametres` WHERE `nom_parametre`=\"heure_fermeture\" ";
	//echo $requete_recup_horaire_fermeture."<br>";
    $db->setQuery($requete_recup_horaire_fermeture);
    return($db->loadResult());

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


function test_nuit_avant_ouverture($horaire){

	if (diff_dates_en_minutes("1970-01-01",$horaire,"1970-01-01",horaire_ouverture())<=0) return(0);	
	else return(1);	
	
}


function test_horaires_ouverture($horaire1,$horaire2){
	
	if ($horaire1=="") $horaire1=horaire_ouverture();
	if ($horaire2=="") $horaire2=horaire_fermeture();
	
	list($heure1,$minutes1) = explode(':', $horaire1);
	list($heure2,$minutes2) = explode(':', $horaire2);
	list($heure_ouverture,$minutes_ouverture) = explode(':', horaire_ouverture());
	list($heure_fermeture,$minutes_fermeture) = explode(':', horaire_fermeture());
	
	if ((($heure1+0)<($heure_ouverture+0)) and (($heure2+0)<($heure_ouverture+0))) $date1="1970-01-02";
	else $date1="1970-01-01";
	
	if (($heure2+0)<($heure_ouverture+0)) $date2="1970-01-02";
	else $date2="1970-01-01";
	
	if (($heure_fermeture+0)<($heure_ouverture+0)) $date_fermeture="1970-01-02";
	else $date_fermeture="1970-01-01";
	
	//echo "<br>testhor Ouv :".$date1."**".$horaire1."**"."1970-01-01"."**".horaire_ouverture();
	//echo "<br>testhor Ferm :".$date2."**".$horaire2."**".$date_fermeture."**".horaire_fermeture();
	
	if (diff_dates_en_minutes($date1,$horaire1,"1970-01-01",horaire_ouverture())>0) return(0);
	if (diff_dates_en_minutes($date2,$horaire2,$date_fermeture,horaire_fermeture())<0) return(0);
	//echo "<br>bonne sortie";	
	return(1);	
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

function recup_date_tarif($date,$type_retour=0,$type_terrain){
    $db = & JFactory::getDBO();
    
    	$requete_si_jour_ferie="SELECT * FROM Jours_feries where date=\"".$date."\"";
	//echo "req: ".$requete_si_jour_ferie;
	$db->setQuery($requete_si_jour_ferie);
	$db->query();
	$nbre_resultats=$db->getNumRows();
	
	if ($nbre_resultats>0) $numero_jour_de_la_semaine=7;//c'est comme dimanche
	else {
		list($annee,$mois,$jour) = explode('-', $date);
		$numero_jour_de_la_semaine=date("N", mktime(0, 0, 0, $mois, $jour, $annee));
	}
	$tarif_special=" and tp.date_debut_periode<=\"".$date."\" and tp.date_fin_periode>=\"".$date."\" ";
	$tarif_par_defaut=" and tp.tarif_par_defaut=1 ";
	
	$requete_recup_tarif="SELECT * FROM `Plage_tarif` as pt, Tarif as t, Tarif_periode as tp, Tarif_periode_type_terrain as tptt "
					." where pt.`id_tarif`=t.`id_tarif` and tp.id_tarif=t.id_tarif and tptt.id_periode=tp.id_periode and tptt.id_type_terrain=".$type_terrain
					." and ".$numero_jour_de_la_semaine.">=pt.jour_debut_periode and ".$numero_jour_de_la_semaine."<=jour_fin_periode ";
	
	//echo "req: ".$requete_recup_tarif;
	$db->setQuery($requete_recup_tarif.$tarif_special);

	if ($type_retour==0){
	    $resultat=$db->loadObjectList();
	    if (test_non_vide($resultat))
		    return($resultat);
	    else {
		    $db->setQuery($requete_recup_tarif.$tarif_par_defaut);
		    return($db->loadObjectList());
	    }
	}
	else return($numero_jour_de_la_semaine);
	
}

function tarif($date,$horaire_debut,$horaire_fin,$id_type_regroupement,$police,$type_terrain){
	$db = & JFactory::getDBO();
	
	$resultat_recup_tarif=recup_date_tarif($date,0,$type_terrain);
	
	$j=0;
	foreach($resultat_recup_tarif as $recup_tarif) {
		$tab_tarif[$j]["id_tarif"]=$recup_tarif->id_tarif;
		$tab_tarif[$j]["id_plage_tarif"]=$recup_tarif->id_plage_tarif;
		$tab_tarif[$j]["heure_debut"]=$recup_tarif->heure_debut;
		$tab_tarif[$j]["heure_fin"]=$recup_tarif->heure_fin;
		$tab_tarif[$j]["montant_horaire"]=$recup_tarif->montant_horaire;
		$tab_tarif[$j]["taux_TVA"]=$recup_tarif->taux_TVA;
		$j++;
	}
	$tarif = 0;
		
	if (test_nuit_avant_ouverture($horaire_debut)==1) $date_deb=decaler_jour($date,1);
	else $date_deb=$date;
	
	if (test_nuit_avant_ouverture($horaire_fin)==1) $date_fin=decaler_jour($date,1);
	else $date_fin=$date;

	/*if ($type_terrain==3)
	    return((50/60)*diff_dates_en_minutes($date_deb,$horaire_debut,$date_fin,$horaire_fin));
	else {*/
	    for ($i=0;$i<$j;$i++){
		
		if (test_nuit_avant_ouverture($tab_tarif[$i]["heure_debut"])==1) $date_Tab_deb=decaler_jour($date,1);
		else $date_Tab_deb=$date;
	
		if (test_nuit_avant_ouverture($tab_tarif[$i]["heure_fin"])==1) $date_Tab_fin=decaler_jour($date,1);
		else $date_Tab_fin=$date;
		
		$diff_bornes_debut_debut=diff_dates_en_minutes($date_deb,$horaire_debut,$date_Tab_deb,$tab_tarif[$i]["heure_debut"]);
		$diff_bornes_fin_fin=diff_dates_en_minutes($date_fin,$horaire_fin,$date_Tab_fin,$tab_tarif[$i]["heure_fin"]);
		$diff_bornes_debut_fin=diff_dates_en_minutes($date_deb,$horaire_debut,$date_Tab_fin,$tab_tarif[$i]["heure_fin"]);
		$diff_bornes_fin_debut=diff_dates_en_minutes($date_fin,$horaire_fin,$date_Tab_deb,$tab_tarif[$i]["heure_debut"]);
		
		/*echo "<br><br> bornes deb :".$diff_bornes_debut_debut." test : ".$date_deb."--".$horaire_debut."--".$date_Tab_deb."--".$tab_tarif[$i]["heure_debut"];
		echo "<br>borne fin : ".$diff_bornes_fin_fin." test : ".$date_fin."--".$horaire_fin."--".$date_Tab_fin."--".$tab_tarif[$i]["heure_fin"];
		echo "<br>debut_fin : ".$diff_bornes_debut_fin." fin_debut : ".$diff_bornes_fin_debut."<br>";*/
		
		if ($tab_tarif[$i]["id_plage_tarif"]==1 and (in_array($id_type_regroupement,array(1,2)) or $police==1))
		    $remise=2;
		else $remise=1;
		
		if (($diff_bornes_fin_fin>=0) and ($diff_bornes_debut_debut<=0)) 
			return((($tab_tarif[$i]["montant_horaire"]/60)*diff_dates_en_minutes($date_deb,$horaire_debut,$date_fin,$horaire_fin))/$remise);
		
		if (($diff_bornes_fin_fin>=0) and ($diff_bornes_debut_debut>0) and $diff_bornes_fin_debut<0)
			$tarif = $tarif + (($tab_tarif[$i]["montant_horaire"]/60)*diff_dates_en_minutes($date_Tab_deb,$tab_tarif[$i]["heure_debut"],$date_fin,$horaire_fin))/$remise;
		
		if (($diff_bornes_fin_fin<0) and ($diff_bornes_debut_debut<=0) and $diff_bornes_debut_fin>0)	
			$tarif = $tarif + (($tab_tarif[$i]["montant_horaire"]/60)*diff_dates_en_minutes($date_deb,$horaire_debut,$date_Tab_fin,$tab_tarif[$i]["heure_fin"]))/$remise;
		
		if (($diff_bornes_fin_fin<0) and ($diff_bornes_debut_debut>0))	
			$tarif = $tarif + (($tab_tarif[$i]["montant_horaire"]/60)*diff_dates_en_minutes($date_Tab_deb,$tab_tarif[$i]["heure_debut"],$date_Tab_fin,$tab_tarif[$i]["heure_fin"]))/$remise;	
		
		//echo "<br>Tarif : ".$tarif."<hr>";
	    }
	//}
	return($tarif);

}

// Interface PHP pour mail()
function sendMail($id_client,$objet,$corps,$email="") {
$user =& JFactory::getUser();
$db = & JFactory::getDBO();


   // l'&eacute;metteur
   $tete = 'MIME-Version: 1.0' . "\r\n";
   $tete .= "Content-type: text/html; charset=ISO-8859-1\r\n";
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
	    //$corps=str_replace("\r\n", "<br>", $corps);
	    //$corps=str_replace("\n", "<br>", $corps);
	    
	    if ($notif_email=="1" and ($email<>"agent@footinfive.com"))
		 return mail("<$email>","$objet",$corps,$tete);
	    else return 1;
    }
}

function connection_a_google_cal() {
require_once ('Zend/Gdata/ClientLogin.php');
require_once ('Zend/Gdata/Calendar.php');
$db = & JFactory::getDBO();

		$requete_recup_infos_google="SELECT  email, passwd FROM infos_google limit 0,1";
		$db->setQuery($requete_recup_infos_google);		
		$recup_infos_google = $db->loadObject();
					
		$email = $recup_infos_google->email;
		$passwd = $recup_infos_google->passwd;
		
		try {
			 $client = Zend_Gdata_ClientLogin::getHttpClient($email, $passwd, 'cl');
		}    
		catch (Zend_Gdata_App_CaptchaRequiredException $cre) {
			echo "l'URL de l\'image CAPTCHA est: ". $cre->getCaptchaUrl() ."\n";
			echo "Token ID: ". $cre->getCaptchaToken() ."\n";
		} 

		catch (Zend_Gdata_App_AuthException $ae) {
		   echo "Probl�me d'authentification : ". $ae->exception() ."\n";
		}
		 
		$cal = new Zend_Gdata_Calendar($client);

		try {
			$listFeed= $cal->getCalendarListFeed();
		} catch (Zend_Gdata_App_Exception $e) {
			echo "Error: " . $e->getMessage();
		}

		 return ($cal);

}

function suppr_event_cal_google ($id_resa){

$user =& JFactory::getUser();
$db = & JFactory::getDBO();
	
	$requete_recup_resa="select adresse_resa_google from Reservation where id_resa=".$id_resa;
	$db->setQuery($requete_recup_resa);	
	$adresse_resa_google_a_suppr=$db->loadResult();
	
	/*if ($adresse_resa_google_a_suppr<>""){	
		$cal = connection_a_google_cal();
		$event = $cal->getCalendarEventEntry($adresse_resa_google_a_suppr);
		$cal->delete($event->getEditLink()->href);
	}*/
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

function ajout_event_cal_google ($id_resa) {
$user =& JFactory::getUser();
$db = & JFactory::getDBO();
$cal = connection_a_google_cal();

	$requete_recup_resa="select r.*,t.*, c.nom as nom_client,c.* from Reservation as r, Terrain t, Client c ";
	$requete_recup_resa.=" where c.id_client=r.id_client and t.id=r.id_terrain and r.id_resa=".$id_resa;
	//echo "req1: ".$requete_recup_resa;
	$db->setQuery($requete_recup_resa);	
	$recup_resa = $db->loadObject();

	
	$infos_client=$recup_resa->nom_client." | ".$recup_resa->prenom." | ".$recup_resa->mobile1." | ".$recup_resa->fixe;
					

	$corps=texte_resa($recup_resa->nom_client, $recup_resa->date_debut_resa,$recup_resa->heure_debut_resa,$recup_resa->heure_fin_resa,$recup_resa->montant_total);
	
	$adresse_cal_google="http://www.google.com/calendar/feeds/".$recup_resa->id_cal_google."/private/full";
	$corps="RESA WEB\n\nhttp://footinfive.com/FIF/index.php/component/content/article?id=61&id_resa=".$id_resa."\n\n".$corps;
	

	$event= $cal->newEventEntry();
	
	$event->title = $cal->newTitle("RESA: ".$id_resa." / Client (".$recup_resa->id_client.") / ".$infos_client." ");
	$event->where = array($cal->newWhere("T".$recup_resa->id_terrain." : ".$recup_resa->nom_long));
	$event->content = $cal->newContent("$corps");

        if  (test_heure_ete($recup_resa->date_debut_resa))
		$tzOffset = "+02";
	else $tzOffset = "+01";
		 
	$when = $cal->newWhen();
	
	$when->startTime = $recup_resa->date_debut_resa."T".Ajout_zero_si_absent($recup_resa->heure_debut_resa).":00.000".$tzOffset.":00";
	$when->endTime = $recup_resa->date_fin_resa."T".Ajout_zero_si_absent($recup_resa->heure_fin_resa).":00.000".$tzOffset.":00";
	
	$event->when = array($when);
	
	$newEvent = $cal->insertEvent($event,"$adresse_cal_google");
	return($newEvent);

}

function consult_cal_google ($cal,$query, $date_Min, $horaire_Min, $date_Max, $horaire_Max) {

	$tzOffset = "+02";
	
	//test si en dehors des creneaux d'ouverture
	//echo "<br> test agenda avant : ".$horaire_Min." ttt ".$horaire_Max;
	if (test_horaires_ouverture($horaire_Min,"")==0) $horaire_Min=horaire_ouverture();
	
	if (test_horaires_ouverture("",$horaire_Max)==0) $horaire_Max=horaire_fermeture();
	
	//echo "<br> test agenda apres : ".$horaire_Min." ttt ".$horaire_Max;

	if (test_nuit_avant_ouverture($horaire_Min)==1) $date_Min=decaler_jour($date_Min,1);
	if (test_nuit_avant_ouverture($horaire_Max)==1) $date_Max=decaler_jour($date_Max,1);
	
	//echo "<br>startMin :".$date_Min."T".Ajout_zero_si_absent($horaire_Min).":00.000".$tzOffset.":00";
	//echo "<br>StartMax :".$date_Max."T".Ajout_zero_si_absent($horaire_Max).":00.000".$tzOffset.":00";
	
	$query->setStartMin($date_Min."T".Ajout_zero_si_absent($horaire_Min).":00.000".$tzOffset.":00");
	$query->setStartMax($date_Max."T".Ajout_zero_si_absent($horaire_Max).":00.000".$tzOffset.":00");

	try {
		$eventFeed = $cal->getCalendarEventFeed($query);
	} catch (Zend_Gdata_App_Exception $e) {
		echo "Error consult_cal_google : " . $e->getMessage();
	}

	return ($eventFeed);
}

function rechercher_resa_valid_sans_presence_google_cal(){
    $db = & JFactory::getDBO();
    
    $requete_resa_valid_sans_presence_google_cal="SELECT `id_resa` FROM `Reservation` WHERE `adresse_resa_google`=\"\" and `indic_annul`=0";

	$db->setQuery($requete_resa_valid_sans_presence_google_cal);	
	//echo "<br>".$requete_resa_valid_sans_presence_google_cal."<br>";
	$resultat_resa_valid_sans_presence_google_cal = $db->loadObjectList();
        $liste_resas="";
        foreach($resultat_resa_valid_sans_presence_google_cal as $resa_valid_sans_presence_google_cal)
            $liste_resas.=$resa_valid_sans_presence_google_cal->id_resa;
        return($liste_resas);
    
}

function test_dispo($type_terrain,$date_saisie_Min, $horaire_Min,$horaire_Max,$terrain="",$resa_modif="") {
// cette fonction renvoie 0 si aucune dispo sinon 1
	$db = & JFactory::getDBO();
	
	list($heure_min,$minutes_min) = explode(':', $horaire_Min);
	list($heure_max,$minutes_max) = explode(':', $horaire_Max);
	
	//echo "<br>Test_dispo avant horaire ouvert : ".$horaire_Min." - ".$horaire_Max;
	//test si en dehors des creneaux d'ouverture
	if (test_horaires_ouverture($horaire_Min,$horaire_Max)==0) return(0);
	
	if (test_nuit_avant_ouverture($horaire_Min)==1) {
		$date_Min=decaler_jour($date_saisie_Min,1);
		$date_Max=$date_Min;
	}
	else {
		$date_Min=$date_saisie_Min;
		if (test_nuit_avant_ouverture($horaire_Max)==1) $date_Max=decaler_jour($date_saisie_Min,1);
		else $date_Max=$date_saisie_Min;
	}
	if ($heure_min<9)
	    $stricte="=";
	else $stricte="=";
	//echo "<br>Test_dispo apres : ".$date_Min." ".$horaire_Min." ---- ".$date_Max." ".$horaire_Max." - ".$terrain."-".$resa_modif;
	
	$requete_test_dispo="select * from Terrain as t where t.is_active=1 and t.id_type=".$type_terrain." and t.id not in ";
	$requete_test_dispo.=" (SELECT r.id_terrain FROM `Reservation` as r where ";
	$requete_test_dispo.=" not(((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_debut_resa`,\" \",`heure_debut_resa`) AS CHAR(22)),";
	$requete_test_dispo.=" CAST(concat(\"".$date_Max."\",\" \",\"".$horaire_Max."\") AS CHAR(22)))) <".$stricte."0) ";
	$requete_test_dispo.=" or ((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_fin_resa`,\" \",`heure_fin_resa`) AS CHAR(22)),";
	$requete_test_dispo.=" CAST(concat(\"".$date_Min."\",\" \",\"".$horaire_Min."\") AS CHAR(22))))>".$stricte."0))  ";
	if (test_non_vide($resa_modif)) $requete_test_dispo.=" and r.id_resa<>".$resa_modif;
	$requete_test_dispo.=" and r.indic_annul<>1) ";
	if ($terrain<>"") $requete_test_dispo.=" and t.id=".$terrain;

	$db->setQuery($requete_test_dispo);	
	//echo "<br>".$requete_test_dispo."<br>";
	$resultat_test_dispo = $db->loadObjectList();
	
	foreach($resultat_test_dispo as $test_dispo) {
		if ($terrain<>""){
			$recup_infos_terrain[0]=$test_dispo->id_cal_google;
			$recup_infos_terrain[1]=$test_dispo->nom_long;
			$recup_infos_terrain[2]=$test_dispo->nom;
			$recup_infos_terrain[3]=$test_dispo->id;
		}
		else $recup_infos_terrain[]=$test_dispo->id;
		//echo "[".$test_dispo->nom."]";
	}
	if (is_array($recup_infos_terrain)) return($recup_infos_terrain);
	else return (0);
}

function trouve_dispo($type_terrain,$date_saisie_Min, $horaire_Min, $horaire_Max,$num_resa,$rubiks_cube=false) {

		
		//echo "<br> Hmin : ".$horaire_Min." Hmax : ".$horaire_Max;
		$tab_liste_terrains_dispo=test_dispo($type_terrain,$date_saisie_Min, $horaire_Min,$horaire_Max,'',$num_resa);
		if (is_array($tab_liste_terrains_dispo)){
			
			//echo "<br>dispo : oui";
			$tab_liste_terrains_dispo_sans_trous_demi_heure="";
			foreach($tab_liste_terrains_dispo as $liste_terrains_dispo) {
				//echo "terrain: ".$liste_terrains_dispo."<br>";
				$le_terrain_dispo=test_dispo($type_terrain,$date_saisie_Min, $horaire_Min,$horaire_Max,$liste_terrains_dispo,$num_resa);
				
				if (!$rubiks_cube){
				    /////////// on test les 30 mins avant et apres
				    $demi_heure_avant=test_dispo($type_terrain,$date_saisie_Min, decaler_heure($horaire_Min,-30), $horaire_Min,$liste_terrains_dispo,$num_resa);
				    //echo "<br>1/2h avant : ".$demi_heure_avant;
				    
				    $demi_heure_apres=test_dispo($type_terrain,$date_saisie_Min, $horaire_Max, decaler_heure($horaire_Max,30),$liste_terrains_dispo,$num_resa);
				    //echo "<br>1/2h apres : ".$demi_heure_apres;
				}
				
				//les demies heures sont pleines donc cas id�al
				if ((!is_array($demi_heure_avant)) and (!is_array($demi_heure_apres))) 
					$tab_liste_terrains_dispo_sans_trous_demi_heure[]=$le_terrain_dispo;// return($le_terrain_dispo);
			
				//tester 1h avant et apres
				if (is_array($demi_heure_avant) and is_array($demi_heure_apres)){ 

					$une_heure_avant_et_apres=test_dispo($type_terrain,$date_saisie_Min, decaler_heure($horaire_Min,-60), decaler_heure($horaire_Max,60),$liste_terrains_dispo,$num_resa);
					//echo "<br>1h avant et apres : ".$une_heure_avant_et_apres." test : ".decaler_heure($horaire_Min,-60)."-".decaler_heure($horaire_Max,60);
					
					//1h avant et apres sont dispos donc ok
					if (is_array($une_heure_avant_et_apres)) 
						$tab_liste_terrains_dispo_sans_trous_demi_heure[]=$le_terrain_dispo;// return($le_terrain_dispo);

				}
				if (is_array($demi_heure_avant) and (!is_array($demi_heure_apres))){ 
					// 1/2h apres pleine et 1/2h avant vide
					// tester 1h avant
									
					$une_heure_avant=test_dispo($type_terrain,$date_saisie_Min, decaler_heure($horaire_Min,-60), $horaire_Max,$liste_terrains_dispo,$num_resa);
					//echo "<br>1h avant : ".$une_heure_avant." test : ".decaler_heure($horaire_Min,-60)."-".$horaire_Max;	
					//1h avant dispo donc ok
					if (is_array($une_heure_avant)) 
						$tab_liste_terrains_dispo_sans_trous_demi_heure[]=$le_terrain_dispo;// return($le_terrain_dispo);
				}
				if ((!is_array($demi_heure_avant)) and (is_array($demi_heure_apres))){ 
					// 1/2h avant pleine et 1/2h apres vide
					// tester 1h apres
					
					$une_heure_apres=test_dispo($type_terrain,$date_saisie_Min, $horaire_Min, decaler_heure($horaire_Max,60),$liste_terrains_dispo,$num_resa);
					//echo "<br>1h apres : ".$une_heure_apres." test : ".$horaire_Min."-".decaler_heure($horaire_Max,60);
					
					//1h apres dispo donc ok
					if (is_array($une_heure_apres)) 
						$tab_liste_terrains_dispo_sans_trous_demi_heure[]=$le_terrain_dispo;// return($le_terrain_dispo);
				}
			}
		}
	
	if (is_array($tab_liste_terrains_dispo_sans_trous_demi_heure)) return($tab_liste_terrains_dispo_sans_trous_demi_heure);
	else return(0);
}

function test_limite_nbre_resa_par_caution($date_debut_resa,$id_client) {
	
	$db = & JFactory::getDBO();
	$limite_par_jour=2;
	$limite_par_semaine=3;
	$nbre_jours=4;
	
	$requete_test_limite_jour="SELECT IFNULL((select count(id_resa) from Reservation where "
				." `date_debut_resa`=\"".$date_debut_resa."\" "
				." and id_client=".$id_client." and indic_annul<>1 and accompte_necessaire<>1 and cautionnable=1),0) ";
	//echo $requete_test_limite_jour;
	$db->setQuery($requete_test_limite_jour);
	$nbre_resas_meme_jour=$db->loadResult();
	
	if ($nbre_resas_meme_jour>=$limite_par_jour)
		return (false);
	
	$requete_test_limite_semaine="SELECT IFNULL((select count(id_resa) from Reservation where "
				." ((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_debut_resa`,\" \",`heure_debut_resa`) AS CHAR(22)),"
				." CAST(concat(\"".$date_debut_resa."\",\" \",\"00:00\") AS CHAR(22)))) >= ".(-1*$nbre_jours*24*60).") "
				." and ((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_fin_resa`,\" \",`heure_fin_resa`) AS CHAR(22)),"
				." CAST(concat(\"".$date_debut_resa."\",\" \",\"00:00\") AS CHAR(22))))<= ".($nbre_jours*24*60).") "
				." and id_client=".$id_client." and indic_annul<>1 and accompte_necessaire<>1 and cautionnable=1),0) ";
	//echo $requete_test_limite_semaine;
	$db->setQuery($requete_test_limite_semaine);
	$nbre_resas_semaine=$db->loadResult();
	
	if ($nbre_resas_semaine>=$limite_par_semaine)
		return (false);
	else return(true);

}

function recup_resa_si_2_validations($date_debut_resa, $heure_debut_resa, $heure_fin_resa,$terrain_choisit) {
	
	$db = & JFactory::getDBO();
	
	$requete_recup_resa_si_2_validations="select count(id_resa) from Reservation where ";
	$requete_recup_resa_si_2_validations.=" date_debut_resa=\"".$date_debut_resa."\" ";
	$requete_recup_resa_si_2_validations.=" and heure_debut_resa=\"".$heure_debut_resa."\" ";
	$requete_recup_resa_si_2_validations.=" and heure_fin_resa=\"".$heure_fin_resa."\" ";
	$requete_recup_resa_si_2_validations.=" and id_terrain=\"".$terrain_choisit."\" and indic_annul<>1";
	//echo $requete_recup_resa_si_2_validations;
	$db->setQuery($requete_recup_resa_si_2_validations);
	return ($db->loadResult());

}

function recup_resa_sur_periode($date_debut_resa, $heure_debut_resa, $heure_fin_resa) {
	
	$db = & JFactory::getDBO();
	
	$requete_recup_resa_sur_periode="select * from Reservation where "
	    ." date_debut_resa=\"".$date_debut_resa."\" and heure_debut_resa>=\"".$heure_debut_resa."\" "
	    ." and heure_debut_resa<=\"".$heure_fin_resa."\" and indic_annul<>1 and bloquer_optimisation=0 ";
	//echo $requete_recup_resa_sur_periode;
	$db->setQuery($requete_recup_resa_sur_periode);
	return ($db->loadObjectList());

}

function recup_les_terrains($compl=" and is_active=1") {
	
	$db = & JFactory::getDBO();
	
	$requete_recup_les_terrains="SELECT * FROM `Terrain` WHERE 1 ".$compl;
	//echo $requete_recup_les_terrains;
	$db->setQuery($requete_recup_les_terrains);
	return ($db->loadObjectList());

}

function ajout_resa($date_debut_resa,$date_fin_resa, $heure_debut_resa, $heure_fin_resa,$id_client,$terrain_choisit,$montant_total,$duree_resa,$cautionnable) {
	
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$requete_ajout_resa="INSERT INTO `Reservation`(";
	if ($id_resa<>"") $requete_ajout_resa.="id_resa,";
	$requete_ajout_resa.="`date_debut_resa`, `date_fin_resa`,`heure_debut_resa`, `heure_fin_resa`, `id_user`,id_client, `id_terrain`, ";
	$requete_ajout_resa.=" adresse_resa_google, `date_valid`, `heure_valid`, `montant_total`,`tarif_horaire`, `montant_horaire_app`,";
	$requete_ajout_resa.="  cautionnable,accompte_necessaire) VALUES (";
	if ($id_resa<>"") $requete_ajout_resa.=$id_resa.",";
	$requete_ajout_resa.="\"".$date_debut_resa."\",\"".$date_fin_resa."\",";
	$requete_ajout_resa.=" \"".Ajout_zero_si_absent($heure_debut_resa)."\",\"".Ajout_zero_si_absent($heure_fin_resa)."\",";
	$requete_ajout_resa.=" ".$user->id.",".$id_client.", ";
	$requete_ajout_resa.=$terrain_choisit.",\"\",\"".date("Y")."-".date("m")."-".date("d")."\",";
	$requete_ajout_resa.=" \"".Ajout_zero_si_absent(date("H").":".date("i"))."\",";
	$requete_ajout_resa.=$montant_total.",".(($montant_total/duree_en_minutes($duree_resa))*60).",\"\",";
	$requete_ajout_resa.=" ".$cautionnable.",(SELECT `accompte_necessaire` FROM `Client` where `id_client`=".$id_client." ))";
	//echo "req4: ".$requete_ajout_resa;
	$db->setQuery($requete_ajout_resa);	
	$resultat_ajout_resa = $db->query();
	return($db->insertid());

}

function maj_match_ledg($id_match,$date_debut_resa, $heure_debut_resa,$terrain_choisit) {

            $mysql_ledg=connect_ledg();
	
            $query ="UPDATE `vlxhj_bl_match` SET `m_date`=\"".$date_debut_resa."\","
                ." `m_time`=\"".Ajout_zero_si_absent($heure_debut_resa)."\",`m_location`=\"T".$terrain_choisit."\" WHERE `id`=".$id_match;	
            //echo $query;
            $mysql_ledg->query($query);
            $mysql_ledg->close(); 

}
function maj_resa($id_resa,$date_debut_resa,$date_fin_resa, $heure_debut_resa, $heure_fin_resa,$id_client,$terrain_choisit,$montant_total,$duree_resa,$cautionnable) {
	
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
        
        if ($id_client<>3586 and $id_client<>1720)
            $compl_req="`montant_total`=".$montant_total.",`tarif_horaire`=".(($montant_total/duree_en_minutes($duree_resa))*60).",";
        
        
	$requete_maj_resa="UPDATE `Reservation` set "
            ."`date_debut_resa`=\"".$date_debut_resa."\", `date_fin_resa`=\"".$date_fin_resa."\","
            ." `heure_debut_resa`=\"".Ajout_zero_si_absent($heure_debut_resa)."\","
            ." `heure_fin_resa`=\"".Ajout_zero_si_absent($heure_fin_resa)."\", `id_user`=".$user->id.","
            ." id_client=".$id_client.", `id_terrain`=".$terrain_choisit.", "
            ." `date_valid`=\"".date("Y-m-d")."\", `heure_valid`=\"".Ajout_zero_si_absent(date("H:i"))."\""
            ." , ".$compl_req
            ." cautionnable=".$cautionnable.", notification=0 WHERE id_resa=".$id_resa;
	
	//echo "req4: ".$requete_maj_resa;
	$db->setQuery($requete_maj_resa);	
	$resultat_maj_resa = $db->query();
        
        if ($id_client==3586){
            $id_match=recup_id_match_de_resa($id_resa);
            maj_match_ledg($id_match,$date_debut_resa, $heure_debut_resa,$terrain_choisit);

        }

	return($id_resa);

}

function maj_resa_notification($id_resa,$notification) {
	
	$db = & JFactory::getDBO();
	
	$requete_maj_resa="UPDATE  Reservation set notification=".$notification."  where id_resa=".$id_resa;
	$db->setQuery($requete_maj_resa);	
	$db->Query();

}

function recup_infos_nbre_credit($id_moyen_paiement,$complement_date_requete,$origine_credit,$Type_credit="",$signe="") {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	if ($Type_credit=="") $complement_requete_type_credit="";
	else $complement_requete_type_credit=" and type_credit=".$Type_credit;
	
	if ($signe=="") $complement_requete_signe="";
	else $complement_requete_signe=" and credit".$signe."0 ";
	
	$requete_recup_infos_nbre_credit="select (IFNULL((SELECT count( `id_client`)  FROM `Credit_client` where ";
        $requete_recup_infos_nbre_credit.=" `id_moyen_paiement`=".$id_moyen_paiement." and `validation_credit`=1 ".$complement_requete_signe;
	$requete_recup_infos_nbre_credit.= $complement_requete_type_credit." and origine_credit=\"".$origine_credit."\" ".$complement_date_requete."),0) + ";
	$requete_recup_infos_nbre_credit.=" IFNULL((SELECT count( `id_client`)  FROM `Hist_Credit_client` where ";
        $requete_recup_infos_nbre_credit.=" `id_moyen_paiement`=".$id_moyen_paiement." and `validation_credit`=1 ".$complement_requete_signe;
	$requete_recup_infos_nbre_credit.= $complement_requete_type_credit." and origine_credit=\"".$origine_credit."\" ".$complement_date_requete."),0)) as le_nbre ";
        //echo $requete_recup_infos_nbre_credit;
        $db->setQuery($requete_recup_infos_nbre_credit);	
        $recup_infos_nbre_credit = $db->loadObject();
	return ($recup_infos_nbre_credit->le_nbre);

}

function recup_infos_nbre_reglements($id_moyen_paiement,$complement_date_requete,$signe="",$base="") {

    if (!test_non_vide($base))
    $db = & JFactory::getDBO();
else $mysql_ledg=connect_ledg();
	
	if ($signe=="") $complement_requete_signe="";
	else $complement_requete_signe=" and montant_reglement".$signe."0 ";
	
	$requete_recup_infos_nbre_reglements="SELECT count(`id_reglement`) as le_nbre FROM `Reglement` where `id_moyen_paiement`=".$id_moyen_paiement." ";
        $requete_recup_infos_nbre_reglements.= $complement_requete_signe."  and `validation_reglement`=1  ".$complement_date_requete;
        //echo $requete_recup_infos_nbre_reglements;
        if (!test_non_vide($base)){
            $db->setQuery($requete_recup_infos_nbre_reglements);	
            $recup_infos_nbre_reglements = $db->loadObject();
            return ($recup_infos_nbre_reglements->le_nbre);
        }
        else {
            $resultat=$mysql_ledg->query($requete_recup_infos_nbre_reglements);
            $row = $resultat->fetch_row();
            $resultat->close();
            $mysql_ledg->close(); 
            return ($row[0]);  
        }

}

function recup_infos_montant_credit($id_moyen_paiement,$complement_date_requete,$origine_credit,$Type_credit="",$signe="") {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	if ($Type_credit=="") $complement_requete_type_credit="";
	else $complement_requete_type_credit=" and type_credit=".$Type_credit;
	
	if ($signe=="") $complement_requete_signe="";
	else $complement_requete_signe=" and credit".$signe."0 ";
	
	$requete_recup_infos_montant_credit="select (IFNULL((SELECT sum(`credit`) FROM `Credit_client` where `id_moyen_paiement`=".$id_moyen_paiement."  ";
        $requete_recup_infos_montant_credit.=$complement_requete_signe." and validation_credit=1 and origine_credit=\"".$origine_credit."\" ".$complement_requete_type_credit;
	$requete_recup_infos_montant_credit.=" ".$complement_date_requete."),0) + IFNULL((SELECT sum(`credit`) FROM ";
        $requete_recup_infos_montant_credit.=" `Hist_Credit_client` where `id_moyen_paiement`=".$id_moyen_paiement." ".$complement_requete_signe;
	$requete_recup_infos_montant_credit.= $complement_requete_type_credit."  and validation_credit=1 and origine_credit=\"".$origine_credit."\" ";
	$requete_recup_infos_montant_credit.= $complement_date_requete."),0)) as total_reglement ";
	//echo $requete_recup_infos_montant_credit;
        $db->setQuery($requete_recup_infos_montant_credit);	
        $recup_infos_montant_credit = $db->loadObject();

	return ($recup_infos_montant_credit->total_reglement);

}

function recup_infos_montant_reglements($id_moyen_paiement,$complement_date_requete,$signe="",$base="") {

if (!test_non_vide($base))
    $db = & JFactory::getDBO();
else $mysql_ledg=connect_ledg();

    
	if ($signe=="") $complement_requete_signe="";
	else $complement_requete_signe=" and montant_reglement".$signe."0 ";
	
	$requete_recup_infos_montant_reglements="SELECT sum(`montant_reglement`) as total_reglement FROM `Reglement` where `id_moyen_paiement`=".$id_moyen_paiement." ";
        $requete_recup_infos_montant_reglements.= $complement_requete_signe." and `validation_reglement`=1  ".$complement_date_requete;
	//echo $requete_recup_infos_montant_reglements;
        if (!test_non_vide($base)){
            $db->setQuery($requete_recup_infos_montant_reglements);	
            $recup_infos_montant_reglements = $db->loadObject();
            return ($recup_infos_montant_reglements->total_reglement);
        }
        else {
            //echo $requete_recup_infos_montant_reglements;
            $resultat=$mysql_ledg->query($requete_recup_infos_montant_reglements);
            $row = $resultat->fetch_row();
            $resultat->close();
            $mysql_ledg->close(); 
            return ($row[0]);
        }
}

function recup_accompte_necessaire($id_client) {
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	$requete_recup_accompte_necessaire="select accompte_necessaire from Client where id_client=".$id_client;
	//echo $requete_recup_accompte_necessaire;
	$db->setQuery($requete_recup_accompte_necessaire);	
	return($db->loadResult());
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
        $texte="<tr><td><br>Pas d'escompte en cas de paiement anticip&eacute;"
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