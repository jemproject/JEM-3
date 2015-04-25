<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 *  component helper.
 *
 * @subpackage	com_jem
 *
 */
class JemHelperBackend
{

	public static $extension = 'com_jem';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 *
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_JEM_SUBMENU_MAIN'),
			'index.php?option=com_jem&view=main',
			$vName == 'main'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JEM_EVENTS'),
			'index.php?option=com_jem&view=events',
			$vName == 'events'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JEM_VENUES'),
			'index.php?option=com_jem&view=venues',
			$vName == 'venues'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JEM_CATEGORIES'),
			'index.php?option=com_jem&view=categories',
			$vName == 'categories'
		);

		JHtmlSidebar::addEntry(
		JText::_('COM_JEM_GROUPS'),
		'index.php?option=com_jem&view=groups',
		$vName == 'groups'
				);

		JHtmlSidebar::addEntry(
		JText::_('COM_JEM_HELP'),
		'index.php?option=com_jem&view=help',
		$vName == 'help'
				);

		if (JFactory::getUser()->authorise('core.manage')) {
			JHtmlSidebar::addEntry(
			JText::_('COM_JEM_SETTINGS_TITLE'),
			'index.php?option=com_jem&view=settings',
			$vName == 'settings'
					);
		}

	}

	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $component  The component name.
	 * @param   string   $section    The access section name.
	 * @param   integer  $id         The item ID.
	 *
	 * @return  JObject
	 */
	public static function getActions($component = '', $section = '', $id = 0)
	{
		// Check for deprecated arguments order
		if (is_int($component) || is_null($component))
		{
			//$result = self::_getActions($component, $section, $id);
			$result = false;
			return $result;
		}
	
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		$path = JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml';
	
		if ($section && $id)
		{
			$assetName = $component . '.' . $section . '.' . (int) $id;
		}
		else
		{
			$assetName = $component;
		}
	
		$actions = JAccess::getActionsFromFile($path, "/access/section[@name='component']/");
	
		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}
	
		return $result;
	}
	

	public static function getCountryOptions()
	{
		$options = array();
		$options = array_merge(JEMHelperCountries::getCountryOptions(),$options);

		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_JEM_SELECT_COUNTRY')));

		return $options;
	}
}
