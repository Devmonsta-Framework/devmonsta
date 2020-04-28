(function($){
    $(document).ready(function(){
        $(".dm_switcher_item").click(function() {
            console.log("here");
            var checkBoxes = $("input[name=devmonsta_switcher]");
            var checkBoxes = $(this).find(':checkbox');
            checkBoxes.attr('checked', !checkBoxes.attr('checked'));
        
        });    
    })
})(jQuery)