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
 * Model-Attendees
 */
class JemModelAttendees extends JModelLegacy
{
	/**
	 * Events data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Events total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Events total
	 *
	 * @var integer
	 */
	var $_event = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Events id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$app 			= JFactory::getApplication();
		$jinput 		= JFactory::getApplication()->input;
		$jemsettings 	= JEMHelper::config();
		$itemid			= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$id = $jinput->getInt('id');
		$this->setId($id);

		$limit		= $app->getUserStateFromRequest('com_jem.attendees.'.$itemid.'.limit', 'limit', $jemsettings->display_num, 'uint');
		$limitstart = $app->input->get('limitstart', 0, 'uint');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		//set unlimited if export or print action | task=export or task=print
		$this->setState('unlimited', $jinput->getCmd('task'));


	}

	/**
	 * Method to set the category identifier
	 *
	 * @access	public
	 * @param	int Category identifier
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id	    = $id;
		$this->_data 	= null;
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

			if ($this->getState('unlimited') == '') {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$pagination = $this->getPagination();
				$this->_data = $this->_getList($query, $pagination->limitstart, $pagination->limit);
			}
		}

		return $this->_data;
	}

	/**
	 * Method to get the total nr of the attendees
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
	 * Method to build the query for the attendees
	 *
	 * @access private
	 * @return integer
	 *
	 */
	protected function _buildQuery()
	{
		// Get the ORDER BY clause for the query
		$orderby	= $this->_buildContentOrderBy();
		$where		= $this->_buildContentWhere();

		$query = 'SELECT r.*, u.username, u.name, u.email, a.created_by, a.published,'
				. ' c.catname, c.id AS catid'
		. ' FROM #__jem_register AS r'
		. ' LEFT JOIN #__jem_events AS a ON r.event = a.id'
		. ' LEFT JOIN #__users AS u ON u.id = r.uid'
		. ' LEFT JOIN #__jem_cats_event_relations AS rel ON rel.itemid = a.id'
		. ' LEFT JOIN #__jem_categories AS c ON c.id = rel.catid'
		. $where
		. ' GROUP BY r.id'
		. $orderby
		;

		return $query;
	}

	/**
	 * Method to build the orderby clause of the query for the attendees
	 *
	 * @access private
	 * @return integer
	 *
	 */
	protected function _buildContentOrderBy()
	{
		$app	= JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$itemid	= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_order		= $app->getUserStateFromRequest('com_jem.attendees.'.$itemid.'.filter_order','filter_order','u.username','cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.attendees.'.$itemid.'.filter_order_Dir','filter_order_Dir','','word');
		
		$filter_order		= JFilterInput::getinstance()->clean($filter_order, 'cmd');
		$filter_order_Dir	= JFilterInput::getinstance()->clean($filter_order_Dir, 'word');

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', u.name';

		return $orderby;
	}

	/**
	 * Method to build the where clause of the query for the attendees
	 *
	 * @access private
	 * @return string
	 *
	 */
	protected function _buildContentWhere()
	{
		$app	=  JFactory::getApplication();
		$user	= JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		$jinput = JFactory::getApplication()->input;
		$itemid	= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_type 		= $app->getUserStateFromRequest('com_jem.attendees.'.$itemid.'.filter_type','filter_type','','int');
		$search 			= $app->getUserStateFromRequest('com_jem.attendees.'.$itemid.'.filter_search','filter_search','','string');
		$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );
		$filter_waiting		= $app->getUserStateFromRequest('com_jem.attendees.'.$itemid.'.filter_waiting','filter_waiting',0,'int');

		$where = array();

		$where[] = 'r.event = '.$this->_id;
		
		if ($filter_waiting == -1) {
			$filter_waiting = 0;
		}
		
		if ($filter_waiting) {
			$where[] = ' (a.waitinglist = 0 OR r.waiting = '.($filter_waiting-1).') ';
		}


		// First thing we need to do is to select only needed events
		$where[] = ' a.published = 1';
		$where[] = ' c.published = 1';
		$where[] = ' c.access  IN (' . implode(',', $levels) . ')';

		// then if the user is the owner of the event
		$where[] = ' a.created_by = '.$this->_db->Quote($user->id);

		/*
		* Search name
		*/
		/*
		if ($search && $filter == 1) {
			$where[] = ' LOWER(u.name) LIKE \'%'.$search.'%\' ';
		}
		*/

		/*
		* Search username
		*/
		if ($search && $filter_type == 2) {
			$where[] = ' LOWER(u.username) LIKE \'%'.$search.'%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	/**
	 * Get event data
	 *
	 * @access public
	 * @return object
	 *
	 */
	function getEvent()
	{

		$query = 'SELECT id, alias, title, dates, enddates, times, endtimes, maxplaces, waitinglist FROM #__jem_events WHERE id = '.$this->_id;

		$this->_db->setQuery( $query );

		$_event = $this->_db->loadObject();

		return $_event;
	}

	/**
	 * Delete registered users
	 *
	 * @access public
	 * @return true on success
	 *
	 */
	function remove($cid = array())
	{
		if (count( $cid ))
		{
			$user = implode(',', $cid);

			$query = 'DELETE FROM #__jem_register WHERE id IN ('. $user .') ';

			$this->_db->setQuery( $query );

			if (!$this->_db->execute()) {
				JError::raiseError( 1001, $this->_db->getErrorMsg() );
			}
		}
		return true;
	}
}
?>