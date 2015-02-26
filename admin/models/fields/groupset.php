<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Color Form Field
 */
class JFormFieldGroupset extends JFormFieldList
{
	/**
	 * The form field type.
	 */
	protected $type = 'Groupset';

	/**
	 *
	 */
	public function getOptions()
	{
		$options = JemHelper::getGroupset();

		return array_merge(parent::getOptions(), $options);
	}
}
