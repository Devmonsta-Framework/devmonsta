jQuery(window).on('load', function(){
    jQuery('.dm-vue-app').addClass('active-script');
    jQuery(window).trigger('dm-vue.dm');
})




jQuery(document).ready(function($){

    // $(".dm-dimension-attachment-input").on("click", function(e){
    //     e.preventDefault();
    //     var current_div = $(this);
    //     //toggle class on clicking isLinked button
    //     current_div.toggleClass('clicked');

    //     //change value of hidden field to store clicked value
    //     current_div.hasClass('clicked') ?  
    //             current_div.siblings(".dm-dimension-linked-input").val('1'):
    //             current_div.siblings(".dm-dimension-linked-input").val('0'); 

    //     //change isLinked button background color on clicking
    //     current_div.hasClass('clicked') ?  
    //             current_div.css("background-color","gray"):
    //             current_div.css("background-color","white"); 
       
    //     // update values of all inputs on clickng isLinked button
    //     if(current_div.hasClass('clicked')){
    //         let fixed_value = parseInt(current_div.siblings(".input-top").val());
    //         current_div.siblings(".input-top").val(fixed_value);
    //         current_div.siblings(".input-right").val(fixed_value);
    //         current_div.siblings(".input-bottom").val(fixed_value);
    //         current_div.siblings(".input-left").val(fixed_value);
    //     }
    // });
    


});