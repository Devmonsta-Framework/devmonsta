jQuery(document).ready(function($) {
    
    //Initialize the datepicker and set the first day of the week as Monday
    if ($("#dm_multi_select").length) {

        // $(this).find('.select2').remove();
        $('#dm_multi_select').select2({multiple:true});
    }
});