<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$group = 'vvenue';
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_VVENUE'); ?></legend>
			
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('show_locdescription',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('show_locdescription',$group); ?></div>
		</div>
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('show_detailsadress',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('show_detailsadress',$group); ?></div>
		</div>
		<div class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('show_detlinkvenue',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('show_detlinkvenue',$group); ?></div>
		</div>
		<div class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('show_mapserv',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('show_mapserv',$group); ?></div>
		</div>
		<div class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('show_country',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('show_country',$group); ?></div>
		</div>
		<div class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('show_state',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('show_state',$group); ?></div>
		</div>
</fieldset>