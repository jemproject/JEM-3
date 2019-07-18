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
 * Venue-View
 */
class JemViewVenue extends JEMView {

	protected $state;

	function __construct($config = array()) {
		parent::__construct($config);
	}

	/**
	 * Creates the Venue View
	 */
	function display($tpl = null) {

			// initialize variables
			$app 			= JFactory::getApplication();
			$jinput 		= $app->input;
			$document 		= JFactory::getDocument();
			$menu 			= $app->getMenu();
			$menuitem		= $menu->getActive();
			$jemsettings 	= JemHelper::config();
			$vsettings		= JemHelper::viewSettings('vvenue');
			$db 			= JFactory::getDBO();
			$state 			= $this->get('State');
			$params 		= $state->params;
			$pathway 		= $app->getPathWay ();
			$uri 			= JFactory::getURI();
			$task 			= $jinput->getCmd('task');
			$user			= JFactory::getUser();
			$itemid 		= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
			$print			= $jinput->getBool('print');

			$this->state	= $this->get('State');

			// Load css
			JemHelper::loadCss('jem');
			JemHelper::loadCustomCss();
			JemHelper::loadCustomTag();

			if ($print) {
				JemHelper::loadCss('print');
				$document->setMetaData('robots', 'noindex, nofollow');
			}

			// get data from model
			$rows	= $this->get('Items');
			$venue	= $this->get('Venue');

			// are events available?
			if (!$rows) {
				$noevents = 1;
			} else {
				$noevents = 0;
			}

			// Decide which parameters should take priority
			$useMenuItemParams = ($menuitem && $menuitem->query['option'] == 'com_jem'
			                                && $menuitem->query['view']   == 'venue'
			                                && (!isset($menuitem->query['layout']) || $menuitem->query['layout'] == 'default')
			                                && $menuitem->query['id']     == $venue->id);

			// get search & user-state variables
			$filter_order 		= $app->getUserStateFromRequest('com_jem.venue.'.$itemid.'.filter_order', 'filter_order', 'a.dates', 'cmd');
			$filter_order_DirDefault = 'ASC';
			// Reverse default order for dates in archive mode
			if($task == 'archive' && $filter_order == 'a.dates') {
				$filter_order_DirDefault = 'DESC';
			}
			$filter_order_Dir 	= $app->getUserStateFromRequest('com_jem.venue.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', $filter_order_DirDefault, 'word');
			$filter_type		= $app->getUserStateFromRequest('com_jem.venue.'.$itemid.'.filter_type', 'filter_type', '', 'int');
			$search 			= $app->getUserStateFromRequest('com_jem.venue.'.$itemid.'.filter_search', 'filter_search', '', 'string');
			$search 			= $db->escape(trim(JString::strtolower($search)));

			// table ordering
			$lists['order_Dir']	= $filter_order_Dir;
			$lists['order']		= $filter_order;

			// Get image
			$limage = JemImage::flyercreator($venue->locimage,'venue');

			// Add feed links
			$link    = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);

			// pathway, page title, page heading
			if ($useMenuItemParams) {
				$pagetitle   = $params->get('page_title', $menuitem->title ? $menuitem->title : $venue->venue);
				$pageheading = $params->get('page_heading', $pagetitle);
				$pathway->setItemName(1, $menuitem->title);
			} else {
				$pagetitle   = $venue->venue;
				$pageheading = $pagetitle;
				$params->set('show_page_heading', 1); // ensure page heading is shown
				$pathway->addItem($pagetitle, JRoute::_(JemHelperRoute::getVenueRoute($venue->slug)));
			}
			$pageclass_sfx = $params->get('pageclass_sfx');

