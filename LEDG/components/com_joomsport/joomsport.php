<?php
/**
 http://www.BearDev.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');



// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path) && $controller) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'JoomsportController'.ucfirst($controller);
$controller = new $classname( );
$seasid = JRequest::getVar( 'seasid', 0, '', 'int' );
// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect();