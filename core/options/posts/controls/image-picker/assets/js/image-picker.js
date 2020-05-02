(function($){
    $(document).ready(function(){
        $(".image_picker_selector li").on('click',function(){
            $(".image_picker_selector li").removeClass("selected");
            $(this).addClass("selected");
            var name =$(this).data("image_name");
            $("#dm_image_picker option:selected").removeAttr("selected");
            $("#dm_image_picker ").find('option[value="'+name+'"]').attr("selected",true);

        })
    })
})(jQuery)