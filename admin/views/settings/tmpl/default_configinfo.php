<?php
/**
 * @version 3.0.2
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

?>
<div class="span12">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_CONFIGINFO'); ?></legend>
		
		<table id="eventList" class="table table-striped">
		<thead>
			<tr>
				<th width="25%">
					<?php echo JText::_('COM_JEM_SETTINGS_CONFIG_NAME'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">&#160;</td>
			</tr>
		</tfoot>
		<tbody>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_COMPONENT').': '; ?></td>
					<td><b><?php echo $this->config->vs_component; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_MAILER').': '; ?></td>
					<td><b><?php echo $this->config->vs_plg_mailer; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM_CAL').': '; ?></td>
					<td><b><?php echo $this->config->vs_mod_jem_cal; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM').': '; ?></td>
					<td><b><?php echo $this->config->vs_mod_jem; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM_WIDE').': '; ?></td>
					<td><b><?php echo $this->config->vs_mod_jem_wide; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM_TEASER').': '; ?></td>
					<td><b><?php echo $this->config->vs_mod_jem_teaser; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PHP').': '; ?></td>
					<td><b><?php echo $this->config->vs_php; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PHP_MAGICQUOTES').': '; ?></td>
					<td><b><?php echo $this->config->vs_php_magicquotes; ?> </b></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_GD').': '; ?></td>
					<td><b><?php echo $this->config->vs_gd; ?> </b></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</div>