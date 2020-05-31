jQuery(document).ready(function ($) {
    jQuery('.dm-repeater-add-new').on('click', function(e){
        e.preventDefault();
        var id = $(this).data('id');

        var repeaterControl = $('.dm-'+id+'-repeater-control'),
            clonedElement = repeaterControl.first().clone();

            let href = clonedElement.find('.dm-repeater-control-action')[0].href;
            href = href.replace(/inlineId=(\w+)/, 'inlineId=$1_' + (repeaterControl.length + 1));
            clonedElement.find('.dm-repeater-control-action').attr('href', href).end();
            clonedElement.find('.editor-post-trash').attr('data-id', id+'_'+(repeaterControl.length + 1)).end();
            clonedElement.find('.dm-repeater-inner-controls').attr('id', id+'_'+(repeaterControl.length + 1)).end();


        $(this).parents('.dm-repeater-column').find('.dm-repeater-control-list').append(clonedElement);
        // jQuery('.dm-color-picker-field').unbind().wpColorPicker();
        $('.dm_select').empty().trigger('change');
    });
    
});