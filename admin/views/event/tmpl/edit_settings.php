<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Create shortcut to parameters.
$params = $this->state->get('params');

$params = $params->toArray();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params['show_publishing_options']);

if (!$editoroptions) :
    $params['show_publishing_options'] = '1';
    $params['show_article_options'] = '1';
    $params['show_urls_images_backend'] = '0';
    $params['show_urls_images_frontend'] = '0';
endif;

defined('_JEXEC') or die;
$group = 'attribs';

?>
<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_EVENT'); ?></legend>
		
			<?php foreach ($this->form->getFieldset('basic') as $field): ?>
			<div class="control-group">	
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
		
			<?php foreach ($this->form->getFieldset('evevents',$group) as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
</fieldset>
	
	

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
			<div class="control-group" id="eventmap1" style="display:none">
				<div class="control-label"><?php echo $this->form->getLabel('event_tld',$group); ?></div>
				<div class="controls"><?php echo $this->form->getInput('event_tld',$group); ?></div>
			</div>
			<div class="control-group" id="eventmap2" style="display:none">
				<div class="control-label"><?php echo $this->form->getLabel('event_lg',$group); ?></div>
				<div class="controls"><?php echo $this->form->getInput('event_lg',$group); ?></div>
			</div>
</fieldset>
	
<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_REGISTRATION'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('event_comunsolution',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_comunsolution',$group); ?></div>
		</div>
		<div class="control-group" id="comm1" style="display:none">
			<div class="control-label"><?php echo $this->form->getLabel('event_comunoption',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('event_comunoption',$group); ?></div>
		</div>
</fieldset>
