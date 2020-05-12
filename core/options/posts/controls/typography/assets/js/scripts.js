jQuery(document).ready(function ($) {
    // for color
    var dmOptions = {
        defaultColor: typo_config.selected_data.color,
        hide: true,
    };
    $('.dm-typography-color-field').wpColorPicker(dmOptions);
    // for select2 
    $('.google-fonts-list').select2();
});

//slider
(function ($) {
    // slide ranger
    $(document).ready(function () {
        $(document).on('change', '.range-slider-font-size', function () {
            document.getElementById('size_value').value = $(this).val();
        });
        $(document).on('change', '.range-slider-line-height', function () {
            document.getElementById('line_height_value').value = $(this).val();
        });
        $(document).on('change', '.range-slide-letter-space', function () {
            document.getElementById('latter_spacing_value').value = $(this).val();
        });
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

    // select 2 on change style and weight
    $('.google-fonts-list').on("change", function (e) {
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
})(jQuery);