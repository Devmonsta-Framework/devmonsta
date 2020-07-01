jQuery(window).on('devm-scripts.dm', function(){
    var el = jQuery('.devm-option.active-script .devm-slider');
    
    if (el.length) {
        el.asRange({
            max: devm_slider_config.max,
            min: devm_slider_config.min,
            step: devm_slider_config.step,
            limit: true,
            range: false,
            direction: 'h', // 'v' or 'h'
            keyboard: true,
            replaceFirst: false, // false, 'inherit', {'inherit': 'default'}
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