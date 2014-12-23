<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal', 'a.modal');

// Create shortcut to parameters.
$params = $this->state->get('params');
$params = $params->toArray();

# defining values for centering default-map
$location = JemHelper::defineCenterMap($this->form);
$mapType = $this->mapType;

// Define slides options
$slidesOptions = array(
		"useCookie" => "1"
);
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'venue.cancel' || document.formvalidator.isValid(document.id('venue-form'))) {
			Joomla.submitform(task, document.getElementById('venue-form'));
		}
	}

	window.addEvent('domready', function() {
		setAttribute();
		test();
	});

	function setAttribute(){
		jQuery("#tmp_form_postalCode").attr("geo-data", "postal_code");
		jQuery("#tmp_form_city").attr("geo-data", "locality");
		jQuery("#tmp_form_state").attr("geo-data", "administrative_area_level_1");
		jQuery("#tmp_form_street").attr("geo-data", "street_address");
		jQuery("#tmp_form_route").attr("geo-data", "route");
		jQuery("#tmp_form_streetnumber").attr("geo-data", "street_number");
		jQuery("#tmp_form_country").attr("geo-data", "country_short");
		jQuery("#tmp_form_latitude").attr("geo-data", "lat");
		jQuery("#tmp_form_longitude").attr("geo-data", "lng");
		jQuery("#tmp_form_venue").attr("geo-data", "name");	
	}

	function meta(){
		var f = document.getElementById('venue-form');
		if(f.jform_meta_keywords.value != "") f.jform_meta_keywords.value += ", ";
		f.jform_meta_keywords.value += f.jform_venue.value+', ' + f.jform_city.value;
	}

	function test(){			
			var form 		= document.getElementById('venue-form');
			var map 		= $('jform_map');
			var streetcheck = $(form.jform_street).hasClass('required');

			if(map && map.checked == true) {
				var lat = $('jform_latitude');
				var lon = $('jform_longitude');

				if(lat.value == ('' || 0.000000) || lon.value == ('' || 0.000000)) {
					if(!streetcheck) {
						addrequired();
					}
				} else {
					if(lat.value != ('' || 0.000000) && lon.value != ('' || 0.000000) ) {
						removerequired();
					}
				}
			}

			if(map && map.checked == false) {
				removerequired();
			}
	}

	function addrequired() {
		var form = document.getElementById('venue-form');

		jQuery(form.jform_street).addClass('required');
		jQuery(form.jform_postalCode).addClass('required');
		jQuery(form.jform_city).addClass('required');
		jQuery(form.jform_country).addClass('required');
	}

	function removerequired() {
		var form = document.getElementById('venue-form');

		jQuery(form.jform_street).removeClass('required');
		jQuery(form.jform_postalCode).removeClass('required');
		jQuery(form.jform_city).removeClass('required');
		jQuery(form.jform_country).removeClass('required');
	}

	jQuery(function() {

		var chkGeocode = function() {
		var chk = jQuery( "#geocode:checked" ).length;
			if (chk) {

				jQuery("#mapdiv").show();
				
				jQuery("#geocomplete").geocomplete({
					map: ".map_canvas",
					<?php echo $location; ?>
					details: "form ",
					detailsAttribute: "geo-data",
					types: ['establishment', 'geocode'],
					mapOptions: {
					      zoom: 16,
					      <?php echo 'mapTypeId:'.$mapType; ?>
					    },
					markerOptions: {
						draggable: true
					}
					
				});

				jQuery("#geocomplete").bind('geocode:result', function(){
						var street = jQuery("#tmp_form_street").val();
						var route  = jQuery("#tmp_form_route").val();
						
						if (route) {
							/* something to add */
						} else {
							jQuery("#tmp_form_street").val('');
						}
				});

				jQuery("#geocomplete").bind("geocode:dragged", function(event, latLng){
					jQuery("#tmp_form_latitude").val(latLng.lat());
					jQuery("#tmp_form_longitude").val(latLng.lng());
				});

				jQuery("#find-left").click(function() {
					jQuery("#geocomplete").val(jQuery("#jform_street").val() + ", " + jQuery("#jform_postalCode").val() + " " + jQuery("#jform_city").val());
					jQuery("#geocomplete").trigger("geocode");
				});

				jQuery("#cp-latlong").click(function() {
					document.getElementById("jform_latitude").value = document.getElementById("tmp_form_latitude").value;
					document.getElementById("jform_longitude").value = document.getElementById("tmp_form_longitude").value;
					test();
				});

				jQuery("#cp-address").click(function() {
					document.getElementById("jform_street").value = document.getElementById("tmp_form_street").value;
					document.getElementById("jform_postalCode").value = document.getElementById("tmp_form_postalCode").value;
					document.getElementById("jform_city").value = document.getElementById("tmp_form_city").value;
					document.getElementById("jform_state").value = document.getElementById("tmp_form_state").value;	
					document.getElementById("jform_country").value = document.getElementById("tmp_form_country").value;
				});

				jQuery("#cp-venue").click(function() {
					var venue = document.getElementById("tmp_form_venue").value;
					if (venue) {
						document.getElementById("jform_venue").value = venue;
					}
				});

				jQuery("#cp-all").click(function() {
					jQuery("#cp-address").click();
					jQuery("#cp-latlong").click();
					jQuery("#cp-venue").click();
				});	
			} else {
				jQuery("#mapdiv").hide();

				}
		};
		chkGeocode();


		jQuery("#geocode" ).on("click", chkGeocode );
		jQuery('#jform_map').on('keyup keypress blur change', test);
		jQuery('#jform_latitude').on('keyup keypress blur change', test);
		jQuery('#jform_longitude').on('keyup keypress blur change', test);
	});
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_jem&layout=edit&id='.(int) $this->item->id); ?>"
	class="form-validate" method="post" name="adminForm" id="venue-form" enctype="multipart/form-data">
