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
 * Model-Category
 */
class JemModelCategory extends JemModelEventslist
{
	protected $_id			= null;
	protected $_data		= null;
	protected $_childs		= null;
	protected $_category	= null;
	//protected $_pagination	= null;
	protected $_item		= null;
	protected $_articles	= null;
	protected $_siblings	= null;
	protected $_children	= null;
	protected $_parent	= null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'title', 'a.title',
					'dates', 'a.dates',
					'times', 'a.times',
					'alias', 'a.alias',
					'venue', 'l.venue','venue_title',
					'city', 'l.city', 'venue_city',
			);
		}


		$app			= JFactory::getApplication();
		$jinput 		= JFactory::getApplication()->input;
		$jemsettings	= JEMHelper::config();
		$itemid			= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		// Get the parameters of the active menu item
		$params 	= $app->getParams();

		if ($jinput->getInt('id')) {
			$id = $jinput->getInt('id');
		} else {
			$id = $params->get('id', 1);
		}

		$this->setId((int)$id);

		parent::__construct();
	}

	/**
	 * Set Date
	 */
	function setdate($date)
	{
		$this->_date = $date;
	}

	/**
	 * Method to set the category id
	 */
	function setId($id)
	{
		// Set new category ID and wipe data
		$this->_id			= $id;
		$this->_data		= null;
	}

	/**
	 * set limit
	 * @param int value
	 */
	function setLimit($value)
	{
		$this->setState('limit', (int) $value);
	}

	/**
	 * set limitstart
	 * @param int value
	 */
	function setLimitStart($value)
	{
		$this->setState('limitstart', (int) $value);
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initiliase variables.
		$app			= JFactory::getApplication('site');
		$jemsettings	= JemHelper::config();
		$settings		= JemHelper::globalattribs();
		$jinput         = JFactory::getApplication()->input;
		$task           = $jinput->getCmd('task');
		$itemid			= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);
		$pk				= $jinput->getInt('id');

		$this->setState('category.id', $pk);
		$this->setState('filter.req_catid',$pk);

		$global = new JRegistry;
		$global->loadString($settings);

		$params = clone $global;
		$params->merge($global);
		if ($menu = $app->getMenu()->getActive())
		{
			$params->merge($menu->params);
		}
		$this->setState('params', $params);

		$user		= JFactory::getUser();
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		# limit/start
		$limit	= $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.limit', 'limit', $jemsettings->display_num, 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		# Search - variables
		$search = $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$this->setState('filter.filter_search', $search);

		$filtertype = $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$this->setState('filter.filter_type', $filtertype);

		# publish state
		if ($task == 'archive') {
			$this->setState('filter.published',2);
		} else {
			# we've to check if the setting for the filter has been applied
			if ($params->get('global_show_archive_icon')) {
				$this->setState('filter.published',1);
			} else {
				# retrieve the status to be displayed
				switch ($params->get('global_show_eventstatus')) {
					case 0:
						$status = 1;
						break;
					case 1:
						$status = 2;
						break;
					case 2:
						$status = array(1,2);
						break;
					default:
						$status = 1;
				}
				$this->setState('filter.published',$status);
			}
		}

		###############
		## opendates ##
		###############

		$this->setState('filter.opendates', $params->get('showopendates', 0));

		###########
		## ORDER ##
		###########

		# retrieve default sortDirection + sortColumn
		$sortDir		= strtoupper($params->get('sortDirection'));
		$sortDirArchive	= strtoupper($params->get('sortDirectionArchive'));
		$sortCol		= $params->get('sortColumn');

		$direction	= array('DESC', 'ASC');

		if (!in_array($sortCol, $this->filter_fields))
		{
			$sortCol = 'a.dates';
		}

		if (!in_array($sortDir, $direction))
		{
			$sortDir = 'ASC';
		}

		if (!in_array($sortDirArchive, $direction))
		{
			$sortDirArchive = 'DESC';
		}

		$filter_order		= $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_order', 'filter_order', $sortCol, 'cmd');
		$filter_order_DirDefault = $sortDir;
		// Reverse default order for dates in archive mode
		if($task == 'archive' && $filter_order == 'a.dates') {
			$filter_order_DirDefault = $sortDirArchive;
		}
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.category.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', $filter_order_DirDefault, 'word');
		$filter_order		= JFilterInput::getInstance()->clean($filter_order, 'string');
		$filter_order_Dir	= JFilterInput::getInstance()->clean($filter_order_Dir, 'string');

		if ($filter_order == 'a.dates') {
			$orderby = array('a.dates '.$filter_order_Dir,'a.times '.$filter_order_Dir);
		} else {
			$orderby = $filter_order . ' ' . $filter_order_Dir;
		}

		$this->setState('filter.orderby',$orderby);
		$this->setState('filter.access', true);
	}

	/**
	 * Get the events in the category
	 */
	function getItems()
	{

		$params = clone $this->getState('params');
		$items	= parent::getItems();

		if ($items) {
			foreach ($items as &$item)
			{

			}

			return $items;
		}

		return array();
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @param	int		An optional ID
	 */
	public function getCategory()
	{
		if (!is_object($this->_item)) {
			if( isset( $this->state->params ) ) {
				$params = $this->state->params;
				$options = array();
				$options['countItems'] = $params->get('show_cat_num_articles', 1) || !$params->get('show_empty_categories_cat', 0);
			}
			else {
				$options['countItems'] = 0;
			}

			$categories = new JEMCategories($this->getState('category.id', 'root'));
			$this->_item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions.
			if (is_object($this->_item)) {
				$user	= JFactory::getUser();
				$userId	= $user->get('id');
				$asset	= 'com_jem.category.'.$this->_item->id;

				// Check general create permission.
				if ($user->authorise('core.create', $asset)) {
					$this->_item->getParams()->set('access-create', true);
				}

				$this->_children = $this->_item->getChildren();

				$this->_parent = false;

				if ($this->_item->getParent()) {
					$this->_parent = $this->_item->getParent();
				}

				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else {
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
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

		// here we can extend the query of the Eventslist model
		return $query;
	}

	/**
	 * Get the parent categorie.
	 */
	public function getParent()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_parent;
	}

	/**
	 * Get the left sibling (adjacent) categories.
	 */
	function &getLeftSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_leftsibling;
	}

	/**
	 * Get the right sibling (adjacent) categories.
	 */
	function &getRightSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_rightsibling;
	}

	/**
	 * Get the child categories.
	 */
	function &getChildren()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		// Order subcategories
		if (sizeof($this->_children)) {
			$params = $this->getState()->get('params');
			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha') {
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->_children;
	}
}
