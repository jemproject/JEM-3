<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Model: Events
 */
class JemModelEvents extends JModelList
{
	/**
	 * Constructor.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'alias', 'a.alias',
					'title', 'a.title',
					'state', 'a.state',
					'times', 'a.times',
					'venue','loc.venue',
					'city','loc.city',
					'dates', 'a.dates',
					'datetime',
					'hits', 'a.hits',
					'id', 'a.id',
					'catname', 'c.catname',
					'featured', 'a.featured',
					'language', 'a.language',
					'filtertype',
					'published',
					'access', 'a.access', 'access_level',
					# by adding groupset the groupset filter will stay open and it can be used
					'groupset','a.recurrence_group',
					'ordering',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter.search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter.published', '');
		$this->setState('filter.published', $published);

		$filterfield = $this->getUserStateFromRequest($this->context.'.filter.filtertype', 'filter.filtertype', '', 'int');
		$this->setState('filter.filtertype', $filterfield);

		$begin = $this->getUserStateFromRequest($this->context.'.filter.dates', 'filter.dates', '', 'string');
		$this->setState('filter.dates', $begin);

		$end = $this->getUserStateFromRequest($this->context.'.filter.enddates', 'filter.enddates', '', 'string');
		$this->setState('filter.enddates', $end);

		$end = $this->getUserStateFromRequest($this->context.'.filter.groupset', 'filter.groupset', '', 'string');
		$this->setState('filter.groupset', $end);
		
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		

		// Load the parameters.
		$params = JComponentHelper::getParams('com_jem');
		$this->setState('params', $params);

		# it's needed to set the parent option
		parent::populateState('a.dates', 'asc');
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
		$id.= ':' . $this->getState('filter.groupset');

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
						'a.*'
				)
		);
		$query->from($db->quoteName('#__jem_events').' AS a');

		// Join over the language
		$query->select('l.title AS language_title')
		->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');
		
		
		// Join over venue data.
		$query->select('loc.venue, loc.city, loc.state, loc.checked_out AS vchecked_out');
		$query->join('LEFT', '#__jem_venues AS loc ON loc.id = a.locid');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level')
		->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the user who modified the event.
		$query->select('um.name AS modified_by');
		$query->join('LEFT', '#__users AS um ON um.id = a.modified_by');

		// Join over the author & email.
		$query->select('u.email, u.name AS author');
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');

		# Join over the recurrence table.
		$query->select('rec.groupidhide, rec.exdate AS exdates, rec.groupid_ref');
		$query->join('LEFT', '#__jem_recurrence AS rec ON rec.itemid = a.id');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by Date
		# @todo: add date-checking
		$startDate	= $this->getState('filter_begin');
		$endDate 	= $this->getState('filter_end');
		if (!empty($startDate) && !empty($endDate)) {
			$query->where('(a.dates >= '.$db->Quote($startDate).')');
			$query->where('(a.enddates <= '.$db->Quote($endDate).')');
		} else {
			if (!empty($startDate)) {
				$query->where('(a.dates IS NULL OR a.dates >= '.$db->Quote($startDate).')');
			}
			if (!empty($endDate)) {
				$query->where('(a.enddates IS NULL OR a.enddates <= '.$db->Quote($endDate).')');
			}
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
							/* search event-title or alias */
							$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
							break;
						case 2:
							/* search venue */
							$query->where('loc.venue LIKE '.$search);
							break;
						case 3:
							/* search city */
							$query->where('loc.city LIKE '.$search);
							break;
						case 4:
							/* search category */
							break;
						case 5:
							/* search state */
							$query->where('loc.state LIKE '.$search);
							break;
						case 6:
							/* search country */
							$query->where('loc.country LIKE '.$search);
							break;
						case 7:
							/* search all */
							$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.' OR loc.city LIKE '.$search.' OR loc.state LIKE '.$search.' OR loc.country LIKE '.$search.')');
							break;
						default:
							$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.' OR loc.city LIKE '.$search.' OR loc.state LIKE '.$search.' OR loc.country LIKE '.$search.')');
					}
				}
			}
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		# filter events with a recurrence-group
		$groupset = $this->getState('filter.groupset');

		if (!empty($groupset)) {
			$query->where('a.recurrence_group = '.$groupset);
		}

		# group by a.id
		$query->group('a.id');

		# ordering
		$orderCol	= $this->state->get('list.ordering','a.title');
		$orderDirn	= $this->state->get('list.direction','asc');
		
		// SQL server change
		if ($orderCol == 'language')
		{
			$orderCol = 'l.title';
		}

		if ($orderCol == 'a.dates')
		{
			$query->order(array($db->escape('a.dates '.$orderDirn),$db->escape('a.times '.$orderDirn)));
		} else {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get the userinformation of edited/submitted events
	 * @return object
	 */
	public function getItems()
	{
		$items = parent::getItems();

		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups = $user->getAuthorisedViewLevels();
		$input	= JFactory::getApplication()->input;


		$filter = $this->getState('filter.filtertype');
		$search = $this->getState('filter.search');

		foreach ($items as $index => $item) {
			$item->categories = $this->getCategories($item->id);

			# check if the item-categories is empty
			# in case of filtering we will unset the items without the requested category

			if($search) {
				if ($filter == 4) {
					if (empty($item->categories)) {
						unset ($items[$index]);
					}
				}
			}
		}

		$items = JEMHelper::getAttendeesNumbers($items);

		if ($items) {
			return $items;
		}

		return array();
	}

	/**
	 * Retrieve Categories
	 *
	 * Due to multi-cat this function is needed
	 * filter-index (4) is pointing to the cats
	 */

	function getCategories($id)
	{
		$user 			= JFactory::getUser();
		$levels 		= $user->getAuthorisedViewLevels();
		$app 			= JFactory::getApplication();
		$settings 		= JemHelper::globalattribs();

		# Query
		$db 	= JFactory::getDBO();
		$query = $db->getQuery(true);

		$case_when_c = ' CASE WHEN ';
		$case_when_c .= $query->charLength('c.alias');
		$case_when_c .= ' THEN ';
		$id_c = $query->castAsChar('c.id');
		$case_when_c .= $query->concatenate(array($id_c, 'c.alias'), ':');
		$case_when_c .= ' ELSE ';
		$case_when_c .= $id_c.' END as catslug';

		$query->select(array('DISTINCT c.id','c.id AS catid','c.catname','c.access','c.path','c.checked_out AS cchecked_out','c.color',$case_when_c));
		$query->from('#__jem_categories as c');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.catid = c.id');

		$query->select(array('a.id AS multi'));
		$query->join('LEFT','#__jem_events AS a ON a.id = rel.itemid');

		$query->where('rel.itemid ='.(int)$id);

		###################
		## FILTER-ACCESS ##
		###################

		# Filter by access level.
		$access = $this->getState('filter.access');

		if ($access){
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('c.access IN ('.$groups.')');
		}

		###################
		## FILTER-SEARCH ##
		###################

		# define variables
		$filter = $this->getState('filter.filtertype');
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');

				if($search) {
					if ($filter == 4) {
							$query->where('c.catname LIKE '.$search);
					}
				}
			}
		}

		$db->setQuery($query);
		$cats = $db->loadObjectList();

		return $cats;
	}
}
