jQuery(window).on('devm-scripts.multiSelect', function(){
    var el = jQuery('.devm-option.active-script .devm_multi_select');

    if (el.length) {
        el.select2({multiple:true});
    }
});


jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.multiSelect');
});