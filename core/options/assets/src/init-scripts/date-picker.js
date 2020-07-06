jQuery(window).on('devm-scripts.datePicker', function(e, val){
    
    var el = jQuery('.devm-option.active-script .devm-option-input-date-picker');
    if(val){
        el = val.find('.devm-option-input-date-picker');
    }
    if(!el.length){ return false; }
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
    jQuery(window).trigger('devm-scripts.datePicker');
});