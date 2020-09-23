jQuery(window).on('devm-scripts.dm', function(){
    var el = jQuery('.devm-option.active-script .devm-range-slider');
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
                jQuery(this)[0].$element.trigger("change");
            }
        }); 
    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.dm');
});