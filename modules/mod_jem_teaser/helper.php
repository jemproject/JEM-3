<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Teaser
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
 * Module-Teaser
 */
abstract class modJEMteaserHelper
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

		// all upcoming events
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

		// currently running events only
		elseif ($type == 3) {
			$offset_days = (int)round($offset_hourss / 24);

			$model->setState('filter.published',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));

			$cal_from = " ((DATEDIFF(a.dates, CURDATE()) <= $offset_days) AND (DATEDIFF(IFNULL(a.enddates,a.dates), CURDATE()) >= $offset_days))";
		}

		// featured
		elseif ($type == 4) {
			$offset_minutes = $offset_hourss * 60;

			$model->setState('filter.featured',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));

			$cal_from  = "((TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) > $offset_minutes) ";
			$cal_from .= " OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(IFNULL(a.enddates,a.dates),' ',IFNULL(a.endtimes,'23:59:59'))) > $offset_minutes)) ";
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

		if ($params->get('use_modal', 0)) {
			JHtml::_('behavior.modal', 'a.flyermodal');
		}

		$model->setState('list.limit',$count);


		// Retrieve the available Events
		$events = $model->getItems();

		if (!$events) {
			return array();
		}

		// Loop through the result rows and prepare data
		$i		= 0;
		$lists	= array();
		$FixItemID = $params->get('FixItemID', '');
		$eventimg = $params->get('eventimg',1);
		$venueimg = $params->get('venueimg',1);

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

			/**
			 * DEFINE LIST
			 **/

			$settings	= JEMHelper::globalattribs();
			$access		= !$settings->get('show_noauth','0');
			$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

			$lists[$i] = new stdClass();
			$lists[$i]->title			= htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8');
			$lists[$i]->venue			= htmlspecialchars($row->venue, ENT_COMPAT, 'UTF-8');
			$lists[$i]->state			= htmlspecialchars($row->state, ENT_COMPAT, 'UTF-8');
			$lists[$i]->city			= htmlspecialchars( $row->city, ENT_COMPAT, 'UTF-8' );

			// time/date
			$lists[$i]->date			= modJEMteaserHelper::_format_date($row, $params);
			$lists[$i]->day 			= modJEMteaserHelper::_format_day($row, $params);
			$lists[$i]->dayname			= modJEMteaserHelper::_format_dayname($row);
			$lists[$i]->daynum 			= modJEMteaserHelper::_format_daynum($row);
			$lists[$i]->month 			= modJEMteaserHelper::_format_month($row);
			$lists[$i]->year 			= modJEMteaserHelper::_format_year($row);
			$lists[$i]->time 			= $row->times ? modJEMteaserHelper::_format_time($row->dates, $row->times, $params) : '' ;

			if ($access || in_array($row->access, $authorised))
			{
				// We know that user has the privilege to view the event
				if ($FixItemID)
				{
					$lists[$i]->link = JRoute::_('index.php?option=com_jem&view=event&id=' . $row->slug . '&Itemid=' . $FixItemID);
				}
				else
				{
					$lists[$i]->link = JRoute::_(JEMHelperRoute::getEventRoute($row->slug));
				}
				$lists[$i]->linkText = JText::_('MOD_JEM_TEASER_READMORE');
			}
			else
			{
				$lists[$i]->link = JRoute::_('index.php?option=com_users&view=login');
				$lists[$i]->linkText = JText::_('MOD_JEM_TEASER_READMORE_REGISTER');
			}

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

			$length = $params->get('descriptionlength');
			$length2 = 1;
			$etc = '...';
			$etc2 = JText::_('MOD_JEM_TEASER_NO_DESCRIPTION');

			$allowedTags = $params->get('allowed_tags','<a><em><strong>');
			
			if ($allowedTags == 'none') {
				// strip all tags
				$description = strip_tags($row->introtext);
			} else {
				// apply allowed tags
				$description = strip_tags($row->introtext,$allowedTags);
			}
			
			
			//switch <br /> tags to space character
			if ($params->get('br') == 0) {
			 $description = str_replace('<br />',' ',$description);
			}
			//
			if (strlen($description) > $length) {
				$length -= strlen($etc);
				$description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, $length+1));
				$lists[$i]->eventdescription = substr($description, 0, $length).$etc;
			} else

			if (strlen($description) < $length2) {
			$length -= strlen($etc2);
			$description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, $length+1));
			$lists[$i]->eventdescription = substr($description, 0, $length).$etc2;

			} else {
				$lists[$i]->eventdescription	= $description;
			}

			$lists[$i]->readmore = strlen(trim($row->fulltext));

			$i++;
		}


		return $lists;
	}

	/**
	 *format days
	 */
	protected static function _format_day($row, &$params)
	{
		//Get needed timestamps and format
		$yesterday_stamp	= mktime(0, 0, 0, date("m") , date("d")-1, date("Y"));
		$yesterday 			= strftime("%Y-%m-%d", $yesterday_stamp);
		$today_stamp		= mktime(0, 0, 0, date("m") , date("d"), date("Y"));
		$today 				= date('Y-m-d');
		$tomorrow_stamp 	= mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
		$tomorrow 			= strftime("%Y-%m-%d", $tomorrow_stamp);

		$dates_stamp		= strtotime($row->dates);
		$enddates_stamp		= $row->enddates ? strtotime($row->enddates) : null;

		//check if today or tomorrow or yesterday and no current running multiday event
		if($row->dates == $today && empty($enddates_stamp)) {
			$result = JText::_('MOD_JEM_TEASER_TODAY');
		} elseif($row->dates == $tomorrow) {
			$result = JText::_('MOD_JEM_TEASER_TOMORROW');
		} elseif($row->dates == $yesterday) {
			$result = JText::_('MOD_JEM_TEASER_YESTERDAY');
		} else {
			//if daymethod show day
			if($params->get('daymethod', 1) == 1) {

				//single day event
				$date = strftime('%A', strtotime( $row->dates ));
				$result = JText::sprintf('MOD_JEM_TEASER_ON_DATE', $date);

				//Upcoming multidayevent (From 16.10.2010 Until 18.10.2010)
				if($dates_stamp > $tomorrow_stamp && $enddates_stamp) {
				$startdate = strftime('%A', strtotime( $row->dates ));
				$result = JText::sprintf('FROM', $startdate);
				}

				//current multidayevent (Until 18.08.2008)
				if( $row->enddates && $enddates_stamp > $today_stamp && $dates_stamp <= $today_stamp ) {
				//format date
				$result = strftime('%A', strtotime( $row->enddates ));
				$result = JText::sprintf('MOD_JEM_TEASER_UNTIL', $result);
				}
			} else { // show day difference
				//the event has an enddate and it's earlier than yesterday
				if ($row->enddates && $enddates_stamp < $yesterday_stamp) {
					$days = round( ($today_stamp - $enddates_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_TEASER_ENDED_DAYS_AGO', $days);

				//the event has an enddate and it's later than today but the startdate is today or earlier than today
				//means a currently running event with startdate = today
				} elseif($row->enddates && $enddates_stamp > $today_stamp && $dates_stamp <= $today_stamp) {
					$days = round( ($enddates_stamp - $today_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_TEASER_DAYS_LEFT', $days);

				//the events date is earlier than yesterday
				} elseif($dates_stamp < $yesterday_stamp) {
					$days = round( ($today_stamp - $dates_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_TEASER_DAYS_AGO', $days );

				//the events date is later than tomorrow
				} elseif($dates_stamp > $tomorrow_stamp) {
					$days = round( ($dates_stamp - $today_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_TEASER_DAYS_AHEAD', $days);
				}
			}
		}
		return $result;
	}

	/**
	 * Method to format date information
	 *
	 * @access public
	 * @return string
	 */
	protected static function _format_date($row, &$params)
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
		if($params->get('datemethod', 1) == 2) {
			//check if today or tomorrow
			if($row->dates == $today) {
				$result = JText::_('MOD_JEM_TEASER_TODAY');
			} elseif($row->dates == $tomorrow) {
				$result = JText::_('MOD_JEM_TEASER_TOMORROW');
			} elseif($row->dates == $yesterday) {
				$result = JText::_('MOD_JEM_TEASER_YESTERDAY');

			//This one isn't very different from the DAYS AGO output but it seems
			//adequate to use a different language string here.
			//
			//the event has an enddate and it's earlier than yesterday
			} elseif($row->enddates && $enddates_stamp < $yesterday_stamp) {
				$days = round(($today_stamp - $enddates_stamp) / 86400);
				$result = JText::sprintf('MOD_JEM_TEASER_ENDED_DAYS_AGO', $days);

			//the event has an enddate and it's later than today but the startdate is earlier than today
			//means a currently running event
			} elseif($row->dates && $row->enddates && $enddates_stamp > $today_stamp && $dates_stamp < $today_stamp) {
				$days = round(($today_stamp - $dates_stamp) / 86400);
				$result = JText::sprintf('MOD_JEM_TEASER_STARTED_DAYS_AGO', $days);

			//the events date is earlier than yesterday
			} elseif($row->dates && $dates_stamp < $yesterday_stamp) {
				$days = round(($today_stamp - $dates_stamp) / 86400);
				$result = JText::sprintf('MOD_JEM_TEASER_DAYS_AGO', $days);

			//the events date is later than tomorrow
			} elseif($row->dates && $dates_stamp > $tomorrow_stamp) {
				$days = round(($dates_stamp - $today_stamp) / 86400);
				$result = JText::sprintf('MOD_JEM_TEASER_DAYS_AHEAD', $days);
			}
		} else {
			//single day event
			$date = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime($row->dates.' '.$row->times));
			$result = JText::sprintf('MOD_JEM_TEASER_ON_DATE', $date);

			//Upcoming multidayevent (From 16.10.2008 Until 18.08.2008)
			if($dates_stamp > $tomorrow_stamp && $enddates_stamp) {
				$startdate = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime($row->dates.' '.$row->times));
				$enddate = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime($row->enddates.' '.$row->endtimes));
				$result = JText::sprintf('MOD_JEM_TEASER_FROM_UNTIL', $startdate, $enddate);
			}

			//current multidayevent (Until 18.08.2008)
			if($row->enddates && $enddates_stamp > $today_stamp && $dates_stamp < $today_stamp) {
				//format date
				$result = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime($row->enddates.' '.$row->endtimes));
				$result = JText::sprintf('MOD_JEM_TEASER_UNTIL', $result);
			}
		}

		return $result;
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

	protected static function _format_dayname($row)
	{
		$jdate	 = new JDate($row->dates);
		$dayname = $jdate->format('l',false,true);
		return $dayname;
	}
	protected static function _format_daynum($row)
	{
		$jdate	= new JDate($row->dates);
		$day	= $jdate->format('d',false,true);
		return $day;
	}
	protected static function _format_year($row)
	{
		$jdate	= new JDate($row->dates);
		$year	= $jdate->format('Y',false,true);
		return $year;
	}
	protected static function _format_month($row)
	{
		$jdate	= new JDate($row->dates);
		$month	= $jdate->format('F',false,true);
		return $month;
	}
}
