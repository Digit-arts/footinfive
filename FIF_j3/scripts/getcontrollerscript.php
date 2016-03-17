<?php
$tbl = 'client_tarification_speciale';
$tblParts = explode ( '_', $tbl );
$tblFormat = "";
foreach ( $tblParts as $tblPart ) {
	$tblFormat .= ucfirst ( $tblPart );
}
$idField = '';
/* Ouverture de la connexion */
if (! $Connexion = @mysql_connect ( 'localhost', 'root', '' )) {
	echo "erreur : " . mysql_error ();
} elseif (! $db = @mysql_select_db ( 'mysql_fif_j3', $Connexion ))   /*SÃ©lection de la base de donnÃ©es*/
 				{
	echo "erreur : " . mysql_error ();
} else {
	$Requete = "SHOW COLUMNS FROM $tbl";
	if (! $Resultat = @mysql_query ( $Requete, $Connexion )) {
		echo "erreur : " . mysql_error ();
	} else {
		$numColumns = mysql_num_rows ( $Resultat );
		$x = 0;
		$propArray = '';
		$postString = '';
		$propEntArr  = '';
		while ( $x < $numColumns ) {
			$colname = mysql_fetch_row ( $Resultat );
			$pos_ = strpos ( $colname [0], '_' );
			if ($pos_ === false) {
				$name = $colname [0];
			} else {
				$name = substr ( $colname [0], $pos_ + 1, strlen ( $colname [0] ) - $pos_ + 1 );
			}
			
			$shortname = str_replace ( '_', '', $name );
			
			$propArray .= "'$shortname' => \$entry['$colname[0]'],";
			
			$postString .= "if(isset(\$post ['" . $colname [0] . "'] )) {\$" . strtolower ( $tblFormat ) . "->Set" . $shortname . " ( \$post ['" . $colname [0] . "'] );}";
			
			$propEntArr .= "\$" . strtolower ( $tblFormat ) . "Arr['" . $colname [0] . "'] = \$" . strtolower ( $tblFormat ) . "->Get" . $shortname . "();";
			$x ++;
		}
		
		echo "< ?php<br>";
		echo "require_once (dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . \"DBAL/data/" . strtolower ( $tblFormat ) . "_data.class.php\");<br><br>";
		
		echo "/**<br>";
		echo "* Définition du controller de la classe $tblFormat <br>";
		echo "*<br>";
		echo "* @author DIGITARTS - " . date ( "d M Y" ) . "<br>";
		echo "*<br>";
		echo "*/<br>";
		echo "class " . $tblFormat . "Controller extends " . $tblFormat . "Data {<br>";
		
		echo "/**<br>";
		echo "* Mets à jour $tblFormat<br>";
		echo "* @param " . $tblFormat . "Entity $tblFormat<br>";
		echo "* @return number<br>";
		echo "*/<br>";
		echo "function Update" . $tblFormat . "(" . $tblFormat . "Entity $" . strtolower ( $tblFormat ) . ") {return \$this->Update" . $tblFormat . "Data($" . strtolower ( $tblFormat ) . ");}";
		
		echo "/**<br>";
		echo "* Ajoute une $tblFormat<br>";
		echo "* @param " . $tblFormat . "Entity $tblFormat<br>";
		echo "* @return " . $tblFormat . "Entity|NULL<br>";
		echo "*/<br>";
		echo "function Add" . $tblFormat . "(" . $tblFormat . "Entity $" . strtolower ( $tblFormat ) . ") {\$result = \$this->Add" . $tblFormat . "Data($" . strtolower ( $tblFormat ) . ");if (\$result != - 1) {\$" . strtolower ( $tblFormat ) . "->SetId ( \$result );return $" . strtolower ( $tblFormat ) . ";}return null;}<br><br>";
		
		echo "/**<br>";
		echo "* Récupère une $tblFormat via ID<br>";
		echo "*<br>";
		echo "* @param integer \$" . strtolower ( $tblFormat ) . "Id<br>";
		echo "* @return " . $tblFormat . "Entity<br>";
		echo "*/<br>";
		echo "function Get" . $tblFormat . "ById(\$" . strtolower ( $tblFormat ) . "Id) {				
			return \$this->Get" . $tblFormat . "ClassFromArray ( \$this->Get" . $tblFormat . "ByIdData ( \$" . strtolower ( $tblFormat ) . "Id ) );
		}<br><br>";
		
		echo "/**<br>";
		echo "* Recupère la liste complète des " . $tblFormat . "<br>";
		echo "*<br>";
		echo "* @return multitype:" . $tblFormat . "Entity<br>";
		echo "*/<br>";
		echo "function GetAll" . $tblFormat . "() {
			\$result = \$this->GetAll" . $tblFormat . "Data ();
			
			\$" . strtolower ( $tblFormat ) . "List = array ();
			if(\$result==null)return \$" . strtolower ( $tblFormat ) . "List;	
			
			foreach ( \$result as \$entry ) {
		
				\$" . strtolower ( $tblFormat ) . "List [] = \$this->Get" . $tblFormat . "ClassFromArray ( \$entry );
			}
				
			return \$" . strtolower ( $tblFormat ) . "List;
		}<br><br>";
		
		echo "/**<br>";
		echo "* Cree un objet de type " . strtolower ( $tblFormat ) . " à partir du post<br>";
		echo "*<br>";
		echo "* @param array \$post<br>";
		echo "* @return " . $tblFormat . "Entity<br>";
		echo "*/<br>";
		echo "function Capture" . $tblFormat . "FromPost(\$post) {
			\$" . strtolower ( $tblFormat ) . " = new " . $tblFormat . "Entity (); $postString return \$" . strtolower ( $tblFormat ) . ";}<br><br>";
		
		$propArray = rtrim ( $propArray, ',' );
		echo "/**<br>";
		echo "* Cree une class à partir du tableau<br>";
		echo "*<br>";
		echo "* @param array \$entry<br>";
		echo "* @return " . $tblFormat . "Entity<br>";
		echo "*/<br>";
		echo "protected function Get" . $tblFormat . "ClassFromArray(\$entry) {";
		echo "if (\$entry == null)";
		echo "return null;";
		echo "<br>\$" . strtolower ( $tblFormat ) . " = new $tblFormat" . "Entity ( array (";
		echo $propArray;
		echo ") );";
		echo "return \$" . strtolower ( $tblFormat ) . " ;";
		echo "}<br><br>";
		
		
		echo "/**<br>";
		echo "* Retourne l'entité " . $tblFormat. " sous forme de tableau<br>";
		echo "*<br>";
		echo "* @param " . $tblFormat . "Entity \$" . strtolower ( $tblFormat ) . "<br>";
		echo "* @return array<br>";
		echo "* @deprecated<br>";
		echo "*/<br>";
		echo "public function Get" . $tblFormat . "AsArray(" . $tblFormat . "Entity \$" . strtolower ( $tblFormat ) . ") {";
		echo "\$" . strtolower ( $tblFormat ) . "Arr = array(); $propEntArr return \$" . strtolower ( $tblFormat ) . "Arr;}";
		
		echo "}";
		
		/* Fermer la connexion */
		mysql_close ( $Connexion );
	}
}

?>