jQuery(document).ready(function($){
    var dmOptions = {
        hide: true,
        palettes: color_picker_config.palettes
    };
     
    $('.dm-color-field').wpColorPicker(dmOptions);
});