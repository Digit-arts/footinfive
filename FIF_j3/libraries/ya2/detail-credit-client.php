<?php

require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');
?>
<script type="text/javascript">
	function valider() {
		document.date_form_0.submit()
	}
	
	function recharger(texte_a_afficher,lien) {
			if (texte_a_afficher!=''){
				if (confirm(texte_a_afficher)){
					if (lien!='') document.location.href=lien;
					else document.register_credit.submit();
				}
			}
			else {
				if (lien!='') document.location.href=lien;
				else {
					document.register_credit.Montant.value='';
					document.register_credit.submit();
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

if (test_non_vide($_GET["ok"]) and $_GET["ok"]==1) 
	echo "<font color=red>La transaction est valid&eacute;e.<br><br></font>";
else {
	if (test_non_vide($_GET["ok"]) and ($_GET["ok"]==0 or $_GET["ok"]=="")) 
		echo "<font color=red>La transaction a echou&eacute;e.<br><br></font>";
}	


//if (test_non_vide($_POST["id_client"])) $id_client = $_POST["id_client"];
if (est_min_agent($user)){
	if (test_non_vide($_POST["id_client"])) $id_client=$_POST["id_client"];
	else $id_client=$_GET["id_client"];
} else $id_client=idclient_du_user();

$titre="Les cr&eacute;dits";

if (!test_non_vide($id_client))
	$titre.=" du jour";

if (test_non_vide($id_client))
	$titre.=" du client";

if (test_non_vide($id_client))
	if ((recup_caution_total_client($id_client)<0 or recup_credit_total_client($id_client)<0) and $id_client<>4590){ // il faut trouver pourquoi il bug le $id_client 4590
		sendMail(267,"alerte caution ou avoir negatif","Client : <a href=\"http://www.footinfive.com/FIF/index.php/component/content/article?id=60&id_client=".$id_client."\"/>".$id_client."</a>");
		sendMail(1,"alerte caution ou avoir negatif","Client : <a href=\"http://www.footinfive.com/FIF/index.php/component/content/article?id=60&id_client=".$id_client."\"/>".$id_client."</a>");
	}

	
	
menu_acces_rapide($id_client,$titre);


if (est_min_manager($user) and test_non_vide($_GET["id_cred"]))
	supprimer_erreurs_saisie_credit($_GET["id_cred"]);

if (test_non_vide($_POST["nom"])) $nom=$_POST["nom"];
else $nom=$_GET["nom"];

if (test_non_vide($_POST["prenom"])) $prenom=$_POST["prenom"];
else $prenom=$_GET["prenom"];

if (test_non_vide($_POST["equipe"])) $equipe=$_POST["equipe"];
else $equipe=$_GET["equipe"];

if (test_non_vide($_POST["Moyen_paiement"])) $Moyen_paiement=$_POST["Moyen_paiement"];
else $Moyen_paiement=$_GET["Moyen_paiement"];

if (test_non_vide($_POST["Type_credit"])) $Type_credit=$_POST["Type_credit"];
else $Type_credit=$_GET["Type_credit"];

if (test_non_vide($_POST["info_credit"])) $info_credit=$_POST["info_credit"];
else $info_credit=$_GET["info_credit"];

if (test_non_vide($_POST["indic_annul"])) $indic_annul=$_POST["indic_annul"];
else $indic_annul=$_GET["indic_annul"];

if (test_non_vide($_POST["indic_valid"])) $indic_valid=$_POST["indic_valid"];
else $indic_valid=$_GET["indic_valid"];

if (test_non_vide($_POST["date_fin"])) $date_fin=$_POST["date_fin"];
else $date_fin=$_GET["date_fin"];

if (test_non_vide($_POST["date_deb"])) $date_deb=$_POST["date_deb"];
else {
	$temp=$id_resa.$id_client.$nom.$prenom.$equipe.$Moyen_paiement.$indic_annul.$indic_valid.$Type_credit.$info_credit;
	if (test_non_vide($_GET["date_deb"])) $date_deb=$_GET["date_deb"];
	else if ($temp=="") 
		header("Location: ../index.php?option=com_content&view=article&id=77&date_deb=".date("Y-m-d")."");
}


if (test_non_vide($id_client)){
if ( !(test_non_vide($_POST["Montant"])) or str_replace(",", ".",$_POST["Montant"])==0 )
		$les_erreurs.="Le montant est obligatoire.<br>";
	else {
		if (test_non_vide($_POST["Montant"]) and !(VerifierMonetaire($_POST["Montant"])))
			$les_erreurs.="Le Montant saisie est incorrect.<br>";
		else {
			if ( !(test_non_vide($_POST["Moyen_paiement"])))
					$les_erreurs.="Le moyen de paiement est obligatoire.<br>";
			////dans le cas où le client veut une deduction d'acompte plus importante que ce qu'il a versé
			if ($_POST["type"]==3){
				if ($_POST["Type_credit"]==1){
					if ($_POST["total_credit_actuel"]<str_replace(",", ".",$_POST["Montant"]))
							$les_erreurs.="Le montant que vous souhaitez rembourser est superieur au total des avoirs.<br>";
					else $Montant_a_recrediter=($_POST["total_credit_actuel"]-str_replace(",", ".",$_POST["Montant"]));
				}
				if ($_POST["Type_credit"]==2){
					if ($_POST["total_caution_actuel"]<str_replace(",", ".",$_POST["Montant"]))
							$les_erreurs.="Le montant que vous souhaitez rembourser est superieur au total des cautions.<br>";
					else $Montant_a_recrediter=($_POST["total_caution_actuel"]-str_replace(",", ".",$_POST["Montant"]));
				}
			}
			if (est_agent($user) or est_register($user)){
				if (($_POST["type"]<>3) and (1500<(str_replace(",", ".",$_POST["Montant"])+$_POST["total_credit_actuel"])))
					$les_erreurs.="Le montant saisie + le credit actuel est superieur au maximum autoris&eacute; (1500€).<br>";
				if (($_POST["type"]<>3) and (1>str_replace(",", ".",$_POST["Montant"])))
					$les_erreurs.="Le Montant saisie est inferieur au minimum autoris&eacute; (1€).<br>";
			}
		}
	}

if ((!test_non_vide($les_erreurs)) and test_non_vide($id_client) and test_non_vide($_POST["Type_credit"])){
		
		if ($_POST["Moyen_paiement"]==8){
			$id_cred=ajout_credit($id_client,$_POST["Montant"],$_POST["Type_credit"],"Versement",$_POST["Moyen_paiement"],0,$info_credit);
			$requete_marq_client="UPDATE Client set page_retour=\"".$user->id."#\" where id_client=".$id_client;
			$db->setQuery($requete_marq_client);	
			$db->query();
				
			header("Location: libraries/ya2/CMCIC_Paiement_3_0i/Phase1Aller.php?Montant=".str_replace(",",".",$_POST["Montant"])."&ref=C".$id_cred."");
		}
		else {
			if ($_POST["Type_credit"]==2 ){
				if ($_POST["type"]==1 and $_POST["Type_credit"]==2 and $_POST["Moyen_paiement"]<>2){
					ajout_credit($id_client,$_POST["Montant"],$_POST["Type_credit"],"Versement",$_POST["Moyen_paiement"],1,$info_credit);
					if (est_min_agent($user)) 
						maj_cautionnement_des_resas($id_client,1);
				}	
			}

			if ($_POST["type"]==3 and isset($Montant_a_recrediter) and $Montant_a_recrediter>=0){
				$maj_Montant=(-1*str_replace(",", ".",$_POST["Montant"]));
				//sauvegarder les lignes credit avant 
				ajout_credit($id_client,$maj_Montant,$_POST["Type_credit"],"Remb.",$_POST["Moyen_paiement"],1,$info_credit);
									
				if ($_POST["Type_credit"]==2 and est_min_agent($user))
					maj_cautionnement_des_resas($id_client,1);
			}
			if ($_POST["type"]==1 and $_POST["Type_credit"]==1)
				ajout_credit($id_client,$_POST["Montant"],$_POST["Type_credit"],"Versement",$_POST["Moyen_paiement"],1,$info_credit);
		}
}			
}

if (test_non_vide($id_client)) $complement_req=" and c.`id_client`=".$id_client;

$requete_liste_cred="select (select ugm.group_id from #__user_usergroup_map as ugm where ugm.user_id=cc.id_user_creation) as gid_crediteur,";
$requete_liste_cred.=" (select name from #__users where id=cc.id_user_creation) as crediteur, cc.*,c.*, m.nom as moy_paie, ";
$requete_liste_cred.=" (select nom from Type_credit where id=cc.type_credit) as nom_type_credit from  Client as c, ";
$requete_liste_cred.=" Credit_client as cc left join Moyen_paiement as m on cc.id_moyen_paiement=m.id  where cc.id_client=c.id_client";
$requete_liste_cred.=$complement_req;

if (test_non_vide($date_deb)) {
	$requete_liste_cred.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(cc.date_credit,\" \",cc.heure_credit) AS CHAR(22)),";
	$requete_liste_cred.=" CAST(concat(\"".$date_deb."\",\" \",\"08:00:00\") AS CHAR(22)))<0 ";
	$requete_liste_cred.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(cc.date_credit,\" \",cc.heure_credit) AS CHAR(22)),CAST(concat(\"";
		
	if (test_non_vide($date_fin))
		$requete_liste_cred.=$date_fin;
	else
		$requete_liste_cred.=decaler_jour($date_deb,1);
		
	$requete_liste_cred.="\",\" \",\"05:00:00\") AS CHAR(22)))>0 ";
}
else {
	if (est_register($user)) 
		$requete_liste_cred.=" and cc.date_credit>=\"".date("Y-m-d")."\"";
}

if (test_non_vide($nom)) $requete_liste_cred.=" and c.nom like \"%".$nom."%\"";
if (test_non_vide($prenom)) $requete_liste_cred.=" and c.prenom like \"%".$prenom."%\"";
if (test_non_vide($equipe)) $requete_liste_cred.=" and c.equipe like \"%".$equipe."%\"";
if (test_non_vide($id_client)) $requete_liste_cred.=" and c.id_client=".$id_client;
if (test_non_vide($Moyen_paiement)) $requete_liste_cred.=" and cc.id_moyen_paiement=".$Moyen_paiement;
if (test_non_vide($Type_credit)) $requete_liste_cred.=" and cc.type_credit=".$Type_credit;
if (test_non_vide($indic_annul))
	$requete_liste_cred.=" and cc.validation_credit=".$indic_annul;
	
if (test_non_vide($indic_valid))
	$requete_liste_cred.=" and cc.validation_credit=".$indic_valid;
	
if (test_non_vide($_GET["Remb"]))
	$requete_liste_cred.=" and cc.credit<0  and cc.origine_credit=\"Remb.\" ";
	
if (test_non_vide($_GET["Paie"]))
	$requete_liste_cred.=" and cc.credit>0  and cc.origine_credit=\"Versement\" ";

$requete_liste_cred.=" order by cc.date_credit desc, cc.heure_credit desc";
//echo $requete_liste_cred;

$db->setQuery($requete_liste_cred);	
$resultat_liste_cred= $db->loadObjectList();
	?>
	
	<FORM id="formulaire" name="date_form_0" class="submission box" action="article?id=77" method="post" >

	<table width="100%">
		<tr>
	<? 
			if (test_non_vide($id_client)){
				if (est_min_agent($user)) $compl_req=" id_client=".$id_client." ";
				else $compl_req=" id_user=".$user->id." ";
				
				$requete_recup_client="select id_client, prenom, nom, code_insee from Client where ".$compl_req." order by nom,prenom,code_insee";
				$db->setQuery($requete_recup_client);	
				$resultat_recup_client = $db->loadObjectList();
									
				foreach($resultat_recup_client as $recup_client){
					$nom=$recup_client->nom;
					$prenom=$recup_client->prenom;		
				}
				
			}			
			
		if (est_min_agent($user)){?>
			<td><input name="id_client" type="text"  value="<? echo $id_client;?>" size="5"   placeholder="Num client"></td>
			<td><input name="nom" type="text"  value="<? echo $nom;?>" size="8"   placeholder="nom"></td>
			<td><input name="prenom" type="text"  value="<? echo $prenom;?>" size="8"   placeholder="prenom"></td>
		<?}?><td>
				
			<INPUT type="checkbox" name="indic_annul" value="0" 
			<? if (test_non_vide($indic_annul)) echo "checked"; ?>>
				<img src="images/Cancel-resa.png" title="reglement supprim&eacute;e"/>
				<INPUT type="checkbox" name="indic_valid" value="1" 
			<? if (test_non_vide($indic_valid)) echo "checked"; ?>>
				<img src="images/reglement_valide.png" title="reglement valid&eacute;e"/>
			</td>
			<td ><? menu_deroulant("Type_credit",$Type_credit); ?></td>
		</tr>
		<tr>
			<td nowrap><input type="date" name="date_deb" value="<? echo $date_deb;?>"></td>
			<td nowrap><input type="date" name="date_fin" value="<? echo $date_fin;?>"></td>
			<td ><? menu_deroulant("Moyen_paiement",$Moyen_paiement); ?></td>
			<td><input name="equipe" type="text"  value="<? echo $equipe;?>" size="7"  placeholder="Equipe"></td>
			
		</tr>
	<?if (est_min_agent($user)){?>
		
		<tr>
	<?}?>
			<td align="center" colspan=9>
				<input name="valide" type="button"  value="Filtrer" onclick="valider()">
			</td>
		</tr>
	</table>
	</FORM>
	<hr>
<?

if (!$resultat_liste_cred) echo $prb;
else {

	$details="<table class=\"zebra\"><tr>";
	if (est_min_agent($user)) $details.="<th>Num client</th><th>Client</th>";
	$details.="<th>Date cr&eacute;dit</th><th>Heure cr&eacute;dit</th><th>Montant cr&eacute;dit&eacute;</th>";
	$details.="<th>Moyen paiement</th><th>Origine cr&eacute;dit</th><th>Info</th><th>Type credit</th><th>Effectu&eacute; par</th><th>Suppr</th></tr>";
	$total_versements_page=0;
	$nbre_operations=0;
	foreach($resultat_liste_cred as $liste_cred){
		if  ($liste_cred->validation_credit==1)
			$nbre_operations++;
		$details.="<tr>";
		if (est_min_agent($user)) {
			$details.="<td>".$liste_cred->id_client."</td>";
			$details.="<td><a href=\"index.php/component/content/article?id=60&id_client=".$liste_cred->id_client."\"/>";
			if ($liste_cred->societe=="") {
				if ($liste_cred->equipe==""){
					$details.=$liste_cred->nom." ".$liste_cred->prenom;
				}
				else $details.=$liste_cred->equipe;
			}
			else $details.=$liste_cred->societe;
			$details.="</a></td>";
		}
		$details.="<td>";
		$details.=inverser_date($liste_cred->date_credit)."</td><td>";
		$details.=$liste_cred->heure_credit."</td><td>";
		$details.=str_replace(".", ",",$liste_cred->credit)."</td><td>";
		if  ($liste_cred->validation_credit==1)
			$total_versements_page+=$liste_cred->credit;
		$details.=$liste_cred->moy_paie."</td><td>";
		$details.=$liste_cred->origine_credit."</td><td>";
		if (test_non_vide($liste_cred->info_credit))
			$details.="<img src=\"images/info-icon.png\" title=\"".$liste_cred->info_credit."\">";
		$details.="</td><td>";
		$details.=$liste_cred->nom_type_credit."</td>";
		$details.="<td>".$liste_cred->crediteur;
		if (est_min_agent($liste_cred->gid_crediteur)) $details.=" (FIF)";
		$details.="</td><td>";
		if  ($liste_cred->validation_credit==0) 
			$details.=" <img src=\"images/Cancel-resa.png\" title=\"credit supprim&eacute;\">";
		else {
			if ($liste_cred->id_moyen_paiement<>2 and est_min_manager($user)){
				$details.=" <a onClick=\"recharger('Voulez-vous supprimer cette ligne de credit ?'";
				$details.=",'".JRoute::_('../../index.php/article?id=77')."";
				$details.="&id_cred=".$liste_cred->id_credit_client."&id_client=".$liste_cred->id_client."')\">";
				$details.="<img src=\"images/remove-credit-icon.png\" title=\"supprimer ce credit\">";
			}
		}
		$details.="</td></tr>";
	}
	$details.="<tr>";
	$details.="<th>NBRE</th>";
	$details.="<th>".$nbre_operations."</th><th ></th><th >Total</th><th nowrap>".format_fr($total_versements_page)." &#8364;</th><td colspan=5>&nbsp;</th>";
	$details.="</tr></table>";
}	
	$details.="<br><hr><br>";
	if (test_non_vide($id_client)){
		$total_credit_actuel=recup_credit_total_client($id_client);
		$total_caution_actuel=recup_caution_total_client($id_client);
	
	if (test_non_vide($les_erreurs)) echo "<font color=red>".$les_erreurs."</font><hr>";
	?>
		<center>
		<form name="register_credit" class="submission box" action="<?php echo JRoute::_( 'article?id=77&id_client='.$id_client.''); ?>" method="post"  >
		<table class="zebra">
			<tr>
				<th width="50%">Montant caution actuelle</th><th width="50%" >Montant avoir actuel</th>
			</tr>
			<tr>
				<td ><?
					echo format_fr($total_caution_actuel);
					echo "<input name=\"total_caution_actuel\" type=\"hidden\"  value=\"".$total_caution_actuel."\">";
				?> euros</td>
				
				<td ><?
					echo format_fr($total_credit_actuel);
					echo "<input name=\"total_credit_actuel\" type=\"hidden\"  value=\"".$total_credit_actuel."\">";
					echo "<input name=\"id_client\" type=\"hidden\"  value=\"".$id_client."\">";
				?> euros</td>
			</tr>
		</table>
		<hr>
		<table class="zebra">
			<tr>
				<th>Type</th><th>Avoir/Caution</th><th>Moyen de paiement</th><th>Info cr&eacute;dit</th><th>Montant &agrave; cr&eacute;diter</th>
			</tr>
			<tr>
				<td>
					<?if (est_min_agent($user)){?>
						<select name="type" onChange="recharger('','')">
							<option value=1 <?if ($_POST["type"]==1) echo " selected ";?>>Paiement</option>
							<option value=3 <?if ($_POST["type"]==3) echo " selected ";?>>Remboursement</option>
						</select>
					<?}
					else echo "Paiement<input type=\"hidden\" name=\"type\"  value=\"1\">";
					?>
				</td>
				
				<td><? menu_deroulant("Type_credit",$_POST["Type_credit"],"recharger('','')");?></td>

				<td><? menu_deroulant("Moyen_paiement",$_POST["Moyen_paiement"],"",$_POST["type"],1);?></td>

				<td>
				<input type="text" name="info_credit" id="info_credit" size="10" value="<?php echo $_POST["info_credit"];?>" maxlength="50" />
				</td>
				<td>
			<?if (est_register($user)){
				echo "<select name=\"Montant\" >";
				if (isset($_POST["Montant"])) 
					$Montant = $_POST["Montant"];

				for ($i=10;$i<=80;$i+=10) {
						  $select_montant="";
						  if ($Montant==$i) 
							 $select_montant=" selected ";

						  echo "<option value=\"".$i."\" \"".$select_montant."\">".$i." €</option>";
				}
				echo "</select>";
			}
			else {?>
				<input type="text" name="Montant" id="Montant" size="10" value="<?php if (test_non_vide($les_erreurs)) echo $_POST["Montant"];?>" class="inputbox required" maxlength="8" />*
			<?}?>	
				</td>
			<td align="right">
				<input name="valide" type="button" value="payer" onclick="recharger('Confirmez cette nouvelle ligne de credit','')">
			</td>
		</tr>
		</table>
		</form><br>
	<?
	}
	if (test_non_vide($_GET["detail"]) or test_non_vide($equipe))
		echo $details;
	else echo "<a href=\"".$_SERVER['REQUEST_URI']."&id_client=".$id_client."&detail=1\" />Afficher le détail</a><br>"; 	
	if (test_non_vide($_GET["hist"])){
		if (test_non_vide($id_client) or test_non_vide($date_deb) ){
			
			if(test_non_vide($date_deb)) {
				$complement_date_requete2=" and TIMESTAMPDIFF(MINUTE, CAST(concat(hcc.date_credit,\" \",hcc.heure_credit) AS CHAR(22)),";
				$complement_date_requete2.=" CAST(concat(\"".$date_deb."\",\" \",\"08:00:00\") AS CHAR(22)))<0 ";
				$complement_date_requete2.=" and TIMESTAMPDIFF(MINUTE, CAST(concat(hcc.date_credit,\" \",hcc.heure_credit) AS CHAR(22)),";
				$complement_date_requete2.=" CAST(concat(\"".decaler_jour($date_deb,1)."\",\" \",\"05:00:00\") AS CHAR(22)))>0 ";
			}
			else $complement_req2="";
			
			$requete_liste_hist_cred="select (select name from #__users where id=hcc.id_user_suppr) as archiveur, hcc.*,c.*, m.nom as moy_paie, ";
			$requete_liste_hist_cred.=" (select nom from Type_credit where id=hcc.type_credit) as nom_type_credit from  Client as c, Hist_Credit_client as hcc ";
			$requete_liste_hist_cred.=" left join Moyen_paiement as m on hcc.id_moyen_paiement=m.id where hcc.id_client=c.id_client ";
			$requete_liste_hist_cred.=$complement_date_requete2.$complement_req;
			//echo $requete_liste_hist_cred;
			
			if (test_non_vide($Moyen_paiement)) $requete_liste_hist_cred.=" and hcc.id_moyen_paiement=".$Moyen_paiement;
			if (test_non_vide($Type_credit)) $requete_liste_hist_cred.=" and hcc.type_credit=".$Type_credit;
			if (test_non_vide($indic_annul))
				$requete_liste_hist_cred.=" and hcc.validation_credit=".$indic_annul;
				
			if (test_non_vide($indic_valid))
				$requete_liste_hist_cred.=" and hcc.validation_credit=".$indic_valid;
				
			if (test_non_vide($_GET["Remb"]))
				$requete_liste_hist_cred.=" and hcc.credit<0 and hcc.origine_credit=\"Remb.\" ";
				
			if (test_non_vide($_GET["Paie"]))
				$requete_liste_hist_cred.=" and hcc.credit>0 and hcc.origine_credit=\"Versement\" ";
		
			$requete_liste_hist_cred.=" order by hcc.date_suppr desc, hcc.heure_suppr desc";
		
			$db->setQuery($requete_liste_hist_cred);	
			$resultat_liste_hist_cred= $db->loadObjectList();
			
			if (!$resultat_liste_hist_cred) echo $prb;
			else {
				echo "<hr><h2><a name=\"signet\"></a>Historique</h2><hr><table class=\"zebra\"><tr>";
				echo "<th>Client</th><th>Archiveur</th><th>Date arch.</th><th>Heure arch.</th>";
				echo "<th>Date cr&eacute;dit</th><th>Heure<br>cr&eacute;dit</th><th>Montant<br>cr&eacute;dit&eacute;</th>";
				echo "<th>Moyen<br>paiement</th><th>Type<br>cr&eacute;dit</th><th>Origine<br>cr&eacute;dit</th><th>Suppr</th></tr>";
				
				foreach($resultat_liste_hist_cred as $liste_hist_cred){
		
					echo "<tr>";
					echo "<td><a href=\"index.php/component/content/article?id=60&id_client=".$liste_hist_cred->id_client."\"/>";
					if ($liste_hist_cred->societe=="") {
						if ($liste_hist_cred->equipe==""){
							echo $liste_hist_cred->nom." ".$liste_hist_cred->prenom;
						}
						else echo $liste_hist_cred->equipe;
					}
					else echo $liste_hist_cred->societe;
					echo "</a></td><td>";
					echo $liste_hist_cred->archiveur."</td><td>";
					echo inverser_date($liste_hist_cred->date_suppr)."</td><td>";
					echo $liste_hist_cred->heure_suppr."</td><td>";
					if ($liste_hist_cred->date_credit<>"0000-00-00")
						echo inverser_date($liste_hist_cred->date_credit);
					echo "</td><td>";
					if ($liste_hist_cred->heure_credit<>"")
						echo $liste_hist_cred->heure_credit;
					echo "</td><td>";
					echo $liste_hist_cred->credit."</td><td>";
					echo $liste_hist_cred->moy_paie."</td><td>";
					echo $liste_hist_cred->nom_type_credit."</td><td>";
					echo $liste_hist_cred->origine_credit."</td><td>";
					if  ($liste_hist_cred->validation_credit==0) 
							echo " <img src=\"images/Cancel-resa.png\" title=\"credit supprim&eacute;\">";
					echo "</td></tr>";
				}
				echo "</table>";
			}
		
		}
	}
	else echo "<a href=\"".$_SERVER['REQUEST_URI']."&id_client=".$id_client."&hist=1#signet\" />Afficher l'historique</a>";	
	
	
}
	
	



?>