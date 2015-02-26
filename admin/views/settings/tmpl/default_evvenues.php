<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$group = 'globalattribs';
?>


<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_VENUES'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('event_show_locdescription', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_show_locdescription', $group); ?></div>
		</div>
			
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('event_show_detailsadress', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_show_detailsadress', $group); ?></div>
		</div>
			
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('event_show_detlinkvenue', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_show_detlinkvenue', $group); ?></div>
		</div>
			
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('event_show_mapserv', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_show_mapserv', $group); ?></div>
		</div>
			
			
		<div id="eventmap1" style="display:none" class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('event_tld', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_tld', $group); ?></div>
		</div>
			
			
		<div id="eventmap2" style="display:none" class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('event_lg', $group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_lg', $group); ?></div>
		</div>
</fieldset>
<br />
