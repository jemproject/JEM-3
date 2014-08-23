<?php
/**
 * @version 3.0.2
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
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
	function check()
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
		

		//don't override without calling base class
		return parent::bind($array, $ignore);
	}
}
?>