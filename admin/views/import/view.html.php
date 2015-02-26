<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Import
 */
class JEMViewImport extends JViewLegacy
{

    public function display($tpl = null)
	{
		//Load pane behavior
		//jimport('joomla.html.pane');

		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// Load script
		JHtml::_('behavior.framework');

		// Get data from the model
		$eventfields 				= $this->get('EventFields');
		$catfields   				= $this->get('CategoryFields');
		$venuefields 				= $this->get('VenueFields');
		$cateventsfields 			= $this->get('CateventsFields');
		$model 						= $this->getModel();

		$this->eventfields 			= $eventfields;
		$this->catfields 			= $catfields;
		$this->venuefields 			= $venuefields;
		$this->cateventsfields 		= $cateventsfields;
		$this->eventlistVersion		= $this->get('EventlistVersion');
		$this->jemVersion 			= $this->get('JEMVersion');

		$this->eventlistTables		= $model->eventlistTables($this->get('EventlistVersion'));
		$this->detectedJEMTables	= $model->detectedJEMTables($this->get('JEMVersion'));
		$this->jemTables 			= $this->get('JemTablesCount');
		$this->existingJemData 		= $this->get('ExistingJemData');

		$jinput = JFactory::getApplication()->input;
		$progress = new stdClass();
		# EL
		$progress->step 				= $jinput->get('step', 0, 'INT');
		$progress->current 				= $jinput->get->get('current', 0, 'INT');
		$progress->total 				= $jinput->get->get('total', 0, 'INT');
		$progress->table 				= $jinput->get->get('table', '', 'INT');
		$progress->prefix 				= $jinput->get('prefix', '', 'CMD');
		$progress->copyImages			= $jinput->get('copyImages', 0, 'INT');
		$progress->copyAttachments		= $jinput->get('copyAttachments', 0, 'INT');

		# JEM
		$progress->jem_step 			= $jinput->get('jem_step', 0, 'INT');
		$progress->jem_current 			= $jinput->get->get('jem_current', 0, 'INT');
		$progress->jem_total 			= $jinput->get->get('jem_total', 0, 'INT');
		$progress->jem_table 			= $jinput->get->get('jem_table', '', 'INT');
		$progress->jem_prefix 			= $jinput->get('jem_prefix', '', 'CMD');
		$progress->jem_copyImages 		= $jinput->get('jem_copyImages', 0, 'INT');
		$progress->jem_copyAttachments 	= $jinput->get('jem_copyAttachments', 0, 'INT');

		$this->progress = $progress;

		// Do not show default prefix #__ but its replacement value
		$this->prefixToShow = $progress->prefix;
		if($this->prefixToShow == "#__" || $this->prefixToShow == "") {
			$app = JFactory::getApplication();
			$this->prefixToShow = $app->getCfg('dbprefix');
		}

		$this->jem_prefixToShow = $progress->jem_prefix;
		if($this->jem_prefixToShow == "#__" || $this->jem_prefixToShow == "") {
			$app = JFactory::getApplication();
			$this->jem_prefixToShow = $app->getCfg('dbprefix');
		}

		// add toolbar
		$this->addToolbar();

		//$this->sidebar = JHtmlSidebar::render();
		JHtml::_('jquery.framework');

		//JHtml::_('script', 'com_jem/bootstrap-filestyle.js', false, true);
		parent::display($tpl);
	}

	/**
	 * Add Toolbar
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JEM_IMPORT'), 'tableimport');

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolBarHelper::help('import', true);
	}

	function WarningIcon()
	{
		$tip = JHtml::_('image', 'system/tooltip.png', null, NULL, true);

		return $tip;
	}
}
