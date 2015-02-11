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
$function = JFactory::getApplication()->input->getCmd('function', 'jSelectEvent');
?>

<form action="index.php?option=com_jem&amp;view=eventelement&amp;tmpl=component" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<?php echo $this->lists['filter']; ?>
			</div>
			<div class="btn-group pull-left input-append">
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_JEM_SEARCH');?>" value="<?php echo $this->lists['search']; ?>" class="inputbox" onChange="this.form.submit();" />
				<button type="submit" class="btn"><i class="icon-search"></i></button>
				<button type="button" class="btn" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right">
				<select name="filter_state" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions',array('all' => 0, 'trash' => 0)), 'value', 'text', $this->filter_state, true);?>
			</select>
			</div>
			<div class="clearfix"></div>
		</div>
	</fieldset>
	
	<table class="table table-striped" id="table-condensed">
		<thead>
			<tr>
				<th class="center" width="5"><?php echo JText::_('COM_JEM_NUM'); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_EVENT_TITLE', 'a.title', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATE', 'a.dates', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_START', 'a.times', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_VENUE', 'loc.venue', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_CITY', 'loc.city', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
				<th class="title"><?php echo JText::_('COM_JEM_CATEGORY'); ?></th>
				<th class="center" width="1%" nowrap="nowrap"><?php echo JText::_('JSTATUS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->rows as $i => $row) : ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td>
				<a href="javascript:void(0)" class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->title)); ?>');"><?php echo $this->escape($row->title); ?></a>
			</td>
			<td>
				<?php
					// Format date
					echo JemOutput::formatLongDateTime($row->dates, null, $row->enddates, null);
				?>
			</td>
			<td>
				<?php
					// Prepare time
					if (!$row->times) {
						$displaytime = '-';
					} else {
						$time = strftime( $this->jemsettings->formattime, strtotime( $row->times ));
						$displaytime = $time.' '.$this->jemsettings->timename;
					}
					echo $displaytime;
				?>
			</td>
			<td><?php echo $row->venue ? $this->escape($row->venue) : '-'; ?></td>
			<td><?php echo $row->city ? $this->escape($row->city) : '-'; ?></td>
			<td>
			<?php
			# we're referring to the helper due to the multi-cat feature
			echo implode(", ",JemOutput::getCategoryList($row->categories, false));
			?>
			</td>
			<td class="center">
				<?php echo JHtml::_('jgrid.published', $row->published, $i,'',false); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="poweredby">
		<?php echo JemAdmin::footer( ); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="function" value="<?php echo $this->escape($function); ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>