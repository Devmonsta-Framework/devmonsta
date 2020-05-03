(function($){
    $(document).ready(function(){
        $(".dm_switcher_item").click(function() {
            var checkBoxe_right = $("#dm_switcher_right");
            var checkBoxe_left = $("#dm_switcher_left");
            checkBoxe_right.attr('checked', !checkBoxe_right.attr('checked'));  
            if ( checkBoxe_right.attr('checked') ) {
                checkBoxe_left.attr('checked' , false);
            } else {
                checkBoxe_left.attr('checked' , true);
            }      
        });    
    })
})(jQuery)