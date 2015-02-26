<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Based upon: Joomla-Timezone field
 */
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('groupedlist');

/**
 * Timezone field
 */
class JFormFieldTimezone extends JFormFieldGroupedList
{
	/**
	 * The form field type.
	 */
	protected $type = 'Timezone';

	/**
	 * The list of available timezone groups to use.
	 */
	protected static $zones = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');

	/**
	 * The keyField of timezone field.
	 */
	protected $keyField;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'keyField':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'keyField':
				$this->keyField = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->keyField = (string) $this->element['key_field'];
		}

		return $return;
	}

	/**
	 * Method to get the time zone field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 */
	protected function getGroups()
	{
		$settings	= JemHelper::globalattribs();
		$globalTz 	= $settings->get('global_timezone');
		$groups = array();

		$keyField = !empty($this->keyField) ? $this->keyField : 'id';
		$keyValue = $this->form->getValue($keyField);
		$view 	= $this->element['view'];

		// If the timezone is not set use the server setting.
		if (strlen($this->value) == 0 && empty($keyValue))
		{
			# we didn't select A timezone so we'll take a look at the server setting.
			# are we in settings-view?
			if ($view == 'venue') {

				# check if there is a setting filled
				if ($globalTz) {
					$this->value = $globalTz;
				} else {
					# there is no global value so check the the server setting
					$serverTz = JFactory::getConfig()->get('offset');

					# UTC is seen as Abidjan and that's not something we want
					if ($serverTz == 'UTC') {
						$serverTz = 'Europe/Berlin'; // for the moment it's been set to this
					}
					$this->value = $serverTz;
				}
			}
		}

		// Get the list of time zones from the server.
		$zones = DateTimeZone::listIdentifiers();

		// Build the group lists.
		foreach ($zones as $zone)
		{
			// Time zones not in a group we will ignore.
			if (strpos($zone, '/') === false)
			{
				continue;
			}

			// Get the group/locale from the timezone.
			list ($group, $locale) = explode('/', $zone, 2);

			// Only use known groups.
			if (in_array($group, self::$zones))
			{
				// Initialize the group if necessary.
				if (!isset($groups[$group]))
				{
					$groups[$group] = array();
				}

				// Only add options where a locale exists.
				if (!empty($locale))
				{
					$groups[$group][$zone] = JHtml::_('select.option', $zone, str_replace('_', ' ', $locale), 'value', 'text', false);
				}
			}
		}

		// Sort the group lists.
		ksort($groups);

		foreach ($groups as &$location)
		{
			sort($location);
		}

		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}
