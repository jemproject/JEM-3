<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Wide
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jem/models', 'JemModel');
require_once JPATH_SITE . '/components/com_jem/helpers/helper.php';

// perform cleanup if it wasn't done today (archive, trash)
JEMHelper::cleanup();

/**
 * Module-Wide
 */
abstract class modJEMwideHelper
{

	/**
	 * Method to get the events
	 *
	 * @access public
	 * @return array
	 */
	public static function getList(&$params)
	{
		mb_internal_encoding('UTF-8');

		// Retrieve Eventslist model for the data
		$model = JModelLegacy::getInstance('Eventslist', 'JemModel', array('ignore_request' => true));

		// Set params for the model
		// has to go before the getItems function
		$model->setState('params', $params);
		$model->setState('filter.access',true);

		// filter published
		//  0: unpublished
		//  1: published
		//  2: archived
		// -2: trashed

		$type = $params->get('type');
		$offset_hourss = $params->get('offset_hours', 0);

		// all upcoming or unfinished events
		if (($type == 0) || ($type == 1)) {
			$offset_minutes = $offset_hourss * 60;

			$model->setState('filter.published',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));

			$cal_from = "((TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) > $offset_minutes) ";
			$cal_from .= ($type == 1) ? " OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(IFNULL(a.enddates,a.dates),' ',IFNULL(a.endtimes,'23:59:59'))) > $offset_minutes)) " : ") ";
		}

		// archived events only
		elseif ($type == 2) {
			$model->setState('filter.published',2);
			$model->setState('filter.orderby',array('a.dates DESC','a.times DESC'));
			$cal_from = "";
		}

