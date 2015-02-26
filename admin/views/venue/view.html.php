<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Venue
 */
class JEMViewVenue extends JViewLegacy {

	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
		$this->form	 = $this->get('Form');
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// initialise variables
		$document		= JFactory::getDocument();
		$app			= JFactory::getApplication();
		$jinput			= $app->input;
		$this->settings	= JEMAdmin::config();
		$this->settings2	= JemHelper::globalattribs();
		$task			= $jinput->getCmd('task');
		$this->task 	= $task;

		# load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		# load js
		//JHtml::_('behavior.framework'); //mootools
		JHtml::_('script', 'com_jem/attachments.js', false, true);
		//$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');

		$this->access		= JEMHelper::getAccesslevelOptions();

		# retrieve mapType setting
		$settings 		= JemHelper::globalattribs();
		$mapType		= $settings->get('mapType','0');

		switch($mapType) {
			case '0':
				$type = '"roadmap"';
				break;
			case '1':
				$type = '"satellite"';
				break;
			case '2':
				$type = '"hybrid"';
				break;
			case '3':
				$type = '"terrain"';
				break;
		}
		$this->mapType = $type;

		//JHtml::_('jquery.framework');
		JHtml::_('script', 'com_jem/slider-state.js', false, true);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= JEMHelperBackend::getActions();

		JToolBarHelper::title($isNew ? JText::_('COM_JEM_ADD_VENUE') : JText::_('COM_JEM_EDIT_VENUE'), 'venuesedit');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||$canDo->get('core.create'))) {
			JToolBarHelper::apply('venue.apply');
			JToolBarHelper::save('venue.save');
		}
		if (!$checkedOut && $canDo->get('core.create')) {
			JToolBarHelper::save2new('venue.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('venue.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('venue.cancel');
		} else {
			JToolBarHelper::cancel('venue.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('editvenues', true);
	}
}
