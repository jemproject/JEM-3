<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_jem.category');
$saveOrder	= $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jem&task=venues.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'eventList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$params		= (isset($this->state->params)) ? $this->state->params : new JObject();
$settings	= $this->settings;
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

<form action="<?php echo JRoute::_('index.php?option=com_jem&view=venues'); ?>" method="post" name="adminForm" id="adminForm">


<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
<br />

<table class="table table-striped" id="eventList">
	<thead>
		<tr>
			<th width="1%" class="center"><?php echo JText::_('COM_JEM_NUM'); ?></th>
			<th width="1%" class="center"><?php echo JHtml::_('grid.checkall'); ?></th>
			<th class="title"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_VENUE', 'a.venue', $listDirn, $listOrder ); ?></th>
			<th width="20%"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_ALIAS', 'a.alias', $listDirn, $listOrder ); ?></th>
			<th><?php echo JText::_('COM_JEM_WEBSITE'); ?></th>
			<th><?php echo JHtml::_('searchtools.sort', 'COM_JEM_CITY', 'a.city', $listDirn, $listOrder ); ?></th>
			<th><?php echo JHtml::_('searchtools.sort', 'COM_JEM_STATE', 'a.state', $listDirn, $listOrder ); ?></th>
			<th width="1%"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_COUNTRY', 'a.country', $listDirn, $listOrder ); ?></th>
			<th width="1%" class="center" nowrap="nowrap"><?php echo JText::_('JSTATUS'); ?></th>
			<th><?php echo JText::_('COM_JEM_CREATION'); ?></th>
			<th width="1%" class="center" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_EVENTS', 'assignedevents', $listDirn, $listOrder ); ?></th>
			<th width="1%" class="nowrap center hidden-phone">
				<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
			</th>
			<th width="1%" class="center" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_ID', 'a.id', $listDirn, $listOrder ); ?></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="20">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

	<tbody id="search_in_here">
		<?php foreach ($this->items as $i => $row) : ?>
			<?php
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create');
			$canEdit	= $user->authorise('core.edit');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state') && $canCheckin;

			$link 		= 'index.php?option=com_jem&amp;task=venue.edit&amp;id='. $row->id;
			$published 	= JHtml::_('jgrid.published', $row->published, $i, 'venues.', $canChange, 'cb', $row->publish_up, $row->publish_down);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
				<td class="center"><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td align="left" class="venue">
					<?php if ($row->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $row->editor, $row->checked_out_time, 'venues.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jem&task=venue.edit&id='.(int) $row->id); ?>">
							<?php echo $this->escape($row->venue); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($row->venue); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if (JString::strlen($row->alias) > 25) : ?>
						<?php echo $this->escape(JString::substr($row->alias, 0 , 25)).'...'; ?>
					<?php else : ?>
						<?php echo $this->escape($row->alias); ?>
					<?php endif; ?>
				</td>
				<td align="left">
					<?php if ($row->url) : ?>
						<a href="<?php echo $this->escape($row->url); ?>" target="_blank">
							<?php if (JString::strlen($row->url) > 25) : ?>
								<?php echo $this->escape(JString::substr($row->url, 0 , 25)).'...'; ?>
							<?php else : ?>
								<?php echo $this->escape($row->url); ?>
							<?php endif; ?>
						</a>
					<?php else : ?>
						-
					<?php endif; ?>
				</td>
				<td align="left" class="city"><?php echo $row->city ? $this->escape($row->city) : '-'; ?></td>
				<td align="left" class="state"><?php echo $row->state ? $this->escape($row->state) : '-'; ?></td>
				<td class="country"><?php echo $row->country ? $this->escape($row->country) : '-'; ?></td>
				<td class="center"><?php echo $published; ?></td>
				<td>
					<?php echo JText::_('COM_JEM_AUTHOR').': '; ?>
					<a href="<?php echo 'index.php?option=com_users&amp;task=user.edit&id='.$row->created_by; ?>">
						<?php echo $row->author; ?>
					</a><br />
					<?php echo JText::_('COM_JEM_EMAIL').': '; ?><a href="mailto:<?php echo $this->escape($row->email); ?>"><?php echo $this->escape($row->email); ?></a><br />
					<?php
					$created 	= JHtml::_('date',$row->created,JText::_('DATE_FORMAT_LC2'));
					$modified 		= JHtml::_('date',$row->modified,JText::_('DATE_FORMAT_LC2'));
					$image 			= JHtml::_('image','com_jem/icon-16-info.png', NULL,NULL,true);
					
					$overlib 		= JText::_('COM_JEM_CREATED_AT').': '.$created.'<br />';
					if ($row->author_ip != '') {
						$overlib		.= JText::_('COM_JEM_WITH_IP').': '.$row->author_ip.'<br />';
					}
					if ($row->modified != '0000-00-00 00:00:00') {
						$overlib 	.= JText::_('COM_JEM_EDITED_AT').': '.$modified.'<br />';
						$overlib 	.= JText::_('COM_JEM_GLOBAL_MODIFIEDBY').': '.$row->modified_by.'<br />';
					}
					?>
					<span class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_VENUES_STATS').'::'.$overlib;echo JHtml::tooltipText($tooltip,'',true);?>">
						<?php echo $image; ?>
					</span>
				</td>
				<td class="center"><?php echo $row->assignedevents; ?></td>		
				<td class="order nowrap center hidden-phone">
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
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
						</td>
				<td class="center"><?php echo $row->id; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>