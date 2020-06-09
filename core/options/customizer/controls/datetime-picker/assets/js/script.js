// jQuery(window).on("load", function ($) {

//     if (jQuery(".dm-option-input-datetime-picker").length) {

//         var time_picker = (date_time_picker_config.timepicker == 0) ? false : true;

//         jQuery(".dm-option-input-datetime-picker").flatpickr({
//             dateFormat: date_time_picker_config.format,
//             minDate: date_time_picker_config.minDate,
//             maxDate: date_time_picker_config.maxDate,
//             defaultTime: date_time_picker_config.defaultTime,
//             enableTime: time_picker,
//         });
//     }
// });

// jQuery(window).on('dm-scripts.dm', function(){
    

// });


jQuery(document).ready(function($) {
    var el = $('.dm-option-input-datetime-picker');
    console.log("hello from the other side");
    
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