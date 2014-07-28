<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
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
	
		// Register Extra task
		$this->registerTask('table_attachments', 'export_table');
		$this->registerTask('table_categories', 'export_table');
		$this->registerTask('table_cats_event_relations', 'export_table');
		$this->registerTask('table_events', 'export_table');
		$this->registerTask('table_groups', 'export_table');
		$this->registerTask('table_recurrence_master', 'export_table');
		$this->registerTask('table_recurrence', 'export_table');
		$this->registerTask('table_register', 'export_table');
		$this->registerTask('table_settings', 'export_table');
		$this->registerTask('table_venues', 'export_table');
		
		$this->registerTask('table_sql_attachments', 'export_table_sql');
		$this->registerTask('table_sql_categories', 'export_table_sql');
		$this->registerTask('table_sql_cats_event_relations', 'export_table_sql');
		$this->registerTask('table_sql_events', 'export_table_sql');
		$this->registerTask('table_sql_groups', 'export_table_sql');
		$this->registerTask('table_sql_recurrence_master', 'export_table_sql');
		$this->registerTask('table_sql_recurrence', 'export_table_sql');
		$this->registerTask('table_sql_register', 'export_table_sql');
		$this->registerTask('table_sql_settings', 'export_table_sql');
		$this->registerTask('table_sql_venues', 'export_table_sql');
	}
	
	
   /**
	* Proxy for getModel.
	*/
	public function getModel($name = 'Export', $prefix = 'JEMModel', $config=array()) {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function export() {
		$this->sendHeaders("events.csv", "text/csv");
		$this->getModel()->getCsv();
		jexit();
	}
	
	public function exportsql() {
		$this->sendHeaders("events.sql", "application/octet-stream");
		$this->getModel()->getSQL();
		jexit();
	}

	public function exportcatevents() {
		$this->sendHeaders("catevents.csv", "text/csv");
		$this->getModel()->getCsvcatsevents();
		jexit();
	}
	
	public function export_table() {
		$task = $this->getTask();
		$table = str_replace('table_' ,"",$task);
		$this->sendHeaders($table.".csv", "text/csv");
		$this->getModel()->getTableData($table);
		jexit();
	}
	
	public function export_table_sql() {
		$task = $this->getTask();
		$table = str_replace('table_sql_' ,"",$task);
		$this->sendHeaders($table.".sql", "application/octet-stream");
		$this->getModel()->getTableDataSQL($table);
		jexit();
	}

	private function sendHeaders($filename = 'export.csv', $contentType = 'text/plain') {
		// TODO: Use UTF-8
		// We have to fix the model->getCsv* methods too!
		// header("Content-type: text/csv; charset=UTF-8");
		header("Content-type: text/csv;");
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Pragma: no-cache");
		header("Expires: 0");
	}
}