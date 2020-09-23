jQuery(window).on('devm-scripts.datetimePicker', function(e, repeater){

    var el = jQuery('.devm-option.active-script .devm-option-input-datetime-picker');
    
    // update repeater element
    if(repeater) {
        el = repeater.find('.devm-option-input-datetime-picker');
    }

    //Initialize the datepicker and set the first day of the week as Monday
    if (el.length) {
        var time_picker = (date_time_picker_config.timepicker == 0) ? false : true;
        var is_24format = (date_time_picker_config.is24Format == 0) ? false : true;
        var min_date    = (date_time_picker_config.minDate == "") ? false : date_time_picker_config.minDate;
        var max_date    = (date_time_picker_config.maxDate == "") ? false : date_time_picker_config.maxDate;
        el.flatpickr({
            dateFormat: date_time_picker_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_picker_config.defaultTime,
            enableTime: time_picker,
            time_24hr: is_24format
        });
    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.datetimePicker');
});