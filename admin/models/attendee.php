<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Model: Attendee
 */
class JemModelAttendee extends JModelAdmin
{
	/**
	 * attendee id
	 */
	var $_id = null;

	/**
	 * Category data array
	 */
	var $_data = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}


	function toggle($id)
	{
		$row = JTable::getInstance('Register', 'JEMTable');
		$row->load($id);
		$row->waiting = $row->waiting ? 0 : 1;
		return $row->store();
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
	public function getTable($type = 'Register', $prefix = 'JEMTable', $config = array())
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
		$form = $this->loadForm('com_jem.attendee', 'attendee', array('control' => 'jform', 'load_data' => $loadData));
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
		
		$id = $this->getState('attendee.id');
		$jemsettings = JEMAdmin::config();
		$db		= JFactory::getDbo();
		
		if ($item = parent::getItem($pk)){
			
			if (!is_null($item->id)) {
				$query = $db->getQuery(true);
				$query->select(array('name'));
				$query->from('#__users');
				$query->where('id = '.$item->uid);
				$username = $db->setQuery($query);
				$item->username = $db->loadResult();
			}		
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
		$data = JFactory::getApplication()->getUserState('com_jem.edit.attendee.data', array());
	
		if (empty($data)){
			$data = $this->getItem();
		}
	
		return $data;
	}
	
	
	/**
	 * Method to save the form data.
	 *
	 * @param $data array
	 */
	public function save($data)
	{
			
		$app 			= JFactory::getApplication();
		$jinput 		= $app->input;
		$user 			= JFactory::getUser();
		$jemsettings 	= JEMHelper::config();
		$settings 		= JemHelper::globalattribs();
		$table 			= $this->getTable();
		$db 			= JFactory::getDbo();
	
		$eventid 		= $jinput->get('eid');
		$id				= $data['id'];
		$data['uregdate'] = gmdate('Y-m-d H:i:s');
		
		# new attendee
		if ($id == 0) {
			
			$data['uregdate']	= gmdate('Y-m-d H:i:s');
			$data['event'] 		= $eventid;
				
			// Get eventdata
			$query = $db->getQuery(true);
			$query->select(array('maxplaces','waitinglist'));
			$query->from('#__jem_events');
			$query->where('id= '.$db->quote($eventid));
			
			$db->setQuery($query);
			$event = $db->loadObject();
			
			// Get register information of the event
			$query = $db->getQuery(true);
			$query->select(array('COUNT(id) AS registered', 'COALESCE(SUM(waiting), 0) AS waiting'));
			$query->from('#__jem_register');
			$query->where('event = '.$db->quote($eventid));
			
			$db->setQuery($query);
			$register = $db->loadObject();
			
			// If no one is registered yet, $register is null!
			if(is_null($register)) {
				$register = new stdclass;
				$register->registered = 0;
				$register->waiting = 0;
				$register->booked = 0;
			} else {
				$register->booked = $register->registered - $register->waiting;
			}
			
			// put on waiting list ?
			if ($event->maxplaces > 0) // there is a max
			{
				// check if the user should go on waiting list
				if ($register->booked >= $event->maxplaces)
				{
					if (!$event->waitinglist) {
						/*JError::raiseWarning(0, JText::_('COM_JEM_ERROR_REGISTER_EVENT_IS_FULL'));*/
						/*return false;*/
					}
					$data['waiting'] = 1;
				}
			}	
		}
		
		if (parent::save($data)){
	
			// At this point we do have an id.
			$pk = $this->getState($this->getName() . '.id');
				
			
			if (!isset($data['sendmail'])) {
				$data['sendmail'] = 0;
			}
			
			if ($data['sendmail'] == 1) {
				JPluginHelper::importPlugin('jem');
				$dispatcher = JEventDispatcher::getInstance();
				$dispatcher->trigger('onEventUserRegistered', array($pk));
			}
			
			return true;
		}
	
		return false;
	}
}
?>