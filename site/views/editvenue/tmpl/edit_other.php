<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>
<div class="editform_content">
	<!-- CUSTOMFIELDS -->
	<fieldset class="form-horizontal">
	<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_EDITVENUE_CUSTOMFIELDS'); ?></span></legend>
			<?php foreach($this->form->getFieldset('custom') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>

	<!-- IMAGE -->
	<fieldset class="form-horizontal">
	<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_IMAGE'); ?></span></legend>
	<?php
	if (JFactory::getUser()->authorise('core.manage', 'com_jem')) {
		echo $this->form->renderField('locimage');
	}
	?>
		<?php
		if ($this->item->locimage) :
			echo JEMOutput::flyer($this->item, $this->limage, 'venue');
		endif;
		?>
	</fieldset>
</div>