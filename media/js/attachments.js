/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * this file manages the js script for adding/removing attachements in event
 */
jQuery(function() {

	jQuery('.attach-field').change(addattach);
	jQuery('.clear-attach-field').click(clearattach);

	jQuery('.attach-remove').click(function(event){
		var event = event || window.event;
		jQuery(event.target).css('cursor','wait'); /* indicate server request */

		var id = event.target.id.substr(13);
		var url = 'index.php?option=com_jem&task=ajaxattachremove&format=raw&id='+id;
		
		jQuery.ajax({
			type: "POST",
			url: url
			})
			.done(function( msg ) {
				// server sends 1 on success, 0 on error 
				if (msg.indexOf('1') > -1) {
					jQuery(event.target).parent().parent().remove();
				} else {
					jQuery(event.target).css('cursor','not-allowed'); // remove failed - how to show?
				}

			});
	});
});

function addattach()
{
	var tbody = $('el-attachments').getElement('tbody');
	var rows = tbody.getElements('tr');
	var emptyRows = [];
	
	/* do we have empty rows? */
	for(var i = 0; i < rows.length; i++) {
		var af = rows[i].getElement('.attach-field');
		if (af && !(af.files.length > 0)) {
			emptyRows.push(af);
			break; /* one is enough, so we can break */
		}
	};

	/* if not create one */
	if (emptyRows.length < 1) {
		var row = rows[rows.length-1].clone();
		row.getElement('.attach-field').addEvent('change', addattach).value = '';
		row.getElement('.attach-name').value = '';
		row.getElement('.attach-desc').value = '';
		row.getElement('.clear-attach-field').addEvent('click', clearattach).value = '';
		row.inject(tbody);
	}
}

function clearattach(event) {
	var event = event || window.event;

	var grandpa = jQuery(event.target).parent().parent();
	var af = grandpa.find('.attach-field');
	if (af) af.val('');
	var an = grandpa.find('.attach-name');
	if (an) an.val('');
	var ad = grandpa.find('.attach-desc');
	if (ad) ad.val('');
}