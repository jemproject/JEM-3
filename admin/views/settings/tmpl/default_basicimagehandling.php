<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$gdv = JEMImage::gdVersion();
$group = 'globalattribs';
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_( 'COM_JEM_IMAGE_HANDLING' ); ?></legend>
			
		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('sizelimit'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('sizelimit'); ?></div>
		</div>

		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('imagehight'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('imagehight'); ?></div>
		</div>

		<div class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('imagewidth'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('imagewidth'); ?>
				<span class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_WARNING').'::'.JText::_('COM_JEM_WARNING_MAX_IMAGEWIDTH');echo JHtml::tooltipText($tooltip,'',true); ?>">
					<?php echo $this->WarningIcon(); ?>
				</span>
			</div>
		</div>
			
		<?php if ($gdv && $gdv >= 2) : //is the gd library installed on the server and its version > 2? ?>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('gddisabled'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('gddisabled'); ?></div>
		</div>
		<?php endif; ?>

		<div id="lb1" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('lightbox'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('lightbox'); ?></div>
		</div>
</fieldset>