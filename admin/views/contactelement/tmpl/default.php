<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal','a.flyermodal');
$function = JFactory::getApplication()->input->getCmd('function', 'jSelectContact');
?>

<form action="index.php?option=com_jem&amp;view=contactelement&amp;tmpl=component" method="post" name="adminForm" id="adminForm">


	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
			<?php echo $this->lists['filter'];?>
			</div>
			<div class="btn-group pull-left input-append">
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_JEM_SEARCH');?>" value="<?php echo $this->lists['search']; ?>" class="inputbox" onChange="this.form.submit();" />
				<button type="submit" class="btn"><i class="icon-search"></i></button>
				<button type="button" class="btn" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="clearfix"></div>
		</div>
	</fieldset>


	<table class="table table-striped" id="table-condensed">
	<thead>
		<tr>
			<th width="7" class="center"><?php echo JText::_('COM_JEM_NUM'); ?></th>
			<th align="left" class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_NAME', 'con.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th align="left" class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_ADDRESS', 'con.address', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th align="left" class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_CITY', 'con.suburb', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th align="left" class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_STATE', 'con.state', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th align="left" class="title"><?php echo JText::_('COM_JEM_EMAIL'); ?></th>
			<th align="left" class="title"><?php echo JText::_('COM_JEM_TELEPHONE'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="12">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach ($this->rows as $i => $row) : ?>
		 <tr class="row<?php echo $i % 2; ?>">
			<td class="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td align="left">
					<a href="javascript:void(0)" class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->name)); ?>');"><?php echo htmlspecialchars_decode($this->escape($row->name)); ?></a>
			</td>
			<td align="left"><?php echo $this->escape($row->address); ?></td>
			<td align="left"><?php echo $this->escape($row->suburb); ?></td>
			<td align="left"><?php echo $this->escape($row->state); ?></td>
			<td align="left"><?php echo $this->escape($row->email_to); ?></td>
			<td align="left"><?php echo $this->escape($row->telephone); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="function" value="<?php echo $this->escape($function); ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	
	<div class="poweredby">
		<?php echo JemAdmin::footer( ); ?>
	</div>
</form>