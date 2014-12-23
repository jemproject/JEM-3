<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Table: Recurrence
 */
class JEMTableRecurrence extends JTable
{
	public function __construct(&$db) {
		parent::__construct('#__jem_recurrence', 'id', $db);
	}

	/**
	 * Bind
	 */
	public function bind($array, $ignore = ''){
		

		return parent::bind($array, $ignore);
	}


	/**
	 * Check
	 */
	function check()
	{

		return true;
	}

	/**
	 * Store
	 */
	public function store($updateNulls = true)
	{

		return parent::store($updateNulls);
	}

	
}
?>