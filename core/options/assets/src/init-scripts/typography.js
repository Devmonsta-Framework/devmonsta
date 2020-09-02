jQuery(window).on('devm-scripts.typo', function(e,typo){
    var mainEl = jQuery('.devm-option-typography');
   if(typo){
        mainEl = typo.find('.devm-option-typography');
   }
   if(mainEl && !mainEl.length){ return false; }
    mainEl.each(function(){
        // configuration for color
        var typo_config = jQuery(this).data('config'),
            el = jQuery(this).parents('.devm-option.active-script').find('.google-fonts-list'),
            devmOptions = {
                defaultColor: typo_config.selected_data.color,
                hide: true,
                change: function (event, ui) {
                    // for customizer
                    updateValueHolder(jQuery(this));
                }
            };
        
            jQuery(this).parents('.devm-option.active-script').find('.devm-typography-color-field').wpColorPicker(devmOptions);

        // for select2 
        el.select2();

        // select 2 on change style and weight
        el.on("change", function (e) {
            var self = jQuery(this),
                parent = self.parents('.devm-option-typography'),
                weight = parent.find('.google-weight-list'),
                styleField = parent.find('.google-style-list'),
                selected_value = self.val();
            if (typo_config.font_list.length > 0) {
                jQuery.each(typo_config.font_list, function (key, item) {
                    if (item.family == selected_value) {
                        parent.find('.google-weight-list, .google-style-list').html('');
                        // weight
                        jQuery.each(item.variants, function (i, variant) {
                            let selected = weight.data('selected_value') == variant ? 'selected="selected"' : ''
                            weight.append(
                                '<option '+ selected +' value=' + variant + ' >' + variant + '</option>'
                            );
                        });
                        // style
                        jQuery.each(item.subsets, function (i, style) {
                            let selected = styleField.attr('data-selected_value') == style ? 'selected="selected"' : ''
                            styleField.append(
                                '<option '+ selected +' value=' + style + ' >' + style + '</option>'
                            )
                        });
                        return false
                    }
                })
            }
        });


        el.trigger('change');
    });

    // for customizer update the field value on changed
    jQuery('.google-weight-list, .google-fonts-list, .google-style-list,.typo-font-line-height,.typo-font-letter-space, .typo-font-size').on("change", function () {
        updateValueHolder(jQuery(this));
    });

    /**
     * Update value of value-holder input for customizer
     * @param {*} currentObject 
     */
    function updateValueHolder(currentObject) {
        if(!wp.customize){ return false; }
        var obj = {};
        
        obj["color"] = currentObject.parents(".devm-option-typography").find(".devm-typography-color-field").val() ? rgb2hex(currentObject.parents(".devm-option-typography").find(".devm-typography-color-field").val()) : '';

        if (currentObject.parents(".devm-option-typography").find("li.typo-font-list").length > 0) {
            obj["family"] = currentObject.parents(".devm-option-typography").find("select.google-fonts-list").val();
        }
        if (currentObject.parents(".devm-option-typography").find("li.typo-font-weight").length > 0) {
            obj["weight"] = currentObject.parents(".devm-option-typography").find("select.google-weight-list").val();
        }
        if (currentObject.parents(".devm-option-typography").find("li.typo-font-style").length > 0) {
            obj["style"] = currentObject.parents(".devm-option-typography").find("select.google-style-list").val();
        }
        if (currentObject.parents(".devm-option-typography").find("li.typo-font-size").length > 0) {
            obj["size"] = currentObject.parents(".devm-option-typography").find("input.typo-font-size").val();
        }
        if (currentObject.parents(".devm-option-typography").find("li.typo-font-lineheight").length > 0) {
            obj["line_height"] = currentObject.parents(".devm-option-typography").find("input.typo-font-line-height").val();
        }
        if (currentObject.parents(".devm-option-typography").find("li.typo-font-laterspace").length > 0) {
            obj["letter_spacing"] = currentObject.parents(".devm-option-typography").find("input.typo-font-letter-space").val();
        }
        let finalJsonValue = JSON.stringify(obj);
        currentObject.parents(".devm-option-typography").find("input.input-typo-value-holder").val(finalJsonValue).trigger('change');
    }

    /**
     * convert rgb value into hex value
     * @param {*} rgb 
     */
    function rgb2hex(rgb) {
        if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;

        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);

        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }
});

jQuery(document).ready(function($){
    jQuery(window).trigger('devm-scripts.typo');
});