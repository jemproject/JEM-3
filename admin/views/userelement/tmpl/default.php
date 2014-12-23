<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$function = JFactory::getApplication()->input->getCmd('function', 'jSelectUser');
JHtml::_('bootstrap.tooltip');
?>

<form action="index.php?option=com_jem&amp;view=userelement&tmpl=component" method="post" id="adminForm" name="adminForm">

	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left input-append">
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->lists['search']; ?>" class="inputbox" onChange="this.form.submit();" />
				<button type="submit" class="btn"><i class="icon-search"></i></button>
				<button type="button" class="btn" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="clearfix"></div>
	</fieldset>


	<table class="table table-striped" id="table-condensed">
		<thead>
			<tr>
				<th class="center" width="5"><?php echo JText::_('COM_JEM_NUM'); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'Name', 'u.name', $this->lists['order_Dir'], $this->lists['order'], 'selectuser' ); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'Username', 'u.username', $this->lists['order_Dir'], $this->lists['order'], 'selectuser' ); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'Email', 'u.email', $this->lists['order_Dir'], $this->lists['order'], 'selectuser' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
				$row = $this->rows[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td class="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td align="left">
				 <a href="javascript:void(0)" class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->username)); ?>');"><?php echo $this->escape($row->username); ?></a>
			</td>
			<td><?php echo $row->username; ?></td>
			<td><?php echo $row->email; ?></td>
		</tr>
			<?php $k = 1 - $k; } ?>
		</tbody>
	</table>

	<div class="poweredby">
		<?php echo JemAdmin::footer( ); ?>
	</div>

	<input type="hidden" name="task" value="selectuser" />
	<input type="hidden" name="function" value="<?php echo $this->escape($function); ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>