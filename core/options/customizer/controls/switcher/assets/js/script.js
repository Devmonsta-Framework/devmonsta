jQuery(document).ready(function ($) {

    /* === Script For Switcher Control === */

    $('.customize-control-switcher').on(
        'change',
        function () {
            var checked = $(this).parents('.dm-switcher').find('input[type="checkbox"]:checked');
            if (checked.length == 0) {
                $(this).parents('.dm-switcher').find('input[type="hidden"]').val("unchecked_value").trigger('change');
            } else {
                $(this).parents('.dm-switcher').find('input[type="hidden"]').val(this.value).trigger('change');
            }
        }
    );
});