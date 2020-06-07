jQuery(document).ready(function($) {

    var el = $('.dm-range-slider');
    
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
            $(this)[0].$element.trigger("change");
        }
    }); 
});