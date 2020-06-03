jQuery(document).ready(function ($) {
    function controlNameChanging(param){
        $(param.repeaterControl).each(function(index){
            var repeaterCount = index + 1;

            if(param.isRemoved){
                $(this).find('.dm-repeater-control-action, .dm-repeater-popup-close, .dm-editor-post-trash').attr('data-id', param.id+'_'+(repeaterCount));
                $(this).find('.dm-repeater-inner-controls').attr('id', param.id+'_'+(repeaterCount));
            }
            $(this).find('.dm-ctrl').each(function(index){
                var clonedInputs = !param.isRemoved ? param.clonedElement.find('.dm-ctrl')[index] : this,
                    name = $(clonedInputs).attr('name') ? $(clonedInputs).attr('name') : '',
                    formattedName = !param.isRemoved ? 'dm_options['+ param.id +']['+ param.repeatCount +']['+ name +']' : name;

                if(param.isRemoved){
                    formattedName = formattedName.replace(/\[(\d+)\]/, function(args, digit){ 
                        return "["+( repeaterCount )+"]";
                    });
                }

                if(name){
                    $(clonedInputs).attr('name', formattedName)
                }
            });
        })
        
    }
    
    jQuery('.dm-repeater-add-new').on('click', function(e, isRemoved){
        e.preventDefault();
        var id = $(this).data('id');

        console.log(id);

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
            'repeatCount': repeatCount
        };
        if(!isRemoved){
            clonedElement.find('.dm-option').addClass('active-script');
            $(this).parents('.dm-repeater-column').find('.dm-repeater-control-list').append(clonedElement);
        } else {
            repeatCount = repeatCount - 1;
            controlConfig.repeaterControl = $(this).parents('.dm-repeater-column').find('.dm-repeater-control-list').children();
        }


        // resetting repeater name
        controlNameChanging(controlConfig)
        // resetting data
        jQuery(window).trigger('dm-scripts.dm', [clonedElement]);
    });
    
    // open and closing popup
    jQuery(document).on('click', '.dm-repeater-control-action, .dm-repeater-popup-close', function(e){
        e.preventDefault();
        var id = jQuery(this).attr('data-id');
        jQuery('#'+id).toggleClass('open')
    });

    // deleting repeater
    $(document).on('click', '.dm-editor-post-trash',function(){
        $(this).parents('.dm-repeater-control').remove();
        jQuery('.dm-repeater-add-new').trigger('click', [true]);
    })
});
