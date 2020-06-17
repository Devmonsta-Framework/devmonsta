jQuery(window).on('dm-scripts.datePicker', function(){
    
    var el = jQuery('.dm-option.active-script .dm-option-input-date-picker');
    var mondayFirst = (dm_date_picker_config.mondayFirst == 1) ? true : false;
    if(!el.hasClass('flatpickr-input')){
        var datePickerConfig = {
            dateFormat: "Y-m-d",
            minDate: dm_date_picker_config.minDate,
            maxDate:  dm_date_picker_config.maxDate,
            "locale": {
                "firstDayOfWeek": mondayFirst
            }
        }

        el.flatpickr(datePickerConfig);
    }

});

jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.datePicker');
});