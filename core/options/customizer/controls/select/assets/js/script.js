jQuery(window).on('devm-scripts.dm', function(){
    var el = jQuery('.devm_select');
    el.removeClass('.select2-hidden-accessible');
    el.parent().find('.select2').remove();
    el.select2();
});

jQuery(document).ready(function($) {
    
    //Initialize the datepicker and set the first day of the week as Monday
    jQuery(window).trigger('devm-scripts.dm');
});