jQuery(document).ready(function($) {

    $('body').on('click', '.devm_upload_image_button', function(e) {

        e.preventDefault();
        var multiple = false,
            self = $(this);
        if ($(this).data('multiple')) {
            multiple = Boolean($(this).data('multiple'));
        }

        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use this image' 
                },
                multiple: multiple
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
                self.parent().find('.devm-upload').trigger('input');

            })
            .open();
    });

    /*
     * Remove image
     */
    $('body').on('click', '.devm_remove_image_button', function() {
        $(this).hide().prev().val('').prev().addClass('button').html('Upload image');
        $(this).parent().find('.devm-upload').trigger('input');
        return false;
    });
});