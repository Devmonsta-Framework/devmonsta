(function ($, dmsEvents) {
	var defaults = {
		grid: true
	};

	dmsEvents.on('dms:options:init', function (data) {
		data.$elements.find('.dms-option-type-slider:not(.initialized)').each(function () {
			var options = JSON.parse($(this).attr('data-dms-irs-options'));
			var slider = $(this).find('.dms-irs-range-slider').ionRangeSlider(_.defaults(options, defaults));
		}).addClass('initialized');
	});

})(jQuery, dmsEvents);
