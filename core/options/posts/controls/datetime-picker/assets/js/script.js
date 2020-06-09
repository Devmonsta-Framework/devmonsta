jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm-option-input-datetime-picker');
    
    //Initialize the datepicker and set the first day of the week as Monday
    if (el.length) {

        var time_picker = (date_time_picker_config.timepicker == 0) ? false : true;

        el.flatpickr({
            dateFormat: date_time_picker_config.format,
            minDate: date_time_picker_config.minDate,
            maxDate: date_time_picker_config.maxDate,
            defaultTime: date_time_picker_config.defaultTime,
            enableTime: time_picker,
        });
    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});