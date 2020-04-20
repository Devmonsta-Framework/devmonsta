jQuery(document).ready(function ($) {
	var optionTypeClass = 'dms-option-type-background-image';
	var eventNamePrefix = 'dms:option-type:background-image:';

	dms.options.register('background-image', {
		startListeningForChanges: jQuery.noop,
		getValue: function (optionDescriptor) {
			return {
				value: getValueForEl(optionDescriptor.el),
				optionDescriptor: optionDescriptor
			}
		}
	});

	dmsEvents.on('dms:options:init', function (data) {
		var $options = data.$elements.find('.'+ optionTypeClass +':not(.initialized)');

		$options.toArray().map(function (el) {
			/**
			 * Here we start listening to events triggered by inner option
			 * types. We may receive events from 3 nested option types here:
			 *
			 * 1. radio
			 * 2. image-picker
			 * 3. upload
			 */
			dms.options.on.changeByContext(el, function (optionDescriptor) {
				if (optionDescriptor.type === 'radio') {
					var $predefined = $(
						optionDescriptor.el
					).closest('.dms-inner').find('.predefined');

					var $custom = $(
						optionDescriptor.el
					).closest('.dms-inner').find('.custom');

					getValueForEl(el).then(function (value) {
						var type = value.type

						if (type === 'custom') {
							$predefined.hide();
							$custom.show();
						} else {
							$predefined.show();
							$custom.hide();
						}
					})

				}

				triggerChangeAndInferValueFor(
					// Here we refer to the optionDescriptor.context
					// as to the `background-image` option type container
					optionDescriptor.context
				)
			});
		});

		// route inner image-picker events as this option events
		{
			$options.on(
				'dms:option-type:image-picker:clicked',
				'.dms-option-type-image-picker',
				function(e, data) {
					jQuery(this).trigger(eventNamePrefix + 'clicked', data);
				}
			);

			$options.on(
				'dms:option-type:image-picker:changed',
				'.dms-option-type-image-picker',
				function(e, data) {
					jQuery(this).trigger(eventNamePrefix + 'changed', data);
				}
			);
		}

		$options.addClass('initialized');

		function triggerChangeAndInferValueFor (el) {
			getValueForEl(el).then(function (value) {
				dms.options.trigger.changeForEl(el, {
					value: value
				});
			})

		}

	});

	function getValueForEl (el) {
		var promise = $.Deferred();

		var optionDescriptor = dms.options.getOptionDescriptor(el);

		dms.options.getContextValue(
			optionDescriptor.el
		).then(function (value) {
			promise.resolve(value.value);
		});

		return promise;
	}

});
