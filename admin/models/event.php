<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Model: Event
 */
class JemModelEvent extends JModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id)) {
			if ($record->published != -2){
				return ;
			}
			
			# at this point the record has a status of -2 and can be removed
			# but as we're dealing with recurrences we've to do a bit extra

			# load variable
			$user = JFactory::getUser();

			if (!empty($record->catid)){
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__jem_cats_event_relations'));
				$query->where('itemid = '.$record->id);

				$db->setQuery($query);
				$db->execute();

				return $user->authorise('core.delete', 'com_jem.category.'.(int) $record->catid);
			} else {
				# as we don't have a catid in the event-table we're in this part.
				
				###############################################
				## check if the event is part of a groupset  ##
				###############################################
				if ($record->recurrence_group) {
					# this event is part of a recurrence-group.
					
					# Retrieve id of current event from recurrence_table
					# as we're dealing with recurrence we'll check the recurrence_table
					#
					# we're checking:
					# - if groupid = groupid_ref
					# - if ItemId  = $record->id
					#
					# @todo: check
					
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('id');
					$query->from($db->quoteName('#__jem_recurrence'));
					$query->where(array('groupid = groupid_ref ', 'itemid= '.$record->id));
					$db->setQuery($query);
					$recurrenceid = $db->loadResult();
												
					# we know it's part of a set, now check if there is 1 or more occurences of that group
					#
					# we're checking:
					# - if groupid = groupid_ref
					# - if GroupId = $record->recurrence_group
					
					if ($recurrenceid) {
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->select('COUNT(id)');
						$query->from($db->quoteName('#__jem_recurrence'));
						$query->where(array('groupid = groupid_ref ', 'groupid= '.$record->recurrence_group));
						$db->setQuery($query);
						$recurrenceid_count = $db->loadResult();
				
						# if count is 1 the row in the recurrence_table can be deleted completely
						if ($recurrenceid_count == 1) {
							$recurrence_table	= JTable::getInstance('Recurrence', 'JEMTable');
							$recurrence_table->delete($recurrenceid);
						}
						
						
						# If the count is more then 1 we will add an Exdate value in the recurrence_table for this Itemid
						# The exdate is combined: startdate + enddate
							
						if ($recurrenceid_count > 1) {
							# combine startdate + starttime
							if (empty($record->times)){
								$record->times = '00:00:00';
							}
							
							$startDateTime	= $record->dates.' '.$record->times;
							$datetime		= new JDate($startDateTime);
						
							# define Exdate variable
							$exdate = $datetime->format('Ymd\THis\Z');
						
							# We did calculate an exdate and will insert it in the recurrence-table
							$recurrence_table	= JTable::getInstance('Recurrence', 'JEMTable');
							$recurrence_table->load($recurrenceid);
							$recurrence_table->exdate = $exdate;
							$recurrence_table->deleted = '1';
							$recurrence_table->groupid_ref = '';
							$recurrence_table->store();
						}
					}
				} // close recurrence-check
				
				
				# actual deleting of the event.
				#
				# first the removal of the item-id from the catevent-table
				# and then the removal from the events-table
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__jem_cats_event_relations'));
				$query->where('itemid = '.$record->id);

				$db->setQuery($query);
				$db->execute();

				return $user->authorise('core.delete', 'com_jem');
			}
		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid)){
			return $user->authorise('core.edit.state', 'com_jem.category.'.(int) $record->catid);
		} else {
			return $user->authorise('core.edit.state', 'com_jem');
		}
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 *
	 */
	public function getTable($type = 'Events', $prefix = 'JEMTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jem.event', 'event', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		if ($this->getState('event.id')) {
			$pk = $this->getState('event.id');
			$items = $this->getItem($pk);
		
			if ($items->recurrence_group) {
				# the event is part of a recurrence_group
				#
				# we can disable the dates if needed
				/* $form->setFieldAttribute('dates', 'disabled', 'true'); */
				/* $form->setFieldAttribute('enddates', 'disabled', 'true'); */
			}
		}
		
		
		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$jemsettings = JEMAdmin::config();

		if ($item = parent::getItem($pk)){
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the metadata field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			$item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;

			$db = JFactory::getDbo();

			$query = $db->getQuery(true);
			$query->select(array('count(id)'));
			$query->from('#__jem_register');
			$query->where(array('event= '.$db->quote($item->id), 'waiting= 0'));

			$db->setQuery($query);
			$res = $db->loadResult();
			$item->booked = $res;

			$files = JEMAttachment::getAttachments('event'.$item->id);
			$item->attachments = $files;
			
			
			################
			## RECURRENCE ##
			################
			
			# check recurrence
			if ($item->recurrence_group) {
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
				$query->where(array('groupid= '.$item->recurrence_group, 'itemid= '.$item->id,'groupid = groupid_ref'));
			
				$db->setQuery($query);
				$rec_groupset_check = $db->loadResult();
					
				if ($rec_groupset_check == '1') {
					$item->recurrence_groupcheck = true;
				} else {
					$item->recurrence_groupcheck = false;
				}
			} else {
				$item->recurrence_groupcheck = false;
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
				$item->recurrence_country_holidays = true;
			} else {
				$item->recurrence_country_holidays = false;
			}			
		
		
			$item->author_ip = $jemsettings->storeip ? JemHelper::retrieveIP() : false;

			if (empty($item->id)){
				$item->country = $jemsettings->defaultCountry;
			}
		
			if (!empty($item->datimage)) {
				if (strpos($item->datimage,'images/') !== false) {
					# the image selected contains the images path
				} else {
					# the image selected doesn't have the /images/ path
					# we're looking at the locimage so we'll append the venues folder
					$item->datimage = 'images/jem/events/'.$item->datimage;
				}
			}
			
			
			$admin = JFactory::getUser()->authorise('core.manage', 'com_jem');
			if ($admin) {
				$item->admin = true;
			} else {
				$item->admin = false;
			}
			
			
		}
		
		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jem.edit.event.data', array());

		if (empty($data)){
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param $table JTable-object.
	 */
	protected function prepareTable($table)
	{
		$jinput 		= JFactory::getApplication()->input;
		
		$db = $this->getDbo();
		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);

		// Increment version number.
		$table->version ++;
		
		//get time-values from time selectlist and combine them accordingly
		$starthours		= $jinput->get('starthours','','cmd');
		$startminutes	= $jinput->get('startminutes','','cmd');
		$endhours		= $jinput->get('endhours','','cmd');
		$endminutes		= $jinput->get('endminutes','','cmd');
		
		// StartTime
		if ($starthours != '' && $startminutes != '') {
			$table->times = $starthours.':'.$startminutes;
		} else if ($starthours != '' && $startminutes == '') {
			$startminutes = "00";
			$table->times = $starthours.':'.$startminutes;
		} else if ($starthours == '' && $startminutes != '') {
			$starthours = "00";
			$table->times = $starthours.':'.$startminutes;
		} else {
			$table->times = "";
		}
		
		// EndTime
		if ($endhours != '' && $endminutes != '') {
			$table->endtimes = $endhours.':'.$endminutes;
		} else if ($endhours != '' && $endminutes == '') {
			$endminutes = "00";
			$table->endtimes = $endhours.':'.$endminutes;
		} else if ($endhours == '' && $endminutes != '') {
			$endhours = "00";
			$table->endtimes = $endhours.':'.$endminutes;
		} else {
			$table->endtimes = "";
		}	
	}

	/**
	 * Method to save the form data.
	 *
	 * @param $data array
	 */
	public function save($data)
	{
		$date 			= JFactory::getDate();
		$app 			= JFactory::getApplication();
		$jinput 		= $app->input;
		$user 			= JFactory::getUser();
		$jemsettings 	= JEMHelper::config();
		$settings 		= JemHelper::globalattribs();
		$fileFilter 	= new JInput($_FILES);
		$table 			= $this->getTable();

		# Check if we're in the front or back
		if ($app->isAdmin())
			$backend = true;
		else
			$backend = false;

		$cats 						= $data['cats'];
		$metakeywords 				= $jinput->get('meta_keywords', '', '');
		$metadescription 			= $jinput->get('meta_description', '', '');
		$author_ip 					= $jinput->get('author_ip', '', '');
		
		$data['meta_keywords'] 		= $metakeywords;
		$data['meta_description']	= $metadescription;
		$data['author_ip']			= $author_ip;
		
		
		## Recurrence - check option ##
		
		# if the option to hide the recurrence/other tab has been set (front) then
		# we should ignore the recurrence variables.
		
		$option_othertab	=	$settings->get('editevent_show_othertab');
		if ($option_othertab) {
			$hide_othertab = false;
		} else {
			$hide_othertab = true;
		}
		
		
		if ($backend || $hide_othertab == false) {
		
			##############
			## HOLIDAYS ##
			##############
			
			if (isset($data['activated'])) {
				if ($data['activated'] == null) {
					$holidays =	array();
				} else {
					$holidays =	$data['activated'];
				}
			} else {
				$holidays = array();
			}
			$countryholiday		= $jinput->getInt('recurrence_country_holidays','');
		
		
			################
			## RECURRENCE ##
			################
		
			# @todo:alter
			$recurrencenumber 	= $jinput->getInt('recurrence_interval', '');
			$recurrencebyday 	= $jinput->getString('recurrence_byday', '');
		

			# check for dates that should be skipped from generating events
			if (isset($data['dates'])) {
				if ($data['dates'] == null) {
					$dateSet = false;
				} else {
					$dateSet = true;
				}
			} else {
				$dateSet = false;
			}
		
		
			# blank recurrence-fields
			# 
			# if we don't have a startdate or a recurrence-type then 
			# the recurrence-fields within the event-table will be blanked.
			#
			# but the recurrence_group field will stay filled as it's not removed by the user.		
			if ($dateSet == false || $data['recurrence_type'] == '0')
			{
				$data['recurrence_interval']		= '';
				$data['recurrence_byday']		= '';
				$data['recurrence_counter'] 	= '';
				$data['recurrence_type']		= '';
				$data['recurrence_limit']		= '';
				$data['recurrence_limit_date']	= '';
				$data['recurrence_first_id']	= '';
				$data['recurrence_exdates']		= '';
			} else {
				# in here we know that there is a date and that we do have a recurrence-type
				# so we can store the recurrence-info
				$data['recurrence_interval']	= $recurrencenumber;
				$data['recurrence_byday']		= $recurrencebyday;
			}

			# the exdates are not stored in the event-table but they are trown in an variable
			if (isset($data['recurrence_exdates'])) {
				$exdates = $data['recurrence_exdates'];
			} else {
				$exdates = false;
			}
		}
		
		# parent-Save
		if (parent::save($data)){

			// At this point we do have an id.
			$pk = $this->getState($this->getName() . '.id');

			if (isset($data['featured'])){
				$this->featured($pk, $data['featured']);
			}
			
			
			$checkAttachName = $jinput->post->get('attach-name','','array');
			
			if ($checkAttachName) {
				# attachments, new ones first
				$attachments 				= array();
				$attachments 				= $fileFilter->get('attach', array(), 'array');
				$attachments['customname']	= $jinput->post->get('attach-name', array(),'array');
				$attachments['description'] = $jinput->post->get('attach-desc', array(),'array');
				$attachments['access'] 		= $jinput->post->get('attach-access', array(),'array');
				JEMAttachment::postUpload($attachments, 'event' . $pk);
				
				# and update old ones
				$old				= array();
				$old['id'] 			= $jinput->post->get('attached-id', array(),'array');
				$old['name'] 		= $jinput->post->get('attached-name', array(),'array');
				$old['description'] = $jinput->post->get('attached-desc', array(),'array');
				$old['access'] 		= $jinput->post->get('attached-access', array(),'array');
				
				foreach ($old['id'] as $k => $id){
					$attach 				= array();
					$attach['id'] 			= $id;
					$attach['name'] 		= $old['name'][$k];
					$attach['description'] 	= $old['description'][$k];
					$attach['access'] 		= $old['access'][$k];
					JEMAttachment::update($attach);
				}
			} 

			# Store categories
			$cats	= $data['cats'];
			
			$db 	= $this->getDbo();
			$query 	= $db->getQuery(true);

			$query->delete($db->quoteName('#__jem_cats_event_relations'));
			$query->where('itemid = ' . $pk);
			$db->setQuery($query);
			$db->execute();

			foreach ($cats as $cat){
				$db 	= $this->getDbo();
				$query	= $db->getQuery(true);

				// Insert columns.
				$columns = array('catid','itemid');

				// Insert values.
				$values = array($cat,$pk);

				// Prepare the insert query.
				$query->insert($db->quoteName('#__jem_cats_event_relations'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				$db->execute();
			}

			
			if ($backend || $hide_othertab == false) {
			
				# check for recurrence
				# when part of a recurrence_set it will not perform the generating function
			
				/*
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from($db->quoteName('#__jem_recurrence'));
				$query->where(array('exdate IS NULL','itemid ='.$pk));
				$db->setQuery($query);
				$recurrence_set = $db->loadResult();
				*/
			
				$table->load($pk);
			
				# check recurrence
				if ($table->recurrence_group) {
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
					$query->where(array('groupid= '.$table->recurrence_group, 'itemid= '.$pk,'groupid = groupid_ref'));
		
					$db->setQuery($query);
					$rec_groupset_check = $db->loadResult();
										
					if ($rec_groupset_check == '1') {
						$recurrence_set = true;
					} else {
						$recurrence_set = false;
					}
				} else {
					$recurrence_set = false;
				}
			
		
				## check values, pass check before we continue to generate additional events ##

				# - do we have an interval?
				# - does the event has a date?
				# - is the event part of a recurrenceset?
				
				if ($table->recurrence_interval > 0 && !$table->dates == null && $recurrence_set == null){
					
					
					
					# recurrence_interval is bigger then 0
					# we do have a startdate
					# the event is not part of a recurrence-set
								
					# we passed the check but now we'll pass some variables to the generating functions
					#
					# holidays: the holidays that were checked
					# exdates: the dates filled
					# table: the row info
				
					if ($this->state->task == 'apply' || $this->state->task == 'save') {
						JemHelper::generate_events($table,$exdates,$holidays);
					}
				}
			}
			

			return true;
		}

		return false;
	}

	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks)) {
			$this->setError(JText::_('COM_JEM_EVENTS_NO_ITEM_SELECTED'));
			return false;
		}

		try {
			$db = $this->getDbo();

			$db->setQuery(
					'UPDATE #__jem_events' .
					' SET featured = '.(int) $value.
					' WHERE id IN ('.implode(',', $pks).')'
			);
			if (!$db->execute()) {
				throw new Exception($db->getErrorMsg());
			}

		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		$this->cleanCache();

		return true;
	}
}