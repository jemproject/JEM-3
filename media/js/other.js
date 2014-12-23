/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * this file manages the js script for adding/removing attachments in event
 */
 jQuery(function() {
 	jQuery('#userfile-remove').click(alterdata);
 });
 
 function alterdata() {
	var di = jQuery('#hide_image');
	if (di) { di.hide(); }
	var ufr = jQuery('#userfile-remove');
	if (ufr) { ufr.hide(); }
	var ri = jQuery('#removeimage');
	if (ri) { ri.val('1'); }
 }