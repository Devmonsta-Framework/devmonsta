jQuery(document).ready(function($) {

    //Initialize the datepicker and set the first day of the week as Monday
    if ($("#dm-datetime-picker").length) {

        $('#dm-datetime-picker').datetimepicker({
            timepicker: date_time_picker_config.timepicker,
            datepicker: date_time_picker_config.datepicker,
            dateFormat: date_time_picker_config.format,
            // minDate: date_time_picker_config.min_date,
            // maxDate: date_time_picker_config.max_date,
            defaultTime: date_time_picker_config.default_time,
            onSelect: function() {
                $(this).val();
            }
        });
    }
});