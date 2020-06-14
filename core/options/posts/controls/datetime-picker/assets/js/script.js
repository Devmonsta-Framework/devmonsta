jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm-option-input-datetime-picker');
    // console.log(date_time_picker_config);
    //Initialize the datepicker and set the first day of the week as Monday
    if (el.length && !el.hasClass('flatpickr-input')) {
        var time_picker = (date_time_picker_config.timepicker == 0) ? false : true;
        var min_date = (date_time_picker_config.minDate == "") ? false : date_time_picker_config.minDate;
        var max_date = (date_time_picker_config.maxDate == "") ? false : date_time_picker_config.maxDate;

        el.flatpickr({
            dateFormat: date_time_picker_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_picker_config.defaultTime,
            enableTime: time_picker,
        });
    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});