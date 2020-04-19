jQuery(document).ready(function($) {

    // console.log(start_day);

    //Initialize the datepicker and set the first day of the week as Monday
    if ($("#dm-date-picker").length) {

        $('#dm-date-picker').datepicker({
            firstDay: date_picker_config.start_day,
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            minDate: date_picker_config.min_date,
            maxDate: date_picker_config.max_date,
            onSelect: function() {
                $(this).val();
            }
        });
    }
});