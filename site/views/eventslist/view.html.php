<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

require JPATH_COMPONENT_SITE.'/classes/view.class.php';

/**
 * Eventslist-View
*/
class JemViewEventslist extends JEMView
{

	protected $state = null;
	protected $pagination = null;

	function __construct($config = array()) {
		parent::__construct($config);
	}

	/**
	 * Creates the Simple List View
	 */
	function display( $tpl = null )
	{
		// initialize variables
		$state 			= $this->get('State');
		$document 		= JFactory::getDocument();
		$app 			= JFactory::getApplication();
		$jinput 		= $app->input;
		$jemsettings	= JemHelper::config();
		$menu			= $app->getMenu();
		$menuitem		= $menu->getActive();
		$params 		= $state->params;
		$uri 			= JFactory::getURI();
		$db 			= JFactory::getDBO();
		$user			= JFactory::getUser();
		$itemid 		= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		$admin			= JEMUser::superuser();
		$task 			= $jinput->getCmd('task');
		$template 		= $app->getTemplate();

		// userstate variables
		$filter_order		= $app->getUserStateFromRequest('com_jem.eventslist.'.$itemid.'.filter_order', 'filter_order', 'a.dates', 'cmd');
		$filter_order_DirDefault = 'ASC';
		// Reverse default order for dates in archive mode
		if($task == 'archive' && $filter_order == 'a.dates') {
			$filter_order_DirDefault = 'DESC';
		}
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.eventslist.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', $filter_order_DirDefault, 'word');
		$filter_type		= $app->getUserStateFromRequest('com_jem.eventslist.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$search 			= $app->getUserStateFromRequest('com_jem.eventslist.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search 			= $db->escape(trim(JString::strtolower($search)));

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// get data from model
		$rows 	= $this->get('Items');

		// are events available?
		if (!$rows) {
			$noevents = 1;
		} else {
			$noevents = 0;
		}

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
		
		// search filter
		$filters = array();
		$filters[] = JHtml::_('select.option', '0', '&mdash; '.JText::_('COM_JEM_GLOBAL_SELECT_FILTER').' &mdash;');
		if ($jemsettings->showtitle == 1) {
			$filters[] = JHtml::_('select.option', '1', JText::_('COM_JEM_TITLE'));
		}
		if ($jemsettings->showlocate == 1) {
			$filters[] = JHtml::_('select.option', '2', JText::_('COM_JEM_VENUE'));
		}
		if ($jemsettings->showcity == 1) {
			$filters[] = JHtml::_('select.option', '3', JText::_('COM_JEM_CITY'));
		}
		if ($jemsettings->showcat == 1) {
			$filters[] = JHtml::_('select.option', '4', JText::_('COM_JEM_CATEGORY'));
		}
		$lists['filter'] = JHtml::_('select.genericlist', $filters, 'filter_type', array('size'=>'1','class'=>'inputbox input-medium'), 'value', 'text', $filter_type );
		$lists['search']= $search;

		$this->pageclass_sfx 	= htmlspecialchars($params->get('pageclass_sfx'));
		$this->pagination 		= $this->get('Pagination');
		$this->lists			= $lists;
		$this->action			= $uri->toString();
		$this->rows				= $rows;
		$this->task				= $task;
		$this->noevents			= $noevents;
		$this->params			= $params;
		$this->jemsettings		= $jemsettings;
		$this->admin			= $admin;

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$jinput 	= $app->input;
		$menus 		= $app->getMenu();
		$title 		= null;
		$task 		= $jinput->getCmd('task');
		$pathway 	= $app->getPathWay();
		$menu 		= $menus->getActive();
		$print		= $jinput->getBool('print');
		
		// define archive
		if ($task == 'archive') {
			$archive = true;
		} else {
			$archive = false;
		}

		// add feed link
		$link	= 'index.php?option=com_jem&view=eventslist&format=feed';
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$this->document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$this->document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		
		// Load css
		JemHelper::loadCss('jem');
		JemHelper::loadCustomCss();
		JemHelper::loadCustomTag();
	
		// print-link
		if ($print) {
			JemHelper::loadCss('print');
			$document->setMetaData('robots', 'noindex, nofollow');
		}
		
		if ($task == 'archive') {
			$print_link = JRoute::_('index.php?view=eventslist&task=archive&tmpl=component&print=1');
		} else {
			$print_link = JRoute::_('index.php?view=eventslist&tmpl=component&print=1');
		}

		$names = $pathway->getPathwayNames();
		// Pathway
		if ($archive) {
			$name = end($names);
			$key = key($names);
			$pathway->setItemName($key, end($names).' - '.JText::_('COM_JEM_ARCHIVE'));
			//$pathway->addItem(JText::_('COM_JEM_ARCHIVE'), JRoute::_('index.php?view=eventslist&task=archive') );
		}

		// Page-heading
		if ($menu)
		{
			if ($archive) {
				$this->params->def('page_heading', $this->params->get('page_title', $menu->title.' - '.JText::_('COM_JEM_ARCHIVE')));
			} else {
				$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
			}
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_JEM_EVENTS'));
		}

		$names = $pathway->getPathwayNames();
		
		if ($menu)
		{
	
			$pagetitle = $this->params->get('page_title', $menu->title);
			
			if ($archive) {
				$this->params->def('page_heading', $pagetitle.' - '.JText::_('COM_JEM_ARCHIVE'));
			} else {
				$this->params->def('page_heading', $pagetitle);
			}
		}
		else
		{
			$this->params->def('page_heading', JText::_($this->defaultPageTitle));
		}
		
		$ign_site = false;
		$ign_pagetitle = false;
		$ign_paramhdr = false;
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $pagetitle;
			$ign_pagetitle = true;
			if (empty($title)) {
				$title = $app->get('sitename');
				$ign_site = true;
			}
		} else {
			$ign_paramhdr = true;
		}
		
		if ($app->get('sitename_pagetitles', 0) == 0) {
			// we don't want to include the sitename
			if ($ign_site) {
				$title = false;
			} 
		}
		
		if ($app->get('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		
		if ($app->get('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
		
		if (!$ign_site) {
			$this->document->setTitle($title);
		}

		// Meta
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		// Robots
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		// map variables
		$this->print_link		= $print_link;
		$this->print			= $print;
	}
}
