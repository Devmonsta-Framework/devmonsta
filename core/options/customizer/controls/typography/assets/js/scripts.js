jQuery(document).ready(function ($) {

    // for color
    var dmOptions = {
        defaultColor: typo_config.selected_data.color,
        hide: true,
        change: function (event, ui) {
            //update value of value-holder
            var currentObject = $(this);
            updateValueHolder(currentObject);
        }
    };
    $('.dm-typography-color-field').wpColorPicker(dmOptions);

    // for select2 
    $('.google-fonts-list').select2();

    // select 2 on change style and weight
    $('.google-fonts-list').on("change", function (e) {
        
        //update value of value-holder
        var currentObject = $(this);
        updateValueHolder(currentObject);

        var selected_value = $(".google-fonts-list option:selected").text();
        $('.google-weight-list').html(" ");
        $('.google-style-list').html(" ");

        if (typo_config.font_list.length > 0) {
            $.each(typo_config.font_list, function (key, item) {
                if (item.family == selected_value) {
                    // weight
                    $.each(item.variants, function (i, variant) {
                        $('.google-weight-list').append(
                            '<option value=' + variant + ' >' + variant + '</option>'
                        );
                    });
                    // style
                    $.each(item.subsets, function (i, style) {
                        $('.google-style-list').append(
                            '<option value=' + style + ' >' + style + '</option>'
                        )
                    });

                }
            })
        }
    });

    // select 2 on change weight
    $('.google-weight-list').on("change", function () {
        //update value of value-holder
        var currentObject = $(this);
        updateValueHolder(currentObject);
    });

    // select 2 on change style
    $('.google-style-list').on("change", function () {
        //update value of value-holder
        var currentObject = $(this);
        updateValueHolder(currentObject);
    });


    // if value selected
    if (typo_config.selected_data.style !== '') {
        $('.google-weight-list').html(" ");
        $('.google-style-list').html(" ");
        $.each(typo_config.font_list, function (key, item) {
            if (item.family == typo_config.selected_data.family) {
                // weight
                $.each(item.variants, function (i, variant) {
                    var selected_weight = variant == typo_config.selected_data.weight ? 'selected' : '';
                    $('.google-weight-list').append(
                        '<option value=' + variant + ' ' + selected_weight + '>' + variant + '</option>'
                    );
                    // style
                    $.each(item.subsets, function (i, style) {
                        var selected_style = style == typo_config.selected_data.style ? 'selected' : '';
                        $('.google-style-list').append(
                            '<option value=' + style + ' ' + selected_style + '>' + style + '</option>'
                        )
                    });
                });
            }
        })
    }

    // slide ranger
    $('.typo-font-size').on('change', function () {
        // document.getElementById('size_value').value = $(this).val();
        //update value of value-holder
        var currentObject = $(this);
        updateValueHolder(currentObject);
    });
    $('.typo-font-line-height').on('change', function () {
        // document.getElementById('line_height_value').value = $(this).val();
        //update value of value-holder
        var currentObject = $(this);
        updateValueHolder(currentObject);
    });
    $('.typo-font-letter-space').on('change', function () {
        // document.getElementById('latter_spacing_value').value = $(this).val();
        //update value of value-holder
        var currentObject = $(this);
        updateValueHolder(currentObject);
    });


    /**
     * Update value of value-holder input
     * @param {*} currentObject 
     */
    function updateValueHolder(currentObject) {
        var obj = {};

        if (currentObject.parents(".dm-option-typography").find("li.typo-font-color").length > 0) {
            obj["color"] = rgb2hex(currentObject.parents(".dm-option-typography").find(".wp-color-result")[0].style.backgroundColor);
        }
        if (currentObject.parents(".dm-option-typography").find("li.typo-font-list").length > 0) {
            obj["family"] = currentObject.parents(".dm-option-typography").find("select.google-fonts-list").val();
        }
        if (currentObject.parents(".dm-option-typography").find("li.typo-font-weight").length > 0) {
            obj["weight"] = currentObject.parents(".dm-option-typography").find("select.google-weight-list").val();
        }
        if (currentObject.parents(".dm-option-typography").find("li.typo-font-style").length > 0) {
            obj["style"] = currentObject.parents(".dm-option-typography").find("select.google-style-list").val();
        }
        if (currentObject.parents(".dm-option-typography").find("li.typo-font-size").length > 0) {
            obj["size"] = currentObject.parents(".dm-option-typography").find("input.typo-font-size").val();
        }
        if (currentObject.parents(".dm-option-typography").find("li.typo-font-lineheight").length > 0) {
            obj["line_height"] = currentObject.parents(".dm-option-typography").find("input.typo-font-line-height").val();
        }
        if (currentObject.parents(".dm-option-typography").find("li.typo-font-laterspace").length > 0) {
            obj["letter_spacing"] = currentObject.parents(".dm-option-typography").find("input.typo-font-letter-space").val();
        }
        let finalJsonValue = JSON.stringify(obj);
        currentObject.parents(".dm-option-typography").find("input.input-typo-value-holder").val(finalJsonValue).trigger('change');
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