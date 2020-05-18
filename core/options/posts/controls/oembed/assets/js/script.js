jQuery(document).ready(function($){

    var ajaxurl = object.ajaxurl;
    var is_url = function(str) {
	    var pattern = new RegExp(/^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/, 'i');
	    return pattern.test(str);
	};
    
   
    $(".dm-oembed-url-input").on("keyup", 
        _.debounce(function(){
            var wrapper = $(this);
            
            var url_input = $(this).val();

            var iframeWrapper = wrapper.siblings(".dm-oembed-preview");
            if( url_input && is_url( url_input ) ) {
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