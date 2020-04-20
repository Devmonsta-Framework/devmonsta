(function($) {
	var $rootClass = '.dms-option-type-icon-v2';

	/**
	 * We'll have this HTML structure
	 *
	 * <div class="dms-icon-v2-preview-wrapper>
	 *   <div class="dms-icon-v2-preview">
	 *     <i></i>
	 *     <button class="dms-icon-v2-remove-icon"></button>
	 *   </div>
	 *
	 *   <button class="dms-icon-v2-trigger-modal">Add Icon</div>
	 * </div>
	 */

	dmsEvents.on('dms:options:init', function(data) {
		data.$elements.find($rootClass).toArray().map(renderSinglePreview);
	});

	$(document).on('click', $rootClass + ' .dms-icon-v2-remove-icon', removeIcon);
	$(document).on('click', $rootClass + ' .dms-icon-v2-trigger-modal', getNewIcon);
	$(document).on('click', $rootClass + ' .dms-icon-v2-preview', getNewIcon);

	/**
	 * For debugging purposes
	 */
	function refreshEachIcon() {
		$($rootClass).toArray().map(refreshSinglePreview);
	}

	function getNewIcon(event) {
		event.preventDefault();

		var $root = $(this).closest($rootClass);
		var modalSize = $root.attr('data-dms-modal-size');

		/**
		 * dms.OptionsModal should execute it's change:values callbacks
		 * only if the picker was changed. That's why we introduce unique-id
		 * for each picker.
		 */
		if (!$root.data('unique-id')) {
			$root.data('unique-id', dms.randomMD5());
		}

		dmsOptionTypeIconV2Instance.set('size', modalSize);

		dmsOptionTypeIconV2Instance
			.open(getDataForRoot($root))
			.then(function(data) {
				setDataForRoot($root, data);
			})
			.fail(function() {
				// modal closed without save
			});
	}

	function removeIcon(event) {
		event.preventDefault();
		event.stopPropagation();

		setDataForRoot($(this).closest($rootClass), {
			type: 'none',
			'icon-class': '',
			'url': '',
			'attachment-id': ''
		});
	}

	function renderSinglePreview($root) {
		$root = $($root);

		/**
		* Skip element if it's already activated
		*/
		if ($root.hasClass('dms-activated')) {
			return;
		}

		$root.addClass('dms-activated');

		var $wrapper = $('<div>', {
			class: 'dms-icon-v2-preview-wrapper',
			'data-icon-type': getDataForRoot($root)['type'],
		});

		var $preview = $('<div>', {
			class: 'dms-icon-v2-preview',
		})
			.append($('<i>'))
			.append(
				$('<a>', {
					class: 'dms-icon-v2-remove-icon dashicons dms-x',
					html: '',
				})
			);

		$wrapper.append($preview).append(
			$('<button>', {
				class: 'dms-icon-v2-trigger-modal button-secondary button-large',
				type: 'button',
				html: dms_icon_v2_data.add_icon_label,
			})
		);

		$wrapper.appendTo($root);

		if (getDataForRoot($root)['type'] === 'custom-upload') {
			var media = wp.media.attachment(
				getDataForRoot($root)['attachment-id']
			);

			if (! media.get('url')) {
				media.fetch().then(function () {
					refreshSinglePreview($root);
				});
			}
		}

		refreshSinglePreview($root);
	}

	function refreshSinglePreview($root) {
		$root = $($root);

		var data = getDataForRoot($root);

		$root
			.find('.dms-icon-v2-trigger-modal')
			.text(
				dms_icon_v2_data[
					hasIcon(data) ? 'edit_icon_label' : 'add_icon_label'
				]
			);

		$root
			.find('.dms-icon-v2-preview-wrapper')
			.removeClass('dms-has-icon')
			.addClass(hasIcon(data) ? 'dms-has-icon' : '');

		$root
			.find('.dms-icon-v2-preview-wrapper')
			.attr('data-icon-type', data['type']);

		$root.find('i').attr('class', '');
		$root.find('i').attr('style', '');

		if (data.type === 'icon-font') {
			$root.find('i').attr('class', data['icon-class']);
		}

		if (data.type === 'custom-upload') {
			if (hasIcon(data)) {
				$root
					.find('i')
					.attr(
						'style',
						'background-image: url("' +
						// Insert the smallest possible image in the preview
						(_.min(
							_.values(wp.media.attachment(
								data['attachment-id']
							).get('sizes')),
							function (size) {return size.width}
						).url || wp.media.attachment(data['attachment-id']).get('url')) +
						'");'
					);
			}
		}

		function hasIcon(data) {
			return data.type !== 'none';
		}
	}

	function getDataForRoot($root) {
		return JSON.parse($root.find('input').val());
	}

	function setDataForRoot($root, data) {
		var currentData = getDataForRoot($root);

		var actualValue = _.omit(_.extend({}, currentData, data), 'attachment');

		if (actualValue.type === 'icon-font') {
			if ((actualValue['icon-class'] || "").trim() === '') {
				actualValue.type = 'none';
			}
		}

		if (actualValue.type === 'custom-upload') {
			if (! actualValue['attachment-id']) {
				actualValue.type = 'none';
			}
		}

		$root.find('input').val(JSON.stringify(actualValue)).trigger('change');

		dms.options.trigger.changeForEl($root, {
			value: actualValue,
		});

		refreshSinglePreview($root);
	}

	dms.options.register('icon-v2', {
		startListeningForChanges: $.noop,
		getValue: function(optionDescriptor) {
			return {
				value: JSON.parse($(optionDescriptor.el).find('input').val()),

				optionDescriptor: optionDescriptor,
			};
		},
	});
})(jQuery);
