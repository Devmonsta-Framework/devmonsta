jQuery(window).on('dm-scripts-typo.dm', function(e,val){
    var dmColorOptions = {
        defaultColor: dm_color_picker_config.default,
        hide: true,
        palettes: dm_color_picker_config.palettes
    };
    var el = jQuery('.dm-option.active-script .google-fonts-list');
   // for color
   var dmOptions = {
        defaultColor: typo_config.selected_data.color,
        hide: true,
    };
    jQuery('.dm-option.active-script .dm-typography-color-field').wpColorPicker(dmOptions);

    // for select2 
    el.select2();

    // select 2 on change style and weight
    el.on("change", function (e) {
        var self = jQuery(this),
            parent = self.parents('.dm-option-typography'),
            selected_value = self.val();
        if (typo_config.font_list.length > 0) {
            jQuery.each(typo_config.font_list, function (key, item) {
                if (item.family == selected_value) {
                    parent.find('.google-weight-list, .google-style-list').html('');
                    // weight
                    jQuery.each(item.variants, function (i, variant) {
                        parent.find('.google-weight-list').append(
                            '<option value=' + variant + ' >' + variant + '</option>'
                        );
                    });
                    // style
                    jQuery.each(item.subsets, function (i, style) {
                        parent.find('.google-style-list').append(
                            '<option value=' + style + ' >' + style + '</option>'
                        )
                    });
                    return false
                }
            })
        }
    });


    el.trigger('change');
});

jQuery(document).ready(function($){
    jQuery(window).trigger('dm-scripts-typo.dm');
});