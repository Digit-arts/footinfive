<?php

echo get_include_path();

require_once 'Zend/Gdata/ClientLogin.php';

$email = 'lyassine@ifbi.fr';
$passwd = '4. lulu 9.';

try {
     $client = Zend_Gdata_ClientLogin::getHttpClient($email, $passwd, 'cl');
}    
catch (Zend_Gdata_App_CaptchaRequiredException $cre) {
    echo "l'URL de l\'image CAPTCHA est: ". $cre->getCaptchaUrl() ."\n";
    echo "Token ID: ". $cre->getCaptchaToken() ."\n";
} 

catch (Zend_Gdata_App_AuthException $ae) {
   echo "Problème d'authentification : ". $ae->exception() ."\n";
}
 
$cal = new Zend_Gdata_Calendar($client);

?>