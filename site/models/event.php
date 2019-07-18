<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Model-Event
 */
class JemModelEvent extends JModelItem
{
	# Model context string
	protected $_context = 'com_jem.event';

	protected $_registers = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');
		$settings = JemHelper::globalattribs();
		$jinput = $app->input;

		// Load state from the request.
		$pk = $jinput->getInt('id');
		$this->setState('event.id', $pk);

		$offset = $jinput->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// define params
		$global = new JRegistry;
		$global->loadString($settings);
		
		$params = clone $global;
		$params->merge($global);
		if ($menu = $app->getMenu()->getActive())
		{
			$params->merge($menu->params);
		}
		$this->setState('params', $params);

		// @todo: Tune these values based on other permissions.
		$user = JFactory::getUser();
		$userId = $user->get('id');
		if ((!$user->authorise('core.edit.state', 'com_jem')) && (!$user->authorise('core.edit', 'com_jem'))) {
			/* $this->setState('filter.published', 1); */
			/* $this->setState('filter.archived', 2); */
		} 
		
		$this->setState('filter.access', true);
		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

	/**
	 * Method to get event data.
	 *
	 * @param integer	The id of the event.
	 * @return mixed item data object on success, false on failure.
	 */
	public function &getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('event.id');
		
		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {
				$settings = JEMHelper::globalattribs();

				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select(
						$this->getState('item.select',
								'a.id, a.access, a.attribs, a.metadata, a.registra, a.custom1, a.custom2, a.custom3, a.custom4, a.custom5, a.custom6, a.custom7, a.custom8, a.custom9, a.custom10, a.times, a.endtimes, a.dates, a.enddates, a.id AS did, a.title, a.alias, ' .
										'a.created, a.unregistra, a.published, a.created_by, ' .
										'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' . 'a.modified_by, a.checked_out, a.checked_out_time, ' . 'a.datimage,  a.version, ' .
										'a.meta_keywords, a.created_by_alias, a.introtext, a.fulltext, a.maxplaces, a.waitinglist, a.meta_description, a.hits, a.language, a.recurrence_group,' .
										'a.recurrence_type, a.recurrence_first_id,a.registering'));
				$query->from('#__jem_events AS a');

				// Join on user table.
				$name = $settings->get('global_regname','1') ? 'u.name' : 'u.username';
				$query->select(array($name.' AS author','u.name','u.username'));
				$query->join('LEFT', '#__users AS u on u.id = a.created_by');

				// Join on contact-user table.
				$query->select('con.id AS conid, con.name AS conname, con.telephone AS contelephone, con.email_to AS conemail');
				$query->join('LEFT', '#__contact_details AS con ON con.id = a.contactid');

				// Join on venue table.
				$query->select('l.custom1 AS venue1, l.custom2 AS venue2, l.custom3 AS venue3, l.custom4 AS venue4, l.custom5 AS venue5, l.custom6 AS venue6, l.custom7 AS venue7, l.custom8 AS venue8, l.custom9 AS venue9, l.custom10 AS venue10, ' .
				               'l.id AS locid, l.alias AS localias, l.venue, l.city, l.state, l.url, l.locdescription, l.locimage, l.city, l.postalCode, l.street, l.country,l.phone,l.fax,l.email,l.map, l.created_by AS venueowner, l.latitude, l.longitude, l.timezone, l.checked_out AS vChecked_out, l.checked_out_time AS vChecked_out_time');
				$query->join('LEFT', '#__jem_venues AS l ON a.locid = l.id');

				// Filter by language
				if ($this->getState('filter.language')) {
					$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}
				
				$query->where('a.id = ' . (int) $pk);

				// Filter by start and end dates.
				$nullDate = $db->Quote($db->getNullDate());
				$date = JFactory::getDate();

				$nowDate = $db->Quote($date->toSql());


				// Filter by published state.
				$published = $this->getState('filter.published');

				$archived = $this->getState('filter.archived');

				if (is_numeric($published)) {
					$query->where('(a.published = ' . (int) $published . ' OR a.published =' . (int) $archived . ')');
				}

				$db->setQuery($query);
				$data = $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}
				
