<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Venues
 */
class JEMViewVenues extends JViewLegacy
{

	protected $items;
	protected $pagination;
	protected $state;

    public function display($tpl = null)
    {
		$user 		= JFactory::getUser();
		$document	= JFactory::getDocument();
		$url 		= JUri::root();
		$settings 	= JEMHelper::globalattribs();

        // Initialise variables.
        $this->items			= $this->get('Items');
        $this->pagination		= $this->get('Pagination');
		$this->state			= $this->get('State');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		
		$this->columns			= $this->get('Columns');

		$this->settings			= $settings;
		$params 				= $this->state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		$this->user = $user;

		# add toolbar
		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add Toolbar
	 */
    protected function addToolbar()
    {
		JToolBarHelper::title(JText::_('COM_JEM_VENUES'), 'venues');
		
		$canDo = JEMHelperBackend::getActions('com_jem', 'venue', 0);

		/* create */
		if (($canDo->get('core.create'))) {
			JToolBarHelper::addNew('venue.add');
		}

		/* edit */
		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('venue.edit');
			JToolBarHelper::divider();
		}

		/* state */
		if ($canDo->get('core.edit.state')) {
			if ($this->state->get('filter.published') != 2) {
				JToolBarHelper::publishList('venues.publish');
				JToolBarHelper::unpublishList('venues.unpublish');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::checkin('venues.checkin');
		}

		/* delete-trash */
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('COM_JEM_CONFIRM_DELETE', 'venues.remove', 'JACTION_DELETE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('listvenues', true);
    }
}
