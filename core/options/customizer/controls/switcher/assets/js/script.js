jQuery(document).ready(function ($) {
    $('.devm-swticher-box').on('change', function(){
        var name        = $(this).data('name'),
            left_choice = $(this).data('left_choice'),
            right_choice = $(this).data('right_choice'),
            value = ($(this).is(':checked') === true ? right_choice : left_choice );

        wp.customize( name, function ( obj ) {
            obj.set( value );
        } );
    });
});