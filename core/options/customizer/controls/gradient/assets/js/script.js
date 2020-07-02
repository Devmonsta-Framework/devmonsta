jQuery(document).ready(function ($) {

    for(color_id in gradient_picker_config.defaults){
        var single_color = '.devm-gradient-field-' + color_id ;
        let devmOptions = {
            defaultColor: gradient_picker_config.defaults[color_id],
            hide: true,
            change: function(event, ui){
                // var selectedColor = ui.color.toString();
                var primary = $(this).parents(".gradient-parent").find(".wp-color-result")[0].style.backgroundColor ;
                var secondary = $(this).parents(".gradient-parent").find(".wp-color-result")[1].style.backgroundColor ;
                
                var final_color = rgb2hex(primary)  + "," + rgb2hex(secondary);
                $(this).parents(".gradient-parent").find(".devm-gradient-value").val(final_color).trigger('change');
              } 
        };
        $(single_color).wpColorPicker(devmOptions);
     }

     /**
      * convert rgb value into hex value
      * @param {*} rgb 
      */
     function rgb2hex(rgb) {
        if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;
    
        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }
    
});
