jQuery(window).on('dm-scripts.datetimeRange', function (e, val) {
    var el = jQuery('.dm-option.active-script .dm-option-input-datetime-range');

    if (val) {
        el = val.find('.dm-option-input-datetime-range');
    }

    if (el.length) {
        var time_picker = (date_time_range_config.timepicker == 0) ? false : true;
        var is_24format = (date_time_range_config.is24Format == 0) ? false : true;
        var min_date = (date_time_range_config.minDate == "") ? false : date_time_range_config.minDate;
        var max_date = (date_time_range_config.maxDate == "") ? false : date_time_range_config.maxDate;
        el.flatpickr({
            mode: "range",
            dateFormat: date_time_range_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_range_config.defaultTime,
            enableTime: time_picker,
            time_24hr: is_24format
        });
    }

});


jQuery(document).ready(function ($) {
    jQuery(window).trigger('dm-scripts.datetimeRange');
});