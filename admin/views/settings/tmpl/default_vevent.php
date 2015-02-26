<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$group	= 'globalattribs';
$group2 = 'vevent'; 
?>
<div class="row-fluid">
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_VEVENT'); ?></legend>
		<div class="row-fluid">
			<div class="span6">		
				<?php echo $this->loadTemplate('evevents'); ?>
			</div>
			<div class="span6">
				<fieldset class="form-horizontal">
					<legend><?php echo JText::_('COM_JEM_VENUE'); ?></legend>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('event_show_locdescription',$group); ?></div>
							<div class="controls"><?php echo $this->form->getInput('event_show_locdescription',$group); ?></div>
						</div>
						<div class="control-group">	
							<div class="control-label"><?php echo $this->form->getLabel('event_show_detailsadress',$group); ?></div>
							<div class="controls"><?php echo $this->form->getInput('event_show_detailsadress',$group); ?></div>
						</div>
						<div class="control-group">	
							<div class="control-label"><?php echo $this->form->getLabel('event_show_detlinkvenue',$group); ?></div>
							<div class="controls"><?php echo $this->form->getInput('event_show_detlinkvenue',$group); ?></div>
						</div>
						<div class="control-group">	
							<div class="control-label"><?php echo $this->form->getLabel('event_show_mapserv',$group); ?></div>
							<div class="controls"><?php echo $this->form->getInput('event_show_mapserv',$group); ?></div>
						</div>
				</fieldset>
				<fieldset class="form-horizontal">
					<legend><?php echo JText::_('COM_JEM_REGISTRATION'); ?></legend>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('event_comunsolution',$group); ?></div>
							<div class="controls"><?php echo $this->form->getInput('event_comunsolution',$group); ?></div>
						</div>
						<div id="comm1" style="display:none" class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('event_comunoption',$group); ?></div>
							<div class="controls"><?php echo $this->form->getInput('event_comunoption',$group); ?></div>
						</div>
				</fieldset>
			</div>
		</div>	
</fieldset>
</div>

<div class="row-fluid">
<fieldset class="form-horizontal">
<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_VEVENT_BACKEND'); ?></legend>
	<div class="row-fluid">
		<div class="span6">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('COM_JEM_REGISTRATION'); ?></legend>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('registering_b',$group); ?></div>
					<div class="controls"><?php echo $this->form->getInput('registering_b',$group); ?></div>
				</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('unregistering_b',$group); ?></div>
				<div class="controls"><?php echo $this->form->getInput('unregistering_b',$group); ?></div>
			</div>
			</fieldset>
		</div>
		<div class="span6">
			
		</div>
	</div>
	</fieldset>
</div>
