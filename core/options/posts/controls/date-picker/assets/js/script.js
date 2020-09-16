jQuery(window).on('devm-scripts.datePicker', function(e, val){
    
    var el = jQuery('.devm-option.active-script .devm-option-input-date-picker');
    if(val){
        el = val.find('.devm-option-input-date-picker');
    }
    var mondayFirst = (devm_date_picker_config.mondayFirst == 1) ? true : false;
    if(el.length){
        var datePickerConfig = {
            dateFormat: "Y-m-d",
            minDate: devm_date_picker_config.minDate,
            maxDate:  devm_date_picker_config.maxDate,
            "locale": {
                "firstDayOfWeek": mondayFirst
            }
        }

        el.flatpickr(datePickerConfig);
    }

});

jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.datePicker');
});