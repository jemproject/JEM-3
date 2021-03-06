<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Override function within JCategory
 */
class JemCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__jem_cats_event_relations';
		$options['extension'] = 'com_jem';
		$options['statefield'] = 'published';
		$options['published'] = false;
		parent::__construct($options);
	}
	
	
	/**
	 * Load method
	 */
	protected function _load($id)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
	
		// Record that has this $id has been checked
		$this->_checkedCategories[$id] = true;
	
		$query = $db->getQuery(true);
	
		// Right join with c for category
		$query->select('c.id, c.access, c.alias, c.checked_out, c.checked_out_time,
			c.created_time, c.created_user_id, c.description, c.language, c.level,
			c.lft, c.meta_description, c.meta_keywords, c.modified_time, c.note, c.parent_id,
			c.path, c.image, c.published, c.rgt, c.catname, c.modified_user_id');
		$case_when = ' CASE WHEN ';
		$case_when .= $query->charLength('c.alias', '!=', '0');
		$case_when .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$case_when .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= $c_id . ' END as slug';
		$query->select($case_when)
		->from('#__jem_categories as c');
	
		if ($this->_options['access'])
		{
			$query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		}
	
		if ($this->_options['published'] == 1)
		{
			$query->where('c.published = 1');
		}
	
		$query->order('c.lft');
	
		// Note: s for selected id
		if ($id != 'root')
		{
			// Get the selected category
			$query->join('LEFT', '#__jem_categories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)')
			->where('s.id=' . (int) $id);
		}
	
		$subQuery = ' (SELECT cat.id as id FROM #__jem_categories AS cat JOIN #__jem_categories AS parent ' .
				'ON cat.lft BETWEEN parent.lft AND parent.rgt ' .
				' AND parent.published != 1 GROUP BY cat.id) ';
		$query->join('LEFT', $subQuery . 'AS badcats ON badcats.id = c.id')
		->where('badcats.id is null');
	
		// Note: i for item
		if (isset($this->_options['countItems']) && $this->_options['countItems'] == 1)
		{
			if ($this->_options['published'] == 1)
			{
				$query->join(
						'LEFT',
						$db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id AND i.' . $this->_statefield . ' = 1'
				);
			}
			else
			{
				$query->join('LEFT', $db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id');
			}
	
			$query->select('COUNT(i.' . $db->quoteName($this->_key) . ') AS numitems');
		}
	
		// Group by
		$query->group(
				'c.id, c.access, c.alias, c.checked_out, c.checked_out_time,
			 c.created_time, c.created_user_id, c.description, c.language, c.level,
			 c.lft, c.meta_description, c.meta_keywords, c.modified_time, c.note, c.parent_id,
			 c.path, c.published, c.rgt, c.catname, c.modified_user_id'
		);
	
		// Get the results
		$db->setQuery($query);
		$results = $db->loadObjectList('id');
	
		$childrenLoaded = false;
	
		if (count($results))
		{
			// Foreach categories
			foreach ($results as $result)
			{
				// Deal with root category
				if ($result->id == 1)
				{
					$result->id = 'root';
				}
	
				// Deal with parent_id
				if ($result->parent_id == 1)
				{
					$result->parent_id = 'root';
				}
	
				// Create the node
				if (!isset($this->_nodes[$result->id]))
				{
					// Create the JCategoryNode and add to _nodes
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);
	
					// If this is not root and if the current node's parent is in the list or the current node parent is 0
					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id == 1))
					{
						// Compute relationship between node and its parent - set the parent in the _nodes field
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}
	
					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}
	
					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
				elseif ($result->id == $id || $childrenLoaded)
				{
					// Create the JCategoryNode
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);
	
					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id))
					{
						// Compute relationship between node and its parent
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}
	
					if (!isset($this->_nodes[$result->parent_id]))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}
	
					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
			}
		}
		else
		{
			$this->_nodes[$id] = null;
		}
	
	}
}
