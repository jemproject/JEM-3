<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Holds all authentication logic
 */
class JEMUser {

	/**
	 * Checks access permissions of the user regarding on the groupid
	 *
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	static function validate_user($recurse, $level) {
		$user = JFactory::getUser();

		// Only check when user is logged in
		if ( $user->get('id') ) {
			//open for superuser or registered and thats all what is needed
			//level = -1 all registered users
			//level = -2 disabled
			if ((( $level == -1 ) && ( $user->get('id') )) || (( JFactory::getUser()->authorise('core.manage') ) && ( $level == -2 ))) {
				return true;
			}
		}
		// User has no permissions
		return false;
	}

	/**
	 * Checks if the user is allowed to edit an item
	 *
	 *
	 * @param int $allowowner
	 * @param int $ownerid
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	static function editaccess($allowowner, $ownerid, $recurse, $level) {
		$user = JFactory::getUser();

		$generalaccess = JEMUser::validate_user( $recurse, $level );

		if ($allowowner == 1 && ( $user->get('id') == $ownerid && $ownerid != 0 ) ) {
			return true;
		} elseif ($generalaccess == 1) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if the user is a superuser
	 * A superuser will allways have access if the feature is activated
	 *
	 * @return boolean True on success
	 */
	static function superuser() {
		
		$user = JFactory::getUser();
		if ($user->get('isRoot')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks if the user has the privileges to use the wysiwyg editor
	 *
	 * We could use the validate_user method instead of this to allow to set a groupid
	 * Not sure if this is a good idea
	 *
	 * @return boolean True on success
	 */
	static function editoruser() {
		$user 		= JFactory::getUser();
		$userGroups = $user->getAuthorisedGroups();

		$group_ids = array(
 					2, // registered
					3, // author
					4, // editor
					5, // publisher
					6, // manager
					7, // administrator
					8  // Super Users
					);

		foreach ($userGroups as $gid) {
			if (in_array($gid, $group_ids)) return true;
		}

		return false;
	}

	
	
	
	
	/**
	 * function to check if the user is allowed to change the state
	 */
	static function eventPublish($cats) {
		$user 		= JFactory::getUser();
	
		if ($user->get('guest') || $user->get('id') == 0) {
			// guest are handled differently	
			return false;
		}
	
		if (self::superuser()) {
			return true;
		}
	
		$settings = JemHelper::globalattribs();
		$options = $settings->get('acl_event_publish',false);
		
		if (!$options) {
			return false;
		}
		
		if (in_array(1, $options)) {
			// check for JEM Groups
			if (JemUser::ismaintainer('publishevent',false,$cats)) {
				return true;
			}
		}
	
		if (in_array(2, $options)) {
			// check for Joomla Groups
			if (JEMUser::JoomlaGroup('publish',$cats)) {
				return true;
			}
		}
	
		return false;
	}
	
	
	/**
	 * function to check if the user is able to submit Events
	 * or should be able to see the addEvent icon.
	 *
	 * @todo check if there available category's to post in
	 */
	static function addEvent($settings,$icon=false,$view=false) {
	
		if (self::superuser()) {
			// superuser has admin rights
			return true;
		}
	
		if ($icon) {
			$addicon	= $settings->get('acl_event_show_addicon',false);
				
			if (!$addicon) {
				// no icon to be displayed
				return false;
			}
		}
	
		//
		// let's see if the user is allowed to add events.
		// if the user is not allowed then the user shouldn't be able to see an icon either
		//
	
		$user 		= JFactory::getUser();
		if ($user->get('guest') || $user->get('id') == 0) {
			if (self::validate_guest()){
				// guest are allowed to add events so return
				return true;
			} else {
				// guest are not allowed to add events
				return false;
			}
		}
	
		$options = $settings->get('acl_event_add',false);
	
		if (!$options) {
			// JEM+Joomla groups are not allowed to submit events
			return false;
		}
	
		if (in_array(1, $options)) {
			// check for JEM Groups
			if (JemUser::ismaintainer($settings,'addevent',false,false,'event')) {
				return true;
			}
		}
	
		// a JEM group was not allowed so continue with Joomla group
		if (in_array(2, $options)) {
			// check for Joomla Groups
			if (JEMUser::JoomlaGroup($settings,'add',false,'event')) {
				return true;
			}
		}
	
		return false;
	}
	
	
	/**
	 * function to check if the user is allowed to post and if there is a category
	 * to post in.
	 */
	
	
	static function addVenue($settings,$icon=false,$view=false) {
	
		if (self::superuser()) {
			// superuser has admin rights
			return true;
		}
	
		if ($icon) {
			$addicon	= $settings->get('acl_venue_show_addicon',false);
				
			if (!$addicon) {
				// no icon to be displayed
				return false;
			}
		}
		
		$user 		= JFactory::getUser();
		if ($user->get('guest') || $user->get('id') == 0) {
			return false;
		}
	
		$options = $settings->get('acl_venue_add',false);
		
		if (!$options) {
			return false;
		}
		
		if (in_array(1, $options)) {
			// check for JEM Groups
			if (JemUser::venuegroups('add')) {
				return true;
			}
		}
	
		if (in_array(2, $options)) {
			// check for Joomla Groups
			if (JEMUser::JoomlaGroup($settings,'add',null,'venue')) {
				return true;
			}
		}
	
		return false;
	}
	
	
	
	
	/**
	 * Check if the current user is member of Joomla Group
	 */
	
	static function JoomlaGroup($params,$action=false,$cats=false,$type=false) {
		
		$user 		= JFactory::getUser();
		$userGroups = $user->getAuthorisedGroups();
		
		$options = $params->get('acl_'.$type.'_'.$action.'_joomlagroups',false);
		
		if (!$options) {
			return false;
		}
		
		// cats 
		if ($cats) {
			if ($action == 'publish') {
				foreach ($cats as $i => $cat) {
					if ($user->authorise('core.edit.state', 'com_jem.category.' . $cat) != true)
					{
						unset($cats[$i]);
					}
				}
					
				if ($cats) {
					return true;
				} else {
					return false;
				}
					
			}
			
			if ($action == 'edit') {
				// check if the user is allowed to post in a category
				//
				foreach ($cats as $i => $cat) {
					if ($user->authorise('core.edit', 'com_jem.category.' . $cat) != true)
					{
						unset($cats[$i]);
					}
				}
			
				if ($cats) {
					return true;
				} else {
					return false;
				}
			}
			
			return false;
		}
		
		if (empty($cats)) {
			// check for 'add' action
			if ($action != 'add') {	
				return false;
			} 
			
			// check if current user is able to add events
			$result = array_intersect($userGroups, $options);
			if (empty($result)) {
				return false;
			}
			
			return true;
		}
			
		return false;
	}
	
	
	/**
	 * Checks if the user is a maintainer of a category
	 * 
	 * @todo 
	 * when in 1 view it's probably to retrieve all groups to 1 category
	 * and all maintainer actions for the current user
	 */
	static function ismaintainer($params,$action, $eventid = false,$cats=false,$type=false)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$query	= $db->getQuery(true);
		$query->select(array('gr.id'));
		$query->from($db->quoteName('#__jem_groups').' AS gr');
		$query->join('LEFT', '#__jem_groupmembers AS g ON g.group_id = gr.id');
		$query->where(array('g.member = '. (int) $user->get('id'),$db->quoteName('gr.'.$action).' =1','g.member NOT LIKE 0'));
		$db->setQuery($query);
		$groupnumber = $db->loadColumn();
		
		if (!$groupnumber) {
			return false;
		} else {
			if ($action == 'publishevent' && $cats) {
				$query	= $db->getQuery(true);
				$query->select(array('c.id'));
				$query->from($db->quoteName('#__jem_categories').' AS c');
				$query->where(array('c.groupid IN (' . implode(',', $groupnumber) . ')'));
				$db->setQuery($query);
				$result = $db->loadColumn();
				
				if ($result) {
					return true;
				} else {
					return false;
				}
				
			} 
			
			if ($action == 'editevent' && $cats) {
				
				$result = false;
				$groupids = array();
				
				// Retrieve maintain groups for the category
				foreach ($cats AS $category) {
					$query	= $db->getQuery(true);
					$query->select(array('c.groupid'));
					$query->from($db->quoteName('#__jem_categories').' AS c');
					$query->where(array('c.id = '.$category));
					$db->setQuery($query);
					$groupid = $db->loadResult();
					
					if ($groupid) {
						// Convert the groupid field to an array.
						$registry = new JRegistry();
						$registry->loadString($groupid);
						$groupids = $registry->toArray();

						if (empty($groupids)) {
							// the format of the groupid was not JSON so don't
							// fire up the foreach loop
							$groupids[] =  $groupid;
						} else {
							// add the groupids to the array
							foreach ($groupids AS $groupid) {
								$groupids[] =  $groupid;
							}
						}
						
					}
				}
				
				// maintainer groups attached to this category
				if ($groupids) {
					$groupids = array_unique($groupids);
				} else {
					// we need groups
					return false;
				}
				
				// Retrieve the JEM-groups attached to this user with the needed action
				$query	= $db->getQuery(true);
				$query->select(array('a.id'));
				$query->from($db->quoteName('#__jem_groups').' AS a');
				$query->join('LEFT', '#__jem_groupmembers AS gm ON gm.group_id = a.id');
				
				$query->where(array($db->quoteName('a.'.$action),'gm.member = '.$user->get('id'),'gm.member NOT LIKE 0'));
				$db->setQuery($query);
				$jemgroup = $db->loadColumn();
				
				if (empty($jemgroup)) {
					return false;
				}
				
				$result = array_intersect($groupids, $jemgroup);
				
				if ($result) {
					return true;
				} else {
					return false;
				}
			
			} 
			
			if ($action == 'addevent') {
				return true;
			} else {
 				return false;
			}
		}
	}


	/**
	 * Checks if an user is a groupmember and if so
	 * if the group is allowed to add-venues
	 *
	 */
	static function venuegroups($action) {
		//lets look if the user is a maintainer
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();

		/*
		 * just a basic check to see if the current user is in an usergroup with
		 * access for submitting venues. if a result then return true, otherwise false
		 *
		 * Actions: addvenue, publishvenue, editvenue
		 *
		 * views: venues, venue, editvenue
		 */
		$query = 'SELECT gr.id'
				. ' FROM #__jem_groups AS gr'
				. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
				. ' AND '.$db->quoteName('gr.'.$action.'venue').' = 1 '
				. ' WHERE g.member = '.(int) $user->get('id')
				. ' AND g.member NOT LIKE 0';
				;
		$db->setQuery($query);

		$groupnumber = $db->loadResult();

		//no results
		if (!$groupnumber) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * validates guest rights
	 */
	static function validate_guest($option = false) {

		$user 		= JFactory::getUser();
		$guest 		= $user->get('guest');
		$settings 	= JemHelper::globalattribs();

		if ($guest) {

			# check if he global setting has been set
			$addevent	= $settings->get('guest_addevent',0);

			if (!$addevent) {
				return false;
			}

			# then check if we have 1 of the antispam measures enabled
			# if not then the guest is not allowed to submit events

			$mathquiz	= $settings->get('guest_as_math',0);
			$captcha	= $settings->get('guest_as_captcha',0);

			if (!$mathquiz && !$captcha) {
				return false;
			}

			return true;

		}
	}
	
	
	/**
	 * function to check if the user is allowed to edit
	 */
	static function editEvent($params,$icon=false,$eventid=false,$cats=false,$view=false, $created_by=false) {
	
		if (empty($eventid)) {
			// for editing we need an id
			return false;
		}
		
		$user 		= JFactory::getUser();
	
		if ($user->get('guest') || $user->get('id') == 0) {
			// guest are not allowed to edit
			return false;
		}
	
		if (self::superuser()) {
			return true;
		}
	
		if ($view == 'eventslist') {
			// only superuser should see editicon in eventslist view
			return false;
		}
		
		$editown = $params->get('acl_event_editown',false);
		$userId		= $user->get('id');
		
		if ($editown && $user->authorise('core.edit.own', 'com_jem.event.' . $eventid)) {
		
			// Now test the owner is the user.
			$ownerId = (int) isset($created_by) ? $created_by : 0;

			if (empty($ownerId) && $eventid)
			{
				// Need to do a lookup from the model.
				/*
				 $record = $this->getModel()->getItem($locid);
		
				 if (empty($record))
				 {
				 return false;
				 }
		
				 $ownerId = $record->created_by;
				 */
			}
		
			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
	
	
		$catids = array();
		if ($cats) {
			foreach ($cats AS $cat) {
				$catids[] = $cat->id;
			}
		}
	
		if (!($catids)) {
			// normally an event is attached to a category
			return false;
		}
	
		$options = $params->get('acl_event_edit',false);
	
		if (!$options) {
			return false;
		}
		
		if (in_array(1, $options)) {
			// check for JEM Groups
			if (JemUser::ismaintainer($params,'editevent',false,$catids,'event')) {
				return true;
			}
		}
	
		if (in_array(2, $options)) {
			// check for Joomla Groups
			if (JEMUser::JoomlaGroup($params,'edit',$catids,'event')) {
				return true;
			}
		}
	
		return false;
	}
	
	
	/**
	 * function to check if the user is allowed to edit
	 */
	static function editVenue($params,$icon=false, $eventid=false,$locid=false,$view=false,$created_by=false) {
	
		$user 		= JFactory::getUser();
		$userId		= $user->get('id');
		
		if ($user->get('guest') || $user->get('id') == 0) {
			// guest are not allowed to edit
			return false;
		}
		
		if (!($locid)) {
			// no locid so return
			return false;
		}
	
		if (self::superuser()) {
			// superuser is always able to edit
			return true;
		}
	
		$settings = JemHelper::globalattribs();
		$editown = $settings->get('acl_venue_editown',false);
		
		if ($editown && $user->authorise('core.edit.own', 'com_jem.venue.' . $locid)) {
		
			// Now test the owner is the user.
			$ownerId = (int) isset($created_by) ? $created_by : 0;
			
			if (empty($ownerId) && $locid)
			{
				// Need to do a lookup from the model.
				/*
				$record = $this->getModel()->getItem($locid);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
				*/
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		} 
		
		$options = $settings->get('acl_venue_edit',false);
		if (!$options) {
			return false;
		}
		
		// JEM Groups
		if (in_array(1, $options)) {
			if (JemUser::ismaintainer('editvenue',false,$locid)) {
				return true;
			}
		}
	
		// Joomla Groups
		if (in_array(2, $options)) {
			if (JEMUser::JoomlaGroup('editvenue',$locid)) {
				return true;
			}
		}
	
		return false;
	}
	
	
}
