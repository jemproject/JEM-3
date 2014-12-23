<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$print = $this->print;
$admin = $this->admin;
?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<?php if ($this->settings->get('global_show_filter',1) || $this->settings->get('global_display',1)) : ?>
<div id="jem_filter" class="clearfix">
		<?php if ($this->settings->get('global_show_filter',1)) : ?>
			<div class="pull-left">
				<?php
					echo $this->lists['filter'].'&nbsp;';
				?>
				<div class="btn-wrapper input-append">
					<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->lists['search'];?>" class="inputbox input-medium" onchange="this.form.submit();" />
					<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" type="submit"><i class="icon-search"></i></button>
					<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($this->settings->get('global_display',1)) : ?>
			<div class="pull-right">
				<?php
					echo $this->pagination->getLimitBox();
				?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<!-- TABLE -->
<table class="eventtable" style="width:<?php echo $this->jemsettings->tablewidth; ?>;" summary="jem">
	<colgroup>
		<?php if ($this->jemsettings->showeventimage == 1) : ?>
			<col width="<?php echo $this->jemsettings->tableeventimagewidth; ?>" class="jem_col_event_image" />
		<?php endif; ?>
			<col width="<?php echo $this->jemsettings->datewidth; ?>" class="jem_col_date" />
		<?php if ($this->jemsettings->showtitle == 1) : ?>
			<col width="<?php echo $this->jemsettings->titlewidth; ?>" class="jem_col_title" />
		<?php endif; ?>
		<?php if ($this->jemsettings->showlocate == 1) : ?>
			<col width="<?php echo $this->jemsettings->locationwidth; ?>" class="jem_col_venue" />
		<?php endif; ?>
		<?php if ($this->jemsettings->showcity == 1) : ?>
			<col width="<?php echo $this->jemsettings->citywidth; ?>" class="jem_col_city" />
		<?php endif; ?>
		<?php if ($this->jemsettings->showcat == 1) : ?>
			<col width="<?php echo $this->jemsettings->catfrowidth; ?>" class="jem_col_category" />
		<?php endif; ?>
		<?php if ($this->jemsettings->showatte == 1) : ?>
			<col width="<?php echo $this->jemsettings->attewidth; ?>" class="jem_col_attendees" />
		<?php endif; ?>
		<?php if ($admin && !$print) { ?>
			<col class="jem_col_edit" />
		<?php } ?>
	</colgroup>

	<thead>
		<tr>
			<?php if ($this->jemsettings->showeventimage == 1) : ?>
				<th id="jem_eventimage" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_EVENTIMAGE'); ?></th>
			<?php endif; ?>
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
			<?php if ($this->jemsettings->showatte == 1) : ?>
				<th id="jem_attendees" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_ATTENDEES'); ?></th>
			<?php endif; ?>
			<?php if ($admin && !$print) { ?>
				<th id="jem_edit" class="sectiontableheader"></th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php if ($this->noevents == 1) : ?>
			<tr class="noevents"><td colspan="20"><?php echo JText::_('COM_JEM_NO_EVENTS'); ?></td></tr>
		<?php else : ?>
			<?php $this->rows = $this->getRows(); ?>
			<?php foreach ($this->rows as $row) : ?>
			
			<?php
			$params		= $row->params;
			$images 	= json_decode($row->datimage);
			$canEdit	= $row->params->get('access-edit');
			$user		= JFactory::getUser();
			$attribs 	= json_decode($row->attribs);
			?>
			
				<?php if (!empty($row->featured)) :   ?>
				<tr class="featured featured<?php echo $row->id.$this->params->get('pageclass_sfx'); ?>" itemscope="itemscope" itemtype="http://schema.org/Event" >
				<?php else : ?>
				<tr class="sectiontableentry<?php echo ($row->odd +1) . $this->params->get('pageclass_sfx'); ?>" itemscope="itemscope" itemtype="http://schema.org/Event" >
				<?php endif; ?>

				<?php if ($this->jemsettings->showeventimage == 1) : ?>
					<td class="jem_eventimage">
						<?php if (!empty($row->datimage)) : ?>
							<?php
							$dimage = JemImage::flyercreator($row->datimage, 'event');
							echo JemOutput::flyer($row, $dimage, 'event');
							?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<td class="jem_date">
					<?php
						echo JemOutput::formatShortDateTime($row->dates, $row->times,
							$row->enddates, $row->endtimes);
						echo JemOutput::formatSchemaOrgDateTime($row->dates, $row->times,
							$row->enddates, $row->endtimes);
					?>
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
						<a href="<?php echo JRoute::_(JemHelperRoute::getEventRoute($row->slug)); ?>" itemprop="url">
							<span itemprop="name"><?php echo $this->escape($row->title); ?></span>
						</a>
					</td>
				<?php endif; ?>

				<?php if (($this->jemsettings->showtitle == 1) && ($this->jemsettings->showdetails == 0)) : ?>
					<td class="jem_title" itemprop="name">
						<?php echo $this->escape($row->title); ?>
					</td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showlocate == 1) : ?>
					<td class="jem_location">
						<?php if ($this->jemsettings->showlinkvenue == 1) : ?>
							<?php echo !empty($row->locid) ? "<a href='".JRoute::_(JemHelperRoute::getVenueRoute($row->venueslug))."'>".$this->escape($row->venue)."</a>" : '-'; ?>
						<?php else : ?>
							<?php echo !empty($row->locid) ? $this->escape($row->venue) : '-'; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showcity == 1) : ?>
					<td class="jem_city">
						<?php echo !empty($row->city) ? $this->escape($row->city) : '-'; ?>
					</td>
				<?php endif; ?>


				<?php if ($this->jemsettings->showcat == 1) : ?>
					<td class="jem_category">
						<?php echo implode(", ", JemOutput::getCategoryList($row->categories, $this->jemsettings->catlinklist)); ?>
					</td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showatte == 1) : ?>
					<td class="jem_attendees">
						<?php echo !empty($row->regCount) ? $this->escape($row->regCount) : '-'; ?>
					</td>
				<?php endif; ?>
				<?php if ($admin && !$print) { ?>
					<td class="jem_edit">
						<?php echo JemOutput::editbutton($row, $params, $attribs, true, 'eventslist'); ?>
					</td>
				<?php } ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="view" value="eventslist" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir'] ?>" />
	</form>