(function($, dmse) {
	//jQuery.dmsDatetimepicker.setLocale(jQuery('html').attr('lang').split('-').shift());

	var init = function() {
		var $container = $(this),
			$input = $container.find('.dms-option-type-text'),
			data = {
				options: $container.data('datetime-attr'),
				el: $input,
				container: $container
			};

		dmse.trigger('dms:options:datetime-picker:before-init', data);

		$input.dmsDatetimepicker(data.options)
			.on('change', function (e) {
				dms.options.trigger.changeForEl(
					jQuery(e.target).closest('[data-dms-option-type="datetime-picker"]'), {
						value: e.target.value
					}
				)
			});
	};

	dms.options.register('datetime-picker', {
		startListeningForChanges: $.noop,
		getValue: function (optionDescriptor) {
			return {
				value: $(optionDescriptor.el).find(
					'[data-dms-option-type="text"]'
				).find('> input').val(),
				optionDescriptor: optionDescriptor
			}
		}
	})

	dmse.on('dms:options:init', function(data) {
		data.$elements
			.find('.dms-option-type-datetime-picker').each(init)
			.addClass('dms-option-initialized');
	});

})(jQuery, dmsEvents);
