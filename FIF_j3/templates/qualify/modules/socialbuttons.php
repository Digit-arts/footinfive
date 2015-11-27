<?php     
 $facebook	=	htmlspecialchars($this->params->get('facebook')); 
 $twitter	=	htmlspecialchars($this->params->get('twitter')); 
 $gplus	=	htmlspecialchars($this->params->get('gplus'));  
?> 

<ul>
	<a href="<?php echo htmlspecialchars($facebook); ?>"><li><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/facebook.png" width="32" height="32" alt="f" >Like us on Facebook</li></a>
	<a href="<?php echo htmlspecialchars($twitter); ?>"><li><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/twitter.png" alt="t" >Follow us on Twitter</li></a>
	<a href="<?php echo htmlspecialchars($gplus); ?>"><li><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/gplus.png" alt="g" >Follow us on GooglePlus</li></a>
</ul>




