// jQuery(window).on('dm-scripts.dm', function(){
//     var el = jQuery('.dm-option.active-script .dm-range-slider');
    
//     // console.log(el);
//     if (el.length) {
//         // console.log(range_slider_config);
        
//     }

// });


jQuery(document).ready(function($) {
    var el = $('.dm-range-slider');
    console.log(el);
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
          $(this).trigger('change');
          console.log(value);
          return value;
        },
        onchange(instance) {
            console.log(instance)
        }
    }); 
    // jQuery(window).trigger('dm-scripts.dm');
});