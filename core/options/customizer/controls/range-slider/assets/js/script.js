jQuery(window).on('dm-scripts.dm', function(){
    // console.log("sdkjfhakshfsahjfhaskfhkashfksahfahskjfhadkhfkajhfksdjfsd");
    var el = jQuery('.dm-option.active-script .dm-range-slider');
    
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
            }
        }); 
    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});