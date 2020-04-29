jQuery(document).ready(function($){
    
    if ($(".dm-range-slider").length) {
        $('.dm-range-slider').asRange({
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
            }
        }); 
    }
});