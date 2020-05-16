(function($){
    $(document).ready(function(){
        $(".dm-option-image_picker_selector ul").on('click', 'li', function(){
            var name = $(this).data("image_name");

            $(this).addClass("selected").siblings().removeClass("selected");
            $(this).parents(".dm-option-image_picker_selector").find('.dm-option-image-picker-input').val(name);
        })
    })
})(jQuery)