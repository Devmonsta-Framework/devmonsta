jQuery(document).ready(function ($) {
    // console.log(switcher_data);
    $(".dm_switcher_item").click(function (e) {
        e.preventDefault();
        var checkBoxe_right = $(".dm_switcher_right");
        var checkBoxe_left = $(".dm_switcher_left");

        var left_key = switcher_data.left_key;
        var right_key = switcher_data.right_key;

        checkBoxe_right.attr('checked', !checkBoxe_right.attr('checked'));
        if (checkBoxe_right.attr('checked')) {
            checkBoxe_left.attr('checked', false);
            
            wp.customize( switcher_data.settings_id, function ( obj ) {
                obj.bind( function( right_key ) {});
                obj.set( right_key );
            } );
        } else {
            checkBoxe_left.attr('checked', true);
            
            wp.customize( switcher_data.settings_id, function ( obj ) {
                obj.bind( function( left_key ) {});
                obj.set( left_key );
            } );
        }
    });
});