<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// Load tooltips behavior
JHtml::_('bootstrap.tooltip');
?>
<script>
jQuery( document ).ready(function( $ ) {
	calendar();
});
</script>
<div id="jem" class="jlcalendar jem_calendar<?php echo $this->pageclass_sfx;?>">
	<?php if ($this->params->get('show_page_heading', 1)): ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<?php if ($this->params->get('showintrotext')) : ?>
		<div class="description no_space clearfix">
			<?php echo $this->params->get('introtext'); ?>
		</div>
		<p> </p>
	<?php endif; ?>

	<?php
	$countcatevents = array ();
	$countperday = array();
	$limit = $this->params->get('daylimit', 10);
	$catinfo			= array();

	foreach ($this->rows as $row) :
		if (!JemHelper::isValidDate($row->dates)) {
			continue; // skip, open date !
		}

		//get event date
		$year = strftime('%Y', strtotime($row->dates));
		$month = strftime('%m', strtotime($row->dates));
		$day = strftime('%d', strtotime($row->dates));

		@$countperday[$year.$month.$day]++;
		if ($countperday[$year.$month.$day] == $limit+1) {
			$var1a = JRoute::_( 'index.php?view=day&id='.$year.$month.$day );
			$var1b = JText::_('COM_JEM_AND_MORE');
			$var1c = "<a href=\"".$var1a."\">".$var1b."</a>";
			$id = 'eventandmore';

			$this->cal->setEventContent($year, $month, $day, $var1c, null, $id);
			continue;
		} elseif ($countperday[$year.$month.$day] > $limit+1) {
			continue;
		}

		//for time in tooltip
		$timeTip = '';

		if ($this->settings->get('global_show_timedetails','1')) {
			$start = JemOutput::formattime($row->times);
			$end = JemOutput::formattime($row->endtimes);

			if ($start != '') {
				$timeTip = '<div class="time"><span class="text-label">'.JText::_('COM_JEM_TIME_SHORT').': </span>';
				$timeTip .= $start;
				if ($end != '') {
					$timeTip .= ' - '.$end;
				}
				$timeTip .= '</div>';
			}
		}

		$eventname  = '<div class="eventName">'.JText::_('COM_JEM_TITLE_SHORT').': '.$this->escape($row->title).'</div>';
		$detaillink = JRoute::_(JemHelperRoute::getEventRoute($row->slug));

		//initialize variables
		$multicatname = '';
		$colorpic = '';
		$nr = count($row->categories);
		$ix = 0;
		$content = '';
		$contentend = '';

		$catz = array();

		//walk through categories assigned to an event
		foreach($row->categories AS $category) {
			//Currently only one id possible...so simply just pick one up...
			$detaillink = JRoute::_(JemHelperRoute::getEventRoute($row->slug));

			//wrap a div for each category around the event for show hide toggler
			$catz[]= 'cat'.$category->id;

			//attach category color if any in front of the catname
			if ($category->color) {
				$multicatname .= '<span class="colorpic" style="width:6px; background-color: '.$category->color.';"></span>&nbsp;'.$category->catname;
			} else {
				$multicatname .= $category->catname;
			}

			$ix++;
			if ($ix != $nr) {
				$multicatname .= ', ';
			}

			//attach category color if any in front of the event title in the calendar overview
			if (isset($category->color) && $category->color) {
				$colorpic .= '<span class="colorpic" style="width:6px; background-color: '.$category->color.';"></span>';
			}

			if (!isset($row->multi) || ($row->multi == 'first')) {
				if (!array_key_exists($category->id, $countcatevents)) {
					$countcatevents[$category->id] = 1;
				} else {
					$countcatevents[$category->id]++;
				}
			}

			$catinfo[] = array('catid' => $category->id,'color' => $category->color);
		}

		// end of category-loop
		$catz = implode(' ',$catz);

		$content    .= '<div id="catz" hidecat="" class="'.$catz.'">';
		$contentend .= '</div>';

		$color  = '<div id="eventcontenttop" class="eventcontenttop">';
		$color .= $colorpic;
		$color .= '</div>';

		//for time in calendar
		$timeData = '';

		if ($this->settings->get('global_show_timedetails','1')) {
			$start = JemOutput::formattime($row->times,'',false);
			$end   = JemOutput::formattime($row->endtimes,'',false);

			$multi = new stdClass();
			$multi->row = (isset($row->multi) ? $row->multi : 'na');

			if ($multi->row) {
				if ($multi->row == 'first') {
					$timeData .= $image = JHtml::_("image","com_jem/arrow-left.png",'', NULL, true).' '.$start;
					$timeData .= '<br />';
				} elseif ($multi->row == 'middle') {
					$timeData .= JHtml::_("image","com_jem/arrow-middle.png",'', NULL, true);
					$timeData .= '<br />';
				} elseif ($multi->row == 'zlast') {
					$timeData .= JHtml::_("image","com_jem/arrow-right.png",'', NULL, true).' '.$end;
					$timeData .= '<br />';
				} elseif ($multi->row == 'na') {
					if ($start != '') {
						$timeData .= $start;
						/*
						if ($end != '') {
							$timetp .= ' - '.$end;
						}
						$timetp .= '<br />';
						*/
						$timeData .= ' ';
					}
				}
			}
		}

		if ($timeData) {
			$timeHtml  = '<div class="time label label-info">';
			$timeHtml .= $timeData.'</div><br>';
		} else {
			$timeHtml = '';
		}

		$catname = '<div class="catname">'.$multicatname.'</div>';

		$eventdate = !empty($row->multistartdate) ? JemOutput::formatdate($row->multistartdate) : JemOutput::formatdate($row->dates);
		if (!empty($row->multienddate)) {
			$eventdate .= ' - ' . JemOutput::formatdate($row->multienddate);
		} elseif ($row->enddates && $row->dates < $row->enddates) {
			$eventdate .= ' - ' . JemOutput::formatdate($row->enddates);
		}

		//venue
		if ($this->jemsettings->showlocate == 1) {
			$venue  = '<div class="location"><span class="text-label">'.JText::_('COM_JEM_VENUE_SHORT').': </span>';
			$venue .=     $row->locid ? $this->escape($row->venue) : '-';
			$venue .= '</div>';
		} else {
			$venue = '';
		}

		//date in tooltip
		$multidaydate = '<div class="time label label-info"><span class="text-label">'.JText::_('COM_JEM_DATE').': </span>';
		if ($multi->row == 'first') {
			$multidaydate .= JemOutput::formatShortDateTime($row->dates, $row->times, $row->enddates, $row->endtimes);
			$multidaydate .= JemOutput::formatSchemaOrgDateTime($row->dates, $row->times, $row->enddates, $row->endtimes);
		} elseif ($multi->row == 'middle') {
			$multidaydate .= JemOutput::formatShortDateTime($row->multistartdate, $row->times, $row->multienddate, $row->endtimes);
			$multidaydate .= JemOutput::formatSchemaOrgDateTime($row->multistartdate, $row->times, $row->multienddate, $row->endtimes);
		} elseif ($multi->row == 'zlast') {
			$multidaydate .= JemOutput::formatShortDateTime($row->multistartdate, $row->times, $row->multienddate, $row->endtimes);
			$multidaydate .= JemOutput::formatSchemaOrgDateTime($row->multistartdate, $row->times, $row->multienddate, $row->endtimes);
		} else {
			$multidaydate .= JemOutput::formatShortDateTime($row->dates, $row->times, $row->enddates, $row->endtimes);
			$multidaydate .= JemOutput::formatSchemaOrgDateTime($row->dates, $row->times, $row->enddates, $row->endtimes);
		}
		$multidaydate .= '</div>';

		//generate the output
		$content .= JemHelper::caltooltip($catname.$eventname.$timeTip.$venue, $eventdate, $row->title, $detaillink, 'hasTooltip', $timeHtml, $category->color);
		$content .= $colorpic;
		$content .= $contentend;

		$this->cal->setEventContent($year, $month, $day, $content);
	endforeach;

	$catinfo	= JemHelper::arrayUnique($catinfo);

	// create hidden input fields
	foreach ($catinfo as $val) {
		echo "<input name='category".$val['catid']."' type='hidden' value='".$val['color']."'>";
	}
	echo "<input id='usebgcatcolor' name='usebgcatcolor' type='hidden' value='".$this->params->get('usebgcatcolor','0')."'>";

		# output of calendar
		$currentWeek = $this->currentweek;
		$nrweeks = $this->params->get('nrweeks', 1);
	echo $this->cal->showWeeksByID($currentWeek,$nrweeks);
	?>

	<div id="jlcalendarlegend">
		<div class="calendarButtons">
			<div class="calendarButtonsToggle">
				<div id="buttonshowall" class="calendarButton">
					<?php echo JText::_('COM_JEM_SHOWALL'); ?>
				</div>
				<div id="buttonhideall" class="calendarButton">
					<?php echo JText::_('COM_JEM_HIDEALL'); ?>
				</div>
			</div>
		</div>

		<div class="clr"></div>
		<div class="calendarLegends">
			<?php
			//print the legend
			if ($this->params->get('displayLegend')) {
				$counter = array();

				//walk through events
				foreach ($this->rows as $row) {
					//walk through the event categories
					foreach ($row->categories as $cat) {
						//sort out dupes
						if (!in_array($cat->id, $counter)) {
							//add cat id to cat counter
							$counter[] = $cat->id;

							//build legend
							if (array_key_exists($cat->id, $countcatevents)) {
							?>
								<div class="eventCat" id="cat<?php echo $cat->id; ?>">
									<?php
									if (isset($cat->color) && $cat->color) {
										echo '<span class="colorpic" style="background-color: '.$cat->color.';"></span>';
									}
									echo $cat->catname.' ('.$countcatevents[$cat->id].')';
									?>
								</div>
							<?php
							}
						}
					}
				}
			}
			?>
		</div>
	</div>

	<div class="clr"></div>

	<div class="poweredby">
		<?php echo JemOutput::footer(); ?>
	</div>
</div>
