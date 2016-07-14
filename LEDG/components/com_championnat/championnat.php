<?php
defined('_JEXEC') or die('access deny');
jimport('joomla.application.component.controller');

/**
* Creating instance
*/
$controller=JControllerLegacy::getInstance('Championnat');

/**
* Execute task
*/
$controller->execute(JRequest::getCmd('task'));

/**
* Redirect
*/
$controller->redirect();
?>