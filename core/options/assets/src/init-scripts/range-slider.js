jQuery(window).on('dm-scripts.rangeSlider', function(){
    var el = jQuery('.dm-option.active-script .dm-range-slider');
    if (el.length) {
        el.asRange({
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
    jQuery(window).trigger('dm-scripts.rangeSlider');
});