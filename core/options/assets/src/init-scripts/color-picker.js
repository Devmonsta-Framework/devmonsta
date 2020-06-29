jQuery(window).on('dm-scripts.colorPicker', function(e,val){
    var el = jQuery('.dm-option.active-script .dm-color-picker-field');
    if(el && !el.length){ return false; }

    el.each(function(){
        var dm_color_picker_config = jQuery(this).data('config');
            dmColorOptions = {
                defaultColor: dm_color_picker_config.default,
                hide: true,
                palettes: dm_color_picker_config.palettes
            };
        jQuery(this).wpColorPicker(dmColorOptions);
    });
});

jQuery(document).ready(function($){
    jQuery(window).trigger('dm-scripts.colorPicker');
});