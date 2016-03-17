<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: SystemWarpHelper
		Joomla! system helper class, provides Joomla! 1.7 CMS integration (http://www.joomla.org)
*/
class SystemWarpHelper extends WarpHelper {

	/* application */
	public $application;

	/* document */
	public $document;

	/* language */
	public $language;

	/* system path */
	public $path;

	/* system url */
	public $url;

	/* cache path */
	public $cache_path;

	/* cache time */
	public $cache_time;

	/* configuration */
	public $config;
	
	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct() {
		parent::__construct();		

		// init vars
		$this->application = JFactory::getApplication();
        $this->document    = JFactory::getDocument();
		$this->language    = JFactory::getLanguage();
        $this->path        = JPATH_ROOT;
        $this->url         = rtrim(JURI::root(false), '/');
        $this->cache_path  = $this->path.'/cache/template';
        $this->cache_time  = max(JFactory::getConfig()->getValue('config.cachetime') * 60, 86400);

		// set config or load defaults
		$file = $this['path']->path('template:config');
		$this->config = $this['data']->create(file_get_contents($file ? $file : $this['path']->path('template:config.default')));

		// set cache directory
		if (!file_exists($this->cache_path)) {
			JFolder::create($this->cache_path);
		}
	}

	/*
		Function: init
			Initialize system configuration

		Returns:
			Void
	*/
	public function init() {

		// set paths
        $this['path']->register($this->path.'/administrator', 'admin');
        $this['path']->register($this->path, 'site');
        $this['path']->register($this->path.'/cache/template', 'cache');
		$this['path']->register($this['path']->path('warp:systems/joomla/menus'), 'menu');

		// set translations
		$this->language->load('tpl_warp', $this['path']->path('warp:systems/joomla'), null, true);
		
		// is site ?
		if ($this->application->isSite()) {

			// set config
			$this->config->set('language', $this->document->language); 
			$this->config->set('direction', $this->document->direction); 
			$this->config->set('site_url', rtrim(JURI::root(), '/'));
			$this->config->set('site_name', $this->application->getCfg('sitename'));
			$this->config->set('datetime', JHTML::_('date', 'now', 'Y-m-d'));			
			$this->config->set('actual_date', JHTML::_('date', 'now', JText::_('DATE_FORMAT_LC')));
			$this->config->set('page_class', trim(preg_replace(array('/columns-(\d+)/', '/columnwidth-(\d+)/'), array('', ''), $this->application->getParams()->get('pageclass_sfx')))); 

			// IE6 page ?
			if ($this['config']->get('ie6page') && $this['browser']->isIE6()) {
				$this['event']->bind('render.layouts:template', create_function('&$layout,&$args', '$args["title"] = JText::_("TPL_WARP_IE6_PAGE_TITLE"); $args["error"] = "browser"; $args["message"] = JText::_("TPL_WARP_IE6_PAGE_MESSAGE"); $layout = "layouts:error";'));
			}

			// mobile theme ?
			if ($this['config']->get('mobile') && $this['browser']->isMobile()) {
				$this['config']->set('style', 'mobile');
			}

			// branding ?
			if ($this['config']->get('warp_branding')) {
				$this['template']->set('warp_branding', $this->warp->getBranding());
			}

			// set theme style paths
			if ($style = $this['config']->get('style')) {
				foreach (array('css' => 'template:styles/%s/css', 'js' => 'template:styles/%s/js', 'layouts' => 'template:styles/%s/layouts') as $name => $resource) {
					if ($p = $this['path']->path(sprintf($resource, $style))) {
						$this['path']->register($p, $name);
					}
				}
			}
			
			//force show system output on search results
			if(strtolower(JRequest::getVar('option')) == "com_search"){
				$this['config']->set('system_output', 1);
			}
			
        }
		
		// is admin ?
		if ($this->application->isAdmin()) {

			// set paths
	        $this['path']->register($this['path']->path('warp:config'), 'config');
	        $this['path']->register($this['path']->path('warp:systems/joomla/config'), 'config');

			// get xml's
			$tmpl_xml = $this['dom']->create($this['path']->path('template:templateDetails.xml'), 'xml');
			$warp_xml = $this['dom']->create($this['path']->path('warp:warp.xml'), 'xml');

			// cache writable ?
			if (!file_exists($this->cache_path) || !is_writable($this->cache_path)) {
				$this->application->enqueueMessage("Cache not writable, please check directory permissions ({$this->cache_path})", 'notice');
			}			

			// update check
			if ($url = $warp_xml->first('updateUrl')->text()) {

				// create check urls
				$urls['tmpl'] = sprintf('%s?application=%s&version=%s&format=raw', $url, $tmpl_xml->first('name')->text().'_j25', $tmpl_xml->first('version')->text());
				$urls['warp'] = sprintf('%s?application=%s&version=%s&format=raw', $url, 'warp', $warp_xml->first('version')->text());

				foreach ($urls as $type => $url) {

					// only check once a day 
					$hash = md5($url.date('Y-m-d'));
					if ($this['option']->get("{$type}_check") != $hash) {
						if ($request = $this['http']->get($url)) {
							$this['option']->set("{$type}_check", $hash);
							$this['option']->set("{$type}_data", $request['body']);
						}
					}

					// decode response and set message
					if (($data = json_decode($this['option']->get("{$type}_data"))) && $data->status == 'update-available') {
						$this->application->enqueueMessage($data->message, 'notice');
					}

				}
			}
		}
		
	}

	/*
		Function: saveConfig

		Returns:
			Boolean
	*/
	public function saveConfig() {

		// init vars
		$config = JRequest::getVar('config', array(), 'post', 'array', JREQUEST_ALLOWRAW);
		$config = array_merge($config, array('profile_data' => JRequest::getVar('profile_data', array())));
		$config = array_merge($config, array('profile_map' => JRequest::getVar('profile_map', array())));
		$file   = $this['path']->path('template:').'/config';
		$data   = $this['data']->create($config);

		// save config file
		echo json_encode(array('message' => (file_put_contents($file, (string) $data) ? 'success' : 'failed')));
	}

	/*
		Function: isBlog

		Returns:
			Boolean
	*/
	public function isBlog() {

		if (JRequest::getCmd('option') == 'com_content') {
			if (in_array(JRequest::getCmd('view'), array('frontpage', 'article', 'archive', 'featured')) || (JRequest::getCmd('view') == 'category' && JRequest::getCmd('layout') == 'blog')) {
				return true;
			}
		}

		if (JRequest::getCmd('option') == 'com_zoo' && !in_array(JRequest::getCmd('task'), array('submission', 'mysubmissions')) && App::getInstance('zoo')->zoo->getApplication()->getGroup() == 'blog') {
			return true;
		}

		return false;
	}

}