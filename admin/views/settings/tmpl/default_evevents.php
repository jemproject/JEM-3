<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

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


?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_EVENT'); ?></legend>
		
			<?php foreach ($this->form->getFieldset('evevents') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	
			<?php foreach ($this->form->getFieldset('basic') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>	
</fieldset>
<br /><br />
