jQuery(document).ready(function ($) {

    /* === Script For Switcher Control === */

    $('.devm-control-switcher').on(
        'change',
        function () {
            var current_object = $(this);
            var checked = current_object.parents('.devm-switcher').find('input[type="checkbox"]:checked');
            
            if (checked.length == 0) {
                current_object.parents('.devm-switcher').find('input[type="hidden"]').val(current_object.parents('.devm-switcher').find('input[type="hidden"]').data("unchecked_value")).trigger('change');
            } else {
               current_object.parents('.devm-switcher').find('input[type="hidden"]').val(this.value).trigger('change');
            }
        }
    );
});