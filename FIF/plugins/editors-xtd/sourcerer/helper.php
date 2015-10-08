<?php
/**
 * Plugin Helper File
 *
 * @package         Sourcerer
 * @version         4.1.6
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Plugin that places the Button
 */
class plgButtonSourcererHelper
{
	function __construct(&$params)
	{
		$this->params = $params;
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function render($name)
	{
		$button = new JObject;

		if (JFactory::getApplication()->isSite()) {
			$enable_frontend = $this->params->enable_frontend;
			if (!$enable_frontend) {
				return $button;
			}
		}

		JHtml::_('behavior.modal');

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
		$sversion = NoNumberVersions::getXMLVersion('sourcerer', 'editors-xtd', null, 1);

		JHtml::stylesheet('sourcerer/button.min.css', false, true);

		$icon = 'sourcerer';
		if (!$this->params->button_icon) {
			$icon = 'blank blank_sourcerer';
		}

		$link = 'index.php?nn_qp=1'
			. '&folder=plugins.editors-xtd.sourcerer'
			. '&file=sourcerer.inc.php'
			. '&name=' . $name;

		$text_ini = strtoupper(str_replace(' ', '_', $this->params->button_text));
		$text = JText::_($text_ini);
		if ($text == $text_ini) {
			$text = JText::_($this->params->button_text);
		}

		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', trim($text));
		$button->set('name', $icon);
		$button->set('options', "{handler: 'iframe', size: {x:window.getSize().x-100, y: window.getSize().y-100}}");

		return $button;
	}
}
