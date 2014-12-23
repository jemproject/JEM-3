<?php
/**
 * @version 3.0.5
 * @package JEM
 * @subpackage JEM editors-xtd plugin (event)
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Editor Event buton
 */
class PlgButtonEvent extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Display the button
	 *
	 * @param   string  $name  The name of the button to add
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	public function onDisplay($name)
	{
		
		$app = JFactory::getApplication();
		
		if (!$app->isSite())
		{
			return false;
		}
		
		
		/*
		 * Javascript to insert the link
		 * View element calls jSelectEvent when an event is clicked
		 * jSelectEvent creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "
		function jSelectEvent(id, title, object, link, lang)
		{
			var hreflang = '';
			if (lang !== '')
			{
				var hreflang = ' hreflang = \"' + lang + '\"';
			}
			var tag = '<a' + hreflang + ' href=\"' + link + '\">' + title + '</a>';
			jInsertEditorText(tag, '" . $name . "');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		/*
		 * Use the built-in element view to select the event.
		 * Currently uses blank class.
		 */
		$app = JFactory::getApplication();
		
		if ($app->isSite())
		{
			$link = 'index.php?option=com_jem&amp;view=eventslist&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		} else {
			$link = 'index.php?option=com_jem&amp;view=events&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		}
		
		$button = new JObject;
		$button->modal = true;
		$button->class = 'btn';
		$button->link = $link;
		$button->text = JText::_('PLG_EVENT_BUTTON_EVENT');
		$button->name = 'calendar';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
}
