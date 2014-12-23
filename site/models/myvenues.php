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
 * Model-MyVenues
 */
class JemModelMyvenues extends JModelLegacy
{
	var $_venues = null;
	var $_total_venues = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$app		 = JFactory::getApplication();
		$jemsettings = JEMHelper::config();
		$jinput		 = JFactory::getApplication()->input;
		$itemid		 = $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$limit		= $app->getUserStateFromRequest('com_jem.myvenues.'.$itemid.'.limit', 'limit', $jemsettings->display_num, 'uint');
		$limitstart = $app->input->get('limitstart', 0, 'uint');
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the Events user is attending
	 *
	 * @access public
	 * @return array
	 */
	function & getVenues()
	{
		$jinput = JFactory::getApplication()->input;
		$pop 	= $jinput->getBool('pop');

		// Lets load the content if it doesn't already exist
		if ( empty($this->_venues)) {
			$query = $this->_buildQueryVenues();
			$pagination = $this->getVenuesPagination();

			if ($pop) {
				$this->_venues = $this->_getList($query);
			} else {
				$pagination = $this->getVenuesPagination();
				$this->_venues = $this->_getList($query, $pagination->limitstart, $pagination->limit);
			}
		}

		return $this->_venues;
	}

	/**
	 * Total nr of events
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalVenues()
	{
		// Lets load the total nr if it doesn't already exist
		if ( empty($this->_total_venues)) {
			$query = $this->_buildQueryVenues();
			$this->_total_venues = $this->_getListCount($query);
		}

		return $this->_total_venues;
	}

	/**
	 * Method to get a pagination object for the attending events
	 *
	 * @access public
	 * @return integer
	 */
	function getVenuesPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty($this->_pagination_venues))
		{
			jimport('joomla.html.pagination');
			$this->_pagination_venues = new JPagination($this->getTotalVenues(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination_venues;
	}

	/**
	 * Build the query
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildQueryVenues()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildVenuesWhere();
		$orderby = $this->_buildOrderByVenues();

		//Get Events from Database
		$query = 'SELECT l.id, l.venue, l.city, l.state, l.url, l.created_by, l.published,'
		 .' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', l.id, l.alias) ELSE l.id END as venueslug'
		 .' FROM #__jem_venues AS l '
		.$where
		.$orderby
		;

		return $query;
	}

	/**
	 * Build the order clause
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildOrderByVenues()
	{
		$app 				= JFactory::getApplication();
		$jinput 			= JFactory::getApplication()->input;
		$itemid				= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_order		= $app->getUserStateFromRequest('com_jem.myvenues.'.$itemid.'.filter_order', 'filter_order', 'l.venue', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.myvenues.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$filter_order		= JFilterInput::getInstance()->clean($filter_order, 'cmd');
		$filter_order_Dir	= JFilterInput::getInstance()->clean($filter_order_Dir, 'word');

		if ($filter_order != '') {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;
		} else {
			$orderby = ' ORDER BY l.venue ';
		}

		return $orderby;
	}

	/**
	 * Build the where clause
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildVenuesWhere()
	{
		$app 			= JFactory::getApplication();
		$user 			= JFactory::getUser();
		$settings 		= JEMHelper::globalattribs();
		$user 			= JFactory::getUser();
		$jinput			= JFactory::getApplication()->input;
		$itemid			= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_type	= $app->getUserStateFromRequest('com_jem.myvenues.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$search 		= $app->getUserStateFromRequest('com_jem.myvenues.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search 		= $this->_db->escape(trim(JString::strtolower($search)));

		$where = array();
		
		$where[] = ' l.published IN (0,1,2,-2)';

		// check if venue is created by the user
		$where [] = ' l.created_by = '.$this->_db->Quote($user->id);

		if ($settings->get('global_show_filter') && $search) {
			switch($filter_type) {
				case 1:
// 					$where[] = ' LOWER(a.title) LIKE \'%'.$search.'%\' ';
					break;
				case 2:
					$where[] = ' LOWER(l.venue) LIKE \'%'.$search.'%\' ';
					break;
				case 3:
					$where[] = ' LOWER(l.city) LIKE \'%'.$search.'%\' ';
					break;
				case 4:
// 					$where[] = ' LOWER(c.catname) LIKE \'%'.$search.'%\' ';
					break;
				case 5:
				default:
					$where[] = ' LOWER(l.state) LIKE \'%'.$search.'%\' ';
			}
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}
}
?>