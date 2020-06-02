jQuery(document).ready(function ($) {
    function controlNameChanging(param){
        $(param.repeaterControl).each(function(){
            $(this).find('.dm-ctrl').each(function(index){
                var clonedInputs = !param.isRemoved ? param.clonedElement.find('.dm-ctrl')[index] : this;
                if(!param.isRemoved){
                    repeatCount = index;
                }
    
                var name = $(clonedInputs).attr('name') ? $(clonedInputs).attr('name') : '';
                if(name){
                    $(clonedInputs).attr('name', 'dm_options['+ param.id +']['+ param.repeatCount +']['+ name +']')
                }
            });
        })
        
    }
    
    jQuery('.dm-repeater-add-new').on('click', function(e, isRemoved){
        e.preventDefault();
        var id = $(this).data('id');

        var repeaterControl = $('.dm-repeater-sample'),
            clonedElement = repeaterControl.clone().removeClass('dm-repeater-sample'),
            repeatCount = $(this).parents('.dm-repeater-column').find('.dm-repeater-control-list').children().length + 1;

            clonedElement.find('.dm-repeater-control-action, .dm-repeater-popup-close, .dm-editor-post-trash').attr('data-id', id+'_'+(repeatCount)).end();
            clonedElement.find('.dm-repeater-inner-controls').attr('id', id+'_'+(repeatCount)).end();

        var controlConfig = {
            'repeaterControl': [repeaterControl],
            'isRemoved': isRemoved,
            'clonedElement': clonedElement,
            'id': id,
            'repeatCount': repeatCount,
        };
        if(!isRemoved){
            $(this).parents('.dm-repeater-column').find('.dm-repeater-control-list').append(clonedElement);
        } else {
            repeatCount = repeatCount - 1;
        }


        // resetting repeater name
        controlNameChanging(controlConfig)
        // repeaterControl.find('.dm-ctrl').each(function(index){
        //     var clonedInputs = !isRemoved ? clonedElement.find('.dm-ctrl')[index] : this;

        //     var name = $(clonedInputs).attr('name') ? $(clonedInputs).attr('name') : '';
        //     if(name){
        //         $(clonedInputs).attr('name', 'dm_options['+ id +']['+ repeatCount +']['+ name +']')
        //     }
        // });
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
        jQuery('.dm-repeater-add-new').trigger('click', [true]);
    })
});
