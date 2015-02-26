<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @todo: move js to a file
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');


// Define slides options
$slidesOptions = array();

JHtml::_('behavior.framework');
JHtml::_('behavior.modal', 'a.flyermodal');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');


// Create shortcut to parameters.
$params = $this->state->get('params');
$params = $params->toArray();
?>

<script type="text/javascript">
	window.addEvent('domready', function(){
		checkmaxplaces();

	jQuery('#jform_attribs_event_comunsolution').on( "change", testcomm );
	
	var nrcommhandler = jQuery('#jform_attribs_event_comunsolution').val();

	if (nrcommhandler == 1) {
		common();
	} else {
		commoff();
	}

	starter("<?php echo JText::_('COM_JEM_META_ERROR'); ?>",jQuery("#jform_meta_keywords").val(),jQuery("jform_meta_description").val());

	jQuery('#jform_meta_keywords')
		.focus(function() {
			get_inputbox('jform_meta_keywords');
		})
		.blur(function() {
			change_metatags;
	});


	jQuery('#jform_meta_description')
		.focus(function() {
			get_inputbox('jform_meta_description');
		})
		.blur(function() {
			change_metatags;
	});
	
	});
</script>
<script type="text/javascript">
	function checkmaxplaces()
	{

		jQuery("#jform_maxplaces").on("change", function() {
			if (jQuery('#event-available')) {
				var maxplaces = jQuery('#jform_maxplaces').val();
				var booked = jQuery('#event-booked').val();
				jQuery('#event-available').val(maxplaces-booked);
			}
		});

		jQuery("#event-booked").on("change", function() {
			if (jQuery('#event-available')) {
				var maxplaces = jQuery('#jform_maxplaces').val();
				var booked = jQuery('#event-booked').val();
				jQuery('#event-available').val(maxplaces-booked);
			}
		});

	}
	
	function testcomm()
	{
		var nrcommhandler = jQuery('#jform_attribs_event_comunsolution').val();

		if (nrcommhandler == 1) {
			common();
		} else {
			commoff();
		}
	}

	function common()
	{
		jQuery('#comm1').show();
	}

	function commoff()
	{
		jQuery('#comm1').hide();
	}
