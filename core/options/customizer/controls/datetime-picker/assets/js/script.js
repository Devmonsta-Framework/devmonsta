jQuery(document).ready(function ($) {



    //Initialize the datepicker and set the first day of the week as Monday
    if ($("#dm-datetime-picker").length) {

        console.log("reached from date-time picker");

        var min_date;
        var max_date;
        var time_picker = (date_time_picker_config.timepicker == "") ? false : true;
        var date_picker = (date_time_picker_config.datepicker == "") ? false : true;

        if (date_time_picker_config.min_date == "") {
            min_date = false;
        } else {
            min_date = date_time_picker_config.min_date;
        }

        if (date_time_picker_config.max_date == "") {
            max_date = false;
        } else {
            max_date = date_time_picker_config.max_date;
        }

        var today = new Date();
        var defaultDate = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();

        $('#dm-datetime-picker').datetimepicker({

            timepicker: time_picker,
            datepicker: date_picker,
            dateFormat: date_time_picker_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_picker_config.default_time,
            onSelect: function () {
                $(this).val();
            }
        });
    }
});