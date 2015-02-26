<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die; 
?>


	<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_user_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_user_id'); ?></div>
			</div>
			<?php if (intval($this->item->created_time)) : ?>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_time'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_time'); ?></div>
			</div>
			<?php endif; ?>

			<?php if ($this->item->modified_user_id) : ?>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('modified_user_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('modified_user_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('modified_time'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('modified_time'); ?></div>
			</div>
			<?php endif; ?>
	</fieldset>

<?php $fieldSets = $this->form->getFieldsets('params');

foreach ($fieldSets as $name => $fieldSet) :
	$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_CATEGORIES_'.$name.'_FIELDSET_LABEL';
	echo JHtml::_('sliders.panel', JText::_($label), $name.'-options');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
	?>
	<fieldset class="form-vertical">
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label; ?></div>
					<div class="controls"><?php echo $field->input; ?></div>
				</div>
			<?php endforeach; ?>

			<?php if ($name=='basic'):?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('note'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('note'); ?></div>
				</div>
			<?php endif;?>
	</fieldset>
<?php endforeach; ?>