jQuery(window).on('devm-scripts.gradient', function(){    
    var el = jQuery('.devm-option.active-script .devm-gradient-color-picker');
    if(el && !el.length){ return false; }
    
    el.each(function(){
        var self = jQuery(this),
            gradient_picker_config = self.data('config');
        for(color_id in gradient_picker_config.defaults){
            let single_color = self.find('.devm-gradient-field-' + color_id) ;
            let devmOptions = {
                defaultColor: gradient_picker_config.defaults[color_id],
                hide: true,
                change: function(event, ui){
                    var theColor = ui.color.toString();
                }
            };
            jQuery(single_color).wpColorPicker(devmOptions);
        }
    });    
});


jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.gradient');
});