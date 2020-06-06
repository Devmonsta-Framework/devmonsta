// jQuery(window).on('dm-scripts.dm', function(){
//     console.log("Hello from slider");
//     var el = jQuery('.dm-option.active-script .dm-slider');
    
//     if (el.length) {
//         el.asRange({
//             max: dm_slider_config.max,
//             min: dm_slider_config.min,
//             step: dm_slider_config.step,
//             limit: true,
//             range: false,
//             direction: 'h', // 'v' or 'h'
//             keyboard: true,
//             replaceFirst: false, // false, 'inherit', {'inherit': 'default'}
//             tip: true,
//             scale: true,
//             format(value) {
//               return value;
//             }
//         }); 
//     }
// });


jQuery(document).ready(function($) {
    // jQuery(window).trigger('dm-scripts.dm');
    var el = jQuery(' .dm-slider');
    
    if (el.length) {
        el.asRange({
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
                $(this).trigger('change');
                console.log(value);
              return value;
            },
            onchange(instance) {
                console.log(instance)
            }
        }); 
    }
});