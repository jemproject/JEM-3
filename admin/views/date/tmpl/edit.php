<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @todo: move js to a file
 */
defined('_JEXEC') or die;

$options = array(
		'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
		'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
		'opacityTransition' => true,
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
);


// Define slides options
$slidesOptions = array(
		"active" => "date-slide1" // It is the ID of the active tab.
);

JHtml::_('behavior.modal', 'a.flyermodal');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Create shortcut to parameters.
$params = $this->state->get('params');
$params = $params->toArray();
$group = 'attribs';
?>


<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{	
		if (task == 'date.cancel' || document.formvalidator.isValid(document.id('date-form'))) {
			Joomla.submitform(task, document.getElementById('date-form'));
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_jem&layout=edit&id='.(int) $this->item->id); ?>"
	class="form-validate" method="post" name="adminForm" id="date-form" enctype="multipart/form-data">


<div class="span12">
	<div class="span8">
		<fieldset class="form-horizontal">
			<legend>
				<?php echo JText::_('COM_JEM_DATE_SINGLEDATE_LEGEND'); ?>
			</legend>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('date');?></div>
				<div class="controls"><?php echo $this->form->getInput('date'); ?></div>
			</div>
		</fieldset>

		
		<fieldset class="form-horizontal">
			<legend>
				<?php echo JText::_('COM_JEM_DATE_MULTIDATE_LEGEND'); ?>
			</legend>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('date_startdate_range');?></div>
				<div class="controls"><?php echo $this->form->getInput('date_startdate_range'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('date_enddate_range');?></div>
				<div class="controls"><?php echo $this->form->getInput('date_enddate_range'); ?></div>
			</div>
		</fieldset>
		
		<fieldset class="form-horizontal">
			<legend>
				<?php echo JText::_('COM_JEM_DATE_OTHER_LEGEND'); ?>
			</legend>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('holiday');?></div>
				<div class="controls"><?php echo $this->form->getInput('holiday'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('date_name');?></div>
				<div class="controls"><?php echo $this->form->getInput('date_name'); ?></div>
			</div>
		</fieldset>
	</div>


<div class="span4">	
<!--  start of sliders -->
	<?php echo JHtml::_('bootstrap.startAccordion', 'slide-date', $slidesOptions); ?>
	<?php echo JHtml::_('bootstrap.addSlide', 'slide-date', JText::_('COM_JEM_FIELDSET_PUBLISHING'), 'date-slide1'); ?>
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('enabled'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('enabled'); ?></div>
			</div>
		</fieldset>	
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		
		
		<?php echo JHtml::_('bootstrap.addSlide', 'slide-date', JText::_('COM_JEM_DATE_SLIDER_CALENDAR'), 'date-slide2'); ?>
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('calendar_name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('calendar_name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('calendaritemids'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('calendaritemids'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('calendar'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('calendar'); ?></div>
			</div>
		</fieldset>	
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	
	

		
	<input type="hidden" name="task" value="" />
				<!--  END RIGHT DIV -->
				<?php echo JHtml::_('form.token'); ?>
				</div>
		<div class="clr"></div>
</form>