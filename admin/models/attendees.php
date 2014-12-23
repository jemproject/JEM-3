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
 * Model: Attendees
 */
class JemModelAttendees extends JModelList
{
	/**
	 * Events data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Events total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Events total
	 *
	 * @var integer
	 */
	var $_event = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Events id
	 *
	 * @var int
	 */
	var $_eid = null;

	/**
	 * Constructor
	 *
	 */
	/**
	 * Constructor.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'u.username', 'u.name',
					'name','username',
					'r.uregdate','r.uid',
					'r.waiting',
					'waiting','filtertype',
			);
		}
		
		$app 	= JFactory::getApplication();
		$jinput = $app->input;
		
		# retrieve event-id
		$eid		= $jinput->getInt('eid');
		$this->setId($eid);
	
		parent::__construct($config);
	}
	
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app 			= JFactory::getApplication();
		$jemsettings	= JemHelper::config();
		
		# it's needed to set the parent option
		parent::populateState('a.dates', 'asc');
	}
	
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 *
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$app 	= JFactory::getApplication();
		$jinput = $app->input;
		$layout = $jinput->getWord('layout');
		$eid	= $jinput->getInt('eid');
		
		
		$query->select(array('r.*','u.username','u.name','u.email'));
		$query->from('#__jem_register AS r');
		$query->join('LEFT', '#__jem_events AS a ON (r.event = a.id)');
		$query->join('LEFT', '#__users AS u ON (u.id = r.uid)');
		$query->where('r.event = '.$this->_eid);
		
		$filter_waiting = $this->getState('filter.waiting');
		
		if (!empty($filter_waiting)) {
			$query->where('(a.waitinglist = 0 OR r.waiting = '.$db->quote($filter_waiting-1).')');
		}
		
		// Filter by search in title
		$filter = $this->getState('filter.filtertype');
		$search = $this->getState('filter.search');
		
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
		
				if($search) {
					switch($filter) {
						case 1:
							/* search name */
							$query->where('u.name LIKE '.$search);
							break;
						case 2:
							/* search username */
							$query->where('u.username LIKE '.$search);
							break;
						default:
							$query->where('u.name LIKE '.$search);
					}
				}
			}
		}
		
		# ordering
		$orderCol	= $this->state->get('list.ordering','u.username');
		$orderDirn	= $this->state->get('list.direction','asc');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
		
	}
	

	/**
	 * Method to set the category identifier
	 *
	 * @access	public
	 * @param	int Category identifier
	 */
	function setId($eid)
	{
		// Set id and wipe data
		$this->_eid	    = $eid;
	}


	/**
	 * Get event data
	 *
	 * @access public
	 * @return object
	 *
	 */
	function getEvent()
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','title','dates','maxplaces','waitinglist'));
		$query->from('#__jem_events');
		$query->where('id = '.$this->_eid);
		$db->setQuery( $query );
		$_event = $db->loadObject();

		return $_event;
	}

	/**
	 * Delete registered users
	 *
	 * @access public
	 * @return true on success
	 *
	 */
	function remove($cid = array())
	{
		if (count( $cid ))
		{
			$user = implode(',', $cid);
			$db = JFactory::getDbo();

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__jem_register'));
			$query->where('id IN ('.$user.')');

			$db->setQuery($query);

			if (!$db->execute()) {
				JError::raiseError( 4711, $db->getErrorMsg() );
			}
		}
		return true;
	}
}
?>