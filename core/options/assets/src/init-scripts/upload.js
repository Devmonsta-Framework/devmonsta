(function ($) {
    'use strict';

    $(function () {
        var sortValue = function ($el, imgID) {
            var ids = $el.val().split(','),
                indx = ids.indexOf( imgID );

            if ( indx !== -1 ) {
                ids.splice(indx, 1);
            }

            return ids.join(',');
        };

        var setValue = function ($el, $val) {
            if ('customize' in wp) {
                wp.customize( $el.attr('name'), function ( obj ) {
                    obj.set( $val );
                } );
            } else {
                $el.val( $val ).trigger('input change');
            }
        };

        $('body').on('click', '.devm-option-upload--child', function () {
            var $el = $(this),
                $parent = $el.parents('.devm-option-upload--list'),
                $input = $parent.next('input'),
                isMulti = $parent.data('multiple') ? 'add' : false,
                uploader;

            // Register: Media Library
            uploader = wp.media({
                title: 'Insert Image',
                library: {
                    type: 'image'
                },
                multiple: isMulti
            }).on('open', function () {
                var selection = uploader.state().get('selection'),
                    img_ids = $input.val();
                
                if ( img_ids.length ) {
                    img_ids = img_ids.split(',');

                    img_ids.forEach(function (id) {
                        selection.add( wp.media.attachment(id) );
                    });
                }
            }).on('select', function () {
                var imgList = uploader.state().get('selection').toJSON();

                $parent.empty();

                $.each(imgList, function (indx, img) {
                    $parent.append(`<div class="devm-option-upload--item">
                        <img src="${img.url}" class="devm-option-upload--child">
                        <button type="button" class="devm-option-upload--remove dashicons dashicons-dismiss" data-id="${img.id}"></button>
                    </div>`);
                });

                setValue( $input, imgList.map(val => val.id).join(',') );
            });

            // Open: Media Library
            uploader.open();
        }).on('click', '.devm-option-upload--remove', function () {
            var $el = $(this),
                $parent = $el.parents('.devm-option-upload--list'),
                strImg = $parent.data('multiple') ? 'Images' : 'Image',
                $input = $parent.next('input'),
                imgID = String( $el.data('id') );

            // update: values
            var sortedVal = sortValue( $input, imgID );
            setValue( $input, sortedVal );

            // visual: remove image
            $el.parent().remove();

            // add: button
            if ( $parent[0].childElementCount === 0 ) {
                $parent.html('<div class="devm-option-upload--item has--btn"><button type="button" class="devm-option-upload--child button">Upload '+ strImg +'</button></div>');
            }
        });
    });
}(jQuery));
