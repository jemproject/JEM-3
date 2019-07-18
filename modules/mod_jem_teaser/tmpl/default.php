<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Teaser
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

if ($params->get('use_modal', 0)) {
	JHtml::_('behavior.modal', 'a.flyermodal');
	$modal = 'flyermodal';
} else {
	$modal = 'notmodal';
}
?>

<div id="jemmoduleteaser">

	<!-- define foreach loop -->
	<?php foreach ($list as $item) : ?>
	<!-- start row output -->
	<div class="eventset" summary="mod_jem_teaser">

	<!-- define header -->
		<?php if ($item->eventlink) { ?>
		<h2 class="event-title">
			<a href="<?php echo $item->eventlink; ?>" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></a>
		</h2>
		<?php } else { ?>
		<h2 class="event-title">
			<?php echo $item->title; ?>
		</h2>
		<?php }; ?>

		<table>
			<tr>
				<td>
					<div class="calendar">
						<div class="monthteaser">
							<?php echo $item->month; ?>
						</div>
						<div class="dayteaser">
							<?php echo $item->dayname; ?>
						</div>
						<div class="daynumteaser">
							<?php echo $item->daynum; ?>
						</div>
					</div>
				</td>
				<td>
					<div class="teaser-jem">
						<div>
							<?php if(($item->eventimage)!=str_replace("jpg","",($item->eventimage)) OR
									 ($item->eventimage)!=str_replace("gif","",($item->eventimage)) OR
									 ($item->eventimage)!=str_replace("png","",($item->eventimage))) : ?>
								<a href="<?php echo $item->eventimageorig; ?>" class="<?php echo $modal;?>" title="<?php echo $item->title; ?> ">
								<img class="float_right image-preview" src="<?php echo $item->eventimage; ?>" alt="<?php echo $item->title; ?>" /></a>
							<?php else : ?>
							<?php endif; ?>
							<?php if(($item->venueimage)!=str_replace("jpg","",($item->venueimage)) OR
									 ($item->venueimage)!=str_replace("gif","",($item->venueimage)) OR
									 ($item->venueimage)!=str_replace("png","",($item->venueimage))) : ?>
								<a href="<?php echo $item->venueimageorig; ?>" class="<?php echo $modal;?>" title="<?php echo $item->venue; ?> ">
								<img src="<?php echo $item->venueimage; ?>" alt="<?php echo $item->venue; ?>" class="float_right image-preview" /></a>
							<?php endif; ?>
						</div>
						<div>
							<?php echo $item->eventdescription; ?>
							<?php	 
							 if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) :
								echo '<a class="readmore" href="'.$item->link.'">'.$item->linkText.'</a>';
								endif;
							?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php if ($item->date && $params->get('datemethod', 1) == 2) :?>
						<div class="date">
							<small><?php echo $item->date; ?></small>
						</div>
					<?php endif; ?>
					<?php if ($item->time && $params->get('datemethod', 1) == 1) :?>
						<div class="time">
							<small><?php echo $item->time; ?></small>
						</div>
					<?php endif; ?>
				</td>
				<td>
					<div class="venue-title">
					<?php if ($item->venuelink) : ?>
						<a href="<?php echo $item->venuelink; ?>" title="<?php echo $item->venue; ?>"><?php echo $item->venue; ?></a>
					<?php else : ?>
						<?php echo $item->venue; ?>
					<?php endif; ?>
					</div>
					<div class="category">
						<?php echo $item->catname; ?>
					</div>
				</td>
			</tr>
		</table>
	</div> <!-- end of row -->
	<?php endforeach; ?>

</div><!-- end of container -->
