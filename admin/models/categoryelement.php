<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die();


/**
 * Model: Categoryelement
 */
class JemModelCategoryelement extends JModelLegacy
{
	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$app			= JFactory::getApplication();
		$jinput 		= $app->input;
		
		$jemsettings	= JemHelper::config();
		$itemid 		= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		
		$limit 			= $app->getUserStateFromRequest('com_jem.categoryelement.limit', 'limit', $jemsettings->display_num, 'int');
		$limitstart 	= $jinput->getInt('limitstart');
		$limitstart 	= $limit ? (int)(floor($limitstart / $limit) * $limit) : 0;
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);	
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDBO();
		$jinput = $app->input;
		$itemid = $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		static $items;

		if (isset($items)) {
			return $items;
		}

		$filter_order		= $app->getUserStateFromRequest('com_jem.categoryelement.filter_order', 'filter_order', 'c.lft', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.categoryelement.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_state		= $app->getUserStateFromRequest('com_jem.categoryelement.'.$itemid.'.filter_state', 'filter_state', '', 'string');
		$search				= $app->getUserStateFromRequest('com_jem.categoryelement.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search				= $db->escape(trim(JString::strtolower($search)));

		$filter_order		= JFilterInput::getinstance()->clean($filter_order, 'cmd');
		$filter_order_Dir	= JFilterInput::getinstance()->clean($filter_order_Dir, 'word');

		$state = array(1);

		$query = $db->getQuery(true);
		$query->select(array('c.*','u.name AS editor','g.title AS groupname','gr.name AS catgroup'));
		$query->from('#__jem_categories AS c');
		$query->join('LEFT', '#__viewlevels AS g ON g.id = c.access');
		$query->join('LEFT', '#__users AS u ON u.id = c.checked_out');
		$query->join('LEFT', '#__jem_groups AS gr ON gr.id = c.groupid');
		
		if (is_numeric($filter_state)) {
			$query->where('c.published = '.(int) $filter_state);
		} else {
			$query->where('c.published IN (' . implode(',', $state) . ')');
		}
		
		$query->order($filter_order . ' ' . $filter_order_Dir);

		
		$db->setQuery($query);
		$mitems = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
		}

		if (! $mitems) {
			$mitems = array();
			$children = array();

			$parentid = $mitems;
		} else {
			$mitems_temp = $mitems;

			$children = array();
			// First pass - collect children
			foreach ($mitems as $v) {
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			$parentid = intval($mitems[0]->parent_id);
		}

		// get list of the items
		$list = JemCategories::treerecurse($parentid, '', array(), $children, 9999, 0, 0);

	
		// note, since this is a tree we have to do the limits code-side
		if ($search) {
			$query = $db->getQuery(true);
			$query->select('c.id');
			$query->from('#__jem_categories AS c');
			$query->where(array('LOWER(c.catname) LIKE ' . $db->Quote('%' . $this->_db->escape($search, true) . '%', false),'c.published IN (' . implode(',', $state) . ')'));
			
			$db->setQuery($query);
			$search_rows = $db->loadColumn();
		}
		
		
		// eventually only pick out the searched items.
		if ($search) {
			$list1 = array();

			foreach ($search_rows as $sid) {
				foreach ($list as $item) {
					if ($item->id == $sid) {
						$list1[] = $item;
					}
				}
			}
			// replace full list with found items
			$list = $list1;
		}

		$total = count($list);

		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit'));

		// slice out elements based on limits
		$list = array_slice($list, $this->_pagination->limitstart, $this->_pagination->limit);

		return $list;
	}

	function &getPagination()
	{
		if ($this->_pagination == null) {
			$this->getItems();
		}
		return $this->_pagination;
	}
}
?>