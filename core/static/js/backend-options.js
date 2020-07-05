/**
 * Included on pages where backend options are rendered
 */

var dmsBackendOptions = {
	
	openTab: function (tabId) { console.warn('deprecated'); }
};

jQuery(document).ready(function ($) {
	var localized = _dms_backend_options_localized;

	/**
	 * Functions
	 */
	{
		/**
		 * Make dms-postbox to close/open on click
		 *
		 * (fork from /wp-admin/js/postbox.js)
		 */
		function addPostboxToggles($boxes) {
			/** Remove events added by /wp-admin/js/postbox.js */
			$boxes.find('h2, h3, .handlediv').off('click.postboxes');

			var eventNamespace = '.dms-backend-postboxes';

			// make postboxes to close/open on click
			$boxes
				.off('click' + eventNamespace) // remove already attached, just to be sure, prevent multiple execution
				.on('click' + eventNamespace, '> .hndle, > .handlediv', function (e) {
					var $box = $(this).closest('.dms-postbox');

					if ($box.parent().is('.dms-backend-postboxes') && !$box.siblings().length) {
					
						$box.removeClass('closed');
					} else {
						$box.toggleClass('closed');
					}

					var isClosed = $box.hasClass('closed');

					$box.trigger('dms:box:' + (isClosed ? 'close' : 'open'));
					$box.trigger('dms:box:toggle-closed', { isClosed: isClosed });
				});
		}

		/** Remove box header if title is empty */
		function hideBoxEmptyTitles($boxes) {
			$boxes.find('> .hndle > span').each(function () {
				var $this = $(this);

				if (!$.trim($this.html()).length) {
					$this.closest('.postbox').addClass('dms-postbox-without-name');
				}
			});
		}
	}

	/** Init tabs */
	(function () {
		var htmlAttrName = 'data-dms-tab-html',
			initTab = function ($tab) {
				var html;

				if (html = $tab.attr(htmlAttrName)) {
					dmsEvents.trigger('dms:options:init', {
						$elements: $tab.removeAttr(htmlAttrName).html(html),
						/**
						 * Sometimes we want to perform some action just when
						 * lazy tabs are rendered. It's important in those cases
						 * to distinguish regular dms:options:init events from
						 * the ones that will render tabs. Passing by this little
						 * detail may break some widgets because dms:options:init
						 * event may be fired even when tabs are not yet rendered.
						 *
						 * That's how you can be sure that you'll run a piece
						 * of code just when tabs will be arround 100%.
						 *
						 * dmsEvents.on('dms:options:init', function (data) {
						 *   if (! data.lazyTabsUpdated) {
						 *     return;
						 *   }
						 *
						 *   // Do your business
						 * });
						 *
						 */
						lazyTabsUpdated: true
					});
				}
			},
			initAllTabs = function ($el) {
				var selector = '.dms-options-tab[' + htmlAttrName + ']', $tabs;

				
				$el.each(function () {
					if ($(this).is(selector)) {
						initTab($(this));
					}
				});

				// initialized tabs can contain tabs, so init recursive until nothing is found
				while (($tabs = $el.find(selector)).length) {
					$tabs.each(function () { initTab($(this)); });
				}
			};

		dmsEvents.on('dms:options:init:tabs', function (data) {
			initAllTabs(data.$elements);
		});

		dmsEvents.on('dms:options:init', function (data) {
			var $tabs = data.$elements.find('.dms-options-tabs-wrapper:not(.initialized)');

			if (localized.lazy_tabs) {
				$tabs.tabs({
					create: function (event, ui) {
						initTab(ui.panel);
					},
					activate: function (event, ui) {
						initTab(ui.newPanel);
						ui.newPanel.closest('.dms-options-tabs-contents')[0].scrollTop = 0
					}
				});

				$tabs
					.closest('form')
					.off('submit.dms-tabs')
					.on('submit.dms-tabs', function () {
						if (!$(this).hasClass('prevent-all-tabs-init')) {
							// All options needs to be present in html to be sent in POST on submit
							initAllTabs($(this));
						}
					});
			} else {
				$tabs.tabs({
					activate: function (event, ui) {
						ui.newPanel.closest('.dms-options-tabs-contents')[0].scrollTop = 0
					}
				});
			}

			$tabs.each(function () {
				var $this = $(this);

				if (!$this.parent().closest('.dms-options-tabs-wrapper').length) {
					// add special class to first level tabs
					$this.addClass('dms-options-tabs-first-level');
				}
			});

			$tabs.addClass('initialized');
		});
	})();

	/** Init boxes */
	dmsEvents.on('dms:options:init', function (data) {
		var $boxes = data.$elements.find('.dms-postbox:not(.initialized)');

		hideBoxEmptyTitles(
			$boxes.filter('.dms-backend-postboxes > .dms-postbox')
		);

		addPostboxToggles($boxes);

		/**
		 * leave open only first boxes
		 */
		$boxes
			.filter('.dms-backend-postboxes > .dms-postbox:not(.dms-postbox-without-name):not(:first-child):not(.prevent-auto-close)')
			.addClass('closed');

		$boxes.addClass('initialized');

		// trigger on box custom event for others to do something after box initialized
		$boxes.trigger('dms-options-box:initialized');
	});

	/** Init options */
	dmsEvents.on('dms:options:init', function (data) {
		data.$elements.find('.dms-backend-option:not(.initialized)')
			// do nothing, just a the initialized class to make the fadeIn css animation effect
			.addClass('initialized');
	});

	/** Fixes */
	dmsEvents.on('dms:options:init', function (data) {
		{
			var eventNamespace = '.dms-backend-postboxes';

			data.$elements.find('.postbox:not(.dms-postbox) .dms-option')
				.closest('.postbox:not(.dms-postbox)')

				/**
				 * Add special class to first level postboxes that contains framework options (on post edit page)
				 */
				.addClass('postbox-with-dms-options')

				/**
				 * Prevent event to be propagated to first level WordPress sortable (on edit post page)
				 * If not prevented, boxes within options can be dragged out of parent box to first level boxes
				 */
				.off('mousedown' + eventNamespace) // remove already attached (happens when this script is executed multiple times on the same elements)
				.on('mousedown' + eventNamespace, '.dms-postbox > .hndle, .dms-postbox > .handlediv', function (e) {
					e.stopPropagation();
				});
		}

		/**
		 * disable sortable (drag/drop) for postboxes created by framework options
		 * (have no sense, the order is not saved like for first level boxes on edit post page)
		 */
		{
			var $sortables = data.$elements
				.find('.postbox:not(.dms-postbox) .dms-postbox, .dms-options-tabs-wrapper .dms-postbox')
				.closest('.dms-backend-postboxes')
				.not('.dms-sortable-disabled');

			$sortables.each(function () {
				try {
					$(this).sortable('destroy');
				} catch (e) {
					// happens when not initialized
				}
			});

			$sortables.addClass('dms-sortable-disabled');
		}

		/** hide bottom border from last option inside box */
		{
			data.$elements.find('.postbox-with-dms-options > .inside, .dms-postbox > .inside')
				.append('<div class="dms-backend-options-last-border-hider"></div>');
		}

		hideBoxEmptyTitles(
			data.$elements.find('.postbox-with-dms-options')
		);
	});

	/**
	 * Help tips (i)
	 */
	(function () {
		dmsEvents.on('dms:options:init', function (data) {
			var $helps = data.$elements.find('.dms-option-help:not(.initialized)');

			dms.qtip($helps);

			$helps.addClass('initialized');
		});
	})();

	$('#side-sortables').addClass('dms-force-xs');
});
