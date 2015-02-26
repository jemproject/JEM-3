<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Table: Recurrence_master
 */
class JEMTableRecurrence_master extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jem_recurrence_master', 'id', $db);
	}

	/**
	 * bind
	 */
	public function bind($array, $ignore = ''){

		return parent::bind($array, $ignore);
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
	public function store($updateNulls = true)
	{

		return parent::store($updateNulls);
	}
}
