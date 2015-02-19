<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
?>
<script>
jQuery( document ).ready(function( $ ) {
	calendar();
});
</script>
<div id="jem" class="jlcalendar jem_calendar<?php echo $this->pageclass_sfx;?>">

<div class="topbox"></div>
<div class="clearfix"></div>
<div class="info_container">

<!-- heading -->
	<?php if ($this->params->get('show_page_heading', 1)): ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

<!-- introtext -->
	<?php if ($this->params->get('showintrotext')) : ?>
		<div class="description no_space clearfix">
			<?php echo $this->params->get('introtext'); ?>
		</div>
		<p> </p>
	<?php endif; ?>

<!-- calendar -->
<div class="calendarbox">
<?php
	# define variables
	$countcatevents = array ();
	$countperday	= array();
	$limit			= $this->params->get('daylimit', 10);
	$catinfo		= array();

	# loop
	foreach ($this->rows as $row) :
		if (!JemHelper::isValidDate($row->dates)) {
			continue; // skip events with open date !
		}

		// get event date
		$year	= strftime('%Y', strtotime($row->dates));
		$month	= strftime('%m', strtotime($row->dates));
		$day	= strftime('%d', strtotime($row->dates));

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
		$timehtml = '';

		if ($this->vsettings->get('show_timedetails','1')) {
			$start = JemOutput::formattime($row->times);
			$end = JemOutput::formattime($row->endtimes);

			if ($start != '') {
				$timehtml = '<div class="time"><span class="text-label">'.JText::_('COM_JEM_TIME_SHORT').': </span>';
				$timehtml .= $start;
				
				if ($end != '') {
					$timehtml .= ' - '.$end;
				}
				
				$timehtml .= '</div>';
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

			# attach category color if any in front of the event title in the calendar overview
			if (isset($category->color) && $category->color) {
				$colorpic .= '<span class="colorpic" style="width:6px; background-color: '.$category->color.';"></span>';
			}

			# count occurence of the category
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

		# for time in calendar
		
		
		$timetp   = '';
		
		$multi = new stdClass();
		$multi->row = (isset($row->multi) ? $row->multi : 'na');


		$start = JemOutput::formattime($row->times,'',false);
		$end   = JemOutput::formattime($row->endtimes,'',false);

		if (!$this->vsettings->get('show_timedetails','1')) {
			$start = '';
			$end = '';
		}

		if ($multi->row) {
			if ($multi->row == 'first') {
				$timetp .= $image = JHtml::_("image","com_jem/arrow-left.png",'', NULL, true).' '.$start;
				$timetp .= '<br />';
			} elseif ($multi->row == 'middle') {
				$timetp .= JHtml::_("image","com_jem/arrow-middle.png",'', NULL, true);
				$timetp .= '<br />';
			} elseif ($multi->row == 'zlast') {
				$timetp .= JHtml::_("image","com_jem/arrow-right.png",'', NULL, true).' '.$end;
				$timetp .= '<br />';
			} elseif ($multi->row == 'na') {
				if ($start != '') {
					$timetp .= $start;
					/*
					if ($end != '') {
						$timetp .= ' - '.$end;
					}
					*/
					//$timetp .= '<br />';
					$timetp .= ' ';
				}
			}
		}
		$timetp2 = '';
		if ($timetp) {
			$timetp2  = '<div class="time label label-info">';
			$timetp2 .= $timetp.'</div><br>';
		} else {
			$timetp2 .= $timetp;
		}
		

		$catname = '<div class="catname">'.$multicatname.'</div>';

		$eventdate = !empty($row->multistartdate) ? JemOutput::formatdate($row->multistartdate) : JemOutput::formatdate($row->dates);
		if (!empty($row->multienddate)) {
			$eventdate .= ' - ' . JemOutput::formatdate($row->multienddate);
		} elseif ($row->enddates && $row->dates < $row->enddates) {
			$eventdate .= ' - ' . JemOutput::formatdate($row->enddates);
		}

		//venue
		$venue = '';
		if ($this->vsettings->get('show_venue','1')) {
			# check if there is a venue and if so display the venue
			if ($row->locid) {
				$venue  = '<div class="cal_venue"><span class="text-label">'.JText::_('COM_JEM_VENUE_SHORT').': </span>';
				$venue .=  $this->escape($row->venue);
				$venue .= '</div>';
			}
		}

		//date in tooltip
		$multidaydate = '<div class="time"><span class="text-label">'.JText::_('COM_JEM_DATE').': </span>';
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
		$content .= JemHelper::caltooltip($catname.$eventname.$timehtml.$venue, $eventdate, $row->title, $detaillink, 'hasTooltip', $timetp2, $category->color);
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

	// print the calendar
	echo $this->cal->showMonth();
	?>

	</div>

	<div id="jlcalendarlegend">

	<!-- Calendar buttons -->
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

	<!-- Calendar Legend -->
		<div class="calendarLegends">
			<?php
			if ($this->params->get('displayLegend')) {

				##############
				## FOR EACH ##
				##############

				$counter	= array();
				$cats		= array();

				# walk through events
				foreach ($this->rows as $row) {
					foreach ($row->categories as $cat) {

						# sort out dupes for the counter (catid-legend)
						if (!in_array($cat->id, $counter)) {
							# add cat id to cat counter
							$counter[] = $cat->id;

							# build legend
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

	<div class="clearfix"></div>

	<div class="poweredby">
		<?php echo JemOutput::footer(); ?>
	</div>


	</div>
</div>
