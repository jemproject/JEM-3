<?php
/**
 * @version 3.0.2
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

$group = 'globalattribs';
defined('_JEXEC') or die;
?>
<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_GLOBAL_PARAMETERS'); ?></legend>
			<?php foreach ($this->form->getFieldset('globalparam') as $field): ?>
			<div class="control-group">	
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>

</div><div class="span6">

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_GLOBAL_PARAMETERS'); ?></legend>
			<?php foreach ($this->form->getFieldset('globalparam2') as $field): ?>
			<div class="control-group">		
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_VENUES'); ?></legend>
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('global_show_locdescription',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_show_locdescription',$group); ?></div>
		</div>
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('global_show_detailsadress',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_show_detailsadress',$group); ?></div>
		</div>
		<div class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('global_show_detlinkvenue',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_show_detlinkvenue',$group); ?></div>
		</div>
		<div class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('global_show_mapserv',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_show_mapserv',$group); ?></div>
		</div>
		<div id="globalmap1" style="display:none" class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('global_tld',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_tld',$group); ?></div>
		</div>
		<div id="globalmap2" style="display:none" class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('global_lg',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_lg',$group); ?></div>
		</div>
	</fieldset>


	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_VIEW_EDITEVENT'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('global_show_ownedvenuesonly',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('global_show_ownedvenuesonly',$group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_attachmentstab',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_attachmentstab',$group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_othertab',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_othertab',$group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_featured',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_featured',$group); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('editevent_show_published',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('editevent_show_published',$group); ?></div>
		</div>
	</fieldset>
</div>