jQuery(window).on('devm-scripts.conditions',function($){
    function operators(a,b, comparison){  
        switch(comparison) {
            case "<":
                return a < b;
            case "<=":
                return a <= b;
            case ">":
                return a > b;
            case ">=" :
                return a >= b;
            case "==":
                return a == b;
            case "===":
                return a === b;
            case "!=":
                return a != b;
            case "!==":
                return a !== b;
            case "not-empty" :
                return typeof a != 'undefined' && String(a).length > 0;
            case "empty" :
            case "" :
                return typeof a != 'undefined' && String(a).length == 0;
            default:
                return false;
        }
    }

    jQuery(document).on('input change','.devm-ctrl,.devm-icon-picker', function(e, val){
        var currentControlValue = val ? val : jQuery(this).val(),
            conditionalInputs = jQuery('.devm-condition-active'),
            currentControlName = jQuery(this).attr('name'),
            self = jQuery(this),
            values = Array.isArray(currentControlValue) ? currentControlValue : [];

            // checkbox
            if(self.attr('type') == 'checkbox'){
                var checkboxEl = jQuery(this).parents('.devm-option-column').find('input:checked');

                if(checkboxEl.length > 1){
                    jQuery(this).parents('.devm-option-column').find('input:checked').each(function(item){
                        values.push(jQuery(this).val());
                    });
                }
               
                currentControlValue = checkboxEl.length ? (checkboxEl.val() == 'true' ? true : checkboxEl.val() ): false;
            }
            // radio
            if(self.attr('type') == 'radio'){
                currentControlValue = jQuery(this).parents('.devm-option-column').find('input:checked').val();
            }
            // for switcher
            if(self.hasClass('devm-control-switcher')) {
                if(self.is(':checked')) {
                    currentControlValue = self.data('right_key')
                } else {
                    currentControlValue = self.data('left_key')
                }
                
             }

        conditionalInputs.each(function(){
            var conditions = jQuery(this).data('devm_conditions'),
                conditionField =  jQuery(this);
                conditionField.removeClass('applied');
                if( self.parents('.devm-option-column').hasClass('done')){ return false }
                // if value is array
                if(values.length){
                    var conditionValue = conditions.map(item => item.value),
                    is_same = false;
                    values.forEach(function(item){
                        if(conditionValue.indexOf(item) != -1){
                            is_same = true;
                        } else {
                            is_same = false;
                        }
                    });
                    currentControlValue = values;
                }
                // end if value is array

            conditions.forEach(function(item){
                var condition = item,
                    prefix = wp.customize ? '' : 'devmonsta_',
                    name = prefix + condition.control_name,
                    oparator = condition.operator,
                    value = condition.value;
                    // color picker
                    if(typeof value == 'string' && self.hasClass('devm-color-picker-field')){
                        value = value.toLowerCase();
                    } 

                if(conditionField.hasClass('applied')){ return false; }

                if(currentControlName === name){
                    // if value is array
                    if(is_same == true && values.length){
                        currentControlValue = values[0];
                    } 
                    if(operators(currentControlValue, value, oparator)){
                        conditionField.addClass('open');
                        conditionField.addClass('applied');
                    } else {
                        conditionField.removeClass('open');
                    }
                } 
            });
        });
    });

    jQuery('.devm-ctrl').trigger('change');
});

jQuery(document).ready(function(){
    jQuery(window).trigger('devm-scripts.conditions');
});
