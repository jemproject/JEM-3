<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die ;

require_once dirname(__FILE__) . '/eventslist.php';

/**
 * Model-Calendar
 */
class JemModelCalendar extends JemModelEventslist
{
	protected $_date = 0;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->setdate(time());

		parent::__construct();
	}

	function setdate($date)
	{
		$this->_date = $date;
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		# parent::populateState($ordering, $direction);
		$app 			= JFactory::getApplication();
		$jemsettings	= JemHelper::config();
		$jinput			= JFactory::getApplication()->input;
		$itemid 		= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		$params 		= $app->getParams();
		$task           = $jinput->getCmd('task');

		# params
		$this->setState('params', $params);

		# publish state
		$this->setState('filter.published', 1);

		# access
		$this->setState('filter.access', true);

		###########
		## DATES ##
		###########

		#only select events within specified dates. (chosen month)
		$monthstart	= mktime(0, 0, 1, strftime('%m', $this->_date), 1, strftime('%Y', $this->_date));
		$monthend	= mktime(0, 0, -1, strftime('%m', $this->_date)+1, 1, strftime('%Y', $this->_date));

		$filter_date_from	= $this->_db->Quote(strftime('%Y-%m-%d', $monthstart));
		$filter_date_to		= $this->_db->Quote(strftime('%Y-%m-%d', $monthend));

		$where = ' DATEDIFF(IF (a.enddates IS NOT NULL, a.enddates, a.dates), '. $filter_date_from .') >= 0';
		$this->setState('filter.calendar_from',$where);

		$where = ' DATEDIFF(a.dates, '. $filter_date_to .') <= 0';
		$this->setState('filter.calendar_to',$where);

		#####################
		## FILTER-CATEGORY ##
		#####################

		$catids = $params->get('catids');
		$venids = $params->get('venueids');
		$eventids = $params->get('eventids');

		$catidsfilter = $params->get('catidsfilter');
		$venidsfilter = $params->get('venueidsfilter');
		$eventidsfilter = $params->get('eventidsfilter');

		if ($catids) {
			$this->setState('filter.category_id',$catids);
			$this->setState('filter.category_id.include',$catidsfilter);
		}

		if ($venids) {
			$this->setState('filter.venue_id',$venids);
			$this->setState('filter.venue_id.include',$venidsfilter);
		}

		if ($eventids) {
			$this->setState('filter.event_id',$eventids);
			$this->setState('filter.event_id.include',$eventidsfilter);
		}

		# set filter
		$this->setState('filter.calendar_multiday',true);
		$this->setState('filter.groupby',array('a.id'));
	}

	/**
	 * Method to get a list of events.
	 */
	public function getItems()
	{
		$app 			= JFactory::getApplication();
		$params 		= $app->getParams();

		$items	= parent::getItems();

		if ($items) {
			return $items;
		}

		return array();
	}

	/**
	 * @return	JDatabaseQuery
	 */
	function getListQuery()
	{
		$params  = $this->state->params;
		$jinput  = JFactory::getApplication()->input;
		$task    = $jinput->getCmd('task');

		// Create a new query object.
		$query = parent::getListQuery();

		$query->select('DATEDIFF(a.enddates, a.dates) AS datesdiff,DAYOFMONTH(a.dates) AS start_day, YEAR(a.dates) AS start_year, MONTH(a.dates) AS start_month');

		// here we can extend the query of the Eventslist model

		return $query;
	}

	/**
	 * Method to retrieve the dates linked to this calendar
	 *
	 * @access public
	 * @return integer
	 */
	function getSpecialDays () {

		$app 			= JFactory::getApplication();
		$jinput 		= JFactory::getApplication()->input;
		$params 		= $app->getParams();
		$use_dates		= $params->get('use_dates', 1);
		$itemid 		= $jinput->getInt('Itemid', 0);

		if ($use_dates) {

			# retrieve all linked calendar Itemid's
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('calendar_linked'));
			$query->from($db->quoteName('#__jem_dates'));
			$query->where(array('enabled = 1','calendar = 1'));
			$db->setQuery($query);
			$reference = $db->loadResult();
			$array_reference = json_decode($reference);

			# do we have Itemid's?
			if ($array_reference) {

				# let's see if the menu-item of this calendar is linked
				$needle = in_array($itemid,$array_reference);

				if ($needle) {
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select(array('date', 'calendar_name'));
					$query->from($db->quoteName('#__jem_dates'));
					$query->where(array('enabled = 1','calendar = 1'));
					$db->setQuery($query);
					$dates = $db->loadAssocList();
					return $dates;
				}
			}
		}
	}
}
