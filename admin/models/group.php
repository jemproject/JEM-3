<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Model: Group
 */
class JemModelGroup extends JModelAdmin
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param type	The table type to instantiate
	 * @param string	A prefix for the table class name. Optional.
	 * @param array	Configuration array for model. Optional.
	 * @return JTable	A database object
	 *
	 */
	public function getTable($type = 'Groups', $prefix = 'JEMTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jem.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 */
	protected function loadFormData()
	{

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jem.edit.group.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * With $table you can call a table name
	 *
	 */
	protected function prepareTable($table)
	{
		$db = JFactory::getDbo();

		// Bind the form fields to the table
// 		if (!$table->bind($jinput->getArray($_POST))) {
// 			return JError::raiseWarning(500, $table->getError());
// 		}

		// Make sure the data is valid
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store data
		if (!$table->store(true)) {
			JError::raiseError(500, $table->getError());
		}

		$members = JFactory::getApplication()->input->post->get('maintainers',  '', 'array');

		// Updating group references
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__jem_groupmembers'));
		$query->where('group_id = '.$table->id);

		$db->setQuery($query);
		$db->execute();

		if ($members) {
			foreach($members as $member)
			{
				$member = intval($member);
			
				$query = $db->getQuery(true);
				$columns = array('group_id', 'member');
				$values = array($table->id, $member);
			
				$query
				->insert($db->quoteName('#__jem_groupmembers'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			
				$db->setQuery($query);
				$db->execute();
			}
		}
		
	}

	/**
	 * Method to get the members data
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function &getMembers()
	{
		$members = $this->_members();

		$users = array();

		if ($members) {

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('id AS value','username','name'));
			$query->from('#__users');
			$query->where(array('id IN ('.$members.')'));
			$query->order('name ASC');

			$db->setQuery($query);

			$users = $db->loadObjectList();

			for($i=0; $i < count($users); $i++) {
			$item = $users[$i];

			$item->text = $item->name.' ('.$item->username.')';
			}

		}
		return $users;
	}

	/**
	 * Method to get the selected members
	 *
	 * @access	public
	 * @return	string
	 *
	 */
	protected function _members()
	{
		$item = parent::getItem();

		//get selected members
		if ($item->id == null) {
			$this->_members = null;
		} else {
			if ($item->id) {

				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select(array('member'));
				$query->from('#__jem_groupmembers');
				$query->where(array('group_id = '.$item->id));

				$db->setQuery ($query);

				$member_ids = $db->loadColumn();

				if (is_array($member_ids)) {
					$this->_members = implode(',', $member_ids);
				}
			}
		}

		return $this->_members;
	}


	/**
	 * Method to get the available users
	 *
	 * @access	public
	 * @return	mixed
	 *
	 */
	function &getAvailable()
	{
		$members = $this->_members();

		# get non selected members
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id AS value','username','name'));
		$query->from('#__users');

		if ($members) {
			$query->where(array('block = 0 ','id NOT IN ('.$members.')'));
		} else {
			$query->where(array('block = 0 '));
		}

		$query->order('name ASC');

		$db->setQuery($query);

		$this->_available = $db->loadObjectList();

		for($i=0, $n=count($this->_available); $i < $n; $i++) {
			$item = $this->_available[$i];

			$item->text = $item->name.' ('.$item->username.')';
		}

		return $this->_available;
	}
}
