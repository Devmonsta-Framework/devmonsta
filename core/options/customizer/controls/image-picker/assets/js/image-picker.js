(function($){
    $(document).ready(function(){

        // console.log("console from image picker");
        $(".dm-option-image_picker_selector ul").on('click', 'li', function(){
            // console.log("console from image picker");
            var name = $(this).data("image_name");
            // console.log("clicked: " + name);

            $(this).addClass("selected").siblings().removeClass("selected");
            $(this).parents(".dm-option-image_picker_selector").find('.dm-option-image-picker-input').val(name);
            
            // console.log("image selected: " + name);
        })
    })
})(jQuery)