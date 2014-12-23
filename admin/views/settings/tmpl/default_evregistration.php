<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
$group = 'globalattribs';
?>
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
<br />