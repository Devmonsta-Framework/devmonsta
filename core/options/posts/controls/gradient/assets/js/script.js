jQuery(document).ready(function($){

    
     for(color_id in gradient_picker_config.defaults){
        let single_color = '.dm-gradient-field-' + color_id ;
        let dmOptions = {
            defaultColor: gradient_picker_config.defaults[color_id],
            hide: true,
            change: function(event, ui){
                var theColor = ui.color.toString();
                console.log(theColor);
            }
        };
        $(single_color).wpColorPicker(dmOptions);
     }
});