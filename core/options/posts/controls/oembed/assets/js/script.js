jQuery(document).ready(function($){

    console.log("oembed");
    var ajaxurl = object.ajaxurl;
    console.log(ajaxurl);
    var is_url = function(str) {
	    var pattern = new RegExp(/^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/, 'i');
	    return pattern.test(str);
	};
    
   
    $(".dm-oembed-url-input").on("keyup", 
        _.debounce(function(){
            console.log("key pressed");
            var wrapper = $(this);
            
            var url_input = $(this).val();
            console.log(url_input);

            var iframeWrapper = wrapper.siblings(".dm-oembed-preview");
            if( url_input && is_url( url_input ) ) {
                console.log(wrapper.data('nonce'));
                console.log(wrapper.data('preview'));
                var data = {
                    action : 'get_oembed_response', 
                    _nonce : wrapper.data('nonce'),
                    preview: wrapper.data('preview'),
                    url    : url_input				
                };
                $.post(ajaxurl, data, function(response) {
                    iframeWrapper.html(response);
                });
            }else {
                iframeWrapper.html('');
            }
        
        }, 300)
    );

    //initial trigger of oembed
    $( ".dm-oembed-url-input" ).trigger( "keyup" );

});