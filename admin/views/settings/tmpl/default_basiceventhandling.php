<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_EVENT_HANDLING'); ?></legend>
			
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('oldevent'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('oldevent'); ?></div>
		</div>

		<div id="evhandler1" class="control-group">		
			<div class="control-label"><?php echo $this->form->getLabel('minus'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('minus'); ?></div>
		</div>
</fieldset>