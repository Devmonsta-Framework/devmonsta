jQuery(document).ready(function ($) {
	var optionClass = '.dms-option-type-addable-option';

	function initSortable ($options) {
		try {
			$options.sortable('destroy');
		} catch (e) {
			// happens when sortable was not initialized before
		}

		if (! $options.first().closest(optionClass).hasClass('is-sortable')) {
			return false;
		}

		var isMobile = $(document.body).hasClass('mobile');

		$options.sortable({
			items: '> tbody > tr',
			handle: 'td:first',
			cursor: 'move',
			placeholder: 'sortable-placeholder',
			delay: ( isMobile ? 200 : 0 ),
			distance: 2,
			tolerance: 'pointer',
			forcePlaceholderSize: true,
			axis: 'y',
			start: function(e, ui){
				// Update the height of the placeholder to match the moving item.
				{
					var height = ui.item.outerHeight();

					ui.placeholder.height(height);
				}
			},
			update: function(){
				$(this).closest(optionClass).trigger('change'); // for customizer
				dms.options.trigger.changeForEl($(this).closest(optionClass));
			}
		});
	}

	var methods = {
		/** Make full/prefixed event name from short name */
		makeEventName: function (shortName) {
			return 'dms:option-type:addable-option:' + shortName;
		}
	};

	dmsEvents.on('dms:options:init', function (data) {
		var $elements = data.$elements.find(optionClass +':not(.dms-option-initialized)');

		$elements.toArray().map(function (el) {
			// Trigger change when one of the underlying contexts change
			dms.options.on.change(function (data) {
				if (! $(data.context).is(
					'[data-dms-option-type="addable-option"] tr.dms-option-type-addable-option-option'
				)) {
					return;
				}

				// Listen to just its own virtual contexts
				if (! el.contains(data.context)) {
					return;
				}

				dms.options.trigger.changeForEl(el);
			});
		});

		/** Init Add button */
		$elements.on('click', optionClass +'-add', function(){
			var $button   = $(this);
			var $option   = $button.closest(optionClass);
			var $options  = $option.find(optionClass +'-options:first');
			var increment = parseInt($button.attr('data-increment'));

			var $newOption = $(
				$option.find('.default-addable-option-template:first').attr('data-template')
					.split( $button.attr('data-increment-placeholder') ).join( String(increment) )
			);

			// animation
			{
				$newOption.addClass('dms-animation-zoom-in');

				setTimeout(function(){
					$newOption.removeClass('dms-animation-zoom-in');
				}, 300);
			}

			$button.attr('data-increment', increment + 1);

			$options.append($newOption);

			// Re-render wp-editor
			if (
				window.dmsWpEditorRefreshIds
				&&
				$newOption.find('.dms-option-type-wp-editor:first').length
			) {
				dmsWpEditorRefreshIds(
					$newOption.find('.dms-option-type-wp-editor textarea:first').attr('id'),
					$newOption
				);
			}

			// remove focus form "Add" button to prevent pressing space/enter to add easy many options
			$newOption.find('input,select,textarea').first().focus();

			dmsEvents.trigger('dms:options:init', {$elements: $newOption});

			$option.trigger(methods.makeEventName('option:init'), {$option: $newOption});
			dms.options.trigger.changeForEl($option);
		});

		/** Init Remove button */
		$elements.on('click', optionClass +'-remove', function(){
			dms.options.trigger.changeForEl($(this).closest(
				'[data-dms-option-type="addable-option"]'
			));

			$(this).closest(optionClass +'-option').remove();
		});

		$elements.each(function(){
			initSortable($elements.find(optionClass +'-options:first'));
		});

		$elements.addClass('dms-option-initialized');
	});

	dms.options.register('addable-option', {
		startListeningForChanges: $.noop,
		getValue: function (optionDescriptor) {
			var promise = $.Deferred();

			dms.whenAll(
				$(optionDescriptor.el).find(
					'table.dms-option-type-addable-option-options'
				).first().find(
					'> tbody > .dms-backend-options-virtual-context'
				).toArray().map(dms.options.getContextValue)
			).then(function (valuesAsArray) {
				promise.resolve({
					value: valuesAsArray.map(function (singleContextValue) {
						return _.values(singleContextValue.value)[0];
					}),

					optionDescriptor: optionDescriptor
				})
			});

			return promise;
		}
	})
});
