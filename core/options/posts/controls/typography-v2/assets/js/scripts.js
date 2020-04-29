jQuery(document).ready(function($){
    // for color
    var dmOptions = {
        defaultColor: color_picker_config.default,
        hide: true,
    };
     
    $('.dm-typography-color-field').wpColorPicker(dmOptions);
    // for select2 
    $('.google-fonts-list').select2();
    
});

//slider
( function( $ ) {
    $(document).ready(function(){
        $(document).on('change','.range-slider-font-size',function(){
            document.getElementById('size_value').value=$(this).val(); 
        }); 
        $(document).on('change','.range-slider-line-height',function(){
            document.getElementById('line_height_value').value=$(this).val(); 
        });
        $(document).on('change','.range-slide-letter-space',function(){
            document.getElementById('latter_spacing_value').value=$(this).val(); 
        });
    });

 } )( jQuery );