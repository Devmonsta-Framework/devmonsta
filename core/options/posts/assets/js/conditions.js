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
        
        var self = $(this),
            currentControlValue = val ? val : $(this).val(),
            conditionalInputs = $('.dm-condition-active'),
            currentControlName = $(this).attr('name');
            self.parents('.dm-option-column').removeClass('applied');

            if($(this).attr('type') == 'checkbox'){
                var values = [];
                $(this).parents('.dm-option-column').find('input:checked').each(function(item){
                    values.push($(this).val());
                });

                currentControlValue = values;
            }

        conditionalInputs.each(function(){
            var conditions = $(this).data('dm_conditions'),
                conditionField =  $(this);
                conditionField.removeClass('applied');

            conditions.forEach(function(item){
                var condition = item,
                    name = 'devmonsta_' + condition.control_name,
                    oparator = condition.operator,
                    value = condition.value,
                    arrIndex = values.indexOf(value);

                    if(arrIndex != -1){
                        currentControlValue = values[arrIndex];
                    }


                if(self.parents('.dm-option-column').hasClass('applied') ){ return false; }

                if(currentControlName === name){
                    if(operators(currentControlValue, value, oparator)){
                        conditionField.addClass('open');
                        conditionField.addClass('applied');
                        if(self.attr('type') == 'checkbox'){
                            self.parents('.dm-option-column').addClass('applied');
                        }
                    }
                    else {
                        conditionField.removeClass('open');
                    }
                } 
            });
        });
    });

    $('.dm-ctrl').trigger('change');
})