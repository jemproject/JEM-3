<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Settings
 */
class JEMViewSettings extends JViewLegacy
{
	protected $form;
	protected $data;
	protected $state;

	public function display($tpl = null)
	{
		$form	= $this->get('Form');
		$data	= $this->get('Data');
		$state	= $this->get('State');
		$config = $this->get('ConfigInfo');

		$jemsettings = $this->get('Data');
		$document 	= JFactory::getDocument();

		// Load css
        JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);
        JHtml::_('stylesheet', 'com_jem/colorpicker.css', array(), true);

        $style = '
		    div.current fieldset.radio input {
		        cursor: pointer;
		    }';
		$document->addStyleDeclaration($style);

        // Check for model errors.
        if ($errors = $this->get('Errors')) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

		// Bind the form to the data.
		if ($form && $data) {
			$form->bind($data);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Load Script
		JHtml::_('script', 'com_jem/colorpicker.js', false, true);

		JHtml::_('behavior.framework');

		$app = JFactory::getApplication();

		// only admins have access to this view
		if (!JFactory::getUser()->authorise('core.manage')) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_JEM_ALERTNOTAUTH'));
			$app->redirect('index.php?option=com_jem&view=main');
		}

		// mapping variables

		$this->form = $form;
		$this->data = $data;
		$this->state = $state;
		$this->jemsettings = $jemsettings;
		$this->config		= $config;

		// add toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JEM_SETTINGS_TITLE'), 'settings');
		JToolBarHelper::apply('settings.apply');
		JToolBarHelper::save('settings.save');
		JToolBarHelper::cancel('settings.cancel');

		JToolBarHelper::divider();
		JToolBarHelper::help('settings', true);
	}

	function WarningIcon()
	{
		$tip = JHtml::_('image', 'system/tooltip.png', null, NULL, true);

		return $tip;
	}
}
