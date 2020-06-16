jQuery(window).on('dm-scripts.datetimeRange', function(){
    var el = jQuery('.dm-option.active-script .dm-option-input-datetime-range');
    
    if (el.length && !el.hasClass('flatpickr-input')) {

        var time_picker = (date_time_range_config.timepicker == 0) ? false : true;
        var min_date = (date_time_range_config.minDate == "") ? false : date_time_range_config.minDate;
        var max_date = (date_time_range_config.maxDate == "") ? false : date_time_range_config.maxDate;
        console.log(date_time_range_config.format);
        el.flatpickr({
            mode: "range",
            // dateFormat: date_time_range_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_range_config.default_time,
            enableTime: time_picker,
        });
    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.datetimeRange');
});