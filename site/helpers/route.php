<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
require_once(JPATH_SITE.'/components/com_jem/helpers/helper.php');
require_once(JPATH_SITE.'/components/com_jem/classes/categories.class.php');

/**
 * JEM Component Route Helper
 * based on Joomla ContentHelperRoute
 *
 * @static
 * @package		JEM
 *
 */
abstract class JEMHelperRoute
{
	protected static $lookup2;
	const ARTIFICALID = 0;

	/**
	 * Determines an JEM Link
	 *
	 * @param int The id of an JEM item
	 * @param string The view
	 * @param string The category of the item
	 * @deprecated Use specific Route methods instead!
	 *
	 * @return string determined Link
	 */
	public static function getRoute($id, $view = 'event', $category = null)
	{
		// Deprecation warning.
		JLog::add('JEMHelperRoute::getRoute() is deprecated, use specific route methods instead.', JLog::WARNING, 'deprecated');

		$needles = array(
			$view => array((int) $id)
		);

		if ($item = self::_findItem($needles)) {
			$link = 'index.php?Itemid='.$item;
		}
		else {
			// Create the link
			$link = 'index.php?option=com_jem&view='.$view.'&id='. $id;

			// Add category, if available
			if(!is_null($category)) {
				$link .= '&catid='.$category;
			}

			if ($item = self::_findItem($needles)) {
				$link .= '&Itemid='.$item;
			}
			elseif ($item = self::_findItem()) {
				$link .= '&Itemid='.$item;
			}
		}

		return $link;
	}

	public static function getCategoryRoute($id)
	{
		$settings 		= JEMHelper::globalattribs();
		$defaultItemid 	= $settings->get('default_Itemid');
		
		$needles = array(
			'category' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_jem&view=category&id='. $id;

		// If no category view works try categories
		$needles['categories'] = array(self::ARTIFICALID);

		$category = new JEMCategories($id);
		if($category) {
			$needles['categories'] = array_reverse($category->getPath());
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) {	
			if (isset($defaultItemid))
				{
					$link .= '&Itemid='.$defaultItemid;
				} 
		}
		
		return $link;
	}

	public static function getEventRoute($id, $catid = null)
	{
		$settings 		= JEMHelper::globalattribs();
		$defaultItemid 	= $settings->get('default_Itemid');
		
		$needles = array(
			'event' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_jem&view=event&id='. $id;

		// Add category, if available
		if(!is_null($catid)) {
			// TODO
			//$needles['categories'] = $needles['category'];
			$link .= '&catid='.$catid;
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) {	
			$link .= '&Itemid='.$item;
		}
		
		return $link;
	}

	public static function getVenueRoute($id)
	{
		$settings 		= JEMHelper::globalattribs();
		$defaultItemid 	= $settings->get('default_Itemid');
		
		$needles = array(
			'venue' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_jem&view=venue&id='. $id;

		// If no venue view works try venues
		$needles['venues'] = array(self::ARTIFICALID);

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) {	
			$link .= '&Itemid='.$item;
		}
		
		return $link;
	}

	protected static function getRouteWithoutId($my)
	{
		$settings 		= JEMHelper::globalattribs();
		$defaultItemid 	= $settings->get('default_Itemid');
		
		$needles = array();
		$needles[$my] = array(self::ARTIFICALID);

		// Create the link
		$link = 'index.php?option=com_jem&view='.$my;

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) {	
			$link .= '&Itemid='.$item;
		}
		
		return $link;
	}

	public static function getMyAttendancesRoute()
	{
		return self::getRouteWithoutId('myattendances');
	}

	public static function getMyEventsRoute()
	{
		return self::getRouteWithoutId('myevents');
	}

	public static function getMyVenuesRoute()
	{
		return self::getRouteWithoutId('myvenues');
	}


	/**
	 * Determines the Itemid
	 *
	 * searches if a menuitem for this item exists
	 * if not the active menuitem will be returned
	 *
	 * @param array The id and view
	 *
	 *
	 * @return int Itemid
	 */
	protected static function _findItem($needles = null)
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');
		$settings 		= JEMHelper::globalattribs();
		$defaultItemid 	= $settings->get('default_Itemid');

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup2)) {
			self::$lookup2 = array();

			$component = JComponentHelper::getComponent('com_jem');
			$items = $menus->getItems('component_id', $component->id);
					
			// loop trough the menu-items of the component
			if ($items) {
				foreach ($items as $item)
				{
					if (isset($item->query) && isset($item->query['view'])) {
						// skip Calendar-layout
						if (isset($item->query['layout']) && ($item->query['layout'] == 'calendar')) {
							continue; 
						}
						
						// define $view variable
						$view = $item->query['view'];
						
						// skip several views
						if (isset($item->query['view'])) {
							if ($view == 'calendar' || $view == 'search' || $view == 'venues') {
								continue;
							}
						}

						if (!isset(self::$lookup2[$view]))
							self::$lookup2[$view] = array();
						}

						// check for Id's
						if (isset($item->query['id'])) {
							if (!isset(self::$lookup2[$view][$item->query['id']]))
							{
								self::$lookup2[$view][$item->query['id']] = $item->id;
							} 
						} else { 
							// Some views have no ID, but we have to set one
							self::$lookup2[$view][self::ARTIFICALID] = $item->id;
						}
				}
			}
			
			
		}

		// at this point we collected itemid's linking to the component
	

		if ($needles) {
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup2[$view])) {
					foreach($ids as $id)
					{
						if (isset(self::$lookup2[$view][(int)$id])) {
							return self::$lookup2[$view][(int)$id];
						}
					}
				}
			}
		}
		
		
		if ($defaultItemid) {
			return $defaultItemid;
		} else {
			$component = JComponentHelper::getComponent('com_jem');
			$items = $menus->getItems(array('component_id','link'), array($component->id,'index.php?option=com_jem&view=eventslist'),false);
			
			$default = reset($items);
			
			return !empty($default->id) ? $default->id : null;
		}
		
		/*
		$active = $menus->getActive();
		
		if ($active && $active->component == 'com_jem')
		{
			return $active->id;
		}
		*/
		
	}
}
?>