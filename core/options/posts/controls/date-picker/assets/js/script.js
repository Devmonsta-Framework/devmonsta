jQuery(document).ready(function($) {
    
    $(".dm-option-input-date-picker").flatpickr({
        dateFormat: "Y-m-d",
        minDate: dm_date_picker_config.minDate,
        maxDate:  dm_date_picker_config.maxDate,
        "locale": {
            "firstDayOfWeek": dm_date_picker_config.mondayFirst
        }
    });
});