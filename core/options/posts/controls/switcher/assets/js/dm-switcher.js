jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm_switcher_item');
    
    el.click(function () {
        var checkBoxe_right = jQuery(".dm_switcher_right");
        var checkBoxe_left = jQuery(".dm_switcher_left");
        checkBoxe_right.attr('checked', !checkBoxe_right.attr('checked'));
        if (checkBoxe_right.attr('checked')) {
            checkBoxe_left.attr('checked', false);
        } else {
            checkBoxe_left.attr('checked', true);
        }
    });

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});