<?php
/**
* @package JEM
* @subpackage JEM Module-Banner
* @copyright (C) 2014-2015 joomlaeventmanager.net
* @copyright (C) 2005-2009 Christoph Lukes
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jem/models', 'JemModel');

/**
* Module-Banner
*/
abstract class ModJemBannerHelper
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

		$db     = JFactory::getDBO();
		$user   = JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();

		# Retrieve Eventslist model for the data
		$model = JModelLegacy::getInstance('Eventslist', 'JemModel', array('ignore_request' => true));

		# Set params for the model
		# has to go before the getItems function
		$model->setState('params', $params);

		# filter published
		#  0: unpublished
		#  1: published
		#  2: archived
		# -2: trashed

		# type:
		#  0: upcoming (not started) - dates,times > now+offset
		#  1: unfinished (not ended) - enddates,endtimes > now+offset
		#  2: archived               - no limit, but from now back to the past
		#  3: running (today)        - enddates,endtimes > today+offset AND dates,times < tomorrow+offset
		#  4: featured               - ? (same as upcoming yet)
		#  5: open date              - no limit
		$type = (int)$params->get('type');
		$offset_days    = (int)($params->get('offset_hours', 0) / 24); // hours given must be multiple of 24, truncate to full days
		$offset_minutes = (int)($params->get('offset_hours', 0) * 60);
		$max_days       = (int) $params->get('max_days', 0); // empty = unlimited
		$max_minutes    = $max_days ? ($max_days * 24 * 60 + $offset_minutes) : false;
		$max_title_length = (int)$params->get('cuttitle', '25');
		$published = 1;
		$orderdir = 'ASC';
		$opendates = 0;
		$cal_from = false;
		$cal_to = false;

		# count
		$count = min(max($params->get('count', '2'), 1), 100); // range 1..100, default 2

		# shuffle
		$shuffle = (bool)$params->get('shuffle', 0);
		if ($shuffle) {
			$max_count = min(max((int)$params->get('shuffle_count', 20), $count), 100);
		} else {
			$max_count = $count;
		}

		$model->setState('list.limit', $max_count);

		# create type dependent filter rules
		switch ($type) {
		case 1: # unfinished events
			$cal_from  = " (a.dates IS NULL OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) > $offset_minutes) ";
			$cal_from .= "  OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(IFNULL(a.enddates,a.dates),' ',IFNULL(a.endtimes,'23:59:59'))) > $offset_minutes)) ";
			$cal_to = $max_minutes ? " (a.dates IS NULL OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) < $max_minutes)) " : '';
			break;

		case 2: # archived events
			$published = 2;
			$orderdir = 'DESC';
			break;

		case 3: # running events (one day)
			$cal_from = " (DATEDIFF(IFNULL(a.enddates, a.dates), CURDATE()) >= $offset_days)) ";
			$cal_to   = " (DATEDIFF(a.dates, CURDATE()) < ".($offset_days + 1).") ";
			break;

		case 5: # open date (only)
			$opendates = 2;
			break;

		case 4: # featured events
			$model->setState('filter.featured', 1);
			# fall through
		case 0: # upcoming events
		default:
			$cal_from = " (a.dates IS NULL OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) > $offset_minutes) ";
			$cal_to = $max_minutes ? " (a.dates IS NULL OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) < $max_minutes)) " : '';
			break;
		}

		$model->setState('filter.published', $published);
		$model->setState('filter.orderby', array('a.dates '.$orderdir, 'a.times '.$orderdir));
		if (!empty($cal_from)) {
			$model->setState('filter.calendar_from', $cal_from);
		}
		if (!empty($cal_to)) {
			$model->setState('filter.calendar_to', $cal_to);
		}
		$model->setState('filter.groupby', 'a.id');

		# clean parameter data
		$catids = JemHelper::getValidIds($params->get('catid'));
		$venids = JemHelper::getValidIds($params->get('venid'));
		$eventids = JemHelper::getValidIds($params->get('eventid'));

		# Open date support
		if (!empty($eventids)) {
			// allow (also) open dates if limited to specific events
			$opendates = 1;
		}
		$model->setState('filter.opendates', $opendates);

		# filter category's
		if ($catids) {
			$model->setState('filter.category_id', $catids);
			$model->setState('filter.category_id.include', true);
		}

		# filter venue's
		if ($venids) {
			$model->setState('filter.venue_id', $venids);
			$model->setState('filter.venue_id.include', true);
		}

		# filter event id's
		if ($eventids) {
			$model->setState('filter.event_id', $eventids);
			$model->setState('filter.event_id.include', true);
		}

		if ($params->get('flyer_link_type', 0) == 1) {
			JHtml::_('behavior.modal', 'a.flyermodal');
		}

		# date/time
		$dateFormat = $params->get('formatdate', '');
		$timeFormat = $params->get('formattime', '');
		$addSuffix  = empty($timeFormat); // if we use component's default time format we can also add corresponding suffix

		####
		# Retrieve the available Events
		####
		$events = $model->getItems();

		# Loop through the result rows and prepare data
		$lists = array();
		$i     = -1; // it's easier to increment first

		// Don't shuffle original array to keep ordering of remaining events intact.
		$indices = array_keys($events);
		if (count($events) > $count) {
			if ($shuffle) {
				shuffle($indices);
			}
			array_splice($indices, $count);
		}

		foreach ($events as $key => $row)
		{
			if (!in_array($key, $indices)) {
				continue; // skip removed events
			}

			# create thumbnails if needed and receive imagedata
			$dimage = $row->datimage ? JEMImage::flyercreator($row->datimage, 'event') : null;
			$limage = $row->locimage ? JEMImage::flyercreator($row->locimage, 'venue') : null;

			#################
			## DEFINE LIST ##
			#################

			$lists[++$i] = new stdClass(); // add new object

			# check view access
			if (in_array($row->access, $levels)) {
				# We know that user has the privilege to view the event
				$lists[$i]->link = JRoute::_(JEMHelperRoute::getEventRoute($row->slug));
				$lists[$i]->linkText = JText::_('MOD_JEM_BANNER_READMORE');
			} else {
				$lists[$i]->link = JRoute::_('index.php?option=com_users&view=login');
				$lists[$i]->linkText = JText::_('MOD_JEM_BANNER_READMORE_REGISTER');
			}

			# cut titel
			$fulltitle = htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8');
			if (mb_strlen($fulltitle) > $max_title_length) {
				$title = mb_substr($fulltitle, 0, $max_title_length) . '...';
			} else {
				$title = $fulltitle;
			}

			$lists[$i]->title       = $title;
			$lists[$i]->fulltitle   = $fulltitle;
			$lists[$i]->venue       = htmlspecialchars($row->venue, ENT_COMPAT, 'UTF-8');
			$lists[$i]->catname     = implode(", ", JemOutput::getCategoryList($row->categories, $params->get('linkcategory', 1)));
			$lists[$i]->state       = htmlspecialchars($row->state, ENT_COMPAT, 'UTF-8');
			$lists[$i]->city        = htmlspecialchars($row->city, ENT_COMPAT, 'UTF-8');
			$lists[$i]->eventlink   = $params->get('linkevent', 1) ? JRoute::_(JEMHelperRoute::getEventRoute($row->slug)) : '';
			$lists[$i]->venuelink   = $params->get('linkvenue', 1) ? JRoute::_(JEMHelperRoute::getVenueRoute($row->venueslug)) : '';

			# time/date
			/* depending on settongs we need:
		     *  showcalendar 1, datemethod 1 : month, weekday, day + time
		     *  showcalendar 1, datemethod 2 : month, weekday, day + relative date + time
		     *  showcalendar 0, datemethod 1 : (long) date + time
		     *  showcalendar 0, datemethod 2 : relative date + time
		     */
			static $formats  = array('year' => 'Y', 'month' => 'F', 'day' => 'j', 'weekday' => 'l');
			static $defaults = array('year' => '&nbsp;', 'month' => '', 'day' => '?', 'weekday' => '');

			$lists[$i]->startdate   = empty($row->dates)    ? $defaults : self::_format_date_fields($row->dates,    $formats);
			$lists[$i]->enddate     = empty($row->enddates) ? $defaults : self::_format_date_fields($row->enddates, $formats);
			list($lists[$i]->date,
			     $lists[$i]->time)  = self::_format_date_time($row, $params->get('datemethod', 1), $dateFormat, $timeFormat, $addSuffix);
			$lists[$i]->dateinfo    = JEMOutput::formatDateTime($row->dates, $row->times, $row->enddates, $row->endtimes, $dateFormat, $timeFormat, $addSuffix);

			if ($dimage == null) {
				$lists[$i]->eventimage     = '';
				$lists[$i]->eventimageorig = '';
			} else {
				$lists[$i]->eventimage     = JUri::base(true).'/'.$dimage['thumb'];
				$lists[$i]->eventimageorig = JUri::base(true).'/'.$dimage['original'];
			}

			if ($limage == null) {
				$lists[$i]->venueimage     = '';
				$lists[$i]->venueimageorig = '';
			} else {
				$lists[$i]->venueimage     = JUri::base(true).'/'.$limage['thumb'];
				$lists[$i]->venueimageorig = JUri::base(true).'/'.$limage['original'];
			}

			$length = $params->get('descriptionlength');
			$length2 = 1;
			$etc = '...';
			$etc2 = JText::_('MOD_JEM_BANNER_NO_DESCRIPTION');

			//strip html tags but leave <br /> tags
			$description = strip_tags($row->introtext, "<br>");

			//switch <br /> tags to space character
			if ($params->get('br') == 0) {
				$description = str_replace('<br />',' ', $description);
			}
			//
			if (strlen($description) > $length) {
				$length -= strlen($etc);
				$description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, $length+1));
				$lists[$i]->eventdescription = substr($description, 0, $length).$etc;
			} elseif (strlen($description) < $length2) {
				$length -= strlen($etc2);
				$description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, $length+1));
				$lists[$i]->eventdescription = substr($description, 0, $length).$etc2;
			} else {
				$lists[$i]->eventdescription = $description;
			}

			$lists[$i]->readmore = strlen(trim($row->fulltext));
		} // foreach ($events as $row)

		return $lists;
	}

	/**
	 *format days
	 */
	protected static function _format_day($row, &$params)
	{
		//Get needed timestamps and format
		//setlocale (LC_TIME, 'de_DE.UTF8');
		$yesterday_stamp = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
		$yesterday       = strftime("%Y-%m-%d", $yesterday_stamp);
		$today_stamp     = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$today           = date('Y-m-d');
		$tomorrow_stamp  = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));
		$tomorrow        = strftime("%Y-%m-%d", $tomorrow_stamp);

		$dates_stamp     = strtotime($row->dates);
		$enddates_stamp  = $row->enddates ? strtotime($row->enddates) : null;

		//check if today or tomorrow or yesterday and no current running multiday event
		if (($row->dates == $today) && empty($enddates_stamp)) {
			$result = JText::_('MOD_JEM_BANNER_TODAY');
		} elseif ($row->dates == $tomorrow) {
			$result = JText::_('MOD_JEM_BANNER_TOMORROW');
		} elseif ($row->dates == $yesterday) {
			$result = JText::_('MOD_JEM_BANNER_YESTERDAY');
		} else {
			//if daymethod show day
			if ($params->get('daymethod', 1) == 1) {
				//single day event
				$date = strftime('%A', strtotime($row->dates));
				$result = JText::sprintf('MOD_JEM_BANNER_ON_DATE', $date);

				//Upcoming multidayevent (From 16.10.2010 Until 18.10.2010)
				if (($dates_stamp > $tomorrow_stamp) && $enddates_stamp) {
					$startdate = strftime('%A', strtotime($row->dates));
					$result = JText::sprintf('MOD_JEM_BANNER_FROM', $startdate);
				}

				//current multidayevent (Until 18.08.2008)
				if ($row->enddates && ($enddates_stamp > $today_stamp) && ($dates_stamp <= $today_stamp)) {
					//format date
					$enddate = strftime('%A', strtotime($row->enddates));
					$result = JText::sprintf('MOD_JEM_BANNER_UNTIL', $enddate);
				}
			} else { // show day difference
				//the event has an enddate and it's earlier than yesterday
				if ($row->enddates && ($enddates_stamp < $yesterday_stamp)) {
					$days = round( ($today_stamp - $enddates_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_BANNER_ENDED_DAYS_AGO', $days);

				//the event has an enddate and it's later than today but the startdate is today or earlier than today
				//means a currently running event with startdate = today
				} elseif ($row->enddates && ($enddates_stamp > $today_stamp) && ($dates_stamp <= $today_stamp)) {
					$days = round( ($enddates_stamp - $today_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_BANNER_DAYS_LEFT', $days);

				//the events date is earlier than yesterday
				} elseif ($dates_stamp < $yesterday_stamp) {
					$days = round( ($today_stamp - $dates_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_BANNER_DAYS_AGO', $days );

				//the events date is later than tomorrow
				} elseif ($dates_stamp > $tomorrow_stamp) {
					$days = round( ($dates_stamp - $today_stamp) / 86400 );
					$result = JText::sprintf('MOD_JEM_BANNER_DAYS_AHEAD', $days);
				}
			}
		}

		return $result;
	}

	/**
	 * Method to format date and time information
	 *
	 * @access protected
	 * @return array(string, string) returns date and time strings as array
	 */
	protected static function _format_date_time($row, $method, $dateFormat = '', $timeFormat = '', $addSuffix = false)
	{
		if (empty($row->dates)) {
			// open date
			$date  = JEMOutput::formatDateTime('', ''); // "Open date"
			$times = $row->times;
			$endtimes = $row->endtimes;
		} else {
			//Get needed timestamps and format
			$yesterday_stamp = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
			$yesterday       = strftime("%Y-%m-%d", $yesterday_stamp);
			$today_stamp     = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$today           = date('Y-m-d');
			$tomorrow_stamp  = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));
			$tomorrow        = strftime("%Y-%m-%d", $tomorrow_stamp);

			$dates_stamp     = $row->dates ? strtotime($row->dates) : null;
			$enddates_stamp  = $row->enddates ? strtotime($row->enddates) : null;

			$times = $row->times; // show starttime by default

			//if datemethod show day difference
			if ($method == 2) {
				//check if today or tomorrow
				if ($row->dates == $today) {
					$date = JText::_('MOD_JEM_BANNER_TODAY');
				} elseif ($row->dates == $tomorrow) {
					$date = JText::_('MOD_JEM_BANNER_TOMORROW');
				} elseif ($row->dates == $yesterday) {
					$date = JText::_('MOD_JEM_BANNER_YESTERDAY');
				}
				//This one isn't very different from the DAYS AGO output but it seems
				//adequate to use a different language string here.
				//
				//the event has an enddate and it's earlier than yesterday
				elseif ($row->enddates && ($enddates_stamp < $yesterday_stamp)) {
					$days = round(($today_stamp - $enddates_stamp) / 86400);
					$date = JText::sprintf('MOD_JEM_BANNER_ENDED_DAYS_AGO', $days);
					// show endtime instead of starttime
					$times = false;
					$endtimes = $row->endtimes;
				}
				//the event has an enddate and it's later than today but the startdate is earlier than today
				//means a currently running event
				elseif ($row->dates && $row->enddates && ($enddates_stamp > $today_stamp) && ($dates_stamp < $today_stamp)) {
					$days = round(($today_stamp - $dates_stamp) / 86400);
					$date = JText::sprintf('MOD_JEM_BANNER_STARTED_DAYS_AGO', $days);
				}
				//the events date is earlier than yesterday
				elseif ($row->dates && ($dates_stamp < $yesterday_stamp)) {
					$days = round(($today_stamp - $dates_stamp) / 86400);
					$date = JText::sprintf('MOD_JEM_BANNER_DAYS_AGO', $days);
				}
				//the events date is later than tomorrow
				elseif ($row->dates && ($dates_stamp > $tomorrow_stamp)) {
					$days = round(($dates_stamp - $today_stamp) / 86400);
					$date = JText::sprintf('MOD_JEM_BANNER_DAYS_AHEAD', $days);
				}
				else {
					$date = JEMOutput::formatDateTime('', ''); // Oops - say "Open date"
				}
			} else { // datemethod show date
			// TODO: check date+time to be more acurate
				//Upcoming multidayevent (From 16.10.2008 Until 18.08.2008)
				if (($dates_stamp >= $today_stamp) && ($enddates_stamp > $dates_stamp)) {
					$startdate = JEMOutput::formatdate($row->dates, $dateFormat);
					$enddate = JEMOutput::formatdate($row->enddates, $dateFormat);
					$date = JText::sprintf('MOD_JEM_BANNER_FROM_UNTIL', $startdate, $enddate);
					// additionally show endtime
					$endtimes = $row->endtimes;
				}
				//current multidayevent (Until 18.08.2008)
				elseif ($row->enddates && ($enddates_stamp >= $today_stamp) && ($dates_stamp < $today_stamp)) {
					$enddate = JEMOutput::formatdate($row->enddates, $dateFormat);
					$date = JText::sprintf('MOD_JEM_BANNER_UNTIL', $enddate);
					// show endtime instead of starttime
					$times = false;
					$endtimes = $row->endtimes;
				}
				//single day event
				else {
					$startdate = JEMOutput::formatdate($row->dates, $dateFormat);
					$date = JText::sprintf('MOD_JEM_BANNER_ON_DATE', $startdate);
					// additionally show endtime, but on single day events only to prevent user confusion
					if (empty($row->enddates)) {
						$endtimes = $row->endtimes;
					}
				}
			}
		}

		$time  = empty($times)    ? '' : JEMOutput::formattime($times, $timeFormat, $addSuffix);
		$time .= empty($endtimes) ? '' : ('&nbsp;-&nbsp;' . JEMOutput::formattime($row->endtimes, $timeFormat, $addSuffix));

		return array($date, $time);
	}

	/**
	 * Method to format different parts of a date as array
	 *
	 * @access public
	 * @param  string date in form 'yyyy-mm-dd'
	 * @param  array  formats to get as assotiative array (e.g. 'day' => 'j'; see {@link PHP_MANUAL#date})
	 * @return mixed  array of formatted date parts or false
	 */
	protected static function _format_date_fields($date, array $formats)
	{
		if (empty($formats)) {
			return false;
		}

		$result = array();
		$jdate = new JDate($date);

		foreach ($formats as $k => $v) {
			$result[$k] = $jdate->format($v, false, true);
		}

		return $result;
	}
}