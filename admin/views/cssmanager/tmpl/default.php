<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal');
$canDo = JEMHelperBackend::getActions();
?>

<form action="<?php echo JRoute::_('index.php?option=com_jem&view=cssmanager'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">	
	<div class="row-fluid">	
		<div class="span6">
	
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('COM_JEM_CSSMANAGER_DESCRIPTION_LEGEND');?></legend>
			<p><?php echo JText::_('COM_JEM_CSSMANAGER_DESCRIPTION');?></p>
		</fieldset>

		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('COM_JEM_CSSMANAGER_LINENUMBER_LEGEND');?></legend>
			<p><?php echo JText::_('COM_JEM_CSSMANAGER_LINENUMBER_DESCRIPTION'); ?></p>
			<h3><?php echo JText::_('COM_JEM_CSSMANAGER_LINENUMBER_STATUS'); ?></h3>
			<p>
			<?php if ($this->statusLinenumber) : ?>
				<?php echo JText::_('COM_JEM_CSSMANAGER_LINENUMBER_ENABLED'); ?>
				<br />
				<a href="<?php echo JRoute::_('index.php?option=com_jem&amp;task=cssmanager.disablelinenumber');?>">
					<?php echo JText::_('COM_JEM_CSSMANAGER_LINENUMBER_DISABLE'); ?>
				</a>
			<?php else: ?>
				<?php echo JText::_('COM_JEM_CSSMANAGER_LINENUMBER_DISABLED'); ?>
				<br />
				<a href="<?php echo JRoute::_('index.php?option=com_jem&amp;task=cssmanager.setlinenumber');?>">
					<?php echo JText::_('COM_JEM_CSSMANAGER_LINENUMBER_ENABLE'); ?>
				</a>
			<?php endif; ?>
			</p>
		</fieldset>

		</div><div class="span6">
	
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('COM_JEM_CSSMANAGER_FILENAMES');?></legend>
			<?php if (!empty($this->files['css'])) : ?>
				<ul>
				<?php foreach ($this->files['css'] as $file) : ?>
					<li>
					<?php if ($canDo->get('core.edit')) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jem&task=source.edit&id='.$file->id);?>">
					<?php endif; ?>
					<?php echo JText::sprintf('COM_JEM_CSSMANAGER_EDIT_CSS', $file->name);?>
					<?php if ($canDo->get('core.edit')) : ?>
						</a>
					<?php endif; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<br>
<legend><?php echo JText::_('COM_JEM_CSSMANAGER_FILENAMES_CUSTOM');?></legend>
			<?php if (!empty($this->files['custom'])) : ?>
				<ul>
				<?php foreach ($this->files['custom'] as $file) : ?>
					<li>
					<?php if ($canDo->get('core.edit')) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jem&task=source.edit&id='.$file->id);?>">
					<?php endif; ?>
					<?php echo JText::sprintf('COM_JEM_CSSMANAGER_EDIT_CSS', $file->name);?>
					<?php if ($canDo->get('core.edit')) : ?>
						</a>
					<?php endif; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</fieldset>
		
		</div></div>
		
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		<?php JHtml::_('behavior.keepalive'); ?>
		</div>
</form>