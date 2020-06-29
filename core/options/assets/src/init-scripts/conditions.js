jQuery(document).ready(function($){

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

    $(document).on('input change','.dm-ctrl', function(e, val){
        var currentControlValue = val ? val : $(this).val(),
            conditionalInputs = $('.dm-condition-active'),
            currentControlName = $(this).attr('name'),
            self = $(this),
            values = Array.isArray(currentControlValue) ? currentControlValue : [];

            // checkbox
            if(self.attr('type') == 'checkbox'){
                $(this).parents('.dm-option-column').find('input:checked').each(function(item){
                    values.push($(this).val());
                });
                currentControlValue = $(this).parents('.dm-option-column').find('input:checked').val();
            }
            // radio
            if(self.attr('type') == 'radio'){
                currentControlValue = $(this).parents('.dm-option-column').find('input:checked').val();
            }
            // for switcher
            if(self.hasClass('dm-control-switcher')) {
                if(self.is(':checked')) {
                    currentControlValue = self.data('right_key')
                } else {
                    currentControlValue = self.data('left_key')
                }
                
             }
           
        conditionalInputs.each(function(){
            var conditions = $(this).data('dm_conditions'),
                conditionField =  $(this);
                conditionField.removeClass('applied');
                if( self.parents('.dm-option-column').hasClass('done')){ return false }
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
                }
                // end if value is array

            conditions.forEach(function(item){
                var condition = item,
                    name = 'devmonsta_' + condition.control_name,
                    oparator = condition.operator,
                    value = condition.value;
                    if(typeof value === 'boolean'){
                        value = String(value);
                    }

                if(conditionField.hasClass('applied')){ return false; }

                if(currentControlName === name){
                    // if value is array
                    if(is_same && values.length){
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

    $('.dm-ctrl').trigger('change');
})