<?php
/**
 * @package JEM
 * @subpackage JEM Listevents Plugin
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jem/models', 'JemModel');
require_once JPATH_SITE . '/components/com_jem/helpers/helper.php';

// check for component
/*
if (!JComponentHelper::isEnabled('com_jem', true)) {
	return JError::raiseError(JText::_('LISTEVENTS ERROR'), JText::_('EVENTLIST IS NOT INSTALLED ON YOUR SYSTEM'));
}
*/

// TODO: add option to select status of events to be displayed

// Import library dependencies
jimport('joomla.plugin.plugin');

class plgContentListevents extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	* Plugin that outputs a list of events from JEM
	*/

	// onContentPrepare, meaning the plugin is rendered at the first stage in preparing content for output
	public function onContentPrepare($context, &$row, &$params, $page=0 )
	{
		// global $mainframe;

		// simple performance check to determine whether bot should process further
		if ( JString::strpos( $row->text, 'listevents' ) === false ) {
			return true;
		}

 		// expression to search for
		$regex = '/{listevents\s*.*?}/i';

		// check whether plugin has been unpublished
		if (!$this->params->get( 'enabled', 1 ) ) {
			$row->text = preg_replace( $regex, '', $row->text );
			return true;
		}

		// find all instances of plugin and put in $matches
		preg_match_all( $regex, $row->text, $matches );

		// Number of plugins
		$count = count( $matches[0] );

		// plugin only processes if there are any instances of the plugin in the text
		if ( $count ) {
			// Get plugin parameters
			$style = $this->params->def( 'style', -2 );
			$this->_process( $row, $matches, $count, $regex, $style );
		}

		// No return value
	}

	// The proccessing function
	protected function _process( &$content, &$matches, $count, $regex, $style )
	{
		// Get plugin parameters
		$eventstype 	= $this->params->def( 'type', 'current' );
		$eventstitle	= $this->params->def( 'title', 'on' );
		$eventsdate 	= $this->params->def( 'date', 'on' );
		$eventstime 	= $this->params->def( 'time', 'off' );
		$eventscatid	= $this->params->def( 'catid', '' );
		$eventscategory	= $this->params->def( 'category', 'off' );
		$eventsvenueid	= $this->params->def( 'venueid', '' );
		$eventsvenue	= $this->params->def( 'venue', 'off' );
		$eventsmax		= $this->params->def( 'max', 0 );
		$eventsmsgnone	= $this->params->def( 'noeventsmsg', '' );

		for ( $i=0; $i < $count; $i++ )
		{
			// Get plugin parameters from Content
			$parameters 	= str_replace( array('{','listevents','}'), '', $matches[0][$i] );
			$parameters	= strtolower( trim( $parameters ) );
			$special_chars	= array ("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");
			$parameters 	= str_replace( $special_chars, '', $parameters );

			$options = explode(" ", $parameters);
			// $options = explode("|", $parameters);

			foreach ($options as $parameter) {

				$parameter = str_replace("[","",$parameter);
				$parameter = str_replace("]","",$parameter);

				$option = explode("=", $parameter, 2);

				if (!$option[0] || !$option[1] ) {
					// parameter not properly defined
					continue;
				}

				switch ($option[0])
				{
					case 'type':		// type of events
					{
						$eventstype = $option[1];
						break;
					}
					case 'title':		// display title of events
					{
						$eventstitle = $option[1];
						break;
					}
					case 'date':		// display date of events
					{
						$eventsdate = $option[1];
						break;
					}
					case 'time':		// display time of events
					{
						$eventstime = $option[1];
						break;
					}
					case 'catid':		// filter events category
					{
						$eventscatid = $option[1];
						break;
					}
					case 'category':	// display category of events
					{
						$eventscategory = $option[1];
						break;
					}
					case 'venueid':		// filter events venue
					{
						$eventsvenueid = $option[1];
						break;
					}
					case 'venue':	// display venue of events
					{
						$eventsvenue = $option[1];
						break;
					}
					case 'max':			// max number of events
					{
						if (is_numeric($option[1]))
						{
							$eventsmax = (int) $option[1];
						}
						break;
					}
					case 'noeventsmsg':	// no events message
					{
						$eventsmsgnone = $option[1];
						break;
					}

					default:			// ignore parameter
				}

			}

			$parameters = array();
			$parameters["eventstype"] 		= $eventstype;
			$parameters["eventstitle"]		= $eventstitle;
			$parameters["eventsdate"] 		= $eventsdate;
			$parameters["eventstime"] 		= $eventstime;
			$parameters["eventscatid"] 		= $eventscatid;
			$parameters["eventscategory"] 	= $eventscategory;
			$parameters["eventsvenueid"] 	= $eventsvenueid;
			$parameters["eventsvenue"]		= $eventsvenue;
			$parameters["eventsmax"] 		= $eventsmax;
			$parameters["eventsmsgnone"] 	= $eventsmsgnone;

			$eventlist = $this->_load( $parameters );
			$display = $this->_display( $eventlist, $parameters, $i );

			$content->text = str_replace( $matches[0][$i], $display, $content->text );
		}

		// removes tags without matching plugin options
		$content->text = preg_replace( $regex, '', $content->text );
	}

	// The function who takes care for the 'completing' of the plugins' actions : load the events
	protected function _load( $parameters )
	{
		# Retrieve Eventslist model for the data
		$model = JModelLegacy::getInstance('Eventslist', 'JemModel', array('ignore_request' => true));

		################################
		## EXCLUDE/INCLUDE CATEGORIES ##
		################################

		if (isset($parameters["eventscatid"])) {
			$catids = $parameters["eventscatid"];

			if ($catids) {
				$model->setState('filter.category_id',$catids);
				$model->setState('filter.category_id.include',1);
			}
		}

		####################
		## FILTER - VENUE ##
		####################
		if (isset($parameters["eventsvenueid"])) {
			$venueid = $parameters["eventsvenueid"];

			if ($venueid) {
				$model->setState('filter.venue_id',$venueid);
				$model->setState('filter.venue_id.include',1);
			}
		}

		$type = $parameters["eventstype"];
		$offset_hourss = 0;

		# all upcoming events
		if (($type == 0) || ($type == 1)) {

			$offset_minutes = $offset_hourss * 60;

			$model->setState('filter.published',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));

			$cal_from = "((TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) > $offset_minutes) ";
			$cal_from .= ($type == 1) ? " OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(IFNULL(a.enddates,a.dates),' ',IFNULL(a.endtimes,'23:59:59'))) > $offset_minutes)) " : ") ";
		}

		# archived events only
		elseif ($type == 2) {
			$model->setState('filter.published',2);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));
			$cal_from = "";
		}

		# currently running events only
		elseif ($type == 3) {
			$offset_days = (int)round($offset_hourss / 24);

			$model->setState('filter.published',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));

			$cal_from = " ((DATEDIFF(a.dates, CURDATE()) <= $offset_days) AND (DATEDIFF(IFNULL(a.enddates,a.dates), CURDATE()) >= $offset_days))";
		}

		# featured
		elseif ($type == 4) {
			$offset_minutes = $offset_hourss * 60;

			$model->setState('filter.featured',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));

			$cal_from  = "((TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) > $offset_minutes) ";
			$cal_from .= " OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(IFNULL(a.enddates,a.dates),' ',IFNULL(a.endtimes,'23:59:59'))) > $offset_minutes)) ";
		}

		$model->setState('filter.calendar_from',$cal_from);

		// $model->setState('filter.published',1);
		$model->setState('filter.groupby',array('a.id'));
		# Retrieve the available Events
		$rows = $model->getItems();

		return $rows;
	}

	// The function who takes care for the 'completing' of the plugins' actions : display the events
	protected function _display( $rows, $parameters, $listevents_id )
	{
		if (! $rows)
			return $parameters["eventsmsgnone"];

		$html_list  = '<div class="listevents" id="listevents-'. $listevents_id .'">';
		$html_list .= '<ul>';

		$n_event = 0;
		foreach ($rows as $event)
		{
			require_once JPATH_BASE . "/components/com_jem/helpers/route.php";
			$linkdetails 	= JRoute::_( JEMHelperRoute::getEventRoute($event->slug) );
			$linkdate 		= JRoute::_( JEMHelperRoute::getRoute(str_replace( '-','',$event->dates), 'day') );
			$linkvenue		= JRoute::_( JEMHelperRoute::getVenueRoute($event->venueslug) );
			$jemsettings	= JemHelper::config();

			if (( $parameters["eventstype"] == 'regprev') || ( $parameters["eventstype"] == 'regnext'))
			{
				require_once JPATH_BASE . "/components/com_jem/models/eventlist.php";
				$eventsmodel = new EventListModelEventList;
				$query = 'SELECT COUNT(uid) as attendees from #__eventlist_register WHERE event = '.$event->eventid;
				$eventsmodel->_db->setQuery( $query );
				$_event = $eventsmodel->_db->loadObject();
				$attendees = $_event->attendees;
				if ( $attendees == 0 )
					continue;
			}

			$html_list .= '<li id="listevent'. ($n_event+1) .'">';

			if ( $parameters["eventstitle"] != 'off' )
			{
				$html_list .= '<span id="eventtitle">';
				$html_list .= ( ($parameters["eventstitle"] == 'link') ? ('<a href="'.$linkdetails.'">') : '' );
				$html_list .= $event->title;
				$html_list .= ( ($parameters["eventstitle"] == 'link') ? '</a>' : '' );
				$html_list .= '</span>';
			}

			if ( ($parameters["eventsdate"] != 'off') && ($event->dates) )
			{
				# display startdate
				require_once JPATH_BASE . "/components/com_jem/helpers/helper.php";
				require_once JPATH_BASE . "/components/com_jem/classes/output.class.php";
				$html_list .= ' : '.'<span id="eventdate">';
				$html_list .= ( ($parameters["eventsdate"] == 'link') ? ('<a href="'.$linkdate.'">') : '' );
				$html_list .= JEMOutput::formatdate($event->dates);
				$html_list .= ( ($parameters["eventsdate"] == 'link') ? '</a>' : '' );
				$html_list .= '</span>';
			}

			if ( ($parameters["eventstime"] != 'off') && ($event->times) )
			{
				# display starttime
				require_once JPATH_BASE . "/components/com_jem/helpers/helper.php";
				require_once JPATH_BASE . "/components/com_jem/classes/output.class.php";
				$html_list .= ' '.'<span id="eventtime">';
				$html_list .= JEMOutput::formattime($event->times);
				$html_list .= '</span>';
			}

			if ( ($parameters["eventsvenue"] != 'off') && ($event->venue) )
			{
				$html_list .= ' : '.'<span id="eventvenue">';
				$html_list .= ( ($parameters["eventsvenue"] == 'link') ? ('<a href="'.$linkvenue.'">') : '' );
				$html_list .= $event->venue;
				$html_list .= ( ($parameters["eventsvenue"] == 'link') ? '</a>' : '' );
				$html_list .= '</span>';
			}


			if ( ($parameters["eventscategory"] != 'off') && ($event->categories) )
			{
				if ($parameters["eventscategory"] == 'link') {
					$catlink = 1;
				} else {
					$catlink = false;
				}

				$html_list .= " ";
				$html_list .= implode(", ", JemOutput::getCategoryList($event->categories, $catlink));
			}

			$html_list .= '</li>';

			$n_event++;
			if ( $parameters["eventsmax"] && ($n_event >= $parameters["eventsmax"]) )
				break;
		}

		if ( $n_event == 0 )
			$html_list .= $parameters["eventsmsgnone"];

		$html_list .= '</ul>';
		$html_list .= '</div>';

		return $html_list;
	}
}
