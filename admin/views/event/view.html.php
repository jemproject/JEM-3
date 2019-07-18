<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Event
 */
class JemViewEvent extends JViewLegacy {

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

		//initialise variables
		$jemsettings 	= JemHelper::config();
		$document		= JFactory::getDocument();
		$user 			= JFactory::getUser();
		$this->settings	= JemAdmin::config();
		$task			= JFactory::getApplication()->input->get('task');
		$this->task 	= $task;
		$url 			= JUri::root();

		$categories 	= JemCategories::getCategoriesTree(1);
		$selectedcats 	= $this->get('Catsselected');

		$Lists = array();
		$Lists['category'] = JemCategories::buildcatselect($categories, 'cid[]', $selectedcats, 0, 'multiple="multiple" size="8"');

		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// Load scripts
		JHtml::_('script', 'com_jem/attachments.js', false, true);
		JHtml::_('script', 'com_jem/recurrence.js', false, true);
		JHtml::_('script', 'com_jem/seo.js', false, true);
		JHtml::_('script', 'com_jem/slider-state.js', false, true);

		$this->access		= JemHelper::getAccesslevelOptions();
		$this->jemsettings	= $jemsettings;
		$this->Lists 		= $Lists;
		
		$this->canDo	= JEMHelperBackend::getActions('com_jem', 'event', $this->item->id);

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set('hidemainmenu', true);

		$recurrence = $this->item->recurrence_groupcheck;

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= $this->canDo;

		JToolBarHelper::title($isNew ? JText::_('COM_JEM_ADD_EVENT') : JText::_('COM_JEM_EDIT_EVENT'), 'eventedit');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||$canDo->get('core.create'))) {
			JToolBarHelper::apply('event.apply');
			JToolBarHelper::save('event.save');
		}

		if (!$recurrence) {
			if (!$checkedOut && $canDo->get('core.create')) {
				JToolBarHelper::save2new('event.save2new');
			}
			// If an existing item, can save to a copy.
			if (!$isNew && $canDo->get('core.create')) {
				JToolBarHelper::save2copy('event.save2copy');
			}
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('event.cancel');
		} else {
			JToolBarHelper::cancel('event.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('editevents', true);
	}
}
