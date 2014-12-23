<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die();
require_once JPATH_ADMINISTRATOR . '/components/com_jem/models/event.php';

/**
 * Editevent Model
 */
class JemModelEditevent extends JEMModelEvent
{
	public $_context = 'com_jem.editevent';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		// Load state from the request.
		$pk = $jinput->getInt('a_id');
		$this->setState('event.id', $pk);

		$this->setState('event.catid', $jinput->getInt('catid'));

		$return = JFactory::getApplication()->input->get('return', null, 'base64');
		$this->setState('return_page', urldecode(base64_decode($return)));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', JFactory::getApplication()->input->getCmd('layout'));

		parent::populateState('a.dates', 'ASC');
	}

	/**
	 * Method to get event data.
	 *
	 * @param integer	The id of the event.
	 *
	 * @return mixed item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		$jemsettings = JemHelper::config();

		// Initialise variables.
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('event.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		// Convert attrib field to Registry.
		$registry = new JRegistry();
		$registry->loadString($value->attribs);

		$globalsettings = JEMHelper::globalattribs();
		$globalregistry = new JRegistry();
		$globalregistry->loadString($globalsettings);

		$value->params = clone $globalregistry;
		$value->params->merge($registry);

		// Compute selected asset permissions.
		$user = JFactory::getUser();
		$userId = $user->get('id');
		//$asset = 'com_jem.event.' . $value->id;
		$asset = 'com_jem';

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
				'count(id)'
		));
		$query->from('#__jem_register');
		$query->where(array(
				'event= ' . $db->quote($itemId),
				'waiting= 0'
		));

		$db->setQuery($query);
		$res = $db->loadResult();
		$value->booked = $res;

		$files = JEMAttachment::getAttachments('event' . $itemId);
		$value->attachments = $files;


		################
		## RECURRENCE ##
		################

		# check recurrence
		if ($value->recurrence_group) {

			# this event is part of a recurrence-group
			#
			# check for groupid & groupid_ref (recurrence_table)
			# - groupid		= $item->recurrence_group
			# - groupid_ref	= $item->recurrence_group
			# - Itemid		= $item->id

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('count(id)'));
			$query->from('#__jem_recurrence');
			$query->where(array('groupid= '.$value->recurrence_group, 'itemid= '.$value->id,'groupid = groupid_ref'));

			$db->setQuery($query);
			$rec_groupset_check = $db->loadResult();

			if ($rec_groupset_check == '1') {
				$value->recurrence_groupcheck = true;
			} else {
				$value->recurrence_groupcheck = false;
			}
		} else {
			$value->recurrence_groupcheck = false;
		}

		##############
		## HOLIDAYS ##
		##############

		# Retrieve dates that are holidays and enabled.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('holiday');
		$query->from('#__jem_dates');
		$query->where(array('enabled = 1', 'holiday = 1'));

		$db->setQuery($query);
		$holidays = $db->loadColumn();

		if ($holidays) {
			$value->recurrence_country_holidays = true;
		} else {
			$value->recurrence_country_holidays = false;
		}


		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			$value->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_by) {
				$value->params->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId) {
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else {
			// New item.
			$catId = (int) $this->getState('event.catid');

			if ($catId) {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_jem.category.' . $catId));
				$value->catid = $catId;
			}
			else {
				
				$access_change = $user->authorise('core.edit.state', 'com_jem');
				
				$value->params->set('access-change', $access_change);
				
			}
		}

		$value->author_ip = $jemsettings->storeip ? JemHelper::retrieveIP() : false;

		$value->articletext = $value->introtext;
		if (!empty($value->fulltext)) {
			$value->articletext .= '<hr id="system-readmore" />' . $value->fulltext;
		}

		if (!empty($value->datimage)) {
			if (strpos($value->datimage,'images/') !== false) {
				# the image selected contains the images path
			} else {
				# the image selected doesn't have the /images/ path
				# we're looking at the locimage so we'll append the venues folder
				$value->datimage = 'images/jem/events/'.$value->datimage;
			}
		}

		$admin = JFactory::getUser()->authorise('core.manage', 'com_jem');
		if ($admin) {
			$value->admin = true;
		} else {
			$value->admin = false;
		}

		return $value;
	}

	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');

		return parent::loadForm($name, $source, $options, $clear, $xpath);
	}

	/**
	 * Get the return URL.
	 *
	 * @return string return URL.
	 *
	 */
	public function getReturnPage()
	{
		return base64_encode(urlencode($this->getState('return_page')));
	}

	############
	## VENUES ##
	############

	/**
	 * Get venues-data
	 */
	function getVenues()
	{
		$query 		= $this->buildQueryVenues();
		$pagination = $this->getVenuesPagination();

		$rows 		= $this->_getList($query, $pagination->limitstart, $pagination->limit);

		return $rows;
	}


	/**
	 * venues-query
	 */
	function buildQueryVenues()
	{
		$app 				= JFactory::getApplication();
		$params		 		= JemHelper::globalattribs();
		$jinput				= $app->input;
		$itemid				= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_order 		= $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_order', 'filter_order', 'l.venue', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');

		$filter_order 		= JFilterInput::getinstance()->clean($filter_order, 'cmd');
		$filter_order_Dir 	= JFilterInput::getinstance()->clean($filter_order_Dir, 'word');

		$filter_type 		= $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$search      		= $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search      		= $this->_db->escape(trim(JString::strtolower($search)));

		// Query
		$db 	= JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('l.id','l.state','l.city','l.country','l.published','l.venue','l.ordering'));
		$query->from('#__jem_venues as l');

		// where
		$where = array();
		$where[] = 'l.published = 1';

		/* something to search for? (we like to search for "0" too) */
		if ($search || ($search === "0")) {
			switch ($filter_type) {
				case 1: /* Search venues */
					$where[] = 'LOWER(l.venue) LIKE "%' . $search . '%"';
					break;
				case 2: // Search city
					$where[] = 'LOWER(l.city) LIKE "%' . $search . '%"';
					break;
				case 3: // Search state
					$where[] = 'LOWER(l.state) LIKE "%' . $search . '%"';
			}
		}

		if ($params->get('global_show_ownedvenuesonly')) {
			$user = JFactory::getUser();
			$userid = $user->get('id');
			$where[] = ' created_by = ' . (int) $userid;
		}

		$query->where($where);

		if (strtoupper($filter_order_Dir) !== 'DESC') {
			$filter_order_Dir = 'ASC';
		}

		// ordering
		if ($filter_order && $filter_order_Dir) {
			$orderby = $filter_order . ' ' . $filter_order_Dir;
		} else {
			$orderby = array('l.venue ASC','l.ordering ASC');
		}
		$query->order($orderby);

		return $query;
	}

    /**
     * venues-Pagination
     **/
	function getVenuesPagination() {

		$jemsettings 		= JemHelper::config();
		$app 				= JFactory::getApplication();
		$jinput 			= $app->input;
		$itemid				= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		
		$limit 				= $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.limit', 'limit', $jemsettings->display_num, 'uint');
		$limitstart 		= $app->input->get('limitstart', 0, 'uint');
		
		$query = $this->buildQueryVenues();
		$total = $this->_getListCount($query);

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		return $pagination;
	}


	##############
	## CONTACTS ##
	##############

	/**
	 * Get contacts-data
	 */
	function getContacts()
	{
		$query 		= $this->buildQueryContacts();
		$pagination = $this->getContactsPagination();

		$rows 		= $this->_getList($query, $pagination->limitstart, $pagination->limit);

		return $rows;
	}


	/**
	 * contacts-Pagination
	 **/
	function getContactsPagination() {

		$jemsettings 		= JemHelper::config();
		$app 				= JFactory::getApplication();
		$jinput 			= $app->input;
		$itemid				= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		
		$limit 				= $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.limit', 'limit', $jemsettings->display_num, 'uint');
		$limitstart 		= $app->input->get('limitstart', 0, 'uint');
		
		$query = $this->buildQueryContacts();
		$total = $this->_getListCount($query);

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		return $pagination;
	}


	/**
	 * contacts-query
	 */
	function buildQueryContacts()
	{
		$app		  		= JFactory::getApplication();
		$jemsettings  		= JemHelper::config();
		$jinput 			= $app->input;
		$itemid				= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_order 		= $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_order', 'filter_order', 'con.ordering', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$filter_order 		= JFilterInput::getinstance()->clean($filter_order, 'cmd');
		$filter_order_Dir	= JFilterInput::getinstance()->clean($filter_order_Dir, 'word');

		$filter_type   		= $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$search       		= $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search       		= $this->_db->escape(trim(JString::strtolower($search)));

		// Query
		$db 	= JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('con.*'));
		$query->from('#__contact_details As con');

		// where
		$where = array();
		$where[] = 'con.published = 1';

		/* something to search for? (we like to search for "0" too) */
		if ($search || ($search === "0")) {
			switch ($filter_type) {
				case 1: /* Search name */
					$where[] = ' LOWER(con.name) LIKE \'%' . $search . '%\' ';
					break;
				case 2: /* Search address (not supported yet, privacy) */
					//$where[] = ' LOWER(con.address) LIKE \'%' . $search . '%\' ';
					break;
				case 3: // Search city
					$where[] = ' LOWER(con.suburb) LIKE \'%' . $search . '%\' ';
					break;
				case 4: // Search state
					$where[] = ' LOWER(con.state) LIKE \'%' . $search . '%\' ';
					break;
			}
		}
		$query->where($where);

		// ordering

		// ensure it's a valid order direction (asc, desc or empty)
		if (!empty($filter_order_Dir) && strtoupper($filter_order_Dir) !== 'DESC') {
			$filter_order_Dir = 'ASC';
		}

		if ($filter_order != '') {
			$orderby = $filter_order . ' ' . $filter_order_Dir;
			if ($filter_order != 'con.name') {
				$orderby = array($orderby, 'con.name'); // in case of city or state we should have a useful second ordering
			}
		} else {
			$orderby = 'con.name';
		}
		$query->order($orderby);

		return $query;
	}
}