		// currently running events only (today + offset is inbetween start and end date of event)
		elseif ($type == 3) {
			$offset_days = (int)round($offset_hourss / 24);

			$model->setState('filter.published',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));
			$cal_from = " ((DATEDIFF(a.dates, CURDATE()) <= $offset_days) AND (DATEDIFF(IFNULL(a.enddates,a.dates), CURDATE()) >= $offset_days))";
		}

		$model->setState('filter.calendar_from',$cal_from);
		$model->setState('filter.groupby','a.id');

		// clean parameter data
		$catids = $params->get('catid');
		$venids = $params->get('venid');
		$eventids = $params->get('eventid');

		// filter category's
		if ($catids) {
			$model->setState('filter.category_id',$catids);
			$model->setState('filter.category_id.include',true);
		}

		// filter venue's
		if ($venids) {
			$model->setState('filter.venue_id',$venids);
			$model->setState('filter.venue_id.include',true);
		}

		// filter event id's
		if ($eventids) {
			$model->setState('filter.event_id',$eventids);
			$model->setState('filter.event_id.include',true);
		}

		// count
		$count = $params->get('count', '2');
		$model->setState('list.limit',$count);

		if ($params->get('use_modal', 0)) {
			JHtml::_('behavior.modal', 'a.flyermodal');
		}

		// Retrieve the available Events
		$events = $model->getItems();

		if (!$events) {
			return array();
		}

		// define list-array
		// in here we collect the row information
		$lists	= array();
		$i = 0;

		$FixItemID = $params->get('FixItemID', '');
		$eventimg = $params->get('eventimg',1);
		$venueimg = $params->get('venueimg',1);

		/**
		 * DEFINE FOREACH
		 */

		foreach ($events as $row)
		{
			// create thumbnails if needed and receive imagedata
			if ($row->datimage) {
				$dimage = JEMImage::flyercreator($row->datimage, 'event');
			} else {
				$dimage = null;
			}
			if ($row->locimage) {
				$limage = JEMImage::flyercreator($row->locimage, 'venue');
			} else {
				$limage = null;
			}

			// cut titel
			$length = mb_strlen($row->title);
			$maxlength = $params->get('cuttitle', '18');

			if ($length > $maxlength && $maxlength > 0) {
				$row->title = mb_substr($row->title, 0, $maxlength);
				$row->title = $row->title.'...';
			}

			$lists[$i] = new stdClass();
			$lists[$i]->title			= htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8');
			$lists[$i]->venue			= htmlspecialchars($row->venue, ENT_COMPAT, 'UTF-8');
			$lists[$i]->state			= htmlspecialchars($row->state, ENT_COMPAT, 'UTF-8');

			list($lists[$i]->date,
					$lists[$i]->time)		= modJEMwideHelper::_format_date_time($row, $params);

			if ($FixItemID)
			{
				$lists[$i]->eventlink = $params->get('linkevent', 1) ? JRoute::_('index.php?option=com_jem&view=event&id=' . $row->slug . '&Itemid=' . $FixItemID) : '';
				$lists[$i]->venuelink = $params->get('linkvenue', 1) ? JRoute::_('index.php?option=com_jem&view=venue&id=' . $row->venueslug . '&Itemid=' . $FixItemID) : '';
			}
			else
			{
				$lists[$i]->eventlink = $params->get('linkevent', 1) ? JRoute::_(JEMHelperRoute::getEventRoute($row->slug)) : '';
				$lists[$i]->venuelink = $params->get('linkvenue', 1) ? JRoute::_(JEMHelperRoute::getVenueRoute($row->venueslug)) : '';
			}
			$lists[$i]->catname			= implode(", ", JemOutput::getCategoryList($row->categories, $params->get('linkcategory', 1),false,$FixItemID));


			// images
			if ($eventimg) {
				if ($dimage == null) {
					$lists[$i]->eventimage		= '';
					$lists[$i]->eventimageorig	= '';
				} else {
					$lists[$i]->eventimage		= JURI::base(true).'/'.$dimage['thumb'];
					$lists[$i]->eventimageorig	= JURI::base(true).'/'.$dimage['original'];
				}
			} else {
				$lists[$i]->eventimage		= '';
				$lists[$i]->eventimageorig	= '';
			}

			if ($venueimg) {
				if ($limage == null) {
					$lists[$i]->venueimage 		= '';
					$lists[$i]->venueimageorig 	= '';
				} else {
					$lists[$i]->venueimage		= JURI::base(true).'/'.$limage['thumb'];
					$lists[$i]->venueimageorig	= JURI::base(true).'/'.$limage['original'];
				}
			} else {
				$lists[$i]->venueimage 		= '';
				$lists[$i]->venueimageorig 	= '';
			}
			
			$allowedTags = $params->get('allowed_tags','<a><em><strong>');
			
			if ($allowedTags == 'none') {
				// strip all tags
				$lists[$i]->eventdescription= strip_tags($row->fulltext);
				$lists[$i]->venuedescription= strip_tags($row->locdescription);
			} else {
				// apply allowed tags
				$lists[$i]->eventdescription= strip_tags($row->fulltext,$allowedTags);
				$lists[$i]->venuedescription= strip_tags($row->locdescription,$allowedTags);
			}
			$i++;
		}

		return $lists;
	}


	/**
	 * Method to format date information
	 *
	 * @access public
	 * @return array(string, string) returns date and time strings as array
	 */
	protected static function _format_date_time($row, &$params)
	{
		//Get needed timestamps and format
		$yesterday_stamp	= mktime(0, 0, 0, date("m") , date("d")-1, date("Y"));
		$yesterday 			= strftime("%Y-%m-%d", $yesterday_stamp);
		$today_stamp		= mktime(0, 0, 0, date("m") , date("d"), date("Y"));
		$today 				= date('Y-m-d');
		$tomorrow_stamp 	= mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
		$tomorrow 			= strftime("%Y-%m-%d", $tomorrow_stamp);

		$dates_stamp		= $row->dates ? strtotime($row->dates) : null;
		$enddates_stamp		= $row->enddates ? strtotime($row->enddates) : null;

		//if datemethod show day difference
		if($params->get('datemethod', 1) == 2)
		{
			//check if today or tomorrow
			if($row->dates == $today)
			{
				$date = JText::_('MOD_JEM_WIDE_TODAY');
				$time = $row->times ? JEMOutput::formattime($row->times, null, false) : '';
			}
			elseif($row->dates == $tomorrow)
			{
				$date = JText::_('MOD_JEM_WIDE_TOMORROW');
				$time = $row->times ? JEMOutput::formattime($row->times, null, false) : '';
			}
			elseif($row->dates == $yesterday)
			{
				$date = JText::_('MOD_JEM_WIDE_YESTERDAY');
				$time = $row->times ? JEMOutput::formattime($row->times, null, false) : '';

			//This one isn't very different from the DAYS AGO output but it seems
			//adequate to use a different language string here.
			//
			//the event has an enddate and it's earlier than yesterday
			}
			elseif($row->enddates && $enddates_stamp < $yesterday_stamp)
			{
				$days = round(($today_stamp - $enddates_stamp) / 86400);
				$date = JText::sprintf('MOD_JEM_WIDE_ENDED_DAYS_AGO', $days);
				$time = $row->times ? JEMOutput::formattime($row->endtimes, null, false) : '';

			//the event has an enddate and it's later than today but the startdate is earlier than today
			//means a currently running event
			}
			elseif($row->dates && $row->enddates && $enddates_stamp > $today_stamp && $dates_stamp < $today_stamp)
			{
				$days = round(($today_stamp - $dates_stamp) / 86400);
				$date = JText::sprintf('MOD_JEM_WIDE_STARTED_DAYS_AGO', $days);
				$time = $row->times ? JEMOutput::formattime($row->times, null, false) : '';

			//the events date is earlier than yesterday
			}
			elseif($row->dates && $dates_stamp < $yesterday_stamp)
			{
				$days = round(($today_stamp - $dates_stamp) / 86400);
				$date = JText::sprintf('MOD_JEM_WIDE_DAYS_AGO', $days);
				$time = $row->times ? JEMOutput::formattime($row->times, null, false) : '';

			//the events date is later than tomorrow
			}
			elseif($row->dates && $dates_stamp > $tomorrow_stamp)
			{
				$days = round(($dates_stamp - $today_stamp) / 86400);
				$date = JText::sprintf('MOD_JEM_WIDE_DAYS_AHEAD', $days);
				$time = $row->times ? JEMOutput::formattime($row->times, null, false) : '';
			}
		}
		else
		{
			//Upcoming multidayevent (From 16.10.2008 Until 18.08.2008)

			if($dates_stamp > $today_stamp && $enddates_stamp > $dates_stamp) {
				$startdate = JEMOutput::formatdate($row->dates, $params->get('formatdate', '%d.%m.%Y') );
				$enddate = JEMOutput::formatdate($row->enddates, $params->get('formatdate', '%d.%m.%Y') );
				$date = JText::sprintf('MOD_JEM_WIDE_FROM_UNTIL', $startdate, $enddate);
				$time  = $row->times ? JEMOutput::formattime($row->times, null, false) : '';
				// endtime always starts with separator, also if there is no starttime
				$time .= $row->endtimes ? (' - ' . JEMOutput::formattime($row->endtimes, null, false)) : '';
			}

			//current multidayevent (Until 18.08.2008)

			elseif($row->enddates && $enddates_stamp > $today_stamp && $dates_stamp < $today_stamp)
			{
				//format date
				$date = JEMOutput::formatdate($row->enddates, $params->get('formatdate', '%d.%m.%Y') );
				$date = JText::sprintf('MOD_JEM_WIDE_UNTIL', $date);
				$time = $row->times ? JEMOutput::formattime($row->endtimes, null, false) : '';
			}

			//single day event

			else
			{
				$date = JEMOutput::formatdate($row->dates, $params->get('formatdate', '%d.%m.%Y') );
				$date = JText::sprintf('MOD_JEM_WIDE_ON_DATE', $date);
				$time = $row->times ? JEMOutput::formattime($row->times, null, false) : '';
			}
		}

		return array($date, $time);
	}

	/**
	 * Method to format time information
	 *
	 * @access public
	 * @return string
	 */
	protected static function _format_time($date, $time, &$params)
	{
		$time = strftime($params->get('formattime', '%H:%M'), strtotime($date.' '.$time));

		return $time;
	}
}
