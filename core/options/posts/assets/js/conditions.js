jQuery(document).ready(function($){
    $(document).on('input change','.dm-ctrl', function(){
        var currentControlValue = $(this).val(),
            conditionalInputs = $('.dm-condition-active'),
            currentControlName = $(this).attr('name');

        conditionalInputs.each(function(){
            var conditions = $(this).data('dm_conditions'),
                conditionField =  $(this);

            conditions.every(function(item){
                var condition = item,
                    name = 'devmonsta_' + condition.control_name,
                    oparator = condition.operator,
                    value = condition.value;
                    
                if(currentControlName === name){
                    if(currentControlValue === value){
                        conditionField.addClass('open');
                        return false;
                    }
                    else {
                        conditionField.removeClass('open');
                    }
                    return true;
                } 
            });
        });
    });
})