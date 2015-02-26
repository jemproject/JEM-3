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
<!-- CUSTOM FIELDS -->
	<fieldset class="form-horizontal">
		<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_EVENT_CUSTOMFIELDS_LEGEND') ?></span></legend>
			<?php foreach($this->form->getFieldset('custom') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>

	<!-- REGISTRATION -->
	<fieldset class="form-horizontal">
		<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_EVENT_REGISTRATION_LEGEND') ?></span></legend>
		<?php
			echo $this->form->renderField('registra');
			echo $this->form->renderField('unregistra');
			echo $this->form->renderField('maxplaces');

		?>
			<div class="control-group">
				<div class="control-label"><label><?php echo JText::_('COM_JEM_BOOKED_PLACES').':';?></label></div>
				<div class="controls"><input id="event-booked" type="text"  disabled="disabled" readonly="readonly" value="<?php echo $this->item->booked; ?>"  /></div>
			</div>

			<?php if ($this->item->maxplaces): ?>
			<div class="control-group">
				<div class="control-label"><label><?php echo JText::_('COM_JEM_AVAILABLE_PLACES').':';?></label></div>
				<div class="controls"><input id="event-available" type="text"  disabled="disabled" readonly="readonly" value="<?php echo ($this->item->maxplaces-$this->item->booked); ?>" /></div>
			</div>
			<?php endif; ?>

			<?php echo $this->form->renderField('waitinglist');?>
	</fieldset>

	<!-- IMAGE -->
	<fieldset class="form-horizontal">
	<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_IMAGE'); ?></span></legend>

		<?php
		if (JFactory::getUser()->authorise('core.manage', 'com_jem')) {
		?>
		<div class="control-group ">
			<div class="control-label"><?php echo $this->form->getLabel('datimage'); ?></div>
			<div class="controls">

			<div class="input-append">
			<?php echo $this->form->getInput('datimage'); ?>
			</div>
			</div>
		</div>

		<?php } else { ?>

		<div class="control-group ">
			<div class="control-label"><label for="userfile">
				<?php echo JText::_('COM_JEM_IMAGE'); ?>
				<small class="editlinktip hasTooltip" title="<?php echo JText::_('COM_JEM_MAX_IMAGE_FILE_SIZE').' '.$this->jemsettings->sizelimit.' kb'; ?>">
					<?php echo $this->infoimage; ?>
				</small>
			</label></div>

			<div class="controls"><input class="inputbox <?php echo $this->jemsettings->imageenabled == 2 ? 'required' : ''; ?>" name="userfile" id="userfile" type="file" />
			<button type="button" class="btn" onclick="document.getElementById('userfile').value = ''"><?php echo JText::_('JSEARCH_FILTER_CLEAR') ?></button>
				<?php
				if ($this->item->datimage) :
					echo JHtml::image('media/com_jem/images/publish_r.png', null, array('class' => 'btn','id' => 'userfile-remove', 'data-id' => $this->item->id, 'data-type' => 'events', 'title' => JText::_('COM_JEM_REMOVE_IMAGE')));
				endif;
				?>
			</div>
		</div>

		<input type="hidden" name="removeimage" id="removeimage" value="0" />

		<?php } ?>


		<?php
		# image output
		if ($this->item->datimage) :
		?>
		<div id="hide_image" class="edit_imageflyer center">
		<?php
			echo JemOutput::flyer( $this->item, $this->dimage, 'event','hideimage');
		?>
		</div>
		<?php
		endif;
		?>

	</fieldset>

<!-- Recurrence -->
<?php if (!($this->item->recurrence_groupcheck)) { ?>
		<fieldset class="form-horizontal">
		<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_EDITEVENT_FIELD_RECURRENCE'); ?></span></legend>
			<?php
			echo $this->form->renderField('recurrence_freq');
			echo $this->form->renderField('recurrence_interval');
			echo $this->form->renderField('recurrence_count');
			echo $this->form->renderField('recurrence_weekday');
			echo $this->form->renderField('recurrence_exdates');
			echo $this->form->renderField('recurrence_until');
			?>

			<!-- Check if the're holidays -->
			<?php if ($this->item->recurrence_country_holidays) { ?>
			<div class="control-group">
				<div class="control-label"><label><?php //echo 'Exclude Holiday(s)';?></label></div>
				<div class="controls"><?php // echo JemHelper::getHolidayOptions($this->item->recurrence_country_holidays); ?></div>
			</div>
			<?php } ?>
		</fieldset>
<?php } ?>
</div>