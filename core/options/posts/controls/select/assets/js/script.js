jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm_select');
    console.log(el);

    // if(val){
    //     el = val.find('.dm-color-picker-field')
    //     el.parents('.wp-picker-container').find('.wp-picker-default').remove()
    //     el.parents('.wp-picker-container').find('.wp-color-result').remove()
    //     el.parents('.wp-picker-container').find('.wp-picker-holder').remove()
    //     el.parents('.wp-picker-container').find('.wp-picker-input-wrap').removeClass('hidden');
    //     el.wpColorPicker(dmColorOptions);
    //     return false;
    // }

    el.select2();
});

jQuery(document).ready(function($) {
    
    //Initialize the datepicker and set the first day of the week as Monday
    jQuery(window).trigger('dm-scripts.dm');
});