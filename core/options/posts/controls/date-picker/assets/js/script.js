jQuery(window).on('dm-scripts.datePicker', function(e, val){
    
    var el = jQuery('.dm-option.active-script .dm-option-input-date-picker');
    if(val){
        el = val.find('.dm-option-input-date-picker');
    }
    var mondayFirst = (dm_date_picker_config.mondayFirst == 1) ? true : false;
    if(el.length){
        var datePickerConfig = {
            dateFormat: "Y-m-d",
            mondeyFirst: true,
            onReady: function(a,b,c){
                
                this.config.mondeyFirst = true;
                console.log(this);
            }
        }

        el.flatpickr(datePickerConfig);
    }

});

jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.datePicker');
});