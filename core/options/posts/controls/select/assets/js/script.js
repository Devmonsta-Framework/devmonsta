jQuery(window).on('dm-scripts.select', function(){
    var el = jQuery('.dm-option.active-script .dm_select');
    el.select2();
});

jQuery(document).ready(function($) {
    console.log('ok done')
    jQuery(window).trigger('dm-scripts.select');
});