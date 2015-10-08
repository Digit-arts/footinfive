<?


require_once ('fonctions_module_reservation.php');
require_once ('fonctions_gestion_user.php');

?>
<script type="text/javascript">
	
	function enregistrer() {
		document.register_groupe.submit()
	}
	function enregistrer2() {
		document.register_groupe2.submit()
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

if (test_non_vide($_POST["Type_Regroupement"]))
	$id_type_regroupement=$_POST["Type_Regroupement"];

if (test_non_vide($_POST["Regroupement"]))
	$id_regroupement=$_POST["Regroupement"];
		
if (test_non_vide($_POST["Sous_regroupement"]))
	$id_sous_regroupement=$_POST["Sous_regroupement"];
	
if (test_non_vide($_POST["confirmer_suppr"])){
	$table=$_POST["la_table"];
	if (strcmp($_POST["la_table"],"Type_Regroupement")==0){
		$table2="r";
		$champs="id_type_regroupement";
	}
	else {
		if (strcmp($_POST["la_table"],"Regroupement")==0)
			$table2="r";
		else $table2="sr";
		$champs="id";
	}
	
	$resultat=recup_recup_client(" and ".$table2.".".$champs."=".$_POST["l_id"],"",1);
	
	if (!test_non_vide($resultat))
		supprimer_1_element($table,"id",$_POST["l_id"]);
	else echo "<font color=red>Suppression impossible : cet &eacute;lement est utilis&eacute;.</font>";
}
else {
	if (isset($_POST["modif"]) or isset($_POST["ajout"])){
		$existe_erreur=0;
	
		if (test_non_vide($_POST["code_insee"]))
			$code_insee=$_POST["code_insee"];
		
		if (test_non_vide($_POST["code_insee_facturation"]))
			$code_insee_facturation=$_POST["code_insee_facturation"];

		if (!test_non_vide($_POST["nom"]) or !test_non_vide($_POST["Mobile"]) or !test_non_vide($_POST["Courriel"])) {
			echo "<font color=red>Le nom, le numero de mobile ainsi que l'email sont obligatoires.<br></font>";
			$existe_erreur++;
		}
		
		if (test_non_vide($_POST["Mobile"]) and !(VerifierNumMob($_POST["Mobile"]))){
			echo "<font color=red>Numero de Tel mobile  incorrect.<br></font>"; 
			$existe_erreur++;
		}
		
		if (test_non_vide($_POST["Fixe"]) and !(VerifierNumFixe($_POST["Fixe"]))){
			echo "<font color=red>Numero de Tel fixe incorrect.<br></font>";
			$existe_erreur++;
		}
		if (test_non_vide($_POST["Siret"]) and !(VerifierSiret($_POST["Siret"]))){
			echo "<font color=red>Numero de Siret incorrect (14 chiffres).<br></font>";
			$existe_erreur++;
		}
		verif_cp_ville(&$code_insee,$_POST["cp"],$_POST["ville"],&$tab_villes,&$tab_cp,&$nbre_villes,&$nbre_cp,&$existe_erreur);
		verif_cp_ville(&$code_insee_facturation,$_POST["cp_facturation"],$_POST["ville_facturation"],&$tab_villes_facturation,&$tab_cp_facturation,&$nbre_villes_facturation,&$nbre_cp_facturation,&$existe_erreur);
		
		if (test_non_vide($_POST["Courriel"]) and !(VerifierAdresseMail($_POST["Courriel"]))){ 
			echo "<font color=red>Votre adresse email est incorrecte.<br></font>";
			$existe_erreur++;
		}
	}
		
	$infos_sous_regroupement=recup_sous_regroupement($id_type_regroupement,$id_regroupement,$id_sous_regroupement);
	
	
	
	if (test_non_vide($_POST["ajout"]) and $existe_erreur==0){
		$table=$_POST["la_table"];
		
		if (strcmp($_POST["la_table"],"Sous_regroupement")==0)
			$resultat=verif_existe_sous_groupement($_POST["nom"]);
		else $resultat=recup_1_element("id",$table,"nom",$_POST["nom"]);
		
		if (!test_non_vide($resultat)){
			$champs="nom";
			$les_valeurs="\"".$_POST["nom"]."\"";
			if (strcmp($_POST["la_table"],"Sous_regroupement")==0){
				ajout_sous_groupement($_POST["Regroupement"],$_POST["nom"],$_POST["Siret"],$_POST["Mobile"],$_POST["Fixe"],$_POST["Adresse"],$_POST["code_insee"],$_POST["Adresse_facturation"],$_POST["code_insee_facturation"],$_POST["Courriel"],$_POST["Commentaire"],$_POST["Effectif"]);
			}
			else {
				if (strcmp($_POST["la_table"],"Regroupement")==0){
					$champs.=", `id_type_regroupement`,`id_user_modif`, `date_modif`, `heure_modif`";
					$les_valeurs.=",".$_POST["Type_Regroupement"].",".$user->id.",\"".date("Y-m-d")."\",\"".date("H:i")."\" ";
				}
				ajouter_1_element($table,$champs,$les_valeurs);
			}
			header("Location: index.php/component/content/article?id=66");
			
		}
		else echo "<font color=red>Ajout impossible : cet &eacute;lement existe d&eacute;j&agrave;.</font>";
	}
	
	if (test_non_vide($_POST["modif"]) and $existe_erreur==0){
		$table=$_POST["la_table"];
		
		if (strcmp($_POST["la_table"],"Sous_regroupement")==0)
			$resultat=verif_existe_sous_groupement($_POST["nom"]);
		else $resultat=recup_1_element("id",$table,"nom",$_POST["nom"]);
		
		if (!test_non_vide($resultat)){
			$champs_maj="`nom`=\"".$_POST["nom"]."\"";
			if (strcmp($_POST["la_table"],"Sous_regroupement")==0){
				maj_sous_groupement($_POST["l_id"],$_POST["nom"],$_POST["Siret"],$_POST["Mobile"],$_POST["Fixe"],$_POST["Adresse"],$_POST["code_insee"],$_POST["Adresse_facturation"],$_POST["code_insee_facturation"],$_POST["Courriel"],$_POST["Commentaire"],$_POST["Effectif"]);
			}
			else {
				if (strcmp($_POST["la_table"],"Regroupement")==0){
					$champs_maj.=", `id_type_regroupement`=".$_POST["Type_Regroupement"].","
					."`id_user_modif`=".$user->id.", `date_modif`=\"".date("Y-m-d")."\", `heure_modif`=\"".date("H:i")."\"";
				}
				maj_1_element($table,$champs_maj,$_POST["l_id"]);
			}
			header("Location: index.php/component/content/article?id=66");
			
		}
		else echo "<font color=red>Modification impossible : ce nouveau nom existe d&eacute;j&agrave;.</font>";
	}

}
$elt="";
$action=0;

if (test_non_vide($_POST["type"]) and $_POST["type"]>0){
	$elt="Type_Regroupement";
	$action=$_POST["type"];
}
	
if (test_non_vide($_POST["reg"]) and $_POST["reg"]>0){
	$elt="Regroupement";
	$action=$_POST["reg"];
}

if (test_non_vide($_POST["s_reg"]) and $_POST["s_reg"]>0){
	$elt="Sous_regroupement";
	$action=$_POST["s_reg"];
}

if ((test_non_vide($elt) and $action>0) or $existe_erreur>0){		

	if (test_non_vide($_POST["elt"]))
		$elt=$_POST["elt"];

	if (test_non_vide($_POST["action"]))
		$action=$_POST["action"];
	
	if (test_non_vide($_POST["valeur"]))
		$valeur=$_POST["valeur"];
	else $valeur=$_POST["$elt"];
	
		
	if (test_non_vide($_POST["Siret"]))
		$Siret=$_POST["Siret"];
	else if ($action<>2) $Siret=$infos_sous_regroupement->Siret;
		
	if (test_non_vide($_POST["Adresse"]))
		$Adresse=$_POST["Adresse"];
	else if ($action<>2) $Adresse=$infos_sous_regroupement->Adresse;
		
	if (test_non_vide($_POST["Adresse_facturation"]))
		$Adresse_facturation=$_POST["Adresse_facturation"];
	else if ($action<>2) $Adresse_facturation=$infos_sous_regroupement->Adresse_facturation;
		
	if (test_non_vide($_POST["Effectif"]))
		$Effectif=$_POST["Effectif"];
	else if ($action<>2) $Effectif=$infos_sous_regroupement->Effectif;
		
	if (test_non_vide($_POST["Mobile"]))
		$Mobile=$_POST["Mobile"];
	else if ($action<>2) $Mobile=$infos_sous_regroupement->Mobile;
		
	if (test_non_vide($_POST["Fixe"]))
		$Fixe=$_POST["Fixe"];
	else if ($action<>2) $Fixe=$infos_sous_regroupement->Fixe;
		
	if (test_non_vide($_POST["Courriel"]))
		$Courriel=$_POST["Courriel"];
	else if ($action<>2) $Courriel=$infos_sous_regroupement->Courriel;
		
	if (test_non_vide($_POST["Commentaire"]))
		$Commentaire=$_POST["Commentaire"];
	else if ($action<>2) $Commentaire=$infos_sous_regroupement->Commentaire;
		
	if (test_non_vide($_POST["nom"]))
		$nom=$_POST["nom"];
	else if ($action<>2) $nom=recup_1_element("nom",$elt,"id",$valeur);
	

	?>
	<form name="register_groupe2" class="submission box" action="<?php echo JRoute::_( 'index.php/component/content/article?id=66'); ?>" method="post"  >
	<br>
	<table class="zebra" border="0"  >
	<?
	
	switch ($action){
		
		case 1 : echo "<th>Nom</th><td><input type=\"text\" name=\"nom\" id=\"nom\" size=\"40\" "
			." value=\"".$nom."\" class=\"inputbox required\" maxlength=\"50\" />"
			."<input type=\"hidden\" name=\"modif\" value=\"1\"/></td>";break;
			
		case 2 : echo "<th>Nom</th><td><input type=\"text\" name=\"nom\" id=\"nom\" size=\"40\" "
			." value=\"".$nom."\" class=\"inputbox required\" maxlength=\"50\" />"
			."<input type=\"hidden\" name=\"ajout\"  value=\"1\"/></td>";break;
			
		case 3 : echo "<th>Confirmer la suppression de \" ".$nom." \" </th>"
			." <td><input type=\"checkbox\" name=\"confirmer_suppr\" value=\"".$valeur."\"/></td>";break;	
	}
if ($action<>3){	
	if ($id_type_regroupement>0){?>
		<tr>
			<th>SIRET </th>
			<td ><? echo "<input type=\"text\" name=\"Siret\" id=\"Siret\" size=\"40\" "
					." value=\"".$Siret."\"  maxlength=\"14\" />"; ?></td>
		</tr>
		<tr>
			<th>Adresse </th>
			<td ><? echo "<input type=\"text\" name=\"Adresse\" id=\"Adresse\" size=\"40\" "
					." value=\"".$Adresse."\"  maxlength=\"50\" />"; ?></td>
		</tr>
		<tr>
			<th>Code postal, Ville  :	</th>
			<td><? 
				input_cp_ville (&$code_insee,$infos_sous_regroupement->code_postal,$infos_sous_regroupement->nom_maj_ville,&$nbre_cp,&$nbre_villes,&$tab_villes,&$tab_cp,1,"",$action);
			?>
			</td>
		</tr>
		<tr>
			<th>Adresse facturation </th>
			<td ><? echo "<input type=\"text\" name=\"Adresse_facturation\" id=\"Adresse_facturation\" size=\"40\" "
					." value=\"".$Adresse_facturation."\"  maxlength=\"50\" />"; ?></td>
		</tr>
		<tr>
			<th>CP, Ville facturation </th>
			<td ><? 
				input_cp_ville (&$code_insee_facturation,$infos_sous_regroupement->code_postal_facturation,$infos_sous_regroupement->nom_maj_ville_facturation,&$nbre_cp_facturation,&$nbre_villes_facturation,&$tab_villes_facturation,&$tab_cp_facturation,1,"_facturation",$action);
			?></td>
		</tr>
	<?}?>
		<tr>
			<th>Nombre salari&eacute;s </th>
			<td ><? echo "<input type=\"text\" name=\"Effectif\" id=\"Effectif\" size=\"40\" "
					." value=\"".$Effectif."\"  maxlength=\"6\" />"; ?></td>
		</tr>
		<tr>
			<th>Mobile </th>
			<td ><? echo "<input type=\"text\" name=\"Mobile\" id=\"Mobile\" size=\"40\" "
					." value=\"".$Mobile."\"  maxlength=\"10\" />"; ?></td>
		</tr>
		<tr>
			<th>Fixe </th>
			<td ><? echo "<input type=\"text\" name=\"Fixe\" id=\"Fixe\" size=\"40\" "
					." value=\"".$Fixe."\"  maxlength=\"10\" />"; ?></td>
		</tr>
		<tr>
			<th>Email </th>
			<td ><? echo "<input type=\"text\" name=\"Courriel\" id=\"Courriel\" size=\"40\" "
					." value=\"".$Courriel."\"  maxlength=\"50\" />"; ?></td>
		</tr>
		<tr>
			<th>Commentaire </th>
			<td ><? echo "<textarea rows=\"4\" cols=\"100\" name=\"Commentaire\">".$Commentaire."</textarea>"; ?></td>
		</tr>
<?}?>
		<tr>
			<td colspan=2 align=center><input name="valide" type="button" value="Enregistrer" onclick="enregistrer2()"></td>
		</tr>
	<?
	
	echo "<input type =\"hidden\" name=\"l_id\" value=\"".$valeur."\">";
	echo "<input type =\"hidden\" name=\"la_table\" value=\"".$elt."\">";
	
	echo "<input type =\"hidden\" name=\"Type_Regroupement\" value=\"".$id_type_regroupement."\">";
	echo "<input type =\"hidden\" name=\"Regroupement\" value=\"".$id_regroupement."\">";
	echo "<input type =\"hidden\" name=\"Sous_regroupement\" value=\"".$id_sous_regroupement."\">";
	echo "<input type =\"hidden\" name=\"valeur\" value=\"".$valeur."\">";
	echo "<input type =\"hidden\" name=\"action\" value=\"".$action."\">";
	echo "<input type =\"hidden\" name=\"elt\" value=\"".$elt."\">";

	?>
	</table>
	</form>
	<?

}
else {

	menu_acces_rapide($_GET["id_client"],"Gestion des groupes");
	
	
	
	
	?>
	<form name="register_groupe" class="submission box" action="<?php echo JRoute::_( 'index.php/component/content/article?id=66'); ?>" method="post"  >
	<br>
	<table class="zebra" border="0"  >
			
			<tr>
				<th>Type : </th>
				<td>
				<?
				menu_deroulant("Type_Regroupement",$id_type_regroupement,"enregistrer()");
				?>
				</td>
				<td>
				<!--select name="type" onChange="enregistrer()">
					<option value=0 disabled selected>Action</option>
					<option value=1>modifier</option>
					<option value=2>ajouter</option>
					<option value=3>supprimer</option>
				</select-->
				</td>
			</tr>
		<?
			if (test_non_vide($id_type_regroupement)){
		?>
			<tr>
				<th>Nom : </th>
				<td>
				<?
				menu_deroulant_simple("Regroupement",$id_regroupement," and id_type_regroupement=".$id_type_regroupement);
				?>
				</td>
				<td>
				<select name="reg" onChange="enregistrer()">
					<option value=0 disabled selected>Action</option>
					<option value=1>modifier</option>
					<option value=2>ajouter</option>
					<option value=3>supprimer</option>
				</select>
				</td>
			</tr>
		<?
		}
		if (test_non_vide($id_regroupement) and test_non_vide($id_type_regroupement)){
		?>
			<tr>
				<th>Service : </th>
				<td>
				<?
				if ($id_type_regroupement>0)
					$criteres=" and id_regroupement=".$id_regroupement;
				else $criteres="=2"; 
				menu_deroulant_simple("Sous_regroupement",$id_sous_regroupement,$criteres);
				?>
				</td>
				<td>
				<select name="s_reg" onChange="enregistrer()">
					<option value=0 disabled selected>Action</option>
					<option value=1>modifier</option>
					<option value=2>ajouter</option>
					<option value=3>supprimer</option>
				</select>
				</td>
			</tr>
		<?
		}
		if (test_non_vide($id_sous_regroupement) and test_non_vide($id_type_regroupement)){
	
			if ($id_type_regroupement>0){?>
			<tr>
				<th>SIRET </th>
				<td colspan=3><? echo $infos_sous_regroupement->Siret; ?></td>
			</tr>
			<tr>
				<th>Adresse </th>
				<td colspan=3><? echo $infos_sous_regroupement->Adresse."<br>"
							.$infos_sous_regroupement->code_postal." ".$infos_sous_regroupement->nom_maj_ville; ?></td>
			</tr>
			<tr>
				<th>Adresse facturation </th>
				<td colspan=3><? echo $infos_sous_regroupement->Adresse_facturation."<br>"
							.$infos_sous_regroupement->code_postal_facturation." ".$infos_sous_regroupement->nom_maj_ville_facturation; ?></td>
			</tr>
			<?}?>
			<tr>
				<th>Nombre salari&eacute;s </th>
				<td colspan=3><? echo $infos_sous_regroupement->Effectif; ?></td>
			</tr>
			<tr>
				<th>Mobile </th>
				<td colspan=3><? echo $infos_sous_regroupement->Mobile; ?></td>
			</tr>
			<tr>
				<th>Fixe </th>
				<td colspan=3><? echo $infos_sous_regroupement->Fixe; ?></td>
			</tr>
			<tr>
				<th>Email </th>
				<td colspan=3><? echo $infos_sous_regroupement->Courriel; ?></td>
			</tr>
			<tr>
				<th>Commentaire </th>
				<td colspan=3><? echo $infos_sous_regroupement->Commentaire; ?></td>
			</tr>
	
			
		<?
		}
		?>
	</table>
	</form>
<?
}
}
?>