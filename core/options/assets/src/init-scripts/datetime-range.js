jQuery(window).on('devm-scripts.datetimeRange', function (e, val) {
    var el = jQuery('.devm-option.active-script .devm-option-input-datetime-range');

    if (val) {
        el = val.find('.devm-option-input-datetime-range');
    }
    if(el && !el.length){ return false; }

    el.each(function(){
        var date_time_range_config = jQuery(this).data('config');
            time_picker = (date_time_range_config.timepicker == 0) ? false : true,
            is_24format = (date_time_range_config.is24Format == 0) ? false : true,
            min_date = (date_time_range_config.minDate == "") ? false : date_time_range_config.minDate,
            max_date = (date_time_range_config.maxDate == "") ? false : date_time_range_config.maxDate;
        jQuery(this).flatpickr({
            mode: "range",
            dateFormat: date_time_range_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_range_config.defaultTime,
            enableTime: time_picker,
            time_24hr: is_24format
        });
    });

});


jQuery(document).ready(function ($) {
    jQuery(window).trigger('devm-scripts.datetimeRange');
});