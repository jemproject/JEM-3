<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Model: Housekeeping
 */
class JemModelHousekeeping extends JModelLegacy
{
	const EVENTS = 1;
	const VENUES = 2;
	const CATEGORIES = 3;

	/**
	 * images to delete
	 * @var array
	 */
	private $_images = null;

	/**
	 * assigned images
	 * @var array
	 */
	private $_assigned = null;

	/**
	 * unassigned images
	 * @var array
	 */
	private $_unassigned = null;

	/**
	 * Map logical name to folder and db names
	 * @var stdClass
	 */
	private $map = null;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$map = array();
		$map[JemModelHousekeeping::EVENTS] = array("folder" => "events", "table" => "events", "field" => "datimage");
		$map[JemModelHousekeeping::VENUES] = array("folder" => "venues", "table" => "venues", "field" => "locimage");
		$map[JemModelHousekeeping::CATEGORIES] = array("folder" => "categories", "table" => "categories", "field" => "image");
		$this->map = $map;
	}

	/**
	 * Method to delete the images
	 *
	 * @access	public
	 * @return int
	 */
	public function delete($type) {
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Get some data from the request
		$images	= $this->getImages($type);
		
		$folder = $this->map[$type]['folder'];

		$count = count($images);
		$fail = 0;

		if ($count) {
			foreach ($images as $image)
			{
				if ($image !== JFilterInput::getInstance()->clean($image, 'path')) {
					JError::raiseWarning(100, JText::_('COM_JEM_HOUSEKEEPING_UNABLE_TO_DELETE').' '.htmlspecialchars($image, ENT_COMPAT, 'UTF-8'));
					$fail++;
					continue;
				}

				$fullPath = JPath::clean(JPATH_SITE.'/images/jem/'.$folder.'/'.$image);
				$fullPaththumb = JPath::clean(JPATH_SITE.'/images/jem/'.$folder.'/small/'.$image);

				if (is_file($fullPath)) {
					JFile::delete($fullPath);
					if (JFile::exists($fullPaththumb)) {
						JFile::delete($fullPaththumb);
					}
				}
			}
		}

		$deleted = $count - $fail;

		return $deleted;
	}

	/**
	 * Deletes zombie cats_event_relations with no existing event or category
	 * @return boolean
	 */
	function cleanupCatsEventRelations()
	{
		$db = JFactory::getDbo();

		$db->setQuery('DELETE cat FROM #__jem_cats_event_relations as cat'
				.' LEFT OUTER JOIN #__jem_events as e ON cat.itemid = e.id'
				.' WHERE e.id IS NULL');
		$db->execute();

		$db->setQuery('DELETE cat FROM #__jem_cats_event_relations as cat'
				.' LEFT OUTER JOIN #__jem_categories as c ON cat.catid = c.id'
				.' WHERE c.id IS NULL');
		$db->execute();

		return true;
	}

	/**
	 * Truncates JEM tables with exception of settings table
	 */
	public function truncateAllData() {
		$tables = array(
			"attachments",
			"categories",
			"cats_event_relations",
			"dates",
			"events",
			"groupmembers",
			"groups",
			"recurrence",
			"recurrence_master",
			"register",
			"venues"
		);

		$db = JFactory::getDbo();

		foreach ($tables as $table) {
			$db->truncateTable("#__jem_".$table);

			if(!$db->execute()) {
				return false;
			}
		}

		$categoryTable = $this->getTable('Categories', 'JEMTable');
		$categoryTable->addRoot();

		return true;
	}

	/**
	 * Method to determine the images to delete
	 *
	 * @access	private
	 * @return array
	 */
	private function getImages($type) {
		$this->_images = array_diff($this->getAvailable($type), $this->getAssigned($type));

		return $this->_images;
	}

	/**
	 * Method to determine the assigned images
	 *
	 * @access	private
	 * @return array
	 */
	private function getAssigned($type) {
		$query = 'SELECT '.$this->map[$type]['field'].' FROM #__jem_'.$this->map[$type]['table'];

		$this->_db->setQuery($query);
		$this->_assigned = $this->_db->loadColumn();

		return $this->_assigned;
	}

	/**
	 * Method to determine the unassigned images
	 *
	 * @access	private
	 * @return array
	 */
	private function getAvailable($type) {
		// Initialize variables
		$basePath = JPATH_SITE.'/images/jem/'.$this->map[$type]['folder'];

		$images = array ();

		// Get the list of files and folders from the given folder
		$fileList = JFolder::files($basePath);

		// Iterate over the files if they exist
		if ($fileList !== false) {
			foreach ($fileList as $file)
			{
				if (is_file($basePath.'/'.$file) && substr($file, 0, 1) != '.') {
					$images[] = $file;
				}
			}
		}

		$this->_unassigned = $images;

		return $this->_unassigned;
	}

	/**
	 * Cleanup images
	 */
	function CleanupImages() {

		$app = JFactory::getApplication();
		
		# retrieve images from tables
		$event		= $this->retrieveTableImages('events','datimage');
		$category	= $this->retrieveTableImages('categories','image');
		$venue		= $this->retrieveTableImages('venues','locimage');

		# merge the arrays
		$tableImages = array_merge($event,$category,$venue);

		# make it unqiue
		$tableImages = array_unique($tableImages);

		# retrieve images from JEM folder
		$folderImages = $this->retrieveFolderImages();

		# compare table and folder
		$same	= array_intersect($tableImages,$folderImages);
		$diff	= array_diff($folderImages,$tableImages);
		
		$settings 		= JemHelper::globalattribs();
		$trash_path		= $settings->get('trashedimages_path','/trashedimages');
			
		// "trashed" folder
		$trash_folder		= JPATH_SITE . $trash_path;
		if(!JFolder::exists($trash_folder)){
			if (!JFolder::create($trash_folder)) {
				$app->enqueueMessage(JText::_('COM_JEM_HOUSEKEEPING_ERROR_TRASHEDFOLDER'), 'error');
				return false;
			}
		} 
		$trash_foldersmall	= JPATH_SITE . $trash_path.'/small';
		if(!JFolder::exists($trash_foldersmall)){
			if (!JFolder::create($trash_foldersmall)) {
				$app->enqueueMessage(JText::_('COM_JEM_HOUSEKEEPING_ERROR_TRASHEDFOLDER'), 'error');
				return false;
			}
		}
		
		
		// loop trough items
		$thumbs = array();
		$array_folders = array();
		foreach ($diff AS $item) {
			
			if (strpos($item,'images/jem/') !== false) {
				$array_folders[] = (dirname($item));
			}
			$filename = basename($item);
			
			//  check for thumb
			if (strpos($item,'small/') !== false) {
				$thumb = true;
				$folder = $trash_foldersmall;
			} else {
				$thumb = false;
				$folder = $trash_folder;
			}
			if (JFile::exists($folder.'/'.$filename)) {
				$filename = self::file_newname($folder, $filename);
				
				if (JFile::move($item, $folder.'/'.$filename)) {
					if ($thumb) {
						$thumbs[] = true;
					} else {
						$thumbs[] = false;
					}
				} 
				
			} else {
				if (JFile::move($item, $folder.'/'.$filename)) {
					if ($thumb) {
						$thumbs[] = true;
					} else {
						$thumbs[] = false;
					}
				} 
			}					
		}

		// loop trough the folders
		$pass2 = array();
		foreach ($array_folders AS $item) {
			$path = realpath($item);
			
			$files = JFolder::files($path, $filter = '.', false, true , array('index.html'));
			if (!$files) {
				$pass2[] = JFolder::delete($path);
					
			}
		}
		
		return $thumbs;
	}

	/**
	 * Remove images
	 */
	
	function rmObsImages() {
		$settings 		= JemHelper::globalattribs();
		$trash_path		= $settings->get('trashedimages_path','/trashedimages');
			
		
		$trash_folder		= JPATH_SITE . $trash_path;
		$trash_foldersmall	= JPATH_SITE . $trash_path.'/small';
		
		if(JFolder::exists($trash_folder)){
			$files = JFolder::files($trash_folder, $filter = '.', false, true , array('index.html'));
			if ($files) {
				JFile::delete($files);
			}
		}
		
		if(JFolder::exists($trash_foldersmall)){
			$files = JFolder::files($trash_foldersmall, $filter = '.', false, true , array('index.html'));
			if ($files) {
				JFile::delete($files);
			}
		}
		
		return;
	}
	
	
	
	
	/**
	 * @author http://css-tricks.com/snippets/
	 */
	
	function file_newname($path, $filename){
		if ($pos = strrpos($filename, '.')) {
			$name = substr($filename, 0, $pos);
			$ext = substr($filename, $pos);
		} else {
			$name = $filename;
		}
	
		$newpath = $path.'/'.$filename;
		$newname = $filename;
		$counter = 0;
		while (file_exists($newpath)) {
			$newname = $name .'_'. $counter . $ext;
			$newpath = $path.'/'.$newname;
			$counter++;
		}
	
		return $newname;
	}
	
	
	
	/**
	 *
	 *
	 */
	function is_dir_empty($dir) {
		if (!is_readable($dir)) return NULL;
		$handle = opendir($dir);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 *
	 */
	function retrieveTableImages($table,$field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($field);
		$query->from('#__jem_'.$table);
		$db->setQuery($query);
		$db->execute();

		# output array
		$array = $db->loadColumn(0);

		# strip empty values
		$array = array_filter($array, 'strlen' );

		# append images to results without the images/ path
		$images = array();
		$thumbs = array();
		foreach($array AS $item) {
			if (strpos($item,'images/') !== false) {
				# check if we're in the JEM directory
				if (strpos($item,'images/jem') !== false) {
					$images[] = JPATH_SITE.'/'.$item;

					if (strpos($item,'small/') !== false) {

					} else {
						$thumbs[] = JPATH_SITE.'/'.$item;
					}
				}
			} else {
				$images[] = JPATH_SITE.'/images/jem/'.$table.'/'.$item;
				if (strpos($item,'small/') !== false) {

				} else {
					$thumbs[] = JPATH_SITE.'/images/jem/'.$table.'/small/'.$item;
				}
			}
		}

		# merge the arrays
		$result = array_merge($images,$thumbs);

		return $result;
	}

	/**
	 *
	 */
	function retrieveFolderImages()
	{
		$path = JPATH_SITE.'/images/jem/';
		$recurse = true;
		$fullpath = true;
		$exclude = false;

		$array = JFolder::files($path, $filter = '.', $recurse, $fullpath,array('index.html'));

		foreach($array AS $item) {
			$result[] = str_replace('\images\jem\/','/images/jem/',$item);
		}

		return $result;
	}

	/**
	 *
	 */
	function retrieveFolders()
	{
		$path = JPATH_SITE.'/images/jem/';
		$recurse = true;
		$fullpath = true;
		$exclude = false;

		$array = JFolder::folders($path, $filter = '.', $recurse, $fullpath);

		foreach($array AS $item) {
			$result[] = str_replace('\images\jem\/','/images/jem/',$item);
		}

		return $result;
	}
}
