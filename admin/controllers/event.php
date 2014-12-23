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
 * Controller: Event
 */
class JemControllerEvent extends JControllerForm
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
		$model		= $this->getModel();
		$table		= $model->getTable();
		$key		= $table->getKeyName();
		$urlVar		= $key;
		$jinput 	= JFactory::getApplication()->input;
	
		$recordId	= $jinput->getInt($urlVar);
		$recurrence_group = $jinput->getInt('recurrence_group');
	
	
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
		$query->set(array('recurrence_count = ""','recurrence_freq = ""','recurrence_group = ""','recurrence_interval = ""','recurrence_until = ""','recurrence_weekday = ""'));
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