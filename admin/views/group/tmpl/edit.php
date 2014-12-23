<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('behavior.modal', 'a.flyermodal');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Create shortcut to parameters.
$params = $this->state->get('params');
$params = $params->toArray();

// Define slides options
$slidesOptions = array(
		"active" => "slide1" // It is the ID of the active tab.
);

?>
<script type="text/javascript">
	window.addEvent('domready', function(){
	});
	
	// moves elements from one select box to another one
	function moveOptions(from,to) {
		// Move them over
		for (var i=0; i<from.options.length; i++) {
			var o = from.options[i];
			if (o.selected) {
			  to.options[to.options.length] = new Option( o.text, o.value, false, false);
			}
		}

		// Delete them from original
		for (var i=(from.options.length-1); i>=0; i--) {
			var o = from.options[i];
			if (o.selected) {
			  from.options[i] = null;
			}
		}
		from.selectedIndex = -1;
		to.selectedIndex = -1;
	}

	function selectAll()
    {
        selectBox = document.getElementById("maintainers");

        for (var i = 0; i < selectBox.options.length; i++)
        {
             selectBox.options[i].selected = true;
        }
    }
</script>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		selectAll();
		if (task == 'group.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_jem&layout=edit&id='.(int) $this->item->id); ?>"
	class="form-validate" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="form-horizontal">
		<div class="span12">
	
<!-- Tabs -->	
	<div class="span8">
	<?php echo JHtml::_('bootstrap.startTabSet', 'group', array('active' => 'tab1')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'group', 'tab1', JText::_('COM_JEM_GROUP_INFO_TAB', true)); ?>
		<fieldset class="form-horizontal">
			<legend>
				<?php echo empty($this->item->id) ? JText::_('COM_JEM_NEW_GROUP') : JText::sprintf('COM_JEM_GROUP_DETAILS', $this->item->id); ?>
			</legend>
			<?php 
				echo $this->form->renderField('name');
				echo $this->form->renderField('id');
				echo $this->form->renderField('maintainers2');
			?>
		</fieldset>
		<fieldset class="form-horizontal">
		
		

		<div class="row-fluid">
		<div class="span12">
		<div class="row-fluid">
			<div class="span5">
			<b><?php echo JText::_('COM_JEM_GROUP_AVAILABLE_USERS').':'; ?></b><br>
				<?php echo $this->lists['available_users']; ?>
			</div>
			
			<div class="span2">
				<input class="btn" type="button" name="right" value="&gt;" onClick="moveOptions(document.adminForm['available_users'], document.adminForm['maintainers[]'])" />
				<br /><br />
				<input class="btn" type="button" name="left" value="&lt;" onClick="moveOptions(document.adminForm['maintainers[]'], document.adminForm['available_users'])" />
			</div>
			
			<div class="span5">
			<b><?php echo JText::_('COM_JEM_GROUP_MAINTAINERS').':'; ?></b><br>
				<?php echo $this->lists['maintainers']; ?>
			</div>
		</div>
		</div>
		</div>
		<br>
		
	
		</fieldset>
			<fieldset class="form-vertical">
			<?php echo $this->form->renderField('description');?>
		</fieldset>
	
		<?php echo JHtml::_('bootstrap.endTab'); ?>			
		<?php echo JHtml::_('bootstrap.endTabSet');?>
</div>
<div class="span4">	

	
<!--  start of sliders -->
	<?php echo JHtml::_('bootstrap.startAccordion', 'group-sliders-'.$this->item->id, $slidesOptions); ?>
	
<!-- Publishing -->
	<?php echo JHtml::_('bootstrap.addSlide', 'group-sliders-'.$this->item->id, JText::_('COM_JEM_GROUP_PERMISSIONS'), 'slide1'); ?>
	
		<fieldset class="form-vertical">
		<?php 
			echo $this->form->renderField('addvenue');
			echo $this->form->renderField('publishvenue');
			echo $this->form->renderField('editvenue');
			echo $this->form->getLabel('spacer');
			echo $this->form->renderField('addevent');
			echo $this->form->renderField('publishevent');
			echo $this->form->renderField('editevent');
		?>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
		
		<input type="hidden" name="task" value="" />
				<!--  END RIGHT DIV -->
				<?php echo JHtml::_( 'form.token' ); ?>
				</div>
			</div>
	</div>	
</form>