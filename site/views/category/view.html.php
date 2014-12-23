<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

require JPATH_COMPONENT_SITE.'/classes/view.class.php';

/**
 * Category-View
 */
class JemViewCategory extends JEMView
{

	protected $state;
	protected $items;
	protected $pagination;


	function __construct($config = array()) {
		parent::__construct($config);
	}

	/**
	 * Creates the Category View
	 */
	function display($tpl=null)
	{
			//initialize variables
			$app 			= JFactory::getApplication();
			$jinput 		= JFactory::getApplication()->input;
			$document 		= JFactory::getDocument();
			$vsettings		= JemHelper::viewSettings('vcategory');
			$jemsettings 	= JemHelper::config();
			$settings 		= JemHelper::globalattribs();
			$db  			= JFactory::getDBO();
			$user			= JFactory::getUser();
			$print			= $jinput->getBool('print');

			//get menu information
			$params 		= $app->getParams();
			$uri 			= JFactory::getURI();
			$pathway 		= $app->getPathWay();
			$menu			= $app->getMenu();
			$menuitem		= $menu->getActive();

			# load css
			JemHelper::loadCss('jem');
			JemHelper::loadCustomCss();
			JemHelper::loadCustomTag();
						
			//get data from model
			$state		= $this->get('State');
			$params		= $state->params;
			$items		= $this->get('Items');
			$category	= $this->get('Category');
			$children	= $this->get('Children');
			$parent		= $this->get('Parent');
			$pagination = $this->get('Pagination');

			if ($category == false)
			{
				return JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
			}

			//are events available?
			if (!$items) {
				$noevents = 1;
			} else {
				$noevents = 0;
			}

			// Decide which parameters should take priority
			$useMenuItemParams = ($menuitem && $menuitem->query['option'] == 'com_jem'
			                                && $menuitem->query['view']   == 'category'
			                                && (!isset($menuitem->query['layout']) || $menuitem->query['layout'] == 'default')
			                                && $menuitem->query['id']     == $category->id);

			// get variables
			$itemid				= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);


			$filter_order		= $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_order', 'filter_order', 	'a.dates', 'cmd');
			$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_order_Dir', 'filter_order_Dir',	'', 'word');
			$filter_type		= $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_filtertype', 'filter_type', '', 'int');
			$search 			= $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_search', 'filter_search', '', 'string');
			$search 			= $db->escape(trim(JString::strtolower($search)));
			$task 				= $jinput->getCmd('task');

			// table ordering
			$lists['order_Dir'] = $filter_order_Dir;
			$lists['order'] 	= $filter_order;

			//search filter
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
			$lists['filter'] = JHtml::_('select.genericlist', $filters, 'filter_type', array('size'=>'1','class'=>'inputbox input-medium'), 'value', 'text', $filter_type);

			// search filter
			$lists['search']= $search;

			// Add feed links
			$link = '&format=feed&id='.$category->id.'&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);

			//create the pathway
			$cats		= new JEMCategories($category->id);
			$parents	= $cats->getParentlist();

			foreach($parents as $parent) {
				$pathway->addItem($this->escape($parent->catname), JRoute::_(JemHelperRoute::getCategoryRoute($parent->slug)) );
			}

			// Show page heading specified on menu item or category title as heading - idea taken from com_content.
			//
			// Check to see which parameters should take priority
			// If the current view is the active menuitem and an category view for this category, then the menu item params take priority
			if ($useMenuItemParams) {
				$pagetitle   = $params->get('page_title', $menuitem->title ? $menuitem->title : $category->catname);
				$pageheading = $params->get('page_heading', $pagetitle);
				$pathway->setItemName(1, $menuitem->title);
			} else {
				$pagetitle   = $category->catname;
				$pageheading = $pagetitle;
				$params->set('show_page_heading', 1); // ensure page heading is shown
				$pathway->addItem($category->catname, JRoute::_(JemHelperRoute::getCategoryRoute($category->slug)) );
			}
			$pageclass_sfx = $params->get('pageclass_sfx');

			if ($task == 'archive') {
				$pathway->addItem(JText::_('COM_JEM_ARCHIVE'), JRoute::_(JemHelperRoute::getCategoryRoute($category->slug).'&task=archive'));
				$print_link = JRoute::_(JemHelperRoute::getCategoryRoute($category->id) .'&task=archive&print=1&tmpl=component');
				$pagetitle   .= ' - '.JText::_('COM_JEM_ARCHIVE');
				$pageheading .= ' - '.JText::_('COM_JEM_ARCHIVE');
			} else {
				$print_link = JRoute::_(JemHelperRoute::getCategoryRoute($category->id) .'&print=1&tmpl=component');
			}
			
			if ($print) {
				JemHelper::loadCss('print');
				$document->setMetaData('robots', 'noindex, nofollow');
			}

			$params->set('page_heading', $pageheading);

			// Add site name to title if param is set
			if ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$pagetitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pagetitle);
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$pagetitle = JText::sprintf('JPAGETITLE', $pagetitle, $app->getCfg('sitename'));
			}

			//Set Page title & Meta data
			$this->document->setTitle($pagetitle);
			$document->setMetaData('title', $pagetitle);
			$document->setMetadata('keywords', $category->meta_keywords);
			$document->setDescription(strip_tags($category->meta_description));

			//Check if the user has access to the form
			$maintainer = JemUser::ismaintainer('add');
			$genaccess 	= JemUser::validate_user($jemsettings->evdelrec, $jemsettings->delivereventsyes);

			if ($maintainer || $genaccess || $user->authorise('core.create','com_jem')) {
				$dellink = 1;
			} else {
				$dellink = 0;
			}
			
			# Check if the user has access to the add-venueform
			$maintainer2 = JemUser::venuegroups('add');
			$genaccess2 = JemUser::validate_user($jemsettings->locdelrec, $jemsettings->deliverlocsyes);
			if ($maintainer2 || $genaccess2) {
				$this->addvenuelink = 1;
			} else {
				$this->addvenuelink = 0;
			}

			// Create the pagination object
			$pagination = $this->get('Pagination');

			//Generate Categorydescription
			if (empty ($category->description)) {
				$description = JText::_('COM_JEM_NO_DESCRIPTION');
			} else {
				//execute plugins
				$category->text	= $category->description;
				$category->title 	= $category->catname;
				JPluginHelper::importPlugin('content');
				$app->triggerEvent('onContentPrepare', array('com_jem.category', &$category, &$params, 0));
				$description = $category->text;
			}

			$cimage = JemImage::flyercreator($category->image,'category');

			$children = array($category->id => $children);

			$this->lists			= $lists;
			$this->action			= $uri->toString();
			$this->cimage			= $cimage;
			$this->rows				= $items;
			$this->noevents			= $noevents;
			$this->print_link		= $print_link;
			$this->params			= $params;
			$this->dellink			= $dellink;
			$this->task				= $task;
			$this->description		= $description;
			$this->pagination		= $pagination;
			$this->jemsettings		= $jemsettings;
			$this->vsettings		= $vsettings;
			$this->settings			= $settings;
			$this->pageclass_sfx	= htmlspecialchars($pageclass_sfx);
			$this->maxLevel			= $params->get('maxLevel', -1);
			$this->category			= $category;
			$this->children			= $children;
			$this->parent			= $parent;
			$this->user				= $user;
			$this->print			= $print;

		parent::display($tpl);
	}
}
?>