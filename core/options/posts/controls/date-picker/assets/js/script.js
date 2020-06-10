jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm-option-input-date-picker');
    
    if(!el.hasClass('flatpickr-input')){
        el.flatpickr({
            dateFormat: "Y-m-d",
            minDate: dm_date_picker_config.minDate,
            maxDate:  dm_date_picker_config.maxDate,
            "locale": {
                "firstDayOfWeek": dm_date_picker_config.mondayFirst
            }
        });
    }

});

jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});