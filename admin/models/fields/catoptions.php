<?php
/**
 * @version     3.0.3
 * @package     JEM
 * @copyright   Copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright   Copyright (C) 2005-2009 Christoph Lukes
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * CatOptions Field class.
 */
class JFormFieldCatOptions extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 */
	protected $type = 'CatOptions';

	
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
		
		// Selected Categories
		$currentid = JFactory::getApplication()->input->getInt('id');
		$categories = JEMCategories::getCategoriesTree();
		
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query = 'SELECT DISTINCT catid FROM #__jem_cats_event_relations WHERE itemid = '. $db->quote($currentid);
		
		$db->setQuery($query);
		$selectedcats = $db->loadColumn();
		
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats,$this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
			// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats,$this->id);
		}
	
		return implode($html);
	}
	
	
	protected function getOptions() {
		
		$db			= JFactory::getDbo();
		$options	= JEMCategories::getCategoriesTree();
		
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage);
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}