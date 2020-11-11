jQuery(window).on('load',function($){

    jQuery('.devm-ctrl').each(function(){
        var self = jQuery(this),
            name = jQuery(this).attr('data-customize-setting-link'),   
            value = jQuery(this).data('value');

        wp.customize( name, function ( obj ) {
            obj.set( value );
            self.parents('.devm-option').addClass('active-script');
            jQuery(window).trigger('devm-scripts.dm');
        } );
        self.parents('.devm-option').addClass('active-script');
    });
   
    jQuery(window).trigger('devm-scripts');
});