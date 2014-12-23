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
 * View: Attendees
 */
class JemViewAttendees extends JViewLegacy {
	
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		if($this->getLayout() == 'print') {
			$this->_displayprint($tpl);
			return;
		}

		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// Get data from the model
		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		$this->state			= $this->get('State');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$event 					= $this->get('Event');
		$jinput 				= $app->input;
		$eventid 				= $jinput->getInt('eid');

 		if (JEMHelper::isValidDate($event->dates)) {
			$event->dates = JEMOutput::formatdate($event->dates);
		} else {
			$event->dates = JText::_('COM_JEM_OPEN_DATE');
		}

		// assign to template
		$this->event 		= $event;
		$this->eventid		= $eventid;

		// add toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Prepares the print screen
	 *
	 * @param $tpl
	 *
	 *
	 */
	protected function _displayprint($tpl = null)
	{
		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		$this->items			= $this->get('Items');
		$event 					= $this->get('Event');

		if (JEMHelper::isValidDate($event->dates)) {
			$event->dates = JEMOutput::formatdate($event->dates);
		} else {
			$event->dates = JText::_('COM_JEM_OPEN_DATE');
		}

		// assign data to template
		$this->event = $event;

		parent::display($tpl);
	}


	/**
	 * Add Toolbar
	 */
	protected function addToolbar()
	{
		/* retrieving the allowed actions for the user */
		$canDo = JEMHelperBackend::getActions(0);
		
		JToolBarHelper::title(JText::_('COM_JEM_REGISTERED_USERS'), 'users');

		/* create */
		if (($canDo->get('core.create'))) {
			JToolBarHelper::addNew('attendee.add');
		}
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('COM_JEM_CONFIRM_DELETE', 'attendees.remove', 'COM_JEM_ATTENDEES_DELETE');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('attendees.back', 'back', 'back', JText::_('COM_JEM_ATT_BACK'), false);
		JToolBarHelper::divider();
		JToolBarHelper::help('registereduser', true);
	}
}
?>