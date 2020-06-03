jQuery(window).on('dm-scripts.dm', function(){
    var el = jQuery('.dm-option.active-script .dm-option-image_picker_selector ul');
    
    el.on('click', 'li', function(){
        var name = jQuery(this).data("image_name");

        jQuery(this).addClass("selected").siblings().removeClass("selected");
        jQuery(this).parents(".dm-option-image_picker_selector").find('.dm-option-image-picker-input').val(name);
    });

});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.dm');
});