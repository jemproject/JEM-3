<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
$mapType = $this->mapType;
?>

<div id="jem" class="jem_venue<?php echo $this->pageclass_sfx;?>" itemscope="itemscope" itemtype="http://schema.org/Place">
<div class="topbox">	
	<div class="btn-group pull-left">
		<?php echo JEMOutput::statuslabel($this->venue->published); ?>
	</div>
	<div class="btn-group pull-right hidden-phone">
	<?php 
	if ($this->print) { 
		echo JemOutput::printbutton($this->print_link, $this->params);
	} else {
	?>
	<div class="button_flyer icons">
	<?php 
		echo JemOutput::printbutton($this->print_link, $this->params);
		echo JemOutput::mailbutton($this->venue->slug, 'venue', $this->params);
		echo JemOutput::submitbutton($this->addeventlink, $this->params);
		echo JemOutput::addvenuebutton($this->addvenuelink, $this->params, $this->jemsettings);
		echo JemOutput::archivebutton($this->params, $this->task, $this->venue->slug);
	?>
	</div>		
	<?php } ?>			
	</div>
</div>
<div class="clearfix"></div>
<!-- info -->
<div class="info_container">	
	
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
			<span itemprop="name"><?php echo $this->escape($this->params->get('page_heading')); ?></span>
		</h1>
	</div>
	<?php endif; ?>
	
<!--Venue-->
		

	<h2 class="jem">
			<?php echo JText::_('COM_JEM_DETAILS'); ?>
			<?php echo JemOutput::editbutton($this->venue, $this->params, NULL, $this->allowedtoeditvenue, 'venue'); ?>
	</h2>
	
	<?php if ($this->limage) { ?>
		<div class="image imagetop"><?php echo JemOutput::flyer($this->venue, $this->limage, 'venue'); ?></div>
	<?php } ?>

	<div class="container-fluid">
	<div class="row">
	
	<div class="col-md-7">	
		<dl class="location" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
	<?php if (($this->vsettings->get('show_detlinkvenue',1)) && (!empty($this->venue->url))) : ?>
			<dt class="venue"><?php echo JText::_('COM_JEM_WEBSITE').':'; ?></dt>
			<dd class="venue">
				<a href="<?php echo $this->venue->url; ?>" target="_blank"><?php echo $this->venue->urlclean; ?></a>
			</dd>
	<?php endif; ?>
	<?php if ($this->vsettings->get('show_detailsadress',1)) : ?>
			<?php if ($this->venue->street) : ?>
			<dt class="venue_street"><?php echo JText::_('COM_JEM_STREET').':'; ?></dt>
			<dd class="venue_street" itemprop="streetAddress">
				<?php echo $this->escape($this->venue->street); ?>
			</dd>
			<?php endif; ?>

			<?php if ($this->venue->postalCode) : ?>
			<dt class="venue_postalCode"><?php echo JText::_('COM_JEM_ZIP').':'; ?></dt>
			<dd class="venue_postalCode" itemprop="postalCode">
				<?php echo $this->escape($this->venue->postalCode); ?>
			</dd>
			<?php endif; ?>

			<?php if ($this->venue->city) : ?>
			<dt class="venue_city"><?php echo JText::_('COM_JEM_CITY').':'; ?></dt>
			<dd class="venue_city" itemprop="addressLocality">
				<?php echo $this->escape($this->venue->city); ?>
			</dd>
			<?php endif; ?>

			<?php if ($this->venue->state) : ?>
			<dt class="venue_state"><?php echo JText::_('COM_JEM_STATE').':'; ?></dt>
			<dd class="venue_state" itemprop="addressRegion">
				<?php echo $this->escape($this->venue->state); ?>
			</dd>
			<?php endif; ?>

			<?php if ($this->venue->country) : ?>
			<dt class="venue_country"><?php echo JText::_('COM_JEM_COUNTRY').':'; ?></dt>
			<dd class="venue_country">
				<?php echo $this->venue->countryimg ? $this->venue->countryimg : $this->venue->country; ?>
				<meta itemprop="addressCountry" content="<?php echo $this->venue->country; ?>" />
			</dd>
			<?php endif; ?>


			<?php
			for($cr = 1; $cr <= 10; $cr++) {
				$currentRow = $this->venue->{'custom'.$cr};
				if(preg_match('%^https?://[^\s]+$%', $currentRow)) {
					$currentRow = '<a href="'.$this->escape($currentRow).'" target="_blank">'.$this->escape($currentRow).'</a>';
	 			}
				if($currentRow) {
				?>
				<dt class="custom<?php echo $cr; ?>"><?php echo JText::_('COM_JEM_VENUE_CUSTOM_FIELD'.$cr).':'; ?></dt>
				<dd class="custom<?php echo $cr; ?>"><?php echo $currentRow; ?></dd>
				<?php
				}
			}
			?>

			<?php
			if ($this->vsettings->get('show_mapserv')== 1) {
				echo JemOutput::mapicon($this->venue,'venue',$this->vsettings);
			}
			endif;
			?>
		</dl>
	</div>
	
	<div class="col-md-5">
		
	</div> 	   
	 	   
	 	  
	</div>	
	</div> <!-- row-fluid -->
	<?php
		if ($this->vsettings->get('show_mapserv')== 2) {
			echo JemOutput::mapicon($this->venue,'venue',$this->vsettings);
		}
		?>
	<?php if ($this->vsettings->get('show_mapserv')== 3) : ?>			
			<input type="hidden" id="latitude" value="<?php echo $this->venue->latitude;?>">
			<input type="hidden" id="longitude" value="<?php echo $this->venue->longitude;?>">
			
			<input type="hidden" id="venue" value="<?php echo $this->venue->venue;?>">
			<input type="hidden" id="street" value="<?php echo $this->venue->street;?>">
			<input type="hidden" id="city" value="<?php echo $this->venue->city;?>">
			<input type="hidden" id="state" value="<?php echo $this->venue->state;?>">
			<input type="hidden" id="postalCode" value="<?php echo $this->venue->postalCode;?>">
			<input type="hidden" id="mapType" value="<?php echo $this->mapType;?>">
		<?php echo JemOutput::mapicon($this->venue,'venue',$this->vsettings); ?>			
	<?php endif; ?>
	

	<?php if ($this->vsettings->get('show_locdescription',1) && $this->venuedescription != '' &&
	          $this->venuedescription != '<br />') : ?>

		<h2 class="description"><?php echo JText::_('COM_JEM_VENUE_DESCRIPTION'); ?></h2>
		<div class="description no_space clearfix" itemprop="description">
			<?php echo $this->venuedescription; ?>
		</div>
	<?php endif; ?>

	<?php $this->attachments = $this->venue->attachments; ?>
	<?php echo $this->loadTemplate('attachments'); ?>

	<!--table-->
	<form action="<?php echo $this->action; ?>" method="post" id="adminForm">
		<?php echo $this->loadTemplate('table'); ?>

		<p>
		<input type="hidden" name="option" value="com_jem" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<input type="hidden" name="view" value="venue" />
		<input type="hidden" name="id" value="<?php echo $this->venue->id; ?>" />
		</p>
	</form>
	
</div>

	<!--pagination-->
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<?php echo JemOutput::icalbutton($this->venue->id, 'venue'); ?>

	<!--copyright-->
	<div class="poweredby">
		<?php echo JemOutput::footer( ); ?>
	</div>
</div>
