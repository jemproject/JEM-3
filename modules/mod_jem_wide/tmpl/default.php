<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Wide
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('behavior.modal', 'a.flyermodal');
?>

<div id="jemmodulewide">

<table class="eventset" summary="mod_jem_wide">

	<colgroup>
		<col width="30%" class="jemmodw_col_title" />
		<col width="20%" class="jemmodw_col_category" />
		<col width="20%" class="jemmodw_col_venue" />
		<col width="15%" class="jemmodw_col_eventimage" />
		<col width="15%" class="jemmodw_col_venueimage" />
	</colgroup>

<?php foreach ($list as $item) : ?>
	<tr>
		<td valign="top">
			<span class="event-title">
				<?php if ($item->eventlink) : ?>
				<a href="<?php echo $item->eventlink; ?>" title="<?php echo $item->title; ?>">
				<?php endif; ?>

					<?php echo $item->title; ?>

				<?php if ($item->eventlink) : ?>
				</a>
				<?php endif; ?>
			</span>

			<br />

			<span class="date">
				<?php echo $item->date; ?>
			</span>
			<?php

			if ($item->time && $params->get('datemethod', 1) == 1) :
			?>
			<span class="time">
				<?php echo $item->time; ?>
			</span>
			<?php endif; ?>

		</td>
		<td>
			<span class="category">
					<?php echo $item->catname; ?>
			</span>
		</td>
		<td>
			<span class="venue-title">
				<?php if ($item->venuelink) : ?>
				<a href="<?php echo $item->venuelink; ?>" title="<?php echo $item->venue; ?>">
				<?php endif; ?>

					<?php echo $item->venue; ?>

				<?php if ($item->venuelink) : ?>
				</a>
				<?php endif; ?>
			</span>
		</td>
		<td align="center" class="event-image-cell">
			<?php
			if ($item->eventimage) {
				if ($params->get('use_modal')) :
				if ($item->eventimageorig) {
					$image = $item->eventimageorig;
				} else {
					$image = '';
				}
			?>
				<a href="<?php echo $image; ?>" class="flyermodal" title="<?php echo $item->title; ?>">
			<?php endif; ?>
				<img src="<?php echo $item->eventimage; ?>" alt="<?php echo $item->title; ?>" class="image-preview" />
			<?php if ($item->eventlink) : ?>
				</a>
			<?php
			endif;
			}
			?>
		</td>
		<td align="center" class="event-image-cell">
			<?php if ($item->venueimage) { if ($params->get('use_modal')) : ?>
			<a href="<?php echo $item->venueimageorig; ?>" class="flyermodal" title="<?php echo $item->venue; ?>">
			<?php endif; ?>

				<img src="<?php echo $item->venueimage; ?>" alt="<?php echo $item->venue; ?>" class="image-preview" />

			<?php if ($item->venuelink) : ?>
			</a>
			<?php endif; } ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
