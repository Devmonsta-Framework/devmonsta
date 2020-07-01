jQuery(window).on('devm-scripts.dm', function(){    
    for(color_id in gradient_picker_config.defaults){
        let single_color = '.devm-option.active-script .devm-gradient-field-' + color_id ;
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


jQuery(document).ready(function($) {
    jQuery(window).trigger('devm-scripts.dm');
});