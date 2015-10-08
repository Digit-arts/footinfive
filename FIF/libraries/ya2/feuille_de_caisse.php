<?php
require_once ('fonctions_gestion_user.php');
require_once ('fonctions_module_reservation.php');
?>
<script type="text/javascript">
	
    function recharger(texte_a_afficher,lien) {
		if (texte_a_afficher!=''){
                    if (confirm(texte_a_afficher)){
			if (lien!='') document.location.href=lien;
                        else document.register_feuille_caisse.submit();
		    }
		}
    }
    
    	
    function envoyer(texte_a_afficher) {
		if (texte_a_afficher!=''){
                    if (confirm(texte_a_afficher)){
			 document.ouvrir_caisse_non_traitee.submit();
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

if (test_non_vide($_GET["ouvrir"])){
  ?>
    <table border="0" width="100%" >
        <tr>
            <td>
                <?
                if (est_min_manager($user)){ ?>
                <form name="ouvrir_caisse_non_traitee" class="submission box" action="<?php echo JRoute::_('toutes'); ?>" method="post"  >
                <?
                 echo "Ouvrir une journee non traitee ? ";
                 echo " <input type=\"date\" name=\"date_journee\" >";
                ?>
                    <input name="ouvrir" type="button" value="ouvrir" onclick="envoyer('Confirmez votre selection','')">
                </form>
                <?}?>
            </td>
        </tr>
    </table>
<?
    
}
else {

if (test_non_vide($_GET["supprimer"])){
    $requete_supprimer_caisse="DELETE FROM `Feuille_Caisse` WHERE id_journee=".$_GET["supprimer"];
    //echo "req88: ".$requete_supprimer_caisse;
    $db->setQuery($requete_supprimer_caisse);	
    $db->query();
    $requete_supprimer_decompo="DELETE FROM `decompo_caisse` WHERE id_journee=".$_GET["supprimer"];
    //echo "req88: ".$requete_supprimer_decompo;
    $db->setQuery($requete_supprimer_decompo);	
    $db->query();
    header("Location: ../../index.php/caisse/toutes");
}

if (test_non_vide($_POST["modifier"])){
    $requete_modifier_caisse="UPDATE `Feuille_Caisse` SET `date_cloture`=NULL, `heure_cloture`=NULL,";
    $requete_modifier_caisse.=" `id_user_cloture`=NULL ,`montant_ouverture`=NULL,`montant_cloture`=NULL,";
    $requete_modifier_caisse.=" `Nbre_cheques`=0,`Nbre_CB`=0,`Montant_CB`=0,`Montant_Cheques`=0,";
    $requete_modifier_caisse.=" `Montant_Espece`=0,`Remb_CB`=0,`Diff_Caisse`=0,`Nbre_ba`=0,`Montant_ba`=0,";
    $requete_modifier_caisse.=" `solde_total`=0 where id_journee=".$_POST["modifier"];
    //echo "req88: ".$requete_modifier_caisse;
    $db->setQuery($requete_modifier_caisse);	
    $db->query(); 
}

if (test_non_vide($_POST["modifier"])){
    $requete_modifier_decompo_caisse="DELETE FROM `decompo_caisse` WHERE id_journee=".$_POST["modifier"];
    //echo "req88: ".$requete_modifier_decompo_caisse;
    $db->setQuery($requete_modifier_decompo_caisse);	
    $db->query();  
}

if (test_non_vide($_POST["Cloturer"])){
        
        $requete_cloturer_caisse="UPDATE `Feuille_Caisse` SET `date_cloture`=\"".date("Y-m-d")."\", `heure_cloture`=\"".date("H:i")."\",";
        $requete_cloturer_caisse.="`montant_ouverture`=\"".$_POST["Montant_debut"]."\", `montant_cloture`=\"".$_POST["Montant_fin"]."\", ";
        $requete_cloturer_caisse.=" `id_user_cloture`=\"".$user->id."\" , `Nbre_cheques`=\"".$_POST["Nbre_cheques"]."\", `Nbre_CB_web`=\"".$_POST["Nbre_CB_web"]."\", `Montant_CB_web`=\"".$_POST["Montant_CB_web"]."\", ";
        $requete_cloturer_caisse.=" `Nbre_CB`=\"".$_POST["Nbre_CB"]."\", `Montant_CB`=\"".$_POST["Montant_CB"]."\", `Montant_Cheques`=\"".$_POST["Montant_Cheques"]."\", ";
        $requete_cloturer_caisse.=" `Montant_Espece`=\"".$_POST["Montant_Espece"]."\", `Remb_CB`=\"".$_POST["Remb_CB"]."\", `Diff_Caisse`=\"".$_POST["Diff_Caisse"]."\", ";
        $requete_cloturer_caisse.=" `Nbre_ba`=\"".$_POST["Nbre_ba"]."\", `Montant_ba`=\"".$_POST["Montant_ba"]."\", solde_total=\"".$_POST["solde_total"]."\" ";
        $requete_cloturer_caisse.="  where id_journee=".$_POST["Cloturer"]." ";
        //echo "req88: ".$requete_cloturer_caisse;
        $db->setQuery($requete_cloturer_caisse);	
        $db->query();
	
	$corps="<br>date_cloture=".date("Y-m-d")."<br>heure_cloture=".date("H:i")."<br><br>"
		."montant_ouverture=".$_POST["Montant_debut"]."€<br>montant_cloture=".$_POST["Montant_fin"]."€<br><br>"
		."Montant_Espece=".str_replace(",","",number_format(str_replace(",","",$_POST["Montant_Espece"]),2))."€<br>Solde_total=".$_POST["solde_total"]."€<br>Diff_Caisse=".$_POST["Diff_Caisse"]."€<br><br>id_journee=".$_POST["Cloturer"]."";
	
        sendMail(267,"Cloture caisse",$corps);
	//sendMail(1,"Cloture caisse",$corps);
	
        header("Location: ../../index.php/caisse/toutes");
    }

if (test_non_vide($_GET["id_journee"]) or test_non_vide($_GET["journee"]) )
    if (test_non_vide($_GET["id_journee"]))
            $complement_requete1=" id_journee=".$_GET["id_journee"];
    else $complement_requete1=" journee=\"".$_GET["journee"]."\"";
else {
    if (test_non_vide($_POST["journee"]))
        $complement_requete2=" journee=\"".$_POST["journee"]."\" ";
    else {
        $requete_recup_journee_non_cloturee="SELECT min(`id_journee`) as id from Feuille_Caisse where date_cloture is null";
        //echo $requete_recup_journee_non_cloturee;
        $db->setQuery($requete_recup_journee_non_cloturee);
        $resultat_recup_journee_non_cloturee = $db->loadObject();
        
        if (test_non_vide($resultat_recup_journee_non_cloturee->id))
            header("Location: ../caisse/today?id_journee=".$resultat_recup_journee_non_cloturee->id."");
        
        $complement_requete3=" date_cloture is null and date_ouverture=\"".date("Y-m-d")."\"";
    }
}
    
$requete_recup_id_journee="SELECT `id_journee`, montant_ouverture, montant_cloture, date_ouverture, heure_ouverture, date_cloture, heure_cloture, ";
$requete_recup_id_journee.=" (select name from #__users where id=id_user_ouverture) as user_ouverture, ";
$requete_recup_id_journee.=" (select name from #__users where id=id_user_cloture) as user_cloture, journee ";
$requete_recup_id_journee.=" from Feuille_Caisse where ".$complement_requete1.$complement_requete2.$complement_requete3;
//echo $requete_recup_id_journee;
$db->setQuery($requete_recup_id_journee);
$resultat_recup_id_journee = $db->loadObject();
$recup_id_journee=$resultat_recup_id_journee->id_journee;
$journee=$resultat_recup_id_journee->journee;
$recup_montant_ouverture=$resultat_recup_id_journee->montant_ouverture;
$recup_montant_cloture=$resultat_recup_id_journee->montant_cloture;
$date_ouverture=$resultat_recup_id_journee->date_ouverture;
$heure_ouverture=$resultat_recup_id_journee->heure_ouverture;
$user_ouverture=$resultat_recup_id_journee->user_ouverture;
$user_cloture=$resultat_recup_id_journee->user_cloture;
$date_cloture=$resultat_recup_id_journee->date_cloture;
$heure_cloture=$resultat_recup_id_journee->heure_cloture;

if (!test_non_vide($recup_id_journee) and test_non_vide($_GET["journee"])){
    
    $date_ouverture=date("Y-m-d");
    $heure_ouverture=date("H:i");
    $requete_ouverture_caisse="INSERT INTO `Feuille_Caisse` (`id_user_ouverture`,`date_ouverture`, `heure_ouverture`, journee) ";
    $requete_ouverture_caisse.=" VALUES (".$user->id.", \"".date("Y-m-d")."\", \"".date("H:i")."\", \"".$_GET["journee"]."\")";
    //echo "req88: ".$requete_ouverture_caisse;
    $db->setQuery($requete_ouverture_caisse);	
    $db->query();
    $recup_id_journee=$db->insertid();

}

if (test_non_vide($recup_id_journee)){
     
    if (test_non_vide($recup_montant_ouverture))
        $Montant_debut=$recup_montant_ouverture;
    else {
        $requete_recup_liste="Select id, valeur from Monetaire order by id";
        //echo $requete_recup_liste;
        $db->setQuery($requete_recup_liste);
        $resultat_recup_liste = $db->loadObjectList();
        
        $Montant_debut=0;
        $test_si_saisie_qqchose["1"]="";
        
        foreach($resultat_recup_liste as $recup_liste){
            
            if (test_non_vide($_POST["Monetaire_1_".$recup_liste->id.""]) and !(VerifierEntier($_POST["Monetaire_1_".$recup_liste->id.""])))
                    $les_erreurs.="Debut de caisse : Le nbre du type ".$recup_liste->valeur." &#8364; est incorrect.<br>";
            else $Montant_debut=$Montant_debut+($recup_liste->valeur*$_POST["Monetaire_1_".$recup_liste->id.""]);
            
            $old_saisie["1"][$recup_liste->id]=$_POST["Monetaire_1_".$recup_liste->id.""];
            $test_si_saisie_qqchose["1"].=$_POST["Monetaire_1_".$recup_liste->id.""];
        }
        
    }
    if (test_non_vide($recup_montant_cloture))
        $Montant_fin=$recup_montant_cloture;
    else {
        $requete_recup_liste="Select id, valeur from Monetaire order by id";
        //echo $requete_recup_liste;
        $db->setQuery($requete_recup_liste);
        $resultat_recup_liste = $db->loadObjectList();
        
        $Montant_fin=0;
        $test_si_saisie_qqchose["0"]="";
        
        foreach($resultat_recup_liste as $recup_liste){
                 
            if (test_non_vide($_POST["Monetaire_0_".$recup_liste->id.""]) and !(VerifierEntier($_POST["Monetaire_0_".$recup_liste->id.""])))
                    $les_erreurs.="Fin de caisse : Le nbre du type ".$recup_liste->valeur." &#8364; est incorrect.<br>";
            else $Montant_fin=$Montant_fin+($recup_liste->valeur*$_POST["Monetaire_0_".$recup_liste->id.""]);
            
            $old_saisie["0"][$recup_liste->id]=$_POST["Monetaire_0_".$recup_liste->id.""];
            $test_si_saisie_qqchose["0"].=$_POST["Monetaire_0_".$recup_liste->id.""];
        }
        
    }
    
    if ((!(test_non_vide($Montant_debut)) or str_replace(",", ".",$Montant_debut)==0) and !(test_non_vide($_GET["ouverture"])))
        $les_erreurs.="Le montant de debut de caisse est obligatoire.<br>";
        
    if ( !test_non_vide($test_si_saisie_qqchose["0"]) and test_non_vide($_POST["Montant_fin"]) and (!(test_non_vide($Montant_fin)) or str_replace(",", ".",$Montant_fin)==0) )
        $les_erreurs.="Le montant de fin de caisse est obligatoire.<br>";
    
    echo "<font color=red><center>";
    if (test_non_vide($les_erreurs))
        echo $les_erreurs."<hr>";
    echo "</center></font>";
    
    
    $requete_recup_decompo_fin="select * from decompo_caisse where ouv1_ferm0=0 and id_journee=".$recup_id_journee;
    //echo $requete_recup_decompo_fin;
    $db->setQuery($requete_recup_decompo_fin);	
    $resultat_decompo_fin = $db->loadObject();
    
    if (test_non_vide($_POST["diff_caisse"]) and !test_non_vide($les_erreurs) and !test_non_vide($resultat_decompo_fin->id)){
        
        $requete_recup_liste="Select id, valeur from Monetaire order by id";
        //echo "req87: ".$requete_recup_liste."<br>";
        $db->setQuery($requete_recup_liste);
        $resultat_recup_liste = $db->loadObjectList();
        
        foreach($resultat_recup_liste as $recup_liste){
            
            if (test_non_vide($_POST["Monetaire_0_".$recup_liste->id.""]) and $_POST["Monetaire_0_".$recup_liste->id.""]!=0){
                $requete_ajout_decompo_caisse_fin="INSERT INTO `decompo_caisse`(`id_journee`, `id_monetaire`, `ouv1_ferm0`, `nbre`)";
                $requete_ajout_decompo_caisse_fin.=" VALUES (".$recup_id_journee.",".$recup_liste->id.",0,".$_POST["Monetaire_0_".$recup_liste->id.""].")";
                //echo "req89: ".$requete_ajout_decompo_caisse_fin;
                $db->setQuery($requete_ajout_decompo_caisse_fin);	
                $db->query();
            }
        }
    }
    
    $requete_recup_decompo_deb="select * from decompo_caisse where ouv1_ferm0=1 and id_journee=".$recup_id_journee;
    //echo $requete_recup_decompo_deb;
    $db->setQuery($requete_recup_decompo_deb);	
    $resultat_decompo_deb = $db->loadObject();
    
    if (( test_non_vide($_POST["diff_caisse"]) or test_non_vide($_POST["Enregistrer_montant_ouverture"])) and !test_non_vide($les_erreurs) and !test_non_vide($resultat_decompo_deb->id) ){

        $requete_recup_liste="Select id, valeur from Monetaire order by id";
        //echo "req87: ".$requete_recup_liste;
        $db->setQuery($requete_recup_liste);
        $resultat_recup_liste = $db->loadObjectList();
        
        foreach($resultat_recup_liste as $recup_liste){
            
            if (test_non_vide($_POST["Monetaire_1_".$recup_liste->id.""]) and $_POST["Monetaire_1_".$recup_liste->id.""]!=0){
                $requete_ajout_decompo_caisse_deb="INSERT INTO `decompo_caisse`(`id_journee`, `id_monetaire`, `ouv1_ferm0`, `nbre`) ";
                $requete_ajout_decompo_caisse_deb.=" VALUES (".$recup_id_journee.",".$recup_liste->id.",1,".$_POST["Monetaire_1_".$recup_liste->id.""].")";
                //echo "req88: ".$requete_ajout_decompo_caisse_deb;
                $db->setQuery($requete_ajout_decompo_caisse_deb);	
                $db->query();
            }
            
        }
        
        $requete_enregistrement_montant_debut="UPDATE `Feuille_Caisse` SET `montant_ouverture`=".$Montant_debut." ";
        $requete_enregistrement_montant_debut.="  where id_journee=".$recup_id_journee." ";
        //echo "req88: ".$requete_enregistrement_montant_debut;
        $db->setQuery($requete_enregistrement_montant_debut);	
        $db->query();

    }
    
    if (test_non_vide($_GET["journee"]))
        $journee=$_GET["journee"];
    
        
    $complement_date_requete=" and TIMESTAMPDIFF(MINUTE, CAST(concat(date_reglement,\" \",heure_reglement) AS CHAR(22)),";
    $complement_date_requete.=" CAST(concat(\"".$journee."\",\" \",\"08:00:00\") AS CHAR(22)))<0 ";
    $complement_date_requete.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(date_reglement,\" \",heure_reglement) AS CHAR(22)),";
    $complement_date_requete.=" CAST(concat(\"".decaler_jour($journee,1)."\",\" \",\"07:59:59\") AS CHAR(22)))>0 ";

    $complement_date_requete2=" and TIMESTAMPDIFF(MINUTE, CAST(concat(date_credit,\" \",heure_credit) AS CHAR(22)),";
    $complement_date_requete2.=" CAST(concat(\"".$journee."\",\" \",\"08:00:00\") AS CHAR(22)))<0 ";
    $complement_date_requete2.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(date_credit,\" \",heure_credit) AS CHAR(22)),";
    $complement_date_requete2.=" CAST(concat(\"".decaler_jour($journee,1)."\",\" \",\"07:59:59\") AS CHAR(22)))>0 ";
    
    $complement_date_requete3=" and TIMESTAMPDIFF(MINUTE, CAST(concat(date_creation,\" \",heure_creation) AS CHAR(22)),";
    $complement_date_requete3.=" CAST(concat(\"".$journee."\",\" \",\"08:00:00\") AS CHAR(22)))<0 ";
    $complement_date_requete3.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(date_creation,\" \",heure_creation) AS CHAR(22)),";
    $complement_date_requete3.=" CAST(concat(\"".decaler_jour($journee,1)."\",\" \",\"07:59:59\") AS CHAR(22)))>0 ";
    
    $bordure_tab=" style=\"border-collapse: collapse;\" ";
    $bordure_td=" style=\"border: 1px solid black;\" width=\"100\" align=center ";
    $bordure_th=$bordure_td." bgcolor=\"#CCCCCC\" ";
    ?>
    <form name="register_feuille_caisse" class="submission box" action="<?php echo JRoute::_( '../../index.php/caisse/today'); ?>" method="post"  >
    
    <table border="0"  width="700" height="800" CELLPADDING=5 CELLSPACING=0>
    
        <tr>
            <th <? echo $bordure_th;?> colspan=2><? echo "<input type=\"hidden\" name=\"journee\" value=\"".$journee."\">";
            echo "Journee : ".date_longue($journee); ?></th>
        </tr>  
        <tr>
            <td align="center" width="50%">
                <table border="1" width="100%" <? echo $bordure_tab; ?>>
                    <tr>
                        <th <? echo $bordure_th;?> colspan=2>Ouverture</th>
                    </tr>
                    <tr>
                        <? echo "<input type=\"hidden\" name=\"date_ouverture\" value=\"".$date_ouverture."\">"; ?>
                        <td <? echo $bordure_td;?> nowrap> <? echo date_longue($date_ouverture)." &agrave; ".$heure_ouverture." par ".$user_ouverture;?></td>
                    </tr>
                </table>
            </td>
            <td align="center" width="50%">
                <?
                if ((test_non_vide($_GET["modifier"])) or ((test_non_vide($Montant_debut)) and str_replace(",", ".",$Montant_debut)!=0)){
                ?>
                <table border="1" width="100%" <? echo $bordure_tab; ?>>
                    <tr>
                        <th <? echo $bordure_th;?> colspan=2>Fermeture</th>
                    </tr>
                    <tr>
                        <td <? echo $bordure_td;?> nowrap> <?
                        if (test_non_vide($date_cloture))
                            echo date_longue($date_cloture)." &agrave; ".$heure_cloture." par ".$user_cloture;
                        else echo "Journee non cloturee";
                        ?></td>
                    </tr>
                </table>
                 <?}?>
            </td>
           
        </tr>
        <tr>
            <td align="center" width="50%">
                <table border="1"  width="100%" <? echo $bordure_tab; ?>>
                    <tr>
                        <th colspan="3"  <? echo $bordure_th;?> >DEBUT DE CAISSE</th>
                    </tr>
                    <tr>
                        <th  <? echo $bordure_th;?> >TYPE</th>
                        <th  <? echo $bordure_th;?> >NBRE</th>
                        <th  <? echo $bordure_th;?> >TOTAL</th>
                    </tr> 
                        <?php liste_des_champs("Monetaire","1",test_non_vide($les_erreurs),$old_saisie,$recup_id_journee,$test_si_saisie_qqchose,$_GET["modifier"]);?>
                    <tr>
                        <th <? echo $bordure_th;?> colspan="2" >TOTAL</th><td <? echo $bordure_td;?> align=right>
                        <?
                            echo "<input type=\"hidden\" name=\"Montant_debut\" value=\"".$Montant_debut."\">".$Montant_debut." &#8364;";
                        ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td  align="center" width="50%"> 
                <?
                if ((test_non_vide($_GET["modifier"])) or ((test_non_vide($Montant_debut)) and str_replace(",", ".",$Montant_debut)!=0)){
                ?>
                <table border="1"  width="100%" <? echo $bordure_tab; ?> >
                    <tr>
                        <th colspan="3" <? echo $bordure_th ?> >FIN DE CAISSE</th>
                    </tr>
                    <tr>
                        <th  <? echo $bordure_th;?> >TYPE</th>
                        <th  <? echo $bordure_th;?> >NBRE</th>
                        <th  <? echo $bordure_th;?> >TOTAL</th>
                    </tr> 
                    <?php liste_des_champs("Monetaire","0",test_non_vide($les_erreurs),$old_saisie,$recup_id_journee,$test_si_saisie_qqchose,$_GET["modifier"]);?>
                    <tr>
                        <th  <? echo $bordure_th;?> colspan="2" >TOTAL</th><td <? echo $bordure_td;?> align=right>
                        <?
                            echo "<input type=\"hidden\" name=\"Montant_fin\" value=\"".$Montant_fin."\">".$Montant_fin." &#8364;";
                        ?>
                        </td>
                    </tr>
                </table>
                <?
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan=2>
		    <?
		    $titres_petit_tab="<th ".$bordure_th.">Type</th>";
		    $titres_petit_tab.="<th ".$bordure_th." >NBRE</th>";
		    $titres_petit_tab.="<th ".$bordure_th.">AVOIR</th>";
		    $titres_petit_tab.="<th ".$bordure_th." >CAUTION</th>";
		    $titres_petit_tab.="<th ".$bordure_th." >RESA</th>";
		    $titres_petit_tab.="<th ".$bordure_th." >LEDG</th>";
		    $titres_petit_tab.="<th ".$bordure_th." >TOTAL</th>";
		    ?>
                <table border="1"  width="100%" <? echo $bordure_tab; ?> >
                    <tr>
                        <th ROWSPAN="2" <? echo $bordure_th;?>>BON<br>ADMIN</th>

			<? echo $titres_petit_tab; ?>
		    </tr>
		    <tr>
			<th  <? echo $bordure_th;?> >ENC</th>
			<td <? echo $bordure_td;?>>
			<?
			$Nbre_ba=recup_infos_nbre_credit(6,$complement_date_requete2,"Versement")+recup_infos_nbre_reglements(6,$complement_date_requete)+recup_infos_nbre_reglements(6,$complement_date_requete,"",1);

			echo "<input type=\"hidden\" name=\"Nbre_ba\" value=\"".$Nbre_ba."\">";
                        echo $Nbre_ba;
			
                        ?>
			</td>
			<td <? echo $bordure_td;?>><?

			$Montant_ba_avoir=recup_infos_montant_credit(6,$complement_date_requete2,"Versement",1);
                    
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=6&detail=1&Type_credit=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_ba_avoir),2)." &#8364;";
			echo "</a>";
                        echo "<input type=\"hidden\" name=\"Montant_ba_avoir\" value=\"".$Montant_ba_avoir."\">";
			
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_ba_caution=recup_infos_montant_credit(6,$complement_date_requete2,"Versement",2);
                    
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=6&detail=1&Type_credit=2&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_ba_caution),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_ba_caution\" value=\"".$Montant_ba_caution."\">";
			
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_ba_resa=recup_infos_montant_reglements(6,$complement_date_requete);
                    
                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=6&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_ba_resa),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_ba_resa\" value=\"".$Montant_ba_resa."\">";
			
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_ba_ledg=recup_infos_montant_reglements(6,$complement_date_requete,"",1);
                    
                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=6&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_ba_ledg),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_ba_ledg\" value=\"".$Montant_ba_ledg."\">";
			
                        ?></td>
			<td <? echo $bordure_td;?>><?
			$Montant_ba=$Montant_ba_avoir;
			$Montant_ba+=$Montant_ba_caution;
			$Montant_ba+=$Montant_ba_resa;
			$Montant_ba+=$Montant_ba_ledg;
			
                        echo number_format(str_replace(",","",$Montant_ba),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Montant_ba\" value=\"".$Montant_ba."\">";
			
                        ?></td>
			
                    </tr>
                </table>
		</td>
	    </tr>
            <tr>
                <td colspan=2>
                <table border="1"  width="100%" <? echo $bordure_tab; ?> >
                    <tr>
                        <th ROWSPAN="4" <? echo $bordure_th;?>>CHEQUES</th>
                    
		    <? echo $titres_petit_tab; ?>
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?> >ENC</th>
			<td <? echo $bordure_td;?>><?
			
			$Nbre_cheques_paie=recup_infos_nbre_credit(4,$complement_date_requete2,"Versement","",">")+recup_infos_nbre_reglements(4,$complement_date_requete,">")+recup_infos_nbre_reglements(4,$complement_date_requete,">",1);
                            
                        echo $Nbre_cheques_paie;
                        
                        ?></td>
			<td <? echo $bordure_td;?> >
                        <?
			
			$Montant_Cheques_avoir=recup_infos_montant_credit(4,$complement_date_requete2,"Versement",1,">");
                    
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=4&Paie=1&detail=1&Type_credit=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_avoir),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_avoir\" value=\"".$Montant_Cheques_avoir."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
			
			$Montant_Cheques_caution=recup_infos_montant_credit(4,$complement_date_requete2,"Versement",2,">");
                    
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&&indic_valid=1&Moyen_paiement=4&Paie=1&detail=1&Type_credit=2&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_caution),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_caution\" value=\"".$Montant_Cheques_caution."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
			
			$Montant_Cheques_resa=recup_infos_montant_reglements(4,$complement_date_requete,">");
			
                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=4&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_resa),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_resa\" value=\"".$Montant_Cheques_resa."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
			
			$Montant_Cheques_ledg=recup_infos_montant_reglements(4,$complement_date_requete,">",1);
			
                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=4&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_ledg),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_ledg\" value=\"".$Montant_Cheques_ledg."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
                        $Paie_Cheque=$Montant_Cheques_avoir;
			$Paie_Cheque+=$Montant_Cheques_caution;
			$Paie_Cheque+=$Montant_Cheques_resa;
			$Paie_Cheque+=$Montant_Cheques_ledg;
                    
                        echo number_format(str_replace(",","",$Paie_Cheque),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Paie_Cheque\" value=\"".$Paie_Cheque."\">";
			
                        ?>
                        </td>
			
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?> >REMB</th>
			<td <? echo $bordure_td;?>><?
			
			$Nbre_cheques_remb=recup_infos_nbre_credit(4,$complement_date_requete2,"Remb.","","<")+recup_infos_nbre_reglements(4,$complement_date_requete,"<")+recup_infos_nbre_reglements(4,$complement_date_requete,"<",1);
                            
                        echo $Nbre_cheques_remb;
                        
                        ?></td>
			<td <? echo $bordure_td;?> >
                        <?
                        $Montant_Cheques_remb_avoir=recup_infos_montant_credit(4,$complement_date_requete2,"Remb.",1,"<");
                    
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=4&Remb=1&detail=1&Type_credit=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_remb_avoir),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_remb_avoir\" value=\"".$Montant_Cheques_remb_avoir."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
                        $Montant_Cheques_remb_caution=recup_infos_montant_credit(4,$complement_date_requete2,"Remb.",2,"<");
                    
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&&indic_valid=1&Moyen_paiement=4&Remb=1&detail=1&Type_credit=2&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_remb_caution),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_remb_caution\" value=\"".$Montant_Cheques_remb_caution."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
			$Montant_Cheques_remb_resa=recup_infos_montant_reglements(4,$complement_date_requete,"<");
                    
                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=4&Remb=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_remb_resa),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_remb_resa\" value=\"".$Montant_Cheques_remb_resa."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
			$Montant_Cheques_remb_ledg=recup_infos_montant_reglements(4,$complement_date_requete,"<",1);
                    
                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=4&Remb=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_Cheques_remb_ledg),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_Cheques_remb_ledg\" value=\"".$Montant_Cheques_remb_ledg."\">";
                        ?>
                        </td>
			<td <? echo $bordure_td;?> >
                        <?
                        $Remb_Cheque=$Montant_Cheques_remb_avoir;
			$Remb_Cheque+=$Montant_Cheques_remb_caution;
			$Remb_Cheque+=$Montant_Cheques_remb_resa;
			$Remb_Cheque+=$Montant_Cheques_remb_ledg;
			
                    
                        echo number_format(str_replace(",","",$Remb_Cheque),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Remb_Cheque\" value=\"".$Remb_Cheque."\">";
                        ?>
                        </td>
			
                    </tr>
                    <tr>
                        <th  <? echo $bordure_th;?> colspan=6>TOTAL</th><td <? echo $bordure_td;?>>
                        <?
                        $Montant_Cheques=$Remb_Cheque+$Paie_Cheque;
                    
                        echo number_format(str_replace(",","",$Montant_Cheques),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Montant_Cheques\" value=\"".$Montant_Cheques."\">";
			echo "<input type=\"hidden\" name=\"Nbre_cheques\" value=\"".($Nbre_cheques_paie+$Nbre_cheques_remb)."\">";
                        ?>
                        </td>
                    </tr>
                </table>
		</td>
	    </tr>
            <tr>
                <td colspan=2>
                <table border="1"  width="100%" <? echo $bordure_tab; ?> >
                    <tr>
                        <th ROWSPAN="5"  <? echo $bordure_th;?> >CB</th>
                    
		    <? echo $titres_petit_tab; ?>
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?> >ENC TPE</th><td <? echo $bordure_td;?>><?

			$Nbre_CB_paie=recup_infos_nbre_credit(1,$complement_date_requete2,"Versement","",">")+recup_infos_nbre_reglements(1,$complement_date_requete,">")+recup_infos_nbre_reglements(1,$complement_date_requete,">",1);
                            
                        echo $Nbre_CB_paie;

                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_avoir=recup_infos_montant_credit(1,$complement_date_requete2,"Versement",1,">");
                        
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=1&Paie=1&detail=1&Type_credit=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_avoir),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_avoir\" value=\"".$Montant_CB_avoir."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_caution=recup_infos_montant_credit(1,$complement_date_requete2,"Versement",2,">");
                        
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&&indic_valid=1&Moyen_paiement=1&Paie=1&detail=1&Type_credit=2&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_caution),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_caution\" value=\"".$Montant_CB_caution."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_resa=recup_infos_montant_reglements(1,$complement_date_requete,">");
                        
                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=1&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_resa),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_resa\" value=\"".$Montant_CB_resa."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_ledg=recup_infos_montant_reglements(1,$complement_date_requete,">",1);
                        
                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=1&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_ledg),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_ledg\" value=\"".$Montant_CB_ledg."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
                        $Paie_CB=$Montant_CB_avoir;
			$Paie_CB+=$Montant_CB_caution;
			$Paie_CB+=$Montant_CB_resa;
			$Paie_CB+=$Montant_CB_ledg;
                        
                        echo number_format(str_replace(",","",$Paie_CB),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Paie_CB\" value=\"".$Paie_CB."\">";
                        
                        ?></td>
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?> ><a href="https://www.cmcicpaiement.fr/fr/client/Paiement/Paiement_PaiementsJour.aspx?tpe_id=0337424:PI&date=<?php echo inverser_date($journee);  ?>" target="_blank" >ENC Web</a></th><td <? echo $bordure_td;?>><?

			$Nbre_CB_paie_web=recup_infos_nbre_credit(8,$complement_date_requete2,"Versement","",">")+recup_infos_nbre_reglements(8,$complement_date_requete,">")+recup_infos_nbre_reglements(8,$complement_date_requete,">",1);
                            
                        echo $Nbre_CB_paie_web;

                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_avoir_web=recup_infos_montant_credit(8,$complement_date_requete2,"Versement",1,">");
                        
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=8&Paie=1&detail=1&Type_credit=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_avoir_web),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_avoir_web\" value=\"".$Montant_CB_avoir_web."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_caution_web=recup_infos_montant_credit(8,$complement_date_requete2,"Versement",2,">");
                        
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&&indic_valid=1&Moyen_paiement=8&Paie=1&detail=1&Type_credit=2&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_caution_web),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_caution_web\" value=\"".$Montant_CB_caution_web."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_resa_web=recup_infos_montant_reglements(8,$complement_date_requete,">");
                        
                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=8&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_resa_web),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_resa_web\" value=\"".$Montant_CB_resa_web."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Montant_CB_ledg_web=recup_infos_montant_reglements(8,$complement_date_requete,">",1);
                        
                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=8&Paie=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Montant_CB_ledg_web),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Montant_CB_ledg_web\" value=\"".$Montant_CB_ledg_web."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
                        $Paie_CB_web=$Montant_CB_avoir_web;
			$Paie_CB_web+=$Montant_CB_caution_web;
			$Paie_CB_web+=$Montant_CB_resa_web;
                        $Paie_CB_web+=$Montant_CB_ledg_web;
			
                        echo number_format(str_replace(",","",$Paie_CB_web),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Paie_CB_web\" value=\"".$Paie_CB_web."\">";
			echo "<input type=\"hidden\" name=\"Nbre_CB_web\" value=\"".$Nbre_CB_paie_web."\">";
                        
                        ?></td>
                    </tr>
                    <tr>
                        <th  <? echo $bordure_th;?> >REMB</th>
			<td <? echo $bordure_td;?>><?
			
			$Nbre_CB_remb=recup_infos_nbre_credit(1,$complement_date_requete2,"Remb.","","<")+recup_infos_nbre_reglements(1,$complement_date_requete,"<")+recup_infos_nbre_reglements(1,$complement_date_requete,"<",1);
                            
                        echo $Nbre_CB_remb;
			
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Remb_CB_avoir=recup_infos_montant_credit(1,$complement_date_requete2,"Remb.",1,"<");
                        
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=1&Remb=1&detail=1&Type_credit=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Remb_CB_avoir),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Remb_CB_avoir\" value=\"".$Remb_CB_avoir."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
                        $Remb_CB_caution=recup_infos_montant_credit(1,$complement_date_requete2,"Remb.",2,"<");
                        
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&Moyen_paiement=1&Remb=1&detail=1&Type_credit=2&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Remb_CB_caution),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Remb_CB_caution\" value=\"".$Remb_CB_caution."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Remb_CB_resa=recup_infos_montant_reglements(1,$complement_date_requete,"<");

                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=1&Remb=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Remb_CB_resa),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Remb_CB_resa\" value=\"".$Remb_CB_resa."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
			
			$Remb_CB_ledg=recup_infos_montant_reglements(1,$complement_date_requete,"<",1);

                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=1&Remb=1&date_deb=".$journee."\">";
			echo number_format(str_replace(",","",$Remb_CB_ledg),2)." &#8364;";
                        echo "</a>";
			echo "<input type=\"hidden\" name=\"Remb_CB_ledg\" value=\"".$Remb_CB_ledg."\">";
                        
                        ?></td>
			<td <? echo $bordure_td;?>><?
                        $Remb_CB=$Remb_CB_avoir;
			$Remb_CB+=$Remb_CB_caution;
			$Remb_CB+=$Remb_CB_resa;
			$Remb_CB+=$Remb_CB_ledg;
                        
                        echo number_format(str_replace(",","",$Remb_CB),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Remb_CB\" value=\"".$Remb_CB."\">";
                        
                        ?></td>
                    </tr>
                    <tr>
                        <th  <? echo $bordure_th;?> colspan=6 >TOTAL</th>
			<td <? echo $bordure_td;?>><?
                        $Montant_CB=$Remb_CB+$Paie_CB;
			$Montant_CB_web=$Paie_CB_web;
                        echo number_format(str_replace(",","",$Montant_CB),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Montant_CB\" value=\"".$Montant_CB."\">";
			echo "<input type=\"hidden\" name=\"Montant_CB_web\" value=\"".$Montant_CB_web."\">";
			
			echo "<input type=\"hidden\" name=\"Nbre_CB\" value=\"".($Nbre_CB_remb+$Nbre_CB_paie)."\">";
                        
                        ?></td>
                    </tr>
                </table>
		</td>
	    </tr>
            <tr>
                <td colspan=2>
                <table border="1"  width="100%" <? echo $bordure_tab; ?> >
                    <tr>
                        <th ROWSPAN="4"  <? echo $bordure_th;?> >ESPECES</th>
                   
		    <? echo $titres_petit_tab; ?>
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?> >ENC</th><td <? echo $bordure_td;?>>
			<?
			$Nbre_esp_paie=recup_infos_nbre_credit(3,$complement_date_requete2,"Versement","",">")+recup_infos_nbre_reglements(3,$complement_date_requete,">")+recup_infos_nbre_reglements(3,$complement_date_requete,">",1);
                            
                        echo $Nbre_esp_paie;
			?>
			</td>
			<td <? echo $bordure_td;?> nowrap><?
			
			$Montant_Espece_avoir=recup_infos_montant_credit(3,$complement_date_requete2,"Versement",1,">");
			
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&detail=1&Type_credit=1&Moyen_paiement=3&Paie=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_avoir),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_avoir\" value=\"".$Montant_Espece_avoir."\">";
                        
			?></td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Montant_Espece_caution=recup_infos_montant_credit(3,$complement_date_requete2,"Versement",2,">");
			
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&detail=1&Type_credit=2&Moyen_paiement=3&Paie=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_caution),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_caution\" value=\"".$Montant_Espece_caution."\">";
                        
			?></td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Montant_Espece_resa=recup_infos_montant_reglements(3,$complement_date_requete,">");
			
                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=3&Paie=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_resa),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_resa\" value=\"".$Montant_Espece_resa."\">";
                        
			?></td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Montant_Espece_ledg=recup_infos_montant_reglements(3,$complement_date_requete,">",1);
			
                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=3&Paie=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_ledg),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_ledg\" value=\"".$Montant_Espece_ledg."\">";
                        
			?></td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Paie_especes=$Montant_Espece_avoir;
			$Paie_especes+=$Montant_Espece_caution;
			$Paie_especes+=$Montant_Espece_resa;
			$Paie_especes+=$Montant_Espece_ledg;
			
			echo number_format(str_replace(",","",$Paie_especes),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Paie_especes\" value=\"".$Paie_especes."\">";
			
			?></td>
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?> >REMB</th><td <? echo $bordure_td;?>>
			<?
			$Nbre_esp_remb=recup_infos_nbre_credit(3,$complement_date_requete2,"Remb.","","<")+recup_infos_nbre_reglements(3,$complement_date_requete,"<")+recup_infos_nbre_reglements(3,$complement_date_requete,"<",1);
                            
                        echo $Nbre_esp_remb;
			?>
			</td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Montant_Espece_remb_avoir=recup_infos_montant_credit(3,$complement_date_requete2,"Remb.",1,"<");
			
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&detail=1&Type_credit=1&Moyen_paiement=3&Remb=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_remb_avoir),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_remb_avoir\" value=\"".$Montant_Espece_remb_avoir."\">";
                        
			?></td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Montant_Espece_remb_caution=recup_infos_montant_credit(3,$complement_date_requete2,"Remb.",2,"<");
			
                        echo "<a href=\"index.php?option=com_content&view=article&id=77&indic_valid=1&detail=1&Type_credit=2&Moyen_paiement=3&Remb=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_remb_caution),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_remb_caution\" value=\"".$Montant_Espece_remb_caution."\">";
                        
			?></td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Montant_Espece_remb_resa=recup_infos_montant_reglements(3,$complement_date_requete,"<");
			
                        echo "<a href=\"index.php/component/content/article?id=80&indic_valid=1&Moyen_paiement=3&Remb=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_remb_resa),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_remb_resa\" value=\"".$Montant_Espece_remb_resa."\">";
                        
			?></td>
			</td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Montant_Espece_remb_ledg=recup_infos_montant_reglements(3,$complement_date_requete,"<",1);
			
                        echo "<a target=\"_blank\" href=\"http://footinfive.com/LEDG/index.php/accueil/gestion-des-reglements?indic_valid=1&Moyen_paiement=3&Remb=1&date_deb=".$journee."\">";
                        echo str_replace(",","",number_format(str_replace(",","",$Montant_Espece_remb_ledg),2))." &#8364;</a>";
                        echo "<input type=\"hidden\" name=\"Montant_Espece_remb_ledg\" value=\"".$Montant_Espece_remb_ledg."\">";
                        
			?></td>
			<td <? echo $bordure_td;?> nowrap><?
                        $Remb_especes=$Montant_Espece_remb_avoir;
			$Remb_especes+=$Montant_Espece_remb_caution;
			$Remb_especes+=$Montant_Espece_remb_resa;
			$Remb_especes+=$Montant_Espece_remb_ledg;
			
			echo number_format(str_replace(",","",$Remb_especes),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Remb_especes\" value=\"".$Remb_especes."\">";
			
			?></td>
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?> colspan="6" >TOTAL</th>
			<td <? echo $bordure_td;?>><?
                        $Montant_Espece=$Remb_especes+$Paie_especes;
                        echo number_format(str_replace(",","",$Montant_Espece),2)." &#8364;";
                        echo "<input type=\"hidden\" name=\"Montant_Espece\" value=\"".$Montant_Espece."\">";
                        ?></td>
                    </tr>
                    <tr>
                        <th  <? echo $bordure_th;?>  colspan="7"  >DIFF CAISSE</th><td <? echo $bordure_td;?>><?
                        if (test_non_vide($Montant_debut) and test_non_vide($Montant_fin) and (!test_non_vide($les_erreurs))){
                            
                            //echo $recup_infos_especes->total_especes."+".$_POST["Montant_debut"]."-".$_POST["Montant_fin"]."=";
                            $diff=(str_replace(",",".",$Montant_fin)-str_replace(",",".",$Montant_debut)-$Montant_Espece);
                            $diff=str_replace(",","",number_format(str_replace(",","",$diff),2));
                            
                            echo "<input type=\"hidden\" name=\"Diff_Caisse\" value=\"".$diff."\">";
                            echo $diff." &#8364;";
                        }
                        ?></td>
                    </tr>
                    <tr>
                        <th  <? echo $bordure_th;?>  colspan="7"  >SOLDE TOTAL</th><td <? echo $bordure_td;?> ><?
                        if (test_non_vide($Montant_debut) and test_non_vide($Montant_fin) and (!test_non_vide($les_erreurs))){
                            
                            $solde_total=$Montant_Espece;
			    $solde_total+=$Montant_CB;
			    $solde_total+=$Montant_CB_web;
			    $solde_total+=$Montant_Cheques;
			    $solde_total+=$Montant_ba;
			    $solde_total+=$diff;
			    $solde_total=str_replace(",","",number_format(str_replace(",","",$solde_total),2));
                            
                            echo "<input type=\"hidden\" name=\"solde_total\" value=\"".$solde_total."\">";
                            
                            echo $solde_total." &#8364;";
                        }
                        ?></td>
                    </tr>
		    <tr>
                        <th  <? echo $bordure_th;?>  colspan="7"  >REMISES ACCORDEES</th><td <? echo $bordure_td;?> ><?
			    echo "<a href=\"index.php/component/content/article?id=80&remise=1&date_deb=".$journee."\"/>".remises_accordees($complement_date_requete)." &#8364;</a>";
                        ?></td>
                    </tr>
		    <tr>
			<th  <? echo $bordure_th;?>  colspan="7"  >PRESTATIONS A <?
			if ($date_ouverture<"2014-01-01"){
			    $type_tva=2;
			    echo "7";
			}
			else {
			    $type_tva=4;
			    echo "10";
			}
			?>%</th><td <? echo $bordure_td;?> ><?
			    echo montant_total_presta("",$complement_date_requete3,$type_tva)." &#8364;";
                        ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <center>
    <?
    if (!test_non_vide($_GET["vue"])){
        if (test_non_vide($recup_id_journee) and $Montant_debut==0 and !test_non_vide($_GET["modifier"])){
            echo "<input type=\"hidden\" name=\"Enregistrer_montant_ouverture\" value=\"1\" >";
        ?>
           <input name="Ouverture" type="button" value="Calculer le montant ouverture" onclick="recharger('Calculer le montant douverture ?','')">
        <?        
        }
        else {
            if (!(test_non_vide($date_cloture)) and test_non_vide($diff) and !test_non_vide($_GET["modifier"])){
                echo "<input type=\"hidden\" name=\"Cloturer\" value=\"".$recup_id_journee."\">";
        ?>
                <input name="Modif" type="button" value="Modifier" onclick="recharger('Modifier cette feuille de caisse ?','../../index.php/caisse/today?modifier=<? echo $recup_id_journee."&id_journee=".$recup_id_journee;?>')">
                <input name="Cloture" type="button" value="Cloturer la caisse" onclick="recharger('Vous etes sur le point de cloturer la caisse','')">
        <? }
            else {
                if (!test_non_vide($diff) or test_non_vide($_GET["modifier"])){
                    echo "<input type=\"hidden\" name=\"diff_caisse\" value=\"1\">";
                    if (test_non_vide($_GET["modifier"]))
                        echo "<input type=\"hidden\" name=\"modifier\" value=\"".$_GET["modifier"]."\">";
        ?>
        <input name="calcul" type="button" value="Calculer la difference de caisse" onclick="recharger('Confirmez votre saisie','')">
        <?}
        }
        }
    }?>
    </center>
    </form>
    <?
}
else {
    
    if (!test_non_vide($_POST["Cloturer"])){
        $requete_recup_journee_today="SELECT date_cloture from Feuille_Caisse where date_ouverture=\"".date("Y-m-d")."\"";
        //echo $requete_recup_journee_today;
        $db->setQuery($requete_recup_journee_today);
        $resultat_recup_journee_today = $db->loadObject();
            
        if (test_non_vide($resultat_recup_journee_today->date_cloture) and !is_null($resultat_recup_journee_today->date_cloture))
            echo "La caisse d'aujourd'hui est cloturee.";
        else {    
            echo "La caisse d'aujourd'hui n'est pas ouverte.<br><br>Avez-vous le detail des pieces et billets pour ouvrir la caisse ?";
            echo "<a href=\"index.php/caisse/today?journee=".date("Y-m-d")."\"> oui</a> - ";
            echo "<a href=\"index.php/caisse/toutes\">non</a>";
        }
    }
}
}
}
?>













