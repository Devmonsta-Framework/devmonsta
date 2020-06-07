( function( $ ) {
    wp.customizerCtrlEditor = {

        init: function() {

            $(window).load(function(){

                $('textarea.wp-editor-area').each(function(){
                    var tArea = $(this),
                        id = tArea.attr('id'),
                        editor = tinyMCE.get(id),
                        setChange,
                        content;

                    if(editor){
                        editor.onChange.add(function (ed, e) {
                            // console.log("change");
                            ed.save();
                            content = editor.getContent();
                            clearTimeout(setChange);
                            setChange = setTimeout(function(){
                                tArea.val(content).trigger('change');
                            },500);
                        });
                    }

                    tArea.css({
                        visibility: 'visible'
                    }).on('keyup', function(){
                        // console.log("change");
                        content = tArea.val();
                        clearTimeout(setChange);
                        setChange = setTimeout(function(){
                            content.trigger('change');
                        },500);
                    });
                });
            });
        }

    };

    wp.customizerCtrlEditor.init();

} )( jQuery );