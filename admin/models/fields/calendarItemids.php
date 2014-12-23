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

jimport('joomla.html.html');
jimport('joomla.form.formfield');



/**
 * CatOptions Field class.
 *
 *
 */
class JFormFieldCalendarItemids extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 */
	protected $type = 'CalendarItemids';

	public function getInput()
	{
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		//$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		
		$currentid = JFactory::getApplication()->input->getInt('id');
		
		if ($currentid) { 
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
		
			$query->select('calendar_linked');
			$query->from('#__jem_dates');
			$query->where('id = '.$currentid);
		
			$db->setQuery($query);
			$currentValue = $db->loadResult();	
			$currentValue = json_decode($currentValue);
		} else {
			$currentValue = false;
		}
	
		
		# Retrieve Holidays
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('id');
		$query->from('#__menu');
		$query->where('link ='.$db->Quote('index.php?option=com_jem&view=calendar'));
		
		$db->setQuery($query);
		$holidays = $db->loadObjectList();
		
		$options = array();
		foreach ($holidays as $holiday) {
			//$name = explode(',', $country['name']);
			$options[] = JHtml::_('select.option', $holiday->id, JText::_($holiday->id));
		}
		
		//$options2 = array();
		//$options2 = array_merge($options,$options2);
		//array_unshift($options2, JHtml::_('select.option', '0', JText::_('COM_JEM_SELECT_HOLIDAY')));
				
		//$html[] = JHtml::_('select.genericlist', $countryoptions, 'countryactivated', null, 'value', 'text', $currentValue);
		$html[] = JHtml::_('select.genericlist', $options, 'calendarids[]', 'class="inputbox" size="6" multiple="true"', 'value', 'text', $currentValue);
		
		return implode("\n", $html);		
	}
	
}