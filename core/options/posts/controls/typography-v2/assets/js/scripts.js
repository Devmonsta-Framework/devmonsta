jQuery(document).ready(function($){
    // for color
    var dmOptions = {
        defaultColor: typo_config.default,
        hide: true,
    };
    $('.dm-typography-color-field').wpColorPicker(dmOptions);
    // for select2 
    $('.google-fonts-list').select2();
});

//slider
( function( $ ) {
    // slide ranger
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

    // select 2 on change style and weight
    $('.google-fonts-list').on("select2:selecting", function(e) { 
        console.log($(this).val());
        console.log(typo_config);

    });
 } )( jQuery );