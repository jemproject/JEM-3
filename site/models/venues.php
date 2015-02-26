<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/eventslist.php';

/**
 * Model-Venues
 */
class JemModelVenues extends JemModelEventslist
{

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// parent::populateState($ordering, $direction);

		$app 			= JFactory::getApplication();
		$settings		= JemHelper::globalattribs();
		$jinput			= JFactory::getApplication()->input;
		$itemid 		= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		$params 		= $app->getParams();
		$task           = $jinput->getCmd('task');

		// List state information
		$limit		= $app->getUserStateFromRequest('com_jem.venues.'.$itemid.'.limit', 'limit', $params->get('display_venues_num'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		# params
		$this->setState('params', $params);

		$this->setState('filter.published',1);

		$this->setState('filter.access', true);
		$this->setState('filter.groupby',array('l.id','l.venue'));
	}

	/**
	 * Build the query
	 *
	 * @access private
	 * @return string
	 */
	protected function getListQuery()
	{
		$user 	= JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		$jinput	= JFactory::getApplication()->input;
		$task 	= $jinput->getCmd('task');

		// Query
		$db 	= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$case_when_l = ' CASE WHEN ';
		$case_when_l .= $query->charLength('l.alias');
		$case_when_l .= ' THEN ';
		$id_l = $query->castAsChar('l.id');
		$case_when_l .= $query->concatenate(array($id_l, 'l.alias'), ':');
		$case_when_l .= ' ELSE ';
		$case_when_l .= $id_l.' END as venueslug';

		$query->select(array('l.id AS locid','l.locimage','l.locdescription','l.url','l.venue','l.street','l.city','l.country','l.postalCode','l.state','l.map','l.latitude','l.longitude'));
		$query->select(array($case_when_l));
		$query->from('#__jem_events as a');
		$query->join('LEFT', '#__jem_venues AS l ON l.id = a.locid');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.itemid = a.id');
		$query->join('LEFT', '#__jem_categories AS c ON c.id = rel.catid');

		// where
		$where = array();
		$where[] = ' l.published = 1';

		$query->where($where);
		$query->order(array('l.venue ASC'));
		$query->group(array('l.id','l.venue'));


		return $query;
	}

	/**
	 * Method to get a list of venues.
	 */
	public function getItems()
	{
		$query = $this->_getListQuery();
		$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));

		$app = JFactory::getApplication();
		$params = clone $this->getState('params');

		// Lets load the content if it doesn't already exist
		if ($items) {
			foreach ($items as $item) {
				//create target link
				$item->linkEventsArchived = JRoute::_(JEMHelperRoute::getVenueRoute($item->venueslug.'&task=archive'));
				$item->linkEventsPublished = JRoute::_(JEMHelperRoute::getVenueRoute($item->venueslug));

				$item->EventsPublished = $this->AssignedEvents($item->locid,'1');
				$item->EventsArchived = $this->AssignedEvents($item->locid,'2');
			}

			return $items;
		}

