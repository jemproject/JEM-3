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
 * View: Dates
 */
 class JEMViewDates extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$user 		= JFactory::getUser();
		$document	= JFactory::getDocument();
		$settings 	= JEMHelper::globalattribs();

		$jemsettings = JEMAdmin::config();
		$url 		= JUri::root();

		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Retrieving params
		$params = $this->state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// loading Mootools
		JHtml::_('behavior.framework');

		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// Load Scripts
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
		$document->addCustomTag('<script type="text/javascript">jQuery.noConflict();</script>');

		// assign data to template
		$this->user			= $user;
		$this->jemsettings  = $jemsettings;
		$this->settings		= $settings;

		// add toolbar
		$this->addToolbar();

		parent::display($tpl);
		}


	/**
	 * Add Toolbar
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/helper.php';
		JToolBarHelper::title(JText::_('COM_JEM_DATES'), 'events');

		/* retrieving the allowed actions for the user */
		$canDo = JEMHelperBackend::getActions(0);

		/* create */
		if (($canDo->get('core.create'))) {
			JToolBarHelper::addNew('date.add');
		}

		/* edit */
		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('date.edit');
			JToolBarHelper::divider();
		}
		
		
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'dates.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		
		/*
		if ($canDo->get('core.edit.state')) {
			if ($this->state->get('filter_state') != 2) {
				JToolBarHelper::publishList('dates.publish', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::unpublishList('dates.unpublish', 'JTOOLBAR_UNPUBLISH', true);
				//JToolBarHelper::custom('events.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			}

			if ($this->state->get('filter_state') != -1) {
				JToolBarHelper::divider();
				if ($this->state->get('filter_state') != 2) {
					JToolBarHelper::archiveList('dates.archive');
				} elseif ($this->state->get('filter_state') == 2) {
					JToolBarHelper::unarchiveList('dates.publish');
				}
			}
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::checkin('dates.checkin');
		}

		if ($this->state->get('filter_state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'dates.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('dates.trash');
		}
		
		*/
		
		//JToolBarHelper::divider();
		//JToolBarHelper::custom('events.removeRecurrenceset','calendar_delete','','Recurrenceset');
		//JToolBarHelper::trash('events.removeRecurrenceset','Recurrenceset');
		//JToolBarHelper::divider();
		JToolBarHelper::help('listevents', true);
	}
}
?>