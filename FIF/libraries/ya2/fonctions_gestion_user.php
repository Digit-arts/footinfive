<?php

function VerifierAdresseMail($adresse){ 
   $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#'; 
   if(preg_match($Syntaxe,$adresse)) 
      return true; 
   else 
     return false; 
}

function VerifierMonetaire($Montant){
	
	$Syntaxe='`^([0-9]{1,5})(,{0,1})([0-9]{0,2})$`'; 
   if(preg_match($Syntaxe,$Montant)) 
      return true; 
   else 
     return false;

}

function VerifierNom($nom){ 
   $Syntaxe="@^(\pL+[\' -]?)+\pL+$@D";
   if(preg_match($Syntaxe,$nom)) 
      return true; 
   else 
     return false; 
}

function premiere_lettre_maj($mot){ 
   $premiere_lettre_maj=mb_strtoupper(substr($mot,0,1));//on applique remplace_accents 
   $reste_min=mb_strtolower(substr($mot,1));
   $resultat=$premiere_lettre_maj.$reste_min;
   return($resultat);
}

function tout_majuscule($mot){ 

return (mb_strtoupper(Trim($mot)));

}

function VerifierNomVille($nom){ 
   $Syntaxe="@^(\pL+[\' -]?)+\pL+$@D"; 
   if(preg_match($Syntaxe,$nom)) 
      return true; 
   else 
     return false; 
}

function VerifierCP($CP){ 
   $Syntaxe= '`^[0-9]{5,5}$`'; 
   if(preg_match($Syntaxe,$CP)) 
      return true; 
   else 
     return false; 
}
 
function VerifierEntier($valeur){ 
   $Syntaxe= '`^[0-9]{0,5}$`'; 
   if(preg_match($Syntaxe,$valeur)) 
      return true; 
   else 
     return false; 
} 
 
function VerifierNumMob($numero_de_telephone){ 
   $Syntaxe='`^0[6-7]([0-9]{2}){4}$`'; 
   if(preg_match($Syntaxe,$numero_de_telephone)) 
      return true; 
   else 
     return false; 
}



function VerifierNumFixe($numero_de_telephone){ 
   $Syntaxe1='`^0[1-5]([0-9]{2}){4}$`';
   $Syntaxe2='`^0[8-9]([0-9]{2}){4}$`'; 
   if(preg_match($Syntaxe1,$numero_de_telephone) or preg_match($Syntaxe2,$numero_de_telephone)) 
      return true; 
   else 
     return false; 
}

function VerifierSiret($siret){ 
   $Syntaxe='/^\d{14}$/';
   if(preg_match($Syntaxe,$siret)) 
      return true; 
   else 
     return false; 
}

function test_non_vide($var){ 
   
   if(isset($var) and ($var<>""))
      return true; 
   else 
     return false; 
}

function pagination($requete,$lien){

	$db = JFactory::getDBO();
	
	$db->setQuery($requete);
	$db->query();
	$nbre_resultats=$db->getNumRows();
	$nbre_pages=ceil($nbre_resultats/20);
	
			
	if (!test_non_vide($_GET["page"])) {
		$requete_limit=" LIMIT 0 , 20";
		$page_en_cours=1;
	}
	else {
		$page_en_cours=$_GET["page"];
		$requete_limit=" LIMIT ".(($page_en_cours-1)*20)." , 20";
	}

	 if ($page_en_cours<5)
	    $page_min=0;
	 else $page_min=$page_en_cours-5;
	 
	 if ($page_en_cours<($nbre_pages-5))
	    $page_max=$page_en_cours+5;
	 else $page_max=$nbre_pages;
	 

	echo "<br><table width=\"100%\"><tr><td  width=150>".$nbre_resultats." resultas trouv&eacute;s</td><td align=center> pages ";
	for ($i=$page_min;$i<$page_max;$i++) 
		if ($i==($page_en_cours-1)) echo ($i+1)." -"; 
		else echo $lien."&page=".($i+1)."#deb_page\">".($i+1)."</a> - ";
	echo "</td><td width=150 align=right>Page ".$page_en_cours."/".$nbre_pages."</td></tr></table><hr>";

	return($requete_limit);
}


?>