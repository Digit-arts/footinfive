<?php

function menu_deroulant($Table,$old_select,$fonction="",$type="",$is_detail_credit_client=0){
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
	foreach($resultat_recup_liste as $recup_liste)
		if ($recup_liste->is_active==1)
			echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->nom."</option>";
	echo "</select>";
}

function recup_1_element($nom_element,$table,$nom_id,$id){
$db = & JFactory::getDBO();

	$requete_recup_1_element="select ".$nom_element." FROM ".$table." where ".$nom_id."=\"".$id."\" LIMIT 0,1";
	//echo "<br>".$requete_recup_1_element;
	$db->setQuery($requete_recup_1_element);		
	return ($db->LoadResult());

}

function est_agent($le_user){
	
	if ($le_user->id==42 or $le_user->id==5455)
		return(true);
	else return(false);
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

function menu_deroulant_extra_select($fid,$old_select){
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_recup_liste="Select * from #__bl_extra_select where fid=".$fid;
	//echo $requete_recup_liste;
	$db->setQuery($requete_recup_liste);
	$resultat_recup_liste = $db->loadObjectList();
	echo "<select name=\"select_fid_".$fid."\">";
	echo "<option value=\"\" ></option>";
	foreach($resultat_recup_liste as $recup_liste){

		if ($old_select==$recup_liste->id) $select=" selected ";
		else $select="";

		echo "<option value=\"".$recup_liste->id."\" \"".$select."\">".$recup_liste->sel_value."</option>";

	}
	echo "</select>";
}

function photo_user($id_user){

$db = & JFactory::getDBO();

	$query_def_img = "select def_img from #__bl_players where id=".$id_user;
	//echo $query_def_img;
	
	$db->setQuery($query_def_img );
	$def_img=$db->loadResult();

	
	$query ="select ph.ph_filename from #__bl_photos as ph where ph.id=(if(".$def_img.">0,".$def_img.",(select max(photo_id) "
		." from #__bl_assign_photos as ass_ph where ass_ph.cat_type = 1 AND cat_id =".id_player_du_user($id_user)."))) ";	
	$db->setQuery($query);
	return($db->loadResult());
}

function nbre_photo_user($id_user){

$db = & JFactory::getDBO();
	
	$query_nbre_photo = "select count(p.id) as nbre from #__bl_players as p where p.id=".$id_user." and p.id not in "
			."  (SELECT cat_id FROM #__bl_photos as ph, #__bl_assign_photos as ass_ph "
			." where ass_ph.cat_type=1 and ph.id=ass_ph.photo_id and p.id=ass_ph.cat_id)";
	//echo $query_nbre_photo;
	
	$db->setQuery($query_nbre_photo );
	return($db->loadResult());
}



function ajout_photo_user($fichier,$id_user){
	
	$db = & JFactory::getDBO();
	
	$query ="INSERT INTO `#__bl_photos`(`ph_name`, `ph_filename`, `ph_descr`) "
		." VALUES (\"\",\"".$fichier."\",\"\")";	
	$db->setQuery($query);
	$db->query();
	$id_photo=$db->insertid();
	
	$query ="INSERT INTO `#__bl_assign_photos`(`photo_id`, `cat_id`, `cat_type`) "
		." VALUES (".$id_photo.",".id_player_du_user($id_user).",1)";
	$db->setQuery($query);
	$db->query();
	
	$query ="UPDATE `#__bl_players` SET `def_img`=".$id_photo." WHERE usr_id=".$id_user;
		
	$db->setQuery($query);
	$db->query();
}

function maj_info_user($champs,$info,$id_user){
	
	$db = & JFactory::getDBO();
	
	$query ="UPDATE `#__bl_players` SET ".$champs."=\"".$info."\" WHERE usr_id=".$id_user;
		
	$db->setQuery($query);
	$db->query();
}

function id_player_du_user($id_user){
	
	$db = & JFactory::getDBO();
	
	$query ="SELECT id FROM #__bl_players where usr_id=".$id_user;
		
	$db->setQuery($query);
	
	return($db->loadResult());
}

function prenom_user($id_user){
	
	$db = & JFactory::getDBO();
	
	$query ="SELECT first_name FROM #__bl_players where usr_id=".$id_user;
		
	$db->setQuery($query);
	
	return($db->loadResult());
}

function flocage_user($id_user){
	
	$db = & JFactory::getDBO();
	
	$query ="SELECT nick FROM #__bl_players where usr_id=".$id_user;
		
	$db->setQuery($query);
	
	return($db->loadResult());
}

function champs_suppl_select_du_user($id_user,$fid,$sortie=""){
	
	$db = & JFactory::getDBO();
	
	if (test_non_vide($sortie))
		$champ=" id ";
	else $champ=" sel_value ";
	
	$query ="SELECT ".$champ." FROM `#__bl_extra_select` "
		." WHERE id=(SELECT fvalue FROM  #__bl_extra_values where f_id=".$fid." and uid=".id_player_du_user($id_user).")";
		
	$db->setQuery($query);
	$result=$db->loadResult();
	if ($result>0) return($result);
}

function ajout_info_suppl_user($id,$fid,$id_user){
	
	$db = & JFactory::getDBO();
	
	if (test_non_vide($fid) and test_non_vide($id) and test_non_vide($id_user)){
		$query ="DELETE FROM `#__bl_extra_values` where `f_id`=".$fid." and `uid`=".id_player_du_user($id_user);
		//echo $query;	
		$db->setQuery($query);
		$db->query();
		
	}
	
	$query ="INSERT INTO `#__bl_extra_values`(`f_id`, `uid`, `fvalue`, `fvalue_text`, `season_id`) "
		." VALUES (".$fid.",".id_player_du_user($id_user).",".$id.",\"\",\"\")";
	//echo $query;	
	$db->setQuery($query);
	$result=$db->loadResult();
	if ($result>0) return($result);
}


function liste_saisons_avec_virgules($id_user){
$db = & JFactory::getDBO();

	$liste_saisons=saison_en_cours_de_l_equipe($id_user);
	$sid="";
	foreach ($liste_saisons as $saison)
		$sid.=$saison->sid.",";
	return(substr($sid,0,-1));
}
function equipe_du_joueur($id_user,$type_retour=1,$sans_saisons=""){
	
	$db = & JFactory::getDBO();
	$user =& JFactory::getUser();
	
	if ($type_retour==1)
		$complement_requete="t.id";
	else  $complement_requete="t.t_name";
	
	if (!test_non_vide($sans_saisons)){
		$compl_table=" , #__bl_players_team as pt ";
		$compl_cond=" and pt.player_id=".$id_user." and t.id=pt.team_id and pt.season_id in (".liste_saisons_avec_virgules($id_user).")";
	}
	else {
		$compl_table=" , #__bl_players as p ";
		$compl_cond=" and t.id=p.team_id  and p.id=".$id_user." ";
	}

	$query ="SELECT ".$complement_requete." FROM #__bl_teams as t ".$compl_table."  "
		." where 1 ".$compl_cond;
	//echo "<br>".$query;
	
	$db->setQuery($query);
	
	return($db->loadResult());
}

function nom_equipe($id){
	
	$db = & JFactory::getDBO();

	$query ="SELECT t.t_name FROM #__bl_teams as t  where t.id=".$id;
	//echo "<br>".$query;
	
	$db->setQuery($query);
	
	return($db->loadResult());
}

function exist_nom_equipe($nom_equipe,$id_equipe){
	
	$db = & JFactory::getDBO();

	$query ="SELECT IFNULL((SELECT id FROM #__bl_teams as t  where t.id<>".$id_equipe." and t.t_name=\"".$nom_equipe."\" LIMIT 0,1),0) ";
	//echo "<br>".$query;
	
	$db->setQuery($query);
	
	if ($db->loadResult()>0)
		return(true);
	else return(false);
}



function liste_joueurs_d_une_equipe($id_equipe,$sans_saisons=""){
	
$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	if (!test_non_vide($sans_saisons)){
		$compl_table=" , #__bl_players_team as pt ";
		$compl_cond=" and p.id=pt.player_id and t.id=pt.team_id and pt.season_id in (".liste_saisons_avec_virgules($user->id).")";
	}
	else {
		$compl_table="";
		$compl_cond=" and t.id=p.team_id  ";
	}
		
	$query = "SELECT DISTINCT (p.id),u.email, u.lastvisitDate, p.* FROM  #__bl_players as p LEFT OUTER JOIN #__users u ON p.usr_id = u.id, #__bl_teams as t ".$compl_table
	." where t.id=".$id_equipe.$compl_cond
	." and p.nick<>\"CSC\" order by u.name asc";
	//echo $query;
	$db->setQuery($query);	
	return($db->loadObjectList());
}

function saison_en_cours_de_l_equipe($id_user){
	
	$db = & JFactory::getDBO();
	
	$query ="SELECT distinct(pt.season_id) as sid FROM #__bl_players_team AS pt, #__bl_matchday AS md, #__bl_match AS m, #__bl_seasons AS s  "
		." WHERE pt.player_id =".id_player_du_user($id_user). " and s.s_id=md.s_id and s.published=1 "
		." AND md.s_id = pt.season_id AND m.m_id = md.id AND m.m_date  >=  \"".date("Y-m-d")."\"";
		
	$db->setQuery($query);
	
	return($db->loadObjectList());

}


function gen_pass(){ 
   
   return (rand (0,9).rand (0,9).rand (0,9).rand (0,9)); 
}

function test_non_vide($var){ 
   
   if(isset($var) and ($var<>""))
      return true; 
   else 
     return false; 
}

function VerifierNom($nom){ 
   $Syntaxe="@^(\pL+[\' -]?)+\pL+$@D";
   if(preg_match($Syntaxe,$nom)) 
      return true; 
   else 
     return false; 
}

function VerifierNumMob($numero_de_telephone){ 
   $Syntaxe='`^0[6-7]([0-9]{2}){4}$`'; 
   if(preg_match($Syntaxe,$numero_de_telephone)) 
      return true; 
   else 
     return false; 
}

function VerifierAdresseMail($adresse){ 
   $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#'; 
   if(preg_match($Syntaxe,$adresse)) 
      return true; 
   else 
     return false; 
}

function email_du_joueur($id_user){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();

	$requete_email_joueur="Select u.email from #__users as u where u.id=".$id_user;
	$db->setQuery($requete_email_joueur);	
	return($db->LoadResult());
}

function est_capitaine($id_user){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();

	$requete_test_capitaine="Select m.tid from #__bl_moders as m where m.tid=".equipe_du_joueur($id_user,1,1)
		." and m.uid=".$id_user;
	//echo $requete_test_capitaine;
	$db->setQuery($requete_test_capitaine);	
	if ($db->LoadResult())
		return(1);
	else return(0);
}

function capitaine_equipe($id_team){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();

	$requete_capitaine_equipe="Select m.uid from #__bl_moders as m where m.tid=".$id_team;
	//echo $requete_capitaine_equipe;
	$db->setQuery($requete_capitaine_equipe);	
	return($db->LoadResult());
}

function id_table_min_max($table,$id,$min_ou_max){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();

	$requete_id_table_min_max="Select ".$min_ou_max."(".$id.") from ".$table." where 1 ";
	//echo $requete_id_table_min_max;
	$db->setQuery($requete_id_table_min_max);	
	return($db->LoadResult());
}

function ajout_capitaine($id_equipe,$id_joueur){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	if (est_capitaine($id_joueur)==0){
		$requete_suppr_capitaine="DELETE FROM `#__bl_moders` where tid=".$id_equipe;
		$db->setQuery($requete_suppr_capitaine);	
		$db->query();
		
		$requete_ajout_capitaine="INSERT INTO `#__bl_moders`(`uid`, `tid`) VALUES (".$id_joueur.",".$id_equipe.")";
		$db->setQuery($requete_ajout_capitaine);	
		if ($db->query())
			return(true);
		else return(false);
	}
	else "<br>Ce joueur est deja capitaine d'une autre equipe !<br>";
}

function verif_si_email_existe_deja($email){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();
	
	$requete_verif_si_email_existe="SELECT count(id) FROM #__users where email=Trim(\"".Trim($email)."\")  and email<>\"agent@footinfive.com\";";
	$db->setQuery($requete_verif_si_email_existe);		
	return ($db->loadResult());
}

function mail_inscription_au_site($prenom,$email,$pass,$nom_equipe,$type_joueur,$id_user) {

	$objet="Inscription Foot en salle - LEDG - FIF";
	$corps="Salut ".$prenom.",<br><br>Ceci est un mail automatique.<br>"
		."Tu es inscris sur le site http://www.footinfive.com/LEDG/ en tant que ".$type_joueur." de l'equipe ".$nom_equipe."<br>"
		//."<br>Merci de completer ta fiche joueur en utilisant ses infos:"
		//."\n\n<u>identifiant :</u> ".$email." (Site optimis&eacute; pour Google Chrome)"
		//."\n<u>mot de passe :</u> ".$pass
		."\n\nL'&eacute;quipe du Foot In Five te remercie de ta confiance !"
		."\n\nA bient&ocirc;t sur nos terrains..."
		."\n\nFOOT IN FIVE"
		."\nCentre de FOOT en salle 5vs5"
		."\n187 Route de Saint-Leu"
		."\n93800 Epinay-sur-Seine"
		."\nTel : 01 49 51 27 04"
		."\nMail : contact@footinfive.com";
			
//	sendMail("42",$objet,$corps);
	if (sendMail($id_user,$objet,$corps))
		echo "Le ".$type_joueur." ".$prenom." (".$email.") a &eacute;t&egrave; ajout&eacute; avec succ&eacute;s.<br><br>";
}

function sendMail($id_user,$objet,$corps) {
$user =& JFactory::getUser();
$db = & JFactory::getDBO();
		
   // l'&eacute;metteur
   $tete = 'MIME-Version: 1.0' . "\n"
	."Content-type: text/html; charset=\"UTF-8\"\r\n"
        ."From: CONTACT FIF <contact@footinfive.com>\n"
        ."Reply-To: contact@footinfive.com\n"
        ."Return-Path: contact@footinfive.com\n"; 
   // et zou... false si erreur d'&eacute;mission
   $corps=str_replace("\n", "<br>", $corps);
   $email_player=email_du_joueur($id_user);
   
   //return 1;
   return mail($email_player,$objet,$corps,$tete);
}

function decaler_jour($date,$nbreJours){
	
	$date_new = new DateTime($date);
	
	date_add($date_new, date_interval_create_from_date_string(''.$nbreJours.' days'));
	
	return  $date_new->format('Y-m-d');
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

function attribution_terrain_creneau($temp_tab_attribution_terrain_creneau_date,$Equ_A,$Equ_B,$journee,$temp_num_match,&$temp_tab_horaires_pref,$temp_passage,$heure_debut_seule,$heure_fin_seule){
    $nbre_terrains_fif=recup_nbre_terrains_fif();
    switch ($temp_passage){
        //D'abord faire rencontrer les equipes qui veulent jouer à la meme heure
        case '1' :  for ($heure=$heure_debut_seule;$heure<=$heure_fin_seule;$heure++){
                        for ($terrain=1;$terrain<=$nbre_terrains_fif;$terrain++)
                            if ($temp_tab_attribution_terrain_creneau_date[$journee][$heure][$terrain]==""
                                and $heure==$temp_tab_horaires_pref[$Equ_A]["hor_pref"]
                                and $heure==$temp_tab_horaires_pref[$Equ_B]["hor_pref"]
                                and ($temp_tab_horaires_pref[$Equ_A]["reste_hor_pref"]>0)
                                and ($temp_tab_horaires_pref[$Equ_B]["reste_hor_pref"]>0)){
            
                                    $temp_tab_horaires_pref[$Equ_A]["reste_hor_pref"]--;
            
                                    $temp_tab_horaires_pref[$Equ_B]["reste_hor_pref"]--;    
                                
                                return($terrain."_".$heure);
                            }
                    }
                    break;
        
        //Ensuite attribuer les terrains aux equipes qui ont mentionnées des preferences avec priorité aux 2 equipes n'ayant pas utilisés tout le nbre de creneaux qui leur sont attribués.
        case '2':for ($heure=$heure_debut_seule;$heure<=$heure_fin_seule;$heure++){
                    for ($terrain=1;$terrain<=$nbre_terrains_fif;$terrain++)
                        if ($temp_tab_attribution_terrain_creneau_date[$journee][$heure][$terrain]=="" and
                            (($heure==$temp_tab_horaires_pref[$Equ_A]["hor_pref"] and ($temp_tab_horaires_pref[$Equ_A]["reste_hor_pref"]>0) and ($temp_tab_horaires_pref[$Equ_B]["reste_hor_pref"]>0))
                             or ($heure==$temp_tab_horaires_pref[$Equ_B]["hor_pref"] and ($temp_tab_horaires_pref[$Equ_B]["reste_hor_pref"]>0) and ($temp_tab_horaires_pref[$Equ_A]["reste_hor_pref"]>0)))){
                            
                            if ($temp_tab_horaires_pref[$Equ_A]["hor_pref"]==$heure)
                                $temp_tab_horaires_pref[$Equ_A]["reste_hor_pref"]--;
                            
                            if ($temp_tab_horaires_pref[$Equ_B]["hor_pref"]==$heure)
                                $temp_tab_horaires_pref[$Equ_B]["reste_hor_pref"]--;
                                
                            return($terrain."_".$heure);
                        }
                }
                break;
            
        //Ensuite attribuer les terrains aux equipes qui ont mentionnées des preferences avec priorité à l'une des deux equipes n'ayant pas utilisés tout le nbre de creneaux qui leur sont attribués.
        case '3':for ($heure=$heure_debut_seule;$heure<=$heure_fin_seule;$heure++){
                    for ($terrain=1;$terrain<=$nbre_terrains_fif;$terrain++)
                        if ($temp_tab_attribution_terrain_creneau_date[$journee][$heure][$terrain]=="" and
                            (($heure==$temp_tab_horaires_pref[$Equ_A]["hor_pref"] and ($temp_tab_horaires_pref[$Equ_A]["reste_hor_pref"]>0) )
                             or ($heure==$temp_tab_horaires_pref[$Equ_B]["hor_pref"] and ($temp_tab_horaires_pref[$Equ_B]["reste_hor_pref"]>0) ))){
                            
                            if ($temp_tab_horaires_pref[$Equ_A]["hor_pref"]==$heure)
                                $temp_tab_horaires_pref[$Equ_A]["reste_hor_pref"]--;
                            
                            if ($temp_tab_horaires_pref[$Equ_B]["hor_pref"]==$heure)
                                $temp_tab_horaires_pref[$Equ_B]["reste_hor_pref"]--;
                                
                            return($terrain."_".$heure);
                        }
                }
                break;
        
        //Ensuite attribuer les terrains en fOnction de ce qu'il reste
        case '4' :for ($heure=$heure_debut_seule;$heure<=$heure_fin_seule;$heure++){
                    for ($terrain=1;$terrain<=$nbre_terrains_fif;$terrain++)
                        if ($temp_tab_attribution_terrain_creneau_date[$journee][$heure][$terrain]=="")
                            return($terrain."_".$heure);
                }
                break;
        default: break;
    }
}

function liste_equipes_du_groupe($id_du_groupe=""){
	$db = & JFactory::getDBO();
	
	if (test_non_vide($id_du_groupe)){
		$compl_tables=" `#__bl_grteams` gt, `#__bl_groups` as g , ";
		$compl_conditions=" and gt.`g_id`=g.id and t.id=gt.t_id and g.id=".$id_du_groupe;
	}
	
	$requete_liste_equipes_du_groupe="SELECT t.t_name as nom_equipe, t.id as id_equipe "
                        ." FROM ".$compl_tables." #__bl_teams as t  "
                        ." WHERE 1 ".$compl_conditions." order by t_name ";

                                                                
        //echo $requete_liste_equipes_du_groupe;
                            
        $db->setQuery($requete_liste_equipes_du_groupe);	
        return($db->loadObjectList());
}

function liste_groups($id_saison,$sans_grteams=""){
	$db = & JFactory::getDBO();
	
	if (!test_non_vide($sans_grteams)){
		$compl_req_champs=" ,count(gt.t_id) as nbre_equipes ";
		$compl_req_table=" ,`#__bl_grteams` as gt ";
		$compl_req_cond=" and gt.`g_id`=g.id ";
	}
	
	$requete_liste_groups="SELECT g.id as id_du_groupe, g.group_name  ".$compl_req_champs
                        ." FROM  `#__bl_groups` as g  ".$compl_req_table
                        ." WHERE  g.s_id=".$id_saison." ".$compl_req_cond." group by g.id order by g.id desc";
        //echo  $requete_liste_groupe;                       
                        
        $db->setQuery($requete_liste_groups);	
        return($db->loadObjectList());
}

function nbre_equipes_par_groups($id_saison,$id_groupe){
	$db = & JFactory::getDBO();
	
	
	foreach (liste_groups($id_saison) as $grp)
		if ($id_groupe==$grp->id_du_groupe)
			return($grp->nbre_equipes);

}

function equipes_sans_groups($id_saison){
	$db = & JFactory::getDBO();
	$requete_equipes_sans_groups="SELECT * FROM #__bl_teams where id not in "
                        ." (select gt.t_id from #__bl_groups as g, `#__bl_grteams` as gt where gt.g_id=g.id and g.s_id=".$id_saison.") ";
                                            
        $db->setQuery($requete_equipes_sans_groups);	
        return($db->loadObjectList());
}

function groups_sans_equipes($id_groupe){
	$db = & JFactory::getDBO();
	
	$requete_groups_sans_equipes="SELECT *,(select count(g_id) from `#__bl_grteams` as grt where grt.g_id=g.id ) as nbre_equipes_inscrites"
		." FROM `#__bl_groups` as g WHERE  g.id=".$id_groupe." order by group_name LIMIT 0,1";
        //echo  $requete_groups_sans_equipes;                       
                
        $db->setQuery($requete_groups_sans_equipes);	
        return($db->loadObject());
}

function remplir_tableaux_des_preferences(&$tab_horaires_pref,&$tab_nbre_demandes_par_horaires){
	$db = & JFactory::getDBO();
	
	$requete_recup_pref_horaires="SELECT t.id as teamid,LEFT((SELECT es.sel_value FROM #__bl_extra_select as es, #__bl_extra_values as ev, "
                                ." #__bl_players as p where f_id=6 and ev.uid=p.id "
                                ." and t.id=p.team_id and es.id=ev.fvalue and p.nick<>\"CSC\" and es.sel_value<>\"\" "
                                ." group by p.team_id, es.sel_value order by p.team_id,count(p.id) desc LIMIT 0,1),2)"
                                ." as horaire_pref FROM #__bl_teams as t";
        //echo $requete_recup_pref_horaires;
        $db->setQuery($requete_recup_pref_horaires);	
        $resultat_recup_pref_horaires = $db->loadObjectList();
                
        foreach ($resultat_recup_pref_horaires as $recup_pref_horaires) {
		$tab_horaires_pref[$recup_pref_horaires->teamid]["hor_pref"]=$recup_pref_horaires->horaire_pref;
                $tab_nbre_demandes_par_horaires[$recup_pref_horaires->horaire_pref]["horaire"]=$recup_pref_horaires->horaire_pref;
                $tab_nbre_demandes_par_horaires[$recup_pref_horaires->horaire_pref]["nbre_total_demandes"]+=1;
        }
}

function repartir_les_rencontres_par_journees($nbre_equipes,&$tab_journee){
	
	for ($i=2;$i<=$nbre_equipes;$i++)
		$tab_journee[$i][1]=$i-1;
                                
        for ($Equipe_A=1;$Equipe_A<=$nbre_equipes;$Equipe_A++){
                for ($Equipe_B=2;$Equipe_B<=$nbre_equipes;$Equipe_B++){
                                        
                        if ($Equipe_A<=$Equipe_B)
                                continue;
			
			if ($Equipe_A==$nbre_equipes){
				if ((2+$tab_journee[$Equipe_A][$Equipe_B-1])>($nbre_equipes-1))
					$tab_journee[$Equipe_A][$Equipe_B]=1+(2+$tab_journee[$Equipe_A][$Equipe_B-1]-$nbre_equipes);
				else $tab_journee[$Equipe_A][$Equipe_B]=2+$tab_journee[$Equipe_A][$Equipe_B-1];
			}
                        else {
                                if ($tab_journee[$Equipe_A][$Equipe_B-1]==($nbre_equipes-1))
                                        $tab_journee[$Equipe_A][$Equipe_B]=1;
                                else $tab_journee[$Equipe_A][$Equipe_B]=1+$tab_journee[$Equipe_A][$Equipe_B-1];
			}
                                
                                                
		}
	}
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

function connect_fif(){
	
	/* exemple
	$mysql_fif=connect_fif();

	$result = $mysql_fif->query("select * from Client where id_client=313");
	
	while ($row = $result->fetch_row()) {
		echo $row[0]."--".$row[1]."----".$row[2]."--".$row[3];
	}
	$result->close();
	
	$mysql_fif->close();*/

	$mysqli = new mysqli("localhost","Cyclople","MixMax123", "MySql_FIF");
	//$mysqli = new mysqli("localhost", "Flash", "plouf987", "FIF-test");
	
	/* Vérification de la connexion */
	if (mysqli_connect_errno()) {
	    printf("Echec de la connexion : %s\n", mysqli_connect_error());
	    exit();
	}
	return($mysqli);
}


function test_dispo($date_saisie_Min, $horaire_Min,$horaire_Max,$terrain="",$resa_modif="") {
// cette fonction renvoie 0 si aucune dispo sinon tableau des terrains
	$mysql_fif=connect_fif();
	
	list($heure_min,$minutes_min) = explode(':', $horaire_Min);
	list($heure_max,$minutes_max) = explode(':', $horaire_Max);
	
	//echo "<br>Test_dispo apres : ".$date_saisie_Min." ".$horaire_Min." ---- ".$date_Max." ".$horaire_Max." - ".$terrain."-".$resa_modif;
	
	$requete_test_dispo="select id_cal_google,nom_long,nom,id from Terrain as t where t.id_type=1 and t.id not in "
		." (SELECT r.id_terrain FROM `Reservation` as r where "
		." not(((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_debut_resa`,\" \",`heure_debut_resa`) AS CHAR(22)),"
		." CAST(concat(\"".$date_saisie_Min."\",\" \",\"".$horaire_Max."\") AS CHAR(22)))) <=0) "
		." or ((TIMESTAMPDIFF(MINUTE, CAST(concat(`date_fin_resa`,\" \",`heure_fin_resa`) AS CHAR(22)),"
		." CAST(concat(\"".$date_saisie_Min."\",\" \",\"".$horaire_Min."\") AS CHAR(22))))>=0))  ";
		
	if (test_non_vide($resa_modif)) $requete_test_dispo.=" and r.id_resa<>".$resa_modif;
		$requete_test_dispo.=" and r.indic_annul<>1) ";
	if ($terrain<>"") $requete_test_dispo.=" and t.id=".$terrain;

	$resultat_test_dispo = $mysql_fif->query($requete_test_dispo);

	//echo $requete_test_dispo;
	
	while ($row = $resultat_test_dispo->fetch_row()) {
		if ($terrain<>""){
			$recup_infos_terrain[0]=$row[0];
			$recup_infos_terrain[1]=$row[1];
			$recup_infos_terrain[2]=$row[2];
			$recup_infos_terrain[3]=$row[3];
		}
		else $recup_infos_terrain[]=$row[3];
		
	}
	$resultat_test_dispo->close();
	
	$mysql_fif->close();
	
	if (is_array($recup_infos_terrain)) return($recup_infos_terrain);
	else return (0);
}

function dispo_date_fif($la_date,$heure_debut,$heure_fin){
	$tab_liste_terrains_dispo=test_dispo($la_date,$heure_debut,$heure_fin);
	$liste_terrains_dispos="";
	if (is_array($tab_liste_terrains_dispo)){
		foreach($tab_liste_terrains_dispo as $terrains_dispo) {
			$liste_terrains_dispos.="T".$terrains_dispo." ";
		}
	}
	else $liste_terrains_dispos="pas de terrains dispo";
	return($liste_terrains_dispos); 
	
	
}

function menu_deroulant_des_terrains_avec_dispo_fif($le_terrain,$old_terrain,$la_date,$l_heure,$function=""){
	
	$les_terrains_dispos=dispo_date_fif($la_date,$l_heure,decaler_heure($l_heure,60));
	//$le_select_terrain=$la_date.",".$l_heure.",".decaler_heure($l_heure,60);
	if ($les_terrains_dispos<>"pas de terrains dispo"){
					    
		$les_terrains_disposen_tableau=explode(" ",substr($les_terrains_dispos,0,-1));//enleve l'espace à la fin
					    
		$le_select_terrain="<select name=\"".$le_terrain."\"  ".$function." >";
		foreach ($les_terrains_disposen_tableau as $terrain){
			if (test_non_vide($old_terrain) and $old_terrain==$terrain)
				$select_terrain=" selected ";
			else $select_terrain="";
			$le_select_terrain.="<option value=\"".$terrain."\" ".$select_terrain.">".$terrain."</option>";
		}
		$le_select_terrain.="</select>";
	}
	return($le_select_terrain);
				    
}


function menu_deroulant_des_heures_avec_dispo_fif($nom_du_select,$heure_selectionee,$date_a_verifier,$function=""){
	
	
	$le_select_heure="<select name=\"".$nom_du_select."\" ".$function." >";
				    
	list($heure_resa,$minutes_resa)=explode(":",$heure_selectionee);
				
	for ($time=9;$time<=23;$time++) {
		$select_heure="";
		$select_demie="";
		if (dispo_date_fif($date_a_verifier,$time.":00",($time+1).":00")<>"pas de terrains dispo"){
						    
			if (($heure_resa=="") and ($time==9)) $select_heure=" selected ";
			else 
				if ($heure_resa==$time) {
					if ($minutes_resa=="30") $select_demie=" selected ";
					else $select_heure=" selected ";
				}
				$le_select_heure.="<option value=\"".$time.":00\" \"".$select_heure."\">".$time."h00</option>";
		}
		
		if (dispo_date_fif($date_a_verifier,$time.":30",($time+1).":30")<>"pas de terrains dispo")
			$le_select_heure.="<option value=\"".$time.":30\"  \"".$select_demie."\" >".$time."h30</option>";					    
	}
	$le_select_heure.="</select> ";
	return($le_select_heure);
	
	
}




function premiere_lettre_maj($mot){ 
   $premiere_lettre_maj=mb_strtoupper(substr($mot,0,1));//on applique remplace_accents 
   $reste_min=mb_strtolower(substr($mot,1));
   $resultat=$premiere_lettre_maj.$reste_min;
   return($resultat);
}

function tout_majuscule($mot){ 

return (mb_strtoupper(Trim($mot)));

}

function recup_nom_feuille_match($id_match){
	
	if (is_file('Feuilles-de-matchs/'.$id_match.'.pdf'))
		return('Feuilles-de-matchs/'.$id_match.'.pdf');
	
	if (is_file('Feuilles-de-matchs/'.$id_match.'.jpg'))
		return('Feuilles-de-matchs/'.$id_match.'.jpg');

	if (is_file('Feuilles-de-matchs/'.$id_match.'.png'))
		return('Feuilles-de-matchs/'.$id_match.'.png');
	
	if (is_file('Feuilles-de-matchs/'.$id_match.'.jpeg'))
		return('Feuilles-de-matchs/'.$id_match.'.jpeg');
	
	if (is_file('Feuilles-de-matchs/'.$id_match.'.PDF'))
		return('Feuilles-de-matchs/'.$id_match.'.PDF');
	
	if (is_file('Feuilles-de-matchs/'.$id_match.'.JPG'))
		return('Feuilles-de-matchs/'.$id_match.'.JPG');

	if (is_file('Feuilles-de-matchs/'.$id_match.'.PNG'))
		return('Feuilles-de-matchs/'.$id_match.'.PNG');
	
	if (is_file('Feuilles-de-matchs/'.$id_match.'.JPEG'))
		return('Feuilles-de-matchs/'.$id_match.'.JPEG');
return("");
}



function recup_si_id_existant_sur_fif($email){

	$mysql_fif=connect_fif();

	$requete_recup_si_id_existant_sur_fif="SELECT `id` FROM `s857u_users` WHERE `email`=Trim(\"".trim($email)."\") and `email`<>\"agent@footinfive.com\" limit 0,1";
	//echo "req connection_google : ".$requete_recup_si_id_existant_sur_fif;
	$recup_si_id_existant_sur_fif=$mysql_fif->query($requete_recup_si_id_existant_sur_fif);		
		
	$row = $recup_si_id_existant_sur_fif->fetch_row();
	
	$recup_si_id_existant_sur_fif->close();
	return($row[0]);
	
}

function ajout_user($prenom_joueur,$email_joueur,$pass_du_joueur,$comp_req1="",$comp_req2="",$telmob=""){

	$recup_si_id_existant_sur_fif=recup_si_id_existant_sur_fif($email_joueur);
	

	if (test_non_vide($recup_si_id_existant_sur_fif))
		$comp_valeur_id=$recup_si_id_existant_sur_fif.", ";
	else {
		
		$mysql_fif=connect_fif();
		
		$requete_insert_user="INSERT INTO s857u_users(name, username, email, password, usertype, block, sendEmail, registerDate, params, `resetCount`) ";
		$requete_insert_user.="VALUES (\"".premiere_lettre_maj(sans_accents($prenom_joueur))."\",Trim(\"".Trim($email_joueur)."\"),";
		$requete_insert_user.="Trim(\"".Trim($email_joueur)."\"),MD5(\"".$pass_du_joueur."\"),\"Registered\",\"1\",\"0\",";
		$requete_insert_user.=" \"".date("Y-m-d")." ".date("H:i").":00\",\"\",0);";
		echo "<br>".$requete_insert_user;				
		$mysql_fif->query($requete_insert_user);		
		$user_id = $mysql_fif->insert_id;
		$comp_valeur_id=$user_id.", ";
		
														
		$requete_insert_user_group="INSERT INTO s857u_user_usergroup_map (`user_id`, `group_id`) VALUES (".$user_id.",2)";
		//echo "<br>".$requete_insert_user_group;				
		$mysql_fif->query($requete_insert_user_group);
		
		$requete_insert_client="INSERT INTO `Client`( `id_user`, `id_user_modif`,date_modif, heure_modif, `prenom`,`nom`, mobile1) ";
		$requete_insert_client.=" VALUES (".$user_id." , 256,\"".date("Y-m-d")."\",";
		$requete_insert_client.=" \"".Ajout_zero_si_absent(date("H:i"))."\",\"".premiere_lettre_maj(sans_accents($prenom_joueur))."\",\"".tout_majuscule(sans_accents($prenom_joueur))."\",\"".$telmob."\" )";
		//echo "<br>".$requete_insert_client;
		$mysql_fif->query($requete_insert_client);
		
		$mysql_fif->close();
	}
	


$db = & JFactory::getDBO();


	$requete_ajout_user="INSERT INTO #__users (id, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `registerDate`,"
		."`lastvisitDate`, `activation`, `params`, `lastResetTime`, `resetCount`) "
		." VALUES (".$comp_valeur_id.$comp_req2." \"".premiere_lettre_maj($prenom_joueur)."\",Trim(\"".Trim($email_joueur)."\"),"
		." Trim(\"".Trim($email_joueur)."\"),MD5(\"".$pass_du_joueur."\"),\"\",\"0\",\"0\",\"0000-00-00 00:00:00\","
		." \"0000-00-00 00:00:00\",\"\",\"admin_language= language= editor= helpsite= timezone=0\",\"0000-00-00 00:00:00\",0)";
	$db->setQuery($requete_ajout_user);	
	$db->query();
	return($db->insertid());
	
}

function test_saisie($id_user){

	$mysql_fif=connect_fif();

	$requete_recup_si_info_manquante_sur_fif="SELECT prenom, nom, mobile1 FROM `Client` WHERE `id_user`=".$id_user
		." and (`nom`=`prenom` or (mobile1 is null) or mobile1=\"0600000000\" or mobile1=\"\" or nom=\"\") limit 0,1";
	//echo "req connection_google : ".$requete_recup_si_info_manquante_sur_fif;
	$recup_si_info_manquante_sur_fif=$mysql_fif->query($requete_recup_si_info_manquante_sur_fif);		
		
	$row = $recup_si_info_manquante_sur_fif->fetch_row();
	
	$recup_si_info_manquante_sur_fif->close();
	$mysql_fif->close();
	
	if (test_non_vide($row))
		return($row);
	else return(false);

}

function recup_nbre_terrains_fif(){

	$mysql_fif=connect_fif();
	
	$requete_recup_nbre_terrains_fif="SELECT COUNT(`id`) AS nbre FROM  `Terrain` WHERE  `id_type`=1";
	//echo "<br>".$requete_recup_nbre_terrains_fif;
	$recup_nbre_terrains_fif=$mysql_fif->query($requete_recup_nbre_terrains_fif);
	$row = $recup_nbre_terrains_fif->fetch_row();
	$recup_nbre_terrains_fif->close();
	$mysql_fif->close();
	return($row[0]);
}

function forcer_saisie($id_user){

	$row=test_saisie($id_user);

	if (!$row or est_agent($id_user))
		return(true);
	else {
		$prenom_maj=tout_majuscule($row[0]);
		
		echo "<hr><FORM name=\"form_saisie\" class=\"submission box\" action=\"ep\" method=post >"
			." Afin d'acceder au site, completez votre fiche client, merci de saisir : <br>";
		
		if (is_null($row[2]) or !test_non_vide($row[2]) or $row[2]=="0600000000")
			echo "<br>Votre numero de mobile : <input name=\"mobile1_saisie\" type=\"text\"  value=\"".$_POST["mobile1_saisie"]."\" size=10 MAXLENGTH=10>";
		if ($prenom_maj==$row[1] or !test_non_vide($row[1]))
			echo "<br>Votre nom de famille : <input name=\"nom_saisie\" type=\"text\"  value=\"".$_POST["nom_saisie"]."\" size=15>";
			 
	       echo "<center><input name=\"valide\" type=\"submit\"  value=\"enregistrer\" ></center>";
	       echo "</form><hr />";
	}
		
}

function ajout_info_fif($user_id,$nom,$mobile1){

	$mysql_fif=connect_fif();
	
	if (test_non_vide($nom)){
		$requete_maj_nom_client="UPDATE `Client` SET `nom`=\"".tout_majuscule($nom)."\", `id_user_modif`=".$user_id.", "
			." date_modif=\"".date("Y-m-d")."\", heure_modif=\"".Ajout_zero_si_absent(date("H:i"))."\" where  `id_user`=".$user_id;
		//echo "<br>".$requete_maj_nom_client;
		$mysql_fif->query($requete_maj_nom_client);
	}
	if (test_non_vide($mobile1)){
		$requete_maj_mob_client="UPDATE `Client` SET mobile1=\"".$mobile1."\", `id_user_modif`=".$user_id.", "
			." date_modif=\"".date("Y-m-d")."\", heure_modif=\"".Ajout_zero_si_absent(date("H:i"))."\" where  `id_user`=".$user_id;
		//echo "<br>".$requete_maj_mob_client;
		$mysql_fif->query($requete_maj_mob_client);
	}

	$mysql_fif->close();
}

function recup_id_client_fif($user_id){

	$mysql_fif=connect_fif();
	
	$requete_recup_id_client_fif="SELECT id_client FROM `Client` where  `id_user`=".$user_id." limit 0,1";
	//echo "<br>".$requete_recup_id_client_fif;
	$recup_id_client_fif=$mysql_fif->query($requete_recup_id_client_fif);
	$row = $recup_id_client_fif->fetch_row();
	$recup_id_client_fif->close();
	$mysql_fif->close();
	return($row[0]);
}


function supprimer_user($id){

$db = & JFactory::getDBO();
	$requete_supprimer_user="DELETE FROM `#__users` WHERE `id`=".$id;
	$db->setQuery($requete_supprimer_user);	
	$db->query();
}

function ajout_joueur($prenom_joueur,$id_du_new_user,$id_equipe){

$db = & JFactory::getDBO();
$user =& JFactory::getUser();

	$requete_ajout_joueur="INSERT INTO `#__bl_players`(`id`, `first_name`, `last_name`, `nick`, `about`, `position_id`, `def_img`, `team_id`,"
		." `usr_id`, `country_id`, `registered`, `created_by`) "
		." VALUES (".$id_du_new_user.",\"".premiere_lettre_maj($prenom_joueur)."\",\"\",\"\",\"\",0,0,".$id_equipe.","
		." ".$id_du_new_user.",0,1,$user->id)";
						
	$db->setQuery($requete_ajout_joueur);	
	$db->query();
	return($db->insertid());
}

function sans_accents($string){
    $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ 
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ'; 
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuy 
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr'; 
    $string = utf8_decode($string);     
    $string = strtr($string, utf8_decode($a), $b); 
    $string = strtolower($string); 
    return utf8_encode($string); 
}

function maj_joueur($prenom_joueur,$nick_joueur,$id_user){

$db = & JFactory::getDBO();
$user =& JFactory::getUser();

	if (test_non_vide($prenom_joueur)){
		$requete_maj_joueur="UPDATE `#__bl_players` SET `first_name`=\"".premiere_lettre_maj($prenom_joueur)."\" where  id=".$id_user;
						
		$db->setQuery($requete_maj_joueur);	
		$db->query();
		
		$requete_maj_name_user="UPDATE `#__users` SET `name`=\"".premiere_lettre_maj($prenom_joueur)."\" where id=".$id_user;
		$db->setQuery($requete_maj_name_user);	
		$db->query();
		
		$mysql_fif=connect_fif();
	
		$requete_maj_user_fif="UPDATE  s857u_users SET `name`=\"".premiere_lettre_maj(sans_accents($prenom_joueur))."\" where id=".$id_user;
		//echo "<br>".$requete_maj_user_fif;				
		$mysql_fif->query($requete_maj_user_fif);
			
		$requete_maj_client_fif="UPDATE `Client` SET `prenom`=\"".premiere_lettre_maj(sans_accents($prenom_joueur))."\" where  id_user=".$id_user;
		//echo "<br>".$requete_maj_client_fif;
		$mysql_fif->query($requete_maj_client_fif);
		
		$mysql_fif->close();
	}
	
	if (test_non_vide($nick_joueur)){
		$requete_maj_joueur="UPDATE `#__bl_players` SET  nick=\"".$nick_joueur."\" where  id=".$id_user;
						
		$db->setQuery($requete_maj_joueur);	
		$db->query();
	}
	

	
	
}

function maj_email_user($email_joueur,$id_user,$pass_du_joueur){

$db = & JFactory::getDBO();
$user =& JFactory::getUser();
	
	$requete_maj_email_user="UPDATE `#__users` SET `email`=\"".$email_joueur."\", username=\"".$email_joueur."\", password=MD5(\"".$pass_du_joueur."\") where id=".$id_user;
	$db->setQuery($requete_maj_email_user);	
	$db->query();
	
	$recup_id_fif=recup_si_id_existant_sur_fif($email_joueur);
	
	if (test_non_vide($recup_id_fif)){
		$requete_maj_id_user="UPDATE `#__users` SET `id`=".$recup_id_fif." WHERE `id`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__user_usergroup_map` SET `user_id`=".$recup_id_fif."  WHERE `user_id`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `Reglement` SET `id_joueur`=".$recup_id_fif." WHERE `id_joueur`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__bl_assign_photos` SET `cat_id`=".$recup_id_fif." WHERE `cat_type`=1 and `cat_id`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__bl_comments` SET `user_id`=".$recup_id_fif." WHERE `user_id`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__bl_extra_values` SET `uid`=".$recup_id_fif."  WHERE `uid`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__bl_match_events` SET `player_id`=".$recup_id_fif." WHERE `player_id`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__bl_moders` SET `uid`=".$recup_id_fif."  WHERE `uid`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__bl_players` SET `id`=".$recup_id_fif.", `usr_id`=".$recup_id_fif."  WHERE `usr_id`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
		$requete_maj_id_user="UPDATE `#__bl_players_team` SET  `player_id`=".$recup_id_fif." WHERE `player_id`=".$id_user;
		$db->setQuery($requete_maj_id_user);	
		$db->query();
		
	}
	else {	
		$mysql_fif=connect_fif();
	
		$requete_maj_user_fif="UPDATE  s857u_users SET `email`=\"".$email_joueur."\", username=\"".$email_joueur."\" where id=".$id_user;
		//echo "<br>".$requete_maj_user_fif;				
		$mysql_fif->query($requete_maj_user_fif);		
		
		$mysql_fif->close();
	}
	
	
}

function supprimer_player($id){

$db = & JFactory::getDBO();
$user =& JFactory::getUser();

	$requete_supprimer_player="DELETE FROM `#__bl_players` WHERE `id`=".$id;					
	$db->setQuery($requete_supprimer_player);	
	$db->query();
}

function maj_user_du_joueur($id_du_new_user,$id_joueur){

$db = & JFactory::getDBO();
	$requete_maj_joueur="UPDATE `#__bl_players` SET `usr_id`=".$id_du_new_user." where id=".$id_joueur ;
	$db->setQuery($requete_maj_joueur);	
	$db->query();
}

function ajout_user_au_groupe($id_du_new_user){

$db = & JFactory::getDBO();
	$requete_ajout_user_au_groupe="INSERT INTO `#__user_usergroup_map`(`user_id`, `group_id`) VALUES (".$id_du_new_user.",2)";
	$db->setQuery($requete_ajout_user_au_groupe);	
	$db->query();
}

function supprimer_user_du_groupe($id){

$db = & JFactory::getDBO();
	$requete_supprimer_user_du_groupe="DELETE FROM `#__user_usergroup_map` WHERE `user_id`=".$id;
	$db->setQuery($requete_supprimer_user_du_groupe);	
	$db->query();
}

function saisons_en_cours_avec_buts($id_user){

$db = & JFactory::getDBO();
	$requete_saisons_en_cours_avec_buts="SELECT me.`e_id` , md.s_id as sid FROM `#__bl_match_events` as me,#__bl_match as m, #__bl_matchday as md "
		."WHERE  `player_id`=".$id_user." and me.`match_id`=m.id and m.m_id= md.id and  me.`e_id`=1 and md.s_id in (".liste_saisons_avec_virgules($id_user).")";
	//echo "<br>req: ".$requete_saisons_en_cours_avec_buts;
	$db->setQuery($requete_saisons_en_cours_avec_buts);	
	$db->query();
	$liste_saisons=$db->LoadObjectList();
	$sid="";
	foreach ($liste_saisons as $saison)
		$sid.=$saison->sid.",";
	return(substr($sid,0,-1));
}

function supprimer_photo_joueur($id_user){

$db = & JFactory::getDBO();
	
	/*chmod("/var/www/vhosts/footinfive.com/httpdocs/LEDG/media/bearleague/".photo_user($id_user)."\"", 777);
	if (unlink("/var/www/vhosts/footinfive.com/httpdocs/LEDG/media/bearleague/".photo_user($id_user)."\""))
		echo "supprimee";
	else echo "non";*/
	
	$query ="DELETE FROM #__bl_photos as ph where ph.id in ( Select photo_id FROM `#__bl_assign_photos` WHERE `cat_id`="
		.$id_user." and cat_type=1)";	
	$db->setQuery($query);
	$db->query();

	
	$query ="DELETE FROM #__bl_assign_photos where cat_id=".$id_user;	
	$db->setQuery($query);
	$db->query();

	$query ="UPDATE #__bl_players SET def_img=0 where id=".$id_user;	
	$db->setQuery($query);
	$db->query();
}

function supprimer_infos_joueur($id_user){

$db = & JFactory::getDBO();
	
	$query ="DELETE FROM #__bl_extra_values where uid=".$id_user;	
	$db->setQuery($query);
	$db->query();

}

function supprimer_capitaine_joueur($id_user){

$db = & JFactory::getDBO();
	
	$query ="DELETE FROM #__bl_moders where uid=".$id_user;	
	$db->setQuery($query);
	$db->query();

}

function supprimer_reglements_joueur($id_user){

$db = & JFactory::getDBO();
	
	$query ="DELETE FROM Reglement where id_joueur=".$id_user;	
	$db->setQuery($query);
	$db->query();

}

function nbre_reglements_d_un_joueur($id_user){
	$db = & JFactory::getDBO();
	
	$requete_nbre_reglements_d_un_joueur="SELECT count(id_reglement) as nbre_reg FROM `Reglement` as reg "
		." WHERE reg.`id_joueur`=".$id_user." and validation_reglement=1 ";
        $db->setQuery($requete_nbre_reglements_d_un_joueur);	
        $db->query();
	return($db->loadResult());
	
}

function supprimer_joueur($id_user){

$db = & JFactory::getDBO();
		$ttes_saison_ou_le_joueur_est_inscris=liste_saisons_avec_virgules($id_user);
		$id_saisons_avec_buts=saisons_en_cours_avec_buts($id_user);
		if (test_non_vide($id_saisons_avec_buts))
			echo "Ce joueur ne peut pas etre supprim&eacute; car il a mit des buts durant la saison.";
		else {
			if (nbre_reglements_d_un_joueur($id_user)==0){
				supprimer_joueurs_dans_saisons($id_user," and `season_id` in (".$ttes_saison_ou_le_joueur_est_inscris.") ");
			/*	supprimer_player($id_user);
				supprimer_user($id_user);
				supprimer_user_du_groupe($id_user);
				supprimer_photo_joueur($id_user);
				supprimer_infos_joueur($id_user);
				supprimer_reglements_joueur($id_user);
				supprimer_capitaine_joueur($id_user);*/
			}
			else echo "Ce joueur ne peut pas etre supprim&eacute; car il a des reglements.";
		}

}

function test_existe_equipe($nom){
	$db = & JFactory::getDBO();
	
	$requete_test_existe_equipe="SELECT id FROM `#__bl_teams` WHERE t_name=\"".trim($nom)."\" ";
        $db->setQuery($requete_test_existe_equipe);	
        $db->query();
	return($db->loadResult());
	
}

function ajout_equipe($nom){
	$db = & JFactory::getDBO();
	
	if (!test_non_vide(test_existe_equipe($nom))){
		$requete_ajout_equipe="INSERT INTO `#__bl_teams`(`t_name`,created_by)  VALUES (\"".trim($nom)."\",42)";
		//echo $requete_ajout_equipe;
		$db->setQuery($requete_ajout_equipe);	
		$db->query();
		return($db->insertid());	
	}
	else echo "Cette equipe existe d&eacute;j&agrave;<br>";
	return(0);

	
}

function ajout_tournoi($nom,$type){
	$db = & JFactory::getDBO();
	
	$requete_ajout_tournoi="INSERT INTO `#__bl_tournament`(`name`, `descr`, `published`, `t_type`, `t_single`, `logo`) "
                                            ." VALUES (\"".$nom."\",\"\",1,".$type.",0,\"\")";
        //echo $requete_ajout_tournoi;
        $db->setQuery($requete_ajout_tournoi);	
        $db->query();

        return($db->insertid());
	
}

function archiver_saison($id_saison){
	$db = & JFactory::getDBO();
	
	$requete_archiver_saison="UPDATE `#__bl_seasons` SET `published`=0 where s_id=".$id_saison;
        //echo $requete_archiver_saison;
	$db->setQuery($requete_archiver_saison);	
        $db->query();

}

function extract_saison($id_saison){
	$db = & JFactory::getDBO();
	
	$requete_extract_saison="UPDATE `#__bl_seasons` SET `published`=1 where s_id=".$id_saison;
        //echo $requete_archiver_saison;
	$db->setQuery($requete_extract_saison);	
        $db->query();

}

function ajout_saison($nom,$tourn,$nbre_equipes){
	$db = & JFactory::getDBO();
	
	$requete_ajout_saison="INSERT INTO `#__bl_seasons` (`s_name`, `s_descr`, `s_rounds`, `t_id`, `published`, `s_win_point`, `s_lost_point`,"
                        ." `s_enbl_extra`, `s_extra_win`, `s_extra_lost`, `s_draw_point`, `s_groups`, `s_win_away`, `s_draw_away`, `s_lost_away`,"
                        ." `s_participant`, `s_reg`, `reg_start`, `reg_end`, `s_rules`, `ordering`, `idtemplate`) "
                        ." VALUES(\"".$nom."\",\"\", 1, ".$tourn.", \"1\", \"3.00\", \"0.00\","
                        ." 0, \"0.00\", \"0.00\", \"1.00\", \"1\", \"3.00\", \"1.00\", \"0.00\","
                        ." ".$nbre_equipes.", \"0\", \"\", \"\", \"\", 0, 0)";
        $db->setQuery($requete_ajout_saison);	
        $db->query();
        $saison=$db->insertid();

                        
        $requete_ajout_options_saison="INSERT INTO `#__bl_season_option` (`s_id`, `opt_name`, `opt_value`) VALUES"
                        ."(".$saison.", 'draw_chk', '1'),(".$saison.", 'equalpts_chk', '1'),(".$saison.", 'gd_chk', '1'),(".$saison.", 'goalconc_chk', '1'),"
                            ."(".$saison.", 'goalscore_chk', '1'),(".$saison.", 'lost_chk', '1'),(".$saison.", 'played_chk', '1'),(".$saison.", 'point_chk', '1'),(".$saison.", 'win_chk', '1')";

        $db->setQuery($requete_ajout_options_saison);	
        $db->query();
                        
        $requete_ajout_options_rank_saison="INSERT INTO `#__bl_ranksort` (`seasonid`, `sort_field`, `sort_way`, `ordering`) VALUES"
            ."(".$saison.", \"1\", \"0\", 0),(".$saison.", \"4\", \"0\", 1),(".$saison.", \"5\", \"0\", 2),(".$saison.", \"6\", \"0\", 3)";
        $db->setQuery($requete_ajout_options_rank_saison);	
        $db->query();
	
	return($saison);
	
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

function duree_en_minutes($duree){
	
	list($heure,$minutes) = explode(':', $duree);
	return($heure*60+$minutes);

}

function decaler_heure($heure,$ajout)
{
$timestamp = strtotime("$heure");
$heure_ajout = strtotime("+$ajout minutes", $timestamp);
return (Ajout_zero_si_absent(date('H:i', $heure_ajout)));
}


function texte_resa($nom_client, $date_debut_resa,$heure_debut_resa,$heure_fin_resa,$montant_total,$commentaire="",$pourmail=""){

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
        ."\nMontant location : ".$montant_total." euros \n(ce montant ne tient pas compte "
	." des ".$accent_e."ventuelles r".$accent_e."ductions ou versements effectu".$accent_e."s) ";
if (test_non_vide($commentaire)) 
	$corps.="\n\nCommentaire : \n".$commentaire;
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
$corps.="\n<hr>Reservation enregistr".$accent_e."e par Guillaume  ";
$corps.="\nDate validation resa : ".date_longue(date("Y-m-d"))." ".$accent_a." ".date("H:i")."";

return ($corps);
}

function maj_cal_dans_resa($id_resa,$lien){

	$mysql_fif=connect_fif();

	$requete_maj_cal_dans_resa="UPDATE `Reservation` set indic_annul=0, adresse_resa_google=\"".$lien."\","
		." date_valid=\"".date("Y-m-d")."\", heure_valid=\"".date("H:i")."\", id_user=256 where id_resa=".$id_resa;
	//echo "req4: ".$requete_maj_cal_dans_resa;
	
	$mysql_fif->query($requete_maj_cal_dans_resa);

	$mysql_fif->close();
					
}

function ajout_resa($date_debut_resa,$date_fin_resa, $heure_debut_resa, $heure_fin_resa,$id_client,$terrain_choisit,$montant_total,$duree_resa,$commentaire,$cautionnable,$id_match) {

	$mysql_fif=connect_fif();
	
	$requete_ajout_resa="INSERT INTO `Reservation`("
		."`date_debut_resa`, `date_fin_resa`,`heure_debut_resa`, `heure_fin_resa`, `id_user`,id_client, `id_terrain`, "
		." adresse_resa_google, `date_valid`, `heure_valid`, `montant_total`,`tarif_horaire`, `montant_horaire_app`,"
		." cautionnable,accompte_necessaire, id_match,bloquer_optimisation) VALUES ("
		."\"".$date_debut_resa."\",\"".$date_fin_resa."\","
		." \"".Ajout_zero_si_absent($heure_debut_resa)."\",\"".Ajout_zero_si_absent($heure_fin_resa)."\","
		." 256,".$id_client.", "
		.$terrain_choisit.",\"\",\"".date("Y-m-d")."\", \"".Ajout_zero_si_absent(date("H:i"))."\","
		.$montant_total.",".(($montant_total/duree_en_minutes($duree_resa))*60).",\"\","
		.$cautionnable.",(SELECT `accompte_necessaire` FROM `Client` where `id_client`=".$id_client." ),".$id_match.",1)";
	//echo "<br>req4: ".$requete_ajout_resa;
		
	$mysql_fif->query($requete_ajout_resa);
	$id_resa=$mysql_fif->insert_id;
	$mysql_fif->close();
	return($id_resa);

}

function ajout_match_bdd($mday,$team_A,$team_B,$date,$heure,$lieu,$k_ordering=0,$k_title="",$k_stage=1){
	
	$db = & JFactory::getDBO();
	$requete_ajout_matchs="INSERT INTO `#__bl_match`(`m_id`, `team1_id`, `team2_id`, `score1`, `score2`, "
                ." `match_descr`, `published`, `is_extra`, `m_played`, `m_date`, `m_time`, `m_location`, "
                ." `k_ordering`, `k_title`, `k_stage`, `points1`, `points2`, `new_points`, `bonus1`, `bonus2`, "
                ." `venue_id`, `aet1`, `aet2`, `p_winner`, `m_single`, `betavailable`, `betfinishdate`, `betfinishtime`)"
                ." VALUES (".$mday.",".$team_A.",".$team_B.",0,0,"
                ." \"\",1,0,0,\"".$date."\",\"".$heure."\",\"".$lieu."\","
                ." ".$k_ordering.",\"".$k_title."\",".$k_stage.",\"0.00\",\"0.00\",0,\"0.00\",\"0.00\","
                ." 0,0,0,0,0,0,\"0000-00-00\",\"\")";
        //echo "<br><br>".$requete_ajout_matchs;
        $db->setQuery($requete_ajout_matchs);	
        $db->query();
	return($db->insertid());
	
}

function ajout_match($mday,$team_A,$team_B,$date,$heure,$lieu,$k_ordering=0,$k_title="",$k_stage=1){
	$id_match=ajout_match_bdd($mday,$team_A,$team_B,$date,$heure,$lieu,$k_ordering,$k_title,$k_stage);
	
	$id_resa_new=ajout_resa($date,$date, $heure,decaler_heure($heure,60),3586,substr($lieu,1,1),0,"01:00","LEDG : ".nom_equipe($team_A)." - ".nom_equipe($team_B),0,$id_match);	
	return($id_match);
	
	
}

function ajout_match_day($mday,$nom_day,$id_saison,$k_format=0){
	$db = & JFactory::getDBO();
	
	if ($mday>0) {
		$comp_req1=" `id`, ";
		$comp_req2=" ".$mday.", ";
	}
	
	$requete_ajout_match_day="INSERT INTO `#__bl_matchday`( ".$comp_req1." `m_name`, `m_descr`, `s_id`, `is_playoff`, "
                                    ." `k_format`, `ordering`) VALUES ( ".$comp_req2." \"".$nom_day."\",\"\",".$id_saison.",0,".$k_format.",0)";
        //echo "<br>".$requete_ajout_match_day;
	$db->setQuery($requete_ajout_match_day);	
        $db->query();
	
	return($db->insertid());
	
}

function recup_equipe_adverse($id_match,$id_team){
	$db = & JFactory::getDBO();
	
	$requete_recup_equipe_adverse="SELECT IF(team1_id=".$id_team.",team2_id,team1_id) as res FROM `#__bl_match` where id=".$id_match;
        //echo "<br>adv : ".$requete_recup_equipe_adverse;
	$db->setQuery($requete_recup_equipe_adverse);	
        $db->query();
	
	return($db->loadResult());
	
}

function maj_match_knock($id_match,$m_id,$id_team,$k_ordering,$k_stage){
	$db = & JFactory::getDBO();
	
	$condition=" m_id=".$m_id." and k_ordering=".floor($k_ordering/2)." and k_stage=".($k_stage+1)." ";
	
	$query_test_equipe_gagnante = "SELECT * from #__bl_match where "
		."((score1>score2 and team1_id=".$id_team." and score2<>0) or (score1<score2 and team2_id=".$id_team." and score1<>0) ) and id=".$id_match;        
        //echo  $query_test_equipe_gagnante." - ".$db->getNumRows();
        $db->setQuery($query_test_equipe_gagnante);
        $db->query();
	
	if ($db->getNumRows()>0){
		$query_test_team_dispo = "SELECT IF(team1_id>0,IF(team2_id>0,\"0\",\"team2_id\"),\"team1_id\") FROM #__bl_match where ".$condition;        
			//echo  $query_test_team_dispo;
			$db->setQuery($query_test_team_dispo);
			$db->query();
		$le_retour=$db->loadResult();
		//echo "<br>!!!+++".$le_retour;
		if ($le_retour<>"0"){
			
			$query_maj_knock = "UPDATE #__bl_match SET ".$le_retour."=".$id_team." where ".$condition;        
			//echo  "<br>!!".$query_maj_knock;
			$db->setQuery($query_maj_knock);
			$db->query();
	
		}
		else {
			echo "erreurs maj scores... contactez l'administrateur !";
			exit();
		}
	}
	else {
		$id_equipe_adv=recup_equipe_adverse($id_match,$id_team);
		$query_test_equipe_adv_gagnante = "SELECT * from #__bl_match where "
			."((score1>score2 and team1_id=".$id_equipe_adv." and score2<>0) or (score1<score2 and team2_id=".$id_equipe_adv." and score1<>0) ) and id=".$id_match;        
		//echo  "<br>%%".$query_test_equipe_adv_gagnante." - ".recup_equipe_adverse($id_match,$id_team);
		$db->setQuery($query_test_equipe_adv_gagnante);
		$db->query();
		
		if ($db->getNumRows()>0){
			$query_test_team_dispo = "SELECT IF(team1_id>0,IF(team2_id>0,\"0\",\"team2_id\"),\"team1_id\") FROM #__bl_match where ".$condition;        
				//echo  "<br>££".$query_test_team_dispo;
				$db->setQuery($query_test_team_dispo);
				$db->query();
			$le_retour=$db->loadResult();
			//echo "<br>$$".$le_retour;
			if ($le_retour<>"0"){
				
				$query_maj_knock = "UPDATE #__bl_match SET ".$le_retour."=".$id_equipe_adv." where ".$condition;        
				//echo  "<br>==".$query_maj_knock;
				$db->setQuery($query_maj_knock);
				$db->query();
		
			}
			else {
				echo "erreurs maj scores... contactez l'administrateur !";
				exit();
			}
		}
	}
	
}

function ajout_joueurs_dans_saison($team,$id_joueur,$id_saison){
	$db = & JFactory::getDBO();
	
	$requete_ajout_joueurs_dans_saison="INSERT INTO `#__bl_players_team`(`team_id`, `player_id`, `confirmed`, `season_id`, `invitekey`, `player_join`) "
                ." VALUES (".$team.",".$id_joueur.",0,".$id_saison.",\"\",0)";
        //echo "<br>".$requete_ajout_joueurs_dans_saison;
	$db->setQuery($requete_ajout_joueurs_dans_saison);	
        $db->query();
	
	return($db->insertid());
	
}

function supprimer_joueurs_dans_saisons($id_joueur,$conditions_supp){
	$db = & JFactory::getDBO();
	
	$requete_supprimer_joueurs_dans_saisons="DELETE FROM `#__bl_players_team` WHERE `player_id`=".$id_joueur." ".$conditions_supp;
	$db->setQuery($requete_supprimer_joueurs_dans_saisons);	
        $db->query();
	
}

function joueurs_des_equipes($liste_equipes){
	$db = & JFactory::getDBO();
        //substr : pour enlever la derniere virgule à droite
        $requete_liste_joueurs="SELECT * FROM `#__bl_players` WHERE team_id in (".$liste_equipes.")";
        $db->setQuery($requete_liste_joueurs);	
        return($db->loadObjectList());
}

function dernier_id_mday(){
	$db = & JFactory::getDBO();
	$requete_max_mday="SELECT IFNULL((SELECT max(id) FROM `#__bl_matchday`),0)";
        $db->setQuery($requete_max_mday);	
    
        return($db->loadResult());
}

function existe_tourn($nom){
	$db = & JFactory::getDBO();
	$requete_existe_tourn="SELECT IFNULL((SELECT id FROM #__bl_tournament where name=\"".$nom."\"),0)";
        $db->setQuery($requete_existe_tourn);	
	//echo  $requete_existe_tourn;
        return($db->loadResult());
}

function liste_tourn($compl_req="",$sans_group="",$orderby="",$published=1){
	$db = & JFactory::getDBO();
	
	if (!test_non_vide($sans_group)){
		$compl_champs="g.id  as gid, g.group_name,(SELECT count(gt.t_id) FROM `#__bl_grteams` as gt  WHERE gt.`g_id`=g.id ) as nbre_equipes,";
		$compl_table=", `#__bl_groups` as g ";
		$compl_cond=" and g.s_id=s.s_id ";
		if (!test_non_vide($orderby))
			$orderby=" g.group_name ";
	}
	else {
		$compl_champs=" k_format, ";
		$compl_table=", `#__bl_matchday` as md  ";
		$compl_cond=" and md.s_id=s.s_id  ";
		if (!test_non_vide($orderby))
			$orderby=" nom_tourn ";
	}
	
	$requete_liste_tourn="SELECT  ".$compl_champs." s.s_id as id_saison, s.s_name as nom_saison, t.id as id_tourn, "
		." t.name as nom_tourn,s.s_participant as nbre_equipes_total  "
                ." FROM #__bl_seasons as s, #__bl_tournament as t  ".$compl_table
                ." WHERE t.id=s.t_id  and  s.published=".$published." ".$compl_cond.$compl_req."  order by ".$orderby;
        //echo  $requete_liste_tourn;                       
        
        $db->setQuery($requete_liste_tourn);	
        return($db->loadObjectList());
}

function ajout_equipe_dans_saison($id_saison,$id_equipe){
	$db = & JFactory::getDBO();
	$requete_ajout_season_teams="INSERT INTO `#__bl_season_teams` (`season_id`, `team_id`, `bonus_point`, `regtype`) "
                                ." VALUES (".$id_saison.",".$id_equipe.",0,\"0\") ";
        $db->setQuery($requete_ajout_season_teams);	
        $db->query();
}

function ajout_equipe_dans_groupe($id_groupe,$id_equipe){
	$db = & JFactory::getDBO();
	$requete_ajout_group_team="INSERT INTO `#__bl_grteams`(`g_id`, `t_id`) "
                                ." VALUES (".$id_groupe.",".$id_equipe.") ";
        $db->setQuery($requete_ajout_group_team);	
        $db->query();
}

function ajout_groupe($nom_groupe,$saison){
	$db = & JFactory::getDBO();
	$requete_ajout_groups="INSERT INTO `#__bl_groups` (`group_name`, `s_id`, `ordering`) "
                             ." VALUES(\"".$nom_groupe."\", ".$saison.", ".substr($saison, -1).");";
        $db->setQuery($requete_ajout_groups);	
        $db->query();
}

function recup_dernier_id_menu($parent_id){
	$db = & JFactory::getDBO();
	
	$requete_recup_dernier_id_menu_champ="SELECT max(rgt) FROM `#__menu` where `parent_id`=".$parent_id;
        $db->setQuery($requete_recup_dernier_id_menu_champ);
        //echo $requete_recup_dernier_id_menu_champ;
        $db->query();
        return($db->loadResult());
}

function nbre_equipes_dans_saison($id_saison){
	$db = & JFactory::getDBO();
	
	$requete_recup_nbre_equipes_dans_saison="SELECT s_participant FROM #__bl_seasons WHERE s_id=".$id_saison;
        $db->setQuery($requete_recup_nbre_equipes_dans_saison);
        //echo $requete_recup_nbre_equipes_dans_saison;
        $db->query();
        return($db->loadResult());
}

function journees_generes($id_saison){
	$db = & JFactory::getDBO();
	
	$requete_recup_nbre_journees_generes="SELECT count(id) as nbre_journees FROM #__bl_matchday WHERE s_id=".$id_saison;
        $db->setQuery($requete_recup_nbre_journees_generes);
        //echo $requete_recup_nbre_journees_generes;
        $db->query();
        return($db->loadResult());
}


function nbre_groupes_dans_saison($id_saison){
	$db = & JFactory::getDBO();
	
	$requete_recup_nbre_groupes_dans_saison="SELECT count(g.id) as nbre_groupes_dans_saison "
				." FROM `#__bl_groups` as g, #__bl_seasons as s WHERE g.s_id=s.s_id and s.s_id=".$id_saison;
        $db->setQuery($requete_recup_nbre_groupes_dans_saison);
        //echo $requete_recup_nbre_groupes_dans_saison;
        $db->query();
        return($db->loadResult());
}

function annuler_resa($id_match){

	$mysql_fif=connect_fif();

	$requete_annul_resa="Update Reservation SET id_match=\"\" WHERE id_match=".$id_match;
	//echo "req  : ".$requete_annul_resa;
	$mysql_fif->query($requete_annul_resa);		
	$mysql_fif->close();	
}


//test d'existance d'un fichier distant

function remote_file_exists($url){

	file_get_contents($url, NULL, NULL, 1, 1);
	//var_dump($http_response_header[0]);		
		
	if (strpos($http_response_header[0], 'Move') !== false)
		return false;
	else return true;

}

function recup_id_resa_fif($id_match){

	$mysql_fif=connect_fif();

	$requete_recup_id_resa_fif="Select id_resa FROM Reservation WHERE id_match=".$id_match;
	//echo "req  : ".$requete_recup_id_resa_fif;
	$recup_id_resa_fif=$mysql_fif->query($requete_recup_id_resa_fif);		
	$row = $recup_id_resa_fif->fetch_row();
	$recup_id_resa_fif->close();
	$mysql_fif->close();
	return($row[0]);	
}

function supprimer_tournoi($tourn){
	$db = & JFactory::getDBO();
	
	$requete_recup_saison_a_supprimer="SELECT s_id FROM  `#__bl_seasons` where t_id=".$tourn;
        $db->setQuery($requete_recup_saison_a_supprimer);	
        $db->query();
	
	$resultat_recup_saison_a_supprimer = $db->loadObjectList();
	
	foreach ($resultat_recup_saison_a_supprimer as $saison_a_supprimer) {
		$requete_supprimer_options_saison="DELETE FROM `#__bl_season_option` where `s_id`=".$saison_a_supprimer->s_id;
		$db->setQuery($requete_supprimer_options_saison);	
		$db->query();
				
		$requete_supprimer_options_rank_saison="DELETE FROM `#__bl_ranksort` where `seasonid`=".$saison_a_supprimer->s_id;
		$db->setQuery($requete_supprimer_options_rank_saison);	
		$db->query();
		
		$requete_recup_group="SELECT id FROM `#__bl_groups` where `s_id`=".$saison_a_supprimer->s_id;
                $db->setQuery($requete_recup_group);	
                $db->query();
		
		$resultat_recup_group = $db->loadObjectList();
	
		foreach ($resultat_recup_group as $group) {
			$requete_supprimer_groupteams="DELETE FROM `#__bl_grteams` where `g_id`=".$group->id;
			$db->setQuery($requete_supprimer_groupteams);	
			$db->query();
		}
		
		$requete_supprimer_group="DELETE FROM `#__bl_groups` where `s_id`=".$saison_a_supprimer->s_id;
                $db->setQuery($requete_supprimer_group);	
                $db->query();
		
		
		
		$requete_supprimer_season_teams="DELETE FROM `#__bl_season_teams` where `season_id`=".$saison_a_supprimer->s_id;
                $db->setQuery($requete_supprimer_season_teams);	
                $db->query();
		
		$requete_supprimer_joueurs_dans_saison="DELETE FROM `#__bl_players_team` where `season_id`=".$saison_a_supprimer->s_id;
		$db->setQuery($requete_supprimer_joueurs_dans_saison);	
		$db->query();
		
		$requete_recup_journees_a_supprimer="SELECT id FROM  `#__bl_matchday` where s_id=".$saison_a_supprimer->s_id;
		$db->setQuery($requete_recup_journees_a_supprimer);	
		$db->query();
		
		$resultat_recup_journees_a_supprimer = $db->loadObjectList();
		
		foreach ($resultat_recup_journees_a_supprimer as $journees_a_supprimer) {
			
			$requete_recup_matchs_a_supprimer="SELECT id FROM  `#__bl_match` where m_id=".$journees_a_supprimer->id;
			$db->setQuery($requete_recup_matchs_a_supprimer);	
			$db->query();
			
			
			
			
			
			$resultat_recup_matchs_a_supprimer = $db->loadObjectList();
			
			foreach ($resultat_recup_matchs_a_supprimer as $matchs_a_supprimer) {
				
				$requete_supprimer_buts_joueurs_de_la_saison="DELETE FROM `#__bl_match_events` where `match_id`=".$matchs_a_supprimer->id;
				$db->setQuery($requete_supprimer_buts_joueurs_de_la_saison);	
				$db->query();
				annuler_resa($matchs_a_supprimer->id);
			}
			
			$requete_supprimer_matchs_de_la_saison="DELETE FROM `#__bl_match` where `m_id`=".$journees_a_supprimer->id;
			$db->setQuery($requete_supprimer_matchs_de_la_saison);	
			$db->query();
			
		}
		
		$requete_supprimer_journees="DELETE FROM `#__bl_matchday` where s_id=".$saison_a_supprimer->s_id;
		$db->setQuery($requete_supprimer_journees);	
		$db->query();
		
		/*
		$requete_recup_menus_a_supprimer="SELECT s_id FROM  `#__bl_seasons` where t_id=".$tourn;
		$db->setQuery($requete_recup_saison_a_supprimer);	
		$db->query();
		
		$resultat_recup_saison_a_supprimer = $db->loadObjectList();
		
		$requete_ajout_menu="INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, "
                                ." `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`,"
                                ." `params`, `lft`, `rgt`, `home`, `language`, `client_id`) "
                                ." VALUES (\"mainmenu\", \"".$titre."\", \"".str_replace(" ", "-", strtolower($titre))."\", \"\","
                                ." \"".$chemin."\", \"".$lien."\", \"".$type."\", 1, ".$parent.", ".$niveau.", ".$comp_id.", 0, 0, "
                                ." \"0000-00-00 00:00:00\", 0, 1, \"\", 0, ".$menu_choisit.", ".$gche.", ".$dte.", 0, \"*\", 0)";
		$db->setQuery($requete_ajout_menu);
		//echo $requete_ajout_menu;
		$db->query();*/
	
	}
	$requete_saison_a_supprimer="DELETE FROM `#__bl_seasons` where t_id=".$tourn;
        $db->setQuery($requete_saison_a_supprimer);	
        $db->query();
	
	$requete_tourn_a_supprimer="DELETE FROM `#__bl_tournament` WHERE `id`=".$tourn;
        $db->setQuery($requete_tourn_a_supprimer);	
        $db->query();
	
	
	
	
}

function nbre_match_d_une_equipe($id_equipe){
	$db = & JFactory::getDBO();
	
	$requete_nbre_match_d_une_equipe="SELECT count(id) as nbre_matchs FROM `#__bl_match` WHERE `team1_id`=".$id_equipe." or `team2_id`=".$id_equipe;
        $db->setQuery($requete_nbre_match_d_une_equipe);	
        $db->query();
	return($db->loadResult());
	
}

function nbre_match_non_joues_d_une_saison($id_saison){
	$db = & JFactory::getDBO();
	
	$requete_nbre_match_non_joues_d_une_saison="SELECT IFNULL ((SELECT count(m.id) as nbre_matchs_non_joues FROM `#__bl_match` as m, #__bl_matchday as md "
		." WHERE md.`s_id`=".$id_saison." and  m.`m_id`=md.id and m.m_played=0),0) ";
	//echo $requete_nbre_match_non_joues_d_une_saison;
        $db->setQuery($requete_nbre_match_non_joues_d_une_saison);	
        $db->query();
	return($db->loadResult());
	
}

function nbre_reglements_d_une_equipe($id_equipe){
	$db = & JFactory::getDBO();
	
	$requete_nbre_reglements_d_une_equipe="SELECT count(id_reglement) as nbre_reg FROM `Reglement` as reg ,#__bl_players as p "
		." WHERE p.team_id=".$id_equipe." and p.id=reg.`id_joueur` and validation_reglement=1 ";
        $db->setQuery($requete_nbre_reglements_d_une_equipe);	
        $db->query();
	return($db->loadResult());
	
}


function supprimer_equipe($id_equipe){
	$db = & JFactory::getDBO();
	
	
	if (nbre_match_d_une_equipe($id_equipe)==0){
		if (nbre_reglements_d_une_equipe($id_equipe)==0){
			$liste_joueurs = liste_joueurs_d_une_equipe($id_equipe,1);
		
			foreach ($liste_joueurs as $joueur)
				supprimer_joueur($joueur->id);
			
			$requete_supprimer_teams="DELETE FROM `#__bl_moders` where `tid`=".$id_equipe;
			$db->setQuery($requete_supprimer_teams);	
			$db->query();
			
			$requete_supprimer_teams="DELETE FROM `#__bl_teams` where `id`=".$id_equipe;
			$db->setQuery($requete_supprimer_teams);	
			$db->query();
			
			$requete_supprimer_groupteams="DELETE FROM `#__bl_grteams` where `t_id`=".$id_equipe;
			$db->setQuery($requete_supprimer_groupteams);	
			$db->query();
				
			$requete_supprimer_season_teams="DELETE FROM `#__bl_season_teams` where `team_id`=".$id_equipe;
			$db->setQuery($requete_supprimer_season_teams);	
			$db->query();
			echo "Equipe supprim&eacute;e<br><br>";
		}
		else echo "Impossible de supprimer cette equipe car des joueurs ont des reglements<br><br>";
	}
	else echo "Impossible de supprimer cette equipe car elle a des matchs de prévus<br><br>";
	
}

function maj_validation_reglement($validation_reglement,$id_regl,$id_joueur=""){
$db = & JFactory::getDBO();
		
	if (test_non_vide($id_joueur))
		$compl_req=" id_joueur=".$id_joueur;
	
	if (test_non_vide($id_regl))
		$compl_req=" id_reglement=".$id_regl;
	 
	$requete_maj_regl="UPDATE Reglement set  validation_reglement=".$validation_reglement." WHERE ".$compl_req;
	//echo "<br>reqsuppr: ".$requete_maj_regl;
	$db->setQuery($requete_maj_regl);	
		
	return ($db->query());

}

function ajout_reglement($id_joueur,$montant,$moyen_paiement,$info="",$remise="",$validation=1){
	$user =& JFactory::getUser();
	$db = & JFactory::getDBO();
	
	$requete_ajout_reglement="INSERT INTO `Reglement`(id_user,`id_joueur`, `montant_reglement`, `date_reglement`,info,";
	$requete_ajout_reglement.=" `heure_reglement`, `id_moyen_paiement`, `id_type_reglement`, `taux_TVA`, `validation_reglement`,id_remise)";
	$requete_ajout_reglement.=" VALUES (".$user->id.",".$id_joueur.",\"".str_replace(",", ".", $montant)."\",\"".date("Y-m-d")."\",\"".$info."\",";
	$requete_ajout_reglement.=" \"".date("H:i")."\",\"".$moyen_paiement."\",\"\",\"\",".$validation.",\"".$remise."\")";
	//echo "req88: ".$requete_ajout_reglement;
	$db->setQuery($requete_ajout_reglement);	
	$db->query();
	return($db->insertid());
}

function versements_sans_remise_et_avec_validation($id_joueur,$annee="",$mois=""){
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

	if(test_non_vide($annee) and test_non_vide($mois)){
		if($mois>=9)
			$compl_req=" and  `date_reglement`<=\"".($annee+1)."-08-30\" and `date_reglement`>=\"".$annee."-09-01\" ";
		else $compl_req=" and  `date_reglement`<=\"".($annee)."-08-30\" and `date_reglement`>=\"".($annee-1)."-09-01\" ";
	}

	$requete_versement="select sum(reg.montant_reglement) as total_versement from Reglement as reg ";
	$requete_versement.="  where reg.validation_reglement=1 and reg.id_joueur=".$id_joueur." and (reg.id_remise is null or reg.id_remise=0) ".$compl_req; 
						
	//echo $requete_versement;
	$db->setQuery($requete_versement);	
	$db->query();
	return ($db->loadResult());
}







?>