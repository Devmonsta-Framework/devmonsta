jQuery(window).on('load',function($){

    jQuery('.devm-ctrl').each(function(){
        var name = jQuery(this).attr('data-customize-setting-link'),   
            value = jQuery(this).data('value');

            var self = jQuery(this);
        wp.customize( name, function ( obj ) {
            if(self.hasClass('devm-control-switcher')){
                var rightKey = self.data('right_key');
                if(rightKey == value){
                    value = 1;
                } else {
                    value = 0;
                }
            }
            obj.set( value );
            self.parents('.devm-option').addClass('active-script');
            jQuery(window).trigger('devm-scripts.dm');
        } );
    });
    
    // console.log("dui bar");
    jQuery(window).trigger('devm-scripts.oembed');


});