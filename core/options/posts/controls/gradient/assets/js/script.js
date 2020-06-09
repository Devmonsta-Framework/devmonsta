jQuery(window).on('dm-scripts.dm', function(){    
    for(color_id in gradient_picker_config.defaults){
        let single_color = '.dm-option.active-script .dm-gradient-field-' + color_id ;
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
    jQuery(window).trigger('dm-scripts.dm');
});