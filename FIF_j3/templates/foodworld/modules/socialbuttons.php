<?php     
 $facebook	=	htmlspecialchars($this->params->get('facebook')); 
 $twitter	=	htmlspecialchars($this->params->get('twitter')); 
 $gplus	=	htmlspecialchars($this->params->get('gplus'));  
?> 

<a href="<?php echo htmlspecialchars($facebook); ?>"><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/facebook.png" width="32" height="32" alt="f" ></a>
<a href="<?php echo htmlspecialchars($twitter); ?>"><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/twitter.png" alt="t" ></a>
<a href="<?php echo htmlspecialchars($gplus); ?>"><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/gplus.png" alt="g" ></a>




