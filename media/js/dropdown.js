/**
 * @version 3.0.2
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * hide/show dropdown button
 */
jQuery(function() {
	var y = '';
	
	jQuery('.dropdown-menu li').each(function(){
		var html = jQuery(this).html();
		if (html) {
			y += '1';
		}
	});

	if (y == '') {
		jQuery('.dropdown-toggle').hide();
	}
});