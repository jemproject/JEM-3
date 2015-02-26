<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Model: Eventelement
 */
class JemModelEventelement extends JModelLegacy
{
	/**
	 * Events data array
	 *
	 * @var array
	 */
	public $_data = null;

	/**
	 * Events total
	 *
	 * @var integer
	 */
	public $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	public $_pagination = null;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$app =  JFactory::getApplication();

		$limit		= $app->getUserStateFromRequest( 'com_jem.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest( 'com_jem.limitstart', 'limitstart', 0, 'int' );
		$limitstart = $limit ? (int)(floor($limitstart / $limit) * $limit) : 0;

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
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			if ($this->_data)
			{
				$count = count($this->_data);
				for($i = 0; $i < $count; $i++)
				{
					$item = $this->_data[$i];
					$item->categories = $this->getCategories($item->id);
				}
			}
		}

		if($this->_data)
		{
			$count = count($this->_data);
			for($i = 0; $i < $count; $i++){
				$item = $this->_data[$i];
				$item->categories = $this->getCategories($item->id);

				//remove events without categories (users have no access to them)
				if (empty($item->categories)) {
					unset($this->_data[$i]);
				}
			}
		}

		return $this->_data;
	}

	/**
	 * Total nr of events
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Build the query
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildQuery()
	{
		$app =  JFactory::getApplication();
		$jinput 		= $app->input;
		$jemsettings 	= JemHelper::config();
		$itemid 		= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		$db 			= JFactory::getDBO();
		$user 			= JFactory::getUser();
		$levels			= $user->getAuthorisedViewLevels();

		$filter_order		= $app->getUserStateFromRequest('com_jem.eventelement.'.$itemid.'.filter_order', 'filter_order', 'a.dates', 'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.eventelement.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		$filter_order		= JFilterInput::getinstance()->clean($filter_order, 'cmd');
		$filter_order_Dir	= JFilterInput::getinstance()->clean($filter_order_Dir, 'word');

		$published 			= $app->getUserStateFromRequest('com_jem.eventelement.'.$itemid.'.filter_state', 'filter_state', '', 'string');
		$filter_type 		= $app->getUserStateFromRequest('com_jem.eventelement.'.$itemid.'.filter_type', 'filter_type', '', 'int' );
		$search 			= $app->getUserStateFromRequest('com_jem.eventelement.'.$itemid.'.filter_search', 'filter_search', '', 'string' );
		$search 			= $db->escape(trim(JString::strtolower($search)));

		// Query
		$query = $db->getQuery(true);
		$query->select(array('a.*','loc.venue','loc.city','c.catname'));
		$query->from('#__jem_events as a');

		$query->join('LEFT', '#__jem_venues AS loc ON loc.id = a.locid');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.itemid = a.id');
		$query->join('LEFT', '#__jem_categories AS c ON c.id = rel.catid');

		// where
		$where = array();

		// Filter by published state
		if (is_numeric($published)) {
			$where[] = 'a.published = '.(int) $published;
		} elseif ($published === '') {
			$where[] = '(a.published IN (1))';
		}

		$where[] = ' c.published = 1';
		$where[] = ' c.access IN (' . implode(',', $levels) . ')';

		/* something to search for? (we like to search for "0" too) */
		if ($search || ($search === "0")) {
			switch ($filter_type) {
				case 1:
				$where[] = ' LOWER(a.title) LIKE \'%'.$search.'%\' ';
				break;
			case 2:
				$where[] = ' LOWER(loc.venue) LIKE \'%'.$search.'%\' ';
				break;
			case 3:
				$where[] = ' LOWER(loc.city) LIKE \'%'.$search.'%\' ';
				break;
			case 4:
				$where[] = ' LOWER(c.catname) LIKE \'%'.$search.'%\' ';
				break;
			}
		}

		$query->where($where);
		$query->group('a.id');

		$orderby 	= array($filter_order.' '.$filter_order_Dir,'a.dates ASC');
		$query->order($orderby);

		return $query;
	}

	function getCategories($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select(array('c.id','c.catname','c.checked_out AS cchecked_out'));
		$query->from('#__jem_categories AS c');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.catid = c.id');
		$query->where('rel.itemid = '.(int)$id);

		$db->setQuery( $query );

		$this->_cats = $db->loadObjectList();

		$count = count($this->_cats);
		for($i = 0; $i < $count; $i++)
		{
			$item = $this->_cats[$i];
			$cats = new JEMCategories($item->id);
			$item->parentcats = $cats->getParentlist();
		}

		return $this->_cats;
	}
}
