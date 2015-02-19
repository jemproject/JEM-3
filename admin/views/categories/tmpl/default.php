<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$saveOrder 	= ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc');

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_categories&task=categories.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'eventList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jem&view=categories');?>" method="post" name="adminForm" id="adminForm">

	<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
<br/>
	<table class="table table-striped" id="eventList">
		<thead>
			<tr>
				<th width="1%" class="">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th>
					<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.catname', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center" nowrap="nowrap">
					<?php echo JText::_( 'COM_JEM_COLOR' ); ?>
				</th>
				<th width="15%" class="center"><?php echo JHtml::_('grid.sort', 'COM_JEM_GROUP', 'gr.name', $listDirn, $listOrder ); ?></th>
				<th width="1%" class="center" nowrap="nowrap"><?php echo JText::_( 'COM_JEM_EVENTS' ); ?></th>
				<th width="5%">
					<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('searchtools.sort', '', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th class="center" width="10%">
					<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$originalOrders = array();
			foreach ($this->items as $i => $item) :
				$orderkey	= array_search($item->id, $this->ordering[$item->parent_id]);
				$canEdit	= $user->authorise('core.edit');
				$canCheckin	= $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn	= $user->authorise('core.edit.own') && $item->created_user_id == $userId;
				$canChange	= $user->authorise('core.edit.state') && $canCheckin;

				// Get the parents of item for sorting
				if ($item->level > 1)
				{
					$parentsStr = "";
					$_currentParentId = $item->parent_id;
					$parentsStr = " " . $_currentParentId;
					for ($i2 = 0; $i2 < $item->level; $i2++)
					{
					foreach ($this->ordering as $k => $v)
					{
					$v = implode("-", $v);
						$v = "-" . $v . "-";
						if (strpos($v, "-" . $_currentParentId . "-") !== false)
						{
							$parentsStr .= " " . $k;
							$_currentParentId = $k;
								break;
							}
							}
							}
							}
							else
							{
							$parentsStr = "";
							}
				
				
				$grouplink 	= 'index.php?option=com_jem&amp;task=group.edit&amp;id='. $item->groupid;

				/*
				if ($item->level > 0) {
					$repeat = $item->level-1;
				} else {
					$repeat = 0;
				}
				*/
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id; ?>" item-id="<?php echo $item->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>			
						<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_jem&task=category.edit&id='.$item->id);?>">
								<?php echo $this->escape($item->catname); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->catname); ?>
						<?php endif; ?>
						<span class="small" title="<?php echo $this->escape($item->path);?>">
							<?php if (empty($item->note)) : ?>
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							<?php else : ?>
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
							<?php endif; ?></span>
					</td>
					<td class="center">
						<div class="colorpreview" style="width: 20px; cursor:default;background-color: <?php echo ( $item->color == '' )?"transparent":$item->color; ?>;" title="<?php echo $item->color; ?>">
							&nbsp;
						</div>
					</td>
					<td class="center">
						<?php if ($item->catgroup) : ?>
							<span>
							<a href="<?php echo $grouplink; ?>">
								<?php echo $this->escape($item->catgroup); ?>
							</a></span>
						<?php else : ?>
							<?php echo '-'; ?>
						<?php endif; ?>
					</td>
					<td class="center">
						<?php echo $item->assignedevents; ?>
					</td>
					<td class="center">
						<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.', $canChange, 'cb'); ?>
						</div>
					</td>
					<td class="order hidden-phone">
						<?php
								$iconClass = '';
								if (!$canChange)
								{
									$iconClass = ' inactive';
								}
								elseif (!$saveOrder)
								{
									$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
								}
								?>
								<span class="sortable-handler<?php echo $iconClass ?>">
									<i class="icon-menu"></i>
								</span>
								<?php if ($canChange && $saveOrder) : ?>
									<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" />
								<?php endif; ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="center">
						<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt); ?>">
							<?php echo (int) $item->id; ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
</form>