				if (empty($data)) {
					throw new Exception(JText::_('COM_JEM_EVENT_ERROR_EVENT_NOT_FOUND'), 404);
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry;
				$registry->loadString($data->attribs);

				$globalattribs = JEMHelper::globalattribs();
				$globalregistry = new JRegistry;
				$globalregistry->loadString($globalattribs);

				$data->params = clone $globalregistry;
				$data->params->merge($registry);
				
				$registry = new JRegistry;
				$registry->loadString($data->registering);
				$data->registering = $registry;

				 $registry = new JRegistry;
				 $registry->loadString($data->metadata);
				 $data->metadata = $registry;

				// Compute selected asset permissions.
				$user = JFactory::getUser();

				// Technically guest could edit an event, but lets not check
				// that to improve performance a little.
				if (!$user->get('guest')) {
					$userId = $user->get('id');
					$asset = 'com_jem.event.' . $data->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset)) {
						$data->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by) {
							$data->params->set('access-edit', true);
						}
					}
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this
					// user can view.
					
					$category_viewable = $this->getCategories($pk);
					if ($category_viewable) {
						$data->params->set('access-view', true);
					} else {
						$data->params->set('access-view', false);
					}
					
				}
				else {

					# retrieve category's that the user is able to see
					# if there is no category the event should not be displayed

					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					if ($category_viewable) {
						$data->params->set('access-view', true);
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (JException $e) {
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
					return false;
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
					return false;
				}
			}
		}

		// Define Attachments
		$user = JFactory::getUser();
		$this->_item[$pk]->attachments = JEMAttachment::getAttachments('event' . $this->_item[$pk]->did);

		// Define Venue-Attachments
		$this->_item[$pk]->vattachments = JEMAttachment::getAttachments('venue' . $this->_item[$pk]->locid);

		// Define Booked
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
				'COUNT(*)'
		));
		$query->from('#__jem_register');
		$query->where(array(
				'event= ' . $db->quote($this->_item[$pk]->did),
				'waiting= 0'
		));
		$db->setQuery($query);
		$res = $db->loadResult();
		$this->_item[$pk]->booked = $res;

		// Define Waiters
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
				'COUNT(*)'
		));
		$query->from('#__jem_register');
		$query->where(array(
				'event= ' . $db->quote($this->_item[$pk]->did),
				'waiting= 1'
		));
		$db->setQuery($query);
		$res2 = $db->loadResult();
		$this->_item[$pk]->waiters = $res2;

		
		$this->_item[$pk]->categories = $category_viewable;

		return $this->_item[$pk];
	}

	/**
	 * Increment the hit counter for the event.
	 *
	 * @param int		Optional primary key of the event to increment.
	 * @return boolean if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$jinput = JFactory::getApplication()->input;
		$hitcount = $jinput->getInt('hitcount', 1);

		if ($hitcount) {
			// Initialise variables.
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('event.id');
			$db = $this->getDbo();

			$db->setQuery('UPDATE #__jem_events' . ' SET hits = hits + 1' . ' WHERE id = ' . (int) $pk);

			if (!$db->execute()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}


	/**
	 * Retrieve Categories
	 *
	 * Due to multi-cat this function is needed
	 * filter-index (4) is pointing to the cats
	 */

	function getCategories($id = 0)
	{

		$id = (!empty($id)) ? $id : (int) $this->getState('event.id');

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
		
		$query->where('rel.itemid ='.(int)$id);
		$query->where('c.published = 1');

		###################
		## FILTER-ACCESS ##
		###################

		# Filter by access level.
		$access = $this->getState('filter.access');

		###################################
		## FILTER - MAINTAINER/JEM GROUP ##
		###################################

		if ($access){
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('(c.access IN ('.$groups.'))');
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
		$cats = $db->loadObjectList();

		return $cats;
	}

	/**
	 * Method to check if the user is already registered
	 * return false if not registered, 1 for registered, 2 for waiting list
	 *
	 * @access public
	 * @return mixed false if not registered, 1 for registerd, 2 for waiting
	 *         list
	 *
	 */
	function getUserIsRegistered()
	{
		// Initialize variables
		$user	= JFactory::getUser();
		$userid = (int) $user->get('id', 0);

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(array('waiting+1')); // 1 if user is registered, 2 if on waiting
		$query->from('#__jem_register');
		$query->where(array('uid = '.$userid,'event = '. $this->getState('event.id')));

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Method to get the registered users
	 *
	 * @access public
	 * @return object
	 *
	 */
	function getRegisters($event = false)
	{
		// Get registered users
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(array('u.name,u.username, r.uid'));
		$query->from('#__jem_register as r');
		$query->join('LEFT', '#__users AS u ON u.id = r.uid');
		$query->where(array('r.event = '.$event,'r.waiting = 0'));
		$db->setQuery($query);

		$registered = $db->loadObjectList();

		return $registered;
	}


	function setId($id)
	{
		// Set new event ID and wipe data
		$this->_registerid = $id;
	}

	/**
	 * Saves the registration to the database
	 *
	 * @access public
	 * @return int register id on success, else false
	 *
	 */
	function userregister()
	{
		$user = JFactory::getUser();
		$jemsettings = JEMHelper::config();

		$eventid = (int) $this->_registerid;

		$uid = (int) $user->get('id');
		$onwaiting = 0;

		// Must be logged in
		if ($uid < 1) {
			JError::raiseError(403, JText::_('COM_JEM_ALERTNOTAUTH'));
			return;
		}

		try {
			$event = $this->getItem($eventid);
		}
		// error handling
		catch (Exception $e) {
			$event = false;
		}
		if (empty($event)) {
			$this->setError(JText::_('COM_JEM_EVENT_ERROR_EVENT_NOT_FOUND'));
			return false;
		}

		if ($event->maxplaces > 0) 		// there is a max
		{
			// check if the user should go on waiting list
			if ($event->booked >= $event->maxplaces) {
				if (!$event->waitinglist) {
					$this->setError(JText::_('COM_JEM_ERROR_REGISTER_EVENT_IS_FULL'));
					return false;
				}
				$onwaiting = 1;
			}
		}

		// IP
		$uip = $jemsettings->storeip ? JemHelper::retrieveIP() : false;

		$obj = new stdClass();
		$obj->event = (int) $eventid;
		$obj->waiting = $onwaiting;
		$obj->uid = (int) $uid;
		$obj->uregdate = gmdate('Y-m-d H:i:s');
		$obj->uip = $uip;
		$this->_db->insertObject('#__jem_register', $obj);

		return $this->_db->insertid();
	}

	/**
	 * Deletes a registered user
	 *
	 * @access public
	 * @return true on success
	 *
	 */
	function delreguser()
	{
		$user = JFactory::getUser();

		$eventid = (int) $this->_registerid;
		$userid = $user->get('id');

		// Must be logged in
		if ($userid < 1) {
			JError::raiseError(403, JText::_('COM_JEM_ALERTNOTAUTH'));
			return;
		}

		$db 	= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query->delete('#__jem_register');
		$query->where(array('event = ' . $eventid,'uid= ' . $userid));

		$db->SetQuery($query);

		if (!$db->execute()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		return true;
	}

	function getKunenaConfig() {
		static $kconfig = false;
		if ($kconfig === false) {
			// Run only one time
			$kconfig = null;

			// Make sure that Kunena API (if exists) has been loaded
			$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
			if (is_file($api))
				require_once $api;

			if (class_exists('KunenaFactory')) {

				// Support for Kunena 1.6, 1.7 and 2.0
				$kconfig = KunenaFactory::getConfig();

			} elseif (is_file(JPATH_ROOT.'/components/com_kunena/lib/kunena.config.class.php')) {

				// Support for Kunena 1.0 and 1.5
				require_once JPATH_ROOT.'/components/com_kunena/lib/kunena.config.class.php';

				// Next 4 lines are needed to make <1.0.9 and <1.5.2 to work
				if (is_file(JPATH_ROOT.'/components/com_kunena/lib/kunena.debug.php'))
					require_once JPATH_ROOT.'/components/com_kunena/lib/kunena.debug.php';
				if (is_file(JPATH_ROOT.'/components/com_kunena/lib/kunena.user.class.php'))
					require_once JPATH_ROOT.'/components/com_kunena/lib/kunena.user.class.php';

				if (method_exists('CKunenaConfig', 'getInstance')) {
					// Support for Kunena 1.0.9+ and 1.5
					$kconfig = CKunenaConfig::getInstance();

				} elseif (class_exists('CKunenaConfig')) {
					// Support for Kunena 1.0.8
					$kconfig = new CKunenaConfig();
					$kconfig->load();

				} elseif (class_exists('fb_Config')) {
					// Support for Kunena 1.0.6RC2 and 1.0.7b
					$kconfig = new fb_Config();
					$kconfig->load();

				}
			}
		}
		return $kconfig;
	}
}