			// create the pathway
			if ($task == 'archive') {
				$pathway->addItem (JText::_('COM_JEM_ARCHIVE'), JRoute::_(JemHelperRoute::getVenueRoute($venue->slug).'&task=archive'));
				$print_link = JRoute::_(JEMHelperRoute::getVenueRoute($venue->slug).'&task=archive&print=1&tmpl=component');
				$pagetitle   .= ' - ' . JText::_('COM_JEM_ARCHIVE');
				$pageheading .= ' - ' . JText::_('COM_JEM_ARCHIVE');
			} else {
				//$pathway->addItem($venue->venue, JRoute::_(JEMHelperRoute::getVenueRoute($venue->slug)));
				$print_link = JRoute::_(JemHelperRoute::getVenueRoute($venue->slug).'&print=1&tmpl=component');
			}

			$params->set('page_heading', $pageheading);

			// Add site name to title if param is set
			if ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$pagetitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pagetitle);
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$pagetitle = JText::sprintf('JPAGETITLE', $pagetitle, $app->getCfg('sitename'));
			}

			// set Page title & Meta data
			$document->setTitle($pagetitle);
			$document->setMetaData('title', $pagetitle);
			$document->setMetadata('keywords', $venue->meta_keywords);
			$document->setDescription(strip_tags($venue->meta_description));

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
			
			// check if user should the edit-Venue icon
			if (JEMUser::editVenue($params,true,false,$venue->id,'venue',$venue->created_by)) {
				$this->editVenueIcon = 1;
			} else {
				$this->editVenueIcon = 0;
			}
			
			// Generate Venuedescription
			if (!$venue->locdescription == '' || !$venue->locdescription == '<br />') {
				// execute plugins
				$venue->text = $venue->locdescription;
				$venue->title = $venue->venue;
				JPluginHelper::importPlugin ('content');
				$app->triggerEvent ('onContentPrepare', array (
						'com_jem.venue',
						&$venue,
						&$params,
						0
				));
				$venuedescription = $venue->text;
			}

			// prepare the url for output
			if (strlen($venue->url) > 35) {
				$venue->urlclean = $this->escape(substr($venue->url, 0, 35 )) . '...';
			} else {
				$venue->urlclean = $this->escape($venue->url);
			}

			// create flag
			if ($venue->country) {
				$venue->countryimg = JemHelperCountries::getCountryFlag($venue->country);
			}

			# retrieve mapType setting
			$mapType 		= $params->get('mapType','0');

			switch($mapType) {
				case '0':
					$type = 'ROADMAP';
					break;
				case '1':
					$type = 'SATELLITE';
					break;
				case '2':
					$type = 'HYBRID';
					break;
				case '3':
					$type = 'TERRAIN';
					break;
			}
			$this->mapType = $type;

			// Create the pagination object
			$pagination = $this->get('Pagination');

			// filters
			$filters = array ();
			$filters[] = JHtml::_('select.option', '0', '&mdash; '.JText::_('COM_JEM_GLOBAL_SELECT_FILTER').' &mdash;');

			if ($jemsettings->showtitle == 1) {
				$filters[] = JHtml::_('select.option', '1', JText::_('COM_JEM_TITLE'));
			}
			if ($jemsettings->showcat == 1) {
				$filters[] = JHtml::_('select.option', '4', JText::_('COM_JEM_CATEGORY'));
			}
			$lists['filter'] = JHtml::_('select.genericlist', $filters, 'filter_type', array('size'=>'1','class'=>'inputbox input-medium'), 'value', 'text', $filter_type);
			$lists['search'] = $search;

			// mapping variables
			$this->lists 				= $lists;
			$this->action 				= $uri->toString ();
			$this->rows 				= $rows;
			$this->noevents 			= $noevents;
			$this->venue 				= $venue;
			$this->print_link 			= $print_link;
			$this->params 				= $params;
			$this->limage 				= $limage;
			$this->venuedescription		= $venuedescription;
			$this->pagination 			= $pagination;
			$this->jemsettings 			= $jemsettings;
			$this->vsettings			= $vsettings;
			$this->item					= $menuitem;
			$this->pagetitle			= $pagetitle;
			$this->task					= $task;
			$this->pageclass_sfx		= htmlspecialchars($pageclass_sfx);
			$this->print				= $print;

		parent::display($tpl);
	}
}
