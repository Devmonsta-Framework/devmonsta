(function($, dmse) {

	dmse.on('dms:options:init', function(data) {

		data.$elements
			.find(
				'.dms-option-type-multi-picker:not(.dms-option-initialized)'
			)
			.not(
				'.dms-option-type-multi-picker-dynamic'
			)
			.each(initSimpleMultiPicker)
			.addClass('dms-option-initialized');

		data.$elements
			.find(
				'.dms-option-type-multi-picker.dms-option-type-multi-picker-dynamic'
			)
			.not(
				'.dms-option-initialized'
			)
			.each(initDynamicMultiPicker)
			.addClass('dms-option-initialized');

	});

	dmse.on('dms:options:teardown', function (data) {

		data.$elements
			.find(
				'.dms-option-type-multi-picker.dms-option-type-multi-picker-dynamic'
			).filter('.dms-option-initialized')
			.each(function () {
				if ($(this).data().dmsPickerListener) {
					dms.options.off.change($(this).data().dmsPickerListener);
				}
			})
	})

	function initDynamicMultiPicker () {
		var $container = $(this);

		$container.closest(
			'.dms-backend-option-type-multi-picker'
		).addClass('dms-option-type-multi-picker-dynamic-container');

		$container.addClass('dms-option-initialized');

		var optionDescriptor = dms.options.getOptionDescriptor($container[0]);

		var pickerDescriptor = dms.options.findOptionInSameContextFor(
			optionDescriptor.el,
			$container.attr('data-dms-dynamic-picker-path')
		);

		$container.find('> .choice-group').first().addClass('chosen');

		$container.data('dms-picker-listener', handleChange);

		dms.options.on.change($container.data().dmsPickerListener);

		chooseGroupForOptionDescriptor(pickerDescriptor);

		function handleChange (optionDescriptor) {
			if (pickerDescriptor.el === optionDescriptor.el) {
				setTimeout(function () {
					chooseGroupForOptionDescriptor(optionDescriptor);
				}, 0);
			}
		}

		function chooseGroupForOptionDescriptor (optionDescriptor) {
			dms.options.getValueForEl(pickerDescriptor.el).then(function (value) {
				// TODO: implement interfaces for multiple compound option types
				if (pickerDescriptor.type === 'icon-v2') {
					chooseGroup(
						value.value.type === 'none' ? '' : value.value.type
					);
				} else {
					if (! _.isString(value.value)) {
						throw "Your picker returned a non-string value. In order for it to work with multi-pickers it should yield string values";
					}

					chooseGroup(value.value);
				}
			});

			function chooseGroup(groupId) {
				var $choicesGroups = $container.find('> .choice-group');

				var $choicesToReveal = $container.find(
					'.choice-group[data-choice-key="'+ groupId +'"]'
				);

				$choicesGroups.removeClass('chosen');
				$choicesToReveal.addClass('chosen');

				if ($choicesToReveal.length) {
					$container.addClass('has-choice');

					$container.closest(
						'.dms-backend-option-type-multi-picker'
					).addClass('dms-has-dynamic-choice');
				} else {
					$container.removeClass('has-choice');

					$container.closest(
						'.dms-backend-option-type-multi-picker'
					).removeClass('dms-has-dynamic-choice');
				}
			};
		}

	}

	function initSimpleMultiPicker() {
		var $this = $(this);

		var elements = {
			$pickerGroup: $this.find('> .picker-group'),
			$choicesGroups: $this.find('> .choice-group')
		};

		var chooseGroup = function(groupId) {
			var $choicesToReveal = elements.$choicesGroups.filter('.choice-group[data-choice-key="'+ groupId +'"]');

			/**
			 * The group options html was rendered in an attribute to make page load faster.
			 * Move the html from attribute in group and init options with js.
			 */
			if ($choicesToReveal.attr('data-options-template')) {
				$choicesToReveal.html(
					$choicesToReveal.attr('data-options-template')
				);

				$choicesToReveal.removeAttr('data-options-template');

				dmsEvents.trigger('dms:options:init', {
					$elements: $choicesToReveal
				});
			}

			elements.$choicesGroups.removeClass('chosen');
			$choicesToReveal.addClass('chosen');

			if ($choicesToReveal.length) {
				$this.addClass('has-choice');
			} else {
				$this.removeClass('has-choice');
			}
		};


		var pickerType = elements.$pickerGroup.attr('class').match(/picker-type-(\S+)/)[1];

		var flows = {
			'switch': function() {
				elements.$pickerGroup.find(':checkbox').on('change', function() {
					var $this = $(this),
						checked = $(this).is(':checked'),
						value = JSON.parse($this.attr('data-switch-'+ (checked ? 'right' : 'left') +'-value-json'));

					chooseGroup(value);
				}).trigger('change');
			},
			'select': function() {
				elements.$pickerGroup.find('select').on('change', function() {
					chooseGroup(this.value);
				}).trigger('change');
			},
			'short-select': function() {
				this.select();
			},
			'radio': function() {
				elements.$pickerGroup.find(':radio').on('change', function() {
					chooseGroup(this.value);
				}).filter(':checked').trigger('change');
			},
			'image-picker': function() {
				elements.$pickerGroup.find('select').on('change', function() {
					chooseGroup(this.value);
				}).trigger('change');
			},
			'icon-v2': function () {
				var iconV2Selector = '.dms-option-type-icon-v2 > input';

				elements.$pickerGroup.find(iconV2Selector).on('change', function() {
					var type = JSON.parse(this.value)['type'];
					chooseGroup(type);
				}).trigger('change');
			}
		};

		if (! pickerType) {
			console.error('unknown multi-picker type:', pickerType);
		} else {
			if (flows[pickerType]) {
				flows[pickerType]();
			} else {
				var eventName = 'dms:option-type:multi-picker:init:'+ pickerType;

				if (dmse.hasListeners(eventName)) {
					dmse.trigger(eventName, {
						'$pickerGroup': elements.$pickerGroup,
						'chooseGroup': chooseGroup
					});
				} else {
					console.error('uninitialized multi-picker type:', pickerType);
				}
			}
		}
	};

	dms.options.register('multi-picker', {
		getValue: dms.options.get('multi').getValue
	})
})(jQuery, dmsEvents);