<div class="form-horizontal">
<div class="span12">

<!-- Tabs -->	
	<div class="span8">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'venue-tab1','useCookie' => '1')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'venue-tab1', JText::_('COM_JEM_VENUE_INFO_TAB', true)); ?>
		<fieldset class="form-horizontal">
			<legend>
				<?php echo empty($this->item->id) ? JText::_('COM_JEM_NEW_VENUE') : JText::sprintf('COM_JEM_VENUE_DETAILS', $this->item->id); ?>
			</legend>
			<?php 
				echo $this->form->renderField('venue');
				echo $this->form->renderField('alias');
				echo $this->form->renderField('street');
				echo $this->form->renderField('postalCode');
				echo $this->form->renderField('city');
				echo $this->form->renderField('state');
				echo $this->form->renderField('country');
				echo $this->form->renderField('latitude');
				echo $this->form->renderField('longitude');
				echo $this->form->renderField('url');
				echo $this->form->renderField('map');
			?>
		</fieldset>
		
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('locdescription'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('locdescription'); ?></div>
			</div>
		</fieldset>

		<?php echo JHtml::_('bootstrap.endTab'); ?>

<!-- Attachments -->		
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'venue-attachments', JText::_('COM_JEM_EVENT_ATTACHMENTS_TAB', true)); ?>	
		<?php echo $this->loadTemplate('attachments'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		
		
		<?php echo JHtml::_('bootstrap.endTabSet');?>
</div>
<div class="span4">		
	
	<!--  start of sliders -->
	<?php echo JHtml::_('bootstrap.startAccordion', 'venue-sliders-'.$this->item->id, $slidesOptions); ?>

<!-- Publishing -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_FIELDSET_PUBLISHING'), 'venue-publishing'); ?>
		<fieldset class="form-vertical">
		<?php 
			echo $this->form->renderField('id');
			echo $this->form->renderField('published');
		?>
		<?php foreach($this->form->getFieldset('publish') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
		<?php endforeach; ?>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
	
		
<!-- CUSTOM -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_CUSTOMFIELDS'), 'venue-custom'); ?>
		<fieldset class="form-vertical">
				<?php foreach($this->form->getFieldset('custom') as $field): ?>
					<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
				<?php endforeach; ?>
		</fieldset>		
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
	
		
<!-- IMAGE -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_IMAGE'), 'venue-image'); ?>
		<fieldset class="form-vertical">
		<?php 
			echo $this->form->renderField('locimage');
		?>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		
		
<!-- Meta -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_METADATA_INFORMATION'), 'venue-meta'); ?>
		<fieldset class="form-vertical">
			<input type="button" class="btn" value="<?php echo JText::_('COM_JEM_ADD_VENUE_CITY'); ?>" onclick="meta()" />
				<?php foreach($this->form->getFieldset('meta') as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label; ?></div>
					<div class="controls"><?php echo $field->input; ?></div>
				</div>
				<?php endforeach; ?>
			
		</fieldset>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		

<!-- Geodata -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_FIELDSET_GEODATA'), 'venue-geodata'); ?>
		<fieldset class="form-vertical" id="geodata">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('geocode'); ?></div>
				<div class="controls"> <input type="checkbox" id="geocode" /></div>
			</div>
			
			<div class="clr"></div>
			<div id="mapdiv">
			
			<?php 
			# Google-map code will be loaded when the checkbox for geocoding has been ticked
			$language	= JFactory::getLanguage();
			$document	= JFactory::getDocument();
			
			$api		= trim($this->settings2->get('global_googleapi'));
			$clientid	= trim($this->settings2->get('global_googleclientid'));
			$language	= strtolower($language->getTag());
			
			# do we have a client-ID?
			if ($clientid) {
				$document->addScript('http://maps.googleapis.com/maps/api/js?client='.$clientid.'&sensor=false&libraries=places&language='.$language);
			} else {
				# do we have an api-key?
				if ($api) {
					$document->addScript('https://maps.googleapis.com/maps/api/js?key='.$api.'&sensor=false&libraries=places&language='.$language);
				} else {
					$document->addScript('https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&language='.$language);
				}
			}
			
			JHtml::_('stylesheet', 'com_jem/geostyle.css', array(), true);
			JHtml::_('script', 'com_jem/jquery.geocomplete.js', false, true);	
			?>
				<input id="geocomplete" type="text" size="55" placeholder="<?php echo JText::_( 'COM_JEM_VENUE_ADDRPLACEHOLDER' ); ?>" value="" />
				<input id="find-left" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_ADDR_FINDVENUEDATA');?>" />
				<div class="clr"></div>
				<div class="map_canvas"></div>

				
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_STREET'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_street" /></div>
						<input type="hidden" class="readonly" id="tmp_form_streetnumber" readonly="readonly" />
						<input type="hidden" class="readonly" id="tmp_form_route" readonly="readonly" />
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_ZIP'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_postalCode" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_CITY'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_city"/></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_STATE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_state" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_VENUE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_venue" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_COUNTRY'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_country" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_LATITUDE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_latitude" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_LONGITUDE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_longitude" /></div>
				</div>
				
				<div class="clr"></div>
				<input id="cp-all" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_DATA'); ?>" />
				<input id="cp-address" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_ADDRESS'); ?>" />
				<input id="cp-venue" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_VENUE'); ?>" />
				<input id="cp-latlong" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_COORDINATES'); ?>" />
			</div>
		</fieldset>
	
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="author_ip" value="<?php echo $this->item->author_ip; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</div></div>
	</div>
</form>