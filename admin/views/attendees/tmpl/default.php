<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
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
<form action="<?php echo JRoute::_('index.php?option=com_jem&view=attendees&eid='.$this->eventid); ?>"  method="post" name="adminForm" id="adminForm">
	<table class="tableheader">
		<tr>
			<td>
				<b><?php echo JText::_('COM_JEM_DATE').':'; ?></b>&nbsp;<?php echo $this->event->dates; ?><br />
				<b><?php echo JText::_('COM_JEM_EVENT_TITLE').':'; ?></b>&nbsp;<?php echo $this->escape($this->event->title); ?>
			</td>
			<td>
				<div class="btn-wrapper pull-right input-prepend input-append">
					<?php
					// @todo: use helper functions
					
					$text	= JHtml::_('image','com_jem/export_excel.png', JText::_('COM_JEM_EXPORT_FILE'), NULL, true).' '.JText::_('COM_JEM_EXPORT_FILE');
					$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=800,height=480,directories=no,location=no';
					$print_link = 'index.php?option=com_jem&amp;task=attendees.export&amp;tmpl=raw&amp;eid='.$this->eventid;
						
					$overlib = JText::_('COM_JEM_EXPORT_FILE');
					$title = JHtml::tooltipText(JText::_('COM_JEM_EXPORT_FILE'), $overlib, 0);
						
					$attribs = array();
					$attribs['title']   = $title;
					$attribs['class'] = 'btn btn-small icon_csv hasTooltip';
					$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
					$attribs['rel']     = 'nofollow';
					$output =  JHtml::_('link', $print_link, $text, $attribs);
					echo $output;
					

					$text	= JHtml::_('image','system/printButton.png', JText::_('COM_JEM_PRINT'), NULL, true).' '.JText::_('COM_JEM_PRINT');
					$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=800,height=480,directories=no,location=no';
					$print_link = 'index.php?option=com_jem&amp;view=attendees&amp;layout=print&amp;tmpl=component&amp;eid='.$this->eventid;
					
					$overlib = JText::_('COM_JEM_PRINT');
					$title = JHtml::tooltipText(JText::_('COM_JEM_PRINT'), $overlib, 0);
					
					$attribs = array();
					$attribs['title']   = $title;
					$attribs['class'] = 'btn btn-small icon_print hasTooltip';
					$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
					$attribs['rel']     = 'nofollow';
					$output =  JHtml::_('link', $print_link, $text, $attribs);					
					echo $output;
					?>
				</div>
			</td>
		</tr>
	</table>
	<br />
	<table class="adminform">
		<tr>
			<td width="100%">			
			<?php
				// Search tools bar
				echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>
			</td>
		</tr>
	</table>
	<table class="table table-striped" id="eventList">
		<thead>
			<tr>
				<th width="1%" class="center"><?php echo JText::_('COM_JEM_NUM'); ?></th>
				<th width="1%" class="center hidden-phone"><?php echo JHtml::_('grid.checkall'); ?></th>
				<th class="title"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_NAME', 'u.name', $listDirn, $listOrder); ?></th>
				<th class="title"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_USERNAME', 'u.username', $listDirn, $listOrder); ?></th>
				<th class="title"><?php echo JText::_('COM_JEM_EMAIL'); ?></th>
				<th class="title"><?php echo JText::_('COM_JEM_IP_ADDRESS'); ?></th>
				<th class="title"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_REGDATE', 'r.uregdate', $listDirn, $listOrder); ?></th>
				<th class="title center"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_USER_ID', 'r.uid', $listDirn, $listOrder); ?></th>
				<?php if ($this->event->waitinglist): ?>
				<th class="title"><?php echo JHtml::_('searchtools.sort', 'COM_JEM_HEADER_WAITINGLIST_STATUS', 'r.waiting', $listDirn, $listOrder); ?></th>
				<?php endif;?>
				<th class="title center"><?php echo JText::_('COM_JEM_REMOVE_USER'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="20"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
		foreach ($this->items as $i => $row) :
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
				<td class="center"><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_jem&task=attendee.edit&id='.(int)$row->id.'&eid='.$this->eventid); ?>"><?php echo $row->name; ?></a></td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int)$row->uid); ?>"><?php echo $row->username; ?></a>
				</td>
				<td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
				<td><?php echo $row->uip == 'DISABLED' ? JText::_('COM_JEM_DISABLED') : $row->uip; ?></td>
				<td><?php echo JHtml::_('date',$row->uregdate,JText::_('DATE_FORMAT_LC2')); ?></td>
				<td class="center"><?php echo $row->uid; ?></td>
				<?php if ($this->event->waitinglist): ?>
				<td>
					
				<?php if ($row->waiting):?>	
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','attendees.toggle')">
						<?php echo JHtml::_('image','com_jem/publish_y.png',JText::_('COM_JEM_ON_WAITINGLIST'),array('class'=>'hasTooltip','title' => ($row->waiting ? JText::_('COM_JEM_ON_WAITINGLIST') : JText::_('COM_JEM_ATTENDING'))),true); ?>
					</a>
				<?php else: ?>
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','attendees.toggle')">
						<?php echo JHtml::_('image','com_jem/tick.png',JText::_('COM_JEM_ATTENDING'),array('class'=>'hasTooltip','title' => ($row->waiting ? JText::_('COM_JEM_ON_WAITINGLIST') : JText::_('COM_JEM_ATTENDING'))),true); ?>
					</a>
				<?php endif;?>	
					
				</td>
				<?php endif;?>
				<td class="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','attendees.remove')">
					<?php echo JHtml::_('image','com_jem/publish_x.png',JText::_('COM_JEM_REMOVE'),array('class'=>'hasTooltip'),true); ?>
				</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
		<input type="hidden" name="eid" value="<?php echo $this->eventid; ?>" />
</form>