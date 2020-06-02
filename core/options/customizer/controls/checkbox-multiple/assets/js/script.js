jQuery(document).ready(function ($) {

	/* === Checkbox Multiple Control === */

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