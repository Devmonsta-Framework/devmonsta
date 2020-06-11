jQuery(window).on('dm-scripts.dm', function(e,val){
    // console.log(dm_color_picker_config.default);
    var dmColorOptions = {
        defaultColor: dm_color_picker_config.default,
        hide: true,
        palettes: dm_color_picker_config.palettes
    };
    var el = jQuery('.dm-option.active-script .dm-color-picker-field');
    el.wpColorPicker(dmColorOptions);
});

jQuery(document).ready(function($){
    jQuery(window).trigger('dm-scripts.dm');
});