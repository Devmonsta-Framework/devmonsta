jQuery(document).ready(function ($) {

	/* === Script For Multiple Checkbox Control === */

	$('.customize-control-checkbox-multiple').on(
		'change',
		function () {
			checkbox_values = $(this).parents('.customize-control').find('input[type="checkbox"]:checked').map(
				function () {
					return this.value;
				}
			).get().join(',');
			$(this).parents('.customize-control').find('input[type="hidden"]').val(checkbox_values).trigger('change');
		}
	);

});