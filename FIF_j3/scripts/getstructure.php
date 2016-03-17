<?php
$tbl = 'client_tarification_speciale';
$tblParts = explode('_',$tbl);
$tblFormat = "";
foreach ($tblParts as $tblPart) {
	$tblFormat.=ucfirst($tblPart);
}
/* Ouverture de la connexion */
if (! $Connexion = @mysql_connect ( 'localhost', 'root', '' )) {
	echo "erreur : " . mysql_error ();
} elseif (! $db = @mysql_select_db ( 'mysql_fif_j3', $Connexion ))   /*Sélection de la base de données*/
 				{
	echo "erreur : " . mysql_error ();
} else {
	$Requete = "SHOW COLUMNS FROM `".$tbl."`";
	if (! $Resultat = @mysql_query ( $Requete, $Connexion )) {
		echo "erreur : " . mysql_error ();
	} else {
		$numColumns = mysql_num_rows ( $Resultat );
		$x = 0;
		$properties = '';
		print "< ?php<br><br>";
		echo "require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . \"record.class.php\");<br>";
			
		echo "<br><br>/**<br>";
		echo "* ".$tblFormat." entity<br>";
		echo "*<br>";
		echo "* @author DIGITARTS - ".date("d M Y")."<br>";
		echo "*<br>";
		echo "*/<br>";
		echo "class ".$tblFormat."Entity extends Record {";
		
		while ( $x < $numColumns ) {
			$colname = mysql_fetch_row ( $Resultat );
			$pos_ = strpos ( $colname [0], '_' );
			if ($pos_ === false) {
				$name = $colname [0];
			} else {
				$name = substr ( $colname [0], $pos_ + 1, strlen ( $colname [0] ) - $pos_ + 1 );
			}
			$pos = strpos ( $colname [1], '(' );
			if ($pos === false) {
				$type = $colname [1];
			} else {
				$type = substr ( $colname [1], 0, $pos );
			}
			$null = $colname [2];
			$default = $colname [4];
			
			$properties .= "'" . str_replace ( '_', '', $name ) . "',";
			echo "<br><br>//FIELD : $colname[0] TYPE : $colname[1] NULL : $colname[2] KEY : $colname[3] DEFAULT : $colname[4] EXTRA : $colname[5] <br>";
			$def = '';
			$pdoType='PDO::PARAM_STR';
			if ($null == 'NO') {
				switch ($type) {
					case 'int' :
					case 'tinyint' :
					case 'bigint' :
						$def = "=$default";
						$pdoType="PDO::PARAM_INT";
						break;
					case 'decimal' :
						$def = "=$default";
						break;
					case 'varchar' :
					case 'char' :
					case 'longtext' :
					case 'date' :
					case 'datetime' :
						$def = "='$default'";
						break;
					default :
						$def = "='$default'";
						break;
				}
			}
			if (strtolower ( $name ) == 'id') {
				$def = '';
			}
			
			
				
			echo "protected \$" . strtolower ( $name ) . $def . ";";
			echo "const INVALID_" . strtoupper ( $name ) . " = $x;";
			echo "/**<br>* Retourne la valeur du champs $colname[0]<br>*/";
			echo "public function Get" . str_replace ( '_', '', $name ) . "() {	return \$this->" . strtolower ( $name ) . ";}";
			
			echo "/**<br>* Retourne la type du champs $colname[0]<br>*/";
			echo "public function Get" . str_replace ( '_', '', $name ) . "PDOType() {	return \"" . $pdoType . "\";}";
			
			echo "/**<br>* Definit la valeur du champs $colname[0]<br>* @param $type $name<br>*/";
			echo "public function Set" . str_replace ( '_', '', $name ) . "(\$$name) {";
			
			switch ($type) {
				case 'int' :
				case 'decimal' :
					echo "if(!is_numeric(\$$name)) ";
					echo "{\$this->erreurs[] = self::INVALID_" . strtoupper ( $name ) . ";}else {\$this->" . strtolower ( $name ) . " = \$$name;}}";
					break;
				case 'varchar' :
				case 'char' :
				case 'longtext' :
					echo "if(empty(\$$name) || !is_string(\$$name)) ";
					echo "{\$this->erreurs[] = self::INVALID_" . strtoupper ( $name ) . ";}else {\$this->" . strtolower ( $name ) . " = \$$name;}}";
					break;
				default :
					echo "\$this->" . strtolower ( $name ) . " = \$$name;}";
			}
			$x ++;
		}
		
		$properties = rtrim ( $properties, ',' );
		echo "<br><br>/**<br>";
		echo "* Liste des proprietes de l'entites<br>";
		echo "* @var properties<br>";
		echo "*/";
		echo "<br>protected \$properties;";
		echo "public function __construct(array \$donnees = array()){\$this->properties = array($properties);parent::__construct(\$donnees);}";
		
		echo "/**<br>";
		echo "* Renvoie un tableau associatif base sur le contenu de l'objet<br>";
		echo "* @see Record::ToArray()<br>";
		echo "*/<br>";
		echo "public function ToArray() {return parent::ToArrayBase(\$this->properties);}";
		
		echo "public function ToArrayNoId() {\$propertiesNoId = array ();foreach (\$this->properties as \$prop) {if(strtolower(\$prop)!='id') 
{\$propertiesNoId[] = \$prop;}}return parent::ToArrayBase (\$propertiesNoId);}";
		echo "} ?>";
		/* Fermer la connexion */
		mysql_close ( $Connexion );
	}
}

?>