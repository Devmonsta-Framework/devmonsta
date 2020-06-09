jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm_select');
    el.removeClass('.select2-hidden-accessible');
    el.parent().find('.select2').remove();
    el.select2();
});

jQuery(document).ready(function($) {
    
    //Initialize the datepicker and set the first day of the week as Monday
    jQuery(window).trigger('dm-scripts.dm');
});