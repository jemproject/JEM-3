<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_( 'COM_JEM_DISPLAY_SETTINGS' ); ?></legend>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('showdetails'); ?></div> 
		<div class="controls"><?php echo $this->form->getInput('showdetails'); ?></div>
	</div>
	
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('formatShortDate'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('formatShortDate'); ?>
			<span class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_PHP_DATE_MANUAL').'::'.JText::_('COM_JEM_PHP_DATE_MANUAL_DESC');echo JHtml::tooltipText($tooltip,'',true);?>"> 
				<a href="http://php.net/manual/en/function.date.php" target="_blank"><?php echo $this->WarningIcon(); ?></a>
			</span>
		</div>
	</div>
	
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('formatdate'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('formatdate'); ?>
			<span class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_PHP_DATE_MANUAL').'::'.JText::_('COM_JEM_PHP_DATE_MANUAL_DESC');echo JHtml::tooltipText($tooltip,'',true);?>">
				<a href="http://php.net/manual/en/function.date.php" target="_blank"><?php echo $this->WarningIcon(); ?></a>
			</span>
		</div>
	</div>
				
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('formattime'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('formattime'); ?>
			<span class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_TIME_STRFTIME').'::'.JText::_('COM_JEM_TIME_STRFTIME_DESC');echo JHtml::tooltipText($tooltip,'',true); ?>">
				<a href="http://www.php.net/strftime" target="_blank"><?php echo $this->WarningIcon(); ?></a>
			</span>
		</div>
	</div>
			
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('timename'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('timename'); ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('storeip'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('storeip'); ?></div>
	</div>	
</fieldset>

<br />