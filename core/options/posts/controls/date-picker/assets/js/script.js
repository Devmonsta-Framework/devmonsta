jQuery(document).ready(function($) {

    // console.log(start_day);

    //Initialize the datepicker and set the first day of the week as Monday
    if ($("#dm-date-picker").length) {

        $('#dm-date-picker').datepicker({
            firstDay: start_day,
            dateFormat: "yy-mm-dd",
            onSelect: function() {
                $(this).val();
            }
        });
    }
});