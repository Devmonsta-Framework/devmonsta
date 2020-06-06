jQuery(window).on('dm-vue.dm', function(e, val){
    let elements = jQuery('.dm-vue-app.active-script');
    if(val){
        elements = val.find('.dm-vue-app.active-script');
    }
    elements.each(function (item) {
        new Vue({
            el: jQuery(this)[0]
        });
    });
});
jQuery(document).ready(function(){
    jQuery(window).trigger('dm-vue.dm');
});



