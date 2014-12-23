<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

use Recurr\RecurrenceRule;
use Recurr\RecurrenceRuleTransformer;


/**
 * Holds some usefull functions to keep the code a bit cleaner
 */
class JemHelper {


	/**
	 * Pulls settings from database and stores in an static object
	 *
	 * @return object
	 */
	static function viewSettings($view)
	{
		static $settings;
	
		if (!is_object($settings)) {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
	
			$query->select($view);
			$query->from('#__jem_settings');
			$query->where('id = 1');
	
			$db->setQuery($query);
			$settings = $db->loadResult();		
		}
	
		$vregistry = new JRegistry;
		$vregistry->loadString($settings);
	
		return $vregistry;
	}
	
	
	/**
	 * Pulls settings from database and stores in an static object
	 * @return object
	 */
	static function config()
	{
		static $config;

		if (!is_object($config)) {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query->select('*');
			$query->from('#__jem_settings');
			$query->where('id = 1');

			$db->setQuery($query);
			$config = $db->loadObject();

			$config->params = JComponentHelper::getParams('com_jem');
		}

		return $config;
	}


	/**
	 * Pulls settings from database and stores in an static object
	 *
	 * @return object
	 *
	 */
	static function globalattribs()
	{
		static $globalattribs;

		if (!is_object($globalattribs)) {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query->select('globalattribs');
			$query->from('#__jem_settings');
			$query->where('id = 1');

			$db->setQuery($query);
			$globalattribs = $db->loadResult();
		}

		$globalregistry = new JRegistry;
		$globalregistry->loadString($globalattribs);

		return $globalregistry;
	}


	/**
	 * Retrieves the CSS-settings from database and stores in an static object
	 */
	static function retrieveCss()
	{
		static $css;

		if (!is_object($css)) {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query->select('css');
			$query->from('#__jem_settings');
			$query->where('id = 1');

			$db->setQuery($query);
			$css = $db->loadResult();
		}

		$registryCSS = new JRegistry;
		$registryCSS->loadString($css);

		return $registryCSS;
	}


	/**
	 * Performs daily scheduled cleanups
	 *
	 * Currently it archives and removes outdated events
	 */
	static function cleanup($forced = 0)
	{
		
		# run only once (component/modules), non-forced
		if (!$forced) {
			static $counter = 0;
			if ( $counter>0 ) {
				return;
			}
			$counter++;
		}
		
		$jemsettings	= JemHelper::config();

		$now = time();
		$lastupdate = $jemsettings->lastupdate;

		# New day since last update?
		$nrdaysnow = floor($now / 86400);
		$nrdaysupdate = floor($lastupdate / 86400);

		if ($nrdaysnow > $nrdaysupdate || $forced) {

			$db = JFactory::getDbo();
			
			# delete outdated events
			if ($jemsettings->oldevent == 1) {
				$query = $db->getQuery(true);
				$query->update('#__jem_events');
				$query->set('published = -2');
				$query->where(array("dates > 0","DATE_SUB(NOW(), INTERVAL ".$jemsettings->minus." DAY) > (IF (enddates IS NOT NULL, enddates, dates))","published=1"));
				$db->SetQuery($query);
				$db->execute();
			}

			# Set state archived of outdated events
			if ($jemsettings->oldevent == 2) {
				$query = $db->getQuery(true);
				$query->update('#__jem_events');
				$query->set('published = 2');
				$query->where(array("dates > 0","DATE_SUB(NOW(), INTERVAL ".$jemsettings->minus." DAY) > (IF (enddates IS NOT NULL, enddates, dates))","published=1"));
				$db->SetQuery($query);
				$db->execute();
			}

			# Set timestamp of last cleanup
			$query = $db->getQuery(true);
			$query->update('#__jem_settings');
			$query->set('lastupdate = '.time());
			$query->where('id=1');
			$db->SetQuery($query);
			$db->execute();
		}
	}


	/**
	 * Build the select list for access level
	 */
	static function getAccesslevelOptions()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select(array('id AS value','title AS text'));
		$query->from('#__viewlevels');
		$query->order('id');

		$db->setQuery($query);
		$groups = $db->loadObjectList();

