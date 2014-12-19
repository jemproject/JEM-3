<?php
/**
 * @version 3.0.4
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * 
 * Plugin based on the Joomla! update notification plugin
 */
defined('_JEXEC') or die;

/**
 * JEM Quickicon-Plugin
 */
class plgQuickiconJemquickicon extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onGetIcons($context)
	{
		
		if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_installer'))
		{
			return;
		}

		JHtml::_('jquery.framework');
		
		$text = $this->params->get('displayedtext');
		if(empty($text)) $text = JText::_('PLG_QUICKICON_JEMQUICKICON_MAIN');

		return array(array(
			'link' => 'index.php?option=com_jem',
			'image' => 'calendar',
			'icon' => JURI::base().'../media/com_jem/images/icon-48-home.png',
			'text' => $text,
			'id' => 'plg_jem_quickicon'
		));
	}
}
