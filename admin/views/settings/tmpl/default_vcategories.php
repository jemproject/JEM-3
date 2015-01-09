<?php
/**
 * @version 3.0.6
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
$group = 'vcategories';
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_VCATEGORIES'); ?></legend>
		
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('show_category_image',$group); ?></div>
			<div class="controls"><?php echo $this->form->getInput('show_category_image',$group); ?></div>
		</div>	
		
</fieldset>