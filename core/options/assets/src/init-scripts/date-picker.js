jQuery(window).on('dm-scripts.datePicker', function(e, val){
    var el = jQuery('.dm-option.active-script .dm-option-input-date-picker');
    if(val){
        el = val.find('.dm-option-input-date-picker');
    }
    el.each(function(){
        var mondayFirst = (jQuery(this).data('mondey-first') == 1) ? true : false;
        
        var datePickerConfig = {
            dateFormat: "Y-m-d",
            "locale": {
                "firstDayOfWeek": mondayFirst
            }
        }
        jQuery(this).flatpickr(datePickerConfig);
    })
    

});

jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.datePicker');
});