<?php

defined('_JEXEC') or die( 'Restricted access' );
?>

Afin que je puisse planifier les matchs de coupe, je vous redonne la possibilit&eacute; de voter pour un nouvel horaire pr&eacute;f&eacute;r&eacute; <b>jusqu&apos;au mardi 20 novembre 2012 minuit</b>.
<br> 
Par d&eacute;faut, si vous ne votez pas, je prendrais en compte votre vote de septembre dernier.
<br><br>
<?
$user =& JFactory::getUser();
$db = & JFactory::getDBO();

if (isset($_POST["etat"])){
	$requete_efface_old_etat="delete from #__bl_extra_values where uid=".$user->id." and f_id=6";
       
       $db->setQuery($requete_efface_old_etat);	
       
       $resultat_efface_old_etat = $db->loadObjectList();
       
       $requete_insert_new_etat="INSERT INTO #__bl_extra_values (f_id, uid, fvalue) VALUES (\"6\",".$user->id.",\"".$_POST["etat"]."\")";
       $db->setQuery($requete_insert_new_etat);	
       
       $resultat_insert_new_etat = $db->loadObjectList();
}

 
$query = "select team_id from #__bl_players_team as pt where pt.player_id=".$user->id." and pt.season_id in (2,3,5)";

$db->setQuery($query);
$resultat_equipe_user = $db->loadObjectList();

foreach ($resultat_equipe_user as $equipe_user ){

	$query = "SELECT u.email, p.id, p.first_name as Prenom, p.nick,(select es.sel_value from #__bl_extra_select as es, #__bl_extra_values as ev "
		." where es.fid=6 and p.id=ev.uid and es.id=ev.fvalue) as vote  FROM  #__bl_players as p "
		." LEFT OUTER JOIN #__users u ON p.usr_id = u.id, #__bl_players_team as pt, #__bl_teams as t "
		." where p.id=pt.player_id and t.id=".$equipe_user->team_id." and t.id=pt.team_id and pt.season_id in (2,3,5) "
		." and p.nick<>\"CSC\" order by u.email desc,vote asc";
	
	
	$db->setQuery($query);
	$vote_horaire = $db->loadObjectList();
	
	
	$requete_liste_etat="SELECT * FROM #__bl_extra_select where fid=6 ORDER BY id";
	
	$db->setQuery($requete_liste_etat);	
	$resultat_liste_etat = $db->loadObjectList();
	
	echo "<table class=\"zebra\">";
		
	echo "<thead><tr><th align=\"center\">Pr&eacute;nom</th><th align=\"center\">Flocage</th><th align=\"center\">mon vote</th><th align=\"center\">Choix</th></tr></thead>";
	
		$rep=JURI::base();
		foreach ($vote_horaire as $horaire ){
			
			echo "<tr><td >";
			echo $horaire->Prenom;
			echo "</td><td >";
			echo $horaire->nick;
			echo "</td><td >"; 
	
			if ($user->id==$horaire->id) {
				 echo "<FORM name=\"form\" class=\"submission box\" action=\"horaire-prefere\" method=post >";
			       echo "<select name=\"etat\" size=1><option value=0></option>";
			       foreach($resultat_liste_etat as $liste_etat) 
				       echo "<option value=\"".$liste_etat->id."\">".$liste_etat->sel_value."</option>";
			       
			       echo "</select><input name=\"valide\" type=\"submit\"  value=\"OK\" >";
			echo "</form>";
			}
			echo "</td><td >".$horaire->vote."</td></tr>";			
		}
		echo "</table><br />";


?>
<HR>
<font size="3"><b>R&eacute;sultat provisoire du vote sur les horaires</b></font>
	<br>
	<HR>
<table class='zebra'>
	<tr><td align="center"><b>Horaire</b></td><td><b>Nbre de votants</b></td></tr>
<?php
	$query = "SELECT pt.team_id,ev.f_id,ef.name, ev.fvalue,es.sel_value, count(ev.uid) as vote_equipe "
		." FROM #__bl_extra_values as ev, #__bl_players_team as pt, #__bl_extra_select as es, #__bl_extra_filds as ef  "
		." WHERE  ef.id=ev.f_id and es.id=ev.fvalue and ev.uid=pt.player_id and pt.season_id in (2,3,5) and ev.f_id=6 "
		." and pt.team_id=".$equipe_user->team_id." group by pt.team_id,ev.f_id,ev.fvalue  order by pt.team_id,ev.f_id,ev.fvalue";
	//echo $query;
	$db->setQuery($query);
	$vote_horaires = $db->loadObjectList();
		
	$nom_max="sans pr&eacute;f&eacute;rence";
	$val_max=0;
	for($i=0;$i<count($vote_horaires);$i++){
		$vote = $vote_horaires[$i];
		if ($vote->vote_equipe>$val_max){
			$nom_max=$vote->sel_value;
			$val_max=$vote->vote_equipe;	
		}
		?>
		<tr><td align="center"><? echo $vote->sel_value; ?></td><td><? echo $vote->vote_equipe; ?></td></tr>
	<? }?>
		<tr><td align="center"><b>Resultat</b></td><td><b><?echo $nom_max; ?></b></td></tr>
	
</table>
<br><br>IMPORTANT : En cas d'egalit&eacute; de vote au sein d'une meme &eacute;quipe, je prendrai en compte l'horaire le moins demand&eacute; par les autres &eacute;quipes.

<?
}

$requete_recup_pref_horaires="SELECT t.t_name as team,LEFT((SELECT es.sel_value FROM #__bl_extra_select as es, #__bl_extra_values as ev, "
                                    ." #__bl_players as p, #__bl_players_team as pt where f_id=6 and ev.uid=p.id and p.id=pt.player_id "
                                    ." and t.id=pt.team_id and es.id=ev.fvalue and pt.season_id in (2,3,5) and p.nick<>\"CSC\" and es.sel_value<>\"\" "
                                    ." group by pt.team_id, es.sel_value order by pt.team_id,count(p.id) desc LIMIT 0,1),2)"
                                    ." as horaire_pref FROM #__bl_teams as t";
$db->setQuery($requete_recup_pref_horaires);	
$resultat_recup_pref_horaires = $db->loadObjectList();
    
$tab_horaires_pref=array();
$tab_nbre_demandes_par_horaires=array();
    
foreach ($resultat_recup_pref_horaires as $recup_pref_horaires) {
	$tab_horaires_pref[]=array("equipe" => $recup_pref_horaires->team,"hor_pref" => $recup_pref_horaires->horaire_pref);
        $tab_nbre_demandes_par_horaires[$recup_pref_horaires->horaire_pref]["horaire"]=$recup_pref_horaires->horaire_pref;
        $tab_nbre_demandes_par_horaires[$recup_pref_horaires->horaire_pref]["nbre_total_demandes"]+=1;
}
    
echo "<br><hr><br><b>Horaires pr&eacute;f&eacute;r&eacute;s de toutes les &eacute;quipes :</b>";
foreach ($tab_nbre_demandes_par_horaires as $demandes_par_horaires){
	if ($demandes_par_horaires["horaire"]=="")
		$texte="sans preference";
	else $texte =$demandes_par_horaires["horaire"]."h";
	echo "<br><br><b><u>".$texte." :</u> ".$demandes_par_horaires["nbre_total_demandes"]." Equipes</b>";
	foreach ($tab_horaires_pref as $horaires_pref){
		if ($demandes_par_horaires["horaire"]==$horaires_pref["hor_pref"])
		echo "<br>".$horaires_pref["equipe"];
	
	}
	
}
?>
