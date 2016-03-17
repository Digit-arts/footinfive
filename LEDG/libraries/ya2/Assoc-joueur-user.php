<?php

defined('_JEXEC') or die( 'Restricted access' );

$user =& JFactory::getUser();
$db = & JFactory::getDBO();

if ($user->id==42){
$requete_joueurs_non_assoc="select * from vlxhj_bl_players where usr_id not in (select id from vlxhj_users as u) and nick<>\"Csc\" ";
$db->setQuery($requete_joueurs_non_assoc);	
$resultat_joueurs_non_assoc = $db->loadObjectList();

//echo $requete_joueurs_non_assoc;

echo "<FORM name=\"form\"  class=\"submission box\" action=\"assoc-joueur-user\" method=post >";


foreach ($resultat_joueurs_non_assoc as $joueurs_non_assoc) {
   echo "<br />".$joueurs_non_assoc->id."-".$joueurs_non_assoc->first_name;
   echo " associ&eacute; &agrave; <input name=\"email".$joueurs_non_assoc->id."\" type=\"text\" >";
   echo " <input name=\"pass".$joueurs_non_assoc->id."\" type=\"text\">";
   echo " <input name=\"name".$joueurs_non_assoc->id."\" type=\"hidden\"  value=\"".$joueurs_non_assoc->first_name."\" >";
   echo " <input name=\"id".$joueurs_non_assoc->id."\" type=\"hidden\"  value=\"".$joueurs_non_assoc->id."\" >";
}

echo "<input name=\"valide\" type=\"submit\"  value=\"OK\" >";
echo "</form>";
echo "<br /><br />";
$id_aro=$id_aro_max->le_max+1;
    for ($i=1;$i<=1000;$i++){
     if ($_POST["email$i"]<>""){
         
         echo "UPDATE vlxhj_bl_players set usr_id=\"".$_POST["id$i"]."\" where id=\"".$_POST["id$i"]."\";<br />";
         echo "INSERT INTO vlxhj_users(`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `registerDate`, `lastvisitDate`, `activation`, `params`, `lastResetTime`, `resetCount`) ";
         echo "VALUES (\"".$_POST["id$i"]."\",\"".$_POST["name$i"]."\",Trim(\"".Trim($_POST["email$i"])."\"),";
         echo " Trim(\"".Trim($_POST["email$i"])."\"),MD5(\"".$_POST["pass$i"]."\"),\"\",\"0\",\"0\",\"0000-00-00 00:00:00\",\"0000-00-00 00:00:00\",\"\",\"admin_language=\nlanguage=\neditor=\nhelpsite=\ntimezone=0\",\"0000-00-00 00:00:00\",0);<br />";
         echo "INSERT INTO `vlxhj_user_usergroup_map`(`user_id`, `group_id`) VALUES (\"".$_POST["id$i"]."\",2);<br />";
$id_aro++;
     }
    }
}
?>