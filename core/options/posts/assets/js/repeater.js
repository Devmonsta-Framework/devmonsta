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
    
    jQuery(document).on('click', '.dm-repeater-add-new', function(e, isRemoved){
        e.preventDefault();
        var id = $(this).data('id');

        var repeaterContent      = $('#dm-repeater-control-list-' + id),
            repeaterContentField = $('#dm_repeater_content_' + id);
    

        var repeaterControl = $(this).closest('.dm-repeater-column').children('.dm-repeater-sample'),
            clonedElement = repeaterControl.clone().removeClass('dm-repeater-sample'),
            repeatCount = $(this).closest('.dm-repeater-column').children('.dm-repeater-control-list').children().length + 1;

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
            clonedElement.children('.dm-repeater-inner-controls').children('.dm-repeater-inner-controls-inner').children('.dm-repeater-popup-data').children('.dm-option:not(.dm-repeater-child)').addClass('active-script');
            $(this).closest('.dm-repeater-column').children('.dm-repeater-control-list').append(clonedElement);
            // console.log(repeaterContent.html());
            repeaterContentField.val(repeaterContent.html());
        } else {
            
            repeatCount = repeatCount - 1;
            controlConfig.repeaterControl = $(this).closest('.dm-repeater-column').children('.dm-repeater-control-list').children();
            repeaterContentField.val(repeaterContent.html());
        }

        // open popup after added the repeated item
        jQuery(this).closest('.dm-repeater-column').find('.dm-repeater-control-list > .dm-repeater-control > .dm-repeater-control-action').last().trigger('click');

        // resetting repeater name
        controlNameChanging(controlConfig)
        // resetting data
        resetRepeater(clonedElement);
    });
    
    // open and closing popup
    jQuery(document).on('click', '.dm-repeater-control-action', function(e){
        e.preventDefault();
        $(this).closest('.dm-repeater-control').children('.dm-repeater-inner-controls').addClass('open')
    });

    jQuery(document).on('click', '.dm-repeater-popup-close', function(e){
        e.preventDefault();
        $(this).closest('.dm-repeater-control').children('.dm-repeater-inner-controls').removeClass('open')
    });

    // deleting repeater
    $(document).on('click', '.dm-editor-post-trash',function(){
        $(this).closest('.dm-repeater-control').remove();
        jQuery('.dm-repeater-add-new').trigger('click', [true]);
    })
});

// reset repeater func
function resetRepeater(clonedElement){
    jQuery(window).trigger('dm-scripts.dm', [clonedElement]);
    jQuery(window).trigger('dm-vue.dm', [clonedElement]);
    // multi-step
    jQuery(window).trigger('dm-scripts.multiSelect', [clonedElement]);
    // select
    jQuery(window).trigger('dm-scripts.select', [clonedElement]);
    // typography
    jQuery(window).trigger('dm-scripts-typo.dm', [clonedElement]);
    // gradient
    jQuery(window).trigger('dm-scripts.gradient', [clonedElement]);
    // datetime Range
    jQuery(window).trigger('dm-scripts.datetimeRange', [clonedElement]);
    // Date Picker
    jQuery(window).trigger('dm-scripts.datePicker', [clonedElement]);
    // colorpicker
    jQuery(window).trigger('dm-scripts.colorPicker', [clonedElement]);
    // datetime picker
    jQuery(window).trigger('dm-scripts.datetimePicker', [clonedElement]);
}