<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Define slides options
$slidesOptions = array(
);
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php
			echo $this->form->getField('description')->save();
			?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
		
<form action="<?php echo JRoute::_('index.php?option=com_jem&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
<div class="form-horizontal">	
	<div class="span12">
	
<!-- Tabs -->	
	<div class="span8">
	<?php echo JHtml::_('bootstrap.startTabSet', 'category', array('active' => 'tab1', 'useCookie' => true)); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'category', 'tab1', JText::_('COM_JEM_CATEGORY_FIELDSET_DETAILS', true)); ?>
	
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('COM_JEM_CATEGORY_FIELDSET_DETAILS');?></legend>
			
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('catname'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('catname'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('extension'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('extension'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('parent_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('parent_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('color'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('color'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-label">
				<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
			</div>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet');?>

		</div><div class="span4">	

		
<!--  SLIDERS -->
	<?php echo JHtml::_('bootstrap.startAccordion', 'categories-sliders-'.$this->item->id, $slidesOptions); ?>		
		
		
<!-- PUBLISHING -->		
	<?php echo JHtml::_('bootstrap.addSlide', 'categories-sliders-'.$this->item->id, JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'slide1'); ?>
			<?php echo $this->loadTemplate('options'); ?>
	<?php echo JHtml::_('bootstrap.endSlide'); ?>
			
			
	<?php echo JHtml::_('bootstrap.addSlide', 'categories-sliders-'.$this->item->id, JText::_('COM_JEM_CATEGORY_FIELDSET_EMAIL'), 'slide2'); ?>
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
				</div>
			</fieldset>
	<?php echo JHtml::_('bootstrap.endSlide'); ?>	

<!-- GROUP -->
	<?php echo JHtml::_('bootstrap.addSlide', 'categories-sliders-'.$this->item->id, JText::_('COM_JEM_GROUP'), 'slide3'); ?>
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="control-label"><label for="groups"> <?php echo JText::_('COM_JEM_GROUP').':'; ?></label></div>
					<div class="controls"><?php echo $this->Lists['groups']; ?></div>
				</div>
			</fieldset>
	<?php echo JHtml::_('bootstrap.endSlide'); ?>

<!-- IMAGE -->
	<?php echo JHtml::_('bootstrap.addSlide', 'categories-sliders-'.$this->item->id, JText::_('COM_JEM_IMAGE'), 'slide4'); ?>
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('image'); ?></div>
			</div>
		</fieldset>
	<?php echo JHtml::_('bootstrap.endSlide'); ?>

<!-- META -->
	<?php echo JHtml::_('bootstrap.addSlide', 'categories-sliders-'.$this->item->id, JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'slide5'); ?>
		<fieldset class="form-vertical">
			<?php echo $this->loadTemplate('metadata'); ?>
		</fieldset>

		<?php  $fieldSets = $this->form->getFieldsets('attribs'); ?>
		<?php foreach ($fieldSets as $name => $fieldSet) : ?>
			<?php $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_JEM_'.$name.'_FIELDSET_LABEL'; ?>
			<?php if ($name != 'editorConfig' && $name != 'basic-limited') : ?>
				<?php echo JHtml::_('sliders.panel', JText::_($label), $name.'-options'); ?>
				<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
					<p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
				<?php endif; ?>
				<fieldset class="form-horizontal">
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label; ?></div>
							<div class="controls"><?php echo $field->input; ?></div>
						</div>
					<?php endforeach; ?>
				</fieldset>
			<?php endif ?>
		<?php endforeach; ?>
	<?php echo JHtml::_('bootstrap.endSlide'); ?>
	<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	
	
	
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	
	</div>
			</div>
	</div>	
</form>