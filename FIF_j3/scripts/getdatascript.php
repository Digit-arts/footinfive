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
} elseif (! $db = @mysql_select_db ( 'mysql_fif_j3', $Connexion ))   /*Sélection de la base de données*/
 				{
	echo "erreur : " . mysql_error ();
} else {
	$Requete = "SHOW COLUMNS FROM $tbl";
	if (! $Resultat = @mysql_query ( $Requete, $Connexion )) {
		echo "erreur : " . mysql_error ();
	} else {
		$numColumns = mysql_num_rows ( $Resultat );
		$x = 0;
		$y = 1;
		$z = 1;
		$fields = '';
		$values = '';
		$properties = '';
				$propArray ='';
		while ( $x < $numColumns ) {
			
			$colname = mysql_fetch_row($Resultat);
			$pos_ = strpos($colname[0], '_');
			if ($pos_ === false) {
				$name = $colname[0];
			} else {
				$name = substr($colname[0],$pos_+1,strlen($colname[0])-$pos_+1);
			}
				
			$shortname = str_replace('_', '', $name);
			if(strtolower($name)=='id') {$idField=$colname[0];}
			else {$properties .="<br>$colname[0] = :".str_replace('_', '', $name).",";}
				
			$propArray .= "':$shortname' => \$$tbl".'->Get'.$shortname."(),";
			$x++;
			
			
			if (strtolower ( $name ) != 'id') {
				$fields .= "`$colname[0]`,";
				$values .= ":" . str_replace ( '_', '', $name ) . ",";
				$propArray .= "':" . str_replace ( '_', '', $name ) . "' => \$$tblFormat" . '->Get' . str_replace ( '_', '', $name ) . "(),";
				
				if (($y == 4) && ($x < $numColumns - 1)) {
					$fields .= "<br>";
					$y = 0;
				}
				if (($z == 6) && ($x < $numColumns - 1)) {
					$values .= "<br>";
					$z = 0;
				}
				$y ++;
				$z ++;
			}
		}
		
		print "< ?php<br><br>";
		echo "require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . \"data.class.php\");<br>";
		echo "require_once (dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . \"entities/".strtolower($tblFormat)."_entity.class.php\");<br><br>";
		
		echo "/**<br>";
		 echo "* Couche SQL : ".$tblFormat." <br>";
		 echo "*<br>";
		 echo "* @author DIGITARTS - ".date("d M Y")."<br>";
		 echo "*<br>";
		echo "*/<br>";
		echo "class ".$tblFormat."Data extends Data {<br><br>";
		
		$properties = rtrim($properties,',');
		$propArray = rtrim($propArray,',');
		
		echo "protected function Update" . $tblFormat . "Data(" . $tblFormat . "Entity \$".strtolower($tblFormat).") {";
		
		echo "\$query=\"";
		echo "UPDATE $tbl SET ";
		echo $properties;
		echo "<br>WHERE $idField = :id;\";<br>";
		
		/*echo "<br>\$params = array (";
				echo $propArray;
				echo ");";*/
		echo "return self::UpdateMulti ( \$query, $" . strtolower($tblFormat) . "->ToArray () );";
		echo "}";
		
				echo "<br><br><br>";
		
		
		$fields = rtrim ( $fields, ',' );
		$values = rtrim ( $values, ',' );
		$propArray = rtrim ( $propArray, ',' );
		
		echo "protected function Add" . $tblFormat . "Data(" . $tblFormat . "Entity \$".strtolower($tblFormat).") {";
		
		echo "\$query=\"";
		echo "INSERT INTO $tbl ($fields) ";
		echo "<br>VALUES ($values);\";<br>";
		
		//echo "\$params = array ($propArray);";
		echo "return self::InsertSingleLine ( \$query, $" . strtolower($tblFormat) . "->ToArrayNoId () );";
		echo "}<br><br>";
		
		
		echo "protected function Get".$tblFormat."ByIdData(\$".strtolower($tblFormat)."Id) {
			\$query = \"SELECT * FROM $tbl WHERE $idField = :".strtolower($tblFormat)."Id ;\";
		
			\$params = array (
					':".strtolower($tblFormat)."Id' => \$".strtolower($tblFormat)."Id
			);
		
			return self::QuerySingleLine ( \$query, \$params );
		}<br><br>";
		
		echo "protected function GetAll".$tblFormat."Data() {
			\$query = \"SELECT * FROM $tbl\";
		
			return self::QueryMultipleLine ( \$query );
		}";
		
		echo "} ?>";
		
		/* Fermer la connexion */
		mysql_close ( $Connexion );
	}
}

?>