		return $groups;
	}


	static function buildtimeselect($max, $name, $selected, $class = array('class'=>'inputbox'))
	{
		$timelist = array();
		$timelist[0] = JHtml::_('select.option', '', '');

		foreach(range(0, $max) as $value) {
			if($value >= 10) {
				$timelist[] = JHtml::_('select.option', $value, $value);
			} else {
				$timelist[] = JHtml::_('select.option', '0'.$value, '0'.$value);
			}
		}
		return JHtml::_('select.genericlist', $timelist, $name, $class, 'value', 'text', $selected);
	}


	/**
	 * returns mime type of a file
	 *
	 * @param string file path
	 * @return string mime type
	 */
	static function getMimeType($filename)
	{
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else if (function_exists('mime_content_type') && 0)
		{
			return mime_content_type($filename);
		}
		else
		{
			$mime_types = array(
				'txt' => 'text/plain',
				'htm' => 'text/html',
				'html' => 'text/html',
				'php' => 'text/html',
				'css' => 'text/css',
				'js' => 'application/javascript',
				'json' => 'application/json',
				'xml' => 'application/xml',
				'swf' => 'application/x-shockwave-flash',
				'flv' => 'video/x-flv',

				// images
				'png' => 'image/png',
				'jpe' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'gif' => 'image/gif',
				'bmp' => 'image/bmp',
				'ico' => 'image/vnd.microsoft.icon',
				'tiff' => 'image/tiff',
				'tif' => 'image/tiff',
				'svg' => 'image/svg+xml',
				'svgz' => 'image/svg+xml',

				// archives
				'zip' => 'application/zip',
				'rar' => 'application/x-rar-compressed',
				'exe' => 'application/x-msdownload',
				'msi' => 'application/x-msdownload',
				'cab' => 'application/vnd.ms-cab-compressed',

				// audio/video
				'mp3' => 'audio/mpeg',
				'qt' => 'video/quicktime',
				'mov' => 'video/quicktime',

				// adobe
				'pdf' => 'application/pdf',
				'psd' => 'image/vnd.adobe.photoshop',
				'ai' => 'application/postscript',
				'eps' => 'application/postscript',
				'ps' => 'application/postscript',

				// ms office
				'doc' => 'application/msword',
				'rtf' => 'application/rtf',
				'xls' => 'application/vnd.ms-excel',
				'ppt' => 'application/vnd.ms-powerpoint',

				// open office
				'odt' => 'application/vnd.oasis.opendocument.text',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
			);

			//$ext = strtolower(array_pop(explode('.',$filename)));
			$var = explode('.',$filename);
			$ext = strtolower(array_pop($var));
			if (array_key_exists($ext, $mime_types)) {
				return $mime_types[$ext];
			}
			else {
				return 'application/octet-stream';
			}
		}
	}

	/**
	 * updates waiting list of specified event
	 *
	 * @param int event id
	 * @param boolean bump users off/to waiting list
	 * @return bool
	 */
	static function updateWaitingList($event)
	{
		$db = Jfactory::getDBO();

		// get event details for registration
		$query = $db->getQuery(true);
		$query->select(array('maxplaces','waitinglist'));
		$query->from('#__jem_events');
		$query->where('id = ' . $db->Quote($event));
		$db->setQuery($query);
		$event_places = $db->loadObject();

		// get attendees after deletion, and their status
		$query = $db->getQuery(true);
		$query->select(array('r.id, r.waiting'));
		$query->from('#__jem_register AS r');
		$query->where('r.event = '.$db->Quote($event));
		$query->order('r.uregdate ASC');
		$db->SetQuery($query);
		$res = $db->loadObjectList();

		$registered = 0;
		$waiting = array();
		foreach ((array) $res as $r)
		{
			if ($r->waiting) {
				$waiting[] = $r->id;
			} else {
				$registered++;
			}
		}

		if ($registered < $event_places->maxplaces && count($waiting))
		{
			// need to bump users to attending status
			$bumping = array_slice($waiting, 0, $event_places->maxplaces - $registered);
			
			$query = $db->getQuery(true);
			$query->update('#__jem_register');
			$query->set('waiting = 0');
			$query->where('id IN ('.implode(',', $bumping).')');	
			$db->setQuery($query);
			
			if (!$db->execute()) {
				$this->setError(JText::_('COM_JEM_FAILED_BUMPING_USERS_FROM_WAITING_TO_CONFIRMED_LIST'));
				Jerror::raisewarning(0, JText::_('COM_JEM_FAILED_BUMPING_USERS_FROM_WAITING_TO_CONFIRMED_LIST').': '.$db->getErrorMsg());
			} else {
				foreach ($bumping AS $register_id)
				{
					JPluginHelper::importPlugin('jem');
					$dispatcher = JEventDispatcher::getInstance();
					$res = $dispatcher->trigger('onUserOnOffWaitinglist', array($register_id));
				}
			}
		}

		return true;
	}

	/**
	 * Adds attendees numbers to rows
	 *
	 * @param $data reference to event rows
	 * @return false on error, $data on success
	 */
	static function getAttendeesNumbers(& $data) {
		// Make sure this is an array and it is not empty
		if (!is_array($data) || !count($data)) {
			return false;
		}

		// Get the ids of events
		$ids = array();
		foreach ($data as $event) {
			$ids[] = $event->id;
		}
		$ids = implode(",", $ids);

		$db = Jfactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('COUNT(id) as total, SUM(waiting) as waitinglist, event');
		$query->from('#__jem_register');
		$query->where('event IN (' . $ids .')');
		$query->group('event');

		$db->setQuery($query);
		$res = $db->loadObjectList('event');

		foreach ($data as $k => $event) {
			if (isset($res[$event->id])) {
				$data[$k]->waiting  = $res[$event->id]->waitinglist;
				$data[$k]->regCount = $res[$event->id]->total - $res[$event->id]->waitinglist;
			} else {
				$data[$k]->waiting  = 0;
				$data[$k]->regCount = 0;
			}
			$data[$k]->available = $data[$k]->maxplaces - $data[$k]->regCount;
		}
		return $data;
	}

	/**
	 * returns timezone name
	 */
	public static function getTimeZoneName() {
		$settings	= self::globalattribs();
		
		$userTz		= JFactory::getUser()->getParam('timezone');
		
		$timeZone	= JFactory::getConfig()->get('offset');

		/* disabled
		 * @todo: change
		if($userTz) {
			$timeZone = $userTz;
		}
		*/
		return $timeZone;
	}


	/**
	 * returns short timezone name
	 */
	public static function getTimeZoneNameShort() {
		# default of server
		date_default_timezone_set(JemHelper::getTimeZoneName());
		$timeZoneShort = date('T');

		return $timeZoneShort;
	}


	/**
	 * returns offset
	 */
	public static function getTimeZoneOffset() {
		$dtz = new DateTimeZone(JemHelper::getTimeZoneNameShort());
		$time = new DateTime('now', $dtz);
		$offset = $dtz->getOffset($time) / 3600;
		if ($offset < 0)
			$offset = $offset;
		else  {
			$offset = "+".$offset;
		}

		return $offset;
	}


	/**
	 * return true is a date is valid (not null, or 0000-00...)
	 *
	 * @param string $date
	 * @return boolean
	 */
	static function isValidDate($date)
	{
		if (is_null($date)) {
			return false;
		}
		if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
			return false;
		}
		if (!strtotime($date)) {
			return false;
		}
		return true;
	}

	/**
	 * return true is a time is valid (not null, or 00:00:00...)
	 *
	 * @param string $time
	 * @return boolean
	 */
	static function isValidTime($time)
	{
		if (is_null($time)) {
			return false;
		}

		if (!strtotime($time)) {
			return false;
		}
		return true;
	}
	
	
	/**
	 * Returns array of positive numbers
	 *
	 * @param string comma separated list of ids
	 * @return mixed array of numbers greater zero or false
	 */
	static function getValidIds($idstring)
	{
		$ids = array();
		$tmp = explode(',', $idstring);
		foreach ($tmp as $id) {
			if ((int)$id > 0) {
				$ids[] = (int)$id;
			}
		}
	
		return (empty($ids) ? false : $ids);
	}
	

	/**
	 * Creates a tooltip
	 */
	static function caltooltip($tooltip, $title = '', $text = '', $href = '', $class = '', $time = '', $color = '') {
		$tooltip = $tooltip;
		$title = $title;
		$titleTip = JHtml::tooltipText($title, $tooltip, true,true);
				
		if ($href) {
			$href = JRoute::_ ($href);
			$tip = '<div class="'.$class.'" title="'.$titleTip.'"><a href="'.$href.'">'.$time.$text.'</a></div>';
		} else {
			$tip = '<div class="'.$class.'" title="'.$titleTip.'">'.$text.'</div>';
		}
		return $tip;
	}

	/**
	 * Function to retrieve IP
	 * @author: https://gist.github.com/cballou/2201933
	 */
	static function retrieveIP() {
		$ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
		foreach ($ip_keys as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					# trim for safety measures
					$ip = trim($ip);
					# attempt to validate IP
					if (self::validate_ip($ip)) {
						return $ip;
					}
				}
			}
		}
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
	}

	/**
	 * Ensures an ip address is both a valid IP and does not fall within
	 * a private network range.
	 *
	 * @author: https://gist.github.com/cballou/2201933
	 */
	static function validate_ip($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
			return false;
		}
		return true;
	}

	/**
	 * Function to load CSS
	 *
	 * @param  $css
	 * @return mixed
	 */

	static function loadCss($css) {

		jimport('joomla.filesystem.file');

		$settings = self::retrieveCss();

		if($settings->get('css_'.$css.'_usecustom','0')) {

			# we want to use custom so now check if we've a file
			$file = $settings->get('css_'.$css.'_customfile');
			$filename = false;


			# something was filled, now check if we've a valid file
			if ($file) {
				$filename	= JPATH_SITE.'/'.$file;
				$filename	= JFile::exists($file);

				if ($filename) {
					# at this point we do have a valid file but let's check the extension too.
					$ext =  JFile::getExt($file);
					if ($ext != 'css') {
						# the file is valid but the extension not so let's return false
						$filename = false;
					}
				}
			}

			if ($filename) {
				# we do have a valid file so we will use it.
				$css = JHtml::_('stylesheet', $file, array(), false);
			} else {
				# unfortunately we don't have a valid file so we're looking at the default
				$css = JHtml::_('stylesheet', 'com_jem/'.$css.'.css', array(), true);
			}
		} else {
			# here we want to use the normal css
			$css = JHtml::_('stylesheet', 'com_jem/'.$css.'.css', array(), true);
		}

		return $css;
	}


	static function defineCenterMap($data = false) {
		# retrieve venue
		$venue		= $data->getValue('venue');

		if ($venue) {
			# latitude/longitude
			$lat 	= $data->getValue('latitude');
			$long	= $data->getValue('longitude');

			if ($lat == 0.000000) {
				$lat = null;
			}

			if ($long == 0.000000) {
				$long = null;
			}

			if ($lat && $long) {
				$location = '['.$data->getValue('latitude').','.$data->getValue('longitude').']';
			} else {
				# retrieve address-info
				$postalCode = $data->getValue('postalCode');
				$city		= $data->getValue('city');
				$street		= $data->getValue('street');

				$address = '"'.$street.' '.$postalCode.' '.$city.'"';
				$location = $address;
			}
			$location = 'location:'.$location.',';
		} else {
			$location = '';
		}

		return $location;
	}

	/**
	 * Load Custom CSS
	 *
	 * @return boolean
	 */
	static function loadCustomCss() {

		$settings = self::retrieveCss();

		$style = "";

		# background-colors
		$bg_filter			= $settings->get('css_color_bg_filter');
		$bg_h2				= $settings->get('css_color_bg_h2');
		$bg_jem				= $settings->get('css_color_bg_jem');
		$bg_table_th		= $settings->get('css_color_bg_table_th');
		$bg_table_td		= $settings->get('css_color_bg_table_td');
		$bg_table_tr_entry2	= $settings->get('css_color_bg_table_tr_entry2');
		$bg_table_tr_hover 	= $settings->get('css_color_bg_table_tr_hover');
		$bg_table_tr_featured = $settings->get('css_color_bg_table_tr_featured');

		if ($bg_filter) {
			$style .= "div#jem #jem_filter {background-color:".$bg_filter.";}";
		}
		if ($bg_h2) {
			$style .= "div#jem h2 {background-color:".$bg_h2.";}";
		}
		if ($bg_jem) {
			$style .= "div#jem {background-color:".$bg_jem.";}";
		}
		if ($bg_table_th) {
			$style .= "div#jem table.eventtable th {background-color:" . $bg_table_th . ";}";
		}
		if ($bg_table_td) {
			$style .= "div#jem table.eventtable td {background-color:" . $bg_table_td . ";}";
		}
		if ($bg_table_tr_entry2) {
			$style .= "div#jem table.eventtable tr.sectiontableentry2 td {background-color:" . $bg_table_tr_entry2 . ";}";
		}
		if ($bg_table_tr_hover) {
			$style .= "div#jem table.eventtable tr:hover td {background-color:" . $bg_table_tr_hover . ";}";
		}
		if ($bg_table_tr_featured) {
			$style .= "div#jem table.eventtable tr.featured td {background-color:" . $bg_table_tr_featured . ";}";
		}

		# border-colors
		$border_filter		= $settings->get('css_color_border_filter');
		$border_h2			= $settings->get('css_color_border_h2');
		$border_table_th	= $settings->get('css_color_border_table_th');
		$border_table_td	= $settings->get('css_color_border_table_td');

		if ($border_filter) {
			$style .= "div#jem #jem_filter {border-color:" . $border_filter . ";}";
		}
		if ($border_h2) {
			$style .= "div#jem h2 {border-color:".$border_h2.";}";
		}
		if ($border_table_th) {
			$style .= "div#jem table.eventtable th {border-color:" . $border_table_th . ";}";
		}
		if ($border_table_td) {
			$style .= "div#jem table.eventtable td {border-color:" . $border_table_td . ";}";
		}

		# font-color
		$font_table_h2		= $settings->get('css_color_font_h2');
		$font_table_td		= $settings->get('css_color_font_table_td');
		$font_table_td_a	= $settings->get('css_color_font_table_td_a');

		if ($font_table_h2) {
			$style .= "div#jem h2 {color:" . $font_table_h2 . ";}";
		}
		if ($font_table_td) {
			$style .= "div#jem table.eventtable td {color:" . $font_table_td . ";}";
		}
		if ($font_table_td_a) {
			$style .= "div#jem table.eventtable td a {color:" . $font_table_td_a . ";}";
		}

		$document 	= JFactory::getDocument();
		$document->addStyleDeclaration($style);

		return true;
	}

	/**
	 * Loads Custom Tags
	 *
	 * @return boolean
	 */

	static function loadCustomTag() {

		$document 	= JFactory::getDocument();
		$tag = "";

		$document->addCustomTag($tag);

		return true;
	}

	/**
	 * get a variable from the manifest cache/params column within the extensions table.
	 *
	 * $column = manifest_cache(1),params(2)
	 * $setting = name of setting to retrieve
	 * $type = compononent(1), plugin(2)
	 * $name = name to search in column name
	 */
	static function getParam($column,$setting,$type,$name,$prefix = false) {

		switch($column) {
			case 1:
				$column = 'manifest_cache';
				break;
			case 2:
				$column = 'params';
				break;
		}

		switch($type) {
			case 1:
				$type = 'component';
				break;
			case 2:
				$type = 'plugin';
				break;
			case 3:
				$type = 'module';
				break;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array($column));
		if ($prefix) {
			$query->from($prefix.'extensions');
		} else {
			$query->from('#__extensions');
		}
		$query->where(array('name = '.$db->quote($name),'type = '.$db->quote($type)));
		$db->setQuery($query);

		$manifest = json_decode($db->loadResult(), true);
		$result = $manifest[$setting];

		if (empty($result)) {
			$result = 'N/A';
		}
		return $result;
	}


	/**
	 * get Holiday-options
	 */
	static function getHolidayOptions($countryType=false, $value_tag = 'value', $text_tag = 'text'){

		$currentValue = '0';

		# Retrieve Country's
		$countryoptions = array();
		$countryoptions = array_merge(JEMHelperCountries::getCountryOptions(),$countryoptions);
		array_unshift($countryoptions, JHtml::_('select.option', '0', JText::_('COM_JEM_SELECT_COUNTRY')));
		$countryoutput = JHtml::_('select.genericlist', $countryoptions, 'countryactivated', null, 'value', 'text', $currentValue);

		# Retrieve Holidays
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('date_name,id');
		$query->from('#__jem_dates');
		$query->where('holiday ='.$db->Quote('1'));

		$db->setQuery($query);
		$holidays = $db->loadObjectList();

		$options = array();
		foreach ($holidays as $holiday) {
			//$name = explode(',', $country['name']);
			$options[] = JHtml::_('select.option', $holiday->id, JText::_($holiday->date_name), $value_tag, $text_tag);
		}

		//$options2 = array();
		//$options2 = array_merge($options,$options2);
		//array_unshift($options2, JHtml::_('select.option', '0', JText::_('COM_JEM_SELECT_HOLIDAY')));


		//$html[] = JHtml::_('select.genericlist', $countryoptions, 'countryactivated', null, 'value', 'text', $currentValue);
		$html[] = JHtml::_('select.genericlist', $options, 'activated[]', 'class="inputbox" size="6" multiple="true"', 'value', 'text', $currentValue);

		return implode("\n", $html);
	}


	/**
	* get Groupset
	**/
	static function getGroupset($rows=false){

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('recurrence_group');
		$query->from($db->quoteName('#__jem_events'));
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$groupsets2 = array();
		foreach ($rows as $row) {
			if ($row->recurrence_group) {
				$groupsets2[] = array('recurrence_group' => $row->recurrence_group);
			}
		}

		$groupsets = self::arrayUnique($groupsets2);

		$options = array();
		foreach ($groupsets as $groupset) {
			$options[] = JHtml::_('select.option', $groupset['recurrence_group'], JText::_($groupset['recurrence_group']));
		}

		return $options;

	}


	/**
	 * Create Unique Arrays using an md5 hash
	 * @link http://phpdevblog.niknovo.com/2009/01/using-array-unique-with-multidimensional-arrays.html
	 *
	 * @param array $array
	 * @return array
	 */
	static function arrayUnique($array, $preserveKeys = false)
	{
		// Unique Array for return
		$arrayRewrite = array();
		// Array with the md5 hashes
		$arrayHashes = array();

		foreach($array as $key => $item) {
		// Serialize the current element and create a md5 hash
			$hash = md5(serialize($item));
			// If the md5 didn't come up yet, add the element to
			// to arrayRewrite, otherwise drop it
			if (!isset($arrayHashes[$hash])) {
			// Save the current element hash
				$arrayHashes[$hash] = $hash;
				// Add element to the unique Array
				if ($preserveKeys) {
					$arrayRewrite[$key] = $item;
				} else {
					$arrayRewrite[] = $item;
				}
			}
		}

		return $arrayRewrite;

	}


	/**
	 * takes care of the recurrence of events
	 */
	static function generate_events($table,$exdates=false,$holidays=false)
	{
		# include route
		require_once (JPATH_COMPONENT_SITE.'/helpers/route.php');

		$jemsettings			= JemHelper::config();
		$weekstart 				= $jemsettings->weekdaystart;
		$anticipation			= $jemsettings->recurrence_anticipation;

		#####################
		## Reference table ##
		#####################

		# this is the events-table and will be used as base

		# define variables
		$id 					= $table->id;
		$times 					= $table->times;
		$endtimes 				= $table->endtimes;
		$dates 					= $table->dates;
		$enddates 				= $table->enddates;
		$recurrence_count		= $table->recurrence_count;
		$recurrence_freq		= $table->recurrence_freq;
		$recurrence_interval	= $table->recurrence_interval;
		$recurrence_until		= $table->recurrence_until;
		$recurrence_weekday		= $table->recurrence_weekday;
		$recurrence_group		= $table->recurrence_group;
		
		# select all the data from the event and make an array of it
		# this info will be used for the generated events.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__jem_events'));
		$query->where('id = '.$table->id);
		$db->setQuery($query);
		$reference = $db->loadAssoc();

		$rruledatetime1 = new DateTime($dates);
		$rruledatetime2 = new DateTime($recurrence_until);
		$rruleinterval	= $rruledatetime1->diff($rruledatetime2);
		$rruleDiff 		= $rruleinterval->format('%a');

		if ($anticipation <= $rruleDiff) {
			$jdate2 			= new JDate($dates);
			$var9				= '+'.$anticipation.' days';
			$anticipationDate	= $jdate2->modify($var9);
			$rruleUntilLimit	= $anticipationDate;
		} else {
			$rruleUntilLimit	= $recurrence_until;
		}

		# Check if startdate is before limitdate
		if (strtotime($dates) <= strtotime($rruleUntilLimit)){

		# combine startdate + time
		if (empty($times)){
			$times = '00:00:00';
		}
		$startDateTime = $dates.' '.$times;

		if (empty($enddates)) {
			$enddates = $dates;
		}

		# combine enddate + time
		if (empty($endtimes)){
			$endtimes = '00:00:00';
		}
		$endDateTime = $enddates.' '.$endtimes;

		# Calculate time difference, for enddate
		$datetime1 = new DateTime($startDateTime);
		$datetime2 = new DateTime($endDateTime);
		$interval = $datetime1->diff($datetime2);

		$diffYear 			= $interval->format('%y');
		$diffMonth			= $interval->format('%m');
		$diffDay			= $interval->format('%d');
		$diffHour			= $interval->format('%h');
		$diffMinutes		= $interval->format('%i');
		$diffSeconds		= $interval->format('%s');
		$diffDays			= $interval->format('days');

		$formatDifference 	= 'P'.$diffYear.'Y'.$diffMonth.'M'.$diffDay.'DT'.$diffHour.'H'.$diffMinutes.'M'.$diffSeconds.'S';

		$jdate1 	= new JDate($rruleUntilLimit);
		$year1 		= $jdate1->format('Y');
		$month1 	= $jdate1->format('m');
		$day1 		= $jdate1->format('d');
		$hour1 		= $jdate1->format('H');
		$minutes1 	= $jdate1->format('i');
		$seconds1 	= $jdate1->format('s');

		$limit_date2 = $year1.$month1.$day1.'T235959Z';
		
		# Define FREQ
		switch($recurrence_freq) {
			case "1":
				$freq = 'DAILY';
				break;
			case "2":
				$freq = 'WEEKLY';
				break;
			case "3":
				$freq = 'MONTHLY';
				break;
			case "4":
				$freq = 'YEARLY';
				break;
			default:
				$freq = '';
		}		
		
		
		# let's check if the user did select a weekday
		if ($recurrence_weekday) {
			$rrule = 'FREQ='.$freq.';INTERVAL='.$recurrence_interval.';UNTIL='.$limit_date2.';BYDAY='.$recurrence_weekday;
		} else {
			$rrule = 'FREQ='.$freq.';INTERVAL='.$recurrence_interval.';UNTIL='.$limit_date2;
		}
		
		# Get new dates
		$timezone    = JemHelper::getTimeZoneName();
		$startDate   = new DateTime($startDateTime, new DateTimeZone($timezone));

		####################
		## RECURR - CLASS ##
		####################

		$rule 		= new RecurrenceRule($rrule, $startDate, $timezone);
		$transformer = new RecurrenceRuleTransformer($rule);

		# here we've the new dates
		$newEventArray = $transformer->getComputedArray();

		# output is like:
		#
		# array
		# 	- public 'Date'
		# 	- public 'timezone_type'
		# 	- public 'timezone'
		
	
		#########
		## END ##
		#########

		$newArray = array();
		foreach($newEventArray as $newEvent) {
			$date = $newEvent->format('Y-m-d');
			$enddate = new DateTime($date);
			$enddate->add(new DateInterval($formatDifference));
			$var2 = $enddate->format('Y-m-d');

			if ($date != $dates){
				$item = array(
					'startDate' => $date,
					'endDate' => $var2
				);
				$newArray[] = $item;
			}

		}


		$newArray2 = array();
		foreach($newEventArray as $newEvent2) {
			$date2 = $newEvent2->format('Y-m-d');
			$enddate2 = new DateTime($date2);
			$enddate2->add(new DateInterval($formatDifference));
			$var22 = $enddate2->format('Y-m-d');
			if ($date2 != $dates){
				$newArray2[] = $date2;
			}
		}


		# retrieve first+last startdate of the array
		$date_first_calculated_occurrence	=	reset($newArray2);
		$date_last_calculated_occurrence	=	end($newArray2);


		###########################
		## IGNORE DATES: HOLIDAY ##
		###########################

		/*
		$currenttime	= new JDate();
		$year 			= $currenttime->format('Y');
		*/

		if ($holidays) {
			$currenttime	= new JDate();
			$year 			= $currenttime->format('Y');
			$format 		= 'd-m-Y';
			$holiday_array 	= array();

			foreach ($holidays as $holiday) {
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from($db->quoteName('#__jem_dates'));
				$query->where(array('id = '.$holiday,'holiday =' .$db->quote('1')));
				$db->setQuery($query);
				$reference2 = $db->loadAssoc();

				if ($reference2['date_startdate_range']) {
					# If we're dealing with a range we've to calculate things

					$start_range_input	= $reference2['date_startdate_range'];
					$end_range_input	= $reference2['date_enddate_range'];

					$start_range_parsed = date_parse($start_range_input);
					$end_range_parsed	= date_parse($end_range_input);

					if (checkdate($start_range_parsed["month"], $start_range_parsed["day"], $start_range_parsed["year"]) && !$start_range_parsed["errors"]
						&& checkdate($end_range_parsed["month"], $end_range_parsed["day"], $end_range_parsed["year"]) && !$end_range_parsed["errors"]) {


						# at this point we made sure we're dealing with valid start+enddates
						# now we're making a DateTimeperiod
						$begin2		= new DateTime($start_range_input);
						$end2		= new DateTime($end_range_input);
						$end2		= $end2->modify('+1 day');
						$interval2	= new DateInterval('P1D');
						$daterange2	= new DatePeriod($begin2, $interval2 ,$end2);

						foreach($daterange2 as $exdate2){
							$holiday_array[] = $exdate2->format("Y-m-d");
						}
					}
				} else {
					# If we're dealing with a single_date we can use the date supplied
					$holiday_array[] = $reference2['date'];
				}

			} // end foreach


			# it's possible to have duplicates so we've to make the array Unique
			$holiday_array = array_unique($holiday_array);
		} // end holiday-check





		####################################################
		## IGNORE DATES: FORM FIELD (exdates), NO HOLIDAY ##
		####################################################

		# basically we add all occurrences of the set to the database but the unneeded ones will
		# get a 1 in the ignore field. Those events will get an exdate in the iCal RRULE output

		# dates provided in the exdate field
		if ($exdates) {
			# remove white space
			$exdates = preg_replace('/\s+/', '', $exdates);
			# put the dates into an array
			$form_exdates_array = explode(",",$exdates);

			$form_exdate_output = array();
			foreach ($form_exdates_array as $form_exdate) {
				$form_exdate_splits = explode(":",$form_exdate);

				$form_exdate_splits2 = array();
				foreach ($form_exdate_splits as $ignoredate) {
					$form_exdate_splits2[] = date("Y-m-d", strtotime($ignoredate));
				}
				
				foreach ($form_exdate_splits2 as $form_exdate_split) {
					
					$date = date_parse($form_exdate_split);
					if (checkdate($date["month"], $date["day"], $date["year"]) && !$date["errors"]) {

						# retrieve first+last value of the created array
						$first_form_exdate	=	reset($form_exdate_splits2);
						$last_form_exdate 	=	end ($form_exdate_splits2);

						# now we're making a DateTimeperiod
						$begin 		= new DateTime($first_form_exdate);
						$end		= new DateTime($last_form_exdate);
						$end		= $end->modify('+1 day');
						$interval	= new DateInterval('P1D');
						$daterange	= new DatePeriod($begin, $interval ,$end);

						foreach($daterange as $exdate){
							$form_exdate_output[] = $exdate->format("Y-m-d");
						}
					}
				}
			}

			# check for duplicates
			$form_exdate_output = array_unique($form_exdate_output);
		} // end check exdates



		#####################################
		## IGNORE-DATES: TABLE, NO HOLIDAY ##
		#####################################

		## select dates from the date-table, within the calculated range
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('date');
		$query->from($db->quoteName('#__jem_dates'));
		$query->where(array('enabled = 1','holiday <> 1','date >= '.$db->Quote($date_first_calculated_occurrence),'date <= '.$db->Quote($date_last_calculated_occurrence)));
		$db->setQuery($query);
		$dateTable = $db->loadColumn();
		
		if ($dateTable) {
			$excluded_dates = $dateTable;
		} else {
			$excluded_dates = array();
		}

		###########################################
		## IGNORE-DATES: CREATE ARRAY, INTERSECT ##
		###########################################

		## $newArray2 = generated values // Y-m-d
		## $excluded_dates = single-dates from table, no holiday

		# we will be making an array that contains the values that are present in exluded_dates + generated_dates
		# this makes sure that the excluded dates are actually in the range of the generating part
		$exclude_date_array		= array_intersect($newArray2, $excluded_dates);

		# if the exdate form is field we will do a second round
		if ($exdates){
			$exclude_exdate_form	= array_intersect($newArray2, $form_exdate_output);
		} else {
			$exclude_exdate_form = false;
		}

		# If a holiday has been selected we will even do a third round
		if ($holidays){
			$exclude_exdate_holiday	= array_intersect($newArray2, $holiday_array);
		} else {
			$exclude_exdate_holiday = false;
		}


		######################################
		## IGNORE-DATES: CREATE ARRAY, DIFF ##
		######################################

		# create array with only the dates we want to have
		$array_input 		= $newArray2;
		$array_to_remove 	= $exclude_date_array;
		$array_output		= array_diff($array_input,$array_to_remove);

		if ($exdates && !$holidays){
			# exdate field has been filled
			$array_input_form 		= $array_output;
			$array_to_remove_form	= $exclude_exdate_form;
			$generating_array		= array_diff($array_input_form,$array_to_remove_form);
		}

		if (!$exdates && $holidays){
			# holiday field has been filled
			$array_input_form 		= $array_output;
			$array_to_remove_form	= $exclude_exdate_holiday;
			$generating_array		= array_diff($array_input_form,$array_to_remove_form);
		}


		if ($exdates && $holidays){
			# both fields have been filled
			# in this case we've to merge both arrays and check for duplicates
			$exdates_holidays_combined 			= array_merge($exclude_exdate_holiday,$exclude_exdate_form);
			$exdates_holidays_combined_unique 	= array_unique($exdates_holidays_combined);

			$array_input_form 		= $array_output;
			$array_to_remove_form	= $exdates_holidays_combined_unique;
			$generating_array		= array_diff($array_input_form,$array_to_remove_form);
		}

		if (!$exdates && !$holidays){
			# no fields have been filled
			$generating_array = $array_output;
		}


		$new_generating_array = array();
		foreach($generating_array as $generated) {
			$generated_enddate = new DateTime($generated);
			$generated_enddate->add(new DateInterval($formatDifference));
			$var2a = $generated_enddate->format('Y-m-d');

			$item2 = array('startDate' => $generated,'endDate' => $var2a);
			$new_generating_array[] = $item2;
		}


		#############
		## EXDATES ##
		#############

		# do we have an array with dates to ignore?
		# if yes we need to input the dates as exdate value in the master-table

		if ($exclude_date_array) {
			$exdate_date_array = $exclude_date_array;
		} else {
			$exdate_date_array = array();
		}

		if ($exclude_exdate_form) {
			$exdate_exdate_form = $exclude_exdate_form;
		} else {
			$exdate_exdate_form = array();
		}

		if ($exclude_exdate_holiday) {
			$exdate_exdate_holiday = $exclude_exdate_holiday;
		} else {
			$exdate_exdate_holiday = array();
		}

		$exdates_input = array_merge($exdate_date_array,$exdate_exdate_form,$exdate_exdate_holiday);
		$exdates_unique = array_unique($exdates_input);

		if ($exdates_unique) {
			$exdates = json_encode($exdates_unique);
		} else {
			$exdates = '';
		}

		#######################################################
		## Store the first occurence to the Recurrence table ##
		#######################################################
		$first_event_recurrence	= JTable::getInstance('Recurrence', 'JEMTable');
		$first_event_recurrence->itemid 		= $table->id;
		$first_event_recurrence->groupid		= $table->recurrence_group;
		$first_event_recurrence->groupid_ref	= $table->recurrence_group;
		$first_event_recurrence->interval 		= $table->recurrence_interval;
		$first_event_recurrence->freq 			= $freq;
		$first_event_recurrence->startdate_org 	= $startDateTime;
		$first_event_recurrence->enddate_org 	= $endDateTime;
		$first_event_recurrence->wholeday 		= $table->wholeday;

		$var2 	= 	$first_event_recurrence->startdate_org;
		$var3	=	new JDate($var2);
		$var4	=	$var3->format('Ymd\THis\Z');

		$first_event_recurrence->recurrence_id	= $var4;
		$first_event_recurrence->store();


		##############################################################
		## Store the first occurence to the Recurrence-Master table ##
		##############################################################

		# define link
		$master_link 		  = JRoute::_(JURI::root().JemHelperRoute::getEventRoute($table->id.':'.$table->alias));

		# Retrieve venue + countryname
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('l.venue, l.city, l.state, l.url, l.street,ct.name AS countryname');
		$query->from($db->quoteName('#__jem_events','a'));
		$query->join('LEFT', '#__jem_venues AS l ON l.id = a.locid');
		$query->join('LEFT', '#__jem_countries AS ct ON ct.iso2 = l.country');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.itemid = a.id');
		$query->join('LEFT', '#__jem_categories AS c ON c.id = rel.catid');
		$query->where(array('a.id ='.$table->id));
		$db->setQuery($query);
		$event_venuecountry = $db->loadObject();

		# define location array
		$location = array();
		if (isset($event_venuecountry->venue) && !empty($event_venuecountry->venue)) {
			$location[] 	= $event_venuecountry->venue;
		}
		if (isset($event_venuecountry->city) && !empty($event_venuecountry->city)) {
			$location[]		= $event_venuecountry->city;
		}
		if (isset($event_venuecountry->state) && !empty($event_venuecountry->state)) {
			$location[]		= $event_venuecountry->state;
		}
		if (isset($event_venuecountry->url) && !empty($event_venuecountry->url)) {
			$location[]		= $event_venuecountry->url;
		}
		if (isset($event_venuecountry->street) && !empty($event_venuecountry->street)) {
			$location[]		= $event_venuecountry->street;
		}
		if (isset($event_venuecountry->countryname) && !empty($event_venuecountry->countryname)) {
			$exp = explode(",",$event_venuecountry->countryname);
			$location[] = $exp[0];
		}
		$location = implode(",", $location);


		# retrieve categories
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('c.id,c.catname');
		$query->from($db->quoteName('#__jem_events','a'));
		$query->join('LEFT', '#__jem_venues AS l ON l.id = a.locid');
		$query->join('LEFT', '#__jem_countries AS ct ON ct.iso2 = l.country');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.itemid = a.id');
		$query->join('LEFT', '#__jem_categories AS c ON c.id = rel.catid');
		$query->where(array('a.id ='.$table->id));
		$db->setQuery($query);
		$event_categories = $db->loadObjectList();

		# create array with category id's
		$categories = array();
		foreach ($event_categories as $c) {
			$categories[] = $c->id;
		}

		# define fields
		$rec_master	= JTable::getInstance('Recurrence_master', 'JEMTable');
		$rec_master->itemid 		= $table->id;
		$rec_master->groupid		= $table->recurrence_group;
		$rec_master->groupid_ref	= $table->recurrence_group;
		$rec_master->interval 		= $table->recurrence_interval;
		$rec_master->freq 			= $freq;
		$rec_master->startdate_org 	= $startDateTime;
		$rec_master->enddate_org 	= $endDateTime;
		$rec_master->exdates		= $exdates;
		$rec_master->store();


		#######################################
		## Bind & Store the generated values ##
		#######################################

		foreach($new_generating_array as $value){
			# load tables
			$new_event 				= JTable::getInstance('Events', 'JEMTable');
			$new_event_recurrence	= JTable::getInstance('Recurrence', 'JEMTable');

			# bind reference-values + strip out individual fields
			$new_event->bind($reference, array('id', 'hits', 'dates', 'enddates','checked_out_time','checked_out'));

			# define new startdate + enddate
			$new_event->dates 		= $value['startDate'];
			$new_event->enddates 	= $value['endDate'];

			# define ical settings
			$new_event->recurrence_until 		= $rruleUntilLimit;
			$new_event->recurrence_count 		= $recurrence_count;
			$new_event->recurrence_interval 	= $recurrence_interval;
			$new_event->recurrence_freq 		= $freq;
			if ($recurrence_weekday){
				$new_event->recurrence_weekday 		= $recurrence_weekday;
			}

			# store event
			if ($new_event->store()){

				# combine startdate+time
				if (empty($new_event->times)){
					$new_event->times = '00:00:00';
				}
				$newevent_startDateTime = $new_event->dates.' '.$new_event->times;

				if (empty($new_event->enddates)) {
					$new_event->enddates = $new_event->dates;
				}

				# combine enddate + time
				if (empty($new_event->endtimes)){
					$new_event->endtimes = '00:00:00';
				}
				$newevent_endDateTime = $new_event->enddates.' '.$new_event->endtimes;

				# store generated event-info into recurrence_table
				$new_event_recurrence->itemid 			= $new_event->id;
				$new_event_recurrence->groupid			= $recurrence_group;
				$new_event_recurrence->groupid_ref		= $recurrence_group;
				$new_event_recurrence->interval 		= $recurrence_interval;
				$new_event_recurrence->startdate_org	= $newevent_startDateTime;
				$new_event_recurrence->enddate_org 		= $newevent_endDateTime;
				$new_event_recurrence->freq				= $freq;
				$new_event_recurrence->wholeday			= $new_event->wholeday;


				$var5 	= 	$new_event_recurrence->startdate_org;
				$var6	=	new JDate($var5);
				$var7	=	$var3->format('Ymd\THis\Z');

				$new_event_recurrence->recurrence_id	= $var7;

				$new_event_recurrence->store();

				$db 	= JFactory::getDbo();
				$query	= $db->getQuery(true);

				$query->select(array('catid'));
				$query->from($db->quoteName('#__jem_cats_event_relations'));
				$query->where('itemid = '.$table->id);
				$db->setQuery($query);
				$cats = $db->loadColumn(0);

				foreach ($cats as $cat){
					$db 	= JFactory::getDbo();
					$query	= $db->getQuery(true);

					// Insert columns.
					$columns = array('catid','itemid');

					// Insert values.
					$values = array($cat,$new_event->id);

					// Prepare the insert query.
					$query->insert($db->quoteName('#__jem_cats_event_relations'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));

					// Reset the query using our newly populated query object.
					$db->setQuery($query);
					$db->execute();
					}
				}
			}

		}  // end adding new Events
	}// end function


	/**
	 * return initialized calendar tool class for ics export
	 *
	 * @return object
	 */
	static function getCalendarTool()
	{
		require_once JPATH_SITE.'/components/com_jem/classes/iCalcreator.class.php';
		$timezone_name 	= JemHelper::getTimeZoneName();
		$config			= JFactory::getConfig();
		$sitename		= $config->get('sitename');

		$config = array( "unique_id" => $sitename, "TZID" => $timezone_name );

		$vcal = new vcalendar($config);
		if (!file_exists(JPATH_SITE.'/cache/com_jem')) {
			jimport('joomla.filesystem.folder');
			JFolder::create(JPATH_SITE.'/cache/com_jem');
		}
		$vcal->setConfig('directory', JPATH_SITE.'/cache/com_jem');
		$vcal->setProperty("calscale", "GREGORIAN");
		$vcal->setProperty('method', 'PUBLISH');
		$vcal->setProperty("X-WR-TIMEZONE", $timezone_name);
		$vcal->setProperty("x-wr-calname", "Calendar");
		$vcal->setProperty("X-WR-CALDESC", "Calendar Description");

		$xprops = array( "X-LIC-LOCATION" => $timezone_name );
		
		if ($timezone_name != 'UTC') {
			iCalUtilityFunctions::createTimezone( $vcal, $timezone_name, $xprops);
		}

		return $vcal;
	}



	static function icalAddEvent(&$calendartool, $event,$rows)
	{
		require_once JPATH_SITE.'/components/com_jem/classes/iCalcreator.class.php';
		$jemsettings	= JemHelper::config();
		$settings 		= JemHelper::globalattribs();
		$config			= JFactory::getConfig();
		$sitename		= $config->get('sitename');
		
		
		# retrieve TimezoneName
		# if we have a timezone for the venue then that info will be used for the output
		
		if ($event->timezone) {
			# venue - timeZone
			$timezone_name 	= $event->timezone;
		} else {
			# global - TimeZone
			$timezone_name	= JemHelper::getTimeZoneName();
		}
		
		// get categories names
		$categories = array();
		foreach ($event->categories as $c) {
			$categories[] = $c->catname;
		}

		// no start date...
		$validdate = JemHelper::isValidDate($event->dates);

		if (!$event->dates || !$validdate) {
			return false;
		}
		// make end date same as start date if not set
		if (!$event->enddates) {
			$event->enddates = $event->dates;
		}

		// start
		if (!preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$event->dates, $start_date)) {
			JError::raiseError(0, JText::_('COM_JEM_ICAL_EXPORT_WRONG_STARTDATE_FORMAT'));
		}
		$date = array('year' => (int) $start_date[1], 'month' => (int) $start_date[2], 'day' => (int) $start_date[3]);

		// all day event if start time is not set
		if (!$event->times) // all day !
		{
			$dateparam = array('VALUE' => 'DATE');

			// for ical all day events, dtend must be send to the next day
			$event->enddates = strftime('%Y-%m-%d', strtotime($event->enddates.' +1 day'));

			if (!preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$event->enddates, $end_date)) {
				JError::raiseError(0, JText::_('COM_JEM_ICAL_EXPORT_WRONG_ENDDATE_FORMAT'));
			}
			$date_end = array('year' => $end_date[1], 'month' => $end_date[2], 'day' => $end_date[3]);
			$dateendparam = array('VALUE' => 'DATE');
		}
		else // not all day events, there is a start time
		{
			if (!preg_match('/([0-9]{2}):([0-9]{2}):([0-9]{2})/',$event->times, $start_time)) {
				JError::raiseError(0, JText::_('COM_JEM_ICAL_EXPORT_WRONG_STARTTIME_FORMAT'));
			}
			$date['hour'] = $start_time[1];
			$date['min']  = $start_time[2];
			$date['sec']  = $start_time[3];
			$dateparam = array('VALUE' => 'DATE-TIME');
			if ($settings->get('ical_tz',0) == 1) {
				$dateparam['TZID'] = $timezone_name;
			}

			if (!$event->endtimes || $event->endtimes == '00:00:00') {
				$event->endtimes = $event->times;
			}

			// if same day but end time < start time, change end date to +1 day
			if ($event->enddates == $event->dates
			&& strtotime($event->dates.' '.$event->endtimes) < strtotime($event->dates.' '.$event->times)) {
				$event->enddates = strftime('%Y-%m-%d', strtotime($event->enddates.' +1 day'));
			}

			if (!preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$event->enddates, $end_date)) {
				JError::raiseError(0, JText::_('COM_JEM_ICAL_EXPORT_WRONG_ENDDATE_FORMAT'));
			}
			$date_end = array('year' => $end_date[1], 'month' => $end_date[2], 'day' => $end_date[3]);

			if (!preg_match('/([0-9]{2}):([0-9]{2}):([0-9]{2})/',$event->endtimes, $end_time)) {
				JError::raiseError(0, JText::_('COM_JEM_ICAL_EXPORT_WRONG_STARTTIME_FORMAT'));
			}
			$date_end['hour'] = $end_time[1];
			$date_end['min']  = $end_time[2];
			$date_end['sec']  = $end_time[3];
			$dateendparam = array('VALUE' => 'DATE-TIME');
			if ($settings->get('ical_tz') == 1) {
				$dateendparam['TZID'] = $timezone_name;
			}
		}

		// item description text
		$description = $event->title.'\\n';
		$description .= JText::_('COM_JEM_CATEGORY').': '.implode(', ', $categories).'\\n';

		$link = JURI::root().JemHelperRoute::getEventRoute($event->slug);
		$link = JRoute::_($link);
		$description .= JText::_('COM_JEM_ICS_LINK').': '.$link.'\\n';

		// location
		$location = array($event->venue);
		if (isset($event->street) && !empty($event->street)) {
			$location[] = $event->street;
		}

		if (isset($event->postalCode) && !empty($event->postalCode) && isset($event->city) && !empty($event->city)) {
			$location[] = $event->postalCode.' '.$event->city;
		} else {
			if (isset($event->postalCode) && !empty($event->postalCode)) {
				$location[] = $event->postalCode;
			}
			if (isset($event->city) && !empty($event->city)) {
				$location[] = $event->city;
			}
		}

		if (isset($event->countryname) && !empty($event->countryname)) {
			$exp = explode(",",$event->countryname);
			$location[] = $exp[0];
		}
		$location = implode(",", $location);

		$e = new vevent();
		$e->setProperty('summary', $event->title);
		$e->setProperty('categories', implode(', ', $categories));
		$e->setProperty('dtstart', $date, $dateparam);
		if (count($date_end)) {
			$e->setProperty('dtend', $date_end, $dateendparam);
		}
		$e->setProperty('description', $description);
		if ($location != '') {
			$e->setProperty('location', $location);
		}
		$e->setProperty('url', $link);
		$e->setProperty('uid', 'event'.$event->id.'@'.$sitename);
		$calendartool->addComponent($e); // add component to calendar
		return true;
	}

	/**
	 * Sanitize the filename and return an unique string
	 *
	 * @param string $base_Dir the target directory
	 * @param string $filename the unsanitized filename
	 *
	 * @return string $filename the sanitized and unique filename
	 */
	static function sanitize($base_Dir, $filename) {
		jimport('joomla.filesystem.file');

		# check for any leading/trailing dots and remove them (trailing shouldn't be possible cause of the getEXT check)
		$filename = preg_replace("/^[.]*/", '', $filename);
		$filename = preg_replace("/[.]*$/", '', $filename); //shouldn't be necessary, see above

		# we need to save the last dot position cause preg_replace will also replace dots
		$lastdotpos = strrpos($filename, '.');

		# replace invalid characters
		$filename = strtolower(preg_replace("/[^0-9a-zA-Z_-]/", '_', $filename));

		# get the parts before and after the dot (assuming we have an extension...check was done before)
		$beforedot	= substr($filename, 0, $lastdotpos);
		$afterdot 	= substr($filename, $lastdotpos + 1);

		# make a unique filename for the image and check it is not already taken
		# if it is already taken keep trying till success
		$now = rand();

		while(JFile::exists($base_Dir . $beforedot . '_' . $now . '.' . $afterdot)) {
			$now++;
		}

		# create out of the seperated parts the new filename
		$filename = $beforedot . '_' . $now . '.' . $afterdot;

		return $filename;
		}

} // end class
?>
