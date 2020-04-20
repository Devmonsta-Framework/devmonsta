jQuery(function($){
	var initialized = false,
		changeTimeoutId = 0,
		randomIdIncrement = 0,
		localized = _dms_backend_customizer_localized,
		/**
		 * @type {Object} {'#options_wrapper_id':'~'}
		 */
		pendingChanges = {},
		/**
		 * Extract all input values within option and save them to the customizer input (to trigger preview update)
		 */
		processPendingChanges = function(){
			$.each(pendingChanges, function(optionsWrapperId){
				var $optionsWrapper = $('#'+ optionsWrapperId),
					$input = $optionsWrapper.closest('.dms-backend-customizer-option')
						.find('> input.dms-backend-customizer-option-input'),
					newValue = JSON.stringify(fixSerializedValues(
						$optionsWrapper.find(':input').serializeArray()
					));

				if ($input.val() === newValue) {
					return;
				}

				$input.val(newValue).trigger('change');
			});

			pendingChanges = {};
		},
		fixSerializedValues = function(values) {
			var inputNameToIndex = {},
				fixedValues = [];

			/**
			 * Traverse reversed array to leave only the last values.
			 * This is how _POST works, if you have
			 * dms_options[option_name][x]: 3
			 * dms_options[option_name][x]: 7
			 * the last one "wins" and the value of $_POST['dms_options']['option_name']['x'] will be 7
			 */
			for (var i = values.length - 1; i >= 0; i--) {
				if (values[i].name.slice(-2) === '[]') {
					// this will be sent in _POST as array
				} else {
					if (typeof inputNameToIndex[values[i].name] === 'undefined') {
						inputNameToIndex[values[i].name] = i;
					} else {
						continue; // skip if already added (the last overwrites others)
					}
				}

				fixedValues.push(values[i]);
			}

			/**
			 * The array was traversed in revers order, now restore the initial order
			 */
			return fixedValues.reverse();
		},
		init = function(){
			if (initialized) {
				return;
			}

			/**
			 * Populate all <input class="dms-backend-customizer-option-input" ... /> with (initial) options values
			 */
			$('#customize-theme-controls .dms-backend-customizer-option').each(function(){
				$(this).find('> input.dms-backend-customizer-option-input').val(
					JSON.stringify(fixSerializedValues(
						$(this).find('> .dms-backend-customizer-option-inner :input').serializeArray()
					))
				);
			});

			/**
			 * When something may be changed, removed, added; add to pending changes
			 */
			$('#customize-theme-controls').on(
				'change keyup click paste',
				'.dms-backend-customizer-option > .dms-backend-customizer-option-inner > .dms-backend-option > .dms-backend-option-input',
				function(e){
					clearTimeout(changeTimeoutId);

					{
						var optionsWrapperId = $(this).attr('id');

						if (!optionsWrapperId) {
							optionsWrapperId = 'rnid-'+ (++randomIdIncrement);
							$(this).attr('id', optionsWrapperId);
						}

						pendingChanges[optionsWrapperId] = '~';
					}

					changeTimeoutId = setTimeout(
						processPendingChanges,
						/**
						 * Let css animations finish,
						 * to prevent block/glitch in the middle of the animation when the iframe will reload.
						 * Bigger than 300, which most of the css animations are.
						 */
						localized.change_timeout
					);
				}
			);

			initialized = true;
		};

	dmsEvents.one('dms:options:init', function(){
		setTimeout(
			init,
			40 // must be later than first 'dms:options:init' on body http://bit.ly/1F1dDUZ
		);
	});
});