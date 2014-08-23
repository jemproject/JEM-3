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
 * Table: Groups
 */
class JEMTableGroups extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jem_groups', 'id', $db);
	}


	// overloaded check function
	function check()
	{
		// Not typed in a category name?
		if (trim($this->name ) == '') {
			$this->setError(JText::_('COM_JEM_ADD_GROUP_NAME'));
			return false;
		}

		// Set alias
		//$this->alias = JApplication::stringURLSafe($this->alias);
		//if (empty($this->alias)) {
		//	$this->alias = JApplication::stringURLSafe($this->title);
		//}

		return true;
	}


	/**
	 * Store.
	 */
	public function store($updateNulls = false)
	{
		return parent::store($updateNulls);
	}


	public function bind($array, $ignore = '')
	{
		// in here we are checking for the empty value of the checkbox

		//don't override without calling base class
		return parent::bind($array, $ignore);
	}
}
?>