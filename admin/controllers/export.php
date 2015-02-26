<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Based on: https://gist.github.com/dongilbert/4195504
 */
defined('_JEXEC') or die;


/**
 * Controller: Export
 */
class JemControllerExport extends JControllerAdmin {


	public function __construct()
	{
		parent::__construct();

		$jinput 	= JFactory::getApplication()->input;
		$task 		= $jinput->getCmd('task');

		if (strpos($task,'table_') !== false) {

			if (strpos($task,'table_sql') !== false) {

				$this->registerTask($task, 'export_table_sql');
			} else {
				$this->registerTask($task, 'export_table');
			}
		}
	}


   /**
	* Proxy for getModel.
	*/
	public function getModel($name = 'Export', $prefix = 'JEMModel', $config=array()) {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function export() {
		$this->sendHeaders("events_".date('Ymd') .'_' . date('Hi').".csv", "text/csv");
		$this->getModel()->getCsv();
		jexit();
	}

	public function exportsql() {
		$this->sendHeaders("events_".date('Ymd') .'_' . date('Hi').".sql", "text/plain");
		$this->getModel()->getSQL();
		jexit();
	}

	public function export_table() {
		$task = $this->getTask();
		$table = str_replace('table_' ,"",$task);
		$this->sendHeaders($table.'_'.date('Ymd') .'_' . date('Hi').".csv", "text/csv");
		$this->getModel()->getTableData($table);
		jexit();
	}

	public function export_table_sql() {
		$task = $this->getTask();
		$table = str_replace('table_sql_' ,"",$task);

		$this->sendHeaders($table.'_'.date('Ymd') .'_' . date('Hi').".sql", "text/plain");
		$this->getModel()->getTableDataSQL($table);
		jexit();
	}

	public function tabledump() {

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
				"venues");

		# add headers
		$this->sendHeaders('tabledump_'.date('Ymd') .'_' . date('Hi').".sql", "text/plain");

		$this->getModel()->getTableDataSQL($tables,true);

		# end
		jexit();
	}

	private function sendHeaders($filename = 'export.csv', $contentType = 'text/csv') {
		header("Content-type: ".$contentType);
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		//echo "\xEF\xBB\xBF";
	}
}
