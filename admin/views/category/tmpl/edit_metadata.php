<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

?>

<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('meta_description'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('meta_description'); ?></div>
</div>

<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('meta_keywords'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('meta_keywords'); ?></div>
</div>

	<?php foreach($this->form->getGroup('metadata') as $field): ?>
		<div class="control-group">
		<?php if ($field->hidden) : ?>
			<div class="controls"><?php echo $field->input; ?></div>
		<?php else: ?>
			<div class="control-label"><?php echo $field->label; ?></div>
			<div class="controls"><?php echo $field->input; ?></div>
		<?php endif; ?>
		</div>
	<?php endforeach; ?>
