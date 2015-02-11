<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>

<table class="eventtable" style="width:<?php echo $this->jemsettings->tablewidth; ?>;" summary="jem">
	<colgroup>
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
	</colgroup>

	<thead>
		<tr>
			<th id="jem_date_cat<?php echo $this->catrow->id; ?>" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_DATE'); ?></th>
			<?php if ($this->jemsettings->showtitle == 1) : ?>
			<th id="jem_title_cat<?php echo $this->catrow->id; ?>" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_TITLE'); ?></th>
			<?php endif; ?>
			<?php if ($this->jemsettings->showlocate == 1) : ?>
			<th id="jem_location_cat<?php echo $this->catrow->id; ?>" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_LOCATION'); ?></th>
			<?php endif; ?>
			<?php if ($this->jemsettings->showcity == 1) : ?>
			<th id="jem_city_cat<?php echo $this->catrow->id; ?>" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_CITY'); ?></th>
			<?php endif; ?>
			<?php if ($this->jemsettings->showcat == 1) : ?>
			<th id="jem_category_cat<?php echo $this->catrow->id; ?>" class="sectiontableheader"><?php echo JText::_('COM_JEM_TABLE_CATEGORY'); ?></th>
			<?php endif; ?>
		</tr>
	</thead>

	<tbody>
	<?php if (empty($this->catrow->events)) : ?>
		<tr class="noevents"><td colspan="20"><?php echo JText::_('COM_JEM_NO_EVENTS'); ?></td></tr>
	<?php else : ?>
		<?php $odd = 0; ?>
		<?php foreach ($this->catrow->events as $row) : ?>
			<tr class="sectiontableentry<?php echo ($odd + 1) . $this->params->get( 'pageclass_sfx' ); ?>"
				itemscope="itemscope" itemtype="http://schema.org/Event">

				<td class="jem_date_cat<?php echo $this->catrow->id; ?>">
					<?php
						echo JEMOutput::formatShortDateTime($row->dates, $row->times,
							$row->enddates, $row->endtimes);
						echo JEMOutput::formatSchemaOrgDateTime($row->dates, $row->times,
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
					<td class="jem_title jem_title_cat<?php echo $this->catrow->id; ?>">
						<a href="<?php echo JRoute::_( JEMHelperRoute::getEventRoute($row->slug)); ?>" itemprop="url">
							<span itemprop="name"><?php echo $this->escape($row->title); ?></span>
						</a>
					</td>
				<?php endif; ?>

				<?php if (($this->jemsettings->showtitle == 1) && ($this->jemsettings->showdetails == 0)) : ?>
					<td class="jem_title jem_title_cat<?php echo $this->catrow->id; ?>" itemprop="name">
						<?php echo $this->escape($row->title); ?>
					</td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showlocate == 1) : ?>
					<td class="jem_location_cat<?php echo $this->catrow->id; ?>">
						<?php if ($this->jemsettings->showlinkvenue == 1 ) : ?>
							<?php echo $row->locid != 0 ? "<a href='".JRoute::_(JemHelperRoute::getVenueRoute($row->venueslug))."'>".$this->escape($row->venue)."</a>" : '-'; ?>
						<?php else : ?>
							<?php echo $row->locid ? $this->escape($row->venue) : '-'; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showcity == 1) : ?>
					<td class="jem_city_cat<?php echo $this->catrow->id; ?>"><?php echo $row->city ? $this->escape($row->city) : '-'; ?></td>
				<?php endif; ?>

				<?php if ($this->jemsettings->showcat == 1) : ?>
					<td class="jem_category_cat<?php echo $this->catrow->id; ?>">
					<?php echo implode(", ",
							JemOutput::getCategoryList($row->categories, $this->jemsettings->catlinklist)); ?>
					</td>
				<?php endif; ?>
			</tr>
			<?php $odd = 1 - $odd; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>