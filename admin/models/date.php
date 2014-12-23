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
 * Model: Date
 */
class JemModelDate extends JModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id)) {
	
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_jem');
		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid)){
			return $user->authorise('core.edit.state', 'com_jem.category.'.(int) $record->catid);
		} else {
			return $user->authorise('core.edit.state', 'com_jem');
		}
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 *
	 */
	public function getTable($type = 'Dates', $prefix = 'JEMTable', $config = array())
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
		$form = $this->loadForm('com_jem.date', 'date', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		if ($this->getState('date.id')) {
			$pk = $this->getState('date.id');
			$items = $this->getItem($pk);
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
		$jemsettings = JEMAdmin::config();

		if ($item = parent::getItem($pk)){
		
		}
		
		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jem.edit.date.data', array());

		if (empty($data)){
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param $table JTable-object.
	 */
	protected function prepareTable($table)
	{
		$db = $this->getDbo();
		//$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);

		// Increment version number.
		//$table->version ++;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param $data array
	 */
	public function save($data)
	{
		// Variables
		$date 			= JFactory::getDate();
		$jinput 		= JFactory::getApplication()->input;
		$user 			= JFactory::getUser();
		$jemsettings 	= JEMHelper::config();
		$app 			= JFactory::getApplication();
		$fileFilter 	= new JInput($_FILES);
		$table 			= $this->getTable();

		// Check if we're in the front or back
		if ($app->isAdmin())
			$backend = true;
		else
			$backend = false;

		
	
		if (parent::save($data)){
			// At this point we do have an id.
			$pk = $this->getState($this->getName() . '.id');


			return true;
		}

		return false;
	}

	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function setstatusdate($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
	
		if (empty($pks)) {
			$this->setError(JText::_('COM_JEM_DATES_NO_ITEM_SELECTED'));
			return false;
		}
	
		try {
			$db = $this->getDbo();
	
			$db->setQuery(
					'UPDATE #__jem_dates' .
					' SET enabled = '.(int) $value.
					' WHERE id IN ('.implode(',', $pks).')'
			);
			if (!$db->execute()) {
				throw new Exception($db->getErrorMsg());
			}
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
		$this->cleanCache();
	
		return true;
	}
	
	
}