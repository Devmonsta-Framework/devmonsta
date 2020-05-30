jQuery.ajax({
    type: 'POST',
    url: ajax_object.ajax_url,
    data:{
        'action' : 'dm_repeater_controls',
        'repeater' : 'repeater',
    },
    success:function(data){
        console.log(data);
    },
    error:function(data){
        alert('Something went wrong');
        console.log(data.responseText);
    }
});


jQuery(document).ready(function ($,ajax_object) {
    
   

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    $(".dm-repeater-add-new").click(function () {
        var id = $(this).attr('data-id');
        
       
        $('#dm-repeater-section-' + id).append($('#dm-repeater-template-' + id).clone(true));

        // $('.dm-repeater-delete-btn').click(function () {

        //     $(this).closest('div').remove();

        // });
        console.log(window.ajax_object.ajaxurl);

        $.ajax({
            type:'POST',
            url:window.ajax_object.ajaxurl,
            data:{
                'action' : 'dm_get_control',
                'type' : 'text',
                'name' : 'f_name'
            },
            success:function(data){
                console.log(data);
            },
            error:function(data){
                alert('Something went wrong');
                console.log(data.responseText);
            }
        })

    });


});