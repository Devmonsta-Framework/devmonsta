
jQuery(document).ready(function($) {
    var el = $('.dm-option-input-datetime-picker');
    
    //Initialize the datepicker and set the first day of the week as Monday
    if (el.length) {

        var time_picker = (date_time_picker_config.timepicker == 0) ? false : true;
        var min_date = (date_time_picker_config.minDate == "") ? false : date_time_picker_config.minDate;
        var max_date = (date_time_picker_config.maxDate == "") ? false : date_time_picker_config.maxDate;
        var defaultConfig = {
            dateFormat: date_time_picker_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_picker_config.defaultTime,
            enableTime: time_picker,
        }
        el.flatpickr( defaultConfig );
    }
});