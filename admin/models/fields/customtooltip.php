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
 * Field: Customtooltip
 */
class JFormFieldCustomtooltip extends JFormField
{
	/**
	 * The form field type.
	 */
	protected $type = 'Customtooltip';

	

	/**
	 * Method to get the field input markup.
	 */
	protected function getInput()
	{
		// Translate placeholder text
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		// Initialize some field attributes.
		$size         = !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$maxLength    = !empty($this->maxLength) ? ' maxlength="' . $this->maxLength . '"' : '';
		$class        = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$readonly     = $this->readonly ? ' readonly' : '';
		$disabled     = $this->disabled ? ' disabled' : '';
		$required     = $this->required ? ' required aria-required="true"' : '';
		$hint         = $hint ? ' placeholder="' . $hint . '"' : '';
		$pattern      = !empty($this->pattern) ? ' pattern="' . $this->pattern . '"' : '';
		$inputmode    = !empty($this->inputmode) ? ' inputmode="' . $this->inputmode . '"' : '';
		$dirname      = !empty($this->dirname) ? ' dirname="' . $this->dirname . '"' : '';
		
		$tooltiplabel	= JText::_($this->getAttribute('tooltiplabel'));
		$tooltipdesc	= JText::_($this->getAttribute('tooltipdescription'));
		$tooltiplink    = JText::_($this->getAttribute('tooltiplink'));

		// Initialize JavaScript field attributes.
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		$tip = JHtml::tooltipText($tooltiplabel, $tooltipdesc, 0);
		
		if ($tooltiplink) {
			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['target']   = '_blank';
			$attribs['class'] = 'hasTooltip';
			$image = JHtml::_('image', 'system/tooltip.png', null, NULL, true);
			
			$output = ' '.JHtml::_('link', $tooltiplink, $image, $attribs);
			
		} else {
			
			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'hasTooltip';
			
			$output	= JHtml::_('image', 'system/tooltip.png', $tooltiplabel, $attribs, true);
		}
		
		$html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . $dirname . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly
			. $hint . $onchange . $maxLength . $required . $inputmode . $pattern . ' />';
		$html[] = $output;

		return implode($html);
	}

}