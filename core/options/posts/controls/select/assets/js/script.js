jQuery(window).on('devm-scripts.dm', function(){
    var el = jQuery('.devm-option.active-script .devm_select');
    el.select2();
});

jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.dm');
});