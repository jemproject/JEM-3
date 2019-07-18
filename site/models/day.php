<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/eventslist.php';

/**
 * Model-Day
 */
class JemModelDay extends JemModelEventslist
{
	public $_date = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$app = JFactory::getApplication();
		$jinput = $app->input;

		$rawday = $jinput->getInt('id', null);
		$this->setDate($rawday);
	}

	/**
	 * Method to set the date
	 *
	 * @access	public
	 * @param	string
	 */
	function setDate($date)
	{
		$app = JFactory::getApplication();

		# Get the params of the active menu item
		$params = $app->getParams('com_jem');

		# 0 means we have a direct request from a menuitem and without any params (eg: calendar module)
		if ($date == 0) {
			$dayoffset	= $params->get('days');
			$timestamp	= mktime(0, 0, 0, date("m"), date("d") + $dayoffset, date("Y"));
			$date		= strftime('%Y-%m-%d', $timestamp);

		# a valid date has 8 characters (ymd)
		} elseif (strlen($date) == 8) {
			$year 	= substr($date, 0, -4);
			$month	= substr($date, 4, -2);
			$tag	= substr($date, 6);

			//check if date is valid
			if (checkdate($month, $tag, $year)) {
				$date = $year.'-'.$month.'-'.$tag;
			} else {
				//date isn't valid raise notice and use current date
				$date = date('Ymd');
				JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_JEM_INVALID_DATE_REQUESTED_USING_CURRENT'));
			}
		} else {
			//date isn't valid raise notice and use current date
			$date = date('Ymd');
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_JEM_INVALID_DATE_REQUESTED_USING_CURRENT'));
		}

		$this->_date = $date;
	}

	/**
	 * Return date
	 */
	function getDay()
	{
		return $this->_date;
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		# parent::populateState($ordering, $direction);

		$app 				= JFactory::getApplication();
		$settings			= JemHelper::globalattribs();
		$jemsettings		= JemHelper::config();
		$jinput				= $app->input;
		$itemid 			= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		$task           	= $jinput->getCmd('task',null);
		$requestVenueId		= $jinput->getInt('locid',null);
		$requestCategoryId	= $jinput->getInt('catid',null);
		$item = $jinput->getInt('Itemid');
		
		
		$global = new JRegistry;
		$global->loadString($settings);
		
		$params = clone $global;
		$params->merge($global);
		if ($menu = $app->getMenu()->getActive())
		{
			$params->merge($menu->params);
		}
		$this->setState('params', $params);
		
		// CALAJAX | CALENDAR
		$locid = $app->getUserState('com_jem.calendar.locid'.$item,false);
		$locid_switch = $app->getUserState('com_jem.calendar.locid_switch'.$item,false);
		if ($locid) {
			$this->setstate('filter.venue_id',$locid);
			$this->setstate('filter.venue_id.include',$locid_switch);
		} else {
			$locid = $app->getUserState('com_jem.calajax.locid'.$item,false);
			$locid_switch = $app->getUserState('com_jem.calajax.locid_switch'.$item,false);
			if ($locid) {
				$this->setstate('filter.venue_id',$locid);
				$this->setstate('filter.venue_id.include',$locid_switch);
			}
		}
		
		$catid = $app->getUserState('com_jem.calendar.catid'.$item,false);
		$catid_switch = $app->getUserState('com_jem.calendar.catid_switch'.$item,false);
		
		if ($catid) {
			$this->setState('filter.category_id',$catid);
			$this->setState('filter.category_id.include',$catid_switch);
		} else {
			$catid = $app->getUserState('com_jem.calajax.catid'.$item,false);
			$catid_switch = $app->getUserState('com_jem.calajax.catid_switch'.$item,false);
			if ($catid) {
				$this->setState('filter.category_id',$catid);
				$this->setState('filter.category_id.include',$catid_switch);
			}
		}

		# limit/start
		$limit		= $app->getUserStateFromRequest('com_jem.day.'.$itemid.'.limit', 'limit', $jemsettings->display_num, 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		# Search
		$search = $app->getUserStateFromRequest('com_jem.day.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$this->setState('filter.filter_search', $search);

		# FilterType
		$filtertype = $app->getUserStateFromRequest('com_jem.day.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$this->setState('filter.filter_type', $filtertype);

		###########
		## ORDER ##
		###########
		$filter_order 		= $app->getUserStateFromRequest('com_jem.day.'.$itemid.'.filter_order', 'filter_order', 'a.dates', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.day.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', 'ASC', 'string');
		$filter_order		= JFilterInput::getInstance()->clean($filter_order, 'string');
		$filter_order_Dir	= JFilterInput::getInstance()->clean($filter_order_Dir, 'string');

		if ($filter_order == 'a.dates') {
			$orderby = array('a.dates '.$filter_order_Dir,'a.times '.$filter_order_Dir);
		} else {
			$orderby = $filter_order . ' ' . $filter_order_Dir;
		}

		$this->setState('filter.orderby',$orderby);

		# published
		$this->setState('filter.published',1);

		# request venue-id
		if ($requestVenueId) {
			$this->setState('filter.req_venid',$requestVenueId);
		}

		# request cat-id
		if ($requestCategoryId) {
			$this->setState('filter.req_catid',$requestCategoryId);
		}

		# access
		$this->setState('filter.access', true);

		# groupby
		$this->setState('filter.groupby',array('a.id'));
	}

	/**
	 * Method to get a list of events.
	 */
	public function getItems()
	{
		$params = clone $this->getState('params');
		$items	= parent::getItems();

		if ($items) {
			foreach ($items as &$item)
			{

			}
		}

		return $items;
	}

	/**
	 * @return	JDatabaseQuery
	 */
	function getListQuery()
	{
		$params  = $this->state->params;
		$jinput  = JFactory::getApplication()->input;
		$task    = $jinput->getCmd('task');

		$requestVenueId 	= $this->getState('filter.req_venid');

		// Create a new query object.
		$query = parent::getListQuery();

		if ($requestVenueId){
			$query->where(' a.locid = '.$requestVenueId);
		}

		// Second is to only select events of the specified day
		$query->where('(\''.$this->_date.'\' BETWEEN (a.dates) AND (IF (a.enddates >= a.dates, a.enddates, a.dates)) OR \''.$this->_date.'\' = a.dates)');

		return $query;
	}
}
