jQuery(window).on('dm-scripts.multiSelect', function(){
    var el = jQuery('.dm-option.active-script .dm_multi_select');

    if (el.length) {
        el.select2({multiple:true});
    }
});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.multiSelect');
});