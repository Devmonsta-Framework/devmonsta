jQuery(window).on('dm-scripts.select', function(e, output){
    var el = jQuery('.dm-option.active-script .dm_select');
    console.log(el);
    // if(output){
    //     el = output.find('.dm_select')
    // }
    el.select2();
});

jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.select');
});