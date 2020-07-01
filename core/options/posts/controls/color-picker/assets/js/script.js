jQuery(window).on('devm-scripts.colorPicker', function(e,val){
    var dmColorOptions = {
        defaultColor: devm_color_picker_config.default,
        hide: true,
        palettes: devm_color_picker_config.palettes
    };
    var el = jQuery('.devm-option.active-script .devm-color-picker-field');
    el.wpColorPicker(dmColorOptions);
});

jQuery(document).ready(function($){
    jQuery(window).trigger('devm-scripts.colorPicker');
});