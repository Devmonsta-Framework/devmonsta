jQuery(window).on('load', function ($) {

    var mondayFirst = (dm_date_picker_config.mondayFirst == 1) ? true : false;
    jQuery(".dm-option-input-date-picker").flatpickr({
        dateFormat: "Y-m-d",
        minDate: dm_date_picker_config.minDate,
        maxDate: dm_date_picker_config.maxDate,
        "locale": {
            "firstDayOfWeek": mondayFirst
        }
    });
});