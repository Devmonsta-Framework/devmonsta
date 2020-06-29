jQuery(window).on('dm-scripts.gradient', function(){    
    var el = jQuery('.dm-option.active-script .dm-gradient-color-picker');
    if(el && !el.length){ return false; }
    
    el.each(function(){
        var self = jQuery(this),
            gradient_picker_config = self.data('config');
        for(color_id in gradient_picker_config.defaults){
            let single_color = self.find('.dm-gradient-field-' + color_id) ;
            let dmOptions = {
                defaultColor: gradient_picker_config.defaults[color_id],
                hide: true,
                change: function(event, ui){
                    var theColor = ui.color.toString();
                }
            };
            jQuery(single_color).wpColorPicker(dmOptions);
        }
    });    
});


jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.gradient');
});