<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Category Form
 */
class JFormFieldCategoryEdit extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var		string
	 */
	public $type = 'CategoryEdit';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';
	
		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
	
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
	
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
	
		// Get the field options.
		$options = (array) $this->getOptions();
				
		if ($this->element['editform'] == true) {
			$currentid = JFactory::getApplication()->input->getInt('id');
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->select('DISTINCT catid');
			$query->from('#__jem_cats_event_relations');
			$query->where('itemid = '. $db->quote($currentid));
			
			$db->setQuery($query);
			$selectedcats = $db->loadColumn();
			
			$value = $selectedcats;
			
		} else {
			$value = $this->value;
			
		}
			
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true'){
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
		} else {
			// Create a regular list.
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $value, $this->id);
		}
		
			
		return implode($html);
	}
	
	/**
	 * Method to get a list of categories that respects access controls and can be used for
	 * either category assignment or parent category assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$published = $this->element['published']? $this->element['published'] : array(0,1);
		$name = (string) $this->element['name'];

		// Let's get the id for the current item, either category or event item.
		$jinput = JFactory::getApplication()->input;
		// Load the category options for a given extension.

		// For categories the old category is the category id or 0 for new category.
		if ($this->element['parent'])
		{
			$oldCat = $jinput->get('id', 0);
			$oldParent = $this->form->getValue($name, 0);
			//$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $jinput->get('extension','com_content');
		}
		else
		// For items the old category is the category they are in when opened or 0 if new.
		{
			$thisItem = $jinput->get('id',0);
			$oldCat = $this->form->getValue($name, 0);
			//$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $jinput->get('option','com_content');
		}

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id AS value, a.catname AS text, a.level, a.published');
		$query->from('#__jem_categories AS a');
		$query->join('LEFT', $db->quoteName('#__jem_categories').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// Filter by the extension type
		//if ($this->element['parent'] == true)
		//{
		//	$query->where('(a.parent_id = 0)');
		//}
		// If parent isn't explicitly stated but we are in com_categories assume we want parents
		if ($oldCat != 0 && ($this->element['parent'] == true ))
		{
		// Prevent parenting to children of this item.
		// To rearrange parents and children move the children up, not the parents down.
			$query->join('LEFT', $db->quoteName('#__jem_categories').' AS p ON p.id = '.(int) $oldCat);
			$query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

			$rowQuery	= $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.catname AS text, a.level, a.parent_id');
			$rowQuery->from('#__jem_categories AS a');
			$rowQuery->where('a.id = ' . (int) $oldCat);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}
		// Filter on the published state

		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif (is_array($published))
		{
			JArrayHelper::toInteger($published);
			$query->where('a.published IN (' . implode(',', $published) . ')');
		}
		
		if ($this->element['removeroot'] == true) {
			$query->where('a.catname NOT LIKE "root"');
		}

		$query->group('a.id, a.catname, a.level, a.lft, a.rgt, a.parent_id, a.published');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage);
		}


			// Pad the option text with spaces using depth level as a multiplier.
			for ($i = 0, $n = count($options); $i < $n; $i++)
			{
				// remove root
				if ($this->element['removeroot'] == true)
				{
					if ($options[$i]->level == 0)
					{
						unset($options[$i]);
						continue;
					}
					
					$options[$i]->level = $options[$i]->level-1;
				}
				
				if ($options[$i]->published == 1)
				{
					$options[$i]->text = str_repeat('- ', $options[$i]->level). $options[$i]->text ;
				}
				else
				{
					$options[$i]->text = str_repeat('- ', $options[$i]->level). '[' .$options[$i]->text . ']';
				}
				
			}


		// Get the current user object.
		$user = JFactory::getUser();

		// For new items we want a list of categories you are allowed to create in.
		if ($oldCat == 0)
		{
			foreach ($options as $i => $option)
			{
				// To take save or create in a category you need to have create rights for that category
				// unless the item is already in that category.
				// Unset the option if the user isn't authorised for it. In this field assets are always categories.
				if ($user->authorise('core.create', 'com_jem' . '.category.' . $option->value) != true )
				{
					unset($options[$i]);
				}
			}
		}
		// If you have an existing category id things are more complex.
		else
		{
			// If you are only allowed to edit in this category but not edit.state, you should not get any
			// option to change the category parent for a category or the category for a content item,
			// but you should be able to save in that category.
			foreach ($options as $i => $option)
			{
			if ($user->authorise('core.edit.state', 'com_jem' . '.category.' . $oldCat) != true && !isset($oldParent))
			{
				if ($option->value != $oldCat  )
				{
					unset($options[$i]);
				}
			}
			if ($user->authorise('core.edit.state', 'com_jem' . '.category.' . $oldCat) != true
				&& (isset($oldParent)) && $option->value != $oldParent)
			{
					unset($options[$i]);
			}

			// However, if you can edit.state you can also move this to another category for which you have
			// create permission and you should also still be able to save in the current category.
				if (($user->authorise('core.create', 'com_jem' . '.category.' . $option->value) != true)
					&& ($option->value != $oldCat && !isset($oldParent)))
				{
					{
						unset($options[$i]);
					}
				}
				if (($user->authorise('core.create', 'com_jem' . '.category.' . $option->value) != true)
					&& (isset($oldParent)) && $option->value != $oldParent)
				{
					{
						unset($options[$i]);
					}
				}
			}
		}
		if (($this->element['parent'] == true)
			&& (isset($row) && !isset($options[0])) && isset($this->element['show_root']))
			{
				if ($row->parent_id == '1') {
					$parent = new stdClass();
					$parent->text = JText::_('JGLOBAL_ROOT_PARENT');
					array_unshift($options, $parent);
				}
				array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
			}



		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}