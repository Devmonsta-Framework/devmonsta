jQuery(window).on('load',function($){

    jQuery('input.dm-ctrl').each(function(){
        var name = jQuery(this).attr('data-customize-setting-link'),   
            value = jQuery(this).attr('data-value');

            var self = jQuery(this);
        wp.customize( name, function ( obj ) {
            if(self.hasClass('dm-control-switcher')){
                var rightKey = self.data('right_key');
                if(rightKey == value){
                    value = 1;
                } else {
                    value = 0;
                }
            }
            obj.set( value );
            self.parents('.dm-option').addClass('active-script');
            jQuery(window).trigger('dm-scripts.dm');
        } );
    });
    
    // console.log("dui bar");
    jQuery(window).trigger('dm-scripts.oembed');


});