jQuery(document).ready(function($){
    
    var dmColorOptions = {
        defaultColor: dm_color_picker_config.default,
        hide: true,
        palettes: dm_color_picker_config.palettes
    };
     
    $('.dm-color-picker-field').wpColorPicker(dmColorOptions);
});