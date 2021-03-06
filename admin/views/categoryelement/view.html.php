<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * View: Categoryelement
 */
class JemViewCategoryelement extends JViewLegacy {

	public function display($tpl = null)
	{
		$document	= JFactory::getDocument();
		$db			= JFactory::getDBO();
		$app 		= JFactory::getApplication();
		$jinput 	= JFactory::getApplication()->input;

		$itemid 	= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_order		= $app->getUserStateFromRequest('com_jem.categoryelement.filter_order', 'filter_order', 'c.ordering', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.categoryelement.filter_order_Dir',	'filter_order_Dir',	'', 'word');
		$filter_state 		= $app->getUserStateFromRequest('com_jem.categoryelement.'.$itemid.'.filter_state', 'filter_state', '', 'string');
		$search 			= $app->getUserStateFromRequest('com_jem.categoryelement.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search 			= $db->escape(trim(JString::strtolower($search)));

		// prepare document
		$document->setTitle(JText::_('COM_JEM_SELECT_CATEGORY'));

		// Load css
		JemHelper::loadCss('backend');

		// Get data from the model
		$rows = $this->get('Data');
		$pagination = $this->get('Pagination');

		// publish/unpublished filter
		$lists['state'] = JHtml::_('grid.state', $filter_state);

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		// assign data to template
		$this->lists 		= $lists;
		$this->filter_state = $filter_state;
		$this->rows 		= $rows;
		$this->pagination 	= $pagination;

		parent::display($tpl);
	}
}
