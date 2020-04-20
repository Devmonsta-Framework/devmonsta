(function($) {
	var simpleInputs = [
		'text',
		'short-text',
		'hidden',
		'password',
		'textarea',
		'html',
		'html-fixed',
		'html-full',
		'select',
		'short-select',
		'gmap-key',
		'slider',
		'short-slider',
	];

	simpleInputs.map(function(optionType) {
		dms.options.register(optionType, {
			getValue: getValueForSimpleInput,
		});
	});

	function getValueForSimpleInput(optionDescriptor) {
		return {
			value: optionDescriptor.el.querySelector('input, textarea, select')
				.value,
			optionDescriptor: optionDescriptor,
		};
	}

	dms.options.register('unique', {
		getValue: function(optionDescriptor) {
			var actualValue = optionDescriptor.el.querySelector(
				'input, textarea, select'
			).value;

			return {
				value: !!actualValue.trim() ? actualValue : dms.randomMD5(),
				optionDescriptor: optionDescriptor,
			};
		},
	});

	dms.options.register('checkbox', {
		getValue: function(optionDescriptor) {
			return {
				value: optionDescriptor.el.querySelector(
					'input.dms-option-type-checkbox'
				).checked,
				optionDescriptor: optionDescriptor,
			};
		},
	});

	dms.options.register('checkboxes', {
		getValue: function(optionDescriptor) {
			var checkboxes = $(optionDescriptor.el)
				.find('[type="checkbox"]')
				.slice(1);

			var value = {};

			checkboxes.toArray().map(function(el) {
				value[$(el).attr('data-dms-checkbox-id')] = el.checked;
			});

			return {
				value: value,
				optionDescriptor: optionDescriptor,
			};
		},
	});

	dms.options.register('radio', {
		getValue: function(optionDescriptor) {
			return {
				value: $(optionDescriptor.el).find('input:checked').val(),
				optionDescriptor: optionDescriptor,
			};
		},
	});

	dms.options.register('select-multiple', {
		getValue: function(optionDescriptor) {
			return {
				value: $(optionDescriptor.el.querySelector('select')).val(),
				optionDescriptor: optionDescriptor,
			};
		},
	});

	dms.options.register('multi', {
		getValue: function(optionDescriptor) {
			var promise = $.Deferred();

			dms.options
				.getContextValue(optionDescriptor.el)
				.then(function(result) {
					promise.resolve({
						value: result.value,
						optionDescriptor: optionDescriptor,
					});
				});

			return promise;
		},
	});
})(jQuery);
