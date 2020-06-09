

jQuery(document).ready(function($) {
    var el = jQuery('.dm-option-input-datetime-range');
    
    if (el.length) {
        var time_picker = (date_time_range_config.timepicker == 0) ? false : true;
        var min_date = (date_time_range_config.minDate == "") ? false : date_time_range_config.minDate;
        var max_date = (date_time_range_config.maxDate == "") ? false : date_time_range_config.maxDate;
        var defaultConfig = {
            mode: "range",
            dateFormat: date_time_range_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_range_config.defaultTime,
            enableTime: time_picker
        }
        console.log( defaultConfig);
        el.flatpickr( defaultConfig );
    }
});
