<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

/**
 * Mathquiz
 */
class JFormFieldMathquiz extends JFormField
{
	private $operand1, $operand2;

	/**
	 * The form field type.
	 */
	protected $type = 'MathQuiz';

	/**
	 * Method to get the field input markup.
	 */
	protected function getInput()
	{
		$this->generateNumbers();

		$label = '<span id="mathquiz">'.$this->operand1 . ' + ' . $this->operand2.'</span>';
		//$math = '<input type="text" name="' . $this->name . '" value="" id="' . $this->name . '"></input>';

		$answer = '<input type="hidden" name="' . $this->name . '-answer" value="' . ( $this->operand1 + $this->operand2 ) . '"></input>';
		$string = $label . "\n" . $answer;

		return $string;
	}


	public function generateNumbers()
	{
		$this->operand1 = mt_rand( 1, 9 );
		$this->operand2 = mt_rand( 1, 9 );
	}
}
