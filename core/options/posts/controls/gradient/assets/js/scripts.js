(function($){
	dmEvents.on('dm:options:init', function (data) {
		data.$elements.find('.dm-option.dm-option-type-gradient:not(.initialized)').each(function(){
			var $option = $(this);

			// update secondary color when primary color has changed
			$option.on('dm:color:picker:changed', '.dm-option-type-color-picker.primary', function (event, data) {
				var $secondary = $option.find('.dm-option-type-color-picker.secondary:first');

				if (!$secondary.hasClass('iris-initialized')) {
					$secondary
						.trigger('focus') // color-picker is lazy initialized on 'focus'
						.iris('hide');
				}

				$secondary.val(data.ui.color.toString()).trigger('change');
			});
		}).addClass('initialized');
	});
})(jQuery);