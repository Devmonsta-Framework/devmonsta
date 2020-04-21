jQuery(document).ready(function($){

    // console.log(gradient_picker_config.defaults);
    
     for(color_id in gradient_picker_config.defaults){
        let single_color = '.dm-gradient-field-' + color_id ;
        let dmOptions = {
            defaultColor: gradient_picker_config.defaults[color_id],
            hide: true,
        };
        $(single_color).wpColorPicker(dmOptions);
     }
    
});