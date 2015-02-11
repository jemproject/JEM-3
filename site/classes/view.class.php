<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Class-JemView
 */
class JEMView extends JViewLegacy {
	/**
	 * Adds a row to data indicating even/odd row number
	 *
	 * @return object $rows
	 */
	public function getRows($rowname = "rows")
	{
		if (!isset($this->$rowname) || !count($this->$rowname)) {
			return;
		}

		$k = 0;
		foreach($this->$rowname as $row) {
			$row->odd = $k;
			$k = 1 - $k;
		}

		return $this->$rowname;
	}
}
