<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal','a.flyermodal');
$function = JFactory::getApplication()->input->getCmd('function', 'jSelectVenue');
?>

<form action="index.php?option=com_jem&amp;view=venueelement&amp;tmpl=component" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<?php echo $this->lists['filter']; ?>
			</div>
			<div class="btn-group pull-left input-append">
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->lists['search']; ?>" class="inputbox" onChange="this.form.submit();" />
				<button type="submit" class="btn"><i class="icon-search"></i></button>
				<button type="button" class="btn" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				<button type="button" class="btn" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('', '<?php echo JText::_('COM_JEM_SELECT_VENUE') ?>');"><?php echo JText::_('COM_JEM_NOVENUE')?></button>
			</div>
		</div>
		<div class="clearfix"></div>
	</fieldset>


	<table class="table table-striped" id="table-condensed">
		<thead>
			<tr>
				<th class="center" width="7"><?php echo JText::_('COM_JEM_NUM'); ?></th>
				<th align="left" class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_VENUE', 'l.venue', $this->lists['order_Dir'], $this->lists['order'], 'venueelement' ); ?></th>
				<th align="left" class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_CITY', 'l.city', $this->lists['order_Dir'], $this->lists['order'], 'venueelement' ); ?></th>
				<th align="left" class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_STATE', 'l.state', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th align="left" class="title center"><?php echo JText::_('COM_JEM_COUNTRY'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->rows as $i => $row) : ?>
		 <tr class="row<?php echo $i % 2; ?>">
			<td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td align="left">
				 <a href="javascript:void(0)" class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->venue)); ?>');"><?php echo $this->escape($row->venue); ?></a>
			</td>
			<td align="left"><?php echo $this->escape($row->city); ?></td>
			<td align="left"><?php echo $this->escape($row->state); ?></td>
			<td class="center"><?php echo $this->escape($row->country); ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="poweredby">
		<?php echo JEMAdmin::footer( ); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="function" value="<?php echo $this->escape($function); ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>