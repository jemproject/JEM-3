<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>

<form action="<?php echo JRoute::_('index.php?option=com_jem&view=updatecheck'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">	
<?php
if ($this->updatedata->failed == 0) {
		?>
		<table style="width:100%" class="adminlist">
			<tr>
		  		<td>
		  		<?php
		  			if ($this->updatedata->current == 0 ) {
		  				echo JHtml::_('image', 'com_jem/icon-48-update.png', null, null, true);
		  			} elseif( $this->updatedata->current == -1 ) {
		  				echo JHtml::_('image', 'com_jem/icon-48-update.png', null, null, true);
		  			} else {
		  				echo JHtml::_('image', 'com_jem/icon-48-update.png', null, null, true);
		  			}
		  		?>
		  		</td>
		  		<td>
		  		<?php
                    if ($this->updatedata->current == 0) {
                        echo '<b><font color="green">'.JText::_('COM_JEM_UPDATECHECK_LATEST_VERSION').'</font></b>';
                    } elseif( $this->updatedata->current == -1 ) {
                        echo '<b><font color="red">'.JText::_('COM_JEM_UPDATECHECK_OLD_VERSION').'</font></b>';
		  			} else {
		  				echo '<b><font color="orange">'.JText::_('COM_JEM_UPDATECHECK_NEWER_VERSION').'</font></b>';
		  			}
		  		?>
		  		</td>
			</tr>
		</table>

		<br />
			<table style="width:100%" class="adminlist">
			<tr>
		  		<td><b><?php echo JText::_('COM_JEM_UPDATECHECK_VERSION').':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->versiondetail;
					?>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_('COM_JEM_UPDATECHECK_RELEASE_DATE').':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->date;
					?>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_('COM_JEM_UPDATECHECK_INFORMATION').':'; ?></b></td>
		  		<td>
					<a href="<?php echo $this->updatedata->info; ?>" target="_blank"><?php echo JText::_('COM_JEM_UPDATECHECK_INFORMATION'); ?></a>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_('COM_JEM_UPDATECHECK_FILES').':'; ?></b></td>
		  		<td>
					<a href="<?php echo $this->updatedata->download; ?>" target="_blank"><?php echo JText::_('COM_JEM_UPDATECHECK_DOWNLOAD'); ?></a>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_('COM_JEM_UPDATECHECK_NOTES').':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->notes;
					?>
		  		</td>
			</tr>
		</table>
<?php
} else {
?>
		<table style="width:100%" class="adminlist">
			<tr>
		  		<td>
		  		<?php
		  			echo JHtml::_('image', 'com_jem/icon-48-update.png', NULL, NULL, true);
		  		?>
		  		</td>
		  		<td>
		  		<?php
		  			echo '<b><font color="red">'.JText::_('COM_JEM_UPDATECHECK_CONNECTION_FAILED').'</font></b>';
		  		?>
		  		</td>
			</tr>
		</table>
<?php
}
?>
<br />
		<table style="width:200px;" class="adminlist">
			<tr>
		  		<td><b><?php echo JText::_('COM_JEM_UPDATECHECK_INSTALLED_VERSION').':'; ?></b></td>
		  		<td><?php echo $this->updatedata->installedversion; ?>
		  		</td>
			</tr>
			</table>

<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</div>
</form>
