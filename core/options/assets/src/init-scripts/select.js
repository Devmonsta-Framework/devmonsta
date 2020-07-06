jQuery(window).on('devm-scripts.select', function(){
    var el = jQuery('.devm-option.active-script .devm_select');
    el.select2();
});

jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.select');
});