jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm_select');
    el.select2();
});

jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});