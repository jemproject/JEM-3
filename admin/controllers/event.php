<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Controller: Event
 */
class JEMControllerEvent extends JControllerForm
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_JEM_EVENT';


	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * remove from set
	 */
	function removefromset(){
		//$data  = JFactory::getApplication()->input->get('jform', array(), 'array');
		//$checkin = property_exists($table, 'checked_out');
		//$context = "$this->option.edit.$this->context";
		//$task = $this->getTask();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$key		= $table->getKeyName();
		$urlVar		= $key;
	
		$recordId	= JRequest::getInt($urlVar);
		$recurrence_group = JRequest::getInt('recurrence_group');
	
	
		# Retrieve id of current event from recurrence_table
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__jem_recurrence'));
		$query->where(array('groupid_ref = '.$recurrence_group, 'itemid= '.$recordId));
		$db->setQuery($query);
		$recurrenceid = $db->loadResult();
	
		# Update field recurrence_group in event-table
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->update('#__jem_events');
		$query->set(array('recurrence_group = ""','recurrence_first_id = ""','recurrence_interval = ""','recurrence_type = ""','recurrence_counter = ""','recurrence_limit = ""','recurrence_limit_date = ""','recurrence_byday = ""','recurrence_until = ""','recurrence_freq = ""'));
		$query->where('id = '.$recordId);
		$db->setQuery($query)->query();
	
		# Blank field groupid_ref in recurrence-table and set exdate value
		$recurrence_table	= JTable::getInstance('Recurrence', 'JEMTable');
		$recurrence_table->load($recurrenceid);
					
		$startdate_org_input		= new JDate($recurrence_table->startdate_org);
		$exdate						= $startdate_org_input->format('Ymd\THis\Z');
		$recurrence_table->exdate	= $exdate;
	
		$recurrence_table->groupid_ref = "";
		$recurrence_table->store();
	
		# redirect back
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item. $this->getRedirectToItemAppend($recordId, $urlVar), false));
	}
	
}