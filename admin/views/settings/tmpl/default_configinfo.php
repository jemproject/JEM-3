<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

?>
<div class="span12">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_CONFIGINFO'); ?></legend>
		
		<fieldset>
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
					<td><?php echo $this->config->vs_component; ?></td>
				</tr>
		</tbody>
		</table></fieldset>
	
		<fieldset>
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
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_CONTENT').': '; ?></td>
					<td><?php echo $this->config->vs_plg_content; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_COMMENTS').': '; ?></td>
					<td><?php echo $this->config->vs_plg_comments; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_MAILER').': '; ?></td>
					<td><?php echo $this->config->vs_plg_mailer; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_QUICKICON').': '; ?></td>
					<td><?php echo $this->config->vs_plg_quickicon; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_SEARCH').': '; ?></td>
					<td><?php echo $this->config->vs_plg_search; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_FINDER').': '; ?></td>
					<td><?php echo $this->config->vs_plg_finder; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PLG_XTDEVENT').': '; ?></td>
					<td><?php echo $this->config->vs_plg_xtdevent; ?></td>
				</tr>
			</tbody>
		</table></fieldset>
	
		<fieldset><table id="eventList" class="table table-striped">
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
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM_CAL').': '; ?></td>
					<td><?php echo $this->config->vs_mod_jem_cal; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM').': '; ?></td>
					<td><?php echo $this->config->vs_mod_jem; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM_WIDE').': '; ?></td>
					<td><?php echo $this->config->vs_mod_jem_wide; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_MOD_JEM_TEASER').': '; ?></td>
					<td><?php echo $this->config->vs_mod_jem_teaser; ?></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<fieldset><table id="eventList" class="table table-striped">
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
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PHP').': '; ?></td>
					<td><?php echo $this->config->vs_php; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_PHP_MAGICQUOTES').': '; ?></td>
					<td><?php echo $this->config->vs_php_magicquotes; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JEM_SETTINGS_CONFIG_VS_GD').': '; ?></td>
					<td><?php echo $this->config->vs_gd; ?></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	</fieldset>
</div>