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
JHtml::_('behavior.tabstate');

// Create shortcut to parameters.
$params		= $this->item->params;
//$settings = json_decode($this->item->attribs);

# defining values for centering default-map
$location = JemHelper::defineCenterMap($this->form);
$mapType = $this->mapType;
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'editvenue.cancel' || document.formvalidator.isValid(document.id('venue-form'))) {
			Joomla.submitform(task, document.getElementById('venue-form'));
		}
	}
</script>
<script type="text/javascript">
jQuery(function() {
		setAttribute();
		test();

		jQuery("#inputmeta").click(function() {
			var city		= jQuery("#jform_city");
			var venue		= jQuery("#jform_venue");
			var keywords	= jQuery("#jform_meta_keywords");

			if (keywords.val() != "") {
				keywords.append(", ");
			}

			if (venue.val() == "") {
				keywords.append(venue.val());
			}

			if (city.val() == "") {
				keywords.append(venue.val());
			}

			if (city.val() != "" && venue.val() != "") {
				keywords.append(venue.val()+', '+city.val());
			}
		})
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

	function test(){			
		var form 		= document.getElementById('venue-form');
		var map = jQuery('#jform_map');
		var streetcheck = jQuery(form.jform_street).hasClass('required');

		if(map && map.checked == true) {
			var lat = jQuery('#jform_latitude');
			var lon = jQuery('#jform_longitude');

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

	function addrequired(){
		var form = jQuery('#venue-form');

		jQuery(form.jform_street).addClass('required');
		jQuery(form.jform_postalCode).addClass('required');
		jQuery(form.jform_city).addClass('required');
		jQuery(form.jform_country).addClass('required');
	}

	function removerequired(){
		var form = document.getElementById('venue-form');

		jQuery(form.jform_street).removeClass('required');
		jQuery(form.jform_postalCode).removeClass('required');
		jQuery(form.jform_city).removeClass('required');
		jQuery(form.jform_country).removeClass('required');
	}
	
	jQuery(function(){
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
			jQuery("#jform_latitude").val(jQuery("#tmp_form_latitude").val());
			jQuery("#jform_longitude").val(jQuery("#tmp_form_longitude").val());
			test();
		});

		jQuery("#cp-address").click(function() {
			jQuery("#jform_street").val(jQuery("#tmp_form_street").val());
			jQuery("#jform_postalCode").val(jQuery("#tmp_form_postalCode").val());
			jQuery("#jform_city").val(jQuery("#tmp_form_city").val());
			jQuery("#jform_state").val(jQuery("#tmp_form_state").val());	
			jQuery("#jform_country").val(jQuery("#tmp_form_country").val());
		});

		jQuery("#cp-venue").click(function() {
			var venue = jQuery("#tmp_form_venue").val();
			if (venue) {
				jQuery("#jform_venue").val(venue);
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

<div id="jem" class="jem_editvenue<?php echo $this->pageclass_sfx; ?>">
	<div class="edit item-page">
		<form action="<?php echo JRoute::_('index.php?option=com_jem&a_id='.(int) $this->item->id); ?>" class="form-validate" method="post" name="adminForm" id="venue-form" enctype="multipart/form-data">
			
<div class="topbox">
<div class="btn-group pull-left">
<?php echo JEMOutput::statuslabel($this->item->published); ?>
</div>
	<div class="button_flyer">
		<div class="btn-toolbar">	
			<?php if (JFactory::getUser()->authorise('core.manage', 'com_jem')) { ?>
				<button type="button" class="btn btn-small btn-success" onclick="Joomla.submitbutton('editvenue.apply')"><span class="icon-apply icon-white"></span><?php echo ' '.JText::_('JSAVE') ?></button>
				<button type="button" class="btn btn-small" onclick="Joomla.submitbutton('editvenue.save')"><span class="icon-save"></span><?php echo ' '.JText::_('Save & Close') ?></button>
			<?php } else { ?>
				<button type="button" class="btn btn-small btn-success" onclick="Joomla.submitbutton('editvenue.save')"><span class="icon-save"></span><?php echo ' '.JText::_('JSAVE') ?></button>
			<?php } ?>
			<button type="button" class="btn btn-small" onclick="Joomla.submitbutton('editvenue.cancel')"><span class="icon-cancel icon-red"></span><?php echo ' '.JText::_('JCANCEL') ?></button>
		</div>
	</div>
</div>
	
		<?php if ($params->get('show_page_heading')) : ?>
		<h1>
		<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
		<?php endif; ?>

		<div class="clr"> </div>
		
			<?php if ($this->params->get('showintrotext')) : ?>
			<div class="description no_space clearfix">
				<?php echo $this->params->get('introtext'); ?>
			</div>
			<?php endif; ?>
			<p>&nbsp;</p>

<!-- TABS -->
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JEM_EDITVENUE_INFO_TAB', true)); ?>	

			<fieldset class="form-horizontal">
				<legend><?php echo JText::_('COM_JEM_EDITVENUE_DETAILS_LEGEND'); ?></legend>
					<?php 
					echo $this->form->renderField('venue'); 
					if (is_null($this->item->id)) {
						echo $this->form->renderField('alias');

					 } 
					 echo $this->form->renderField('street');
					 echo $this->form->renderField('postalCode');
					 echo $this->form->renderField('city');
					 echo $this->form->renderField('state');
					 echo $this->form->renderField('country');
					 echo $this->form->renderField('latitude');
					 echo $this->form->renderField('longitude');
					 echo $this->form->renderField('url');
					 echo $this->form->renderField('published');
					 echo $this->form->renderField('map');
					 ?>
			</fieldset>
			
			<fieldset class="form-vertical">
			<div class="clr"></div>
					<?php echo $this->form->getLabel('locdescription'); ?>
					<div class="clr"><br /></div>
					<?php echo $this->form->getInput('locdescription'); ?>
			
			</fieldset>
			
			<!-- VENUE-GEODATA-->
			<fieldset class="form-horizontal" id="geodata">
			<legend><?php echo JText::_('COM_JEM_FIELDSET_GEODATA'); ?></legend>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('geocode'); ?></div>
				<div class="controls"> <input type="checkbox" id="geocode" /></div>
			</div>
			
			<div class="clr"></div>
			<div id="mapdiv">
			<?php 
			# Google-map code
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
				<input id="geocomplete" type="text" size="55" placeholder="<?php echo JText::_('COM_JEM_VENUE_ADDRPLACEHOLDER'); ?>" value="" />
				<input id="find-left" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_ADDR_FINDVENUEDATA');?>" />
				<div class="clr"></div>
				<div class="map_canvas"></div>

				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_STREET'); ?></label></div>
						<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_street" readonly="readonly" /></div>
					
						<input type="hidden" class="readonly" id="tmp_form_streetnumber" readonly="readonly" />
						<input type="hidden" class="readonly" id="tmp_form_route" readonly="readonly" />
					</div>
						
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_ZIP'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_postalCode" readonly="readonly" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_CITY'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_city" readonly="readonly" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_STATE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_state" readonly="readonly" /></div>
				</div>
				<div class="control-group">		
					<div class="control-label"><label><?php echo JText::_('COM_JEM_VENUE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_venue" readonly="readonly" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_COUNTRY'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_country" readonly="readonly" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_LATITUDE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_latitude" readonly="readonly" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_LONGITUDE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_longitude" readonly="readonly" /></div>
				</div>
				
				<div class="clr"></div>
				<input id="cp-all" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_DATA'); ?>" style="margin-right: 3em;" />
				<input id="cp-address" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_ADDRESS'); ?>" />
				<input id="cp-venue" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_VENUE'); ?>" />
				<input id="cp-latlong" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_COORDINATES'); ?>" />
			</div>
		</fieldset>

			<!-- META -->
			<fieldset class="form-horizontal">
				<legend><?php echo JText::_('COM_JEM_META_HANDLING'); ?></legend>
					<input id="inputmeta" type="button" class="btn" value="<?php echo JText::_('COM_JEM_ADD_VENUE_CITY'); ?>" />
					<?php foreach($this->form->getFieldset('meta') as $field): ?>
					<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
					<?php endforeach; ?>
			</fieldset>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

<!-- ATTACHMENTS TAB -->
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'attachments', JText::_('COM_JEM_EDITVENUE_ATTACHMENTS_TAB', true)); ?>
			<?php echo $this->loadTemplate('attachments'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			

			<!-- OTHER TAB -->
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'other', JText::_('COM_JEM_EDITVENUE_OTHER_TAB', true)); ?>
			<?php echo $this->loadTemplate('other'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php 
			echo JHtml::_('bootstrap.endTabSet'); 
			?>

			<div class="clearfix"></div>
			<input id="country" name="country" geo-data="country_short" type="hidden" value="">
			<input type="hidden" name="author_ip" value="<?php echo $this->item->author_ip; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>