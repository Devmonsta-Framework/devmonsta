jQuery(document).ready(function ($) {

	/* === Script For Multiple Checkbox Control === */
	/* === Script For Switcher Control === */

	$('.customize-control-checkbox-multiple').on(
		'change',
		function () {
			if ( $(this).hasClass("switcher-input") ) {
				var checked = $(this).parents('.customize-control').find('input[type="checkbox"]:checked');
				if(checked.length == 0){
					$(this).parents('.customize-control').find('input[type="hidden"]').val("unchecked_value").trigger('change');
				}else{
					$(this).parents('.customize-control').find('input[type="hidden"]').val(this.value).trigger('change');
				}
			}else{
				checkbox_values = $(this).parents('.customize-control').find('input[type="checkbox"]:checked').map(
					function () {
						return this.value;
					}
				).get().join(',');
				$(this).parents('.customize-control').find('input[type="hidden"]').val(checkbox_values).trigger('change');
			}
		}
	);

});