<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Model: Venues
 */
class JemModelVenues extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see	JController
	 *
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'a.id',
					'alias','a.alias',
					'venue', 'a.venue','title',
					'alias', 'a.alias',
					'state', 'a.state',
					'country', 'a.country',
					'url', 'a.url',
					'street', 'a.street',
					'postalCode', 'a.postalCode',
					'city', 'a.city',
					'ordering', 'a.ordering',
					'created', 'a.created',
					'assignedevents','published',
					'filtertype',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter.search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter.published', '', 'string');
		$this->setState('filter.published', $published);

		$filterfield = $this->getUserStateFromRequest($this->context.'.filter.filtertype', 'filter.filtertype', '', 'int');
		$this->setState('filter.filtertype', $filterfield);

		$params = JComponentHelper::getParams('com_jem');
		$this->setState('params', $params);

		parent::populateState('a.venue', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.published');
		$id.= ':' . $this->getState('filter.filtertype');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 *
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
				$this->getState(
						'list.select',
						'a.id, a.venue, a.alias, a.url, a.street, a.postalCode, a.city, a.state, a.country,'
						.'a.latitude, a.longitude, a.locdescription, a.meta_keywords, a.meta_description,'
						.'a.locimage, a.map, a.created_by, a.author_ip, a.created, a.modified,'
						.'a.modified_by, a.version, a.published, a.checked_out, a.checked_out_time,'
						.'a.ordering, a.publish_up, a.publish_down'
				)
		);
		$query->from($db->quoteName('#__jem_venues').' AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

		// Join over the user who modified the event.
		$query->select('um.name AS modified_by');
		$query->join('LEFT', '#__users AS um ON um.id = a.modified_by');

		// Join over the author & email.
		$query->select('u.email, u.name AS author');
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');

		// Join over the assigned events
		$query->select('COUNT(e.locid) AS assignedevents');
		$query->join('LEFT OUTER', '#__jem_events AS e ON e.locid = a.id');
		$query->group('a.id');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in title
		$filter = $this->getState('filter.filtertype');
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');

				if($search) {
					switch($filter) {
						case 1:
							/* search venue or alias */
							$query->where('(a.venue LIKE '.$search.' OR a.alias LIKE '.$search.')');
							break;
						case 3:
							/* search city */
							$query->where('a.city LIKE '.$search);
							break;
						case 5:
							/* search state */
							$query->where('a.state LIKE '.$search);
							break;
						case 6:
							/* search country */
							$query->where('a.country LIKE '.$search);
							break;
						case 7:
						default:
							/* search all */
							$query->where('(a.venue LIKE '.$search.' OR a.alias LIKE '.$search.' OR a.city LIKE '.$search.' OR a.state LIKE '.$search.' OR a.country LIKE '.$search.')');
					}
				}
			}
		}

		$orderCol	= $this->state->get('list.ordering','a.venue');
		$orderDirn	= $this->state->get('list.direction','asc');

		if ($orderCol == 'a.dates')
		{
			$query->order(array($db->escape('a.dates '.$orderDirn),$db->escape('a.times '.$orderDirn)));
		} else {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}

		return $query;
	}


	/**
	 * Method to remove a venue
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function remove($cid)
	{
		$cids	= implode(',', $cid);

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select(array('v.id','v.venue'));
		$query->select(array('COUNT(e.locid) as AssignedEvents'));
		$query->from($db->quoteName('#__jem_venues').' AS v');
		$query->join('LEFT', '#__jem_events AS e ON e.locid = v.id');
		$query->where(array('v.id IN ('.$cids.')'));
		$query->group('v.id');
		$db->setQuery($query);

		if (!($rows = $db->loadObjectList())) {
			JError::raiseError(500, $db->stderr());
			return false;
		}

		$err = array();
		$cid = array();
		foreach ($rows as $row) {
			if ($row->AssignedEvents == 0) {
				$cid[] = $row->id;
			} else {
				$err[] = $row->venue;
			}
		}

		// Assigned-events
		if (count($cid))
		{
			$cids	= implode(',', $cid);
			$db 	= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->delete($db->quoteName('#__jem_venues'));
			$query->where(array('id IN ('.$cids.')'));
			$db->setQuery($query);

			if(!$db->execute()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		// Errors occurred
		if (count($err)) {
			$cids 	= implode(', ', $err);
			$msg 	= JText::sprintf('COM_JEM_VENUES_ASSIGNED_EVENT', $cids);
			return $msg;
		} else {
			$total 	= count($cid);
			$msg 	= $total.' '.JText::_('COM_JEM_VENUES_DELETED');
			return $msg;
		}
	}

	/**
	 * Returns venue items
	 * @return object  Venues
	 */
	public function getItems()
	{
		$items = parent::getItems();
		return $items;
	}
	
	
	function getColumns() {
		
		static $columns;
	
		if (!isset($columns)) {
			if (!is_object($columns)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
		
				$query->select('vvenues');
				$query->from('#__jem_settings');
				$query->where('id = 1');
		
				$db->setQuery($query);
				$columns = $db->loadResult();
				
				$vregistry = new JRegistry;
				$vregistry->loadString($columns);
				$columns = $vregistry;
				$columns = $columns->get('hide_column_backend');
				
				if (is_null($columns)) {
					$columns = array();
				}
			}
		}
		
		return $columns;
	}
	
	
}
