<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


?>
<div id="jem" class="jem_venues<?php echo $this->pageclass_sfx;?>">
<div class="topbox">
<div class="btn-group pull-right">

	<?php
	if ($this->print) {
		echo JemOutput::printbutton($this->print_link, $this->params);
	} else {
	?>
	<div class="button_flyer icons">
		<?php
			echo JemOutput::addvenuebutton($this->addvenuelink, $this->params, $this->jemsettings);
			echo JemOutput::submitbutton($this->addeventlink, $this->params);
			echo JemOutput::printbutton($this->print_link, $this->params);
		?>
	</div>
			<?php } ?>
		</div>
</div>
<div class="clearfix"></div>
<!-- info -->
<div class="info_container">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
	<div class="clr"> </div>

	<?php foreach($this->rows as $row) : ?>

		<?php
			// Create image information
			$row->limage = JEMImage::flyercreator($row->locimage, 'venue');

			//Generate Venuedescription
			if (!$row->locdescription == '' || !$row->locdescription == '<br />') {
				//execute plugins
				$row->text	= $row->locdescription;
				$row->title = $row->venue;
				JPluginHelper::importPlugin('content');
				$this->app->triggerEvent('onContentPrepare', array('com_jem.venue', &$row, &$params, 0));
				$row->locdescription = $row->text;
			}

			//prepare the url for output
			// TODO: Should be part of view! Then use $this->escape()
			if (strlen($row->url) > 35) {
				$row->urlclean = htmlspecialchars(substr($row->url, 0 , 35)).'...';
			} else {
				$row->urlclean = htmlspecialchars($row->url);
			}

			//create flag
			if ($row->country) {
				$row->countryimg = JemHelperCountries::getCountryFlag($row->country);
			}
		?>

	<div itemscope="itemscope" itemtype="http://schema.org/Place">
		<h2 class="jem">
			<a href="<?php echo $row->linkEventsPublished; ?>" itemprop="url"><span itemprop="name"><?php echo $this->escape($row->venue); ?></span></a>
		</h2>

	<?php if ($row->limage) { ?>
		<div class="image imagetop"><?php echo JemOutput::flyer( $row, $row->limage, 'venue' ); ?></div>
	<?php } ?>


		<!--  -->
		<dl class="location" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<?php if (!empty($row->url)) : ?>
			<dt class="venue_website">
				<?php echo JText::_('COM_JEM_WEBSITE').':'; ?>
			</dt>
			<dd class="venue_website">
				<a href="<?php echo $row->url; ?>" target="_blank"> <?php echo $row->urlclean; ?></a>
			</dd>
			<?php endif; ?>

		<?php if ( $this->vsettings->get('show_detailsadress',1)) : ?>
				<?php if ($row->street) : ?>
				<dt class="venue_street">
					<?php echo JText::_('COM_JEM_STREET').':'; ?>
				</dt>
				<dd class="venue_street" itemprop="streetAddress">
					<?php echo $this->escape($row->street); ?>
				</dd>
				<?php endif; ?>

				<?php if ($row->postalCode) : ?>
				<dt class="venue_postalCode">
					<?php echo JText::_('COM_JEM_ZIP').':'; ?>
				</dt>
				<dd class="venue_postalCode" itemprop="postalCode">
					<?php echo $this->escape($row->postalCode); ?>
				</dd>
				<?php endif; ?>

				<?php if ($row->city) : ?>
				<dt class="venue_city">
					<?php echo JText::_('COM_JEM_CITY').':'; ?>
				</dt>
				<dd class="venue_city" itemprop="addressLocality">
					<?php echo $this->escape($row->city); ?>
				</dd>
				<?php endif; ?>

				<?php if ($row->state) : ?>
				<dt class="venue_state">
					<?php echo JText::_('COM_JEM_STATE').':'; ?>
				</dt>
				<dd class="venue_state" itemprop="addressRegion">
					<?php echo $this->escape($row->state); ?>
				</dd>
				<?php endif; ?>

				<?php if ($row->country) : ?>
				<dt class="venue_country">
					<?php echo JText::_('COM_JEM_COUNTRY').':'; ?>
				</dt>
				<dd class="venue_country">
					<?php echo $row->countryimg ? $row->countryimg : $row->country; ?>
					<meta itemprop="addressCountry" content="<?php echo $row->country; ?>" />
				</dd>
				<?php endif; ?>

				<?php if ($this->vsettings->get('show_mapserv') == 1) : ?>
					<?php echo JemOutput::mapicon($row,null,$this->vsettings); ?>
				<?php endif; ?>
				<dt class="venue_eventspublished">
					<?php echo JText::_('COM_JEM_VENUES_EVENTS_PUBLISHED').':'; ?>
				</dt>
				<dd class="venue_eventspublished">
					<a href="<?php echo $row->linkEventsPublished; ?>"><?php echo $row->EventsPublished; ?></a>
				</dd>
				<dt class="venue_archivedevents">
					<?php echo JText::_('COM_JEM_VENUES_EVENTS_ARCHIVED').':'; ?>
				</dt>
				<dd class="venue_archivedevents">
					<a href="<?php echo $row->linkEventsArchived; ?>"><?php echo $row->EventsArchived; ?></a>
				</dd>
			<?php if ($this->vsettings->get('show_mapserv') == 2) : ?>
				<?php echo JemOutput::mapicon($row,null,$this->vsettings); ?>
			<?php endif; ?>
		<?php endif; ?>
		</dl>


		<?php if ($this->vsettings->get('show_mapserv')== 3) : ?>
			<input type="hidden" id="latitude" value="<?php echo $row->latitude;?>">
			<input type="hidden" id="longitude" value="<?php echo $row->longitude;?>">

			<input type="hidden" id="venue" value="<?php echo $row->venue;?>">
			<input type="hidden" id="street" value="<?php echo $row->street;?>">
			<input type="hidden" id="city" value="<?php echo $row->city;?>">
			<input type="hidden" id="state" value="<?php echo $row->state;?>">
			<input type="hidden" id="postalCode" value="<?php echo $row->postalCode;?>">
		<?php echo JemOutput::mapicon($row,'venues',$this->vsettings); ?>
	<?php endif; ?>


		<?php if ($this->vsettings->get('show_locdescription',1) && $row->locdescription != '' && $row->locdescription != '<br />') : ?>
			<h2 class="description">
				<?php echo JText::_('COM_JEM_VENUE_DESCRIPTION').':'; ?>
			</h2>
			<div class="description" itemprop="description">
				<?php echo $row->locdescription; ?>
			</div>
		<?php endif; ?>
		</div> <!-- // end itemscope -->
		<?php endforeach; ?>
	</div> <!-- infocontainer -->

	<!--pagination-->
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<!--copyright-->
	<div class="poweredby">
		<?php echo JemOutput::footer( ); ?>
	</div>
</div>