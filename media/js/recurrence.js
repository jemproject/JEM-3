/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

jQuery(function() {
		checkRecurrence();
		
		jQuery( "#jform_recurrence_freq" ).change(function() {
			checkRecurrence();
		});
});


		function checkRecurrence() {

			var count 		= jQuery("#jform_recurrence_count"); 
			var exdates		= jQuery("#jform_recurrence_exdates");
			var freq 		= jQuery("#jform_recurrence_freq option:selected").index();
			var interval 	= jQuery("#jform_recurrence_interval"); 
			var weekday 	= jQuery("#jform_recurrence_weekday"); 
			var wkstrt 		= jQuery("#jform_recurrence_selectlist_weekdaystart"); 
			var until		= jQuery("#jform_recurrence_until");

			/* define group */
			var countgroup		= count.parent().parent(".control-group");
			var exdatesgroup	= exdates.parent().parent(".control-group");
			var intgroup 		= interval.parent().parent(".control-group");
			var untilgroup		= until.parent().parent().parent(".control-group");
			var wkgroup			= weekday.parent().parent(".control-group");
			var wkstartgroup	= wkstrt.parent().parent(".control-group");

			switch (freq) {
			    case 1: // daily
			    	countgroup.hide();
			    	exdatesgroup.show();
			        intgroup.show();
			        untilgroup.show();
			        wkgroup.hide();
					wkstartgroup.hide();
					wkgroup.show();
			        break;
			    case 2: // weekly
			    	countgroup.hide();
			    	exdatesgroup.show();
			    	intgroup.show();
			    	untilgroup.show();
			    	wkgroup.hide();
			    	wkstartgroup.hide();
			    	wkgroup.show();
			        break;
			    case 3: // monthly
			    	countgroup.hide();
			    	exdatesgroup.show();
			    	intgroup.show();
			    	untilgroup.show();
			    	wkgroup.hide();
			    	wkstartgroup.hide();
			    	wkgroup.show();
			        break; 
			    case 4: // yearly
			    	countgroup.hide();
			    	exdatesgroup.show();
			    	intgroup.show();
			    	untilgroup.show();
			    	wkgroup.hide();
			    	wkstartgroup.hide();
			    	wkgroup.show();
			        break; 
			    default:
				    count.val('');
			    	exdates.val('');
			    	interval.val('1');
			    	until.val('');
			    	weekday.val('');
			    	countgroup.hide();
			    	exdatesgroup.hide();
			    	intgroup.hide();
			    	untilgroup.hide();
			    	wkgroup.hide();
					wkstartgroup.hide();
			}

		}
