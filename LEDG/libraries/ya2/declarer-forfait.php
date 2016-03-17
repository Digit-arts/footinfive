<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once ('libraries/ya2/fonctions_ledg.php');

$user =& JFactory::getUser();
$db = & JFactory::getDBO();
?>
<script type="text/javascript">
    function valider() {
        if (confirm('Confirmez-vous votre choix ?')){
		document.form.submit();
        }
    }
</script>
<?
if (!test_saisie($user->id)){
if (isset($_POST["id_match"])){
    
   $tete = 'MIME-Version: 1.0' . "\n";
   $tete .= "Content-type: text/html; charset=\"UTF-8\"\r\n";
   $tete .= "From: FOOT IN FIVE <contact@footinfive.com>\n";
   $tete .= "Reply-To: contact@footinfive.com\n";
   $tete .= "Return-Path: contact@footinfive.com\n"; 
   
        $query = "SELECT u.email as mail_joueur, p.id, p.first_name as Prenom, p.nick, t.t_name as team, m.m_time as heure, m.m_date as jour_match , m.team1_id, m.team2_id  "
                    ." FROM  `#__bl_match` as m, #__bl_players as p, #__users as u , #__bl_players_team as pt, #__bl_teams as t"
                    ."  where p.usr_id = u.id and p.id=pt.player_id and m.id=".$_POST["id_match"]
                    ." and (t.id=m.`team1_id` or t.id=m.`team2_id`) and t.id=pt.team_id and pt.season_id in (97) and p.nick<>\"CSC\" order by t.t_name ";
            
        //echo  $query;
        
        $db->setQuery($query);
        $liste_emails_equipe = $db->loadObjectList();
        $compteur=0;
        $objet="Match forfait";
        
        foreach ($liste_emails_equipe as $emails_equipe ){
            $le_joueur=$emails_equipe->Prenom."(".$emails_equipe->team.")";
            $bonjour="Salut ".$le_joueur.",<br><br>";
            $corps="Ceci est un mail automatique.<br>";
            $corps.=$user->name." de l &eacute;quipe (".$_POST["nom_equipe_user"].") d&eacute;clare que son &eacute;quipe sera forfait pour le match contre ".$_POST["match"].".<br>";
            $corps.="<br>Merci de pr&eacute;venir les joueurs qui n ont pas communiqu&eacute;s leurs emails.";
            
            mail($emails_equipe->mail_joueur,$objet,$bonjour.$corps,$tete);
  
            //echo $bonjour.$corps;
        }
        mail("lefloch.g@gmail.com;lyassine@ifbi.fr",$objet,$corps,$tete);
        
        $query = "SELECT TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(\"".$_POST["jour_match"]."\",\" \",\"".$_POST["heure"]."\") AS CHAR(22))) as temps_restant ";
        
        //echo $query;

        $db->setQuery($query);
        $resultat_temps_restant = $db->loadResult();
        
        /*if ($resultat_temps_restant>=1440) //si plus de 24h de delais
            $complement_requete=", bonus1=1, bonus2=1 ";
        else {
            $complement_requete1=", bonus1=0, bonus2=1 ";
            $complement_requete2=", bonus1=1, bonus2=0 ";
        }*/
        
        $query1 = "UPDATE `#__bl_match` SET m_played=1, score1=0, score2=5 ".$complement_requete."  ".$complement_requete1
                ." where id=".$_POST["id_match"]." and team1_id=".$_POST["id_equipe_user"];        
        //echo  $query1;
        $db->setQuery($query1);
        $db->query();
        
        $query2 = "UPDATE `#__bl_match` SET m_played=1, score1=5, score2=0 ".$complement_requete."  ".$complement_requete2
                ." where id=".$_POST["id_match"]." and team2_id=".$_POST["id_equipe_user"];        
        //echo  $query2;
        $db->setQuery($query2);
        $db->query();
        
        echo "Le match est d&eacute;clar&eacute; forfait.<br><br>Les emails ont &eacute;t&eacute; envoy&eacute;s aux joueurs des deux &eacute;quipes.<br>";
        
        
}
else {

    $query = "select pt.team_id, t.t_name as la_team from #__bl_players_team as pt, #__bl_teams as t "
            ." where pt.team_id=t.id and pt.player_id=".$user->id." and pt.season_id in (97)";
    
    //echo  $query;
    
    $db->setQuery($query);
    $resultat_equipe_user = $db->loadObjectList();
    
    foreach ($resultat_equipe_user as $equipe_user ){
    
        $query = "SELECT m.id as id_match, m.m_time as heure, m.m_date as jour_match ,t.t_name as nom_equipe_adv, t.id, m.team1_id, m.team2_id "
                ." FROM `#__bl_match` as m, #__bl_teams as t where t.id<>".$equipe_user->team_id ." and (t.id=m.`team1_id` or t.id=m.`team2_id`) "
                ." and m.`published`=1 and m.`m_played`=0 and (m.`team1_id`=".$equipe_user->team_id."  or m.`team2_id`=".$equipe_user->team_id.")  "
                ." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))<=10080 "
                ." and TIMESTAMPDIFF(MINUTE,NOW(),CAST(concat(m.m_date,\" \",m.m_time) AS CHAR(22)))>0";
        
        //echo $query;
        //TIMESTAMPDIFF(MINUTE,NOW(),STR_TO_DATE('m.`m_date` m.m_time', '%Y-%m-%d %H:%i'))
        
        
        $db->setQuery($query);
        $resultat_equipe_adv = $db->loadObjectList();
        
        if (empty($resultat_equipe_adv)) echo "Aucun match n&apos;est pr&eacute;vu pour dimanche prochain.<br />";
        else {
        
            foreach ($resultat_equipe_adv as $equipe_adv ){
                
                $jour_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
                $mois_fr = array("Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
                
                list( $annee, $mois,$jour) = explode('-',$equipe_adv->jour_match);
                $date_longue = mktime (0, 0, 0, $mois, $jour, $annee);
                
                $match=$equipe_adv->nom_equipe_adv
                    ." du ".$jour_fr[date("w", $date_longue)]." ".$jour." ".$mois_fr[date("n", $date_longue)-1]." ".$annee." &agrave; "
                    .$equipe_adv->heure;
                
                echo "Mon &eacute;quipe d&eacute;clare forfait pour le match contre ".$match."<br />";
                
                echo "<FORM name=\"form\" class=\"submission box\" action=\"declarer-forfait\" method=post >";
                    
                    echo "<input name=\"id_match\" type=\"hidden\"  value=\"".$equipe_adv->id_match."\" >";
                    echo "<input name=\"nom_equipe_user\" type=\"hidden\"  value=\"".$equipe_user->la_team."\" >";
                    echo "<input name=\"id_equipe_user\" type=\"hidden\"  value=\"".$equipe_user->team_id."\" >";
                    echo "<input name=\"jour_match\" type=\"hidden\"  value=\"".$equipe_adv->jour_match."\" >";
                    echo "<input name=\"heure\" type=\"hidden\"  value=\"".$equipe_adv->heure."\" >";
                    echo "<input name=\"match\" type=\"hidden\"  value=\"".$match."\" >";
                    
                echo "<input name=\"valide\" type=\"button\"  value=\"D&eacute;clarer forfait\" onclick=\"valider()\">";
                echo "</form><hr><br />";
            }
        }
    }
}
echo  "<HR><h3>La probl&eacute;matique</h3>Une &eacute;quipe ne peut pas jouer son prochain match.<br />";

echo  "<h3>Comment peut-elle d&eacute;clarer forfait ?</h3>En cliquant sur le bouton \"D&eacute;clarer forfait\" en haut de cette page. <font color=red><b>(Attention : action irreversible)</b></font><br />";

echo  "<h3>Que se passera-t-il ?</h3>En cliquant sur \"D&eacute;clarer forfait\", un email automatique sera envoy&eacute; &agrave; l&apos;ensemble des joueurs des deux &eacute;quipes pour les informer.<br />";

echo  "<h3>Qui peut d&eacute;clarer forfait ?</h3>N&apos;importe quel joueur des deux &eacute;quipes, son pr&eacute;nom sera mentionn&eacute; dans l&apos;email.<br />";

echo  "<h3>D&eacute;lais</h3>Vous devrez d&eacute;clarer forfait 24h <font color=red><b>(au plus tard)</b></font> avant le d&eacute;but du match.<br />";

echo  "<h3>La p&eacute;nalit&eacute;</h3>Passez ce d&eacute;lais, le point FairPlay ne vous sera pas accord&eacute;.<br>L&apos;heure du mail faisant foi.";
}
else header("Location: ../ep");
?>