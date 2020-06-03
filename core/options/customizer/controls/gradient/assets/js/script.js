jQuery(document).ready(function ($) {

    $(".wp-color-result").on('change',
        function () {
            var current_object = $(this);
            console.log("changed");
        });
});