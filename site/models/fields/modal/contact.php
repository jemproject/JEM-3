<?php
/**
 * @version 3.0.6
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;



/**
 * Contact select
 */
class JFormFieldModal_Contact extends JFormField
{
	/**
	 * field type
	 * @var string
	 */
	protected $type = 'Modal_Contact';


	/**
	 * Method to get the field input markup
	 */
	protected function getInput()
	{

		$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

		// Load modal behavior
		JHtml::_('behavior.modal', 'a.flyermodal');

		// Build the script
		$script = array();
		$script[] = '    function jSelectContact_'.$this->id.'(id, name, object) {';
		$script[] = '        document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '        document.getElementById("'.$this->id.'_name").value = name;';
		$script[] = '		jQuery("#'.$this->id.'_clear").removeClass("hidden");';
		$script[] = '        SqueezeBox.close();';
		$script[] = '    }';


		// Clear button script
		static $scriptClear;

		if ($allowClear && !$scriptClear)
		{
			$scriptClear = true;

			$script[] = '	function jClear(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "'.htmlspecialchars(JText::_('COM_JEM_SELECT', true), ENT_COMPAT, 'UTF-8').'";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}

		// Add to document head
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display
		$html = array();
		$link = 'index.php?option=com_jem&amp;view=editevent&amp;layout=choosecontact&amp;tmpl=component&amp;function=jSelectContact_'.$this->id;


		if ((int) $this->value > 0)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('name');
			$query->from('#__contact_details');
			$query->where(array('id='.(int)$this->value));
			$db->setQuery($query);

			try
			{
				$contact = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}

		}

		if (empty($contact)) {
			$contact = JText::_('COM_JEM_SELECT_CONTACT');
		}
		$contact = htmlspecialchars($contact, ENT_QUOTES, 'UTF-8');

		// The active contact id field
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}


		// The current contact input field
		$html[] = '<span class="input-append">';
		$html[] = '  <input type="text" class="inputbox input-medium" id="'.$this->id.'_name" value="'.$contact.'" disabled="disabled" size="35" />';
		$html[] = '<a class="flyermodal btn" href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('COM_JEM_SELECT').'</a>';
		$html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? '' : ' hidden').'" onclick="return jClear(\''.$this->id.'\')"><span class="icon-remove"></span> ' . JText::_('COM_JEM_CLEAR') . '</button>';
		$html[] = '</span>';

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}
?>