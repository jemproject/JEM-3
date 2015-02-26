<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$db = JFactory::getDBO();
jimport('joomla.filesystem.folder');

/**
 * Script file of JEM component
*/
class com_jemInstallerScript
{
	private $oldRelease = "";
	private $newRelease = "";

	/**
	 * Method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		$error = array(
				'summary' => 0,
				'folders' => 0
		);

		$this->getHeader();
		?>

		<h2><?php echo JText::_('COM_JEM_INSTALL_STATUS'); ?>:</h2>
		<h3><?php echo JText::_('COM_JEM_INSTALL_CHECK_FOLDERS'); ?>:</h3> <?php

		$imageDir = "/images/jem";
		$createDirs = array(
				$imageDir,
				$imageDir.'/categories',
				$imageDir.'/categories/small',
				$imageDir.'/events',
				$imageDir.'/events/small',
				$imageDir.'/venues',
				$imageDir.'/venues/small'
		);

		// Check for existance of /images/jem directory
		if (JFolder::exists(JPATH_SITE.$createDirs[0])) {
			echo "<p><span style='color:green;'>".JText::_('COM_JEM_INSTALL_SUCCESS').":</span> ".
				JText::sprintf('COM_JEM_INSTALL_DIRECTORY_EXISTS_SKIP', $createDirs[0])."</p>";
		} else {
			echo "<p><span style='color:orange;'>".JText::_('COM_JEM_INSTALL_INFO').":</span> ".
				JText::sprintf('COM_JEM_INSTALL_DIRECTORY_NOT_EXISTS', $createDirs[0])."</p>";
			echo "<p>".JText::_('COM_JEM_INSTALL_DIRECTORY_TRY_CREATE').":</p>";

			echo "<ul>";
			// Folder creation
			foreach($createDirs as $directory) {
				if (JFolder::create(JPATH_SITE.$directory)) {
					echo "<li><span style='color:green;'>".JText::_('COM_JEM_INSTALL_SUCCESS').":</span> ".
						JText::sprintf('COM_JEM_INSTALL_DIRECTORY_CREATED', $directory)."</li>";
				} else {
					echo "<li><span style='color:red;'>".JText::_('COM_JEM_INSTALL_ERROR').":</span> ".
						JText::sprintf('COM_JEM_INSTALL_DIRECTORY_NOT_CREATED', $directory)."</li>";
					$error['folders']++;
				}
			}
			echo "</ul>";
		}

		if($error['folders']) {
			echo "<p>".JText::_('COM_JEM_INSTALL_DIRECTORY_CHECK_EXISTANCE')."</p>";
		}

		echo "<h3>".JText::_('COM_JEM_INSTALL_SETTINGS')."</h3>";

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')->from('#__jem_settings');
		$db->setQuery($query);
		$db->loadResult();

		if($db->loadResult()) {
			echo "<p><span style='color:green;'>".JText::_('COM_JEM_INSTALL_SUCCESS').":</span> ".
				JText::_('COM_JEM_INSTALL_FOUND_SETTINGS')."</p>";
		}

		echo "<h3>".JText::_('COM_JEM_INSTALL_SUMMARY')."</h3>";

		foreach ($error as $k => $v) {
			if($k != 'summary') {
				$error['summary'] += $v;
			}
		}

		if($error['summary']) {
		?>
			<p style='color: red;'>
				<b><?php echo JText::_('COM_JEM_INSTALL_INSTALLATION_NOT_SUCCESSFUL'); ?></b>
			</p>
		<?php
		} else {
		?>
			<p style='color: green;'>
				<b><?php echo JText::_('COM_JEM_INSTALL_INSTALLATION_SUCCESSFUL'); ?></b>
			</p> <?php
		}

		$param_array = array(
				"event_comunoption"=>"0",
				"event_comunsolution"=>"0",
				"event_show_author"=>"1",
				"event_lg"=>"",
				"event_link_author"=>"1",
				"event_show_contact"=>"1",
				"event_link_contact"=>"1",
				"event_show_description"=>"1",
				"event_show_detailsadress"=>"1",
				"event_show_detailstitle"=>"1",
				"event_show_detlinkvenue"=>"1",
				"event_show_hits"=>"0",
				"event_show_locdescription"=>"1",
				"event_show_mapserv"=>"0",
				"event_show_print_icon"=>"1",
				"event_show_email_icon"=>"1",
				"event_show_ical_icon"=>"1",
				"event_tld"=>"",
				"editevent_show_meta_option"=>"0",
				"editevent_show_attachment_tab"=>"0",
				"editevent_show_other_tab"=>"0",
				"global_display"=>"1",
				"global_regname"=>"1",
				"global_show_archive_icon"=>"1",
				"global_show_filter"=>"1",
				"global_show_email_icon"=>"1",
				"global_show_ical_icon"=>"1",
				"global_show_icons"=>"1",
				"global_show_locdescription"=>"1",
				"global_show_print_icon"=>"1",
				"global_show_timedetails"=>"1",
				"global_show_detailsadress"=>"1",
				"global_show_detlinkvenue"=>"1",
				"global_show_mapserv"=>"0",
				"global_tld"=>"",
				"global_lg"=>"",
				"global_cleanup_db_on_uninstall"=>"0"
		);

		$this->setGlobalAttribs($param_array);
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		$this->getHeader(); ?>
		<h2><?php echo JText::_('COM_JEM_UNINSTALL_STATUS'); ?>:</h2>
		<p><?php echo JText::_('COM_JEM_UNINSTALL_TEXT'); ?></p>
		<?php

		$globalParams = $this->getGlobalParams();
		$cleanup = $globalParams->get('global_cleanup_db_on_uninstall', 0);
		if (!empty($cleanup)) {
			// user decided to fully remove JEM - so do it!
			$this->removeJemMenuItems();
			$this->removeAllJemTables();
			$imageDir = JPATH_SITE.'/images/jem';
			if (JFolder::exists($imageDir)) {
				JFolder::delete($imageDir);
			}
		} else {
			// prevent dead links on frontend
			$this->disableJemMenuItems();
		}
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		$this->getHeader(); ?>
		<h2><?php echo JText::_('COM_JEM_UPDATE_STATUS'); ?>:</h2>
		<p><?php echo JText::sprintf('COM_JEM_UPDATE_TEXT', $parent->get('manifest')->version); ?></p>;

		<?php
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// Minimum required PHP version
		$minPhpVersion = "5.3.10";

		// Abort if PHP release is older than required version
		if(version_compare(PHP_VERSION, $minPhpVersion, '<')) {
			Jerror::raiseWarning(100, JText::sprintf('COM_JEM_PREFLIGHT_WRONG_PHP_VERSION', $minPhpVersion, PHP_VERSION));
			return false;
		}

		// Abort if Magic Quotes are enabled, it was removed from phpversion 5.4
		if (version_compare(phpversion(), '5.4', '<') ) {
			if (function_exists('get_magic_quotes_gpc')) {
				if(get_magic_quotes_gpc()) {
					Jerror::raiseWarning(100, JText::_('COM_JEM_PREFLIGHT_MAGIC_QUOTES_ENABLED'));
					return false;
				}
			}
		}

		// Minimum Joomla version as per Manifest file
		$minJoomlaVersion = $parent->get('manifest')->attributes()->version;

		// abort if the current Joomla release is older than required version
		$jversion = new JVersion();
		if(version_compare($jversion->getShortVersion(), $minJoomlaVersion, '<')) {
			Jerror::raiseWarning(100, JText::sprintf('COM_JEM_PREFLIGHT_OLD_JOOMLA_VERSION', $minJoomlaVersion));
			return false;
		}

		// abort if the release being installed is not newer than the currently installed version
		if ($type == 'update') {
			// Installed component version
			$this->oldRelease = $this->getParam('version');

			// Installing component version as per Manifest file
			$this->newRelease = $parent->get('manifest')->version;

			if (version_compare($this->newRelease, $this->oldRelease, 'lt')) {
				Jerror::raiseWarning(100, JText::sprintf('COM_JEM_PREFLIGHT_INCORRECT_VERSION_SEQUENCE', $this->oldRelease, $this->newRelease));
				return false;
			}

			// Remove obsolete files and folder
			$this->deleteObsoleteFiles();

			// Initialize schema table if necessary
			$this->initializeSchema($this->oldRelease);
		}

		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_JEM_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_JEM_POSTFLIGHT_' . $type . '_TEXT') . '</p>';

		if ($type == 'update') {

			// Changes between 3.0.2 -> 3.0.3
			if (version_compare($this->oldRelease, '3.0.3', 'lt') && version_compare($this->newRelease, '3.0.2', 'gt')) {
				$this->update303();
			}

		}

		if ($type == 'install') {
			$this->fixJemMenuItems();
		}
	}

	/**
	 * Get a parameter from the manifest file (actually, from the manifest cache).
	 *
	 * @param $name  The name of the parameter
	 *
	 * @return The parameter
	 */
	private function getParam($name) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('manifest_cache')->from('#__extensions')->where(array("type = 'component'", "element = 'com_jem'"));
		$db->setQuery($query);
		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}

	/**
	 * Sets parameter values in the component's row of the extension table
	 *
	 * @param $param_array  An array holding the params to store
	 */
	private function setParams($param_array) {
		if (count($param_array) > 0) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')->from('#__extensions')->where(array("type = 'component'", "element = 'com_jem'"));
			$db->setQuery($query);
			$params = json_decode($db->loadResult(), true);

			// add the new variable(s) to the existing one(s)
			foreach ($param_array as $name => $value) {
				$params[(string) $name] = (string) $value;
			}

			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode($params);
			$query = $db->getQuery(true);
			$query->update('#__extensions')
				->set('params = '.$db->quote($paramsString))
				->where(array("type = 'component'", "element = 'com_jem'"));
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Gets globalattrib values from the settings table
	 *
	 * @return JRegistry object
	 */
	private function getGlobalParams()
	{
		$registry = new JRegistry;
		try {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('globalattribs')->from('#__jem_settings')->where('id=1');
			$db->setQuery($query);
			$registry->loadString($db->loadResult());
		} catch (Exception $ex) {
		}
		return $registry;
	}

	/**
	 * Sets globalattrib values in the settings table
	 *
	 * @param $param_array  An array holding the params to store
	 */
	private function setGlobalAttribs($param_array) {
		if (count($param_array) > 0) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('globalattribs')->from('#__jem_settings');
			$db->setQuery($query);
			$params = json_decode($db->loadResult(), true);

			// add the new variable(s) to the existing one(s)
			foreach ($param_array as $name => $value) {
				$params[(string) $name] = (string) $value;
			}

			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode($params);
			$query = $db->getQuery(true);
			$query->update('#__jem_settings')
			->set('globalattribs = '.$db->quote($paramsString));
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Helper method that outputs a short JEM header with logo and text
	 */
	private function getHeader() {
		?>
		<img src="../media/com_jem/images/jemlogo.png" alt="" style="float:left; padding-right:20px;" />
		<h1><?php echo JText::_('COM_JEM'); ?></h1>
		<p class="small"><?php echo JText::_('COM_JEM_INSTALLATION_HEADER'); ?></p>
		<?php
	}

	/**
	 * Checks if component is already registered in Joomlas schema table and adds an entry if
	 * neccessary
	 * @param string $versionId The JEM version to add to the schema table
	 */
	private function initializeSchema($versionId) {
		$db = JFactory::getDbo();

		// Get extension ID of JEM
		$query = $db->getQuery(true);
		$query->select('extension_id')->from('#__extensions')->where(array("type='component'", "element='com_jem'"));
		$db->setQuery($query);
		$extensionId = $db->loadResult();

		if(!$extensionId) {
			// This is a fresh installation, return
			return;
		}

		// Check if an entry already exists in schemas table
		$query = $db->getQuery(true);
		$query->select('version_id')->from('#__schemas')->where('extension_id = '.$extensionId);
		$db->setQuery($query);

		if($db->loadResult()) {
			// Entry exists, return
			return;
		}

		// Insert extension ID and old release version number into schemas table
		$query = $db->getQuery(true);
		$query->insert('#__schemas')
			->columns($db->quoteName(array('extension_id', 'version_id')))
			->values(implode(',', array($extensionId, $db->quote($versionId))));

		$db->setQuery($query);
		$db->execute();
	}


	/**
	 * Remove all JEM menu items.
	 *
	 * @return void
	 */
	private function removeJemMenuItems()
	{
		// remove all "com_jem..." frontend entries
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__menu');
		$query->where(array('client_id = 0', 'link LIKE "index.php?option=com_jem%"'));
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Disable all JEM menu items.
	 * (usefull on uninstall to prevent dead links)
	 *
	 * @return void
	 */
	private function disableJemMenuItems()
	{
		// unpublish all "com_jem..." frontend entries
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__menu');
		$query->set('published = 0');
		$query->where(array('client_id = 0', 'published > 0', 'link LIKE "index.php?option=com_jem%"'));
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Fix all JEM menu items by setting new extension id.
	 * (usefull on install to let menu items from older installation refer new extension id)
	 *
	 * @return void
	 */
	private function fixJemMenuItems()
	{
		// Get (new) extension ID of JEM
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id')->from('#__extensions')->where(array("type='component'", "element='com_jem'"));
		$db->setQuery($query);
		$newId = $db->loadResult();

		if($newId) {
			// set compponent id on all "com_jem..." frontend entries
			$query = $db->getQuery(true);
			$query->update('#__menu');
			$query->set('component_id = ' . $db->quote($newId));
			$query->where(array('client_id = 0', 'link LIKE "index.php?option=com_jem%"'));
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Remove all obsolete files and folders of previous versions.
	 *
	 * Todo: Enhance the lists on each new version.
	 *
	 * @return void
	 */
	private function deleteObsoleteFiles()
	{
		$files = array(
			# 3.0.1 -> 3.0.2
			'/administrator/components/com_jem/tables/category.php',
			'/administrator/components/com_jem/tables/date.php',
			'/administrator/components/com_jem/tables/event.php',
			'/administrator/components/com_jem/tables/group.php',
			'/administrator/components/com_jem/tables/jem_attachments.php',
			'/administrator/components/com_jem/tables/jem_categories.php',
			'/administrator/components/com_jem/tables/jem_cats_event_relations.php',
			'/administrator/components/com_jem/tables/jem_events.php',
			'/administrator/components/com_jem/tables/jem_groupmembers.php',
			'/administrator/components/com_jem/tables/jem_groups.php',
			'/administrator/components/com_jem/tables/jem_register.php',
			'/administrator/components/com_jem/tables/jem_settings.php',
			'/administrator/components/com_jem/tables/jem_venues.php',
			'/administrator/components/com_jem/tables/recurrencemaster.php',
			'/administrator/components/com_jem/tables/venue.php',
			'/administrator/components/com_jem/views/attendee/tmpl/default.php',
			'/media/com_jem/js/highlighter.js',
			# 3.0.2 -> 3.0.3
			'/components/com_jem/models/categorycal.php',
			'/components/com_jem/models/venuecal.php',
			'/components/com_jem/models/forms/filter_eventslist.xml',
			'/components/com_jem/views/category/tmpl/calendar.php',
			'/components/com_jem/views/category/tmpl/calendar.xml',
			'/components/com_jem/views/venue/tmpl/calendar.php',
			'/components/com_jem/views/venue/tmpl/calendar.xml',
			'/components/com_jem/views/eventslist/tmpl/default_attachments.php',
			'/components/com_jem/layouts/searchtools/default.php',
			'/components/com_jem/layouts/searchtools/default/bar.php',
			'/components/com_jem/layouts/searchtools/default/filters.php',
			'/components/com_jem/layouts/searchtools/default/list.php',
			'/components/com_jem/layouts/searchtools/grid/sort.php',
			'/media/com_jem/js/settings.js',
			'/media/com_jem/js/unlimited.js',
			# 3.0.3 -> 3.0.4
			'/media/com_jem/js/dropdown.js',
			# 3.0.6 -> 3.0.7
			'/components/com_jem/models/fields/catoptions.php',
			'/components/com_jem/models/fields/modal/venuefront.php',
			'/components/com_jem/models/fields/modal/contactfront.php'
		);
		$folders = array();

		foreach ($files as $file) {
			if (JFile::exists(JPATH_ROOT . $file) && !JFile::delete(JPATH_ROOT . $file)) {
				echo JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file).'<br />';
			}
		}

		foreach ($folders as $folder) {
			if (JFolder::exists(JPATH_ROOT . $folder) && !JFolder::delete(JPATH_ROOT . $folder)) {
				echo JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $folder).'<br />';
			}
		}
	}

	/**
	 * Updating files: 302->303
	 */
	private function update303(){


		###############################
		## # update calendar entries ##
		###############################
		require_once JPATH_SITE . '/components/com_jem/classes/categories.class.php';

		$types = array('calendar','category','venue');

		foreach ($types as $type) :
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, link, params');
			$query->from('#__menu');

			if ($type == 'calendar') {
				$query->where(array("link LIKE 'index.php?option=com_jem&view=calendar'"));
			} else {
				$query->where(array("link LIKE 'index.php?option=com_jem&view=".$type."&layout=calendar%'"));
			}

			$query->order('id');
			$db->setQuery($query);
			$items = $db->loadObjectList();

			foreach ($items as $item) :

				# set params
				$params = json_decode($item->params, true);

				if ($type == 'category' || $type == 'venue') {
					# get id nr
					$id = strstr($item->link, '&id=');
					$id = str_replace('&id=', '', $id);

					if ($type == 'category') {
						$params['catids'] = $id;
						$params['catidsfilter'] = 1;
					}

					if ($type == 'venue') {
						$params['venueids'] = $id;
						$params['venueidsfilter'] = 1;
					}
				}

				if ($type == 'calendar') {
					# retrieve value 'top_category'
					if (isset($params['top_category'])) {
						$top_category	= $params['top_category'];
						$childs = JEMCategories::getChilds($top_category);

						# see if we're dealing with root as first array value
						# if so then we're taking that value and are hiding it so it will display all other categories

						$first = reset($childs);

						if ($first == 0 || $first == 1) {
							$params['catids'] = 1;
							$params['catidsfilter'] = 0;
						} else {
							if (count($childs) > 1) {
								# strip of first value as that one is the category it's retreving childs from
								$reorder = array_shift($childs);
								$params['catids'] = $childs;
								$params['catidsfilter'] = 1;
							}
						}
					}	else {
						$params['catids'] = 1;
						$params['catidsfilter'] = 0;
					}
				}

				# store params + new link value
				$paramsString = json_encode($params);

				$query = $db->getQuery(true);
				$query->update('#__menu')
				->set(array('params = '.$db->quote($paramsString),'link = '.$db->Quote('index.php?option=com_jem&view=calendar')))
				->where(array("id = ".$item->id));
				$db->setQuery($query);
				$db->execute();
			endforeach;
		endforeach;
	}

	/**
	 * Deletes all JEM tables on database if option says so.
	 *
	 * @return void
	 */
	private function removeAllJemTables()
	{
		$db = JFactory::getDbo();
		$tables = array('#__jem_attachments',
		                '#__jem_categories',
		                '#__jem_cats_event_relations',
		                '#__jem_countries',
						'#__jem_dates',
		                '#__jem_events',
		                '#__jem_groupmembers',
		                '#__jem_groups',
						'#__jem_recurrence',
						'#__jem_recurrence_master',
		                '#__jem_register',
		                '#__jem_settings',
		                '#__jem_venues');
		foreach ($tables AS $table) {
			try {
				$db->dropTable($table);
			} catch (Exception $ex) {
				// simply continue with next table
			}
		}
	}
}
