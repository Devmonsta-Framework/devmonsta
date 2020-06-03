jQuery(window).on("load", function ($) {

    console.log("entered");
    //Initialize the datepicker and set the first day of the week as Monday
    if (jQuery(".dm-option-input-datetime-picker").length) {
        // console.log(date_time_picker_config);

        var time_picker = (date_time_picker_config.timepicker == 0) ? false : true;

        jQuery(".dm-option-input-datetime-picker").flatpickr({
            dateFormat: date_time_picker_config.format,
            minDate: date_time_picker_config.minDate,
            maxDate: date_time_picker_config.maxDate,
            defaultTime: date_time_picker_config.defaultTime,
            enableTime: time_picker,
        });
    }
});