<?php

  
$requete_recup_client="select c.*, ";
$requete_recup_client.="  u.email as courriel , v.code_postal, v.nom_maj_ville, ";
$requete_recup_client.=" adresse, date_naissance from Client as c LEFT JOIN Ville as v ON c.code_insee=v.code_insee ";
$requete_recup_client.=" LEFT JOIN  s857u_users as u on u.id=c.id_user where 1 ";
$requete_recup_client.=" order by date_modif desc, heure_modif desc";
						
//echo $requete_recup_client;

$idConnexion=mysql_connect("localhost","Cyclople","MixMax123"); 
$connexionReussie = mysql_select_db("MySql_FIF");

$result = mysql_query($requete_recup_client);

if (file_exists('DOCS_CLIENTS/BddClient.csv')){
    chmod('DOCS_CLIENTS/BddClient.csv', 0777);
    unlink('DOCS_CLIENTS/BddClient.csv');
	
}

$fp = fopen('DOCS_CLIENTS/BddClient.csv','w+');

$fields = array('Num client','Societe','Equipe','Nom','Prenom','Tel','Email','Date Naiss.','Adresse','Code Postal','Ville'); 
fputcsv($fp, $fields,';');

while ($recup_client = mysql_fetch_assoc($result)){				
    $fields=array($recup_client["id_client"],$recup_client["societe"],$recup_client["equipe"],$recup_client["nom"],
        $recup_client["prenom"],$recup_client["mobile1"],$recup_client["courriel"],$recup_client["date_naissance"],
        $recup_client["adresse"],$recup_client["code_postal"],$recup_client["nom_maj_ville"]);
    fputcsv($fp, $fields,';');

}   

fclose($fp);



header("Location: http://footinfive.com/FIF/libraries/ya2/DOCS_CLIENTS/BddClient.csv");
?>

