(function ($, dmsEvents) {
	var defaults = {
		grid: true
	};

	dmsEvents.on('dms:options:init', function (data) {
		data.$elements.find('.dms-option-type-range-slider:not(.initialized)').each(function () {
			var options = JSON.parse($(this).attr('data-dms-irs-options'));
			$(this).find('.dms-irs-range-slider').ionRangeSlider(_.defaults(options, defaults));

			$(this).find('.dms-irs-range-slider').on('change', _.throttle(function (e) {
				dms.options.trigger.changeForEl(e.target, {
					value: getValueForEl(e.target)
				})
			}, 300));
		}).addClass('initialized');
	});

	dms.options.register('range-slider', {
		startListeningForChanges: $.noop,
		getValue: function (optionDescriptor) {
			return {
				value: getValueForEl(
					$(optionDescriptor.el).find('[type="text"]')[0]
				),

				optionDescriptor: optionDescriptor
			}
		}
	});

	function getValueForEl (el) {
		var rangeArray = el.value.split(';');

		return {
			from: rangeArray[0],
			to: rangeArray[1]
		}
	}

})(jQuery, dmsEvents);
