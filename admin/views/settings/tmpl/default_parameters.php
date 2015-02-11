<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$group = 'globalattribs';
?>
<div class="row-fluid">
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_GLOBAL_PARAMETERS'); ?></legend>

	<div class="span6">
	<?php foreach ($this->form->getFieldset('globalparam') as $field) : ?>
		<div class="control-group">	
			<div class="control-label"><?php echo $field->label; ?></div>
			<div class="controls"><?php echo $field->input; ?></div>
		</div>
	<?php endforeach; ?>
	</div>

	<div class="span6">
	<?php foreach ($this->form->getFieldset('globalparam2') as $field): ?>
		<div class="control-group">		
			<div class="control-label"><?php echo $field->label; ?></div>
			<div class="controls"><?php echo $field->input; ?></div>
		</div>
	<?php endforeach; ?>
	</div>
</fieldset>
</div>
