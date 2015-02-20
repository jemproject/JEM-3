<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Table: Settings
 */
class JEMTableSettings extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jem_settings', 'id', $db);
	}

	/**
	 * check
	 */
	public function check()
	{
		return true;
	}

	/**
	 * store
	 */
	public function store($updateNulls = false)
	{
		return parent::store($updateNulls);
	}

	/**
	 * Bind
	 * @see JTable::bind()
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['globalattribs']) && is_array($array['globalattribs']))
		{
			if (!isset($array['globalattribs']['registering_b'])) {
				$array['globalattribs']['registering_b'] = 0 ;
			}
			
			if (!isset($array['globalattribs']['unregistering_b'])) {
				$array['globalattribs']['unregistering_b'] = 0 ;
			}
			
			$registry = new JRegistry;
			$registry->loadArray($array['globalattribs']);
			$array['globalattribs'] = (string) $registry;
		}

		if (isset($array['css']) && is_array($array['css']))
		{
			$registrycss = new JRegistry;
			$registrycss->loadArray($array['css']);
			$array['css'] = (string) $registrycss;
		}

		if (isset($array['vvenue']) && is_array($array['vvenue']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['vvenue']);
			$array['vvenue'] = (string) $registry;
		}

		if (isset($array['vvenues']) && is_array($array['vvenues']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['vvenues']);
			$array['vvenues'] = (string) $registry;
		}

		if (isset($array['vcategories']) && is_array($array['vcategories']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['vcategories']);
			$array['vcategories'] = (string) $registry;
		}

		if (isset($array['vcategory']) && is_array($array['vcategory']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['vcategory']);
			$array['vcategory'] = (string) $registry;
		}

		if (isset($array['vcalendar']) && is_array($array['vcalendar']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['vcalendar']);
			$array['vcalendar'] = (string) $registry;
		}

		if (isset($array['veditevent']) && is_array($array['veditevent']))
		{
			if (!isset($array['veditevent']['registering'])) {
				$array['veditevent']['registering'] = 0 ;
			}
				
			if (!isset($array['veditevent']['unregistering'])) {
				$array['veditevent']['unregistering'] = 0 ;
			}
			
			$registry = new JRegistry;
			$registry->loadArray($array['veditevent']);
			$array['veditevent'] = (string) $registry;
		}

		//don't override without calling base class
		return parent::bind($array, $ignore);
	}
}
