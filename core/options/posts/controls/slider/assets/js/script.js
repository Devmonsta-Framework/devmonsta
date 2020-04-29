jQuery(document).ready(function($) {


    if ($(".dm-slider").length) {
        $('.dm-slider').asRange({
            max: dm_slider_config.max,
            min: dm_slider_config.min,
            step: dm_slider_config.step,
            limit: true,
            range: false,
            direction: 'h', // 'v' or 'h'
            keyboard: true,
            replaceFirst: false, // false, 'inherit', {'inherit': 'default'}
            tip: true,
            scale: true,
            format(value) {
              return value;
            }
        }); 
    }
});