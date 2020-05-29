let elements = document.querySelectorAll('.dm-box');
elements.forEach(function (item) {
    new Vue({
        el: item
    });
});

let taxonomyEl = document.getElementById('addtag');
if (taxonomyEl) {
    new Vue({
        el: taxonomyEl
    });
}

jQuery(document).ready(function ($) {
    // $('.dm-color-field').wpColorPicker();

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    $(".dm-repeater-add-new").click(function () {
        var id = $(this).attr('data-id');
        // $($('#dm-repeater-template').html()).insertAfter('#dm-repeater-section');


        var html_content = $('#dm-repeater-template-' + id).html();
        $('#dm-repeater-section-' + id).append(html_content);

        $('#dm-repeater-section-' + id + ' [class^="dm-option-input"]').each(function () {
            $(this).attr('name', 'repeater_control_' + id + '_' + makeid(5));
        });

        $('.dm-repeater-delete-btn').click(function () {

            $(this).closest('div').remove();

        });

    });


});