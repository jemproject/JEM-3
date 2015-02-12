<?php
/**
 * @package JEM
 * @subpackage JEM Content Plugin
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * JEM Content Plugin
 */
class plgContentJem extends JPlugin
{
    /**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 *
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @param	string	The context for the content passed to the plugin.
	 * @param	object	The data relating to the content that was deleted.
	 */
	public function onContentAfterDelete($context, $data)
	{
		// Skip plugin if we are deleting something other than events
		if (($context != 'com_jem.event') || empty($data->id)) {
			return;
		}

		return;
	}
}
