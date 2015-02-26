<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Attendee
 */
class JemViewAttendee extends JViewLegacy {

	protected $form;
	protected $item;

	public function display($tpl = null)
	{
		// initialise variables
		$document	= JFactory::getDocument();
		$jinput 	= JFactory::getApplication()->input;

		// get vars
		$eventid = $jinput->getInt('eid');

		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// Get data from the model
		$this->item = $this->get('Item');
        $this->form = $this->get('Form');

		// build selectlists
		$lists = array();
		$lists['users'] = JHtml::_('list.users', 'uid', $this->item->uid, false, null, 'name', 0);

		// assign data to template
		$this->lists 	= $lists;
		$this->eventid 	= $eventid;

		// add toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add Toolbar
	 */
	protected function addToolbar()
	{
		$isNew		= ($this->item->id == 0);
		JToolBarHelper::title($isNew ? JText::_('COM_JEM_ADD_ATTENDEE') : JText::_('COM_JEM_EDIT_ATTENDEE'), 'attendeeedit');

		JToolBarHelper::apply('attendee.apply');
		JToolBarHelper::save('attendee.save');

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('attendee.cancel');
		} else {
			JToolBarHelper::cancel('attendee.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('editattendee', true);
	}
}
