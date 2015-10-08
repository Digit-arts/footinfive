<?php

  
$requete_recup_credit="select cc.`id_client`,concat(c.nom,' ',c.prenom) as Client,cc.`date_credit`,cc.`credit`,
		(select mp.nom from Moyen_paiement as mp where cc.`id_moyen_paiement`=mp.id) as le_moyen_de_paiement,
		(select tc.nom from Type_credit as tc where cc.`type_credit`=tc.id) as le_type_de_credit,
		(select u.name from s857u_users as u where u.id=cc.`id_user_creation`) as effectuer_par
		from Client as c, `Credit_client` as cc  where c.id_client=cc.`id_client` and cc.`validation_credit`=1
		and ((SELECT IFNULL((SELECT sum(cc2.credit) FROM Credit_client as cc2 where cc2.type_credit=2 and cc2.validation_credit=1 and cc2.id_client=cc.`id_client`),0)<>0)
		    or (SELECT IFNULL((SELECT sum(cc3.credit) FROM Credit_client as cc3 where cc3.type_credit=1 and cc3.validation_credit=1 and cc3.id_client=cc.`id_client`),0)<>0))
		order by `date_credit` desc, `heure_credit` desc";
						
//echo $requete_recup_credit;

$idConnexion=mysql_connect("localhost","Cyclople","MixMax123"); 
$connexionReussie = mysql_select_db("MySql_FIF");

$result = mysql_query($requete_recup_credit);

if (file_exists('DOCS_CLIENTS/BddCredit.csv')){
    chmod('DOCS_CLIENTS/BddCredit.csv', 0777);
    unlink('DOCS_CLIENTS/BddCredit.csv');
	
}

$fp = fopen('DOCS_CLIENTS/BddCredit.csv','w+');

$fields = array('Num client','Client','Date credit','Montant','Moyen','Type','Effectuer par'); 
fputcsv($fp, $fields,';');

while ($recup_credit = mysql_fetch_assoc($result)){				
    $fields=array($recup_credit["id_client"],$recup_credit["Client"],$recup_credit["date_credit"],str_replace(".", ",", $recup_credit["credit"]),$recup_credit["le_moyen_de_paiement"]
		  ,$recup_credit["le_type_de_credit"],$recup_credit["effectuer_par"]);
    fputcsv($fp, $fields,';');

}   

fclose($fp);



header("Location: http://footinfive.com/FIF/libraries/ya2/DOCS_CLIENTS/BddCredit.csv");
?>

