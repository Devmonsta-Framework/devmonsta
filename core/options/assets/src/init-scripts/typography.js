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
    })
});

jQuery(document).ready(function($){
    jQuery(window).trigger('devm-scripts.typo');
});