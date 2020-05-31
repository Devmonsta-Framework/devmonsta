jQuery(document).ready(function ($) {
    jQuery('.dm-repeater-add-new').on('click', function(e){
        e.preventDefault();
        var id = $(this).data('id');

        var repeaterControl = $('.dm-repeater-sample'),
            clonedElement = repeaterControl.clone().removeClass('dm-repeater-sample'),
            repeatCount = $(this).parents('.dm-repeater-column').find('.dm-repeater-control-list').children.length + 1;
            console.log($(this).parents('.dm-repeater-column').find('.dm-repeater-control-list'));

            clonedElement.find('.dm-repeater-control-action, .dm-repeater-popup-close, .dm-editor-post-trash').attr('data-id', id+'_'+(repeatCount)).end();
            clonedElement.find('.dm-repeater-inner-controls').attr('id', id+'_'+(repeatCount)).end();

        $(this).parents('.dm-repeater-column').find('.dm-repeater-control-list').append(clonedElement);

        // resetting repeater name
        repeaterControl.find('.dm-ctrl').each(function(index){
            console.log(index);
            var name = $(this).attr('name') ? $(this).attr('name') : '';
            if(name){
                $(this).attr('name', 'dm_options['+ id +']['+ repeatCount +']['+ name +']')
            }
        });
        // resetting data
        jQuery(window).trigger('dm-scripts.dm', [$('.dm-'+id+'-repeater-control').last()]);
    });
    
    // open and closing popup
    jQuery('body').on('click', '.dm-repeater-control-action, .dm-repeater-popup-close', function(e){
        e.preventDefault();
        var id = jQuery(this).data('id');
        jQuery('#'+id).toggleClass('open')
    });

    // deleting repeater
    $(document).on('click', '.dm-editor-post-trash',function(){
        $(this).parents('.dm-repeater-control').remove();
    })
});
