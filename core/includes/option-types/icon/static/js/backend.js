jQuery(function ($) {
	var optionTypeClass = '.dms-option-type-icon';

	dmsEvents.on('dms:options:init', function (data) {

		var $options = data.$elements.find(optionTypeClass +':not(.initialized)');

		// handle click on an icon
		$options.find('.js-option-type-icon-item').on('click', function () {
			var $this = $(this);

			if ($this.hasClass('active')) {
				$this.removeClass('active');
				$this.closest(optionTypeClass).find('input').val('').trigger('change');
			} else {
				$this.addClass('active').siblings().removeClass('active');
				$this.closest(optionTypeClass).find('input').val($this.data('value')).trigger('change');
			}
		});

		// handle changing active category
		$options.find('.js-option-type-icon-dropdown')
			.on('change', function () {
				var $this = $(this);
				var group = $this.val();

				$this.closest(optionTypeClass).find('.js-option-type-icon-item').each(function () {
					$(this).toggle(group == 'all' || group == $(this).data('group'));
				});
			})
			.trigger('change');

		$options.addClass('initialized');

	});

});
