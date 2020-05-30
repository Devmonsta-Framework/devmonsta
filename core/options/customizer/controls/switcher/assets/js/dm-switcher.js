jQuery(document).ready(function ($) {
    // console.log("reached switcher");
    $(".dm_switcher_item").click(function () {
        var checkBoxe_right = $(".dm_switcher_right");
        var checkBoxe_left = $(".dm_switcher_left");

        checkBoxe_right.attr('checked', !checkBoxe_right.attr('checked'));
        if (checkBoxe_right.attr('checked')) {
            checkBoxe_left.attr('checked', false);
            // $(this).siblings(".dm_switcher_value").val("0");
            // console.log("right checked");
        } else {
            checkBoxe_left.attr('checked', true);
            // $(this).siblings(".dm_switcher_value").val("1");
            // console.log("left checked");
        }
    });
});