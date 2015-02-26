/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * JavaScript behavior to allow selected collapse to be remained after save or page reload
 * keeping state in localstorage
 * 
 * @todo use id's for sliders + finetune
 */

jQuery(function() {

    var loadcollapse = function() {
        var $ = jQuery.noConflict();

        jQuery(document).find('a[data-toggle="collapse"]').on('click', function(e) {
            // Store the selected collapse href in localstorage
            window.localStorage.setItem('collapse-href', $(e.target).attr('href'));
        });

        var activatecollapse = function(href) {
            var $el = $('a[data-toggle="collapse"]a[href*=' + href + ']');
            var $el2 = $el.parent().parent().parent();
            var $el3 = $el2.find(".accordion-body");
            $el3.collapse('show');
        };

        var hascollapse = function(href){
            return $('a[data-toggle="collapse"]a[href*=' + href + ']').length;
        };

        if (localStorage.getItem('collapse-href')) {
            // When moving from collapse area to a different view
            if(!hascollapse(localStorage.getItem('collapse-href'))){
                localStorage.removeItem('collapse-href');
                return true;
            }
            // Clean default collapse
            $('a[data-toggle="collapse"]').parent().removeClass('in');
            var collapsehref = localStorage.getItem('collapse-href');
            // Add active attribute for selected collapse indicated by url
            activatecollapse(collapsehref);
            // Check whether internal collapse is selected (in format <collapsename>-<id>)
            var seperatorIndex = collapsehref.indexOf('-');
            if (seperatorIndex !== -1) {
                var singular = collapsehref.substring(0, seperatorIndex);
                var plural = singular + "s";
                activatecollapse(plural);
            }
        }
    };
    setTimeout(loadcollapse, 100);

});
