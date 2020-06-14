jQuery(window).on('dm-scripts.oembed', function(){
    var ajaxurl = object.ajaxurl;
    var is_url = function (str) {
        var pattern = new RegExp(/^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/, 'i');
        return pattern.test(str);
    };


    jQuery(document).on("keyup",".dm-oembed-url-input",
         function () {
            var wrapper = jQuery(this);
            var url_input = jQuery(this).val();

            var iframeWrapper = wrapper.siblings(".dm-oembed-preview");
            console.log(url_input);
                if (url_input) {
                    console.log("entered if");
                    var data = {
                        action: "get_oembed_response",
                        _nonce: wrapper.data('nonce'),
                        preview: wrapper.data('preview'),
                        url: url_input
                    };
    
                    jQuery.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: data,
                        success: function (response) {
                            iframeWrapper.html(response);
                            console.log("response received" + response);
                        },
                    });
                } else {
                    console.log("entered else");
                    iframeWrapper.html('');
                }
        }
    );

    //initial trigger of oembed
    jQuery(".dm-oembed-url-input").trigger("keyup");

});



jQuery(document).ready(function($) {
    jQuery(window).trigger('dm-scripts.oembed');
});