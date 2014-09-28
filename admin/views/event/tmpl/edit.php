<?php
/**
 * @version 3.0.2
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Create shortcut to parameters.
$params = $this->state->get('params');
$params = $params->toArray();
?>

<script type="text/javascript">
	window.addEvent('domready', function(){
	checkmaxplaces();

	$('jform_attribs_event_comunsolution').addEvent('change', testcomm);

	var commhandler = $("jform_attribs_event_comunsolution");
	var nrcommhandler = commhandler.options[commhandler.selectedIndex].value;

		if (nrcommhandler == 1) {
			common();
		} else {
			commoff();
		}

	});


	function checkmaxplaces()
	{
		$('jform_maxplaces').addEvent('change', function(){
			if ($('event-available')) {
						var val = parseInt($('jform_maxplaces').value);
						var booked = parseInt($('event-booked').value);
						$('event-available').value = (val-booked);
			}
			});

		$('jform_maxplaces').addEvent('keyup', function(){
			if ($('event-available')) {
						var val = parseInt($('jform_maxplaces').value);
						var booked = parseInt($('event-booked').value);
						$('event-available').value = (val-booked);
			}
			});
	}

	
	function testcomm()
	{
		var commhandler = $("jform_attribs_event_comunsolution");
		var nrcommhandler = commhandler.options[commhandler.selectedIndex].value;

		if (nrcommhandler == 1) {
			common();
		} else {
			commoff();
		}
	}

	function common()
	{
		document.getElementById('comm1').style.display = '';
	}

	function commoff()
	{
		document.getElementById('comm1').style.display = 'none';
	}
</script>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'event.cancel' || document.formvalidator.isValid(document.id('event-form'))) {
			Joomla.submitform(task, document.getElementById('event-form'));

			<?php echo $this->form->getField('articletext')->save(); ?>

			$("meta_keywords").value = $keywords;
			$("meta_description").value = $description;
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

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('title');?></div>
				<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('dates'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('dates'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('enddates'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('enddates'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('times'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('times'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('endtimes'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('endtimes'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('cats'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('cats'); ?></div>
			</div>
		</fieldset>

		<fieldset class="form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('locid'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('locid'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('contactid'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('contactid'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('featured'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('featured'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
			</div>
		</fieldset>
		
		<fieldset class="adminform">
			<div class="clr"></div>
			<?php echo $this->form->getLabel('articletext'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('articletext'); ?>
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
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('hits'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('hits'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('version'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('version'); ?></div>
			</div>
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
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('registra'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('registra'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('unregistra'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('unregistra'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('maxplaces'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('maxplaces'); ?></div>
			</div>
			<div class="control-group">	
				<div class="control-label"><label><?php echo JText::_ ('COM_JEM_BOOKED_PLACES') . ':';?></label></div>
				<div class="controls"><input id="event-booked" class="readonly" type="text"  value="<?php echo $this->item->booked; ?>" /></div>
			</div>
			
			<?php if ($this->item->maxplaces): ?>
			<div class="control-group">	
				<div class="control-label"><label><?php echo JText::_ ('COM_JEM_AVAILABLE_PLACES') . ':';?></label></div>
				<div class="controls"><input id="event-available" class="readonly" type="text"  value="<?php echo ($this->item->maxplaces-$this->item->booked); ?>" /></div>
			</div>
			<?php endif; ?>

			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('waitinglist'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('waitinglist'); ?></div>
			</div>
		</fieldset>
<?php echo JHtml::_('bootstrap.endSlide'); ?>


<!-- Image -->
	<?php echo JHtml::_('bootstrap.addSlide', 'slide', JText::_('COM_JEM_IMAGE'), 'event-image'); ?>
		<fieldset class="form-vertical">
			<div class="control-group">	
				<div class="control-label"><?php echo $this->form->getLabel('datimage'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('datimage'); ?></div>
			</div>
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
			<div class="control-group">	
				<div class="control-label"><label for="meta_keywords"><?php echo JText::_('COM_JEM_META_KEYWORDS').':';?></label></div>
						<?php
						if (! empty ( $this->item->meta_keywords )) {
							$meta_keywords = $this->item->meta_keywords;
						} else {
							$meta_keywords = $this->jemsettings->meta_keywords;
						}
						?>
				<div class="controls"><textarea class="inputbox" name="meta_keywords" id="meta_keywords" rows="5" cols="40" maxlength="150" onfocus="get_inputbox('meta_keywords')" onblur="change_metatags()"><?php echo $meta_keywords; ?></textarea></div>
			</div>
			
			<div class="control-group">	
				<div class="control-label"><label for="meta_description"><?php echo JText::_('COM_JEM_META_DESCRIPTION').':';?></label></div>
					<?php
					if (! empty ( $this->item->meta_description )) {
						$meta_description = $this->item->meta_description;
					} else {
						$meta_description = $this->jemsettings->meta_description;
					}
					?>
				<div class="controls"><textarea class="inputbox" name="meta_description" id="meta_description" rows="5" cols="40" maxlength="200"	onfocus="get_inputbox('meta_description')" onblur="change_metatags()"><?php echo $meta_description;?></textarea></div>
			</div>
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


		<script type="text/javascript">
		<!--
			starter("<?php
			echo JText::_ ( 'COM_JEM_META_ERROR' );
			?>");	// window.onload is already in use, call the function manualy instead
		-->
		</script>
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