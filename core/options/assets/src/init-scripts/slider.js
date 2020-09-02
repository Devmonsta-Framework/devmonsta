jQuery(window).on('devm-scripts.slider', function(){
    var el = jQuery('.devm-option.active-script .devm-slider');
    if (el.length) {
        el.asRange({
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
    jQuery(window).trigger('devm-scripts.slider');
});