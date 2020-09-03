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
                    if(!wp.customize){ return false }
                    var theColor = ui.color.toString();
                    var primary = jQuery(this).parents(".gradient-parent").find(".wp-color-result")[0].style.backgroundColor ;
                    var secondary = jQuery(this).parents(".gradient-parent").find(".wp-color-result")[1].style.backgroundColor ;

                    var final_color = rgb2hex(primary)  + "," + rgb2hex(secondary);
                    jQuery(this).parents(".gradient-parent").find(".devm-gradient-value").val(final_color).trigger('change');
                }
            };
            jQuery(single_color).wpColorPicker(devmOptions);
        }
    }); 
    
    function rgb2hex(rgb) {
        if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;
    
        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }
});


jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.gradient');
});