		return array();
	}

	function AssignedEvents($id,$state=1) {

		$db 	= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$case_when_l = ' CASE WHEN ';
		$case_when_l .= $query->charLength('l.alias');
		$case_when_l .= ' THEN ';
		$id_l = $query->castAsChar('l.id');
		$case_when_l .= $query->concatenate(array($id_l, 'l.alias'), ':');
		$case_when_l .= ' ELSE ';
		$case_when_l .= $id_l.' END as venueslug';

		$query->select(array('a.id'));
		$query->from('#__jem_events as a');
		$query->join('LEFT', '#__jem_venues AS l ON l.id = a.locid');
	    $query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.itemid = a.id');
		$query->join('LEFT', '#__jem_categories AS c ON c.id = rel.catid');

		# venue-id
		$query->where('l.id= '. $id);
		# state
		$query->where('a.published= '.$state);

		#####################
		### FILTER - BYCAT ##
		#####################

		$cats = $this->getCategories('all');
		$query->where('c.id  IN (' . implode(',', $cats) . ')');


		$db->setQuery($query);
		$ids = $db->loadColumn(0);
		$ids = array_unique($ids);
		$nr = count($ids);

		if (empty($nr)) {
			$nr = 0;
		}

		return ($nr);
	}

	/**
	 * Retrieve Categories
	 *
	 * Due to multi-cat this function is needed
	 * filter-index (4) is pointing to the cats
	 *
	 * @todo: check
	 */

	function getCategories($id)
	{
		$user 			= JFactory::getUser();
		$userid			= (int) $user->get('id');
		$levels 		= $user->getAuthorisedViewLevels();
		$app 			= JFactory::getApplication();
		$params 		= $app->getParams();
		$catswitch 		= $params->get('categoryswitch', '0');
		$settings 		= JemHelper::globalattribs();

		// Query
		$db 	= JFactory::getDBO();
		$query = $db->getQuery(true);

		$case_when_c = ' CASE WHEN ';
		$case_when_c .= $query->charLength('c.alias');
		$case_when_c .= ' THEN ';
		$id_c = $query->castAsChar('c.id');
		$case_when_c .= $query->concatenate(array($id_c, 'c.alias'), ':');
		$case_when_c .= ' ELSE ';
		$case_when_c .= $id_c.' END as catslug';

		$query->select(array('DISTINCT c.id','c.catname','c.access','c.checked_out AS cchecked_out','c.color',$case_when_c));
		$query->from('#__jem_categories as c');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.catid = c.id');

		$query->select(array('a.id AS multi'));
		$query->join('LEFT','#__jem_events AS a ON a.id = rel.itemid');

		if ($id != 'all'){
			$query->where('rel.itemid ='.(int)$id);
		}

		$query->where('c.published = 1');

		###################
		## FILTER-ACCESS ##
		###################

		# Filter by access level.
		$access = $this->getState('filter.access');

		###################################
		## FILTER - MAINTAINER/JEM GROUP ##
		###################################

		# as maintainter someone who is registered can see a category that has special rights
		# let's see if the user has access to this category.

		$query3	= $db->getQuery(true);
		$query3 = 'SELECT gr.id'
				. ' FROM #__jem_groups AS gr'
						. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
								. ' WHERE g.member = ' . (int) $user->get('id')
								//. ' AND ' .$db->quoteName('gr.addevent') . ' = 1 '
		. ' AND g.member NOT LIKE 0';
		$db->setQuery($query3);
		$groupnumber = $db->loadColumn();

		if ($access){
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$jemgroups = implode(',',$groupnumber);

			if ($jemgroups) {
				$query->where('(c.access IN ('.$groups.') OR c.groupid IN ('.$jemgroups.'))');
			} else {
				$query->where('(c.access IN ('.$groups.'))');
			}
		}

		#######################
		## FILTER - CATEGORY ##
		#######################

		# set filter for top_category
		$top_cat = $this->getState('filter.category_top');

		if ($top_cat) {
		$query->where($top_cat);
		}

		# Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');

				if (is_numeric($categoryId)) {
				$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';
						$query->where('c.id '.$type.(int) $categoryId);
				}
				elseif (is_array($categoryId)) {
				JArrayHelper::toInteger($categoryId);
				$categoryId = implode(',', $categoryId);
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('c.id '.$type.' ('.$categoryId.')');
				}

				# filter set by day-view
		$requestCategoryId = $this->getState('filter.req_catid');

		if ($requestCategoryId) {
			$query->where('c.id = '.$requestCategoryId);
		}

		###################
		## FILTER-SEARCH ##
		###################

		# define variables
		$filter = $this->getState('filter.filter_type');
		$search = $this->getState('filter.filter_search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				if($search && $settings->get('global_show_filter')) {
					if ($filter == 4) {
						$query->where('c.catname LIKE '.$search);
					}
				}
			}
		}

		$db->setQuery($query);

		if ($id == 'all'){
		$cats = $db->loadColumn(0);
			$cats = array_unique($cats);
			return ($cats);
		} else {
			$cats = $db->loadObjectList();
		}
			return $cats;
	}
}
