(function( $ ) {


    $(document).ajaxSuccess(function (e, request, settings) {

        var object = $.deparam(settings.data);
        if (object.action === 'add-tag' && object.screen === 'edit-post_tag') {
            location.reload();
        }

    });

})( jQuery );
