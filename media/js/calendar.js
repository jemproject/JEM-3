/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

function calendar() {
	
	/* define background */
	var bgoption	= jQuery('#usebgcatcolor').val();
	if (bgoption == 1) {
		jQuery(".jlcalendar .eventcontent").each(function(index, element ) {
			
			// check if we're dealing with a multi-cat event
			var bg_class 			= jQuery(this).children().attr('class');
			var bg_array_classes	= bg_class.split(" ").sort();
			var bg_count_array		= bg_array_classes.length;
			
			if (bg_count_array == 1) {
				var bg_nr		= bg_class.replace( /^\D+/g, '');
				var bg_color	= jQuery('input[name=category'+bg_nr+']').val();
		
				jQuery(this).children().children("span.colorpic").each(function() {
					jQuery(this).remove();	
				}); // end of each	
				
				jQuery(this).css('background-color',bg_color);
				jQuery(this).children().css('background-color',bg_color);
			}
		}); // end of each
	}
	
	/* Button: category */
	jQuery(".eventCat").each(function(index, element ) {
		// attach click Event to the category-button
		jQuery(this).on("click", function(){
			
			jQuery(this).toggleClass('catoff');
					
			var catid = jQuery(this).attr('id');
		
			// add action to every event with a class that has the same name as the category-button
				jQuery('.jlcalendar .'+jQuery(this).attr('id')).each(
						function(eventcat,element2) {
							
							//###################
							//  reorder values //
							//##################
							
							// to match/check we've to make some changes
							var attr_class			= jQuery(this).attr("class");
							var attr_hidecat		= jQuery(this).attr("hidecat");
							var array_classes		= attr_class.split(" ").sort();
							var array_hidecat		= attr_hidecat.split(" ").sort();
							
							var new_array_classes 	= array_classes.join(' ');
							var new_array_hidecat	= array_hidecat.join(' ');
							
							
							// set new values
							// at this point the values for hidecat/class have been reordered
							jQuery(this).attr("class",new_array_classes);
							jQuery(this).attr("hidecat",new_array_hidecat);
									
							// retrieve/define variables to use
							var val_attr_class		= jQuery(this).attr("class");
							var val_attr_hidecat	= jQuery(this).attr("hidecat");
							var val_array_classes	= val_attr_class.split(" ");
							var val_array_hidecat	= val_attr_hidecat.split(" ");
							var val_count_classes	= val_array_classes.length;
										
							if (val_count_classes > 1) {
								// oh no, we are a multi-cat
									
								// ###########
								//   INLINE
								// ########### 
								
								// check if showcat is inline with class
								if (val_attr_hidecat == val_array_classes) {
									// we're inline, in this case the event is hidden and the current cat is within the hidecat attribute
									
									// let's show the event
									jQuery(this).parent().show();
									
									// now remove the current cat from the hidecat attribute
									var new_hidecat_value	=	val_attr_hidecat.replace(catid,'');
									jQuery(this).attr('hidecat',jQuery.trim(new_hidecat_value));
									
									
									
								} else {
									// we're not inline, check if current catid is within the hidecat attribute.
									//--> if not add it to it
									if (jQuery.inArray(catid, val_array_hidecat) == -1){
										jQuery(this).attr('hidecat',jQuery.trim(val_attr_hidecat+' '+catid));
										
										// check if class/hidecat are inline, if so hide the event
										var chk_attr_class			= jQuery(this).attr("class");
										var chk_attr_hidecat		= jQuery(this).attr("hidecat");
										var chk_array_classes		= chk_attr_class.split(" ").sort();
										var chk_array_hidecat		= chk_attr_hidecat.split(" ").sort();
										
										var chk_new_array_classes 	= chk_array_classes.join(' ');
										var chk_new_array_hidecat	= chk_array_hidecat.join(' ');
										
										if (chk_new_array_classes == chk_new_array_hidecat) {
											jQuery(this).parent().hide();
										} else {
											jQuery(this).parent().show();
										}
										
									} else {
										// in this case we need to remove the cat class from the hidecat attribute
										var new_hidecat_value	=	val_attr_hidecat.replace(catid,'');
										jQuery(this).attr('hidecat',jQuery.trim(new_hidecat_value));
										
										// check if class/hidecat are inline, if so hide the event
										var chk_attr_class			= jQuery(this).attr("class");
										var chk_attr_hidecat		= jQuery(this).attr("hidecat");
										var chk_array_classes		= chk_attr_class.split(" ").sort();
										var chk_array_hidecat		= chk_attr_hidecat.split(" ").sort();
										
										var chk_new_array_classes 	= chk_array_classes.join(' ');
										var chk_new_array_hidecat	= chk_array_hidecat.join(' ');
										
										if (chk_new_array_classes == chk_new_array_hidecat) {
											jQuery(this).parent().hide();
										} else {
											jQuery(this).parent().show();
										}
										
									}
									
								}
								
								
							} else {
								// we have 1 class and we consider it as a single-cat
								jQuery(this).parent().toggle();
							}
						});
			});	
	});
	
	
	/* Button: "Show all"  */
	jQuery('#buttonshowall').on('click', function() {
		// event-class
		jQuery(".jlcalendar .eventcontent").each(function(index, element ) {
			jQuery(this).show();
			
			// we have to clear the attribute of the child to none, this a we want to display all events. 
			jQuery(this).children("div").each(function() {
				jQuery(this).attr('hidecat','');
			});
		});
		
		// category-class
		jQuery("#jlcalendarlegend .eventCat").each(function(index, element ) {
			jQuery(this).removeClass('catoff'); 
		});
		
	});

	
	/* Button: "Hide all" */
	jQuery('#buttonhideall').on('click', function() {
		
		// event-class
		jQuery(".jlcalendar .eventcontent").each(function(index, element ) {
			jQuery(this).hide();
			
			// we want to hide all events and this case we're filling the hidecat attribute with data of the class attribute. 
			jQuery(this).children("div").each(function() {
				
				// get value of class value
				var $class_value	= jQuery(this).attr('class');
				
				// copy to hidecat
				jQuery(this).attr('hidecat',$class_value);
				
			}); // end of each	
		}); // end of each
		
		// category-class
		jQuery("#jlcalendarlegend .eventCat").each(function(index, element ) {
			jQuery(this).addClass('catoff');
		});
	});
};