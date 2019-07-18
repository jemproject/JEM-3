<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Venues-View
*/
class JemViewVenues extends JViewLegacy
{
	
	protected $state = null;
	
	/**
	 * Creates the Venuesview
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$jinput = $app->input;

		$state 			= $this->get('State');
		$params 		= $state->params;
		$document		= JFactory::getDocument();
		$jemsettings	= JemHelper::config();
		$vsettings		= JemHelper::viewSettings('vvenues');
		$user			= JFactory::getUser();
		$print			= $jinput->getBool('print');

		//get menu information
		$menu		= $app->getMenu();
		$menuitem	= $menu->getActive();
		$params 	= $app->getParams();

		// Load css
		JemHelper::loadCss('jem');
		JemHelper::loadCustomCss();
		JemHelper::loadCustomTag();

		if ($print) {
			JemHelper::loadCss('print');
			$document->setMetaData('robots', 'noindex, nofollow');
		}

		// Request variables
		$task 	= $jinput->getCmd('task');
		$rows 	= $this->get('Items');

		$pagetitle = $params->def('page_title', $menuitem->title);
		$pageheading = $params->def('page_heading', $params->get('page_title'));
		$pageclass_sfx = $params->get('pageclass_sfx');

		//pathway
		$pathway 	= $app->getPathWay();
		if($menuitem) $pathway->setItemName(1, $menuitem->title);

		if ($task == 'archive') {
			$pathway->addItem(JText::_('COM_JEM_ARCHIVE'), JRoute::_('index.php?view=venues&task=archive'));
			$print_link = JRoute::_('index.php?view=venues&task=archive&print=1&tmpl=component');
			$pagetitle   .= ' - '.JText::_('COM_JEM_ARCHIVE');
			$pageheading .= ' - '.JText::_('COM_JEM_ARCHIVE');
			$params->set('page_heading', $pageheading);
		} else {
			$print_link = JRoute::_('index.php?view=venues&print=1&tmpl=component');
		}

		// Add site name to title if param is set
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$pagetitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pagetitle);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$pagetitle = JText::sprintf('JPAGETITLE', $pagetitle, $app->getCfg('sitename'));
		}

		//Set Page title
		$document->setTitle($pagetitle);
		$document->setMetadata('title' , $pagetitle);
		$document->setMetadata('keywords', $pagetitle);

		// Check if the user should see the submit-Event icon
		if (JEMUser::addEvent($params,true)) {
			$this->submitEventIcon = 1;
		} else {
			$this->submitEventIcon = 0;
		}
		
		// Check if the user should see the submit-Venue icon
		if (JEMUser::addVenue($params,true)) {
			$this->submitVenueIcon = 1;
		} else {
			$this->submitVenueIcon = 0;
		}

		// Create the pagination object
		$pagination = $this->get('Pagination');

		$this->rows				= $rows;
		$this->print_link		= $print_link;
		$this->params			= $params;
		$this->pagination		= $pagination;
		$this->item				= $menuitem;
		$this->jemsettings		= $jemsettings;
		$this->vsettings		= $vsettings;
		$this->task				= $task;
		$this->pagetitle		= $pagetitle;
		$this->pageclass_sfx	= htmlspecialchars($pageclass_sfx);
		$this->print			= $print;
		$this->app				= $app;

		parent::display($tpl);
	}
}
