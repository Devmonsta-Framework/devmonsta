jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm-range-slider');
    console.log("as range loaded");
    if (el.length) {
        el.asRange({
            max: range_slider_config.max,
            min: range_slider_config.min,
            step: range_slider_config.step,
            range: true,
            limit: false,
            direction: 'h', // 'v' or 'h'
            keyboard: true,
            tip: true,
            scale: true,
            format(value) {
              return value;
            },
            onChange(instance) {
                console.log("changed");
                jQuery(this)[0].$element.trigger("change");
            }
        }); 
    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});