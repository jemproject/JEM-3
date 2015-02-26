<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * JHtml Class
 */
abstract class JHtmlJemHtml
{
	/**
	 * @param int $value state value
	 * @param int $i
	 */
	static function featured($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');
		
		
		// Array of image, task, title, action
		$states = array(
				0 => array(
						'unfeatured',
						'events.featured',
						'COM_JEM_EVENTS_UNFEATURED',
						'COM_JEM_EVENTS_TOGGLE_TO_FEATURE'
				),
				1 => array(
						'featured',
						'events.unfeatured',
						'COM_JEM_EVENTS_FEATURED',
						'COM_JEM_EVENTS_TOGGLE_TO_UNFEATURE'
				)
		);
		$state = JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		if ($canChange)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}

		return $html;
	}
	
	/**
	 *
	 * @param int $value state value
	 * @param int $i
	 */
	static function dateenabled($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states = array(
				0 => array(
						'unpublish.png',
						'dates.setstatusdate',
						'COM_JEM_EVENTS_UNFEATURED',
						'COM_JEM_EVENTS_TOGGLE_TO_FEATURE'
				),
				1 => array(
						'publish.png',
						'dates.disabledate',
						'COM_JEM_EVENTS_FEATURED',
						'COM_JEM_EVENTS_TOGGLE_TO_UNFEATURE'
				)
		);
		$state = JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html = JHtml::_('image', 'com_jem/' . $state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . JText::_($state[3]) . '">' . $html . '</a>';
		}

		return $html;
	}
}
