<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Housekeeping
 */
class JemViewHousekeeping extends JViewLegacy
{

    public function display($tpl = null)
    {

		$app = JFactory::getApplication();

		//only admins have access to this view
		if (!JFactory::getUser()->authorise('core.manage')) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_JEM_ALERTNOTAUTH'));
			$app->redirect('index.php?option=com_jem&view=main');
		}

		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// Load Script
		JHtml::_('behavior.framework');

		// add toolbar
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add Toolbar
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JEM_HOUSEKEEPING'), 'housekeeping');

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolBarHelper::help('housekeeping', true);
	}
}
