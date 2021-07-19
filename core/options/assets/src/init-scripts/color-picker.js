jQuery(window).on('devm-scripts.colorPicker', function(e,val){
    var el = jQuery('.devm-option.active-script .devm-color-picker-field');
    if(el && !el.length){ return false; }

    el.each(function(){
        var devm_color_picker_config = jQuery(this).data('config');
            devmColorOptions = {
                defaultColor: devm_color_picker_config.default,
                hide: true,
                palettes: devm_color_picker_config.palettes
            };
        jQuery(this).wpColorPicker(devmColorOptions);
    });
});

jQuery(document).ready(function($){
    jQuery(window).trigger('devm-scripts.colorPicker');
});
