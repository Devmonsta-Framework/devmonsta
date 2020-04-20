jQuery(function($){
	var optionTypeClass = '.dms-container-type-popup',
		optionsModals = {},
		getOptionsModal = function(id, data) {
			if (typeof optionsModals[id] == 'undefined') {
				var $option = data.$option,
					$options = data.$options,
					modal = optionsModals[id] = new dms.Modal({
						title: $option.attr('data-modal-title'),
						size: $option.attr('data-modal-size')
					}),
					onOpen = function(){
						$options.detach();

						modal.content.$el.html('').append($options);

						$options.removeClass('dms-hidden');
					},
					onClose = function(){
						$options.detach();

						$options.addClass('dms-hidden');

						$option.append($options);
					};

				modal.frame.on('open', onOpen);
				modal.frame.on('close', onClose);
			}

			return optionsModals[id];
		};

	dmsEvents.on('dms:options:init', function(data){
		data.$elements.find(optionTypeClass +':not(.initialized)').each(function(){
			var $option = $(this),
				$button = $option.find('> .popup-button-wrapper > .popup-button'),
				$options = $option.find('> .popup-options');

			$button.on('click', function(){
				getOptionsModal($option.attr('id'), {
					$option: $option,
					$options: $options
				}).open();
			});
		}).addClass('initialized');
	});
});