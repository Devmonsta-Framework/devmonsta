jQuery(document).ready(function($) {
    
    // console.log("entered select 2");
    if ($(".dm_multi_select").length) {
        $('.dm_multi_select').select2({multiple:true});
    }
});