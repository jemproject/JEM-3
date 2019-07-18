<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$params = $this->params;
?>
<div id="jem" class="jem_eventslist<?php echo $this->pageclass_sfx;?>">

<div class="topbox">
	<div class="btn-group pull-right">

	<?php
	if ($this->print) {
		echo JemOutput::printbutton($this->print_link, $params);
	} else {
?>
	<div class="button_flyer icons">
		<?php
			echo JemOutput::submitbutton($this->submitEventIcon, $params);
			echo JemOutput::addvenuebutton($this->submitVenueIcon, $params);
			echo JemOutput::archivebutton($params, $this->task);
			echo JemOutput::printbutton($this->print_link, $params);
		?>
	</div>


<?php } ?>
	</div>
</div>
<!-- info -->
<div class="info_container">
	<?php if ($params->get('show_page_heading', 1)) : ?>
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<div class="clearfix"></div>

	<?php if ($params->get('showintrotext')) : ?>
		<div class="description no_space clearfix">
			<?php echo $params->get('introtext'); ?>
		</div>
	<?php endif; ?>

<!--table-->

		<?php echo $this->loadTemplate('events_table'); ?>



	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

<!--footer-->
	<?php if ($params->get('showfootertext')) : ?>
		<div class="description no_space clearfix">
			<?php echo $params->get('footertext'); ?>
		</div>
	<?php endif; ?>

	<br/>

</div>


	<div id="iCal" class="iCal">
			<?php echo JemOutput::icalbutton('', 'eventslist'); ?>
	</div>
	<div class="poweredby">
			<?php echo JemOutput::footer( ); ?>
	</div>
</div>