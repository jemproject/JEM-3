<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Event-Raw
 */
class JemViewEvent extends JViewLegacy
{
	/**
	 * Creates the output for the event view
	 */
	function display($tpl = null)
	{
		$settings = JemHelper::globalattribs();

		// check iCal global setting
		if ($settings->get('global_show_ical_icon','0')==1) {
			// Get data from the model
			$row 				= $this->get('Item');
			$row->categories 	= $this->get('Categories');
			$row->id 			= $row->did;
			$row->slug			= $row->alias ? ($row->id.':'.$row->alias) : $row->id;
			$params				= $row->params;

			// check individual iCal Event setting
			if ($params->get('event_show_ical_icon',1)) {
				
				$filename_type	= $params->get('event_ical_filename_type','2');
				switch ($filename_type) {
					case 1: 
						$filename	= JText::_('COM_JEM_EVENT_ICAL_FILENAME');
						break;
					case 2:
						$filename	= "event".$row->did;
						break;
					case 3:
						$filename	= $params->get('event_ical_filename','event');
						break;
				}
				
				
				// initiate new CALENDAR
				$vcal = JemHelper::getCalendarTool();
				$vcal->setConfig("filename", $filename.'.ics');
				JemHelper::icalAddEvent($vcal, $row,'event');
				// generate and redirect output to user browser
				$vcal->returnCalendar();
			} else {
				return;
			}
		} else {
			return;
		}
	}
}
?>