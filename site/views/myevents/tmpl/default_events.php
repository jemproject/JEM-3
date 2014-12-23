<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>

<?php if (!$this->params->get('show_page_heading', 1)) :
           /* hide this if page heading is shown */     ?>
<h2><?php echo JText::_('COM_JEM_MY_EVENTS'); ?></h2>
<?php endif; ?>

<script type="text/javascript">
	function tableOrdering(order, dir, view)
	{
		var form = document.getElementById("adminForm");

		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		form.submit(view);
	}
</script>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<?php if ($this->settings->get('global_show_filter',1) || $this->settings->get('global_display',1)) : ?>
<div id="jem_filter" class="clearfix">
	<?php if ($this->settings->get('global_show_filter',1)) : ?>
	<div class="pull-left">
		<?php
		echo $this->lists['filter'].'&nbsp;';
		?>
		<div class="btn-wrapper input-append">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->lists['search'];?>" class="inputbox" onchange="this.form.submit();" />
			<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" type="submit"><i class="icon-search"></i></button>
			<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
	</div>
	<?php endif; ?>
	<?php if ($this->settings->get('global_display',1)) : ?>
	<div class="pull-right">
		<?php
		echo $this->events_pagination->getLimitBox();
		?>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>


<table class="eventtable" style="width:<?php echo $this->jemsettings->tablewidth; ?>;" summary="jem">
	<colgroup>
			<col width="1%" class="jem_col_checkall" />
			<col width="<?php echo $this->jemsettings->datewidth; ?>" class="jem_col_date" />
		<?php if ($this->jemsettings->showtitle == 1) : ?>
			<col width="<?php echo $this->jemsettings->titlewidth; ?>" class="jem_col_title" />
		<?php endif; ?>
		<?php if ($this->jemsettings->showlocate == 1) :	?>
			<col width="<?php echo $this->jemsettings->locationwidth; ?>" class="jem_col_venue" />
		<?php endif; ?>
		<?php if ($this->jemsettings->showcity == 1) :	?>
			<col width="<?php echo $this->jemsettings->citywidth; ?>" class="jem_col_city" />
		<?php endif; ?>
		<?php if ($this->jemsettings->showcat == 1) :	?>
			<col width="<?php echo $this->jemsettings->catfrowidth; ?>" class="jem_col_category" />
		<?php endif; ?>
		<?php if ($this->params->get('displayattendeecolumn') == 1) :	?>
			<col width="<?php echo $this->jemsettings->attewidth; ?>" class="jem_col_atte" />
		<?php endif; ?>
			<col width="1%" class="jem_col_status" />
	</colgroup>

	<thead>
		<tr>
			<th><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th id="jem_date" class="sectiontableheader"><?php echo JHtml::_('grid.sort', 'COM_JEM_TABLE_DATE', 'a.dates', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php if ($this->jemsettings->showtitle == 1) : ?>
			<th id="jem_title" class="sectiontableheader"><?php echo JHtml::_('grid.sort', 'COM_JEM_TABLE_TITLE', 'a.title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php endif; ?>
			<?php if ($this->jemsettings->showlocate == 1) : ?>
			<th id="jem_location" class="sectiontableheader"><?php echo JHtml::_('grid.sort', 'COM_JEM_TABLE_LOCATION', 'l.venue', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php endif; ?>
			<?php if ($this->jemsettings->showcity == 1) : ?>
			<th id="jem_city" class="sectiontableheader"><?php echo JHtml::_('grid.sort', 'COM_JEM_TABLE_CITY', 'l.city', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php endif; ?>
			<?php if ($this->jemsettings->showcat == 1) : ?>
			<th id="jem_category" class="sectiontableheader"><?php echo JHtml::_('grid.sort', 'COM_JEM_TABLE_CATEGORY', 'c.catname', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php endif; ?>
			<?php if ($this->params->get('displayattendeecolumn') == 1) : ?>
			<th id="jem_atte" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_ATTENDEES'); ?></th>
			<?php endif; ?>
			<th width="1%" class="center" nowrap="nowrap"><?php echo JText::_('JSTATUS'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php if (count((array)$this->events) == 0) : ?>
		<tr class="noevents"><td colspan="20"><?php echo JText::_('COM_JEM_NO_EVENTS'); ?></td></tr>
	<?php else : ?>
		<?php foreach ($this->events as $i => $row) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo JHtml::_('grid.id', $i, $row->eventid); ?></td>

				<td class="jem_date">
					<?php echo JemOutput::formatShortDateTime($row->dates, $row->times,
						$row->enddates, $row->endtimes); ?>
				</td>
				
				<?php if (($this->jemsettings->showtitle == 1) && ($this->jemsettings->showdetails == 2)) : ?>
				<?php if ($this->escape($row->introtext) != "" ) { ?>
					<td class="jem_title">
						<a href="<?php echo JRoute::_(JemHelperRoute::getEventRoute($row->slug)); ?>" itemprop="url">
							<span itemprop="name"><?php echo $this->escape($row->title); ?></span>
						</a>
					</td>
				<?php } else { ?>
				<td class="jem_title" itemprop="name">
						<?php echo $this->escape($row->title); ?>
					</td>
				<?php } ?>
				<?php endif; ?>

				<?php if (($this->jemsettings->showtitle == 1) && ($this->jemsettings->showdetails == 1)) : ?>
					<td class="jem_title">
						<a href="<?php echo JRoute::_(JemHelperRoute::getEventRoute($row->slug)); ?>">
							<?php echo $this->escape($row->title); ?>
						</a>
					</td>
				<?php endif; ?>

				<?php if (($this->jemsettings->showtitle == 1) && ($this->jemsettings->showdetails == 0)) : ?>
					<td class="jem_title">
						<?php echo $this->escape($row->title); ?>
					</td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showlocate == 1) : ?>
					<td class="jem_location">
						<?php if ($this->jemsettings->showlinkvenue == 1) :  ?>
							<?php echo $row->locid != 0 ? "<a href='".JRoute::_(JemHelperRoute::getVenueRoute($row->venueslug))."'>".$this->escape($row->venue)."</a>" : '-'; ?>
						<?php else : ?>
							<?php echo $row->locid ? $this->escape($row->venue) : '-'; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showcity == 1) : ?>
					<td class="jem_city"><?php echo $row->city ? $this->escape($row->city) : '-'; ?></td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showcat == 1) : ?>
					<td class="jem_category">
					<?php echo implode(", ",
							JemOutput::getCategoryList($row->categories, $this->jemsettings->catlinklist)); ?>
					</td>
				<?php endif; ?>

				<?php if ($this->params->get('displayattendeecolumn') == 1) : ?>
					<td class="attendees">
					<?php if ($row->registra == 1) {
						$app = JFactory::getApplication();
						$menuitem = $app->getMenu()->getActive()->id;
						$linkreg 	= 'index.php?option=com_jem&amp;view=attendees&amp;id='.$row->id.'&Itemid='.$menuitem;
						$count = $row->regCount;
						if ($row->maxplaces)
						{
							$count .= '/'.$row->maxplaces;
							if ($row->waitinglist && $row->waiting) {
								$count .= ' +'.$row->waiting;
							}
						}

						if ($count > 0 && $row->published == 1) {
							?>
							<a href="<?php echo $linkreg; ?>" title="<?php echo JText::_('COM_JEM_MYEVENT_MANAGEATTENDEES'); ?>">
							<?php echo $count; ?>
							</a>
							<?php
						}

						if ($row->published == 0) {
							echo $count;
						}
						if ($count == 0  && $row->published == 1) {
							echo $count;
						}
					} else {
						echo JHtml::_('image', 'com_jem/publish_r.png',NULL,NULL,true);
					}
					?>
				</td>
				<?php endif; ?>
				<td class="center"><?php echo JHtml::_('jgrid.published', $row->published, $i,'myevents.'); ?></td>
			</tr>

		<?php
			$i = 1 - $i;
		?>
		<?php endforeach;?>
	<?php endif;?>
	</tbody>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="enableemailaddress" value="<?php echo $this->enableemailaddress; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_jem" />
<?php echo JHtml::_('form.token'); ?>
</form>

<div class="pagination">
	<?php echo $this->events_pagination->getPagesLinks(); ?>
</div>