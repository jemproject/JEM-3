<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * MathQuiz
 */
class JFormRuleMathquiz extends JFormRule
{
	/**
	 * Method to test if the answer is correct.
	 * 
	 * 
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   JForm             $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 * 
	 */
	public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null)
	{
		$jinput 	= JFactory::getApplication()->input;
		$data  		= $jinput->post->get('jform', array(), 'array');
		
		$question	= $data['mathquiz'];
		$answer		= $data['mathquiz_answer'];
		
		// Test the value.
		if (!($question == $answer))
		{
			return false;
		}

		return true;
	}
}
