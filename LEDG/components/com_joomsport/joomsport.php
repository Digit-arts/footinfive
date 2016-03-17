<?php
/**
 http://www.BearDev.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

//require_once(JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested$task = JRequest::getVar('task', null, 'default', 'cmd');$tmpl = JRequest::getVar( 'tmpl', '', 'get', 'string' );if($task != 'add_comment' && $task != 'del_comment' && $task != 'get_format' && $tmpl != 'component'){	echo '<div id="wr-module">';}
if($controller = JRequest::getWord('controller')) {	
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path) && $controller) {
		require_once $path;
	} else {
		$controller = '';		require_once(JPATH_COMPONENT.DS.'controller.php');
	}
}else {		$controller = '';		require_once(JPATH_COMPONENT.DS.'controller.php');	}

// Create the controller
$classname	= 'JoomsportController'.ucfirst($controller);
$controller = new $classname( );
$seasid = JRequest::getVar( 'seasid', 0, '', 'int' );$sid = JRequest::getVar( 'sid', 0, '', 'int' );if($seasid && !$sid){	JRequest::setVar( 'sid', $seasid );}
// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect(); ?><?php if($task != 'add_comment' && $task != 'del_comment' && $task != 'get_format' && $tmpl != 'component'){?>	<!-- <corner> -->	<div class="wr-module-corner tl"><!-- --></div>	<div class="wr-module-corner tr"><!-- --></div>	<div class="wr-module-corner bl"><!-- --></div>	<div class="wr-module-corner br"><!-- --></div><!-- </corner> --></div><?php } ?>