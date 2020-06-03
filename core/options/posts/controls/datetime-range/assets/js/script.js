jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm-option-input-datetime-range');
    
    
    if (el.length) {

        var time_picker = (date_time_range_config.timepicker == "") ? false : true;
        var date_picker = (date_time_range_config.datepicker == "") ? false : true;
        var time_picker_24hour = (date_time_range_config.time24hours == "") ? false : true;
        var min_date = (date_time_range_config.min_date == "") ? false :date_time_range_config.min_date;
        var max_date = (date_time_range_config.max_date == "") ? false :date_time_range_config.max_date;


        el.flatpickr({
            mode: "range",
            dateFormat: date_time_picker_config.format,
            minDate: min_date,
            maxDate: max_date,
            defaultTime: date_time_picker_config.default_time,
            enableTime: true,
            time_24hr: time_picker_24hour
        });



    }

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});