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
 * View: Help
 */
class JEMViewHelp extends JViewLegacy {

	protected $help_search = null;
	protected $page = null;
	protected $lang_tag = null;
	protected $toc = null;
	
	public function display($tpl = null) {
	
		$this->help_search			= $this->get('HelpSearch');
		$this->page					= $this->get('Page');
		$this->toc					= $this->get('Toc');
		$this->langTag				= $this->get('LangTag');
		
		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// add toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	
	/**
	 * Add Toolbar
	 */
	protected function addToolbar()
	{
		//create the toolbar
		JToolBarHelper::title(JText::_('COM_JEM_HELP'), 'help');
	}
}
?>