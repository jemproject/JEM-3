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
 * View: Date
 */
class JEMViewDate extends JViewLegacy {

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
		JHtml::_('behavior.framework');
		
		// initialise variables
		$jemsettings 	= JEMHelper::config();
		$document		= JFactory::getDocument();
		$this->settings	= JEMAdmin::config();
		$task			= JFactory::getApplication()->input->get('task');
		$this->task 	= $task;
		$url 			= JUri::root();

		
		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);


		// JQuery noConflict
		$document->addCustomTag('<script type="text/javascript">jQuery.noConflict();</script>');
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');

		$this->jemsettings	= $jemsettings;

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= JEMHelperBackend::getActions();

		JToolBarHelper::title($isNew ? JText::_('COM_JEM_DATE_ADD') : JText::_('COM_JEM_DATE_EDIT'), 'eventedit');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||$canDo->get('core.create'))) {
			JToolBarHelper::apply('date.apply');
			JToolBarHelper::save('date.save');
		}
		if (!$checkedOut && $canDo->get('core.create')) {
			JToolBarHelper::save2new('date.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('date.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('date.cancel');
		} else {
			JToolBarHelper::cancel('date.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('editevents', true);
	}
}
?>