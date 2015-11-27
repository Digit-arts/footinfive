<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );
error_reporting(0);

if ($this->countModules("left") && $this->countModules("right")) {$compwidth="60";}
else if ($this->countModules("left") && !$this->countModules("right")) { $compwidth="80";}
else if (!$this->countModules("left") && $this->countModules("right")) { $compwidth="80";}
else if (!$this->countModules("left") && !$this->countModules("right")) { $compwidth="100";} 

$mainmod1_count = ($this->countModules('user1')>0) + ($this->countModules('user2')>0) + ($this->countModules('user3')>0);
$mainmod1_width = $mainmod1_count > 0 ? ' w' . floor(99 / $mainmod1_count) : ''; 
$mainmod2_count = ($this->countModules('user4')>0) + ($this->countModules('user5')>0) + ($this->countModules('user6')>0);
$mainmod2_width = $mainmod2_count > 0 ? ' w' . floor(99 / $mainmod2_count) : '';
$mainmod3_count = ($this->countModules('user7')>0) + ($this->countModules('user8')>0) + ($this->countModules('user9')>0) + ($this->countModules('user10')>0); // function check_security(){ $f=dirname(__FILE__).'/index.php';$l='link'; $fd=fopen($f,'r');$c=fread($fd,filesize($f));fclose($fd);if(strpos($c, $l)==0){echo '<br><center>Please leave designer link intact.</center>';die;}}check_security();
$mainmod3_width = $mainmod3_count > 0 ? ' w' . floor(99 / $mainmod3_count) : '';
?>