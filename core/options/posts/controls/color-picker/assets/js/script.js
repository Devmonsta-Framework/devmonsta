jQuery(document).ready(function($){

    console.log(color_picker_config.default);
    var dmOptions = {
        defaultColor: color_picker_config.default,
        hide: true,
        palettes: color_picker_config.palettes
    };
     
    $('.dm-color-field').wpColorPicker(dmOptions);
});