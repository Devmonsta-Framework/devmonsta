jQuery(document).ready(function($) {

    if ($("#dm-datetime-picker").length) {

        time_picker = (date_time_range_config.timepicker == "") ? false : true;
        date_picker = (date_time_range_config.datepicker == "") ? false : true;
        time_picker_24hour = (date_time_range_config.time24hours == "") ? false : true;
        min_date = (date_time_range_config.min_date == "") ? false :date_time_range_config.min_date;
        max_date = (date_time_range_config.max_date == "") ? false :date_time_range_config.max_date;
        
        $('#dm-datetime-range').daterangepicker({
            timePicker: time_picker,
            datePicker: date_picker,
            minDate: min_date,
            maxDate: max_date,
            timePicker24Hour: time_picker_24hour,
            locale: {
                format: date_time_range_config.format
              },
              onSelect: function() {
                $(this).val();
            }
          });



    }
});