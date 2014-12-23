<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die();


function add_apostroph($str) {
	return sprintf("`%s`", $str);
}

function add_quotes($str) {
	return sprintf("'%s'", $str);
}

/**
 * Model: Export
 */
class JemModelExport extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param array An optional associative array of configuration settings.
	 * @see JController
	 *
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'a.id'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the Events data.
	 *
	 * @return JDatabaseQuery
	 */
	protected function getListQuery()
	{

		// Retrieve variables
		$jinput = JFactory::getApplication()->input;
		$startDate = $jinput->get('dates', '', 'string');
		$endDate = $jinput->get('enddates', '', 'string');
		$cats = $jinput->get('cid', array(), 'post', 'array');

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');
		$query->from('`#__jem_events` AS a');
		$query->join('LEFT', '#__jem_cats_event_relations AS rel ON rel.itemid = a.id');
		$query->join('LEFT', '#__jem_categories AS c ON c.id = rel.catid');

		if (!empty($startDate) && !empty($endDate)) {
			$query->where('DATEDIFF(IF (a.enddates IS NOT NULL, a.enddates, a.dates), "' . $startDate . '") >= 0');
			$query->where('DATEDIFF(a.dates, "' . $endDate . '") <= 0');
		} else {
			if (!empty($startDate)) {
				$query->where('(a.dates IS NULL OR a.dates >= '.$db->Quote($startDate).')');
			}
			if (!empty($endDate)) {
				$query->where('(a.enddates IS NULL OR a.enddates <= '.$db->Quote($endDate).')');
			}
		}
		
		// check if specific category's have been selected
		if (! empty($cats)) {
			$query->where('  (c.id=' . implode(' OR c.id=', $cats) . ')');
		}

		// Group the query
		$query->group('a.id');
		
		return $query;
	}
	
	
	/**
	 * Returns a SQL file with Events data
	 * @return boolean
	 */
	public function getSQL()
	{	
		$jinput = JFactory::getApplication()->input;
		$includecategories = $jinput->getInt('categorycolumn', 0);
		
		# start output
		$csv	= fopen('php://output', 'w');
		$db		= $this->getDbo();
	
		############
		## EVENTS ##
		############
		
		$eventColumns = array();
		$eventColumns = array_keys($db->getTableColumns('#__jem_events'));
		
		$query = $this->getListQuery();
		$events = $this->_getList($query);
		
		$result = $events;
			
		$eventColumns =  implode(',', array_map('add_apostroph', $eventColumns));
			
		$return = '';
		$text = '';
		$text2 = '';
		$text .= "INSERT INTO `".$db->getPrefix()."jem_events` (".$eventColumns.") VALUES";
		$text .= "\r\n";
			
		fwrite($csv,$text);
			
		foreach ($events as $event) {
			$values = get_object_vars($event);
			$values = implode(',',array_map('add_quotes',$values));
				
			$return.= '('.$values.')';	
			$return.=",";
			$return.= "\r\n";
		}

	
		$return = substr_replace($return ,"",-3);
		
		fwrite($csv,$return);
			
		$text2.= ";\n";
		fwrite($csv,$text2);
		
		
		
		################
		## CATEGORIES ##
		################
		
		$categoryColumns = array();
		$categoryColumns = array_keys($db->getTableColumns('#__jem_categories'));
		$categoryColumns =  implode(',', array_map('add_apostroph', $categoryColumns));
			
		$returnCat = '';
		$bak = '';
		$text3 = '';
		$text4 = '';
		$text3 .= "\r\n\n";
		$text3 .= "INSERT INTO `".$db->getPrefix()."jem_categories` (".$categoryColumns.") VALUES";
		$text3 .= "\r\n";
		
		
		fwrite($csv,$text3);
			
		$catid_array = array();
		
		foreach ($events as $event) {
			# get the category id's
			$catids = $this->getCatEvent($event->id);
			
			# as the catid can have multiple values we're exploding it
			$catids = explode(',',$catids);
			
			# now we have the category id's and we can retreive the data that belongs to it
			foreach ($catids as $catid) {	
				$catid_array[] = $catid;
			}
			
			# get catEvent data
			$catEvents	= $this->getCatEventData($event->id);
			
			# we can have multiple results
			foreach ($catEvents as $catEvent) {
				$catEvent = implode(',',array_map('add_quotes',$catEvent));
			
				$bak.= '('.$catEvent.')';
				$bak.=",";
				$bak.= "\r\n";
			}
			
		}
		
		$catid_array = array_unique($catid_array);
		
		foreach($catid_array AS $catid_row) {
			$catValue = $this->getCategoryData($catid_row);
			$catValue = implode(',',array_map('add_quotes',$catValue));
		
			$returnCat.= '('.$catValue.')';
			$returnCat.=",";
			$returnCat.= "\r\n";
		}
		
		$returnCat = substr_replace($returnCat ,"",-3);
		fwrite($csv,$returnCat);
			
		$text4.= ";\n";
		fwrite($csv,$text4);
		
		
		##############
		## CATEVENT ##
		##############
		
		$catEventColumns = array();
		$catEventColumns = array_keys($db->getTableColumns('#__jem_cats_event_relations'));
		$catEventColumns =  implode(',', array_map('add_apostroph', $catEventColumns));

		$text6 = '';
		$text6 .= "\r\n\n";
		$text6 .= "INSERT INTO `".$db->getPrefix()."jem_cats_event_relations` (".$catEventColumns.") VALUES";
		$text6 .= "\r\n";
		
		fwrite($csv,$text6);
		
		$bak = substr_replace($bak ,"",-3);
		fwrite($csv,$bak);
			
		$text7	= ";\n";
		fwrite($csv,$text7);
					
		# return output
		return fclose($csv);
	}
	
	
	/**
	 * Returns a CSV file with Events data
	 * @return boolean
	 */
	public function getCsv()
	{
		$jinput = JFactory::getApplication()->input;
		$includecategories = $jinput->get('categorycolumn', 0, 'int');

		$csv = fopen('php://output', 'w');
		$db = $this->getDbo();

		fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

		if ($includecategories == 1) {
			$header = array();
			$events = array_keys($db->getTableColumns('#__jem_events'));
			$categories = array();
			$categories[] = "categories";
			$header = array_merge($events, $categories);

			fputcsv($csv, $header, ';');

			$query = $this->getListQuery();
			$items = $this->_getList($query);

			foreach ($items as $item) {
				$item->categories = $this->getCatEvent($item->id);
			}
		} else {
			$header = array_keys($db->getTableColumns('#__jem_events'));
			fputcsv($csv, $header, ';');
			$query = $this->getListQuery();
			$items = $this->_getList($query);
		}

		foreach ($items as $lines) {
			fputcsv($csv, (array) $lines, ';', '"');
		}

		return fclose($csv);
	}


	/**
	 * logic to get the categories
	 *
	 * @return void
	 */
	public function getCategories()
	{
		$user = JFactory::getUser();
		$jemsettings = JEMHelper::config();
		$userid = (int) $user->get('id');
		$superuser = JEMUser::superuser();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('c.*'));
		$query->from($db->quoteName('#__jem_categories').' AS c');
		$query->where(array('c.published = 1 '));
		$query->order(array('c.parent_id','c.ordering'));
		$db->setQuery($query);

		$mitems = $db->loadObjectList();

		# Check for a database error.
		if ($db->getErrorNum()){
			JError::raiseNotice(500, $db->getErrorMsg());
		}

		if (!$mitems){
			$mitems = array();
			$children = array();

			$parentid = $mitems;
		}else{

		$mitems_temp = $mitems;

		$children = array();
		# First pass - collect children
		foreach ($mitems as $v){
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}

		$parentid = intval($mitems[0]->parent_id);
		}

		# get list of the items
		$list = JemCategories::treerecurse($parentid, '', array(), $children, 9999, 0, 0);

		return $list;
	}

	/**
	 * Get Cat ID for a specific event
	 */
	function getCatEvent($id)
	{
		# Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		# Select the required fields from the table.
		$query->select('catid');
		$query->from('#__jem_cats_event_relations');
		$query->where('itemid = ' . $id);

		$db->setQuery($query);
		$catidlist = $db->loadObjectList();

		if (count($catidlist)) {
			$catidarray;
			foreach ($catidlist as $obj) {
				$catidarray[] = $obj->catid;
			}

			$catids = implode(',', $catidarray);
		} else {
			$catids = false;
		}

		return $catids;
	}
	
	
	/**
	 * Retrieve categoryData
	 */
	function getCategoryData($catid)
	{
		// Query
		$db 	= JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select(array('*'));
		$query->from('#__jem_categories');
		$query->where('id ='.$catid);
		$db->setQuery($query);
		$result = $db->loadRow();
		
		return $result;
	}
	
	
	/**
	 * Retrieve categoryData
	 */
	function getCatEventData($eventid)
	{
		$db 	= JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__jem_cats_event_relations');
		$query->where('itemid =' . $db->quote($eventid));
		$db->setQuery($query);
		$result = $db->loadRowList();
		
		return $result;
	}
	
	/**
	 * Returns a CSV file with Table data
	 * @return boolean
	 */
	public function getTableData($table)
	{
		$csv = fopen('php://output', 'w');
		fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		$db 	= JFactory::getDBO();
		$header = array();
		$header = array_keys($db->getTableColumns('#__jem_'.$table));
		fputcsv($csv, $header, ';');
	
		$items = $db->setQuery($this->getListQueryTableData($table))->loadObjectList();
	
		foreach ($items as $lines) {
			fputcsv($csv, (array) $lines, ';', '"');
		}
	
		return fclose($csv);
	}
	
	/**
	 * Build an SQL query to load the Table data.
	 *
	 * @return JDatabaseQuery
	 */
	protected function getListQueryTableData($table)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
	
		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__jem_'.$table);
	
		return $query;
	}
	
	
	/**
	 * Returns a SQL file with data
	 * @return boolean
	 */
	public function getTableDataSQL($table)
	{
		# start output
		$sql	= fopen('php://output', 'w');
		$db		= $this->getDbo();
		
		
		if (is_array($table)) {
			$tables	= $table;
			foreach ($tables as $table) {
				
				$query = $this->getListQueryTableDataSQL($table);
				$rows = $this->_getList($query);
		
				$result	= count($rows);
				if ($result == 0) {
					continue;
				}
				
				# retrieve columns
				$columns = array();
				$columns = array_keys($db->getTableColumns('#__jem_'.$table));	
				$columns =  implode(',', array_map('add_apostroph', $columns));
						
				$data = '';
				$start = "INSERT INTO `".$db->getPrefix()."jem_".$table."` (".$columns.") VALUES";
				$start .= "\r\n";
			
				fwrite($sql,$start);
		
		
				foreach ($rows as $row) {
					$values = get_object_vars($row);
					$values = implode(',',array_map('add_quotes',$values));
	
					$data.= '('.$values.')';
					$data.=",";
					$data.= "\r\n";
				}
	
				$data = substr_replace($data ,"",-3);
	
				fwrite($sql,$data);
					
				$end = ";\n\n\n";
				fwrite($sql,$end);
			}
		
		} else {
			# retrieve columns
			$columns = array();
			$columns = array_keys($db->getTableColumns('#__jem_'.$table));
			$columns =  implode(',', array_map('add_apostroph', $columns));
			
			$data = '';
			$start = "INSERT INTO `".$db->getPrefix()."jem_".$table."` (".$columns.") VALUES";
			$start .= "\r\n";
				
			fwrite($sql,$start);
			
			$query = $this->getListQueryTableDataSQL($table);
			$rows = $this->_getList($query);
			
			foreach ($rows as $row) {
				$values = get_object_vars($row);
				$values = implode(',',array_map('add_quotes',$values));
			
				$data.= '('.$values.')';
				$data.=",";
				$data.= "\r\n";
			}
			
			$data = substr_replace($data ,"",-3);
			
			fwrite($sql,$data);
				
			$end = ";\n";
			fwrite($sql,$end);
		}

		# return output
		return fclose($sql);
	}
	
	
	/**
	 * Build an SQL query to load the Table data.
	 *
	 * @return JDatabaseQuery
	 */
	protected function getListQueryTableDataSQL($table)
	{
		# Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
	
		# retrieve data
		$query->select('*');
		$query->from('#__jem_'.$table);
		
		return $query;
	}
	
}