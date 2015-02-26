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
 * Field: Venueoptions
 */
class JFormFieldEventoptions extends JFormFieldList
{
	/**
	 * An event list
	 */
	public $type = 'Eventoptions';

	/**
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$published = $this->element['published']? $this->element['published'] : array(0,1);
		$name = (string) $this->element['name'];

		// Let's get the id for the current item
		$jinput = JFactory::getApplication()->input;

		// Create SQL
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.published');
		$query->from('#__jem_events AS a');

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

		$query->group('a.id');
		$query->order('a.title');

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
				if ($options[$i]->published == 1)
				{

				}
				else
				{

				}
			}

		// Get the current user object.
		$user = JFactory::getUser();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
