<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>
<form name="adminForm" method="post" id="adminForm">

<table style="width:100%">
	<tr>
		<td width="10%">
			<div class="linkicon">
				<a href="index.php?option=com_jem&amp;task=housekeeping.CleanupImages">
					<?php echo JHtml::_('image', 'com_jem/icon-48-cleancategoryimg.png', JText::_('COM_JEM_HOUSEKEEPING_CLEANUP_IMAGES'), null, true); ?>
					<span><?php echo JText::_('COM_JEM_HOUSEKEEPING_CLEANUP_IMAGES'); ?></span>
				</a>
			</div>
		</td>
		<td width="40%" valign="middle">
			<?php echo JText::_('COM_JEM_HOUSEKEEPING_CLEANUP_IMAGES_DESC'); ?>
		</td>
		<!-- CLEAN TRIGGER ARCHIVE -->
		<td width="10%">
			<div class="linkicon">
				<a href="index.php?option=com_jem&amp;task=housekeeping.triggerarchive">
					<?php echo JHtml::_('image', 'com_jem/icon-48-archive.png', JText::_('COM_JEM_HOUSEKEEPING_TRIGGER_AUTOARCHIVE'), null, true); ?>
					<span><?php echo JText::_('COM_JEM_HOUSEKEEPING_TRIGGER_AUTOARCHIVE'); ?></span>
				</a>
			</div>
		</td>
		<td width="40%" valign="middle">
			<?php echo JText::_('COM_JEM_HOUSEKEEPING_TRIGGER_AUTOARCHIVE_DESC'); ?>
		</td>
	</tr>
	<tr>
		<!-- TRUNCATE CATEGORY/EVENT REFERENCES -->
		<td width="10%">
			<div class="linkicon">
				<a href="index.php?option=com_jem&amp;task=housekeeping.cleanupCatsEventRelations">
					<?php echo JHtml::_('image', 'com_jem/icon-48-cleancategoryimg.png', JText::_('COM_JEM_HOUSEKEEPING_CATSEVENT_RELS'), null, true); ?>
					<span><?php echo JText::_('COM_JEM_HOUSEKEEPING_CLEANUP_CATSEVENT_RELS'); ?></span>
				</a>
			</div>
		</td>
		<td width="40%" valign="middle">
			<?php echo JText::_('COM_JEM_HOUSEKEEPING_CLEANUP_CATSEVENT_RELS_DESC'); ?><br/>
		</td>

		<!-- TRUNCATE ALL DATA -->
		<td width="10%">
			<div class="linkicon">
				<a href="index.php?option=com_jem&amp;task=housekeeping.truncateAllData" onclick="javascript:return confirm('<?php echo JText::_('COM_JEM_HOUSEKEEPING_TRUNCATE_ALL_DATA_CONFIRM'); ?>');">
					<?php echo JHtml::_('image', 'com_jem/icon-48-truncatealldata.png', JText::_('COM_JEM_HOUSEKEEPING_TRUNCATE_ALL_DATA'), null, true); ?>
					<span><?php echo JText::_('COM_JEM_HOUSEKEEPING_TRUNCATE_ALL_DATA'); ?></span>
				</a>
			</div>
		</td>
		<td width="40%" valign="middle">
			<?php echo JText::_('COM_JEM_HOUSEKEEPING_TRUNCATE_ALL_DATA_DESC'); ?>
		</td>
	</tr>
	<tr>
	<td width="10%">
			<div class="linkicon">
				<a href="index.php?option=com_jem&amp;task=housekeeping.rmObsImages">
					<?php echo JHtml::_('image', 'com_jem/icon-48-cleancategoryimg.png', JText::_('COM_JEM_HOUSEKEEPING_REMOVE_IMAGES'), null, true); ?>
					<span><?php echo JText::_('COM_JEM_HOUSEKEEPING_REMOVE_IMAGES'); ?></span>
				</a>
			</div>
		</td>
		<td width="40%" valign="middle">
			<?php echo JText::_('COM_JEM_HOUSEKEEPING_REMOVE_IMAGES_DESC'); ?>
		</td>
	</tr>
</table>
</form>