</script>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'event.cancel' || document.formvalidator.isValid(document.id('event-form'))) {
			Joomla.submitform(task, document.getElementById('event-form'));
			<?php echo $this->form->getField('articletext')->save(); ?>
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jem&layout=edit&id='.(int) $this->item->id); ?>" class="form-validate" method="post" name="adminForm" id="event-form" enctype="multipart/form-data">
	<div class="form-horizontal">
		<div class="span12">

<!-- recurrence-message, above the tabs -->		
	<?php if ($this->item->recurrence_groupcheck) { ?>
		<fieldset class="form-horizontal alert">
				<p>
				<?php echo nl2br(JText::_('COM_JEM_EVENT_WARN_RECURRENCE_TEXT')); ?>
				</p>
				
				<button class="btn" type="button" value="<?php echo JText::_('COM_JEM_EVENT_RECURRENCE_REMOVEFROMSET');?>" onclick="Joomla.submitbutton('event.removefromset')"><?php echo JText::_('COM_JEM_EVENT_RECURRENCE_REMOVEFROMSET');?></button>
				
		</fieldset>
		<?php } ?>
	
</div>

<div class="span12">
	
<!-- Tabs -->	
	<div class="span8">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JEM_EVENT_INFO_TAB', true)); ?>
	
		<fieldset class="form-horizontal">
			<legend>
				<?php echo empty($this->item->id) ? JText::_('COM_JEM_NEW_EVENT') : JText::sprintf('COM_JEM_EVENT_DETAILS', $this->item->id); ?>
			</legend>
			<?php 
				echo $this->form->renderField('title');
				echo $this->form->renderField('alias');
				echo $this->form->renderField('dates');
				echo $this->form->renderField('enddates');
				echo $this->form->renderField('times');
				echo $this->form->renderField('endtimes');
				echo $this->form->renderField('cats');
			?>
		</fieldset>

		<fieldset class="form-horizontal">
		<?php 
			echo $this->form->renderField('locid'); 
			echo $this->form->renderField('contactid');
			echo $this->form->renderField('published');
			echo $this->form->renderField('featured');
			echo $this->form->renderField('access');
			echo $this->form->renderField('language');
		?>
		</fieldset>
		
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('articletext'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('articletext'); ?></div>
			</div>
		</fieldset>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

<!-- Attachments -->		
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'otherparams', JText::_('COM_JEM_EVENT_ATTACHMENTS_TAB', true)); ?>	
		<?php echo $this->loadTemplate('attachments'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		
	
<!-- Settings -->
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings', JText::_('COM_JEM_EVENT_SETTINGS_TAB', true)); ?>
		<?php echo $this->loadTemplate('settings'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		
	
			<?php echo JHtml::_('bootstrap.endTabSet');?>
</div>
<div class="span4">	

<!--  start of sliders -->
	<?php echo JHtml::_('bootstrap.startAccordion', 'slide', $slidesOptions); ?>
	
	
<!-- Publishing -->
	<?php echo JHtml::_('bootstrap.addSlide', 'slide', JText::_('COM_JEM_FIELDSET_PUBLISHING'), 'event-publishing'); ?>

		<!-- RETRIEVING OF FIELDSET PUBLISHING -->
		<fieldset class="form-vertical">
		<?php 
			echo $this->form->renderField('id');
			echo $this->form->renderField('created_by');
			echo $this->form->renderField('hits');
			echo $this->form->renderField('created');
			echo $this->form->renderField('modified');
			echo $this->form->renderField('version');
		?>
		</fieldset>
<?php echo JHtml::_('bootstrap.endSlide'); ?>
	
	

<!-- custom -->
	<?php echo JHtml::_('bootstrap.addSlide', 'slide', JText::_('COM_JEM_CUSTOMFIELDS'), 'event-custom'); ?>
		<fieldset class="form-vertical">
			<?php foreach($this->form->getFieldset('custom') as $field): ?>
				<div class="control-group">	
					<div class="control-label"><?php echo $field->label; ?></div>
					<div class="controls"><?php echo $field->input; ?></div>
				</div>
			<?php endforeach; ?>
		</fieldset>
	<?php echo JHtml::_('bootstrap.endSlide'); ?>
	
	
<!-- registra -->	
	<?php echo JHtml::_('bootstrap.addSlide', 'slide', JText::_('COM_JEM_REGISTRATION'), 'event-registra'); ?>
		<fieldset class="form-vertical">
		<?php 
			echo $this->form->renderField('registra'); 
			echo $this->form->renderField('unregistra');
			echo $this->form->renderField('maxplaces');
		?>
			<div class="control-group">	
				<div class="control-label"><label><?php echo JText::_ ('COM_JEM_BOOKED_PLACES') . ':';?></label></div>
				<div class="controls"><input id="event-booked" aria-invalid="false" readonly="" class="readonly" type="text"  value="<?php echo $this->item->booked; ?>" /></div>
			</div>
			
			<?php if ($this->item->maxplaces): ?>
			<div class="control-group">	
				<div class="control-label"><label><?php echo JText::_ ('COM_JEM_AVAILABLE_PLACES') . ':';?></label></div>
				<div class="controls"><input id="event-available" aria-invalid="false" readonly="" class="readonly" type="text"  value="<?php echo ($this->item->maxplaces-$this->item->booked); ?>" /></div>
			</div>
			<?php endif; ?>

			<?php echo $this->form->renderField('waitinglist'); ?> 
		</fieldset>
<?php echo JHtml::_('bootstrap.endSlide'); ?>


<!-- Image -->
	<?php echo JHtml::_('bootstrap.addSlide', 'slide', JText::_('COM_JEM_IMAGE'), 'event-image'); ?>
		<fieldset class="form-vertical">
		<?php 
			echo $this->form->renderField('datimage'); 
		?> 
		</fieldset>
	<?php echo JHtml::_('bootstrap.endSlide'); ?>


<!-- Recurrence -->
	
<?php if (!($this->item->recurrence_groupcheck)) { ?>
	<?php echo JHtml::_('bootstrap.addSlide', 'slide', JText::_('COM_JEM_EVENT_FIELDSET_RECURRING_EVENTS'), 'event-recurrence'); ?>
		<fieldset class="form-vertical">
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
		
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php } ?>
		
		
<!-- Meta -->
	<?php echo JHtml::_('bootstrap.addSlide', 'slide', JText::_('COM_JEM_METADATA_INFORMATION'), 'event-meta'); ?>
	
		<fieldset class="form-vertical">
			<p>
				<input class="btn" type="button" onclick="insert_keyword('[title]')" value="<?php echo JText::_('COM_JEM_EVENT_TITLE');	?>" />
				<input class="btn" type="button" onclick="insert_keyword('[a_name]')" value="<?php	echo JText::_('COM_JEM_VENUE');?>" />
				<input class="btn" type="button" onclick="insert_keyword('[categories]')" value="<?php	echo JText::_('COM_JEM_CATEGORIES');?>" />
				<input class="btn" type="button" onclick="insert_keyword('[dates]')" value="<?php echo JText::_('COM_JEM_DATE');?>" />
				<input class="btn" type="button" onclick="insert_keyword('[times]')" value="<?php echo JText::_('COM_JEM_EVENT_TIME');?>" />
				<input class="btn" type="button" onclick="insert_keyword('[enddates]')" value="<?php echo JText::_('COM_JEM_ENDDATE');?>" />
				<input class="btn" type="button" onclick="insert_keyword('[endtimes]')" value="<?php echo JText::_('COM_JEM_END_TIME');?>" />
			</p>
			<?php  echo $this->form->renderField('meta_keywords'); ?>
			<?php  echo $this->form->renderField('meta_description'); ?>
		</fieldset>

		<fieldset class="form-vertical">
		
		<?php foreach($this->form->getGroup('metadata') as $field): ?>
		<div class="control-group">	
			<?php if (!$field->hidden): ?>
				<div class="control-label"><?php echo $field->label; ?></div>
			<?php endif; ?>
			<div class="controls"><?php echo $field->input; ?></div>
		</div>
		<?php endforeach; ?>

		</fieldset>

	<?php echo JHtml::_('bootstrap.endSlide'); ?>
	<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="author_ip" value="<?php echo $this->item->author_ip; ?>" />
	<input type="hidden" name="recurrence_check" value="<?php echo $this->item->recurrence_groupcheck; ?>" />
	<input type="hidden" name="recurrence_group" value="<?php echo $this->item->recurrence_group; ?>" />
	<input type="hidden" name="recurrence_country_holidays" value="<?php echo $this->item->recurrence_country_holidays; ?>" />
				<!--  END RIGHT DIV -->
				<?php echo JHtml::_('form.token'); ?>
			
			</div>
			</div>
	</div>	
</form>