<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * CatOptions Field class.
 */
class JFormFieldCatOptionsFront extends JFormFieldList
{

	/**
	 * The form field type.
	 */
	protected $type = 'CatOptionsFront';

	protected function getInput()
	{
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= ! empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= ! empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
		
		$frontedit = $this->element['frontedit'];
		
		// To avoid user's confusion, readonly="true" should imply
		// disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1' ||
				 (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
		
		// Get the field options.
		$options = (array) $this->getOptions();
		
		// Selected Categories
		$currentid = JFactory::getApplication()->input->getInt('a_id');
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('DISTINCT catid');
		$query->from('#__jem_cats_event_relations');
		$query->where('itemid = ' . $db->quote($currentid));
		
		$db->setQuery($query);
		$selectedcats = $db->loadColumn();
		
		// Create a read-only list (no name) with a hidden input to store the
		// value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats, $this->id);
		}
		
		return implode($html);
	}

	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$published = $this->element['published'] ? $this->element['published'] : array(0,1);
		$name = (string) $this->element['name'];
		$action = (string) $this->element['action'];
		$frontedit = $this->element['frontedit'];
		$jinput = JFactory::getApplication()->input;
		$db		= JFactory::getDbo();
		$a_id = $jinput->get('a_id',null);
		
		
		// retrieve data
		if ($frontedit)
		{	
			$user = JFactory::getUser();
			$jemsettings = JEMHelper::config();
			$userid = (int) $user->get('id');
			$superuser = JEMUser::superuser();
			$levels = $user->getAuthorisedViewLevels();
			$settings = JemHelper::globalattribs();
			$guestcat = $settings->get('guest_category', '0');
			$jinput = JFactory::getApplication()->input;
			$valguest = JEMUser::validate_guest();
			$name = (string) $this->element['name'];
			$db		= JFactory::getDbo();
			$auth_joomlagr	= $user->getAuthorisedGroups();
		
			$oldCat = 0;
			
			$query = $db->getQuery(true)
			->select('a.id AS value, a.catname AS text, a.level, a.published')
			->from('#__jem_categories AS a')
			->join('LEFT', $db->quoteName('#__jem_categories') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
			
			if (is_numeric($published))
			{
				$query->where('a.published = ' . (int) $published);
			}
			elseif (is_array($published))
			{
				JArrayHelper::toInteger($published);
				$query->where('a.published IN (' . implode(',', $published) . ')');
			}
			
			
			// specific code
			if (!$valguest) {
				
				$validated =false;
				if($superuser) {
					$validated = true;
				}
							
			} else {
				// $specified guest category
				$query->where(array('a.id = '. $guestcat));
			}
			
			$query->group('a.id, a.catname, a.level, a.lft, a.rgt, a.parent_id, a.published')
			->order('a.lft ASC');
				
			$db->setQuery($query);
		}
		
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{		
			if (!$valguest) {
				JError::raiseWarning(500, $e->getMessage);
			}
		}
		
		if ($valguest) {
			// Merge any additional options in the XML definition.
			$options = array_merge(parent::getOptions(), $options);
			return $options;
		}
		
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i ++)
		{
			// remove root
			if ($this->element['removeroot'] == true)
			{
				if ($options[$i]->level == 0)
				{
					unset($options[$i]);
					continue;
				}
				
				$options[$i]->level = $options[$i]->level - 1;
			}
			
			if ($options[$i]->published == 1)
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			}
			else
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . '[' . $options[$i]->text . ']';
			}
		}
		
		$eventid = $this->form->getValue('id', 0);
		
		if (!$validated) {
			if ($eventid) {
				$type = 'editevent';
			} else {
				$type = 'addevent';
			}
			
			// catch the groupnumber of the user+add rights
			$query	= $db->getQuery(true);
			$query->select(array('gr.id'));
			$query->from($db->quoteName('#__jem_groups').' AS gr');
			$query->join('LEFT', '#__jem_groupmembers AS g ON g.group_id = gr.id');
			$query->where(array('g.member = '. (int) $user->get('id'),$db->quoteName('gr.'.$type).' =1','g.member NOT LIKE 0'));
			$db->setQuery($query);
			$groupnumber = $db->loadColumn();
				
			// is the user member of a group with edit rights?
			if ($groupnumber) {
				$query = $db->getQuery(true);
				$query->select('c.id');
				$query->from($db->quoteName('#__jem_categories').' AS c');
				$query->where(array('c.groupid IN (' . implode(',', $groupnumber) . ')'));
				$db->setQuery($query);
				$cats = $db->loadColumn();
			} else {
				$cats = false;
			}
		}
		
		
		// Get the current user object.
		$user = JFactory::getUser();
		
		// For new items we want a list of categories you are allowed to create in.
		if ($eventid == 0 || $eventid == 'null')
		{
			foreach ($options as $i => $option)
			{
				// To save or create in a category you need to have create rights for that category
			
				// check joomla rights
				if ($user->authorise('core.create', 'com_jem.category.' . $option->value) != true)
				{
					// joomla check failed, so check JEM Groups
					if ($cats && !$valguest) {
						if (!in_array($option->value, $cats)) {
							unset($options[$i]);
						}
					}
					
					unset($options[$i]);
				}
			}
		}
		// Editing, more complex.
		else
		{
			
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}
