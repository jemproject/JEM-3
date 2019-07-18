<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Table: Events
 */
class JemTableEvents extends JTable
{
	public function __construct(&$db)
    {
		parent::__construct('#__jem_events', 'id', $db);
    }

    
    /**
     * Method to compute the default name of the asset.
     * The default name is in the form table_name.id
     * where id is the value of the primary key of the table.
     *
     * @return  string
     */
    protected function _getAssetName()
    {
    	$k = $this->_tbl_key;
    	return 'com_jem.event.' . (int) $this->$k;
    }
    
    /**
     * Method to return the title to use for the asset table.
     *
     * @return  string
     */
    protected function _getAssetTitle()
    {
    	return $this->title;
    }
    
    
	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID,
	 * which will default to 1 if none exists.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// For simple cases, parent to the asset root.
		$assets = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
		$rootId = $assets->getRootId();

		if (!empty($rootId))
		{
			return $rootId;
		}

		return 1;
	}
    
  
    
	/**
	 * Overloaded bind method for the Event table.
	 */
	public function bind($array, $ignore = '')
	{
		// in here we are checking for the empty value of the checkbox

		if (!isset($array['registra'])) {
			$array['registra'] = 0 ;
		}

		if (!isset($array['unregistra'])) {
			$array['unregistra'] = 0 ;
		}

		if (!isset($array['waitinglist'])) {
			$array['waitinglist'] = 0 ;
		}

		// Search for the {readmore} tag and split the text up accordingly.
		if (isset($array['articletext'])) {
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $array['articletext']);

			if ($tagPos == 0) {
				$this->introtext = $array['articletext'];
				$this->fulltext = '';
			} else {
				list ($this->introtext, $this->fulltext) = preg_split($pattern, $array['articletext'], 2);
			}
		}

		if (isset($array['attribs']) && is_array($array['attribs'])) {
			$registry = new JRegistry;
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata'])) {
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}
		
		if (isset($array['registering']) && is_array($array['registering'])) {
			$registry = new JRegistry;
			$registry->loadArray($array['registering']);
			$array['registering'] = (string) $registry;
		}
		
		// Bind the rules.
		// libraries/legacy/table/content.php
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}
		
		return parent::bind($array, $ignore);
	}

	/**
	 * overloaded check function
	 */
	public function check()
	{
		$jinput = JFactory::getApplication()->input;

		if (trim($this->title) == ''){
			$this->setError(JText::_('COM_JEM_EVENT_ERROR_NAME'));
			return false;
		}

		if (trim($this->alias) == ''){
			$this->alias = $this->title;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);
		if (empty($this->alias)) {
			$this->alias = JApplication::stringURLSafe($this->title);
			if (trim(str_replace('-', '', $this->alias)) == ''){
				$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
			}
		}

		###############
		## DATE-TIME ##
		###############

		// default empty values to null
		# user didn't select a value for it

		if (empty($this->times)) {
			$this->times = null;
		}
		if (empty($this->endtimes)) {
			$this->endtimes = null;
		}
		if (empty($this->dates) || $this->enddates == '0000-00-00') {
			$this->dates = null;
		}
		if (empty($this->enddates) || $this->enddates == '0000-00-00') {
			$this->enddates = null;
		}

		// opendate
		# do we have a startdate?
		# if no then we consider it an "open date"
		$this->opendate = 0;
		$opendate = false;
		if ($this->dates == null) {
			// $this->times 	= null;
			$this->enddates = null;
			// $this->endtimes = null;

			$this->opendate = 1;
			$opendate = true;
		}

		// combine DateTime
		# startDateTime
		if ($this->dates == null) {
			$startDate = '0000-00-00';
		} else {
			$startDate = $this->dates;
		}
		if ($this->times == null) {
			$startTime = '00:00:00';
		} else {
			$startTime = $this->times.':00';
		}
		$this->startDateTime	= $startDate.' '.$startTime;

		# endDateTime
		if ($this->enddates == null) {
			$endDate = '0000-00-00';
		} else {
			$endDate = $this->enddates;
		}
		if ($this->endtimes == null) {
			$endTime = '00:00:00';
		} else {
			$endTime = $this->endtimes.':00';
		}
		if ($endDate == '0000-00-00') {
			if ($endTime != '00:00:00') {
				$this->endDateTime		= $startDate.' '.$endTime;
			} else {
				$this->endDateTime		= $startDate.' '.$startTime;
			}
		} else {
			$this->endDateTime		= $endDate.' '.$endTime;
		}

		// check if endDateTime is before startDateTime
		if ($startDate != '0000-00-00' && !$opendate) {
			if ($this->startDateTime > $this->endDateTime) {
				$this->setError(JText::_('COM_JEM_EVENT_ERROR_END_BEFORE_START'));
			}
		}
		
		
		/*
		 * lirbraries/cms/html/rules.php
		 * 
		if (!$this->id)
		{
			// If we don't have any access rules set at this point just use an empty JAccessRules class
			if (!isset($this->rules))
			{
				$rules = $this->getDefaultAssetValues('com_jem');
				$this->setRules($rules);
			}
		}
		*/

		if (!$this->getErrors()) {
			return true;
		}

	}

	/**
	 * Store
	 */
	public function store($updateNulls = true)
	{
		$date 			= JFactory::getDate();
		$user 			= JFactory::getUser();
		$jinput 		= JFactory::getApplication()->input;
		$app 			= JFactory::getApplication();
		$jemsettings 	= JEMHelper::config();
		$settings 		= JemHelper::globalattribs();
		$valguest		= JEMUser::validate_guest();
		$guest_fldstatus = $settings->get('guest_fldstatus','0');

		// Check if we're in the front or back
		if ($app->isAdmin())
			$backend = true;
		else
			$backend = false;

		if ($this->id) {
			// Existing event
			$this->modified = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New event
			if (!intval($this->created)){
				$this->created = $date->toSql();
			}
			if (empty($this->created_by)){
				$this->created_by = $user->get('id');
			}
		}

		// Check if image was selected
		jimport('joomla.filesystem.file');
		$image_dir = JPATH_SITE.'/images/jem/events/';
		$allowable = array ('gif', 'jpg', 'png');
		$image_to_delete = false;

		// get image (frontend) - allow "removal on save" (Hoffi, 2014-06-07)
		if (!$backend) {
			if (($jemsettings->imageenabled == 2 || $jemsettings->imageenabled == 1)) {
				$file = JFactory::getApplication()->input->files->get('userfile', '', 'array');
				$removeimage = JFactory::getApplication()->input->get('removeimage', '', 'int');

				if (!empty($file['name'])) {
					//check the image
					$check = JEMImage::check($file, $jemsettings);

					if ($check !== false) {
						//sanitize the image filename
						$filename = JemHelper::sanitize($image_dir, $file['name']);
						$filepath = $image_dir . $filename;

						if (JFile::upload($file['tmp_name'], $filepath)) {
							$image_to_delete = $this->datimage; // delete previous image
							$this->datimage = $filename;
						}
					}
				} elseif (!empty($removeimage)) {
					// if removeimage is non-zero remove image from event
					// (file will be deleted later (e.g. housekeeping) if unused)
					$image_to_delete = $this->datimage;
					$this->datimage = '';
				}
			} // end image if
		} // if (!backend)

		$format = JFile::getExt($image_dir . $this->datimage);
		if (!in_array($format, $allowable))
		{
			$this->datimage = '';
		}

		if (!$backend) {
			if ($valguest) {
				$this->published = $guest_fldstatus;
			} else {
				$jinput	= JFactory::getApplication()->input;
				$data = $jinput->post->get('jform', array(),'array');
				$cats = $data['cats'];
				if ($cats) {
					if (!JEMUser::eventPublish($cats)) {
						$this->published = 0;
					}
				} else {
					$this->published = 0;
				}
			}
		}

		################
		## RECURRENCE ##
		################

		# check if recurrence_groupcheck is true
		$rec_groupcheck		= $jinput->getInt('recurrence_check');

		if ($rec_groupcheck) {
			# the check returned true, so it's considered as an edit

			# Retrieve id of current event from recurrence_table
			# as the check was true we can skip the groupid=groupid_ref from the where statement
			# but to be sure it's added here too
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__jem_recurrence'));
			$query->where(array('groupid = groupid_ref ', 'itemid= '.$this->id));
			$db->setQuery($query);
			$recurrenceid = $db->loadResult();

			if ($recurrenceid) {
				# Retrieve recurrence-table
				$recurrence_table	= JTable::getInstance('Recurrence', 'JEMTable');
				# Load row-data
				$recurrence_table->load($recurrenceid);

				# We want to skip this event from Ical output
				/* $recurrence_table->exdate = $this->dates.'T'.$this->times; */
				# it's a delete of the set so groupid_ref will be blanked
				/* $recurrence_table->groupid_ref = ""; */

				# it's an edit and not a delete so groupid_ref won't be adjusted
				# but we will set the recurrence_id field, as this event has been adjusted and contains
				# info that's not inline with original recurrence-info

				$var2 	= 	$recurrence_table->startdate_org;
				$var3	=	new JDate($var2);
				$var4	=	$var3->format('Ymd\THis\Z');
				$recurrence_table->recurrence_id = $var4;

				# Store fields
				$recurrence_table->store();
			}
		}

		# check if the field recurrence_group is filled and if the recurrence_type has been set
		# if the type has been set then it's part of recurrence and we should have a recurrence_group number
		if (empty($this->recurrence_group) && $this->recurrence_freq) {
			$this->recurrence_group = mt_rand(0,9999999);
		}

		## END RECURRENCE ##

		/* return parent::store($updateNulls); */

		// No return to default JTable as it will result into a problem with the assets table
		// so just in case we're continueing with the code below but with an unset for $assets->alias
		
		$k = $this->_tbl_keys;
		
		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeStore', array($updateNulls, $k));
		
		$currentAssetId = 0;
		
		if (!empty($this->asset_id))
		{
			$currentAssetId = $this->asset_id;
		}
		
		// The asset id field is managed privately by this class.
		if ($this->_trackAssets)
		{
			unset($this->asset_id);
		}
		
		// If a primary key exists update the object, otherwise insert it.
		if ($this->hasPrimaryKey())
		{
			$result = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_keys, $updateNulls);
		}
		else
		{
			$result = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_keys[0]);
		}
		
		// If the table is not set to track assets return true.
		if ($this->_trackAssets)
		{
			if ($this->_locked)
			{
				$this->_unlock();
			}
		
			/*
			 * Asset Tracking
			 */
			$parentId = $this->_getAssetParentId();
			$name     = $this->_getAssetName();
			$title    = $this->_getAssetTitle();
		
			$asset = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
			$asset->loadByName($name);
					
			// Re-inject the asset id.
			$this->asset_id = $asset->id;
		
			// Check for an error.
			$error = $asset->getError();
		
			if ($error)
			{
				$this->setError($error);
		
				return false;
			}
			else
			{
				// Specify how a new or moved node asset is inserted into the tree.
				if (empty($this->asset_id) || $asset->parent_id != $parentId)
				{
					$asset->setLocation($parentId, 'last-child');
				}
		
				// Prepare the asset to be stored.
				$asset->parent_id = $parentId;
				$asset->name      = $name;
				$asset->title     = $title;
				unset ($asset->alias);
		
		
				if ($this->_rules instanceof JAccessRules)
				{
					$asset->rules = (string) $this->_rules;
				}
		
				if (!$asset->check() || !$asset->store($updateNulls))
				{
					$this->setError($asset->getError());
					return false;
				}
				else
				{
					// Create an asset_id or heal one that is corrupted.
					if (empty($this->asset_id) || ($currentAssetId != $this->asset_id && !empty($this->asset_id)))
					{
						// Update the asset_id field in this table.
						$this->asset_id = (int) $asset->id;
		
						$query = $this->_db->getQuery(true)
						->update($this->_db->quoteName($this->_tbl))
						->set('asset_id = ' . (int) $this->asset_id);
						$this->appendPrimaryKeys($query);
						$this->_db->setQuery($query)->execute();
					}
				}
			}
		}
		
		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterStore', array(&$result));
		
		return $result;
	}

	/**
	 * try to insert first, update if fails
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function insertIgnore($updateNulls=false)
	{
		$ret = $this->_insertIgnoreObject($this->_tbl, $this, $this->_tbl_key);
		if(!$ret) {
			$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Inserts a row into a table based on an objects properties, ignore if already exists
	 *
	 * @access protected
	 * @param string  The name of the table
	 * @param object  An object whose properties match table fields
	 * @param string  The name of the primary key. If provided the object property is updated.
	 * @return int number of affected row
	 */
	protected function _insertIgnoreObject($table, &$object, $keyName = NULL)
	{
		$fmtsql = 'INSERT IGNORE INTO '.$this->_db->quoteName($table).' (%s) VALUES (%s) ';
		$fields = array();

		foreach (get_object_vars($object) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->_db->quoteName($k);
			$values[] = $this->_db->quote($v);
		}

		$this->_db->setQuery(sprintf($fmtsql, implode(",", $fields), implode(",", $values)));
		if (!$this->_db->execute()) {
			return false;
		}
		$id = $this->_db->insertid();
		if ($keyName && $id) {
			$object->$keyName = $id;
		}

		return $this->_db->getAffectedRows();
	}
	
	/**
	 * Gets the default asset values for a component.
	 *
	 * @param   $string  $component  The component asset name to search for
	 *
	 * @return  JAccessRules  The JAccessRules object for the asset
	 */
	protected function getDefaultAssetValuesObsolete($component)
	{
		// Need to find the asset id by the name of the component.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select($db->quoteName('id'))
		->from($db->quoteName('#__assets'))
		->where($db->quoteName('name') . ' = ' . $db->quote($component));
		$db->setQuery($query);
		$assetId = (int) $db->loadResult();
	
		return JAccess::getAssetRules($assetId);
	}
	
}
