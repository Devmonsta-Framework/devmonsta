jQuery(document).ready(function ($) {
    function controlNameChanging(param){
        $(param.repeaterControl).each(function(index){
            var repeaterCount = index + 1;

            if(param.isRemoved){
                $(this).find('.devm-repeater-control-action, .devm-repeater-popup-close, .devm-editor-post-trash').attr('data-id', param.id+'_'+(repeaterCount));
                $(this).find('.devm-repeater-inner-controls').attr('id', param.id+'_'+(repeaterCount));
            }
            $(this).find('.devm-ctrl').each(function(index){
                var clonedInputs = !param.isRemoved ? param.clonedElement.find('.devm-ctrl')[index] : this,
                    name = $(clonedInputs).attr('name') ? $(clonedInputs).attr('name') : '',
                    formattedName = !param.isRemoved ? 'devm_options['+ param.id +']['+ param.repeatCount +']['+ name +']' : name;

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
    
    jQuery(document).on('click', '.devm-repeater-add-new', function(e, isRemoved){
        e.preventDefault();
        var id = $(this).data('id');

        var repeaterContent      = $('#devm-repeater-control-list-' + id),
            repeaterContentField = $('#devm_repeater_content_' + id);
    

        var repeaterControl = $(this).closest('.devm-repeater-column').children('.devm-repeater-sample'),
            clonedElement = repeaterControl.clone().removeClass('devm-repeater-sample'),
            repeatCount = $(this).closest('.devm-repeater-column').children('.devm-repeater-control-list').children().length + 1;

            clonedElement.find('.devm-repeater-control-action, .devm-repeater-popup-close, .devm-editor-post-trash').attr('data-id', id+'_'+(repeatCount)).end();
            clonedElement.find('.devm-repeater-inner-controls').attr('id', id+'_'+(repeatCount)).end();

        var controlConfig = {
            'repeaterControl': [repeaterControl],
            'isRemoved': isRemoved,
            'clonedElement': clonedElement,
            'id': id,
            'repeatCount': repeatCount
        };
        if(!isRemoved){
            clonedElement.children('.devm-repeater-inner-controls').children('.devm-repeater-inner-controls-inner').children('.devm-repeater-popup-data').children('.devm-option:not(.devm-repeater-child)').addClass('active-script');
            $(this).closest('.devm-repeater-column').children('.devm-repeater-control-list').append(clonedElement);
            // console.log(repeaterContent.html());
            repeaterContentField.val(repeaterContent.html());
        } else {
            
            repeatCount = repeatCount - 1;
            controlConfig.repeaterControl = $(this).closest('.devm-repeater-column').children('.devm-repeater-control-list').children();
            repeaterContentField.val(repeaterContent.html());
        }

        // open popup after added the repeated item
        jQuery(this).closest('.devm-repeater-column').find('.devm-repeater-control-list > .devm-repeater-control > .devm-repeater-control-action').last().trigger('click');

        // resetting repeater name
        controlNameChanging(controlConfig)
        // resetting data
        resetRepeater(clonedElement);
    });
    
    // open and closing popup
    jQuery(document).on('click', '.devm-repeater-control-action', function(e){
        e.preventDefault();
        $(this).closest('.devm-repeater-control').children('.devm-repeater-inner-controls').addClass('open')
    });

    jQuery(document).on('click', '.devm-repeater-popup-close', function(e){
        e.preventDefault();
        $(this).closest('.devm-repeater-control').children('.devm-repeater-inner-controls').removeClass('open')
    });

    // deleting repeater
    $(document).on('click', '.devm-editor-post-trash',function(){
        $(this).closest('.devm-repeater-control').remove();
        jQuery('.devm-repeater-add-new').trigger('click', [true]);
    })
});

// reset repeater func
function resetRepeater(clonedElement){
    jQuery(window).trigger('devm-scripts.dm', [clonedElement]);
    jQuery(window).trigger('devm-vue.dm', [clonedElement]);
    
    jQuery(window).trigger('devm-scripts', [clonedElement]);
}