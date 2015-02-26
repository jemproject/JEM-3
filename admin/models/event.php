<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
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
	 * Method to delete one or more records. (override)
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function delete(&$pks)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$pks = (array) $pks;
		$table = $this->getTable();

		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
        foreach ($pks as $i => $pk) {
            if ($table->load($pk)) {
                if ($this->canDelete($table)) {

					#####################################################
					## check if the event is part of a recurrence-set  ##
					#####################################################
					if ($table->recurrence_group) {

						# this event is part of a recurrence-set.

						# Retrieve id of current event from recurrence_table
						# as we're dealing with recurrence we'll check the recurrence_table
						#
						# we're checking:
						# - if groupid = groupid_ref
						# - if ItemId  = $record->id

						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->select('id');
						$query->from($db->quoteName('#__jem_recurrence'));
						$query->where(array('groupid = groupid_ref ', 'itemid= '.$table->id));
						$db->setQuery($query);
						$recurrenceid = $db->loadResult();

						# we know it's part of a set,
						# now check if there is 1 or more occurences of that group
						#
						# we're checking:
						# - if groupid = groupid_ref
						# - if GroupId = $record->recurrence_group

						if ($recurrenceid) {

							$db = JFactory::getDbo();
							$query = $db->getQuery(true);
							$query->select('COUNT(id)');
							$query->from($db->quoteName('#__jem_recurrence'));
							$query->where(array('groupid = groupid_ref ', 'groupid= '.$table->recurrence_group));
							$db->setQuery($query);
							$recurrenceid_count = $db->loadResult();

							# if count is 1 the row in the recurrence_table can be deleted completely
							# and we can also remove the other references linked to the recurrence-set
							if ($recurrenceid_count == 1) {

								# retrieve all id's from recurrence-table that are linked to the recurrence-set
								$db = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->select('id');
								$query->from($db->quoteName('#__jem_recurrence'));
								$query->where(array('groupid='.$table->recurrence_group));
								$db->setQuery($query);
								$recurrenceid = $db->loadColumn();

								$recurrence_table	= JTable::getInstance('Recurrence', 'JEMTable');

								# now loop the results and remove the references from the table
								foreach ($recurrenceid AS $row) {
									$recurrence_table->delete($row);
								}

								# furtermore we can remove the data from the master table
								$db = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->select('id');
								$query->from($db->quoteName('#__jem_recurrence_master'));
								$query->where(array('groupid = '.$table->recurrence_group));
								$db->setQuery($query);
								$masterid = $db->loadResult();

								$recurrence_master	= JTable::getInstance('Recurrence_master', 'JEMTable');
								$recurrence_master->delete($masterid);
							}

							# If the count is more then 1 we will add an Exdate value in the recurrence_table for this Itemid
							# The exdate is combined: startdate + enddate

							if ($recurrenceid_count > 1) {
								# combine startdate + starttime
								if (empty($table->times)){
									$table->times = '00:00:00';
								}

								$startDateTime	= $table->dates.' '.$table->times;
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
					$query->where('itemid = '.$table->id);

					$db->setQuery($query);
					$db->execute();

					$context = $this->option . '.' . $this->name;

					// Trigger the onContentBeforeDelete event.
					$result = $dispatcher->trigger($this->event_before_delete, array($context, $table));

					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					}

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}

					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger($this->event_after_delete, array($context, $table));
				}
				else
				{

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						JLog::add($error, JLog::WARNING, 'jerror');
						return false;
					}
					else
					{
						JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
						return false;
					}
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
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

		$jemsettings 	= JemHelper::config();
		$app 			= JFactory::getApplication();
		if ($app->isAdmin())
			$backend = true;
		else
			$backend = false;
		

		if ($this->getState('event.id')) {
			// existing event

			$pk = $this->getState('event.id');
			$item = $this->getItem($pk);

			if ($item->recurrence_group) {
				# the event is part of a recurrence_group
				#
				# we can disable the dates if needed
				/* $form->setFieldAttribute('dates', 'disabled', 'true'); */
				/* $form->setFieldAttribute('enddates', 'disabled', 'true'); */
			}

			if ($item->recurrence_groupcheck) {
				# disable recurrence fields
				$form->removeField('recurrence_count');
				$form->removeField('recurrence_exdates');
				$form->removeField('recurrence_freq');
				$form->removeField('recurrence_interval');
				$form->removeField('recurrence_until');
				$form->removeField('recurrence_weekday');
			}

			if (!empty ($item->meta_keywords )) {
				$meta_keywords = $item->meta_keywords;
			} else {
				$meta_keywords = $jemsettings->meta_keywords;
			}

			$form->setFieldAttribute('meta_keywords', 'default', $meta_keywords);

			if (!empty ($item->meta_description )) {
				$meta_description = $item->meta_description;
			} else {
				$meta_description = $jemsettings->meta_description;
			}

			$form->setFieldAttribute('meta_description', 'default', $meta_description);

		} else {
			// new event
			
			
			// specific backend settings
			if ($backend) {
				$settings 		= JemHelper::globalattribs();
				$registering = $settings->get('registering_b');
				$form->setFieldAttribute('registra', 'default', $registering);
				$unregistering = $settings->get('unregistering_b');
				$form->setFieldAttribute('unregistra', 'default', $unregistering);
			} else {
				$veditevent		= JemHelper::viewSettings('veditevent');
				$registering = $veditevent->get('registering');
				$form->setFieldAttribute('registra', 'default', $registering);
				$unregistering = $veditevent->get('unregistering');
				$form->setFieldAttribute('unregistra', 'default', $unregistering);
			}
			

			$meta_keywords = $jemsettings->meta_keywords;
			$form->setFieldAttribute('meta_keywords', 'default', $meta_keywords);

			$meta_description = $jemsettings->meta_description;
			$form->setFieldAttribute('meta_description', 'default', $meta_description);
		}

		$settings = JemHelper::globalattribs();
		$valguest = JEMUser::validate_guest();

		$asCaptcha	= $settings->get('guest_as_captcha','0');
		$asMath		= $settings->get('guest_as_math','0');

		if (!$valguest) {
			$form->removeField('captcha');
			$form->removeField('mathquiz');
			$form->removeField('mathquiz_answer');
			$form->removeField('timeout');
		}

		if ($valguest && !$asMath) {
			$form->removeField('mathquiz');
			$form->removeField('mathquiz_answer');
			$form->setFieldAttribute('articletext', 'buttons', 'false');
		}

		if ($valguest && !$asCaptcha) {
			$form->removeField('captcha');
			$form->setFieldAttribute('articletext', 'buttons', 'false');
		}
		
		
		
		

		return $form;
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
		$starthours		= $jinput->getCmd('starthours');
		$startminutes	= $jinput->getCmd('startminutes');
		$endhours		= $jinput->getCmd('endhours');
		$endminutes		= $jinput->getCmd('endminutes');

		// StartTime
		if ($starthours != '' && $startminutes != '') {
			$table->times = $starthours.':'.$startminutes;
		} elseif ($starthours != '' && $startminutes == '') {
			$startminutes = "00";
			$table->times = $starthours.':'.$startminutes;
		} elseif ($starthours == '' && $startminutes != '') {
			$starthours = "00";
			$table->times = $starthours.':'.$startminutes;
		} else {
			$table->times = "";
		}

		// EndTime
		if ($endhours != '' && $endminutes != '') {
			$table->endtimes = $endhours.':'.$endminutes;
		} elseif ($endhours != '' && $endminutes == '') {
			$endminutes = "00";
			$table->endtimes = $endhours.':'.$endminutes;
		} elseif ($endhours == '' && $endminutes != '') {
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
		$veditevent		= JemHelper::viewSettings('veditevent');
		$fileFilter 	= new JInput($_FILES);
		$table 			= $this->getTable();

		# Check if we're in the front or back
		if ($app->isAdmin())
			$backend = true;
		else
			$backend = false;

		$cats 						= $data['cats'];
		$data['author_ip']			= $jinput->getString('author_ip');

		## Recurrence - check option ##

		# if the option to hide the recurrence/other tab has been set (front) then
		# we should ignore the recurrence variables.

		$option_othertab	=	$veditevent->get('editevent_show_othertab');
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

			# check if a startdate has been set
			if (isset($data['dates'])) {
				if ($data['dates'] == null) {
					$dateSet = false;
				} else {
					$dateSet = true;
				}
			} else {
				$dateSet = false;
			}

			if (!isset($data['recurrence_freq'])) {
				$data['recurrence_freq'] = 0;
			}

			# implode weekday values
			# @todo implement check to see if days have been selected in case of freq week
			if (isset($data['recurrence_weekday'])) {
				$data['recurrence_weekday'] = implode(',', $data['recurrence_weekday']);
			}

			# blank recurrence-fields
			#
			# if we don't have a startdate or a recurrence-type then
			# the recurrence-fields within the event-table will be blanked.
			#
			# but the recurrence_group field will stay filled as it's not removed by the user.
			if (empty($data['dates']) || $data['recurrence_freq'] == '0')
			{
				$data['recurrence_count'] 		= '';
				$data['recurrence_freq']		= '';
				$data['recurrence_interval']	= '';
				$data['recurrence_until']		= '';
				$data['recurrence_weekday']		= '';
				$data['recurrence_exdates']		= '';
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
