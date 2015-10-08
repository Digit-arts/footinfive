<?php
require_once ('fonctions_gestion_user.php');
require_once ('fonctions_module_reservation.php');

if (test_non_vide($_POST["date_journee"]))
    header("Location: today?journee=".$_POST["date_journee"]."");
    
?>
<script type="text/javascript">
    
    function filtrer(texte_a_afficher) {
		if (texte_a_afficher!=''){
                    if (confirm(texte_a_afficher)){
			 document.form_filtrer.submit();
		    }
		}
    }
    function recharger(texte_a_afficher,lien) {
	if (texte_a_afficher!=''){
	    if (confirm(texte_a_afficher)){
		if (lien!='') document.location.href=lien;
            }
        }
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
    
$requete_recup_liste="SELECT (select name from #__users where id=id_user_ouverture) as user_ouv, ";
$requete_recup_liste.=" (select name from #__users where id=id_user_cloture) as user_clo, ";
$requete_recup_liste.=" FC.* FROM `Feuille_Caisse` as FC where 1 ";

//echo $requete_recup_liste;

if (test_non_vide($_POST["date_filtree"]))
    $requete_recup_liste.=" and journee=\"".$_POST["date_filtree"]."\"";;

$requete_recup_liste.=" order by journee desc ";
//echo "req87: ".$requete_recup_liste;
$db->setQuery($requete_recup_liste);
$resultat_recup_liste = $db->loadObjectList();

$bordure_tab=" style=\"border-collapse: collapse;\" ";
$bordure_td=" style=\"border: 1px solid black;\" width=\"100\" align=center ";
$bordure_th=$bordure_td." bgcolor=\"#CCCCCC\" align=center ";
?>
<table border="0" width="100%" >
        <tr>
            <td>
                <?
                echo "<a href=\"index.php/caisse/today?ouvrir=1\">Ouvrir une journee non traitee</a>";
                ?>
            </td>
            <td>
                <form name="form_filtrer" class="submission box" action="<?php echo JRoute::_('index.php/component/content/article?id=71'); ?>" method="post"  >
                <?
                 echo "Date : ";
                 echo " <input type=\"date\" name=\"date_filtree\" >";
                ?>
                    <input name="filtre" type="button" value="filtrer" onclick="filtrer('Confirmez votre selection','')">
                </form>
   
            </td>
        </tr>
</table>

<hr>

<table border="1" width="100%" <? echo $bordure_tab; ?>>
                <tr>
                    <th <? echo $bordure_th?> >&nbsp;</th>
                    <th <? echo $bordure_th?> colspan=3>LIENS</th>
                    <th <? echo $bordure_th?> colspan=3>DEBUT</th>
                    <th <? echo $bordure_th?> colspan=3>FIN</th>
                    <th <? echo $bordure_th?> colspan=3>CAISSE</th>
                </tr>
                <tr>
                    <th <? echo $bordure_th?> >Journee</th>
                    <th <? echo $bordure_th?> >VOIR</th>
                    <th <? echo $bordure_th?> >MODIF</th>
                    <th <? echo $bordure_th?> >SUPPR</th>
                    <th <? echo $bordure_th?> > Date<br>Heure</th>
                    <th <? echo $bordure_th?> >Agent</th>
                    <th <? echo $bordure_th?> >MONT</th>
                    <th <? echo $bordure_th?> >DATE<br>Heure</th>
                    <th <? echo $bordure_th?> >Agent</th>
                    <th <? echo $bordure_th?> >MONT</th>
                    <th <? echo $bordure_th?> >REGL<br>ESP</th>
                    <th <? echo $bordure_th?> >DIFF</th>
                    <th <? echo $bordure_th?> >SOLDE<br>TOTAL</th>
                </tr>
<? $i=0;          
    foreach($resultat_recup_liste as $recup_liste){
        echo "<tr>";
        echo "<td ".$bordure_td." ><b>".date_longue($recup_liste->journee)."</b></td>";
        echo "<td ".$bordure_td." ><a target=_blank href=\"index.php/caisse/today?%3Afeuille-de-caisse&tmpl=component&print=1&layout=default&page=&option=com_content&Itemid=307&vue=1&id_journee=".$recup_liste->id_journee."\">";
        echo "<img src=\"images/feuille-caisse-icon.png\" title=\"detail feuille de caisse\" ></a></td>";
        echo "<td ".$bordure_td." >";
        if (est_min_manager($user)){
            echo "<a href=\"index.php/caisse/today?modifier=".$recup_liste->id_journee."&id_journee=".$recup_liste->id_journee."\">";
            echo "<img src=\"images/modifier-feuille-caisse.png\" title=\"Modifier feuille de caisse\" ></a>";
        }
        echo "</td>";
                echo "<td ".$bordure_td." >";
        if (est_min_manager($user)){
            echo "<a onClick=\"recharger('Voulez-vous supprimer cette journee ?','../../index.php/caisse/today?supprimer=".$recup_liste->id_journee."&id_journee=".$recup_liste->id_journee."')\">";
            echo "<img src=\"images/supprimer-feuille-caisse.png\" title=\"Supprimer feuille de caisse\" ></a>";
        }
        echo "</td>";
        echo "<td ".$bordure_td." nowrap>".$recup_liste->date_ouverture."<br>".$recup_liste->heure_ouverture."</td>";
        echo "<td ".$bordure_td." >".$recup_liste->user_ouv."</td>";
        echo "<td ".$bordure_td." >".$recup_liste->montant_ouverture."&#8364;</td>";
        echo "<td ".$bordure_td." nowrap >".$recup_liste->date_cloture."<br>".$recup_liste->heure_cloture."</td>";
        echo "<td ".$bordure_td." >".$recup_liste->user_clo."</td>";
        echo "<td ".$bordure_td." >".$recup_liste->montant_cloture."&#8364;</td>";
        echo "<td ".$bordure_td." >".$recup_liste->Montant_Espece."&#8364;</td>";
        echo "<td ".$bordure_td." >".$recup_liste->Diff_Caisse."&#8364;</td>";
        echo "<td ".$bordure_td." >".$recup_liste->solde_total."&#8364;</td>";
        
        echo "</tr>";
	$i++;
	if ($i==54) break; // si plus de 55 lignes, bug d'affichage...
    }


?>
</table>
<?
}
?>


