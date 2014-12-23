<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;

$saveOrder	= $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jem&task=events.saveOrderAjax&tmpl=component';
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

<form action="<?php echo JRoute::_('index.php?option=com_jem&view=events'); ?>" method="post" name="adminForm" id="adminForm">
		
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
				<th width="1%" class="center hidden-phone"><?php echo JHtml::_('grid.checkall'); ?></th>
				<th class="nowrap"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_DATE', 'a.dates', $listDirn, $listOrder ); ?></th>
				<th><?php echo JText::_('COM_JEM_EVENT_TIME'); ?></th>
				<th class="nowrap"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_EVENT_TITLE', 'a.title', $listDirn, $listOrder ); ?></th>
				<th><?php echo JHtml::_('searchtools.sort', 'COM_JEM_VENUE', 'loc.venue', $listDirn, $listOrder ); ?></th>
				<th><?php echo JHtml::_('searchtools.sort', 'COM_JEM_CITY', 'loc.city', $listDirn, $listOrder ); ?></th>
				<th><?php echo JText::_('COM_JEM_CATEGORIES'); ?></th>
				<th width="1%" class="center nowrap"><?php echo JText::_('JSTATUS'); ?></th>
				<th width="5%">
					<?php echo JHtml::_('searchtools.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
				</th>
				<th class="nowrap"><?php echo JText::_('COM_JEM_CREATION'); ?></th>
				<th class="center"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_HITS', 'a.hits', $listDirn, $listOrder ); ?></th>
				<th width="1%" class="center nowrap"><?php echo JText::_('COM_JEM_REGISTERED_USERS'); ?></th>
				<th width="1%" class="center nowrap"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_ID', 'a.id', $listDirn, $listOrder ); ?></th>
				<th width="1%" class="center nowrap"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_RECURRENCE', 'a.recurrence_group', $listDirn, $listOrder); ?></th>
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
			<?php
			foreach ($this->items as $i => $row) :
	
				//Prepare date
				$displaydate = JemOutput::formatLongDateTime($row->dates, null, $row->enddates, null);
				// Insert a break between date and enddate if possible
				$displaydate = str_replace(" - ", " -<br />", $displaydate);

				//Prepare time
				if (!$row->times) {
					$displaytime = '-';
				} else {
					$displaytime = JemOutput::formattime($row->times);
				}

				$ordering	= ($listOrder == 'ordering');
				$canCreate	= $user->authorise('core.create');
				$canEdit	= $user->authorise('core.edit');
				$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
				$canChange	= $user->authorise('core.edit.state') && $canCheckin;

				$venuelink 		= 'index.php?option=com_jem&amp;task=venue.edit&amp;id='.$row->locid;
				$published 		= JHtml::_('jgrid.published', $row->published, $i, 'events.');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
				<td class="center"><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td>
					<?php if ($row->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $row->editor, $row->checked_out_time, 'events.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jem&task=event.edit&id='.(int) $row->id); ?>">
							<?php echo $displaydate; ?>
						</a>
					<?php else : ?>
						<?php echo $displaydate; ?>
					<?php endif; ?>
				</td>
				<td><?php echo $displaytime; ?></td>
				<td class="eventtitle">
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jem&task=event.edit&id='.(int) $row->id); ?>">
							<?php echo $this->escape($row->title);?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($row->title) ; ?>
					<?php endif; ?>
					<br />
					<?php if (JString::strlen($row->alias) > 25) : ?>
						<?php echo JString::substr( $this->escape($row->alias), 0 , 25).'...'; ?>
					<?php else : ?>
						<?php echo $this->escape($row->alias); ?>
					<?php endif; ?>
				</td>
				<td class="venue">
					<?php if ($row->venue) : ?>
						<?php if ( $row->vchecked_out && ( $row->vchecked_out != $this->user->get('id') ) ) : ?>
							<?php echo $this->escape($row->venue); ?>
						<?php else : ?>
							<span>
								<a href="<?php echo $venuelink; ?>">
									<?php echo $this->escape($row->venue); ?>
								</a>
							</span>
						<?php endif; ?>
					<?php else : ?>
						<?php echo '-'; ?>
					<?php endif; ?>
				</td>
				<td class="city"><?php echo $row->city ? $this->escape($row->city) : '-'; ?></td>
				<td class="category">
				<?php echo implode(", ", JemOutput::getCategoryList($row->categories, $this->jemsettings->catlinklist,true)); ?>
				</td>
				<td class="center"><?php echo $published; ?></td>
				<td class="center">
					<?php echo JHtml::_('jemhtml.featured', $row->featured, $i, $canChange); ?>
				</td>
				<td>
					<?php echo JText::_('COM_JEM_AUTHOR').': '; ?><a href="<?php echo 'index.php?option=com_users&amp;task=user.edit&id='.$row->created_by; ?>"><?php echo $row->author; ?></a><br />
					<?php echo JText::_('COM_JEM_EMAIL').': '; ?><a href="mailto:<?php echo $this->escape($row->email); ?>"><?php echo $this->escape($row->email); ?></a><br />
					<?php
					$created	 	= JHtml::_('date',$row->created,JText::_('DATE_FORMAT_LC2'));
					$modified 		= JHtml::_('date',$row->modified,JText::_('DATE_FORMAT_LC2') );
					$image 			= JHtml::_('image','com_jem/icon-16-info.png',NULL,NULL,true );

					$overlib 		= JText::_('COM_JEM_CREATED_AT').': '.$created.'<br />';
					if ($row->author_ip != '') {
						$overlib		.= JText::_('COM_JEM_WITH_IP').': '.$row->author_ip.'<br />';
					}
					if ($row->modified != '0000-00-00 00:00:00') {
						$overlib 	.= JText::_('COM_JEM_EDITED_AT').': '.$modified.'<br />';
						$overlib 	.= JText::_('COM_JEM_GLOBAL_MODIFIEDBY').': '.$row->modified_by.'<br />';
					}
					?>
					<span class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_EVENTS_STATS').'::'.$overlib;echo JHtml::tooltipText($tooltip,'',true);?>">
						<?php echo $image; ?>
					</span>
				</td>
				<td class="center"><?php echo $row->hits; ?></td>

				<td class="center">
					<?php
					if ($row->registra == 1) {
						$linkreg 	= 'index.php?option=com_jem&amp;view=attendees&amp;eid='.$row->id;
						$count = $row->regCount;
						if ($row->maxplaces)
						{
							$count .= '/'.$row->maxplaces;
							if ($row->waitinglist && $row->waiting) {
								$count .= ' +'.$row->waiting;
							}
						}
						?>
						<a href="<?php echo $linkreg; ?>" title="<?php echo JText::_('COM_JEM_EVENTS_MANAGEATTENDEES'); ?>">
							<?php echo $count; ?>
						</a>
					<?php } else { ?>
						<?php echo JHtml::_('image', 'com_jem/publish_r.png', NULL, NULL, true); ?>
					<?php } ?>
				</td>
				<td class="center"><?php echo $row->id; ?></td>
				<td class="center">
				<?php 
				# check if this event has a recurrence_group
				if ($row->recurrence_group) {
					# the event belongs to a recurrence_group so we will output the recurrence_group
					echo $row->recurrence_group;
				}
				?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>