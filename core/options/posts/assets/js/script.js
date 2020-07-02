jQuery(window).on('devm-vue.dm', function(e, val){
    let elements = jQuery('.devm-vue-app.active-script');
    if(val){
        elements = val.find('.devm-vue-app.active-script');
    }
    elements.each(function (item) {
        new Vue({
            el: jQuery(this)[0]
        });
    });
});
jQuery(window).on('load',function(){
    jQuery(window).trigger('devm-vue.dm');
});



