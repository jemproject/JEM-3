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
 * Categories-View
 */
class JemViewCategories extends JEMView
{
	/**
	 * Categories-View
	 */
	function display($tpl=null)
	{
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$settings		= JemHelper::globalattribs();
		$document 		= JFactory::getDocument();
		$jemsettings 	= JemHelper::config();
		$vsettings		= JemHelper::viewSettings('vcategories');
		$settings		= JemHelper::globalattribs();
		$user			= JFactory::getUser();
		$print			= $jinput->getBool('print');
		$task			= $jinput->getCmd('task');
		$model 			= $this->getModel();
		$id 			= $jinput->getInt('id', 1);

		$rows 		= $this->get('Data');
		$pagination = $this->get('Pagination');

		// Load css
		JemHelper::loadCss('jem');
		JemHelper::loadCustomCss();
		JemHelper::loadCustomTag();

		if ($print) {
			JemHelper::loadCss('print');
			$document->setMetaData('robots', 'noindex, nofollow');
		}

		//get menu information
		$menu		= $app->getMenu();
		$menuitem	= $menu->getActive();

		$global = new JRegistry;
		$global->loadString($settings);

		$params = clone $global;
		$params->merge($global);
		if ($menu = $app->getMenu()->getActive())
		{
			$params->merge($menu->params);
		}

		$pagetitle		= $params->def('page_title', $menuitem->title);
		$pageheading	= $params->def('page_heading', $params->get('page_title'));
		$pageclass_sfx	= $params->get('pageclass_sfx');

		//pathway
		$pathway = $app->getPathWay();
		if($menuitem) {
			$pathway->setItemName(1, $menuitem->title);
		}

		if ($task == 'archive') {
			$pathway->addItem(JText::_('COM_JEM_ARCHIVE'), JRoute::_('index.php?view=categories&id='.$id.'&task=archive'));
			$print_link = JRoute::_('index.php?option=com_jem&view=categories&id='.$id.'&task=archive&print=1&tmpl=component');
			$pagetitle   .= ' - '.JText::_('COM_JEM_ARCHIVE');
			$pageheading .= ' - '.JText::_('COM_JEM_ARCHIVE');
			$params->set('page_heading', $pageheading);
		} else {
			$print_link = JRoute::_('index.php?option=com_jem&view=categories&id='.$id.'&print=1&tmpl=component');
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
		$document->setMetaData('title' , $pagetitle);

		// Check if the user has access to the form
		if (JEMUser::addEvent(true)) {
			$dellink = 1;
		} else {
			$dellink = 0;
		}

		// Get events if requested
		if ($params->get('detcat_nr', 0) > 0) {
			foreach($rows as $row) {
				$row->events = $model->getEventdata($row->id);
			}
		}

		$this->rows				= $rows;
		$this->task				= $task;
		$this->params			= $params;
		$this->dellink			= $dellink;
		$this->pagination		= $pagination;
		$this->item				= $menuitem;
		$this->jemsettings		= $jemsettings;
		$this->vsettings		= $vsettings;
		$this->settings			= $settings;
		$this->pagetitle		= $pagetitle;
		$this->print_link		= $print_link;
		$this->model			= $model;
		$this->id				= $id;
		$this->pageclass_sfx	= htmlspecialchars($pageclass_sfx);
		$this->print			= $print;

		parent::display($tpl);
	}
}
