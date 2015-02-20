<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$group = 'veditevent';
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_VEDITEVENT'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('global_show_ownedvenuesonly', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_show_ownedvenuesonly', $group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_attachmentstab', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_attachmentstab', $group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_othertab', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_othertab', $group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_featured', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_featured', $group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_published', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_published', $group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('registering', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('registering', $group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('unregistering', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('unregistering', $group); ?></div>
		</div>
</fieldset>
