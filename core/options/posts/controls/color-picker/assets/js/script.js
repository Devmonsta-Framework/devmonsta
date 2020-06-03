jQuery(window).on('dm-scripts.dm', function(e,val){
    var dmColorOptions = {
        defaultColor: dm_color_picker_config.default,
        hide: true,
        palettes: dm_color_picker_config.palettes
    };
    var el = jQuery('.dm-option.active-script .dm-color-picker-field');

    // if(val){
    //     el = val.find('.dm-color-picker-field')
    //     el.parents('.wp-picker-container').find('.wp-picker-default').remove()
    //     el.parents('.wp-picker-container').find('.wp-color-result').remove()
    //     el.parents('.wp-picker-container').find('.wp-picker-holder').remove()
    //     el.parents('.wp-picker-container').find('.wp-picker-input-wrap').removeClass('hidden');
    //     el.wpColorPicker(dmColorOptions);
    //     return false;
    // }
    el.wpColorPicker(dmColorOptions);
});

jQuery(document).ready(function($){
    jQuery(window).trigger('dm-scripts.dm');
});