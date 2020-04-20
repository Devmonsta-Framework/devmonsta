(function ($, dmse) {
	var activeVisualMode = {},
		/**
		 * Quick Tags
		 * http://stackoverflow.com/a/21519323/1794248
		 */
		qTagsInit = function (id, $option, $wrap, $textarea, editor) {
			if (!tinyMCEPreInit.qtInit[ id ]) {
				return;
			}

			new QTags( tinyMCEPreInit.qtInit[ id ] );

			QTags._buttonsInit();

			if ($wrap.hasClass('html-active')) { // fixes glitch on init
				$wrap.find('.switch-html:first').trigger('click');
			}

			var visualMode = (typeof activeVisualMode[ id ] != 'undefined')
				? activeVisualMode[ id ]
				: (
					(typeof $option.attr('data-mode') != 'undefined')
					? ($option.attr('data-mode') == 'tinymce')
					: $wrap.hasClass('tmce-active')
				);

			$wrap.on('click', '.wp-switch-editor', function () {
				activeVisualMode[ id ] = $(this).hasClass('switch-tmce');
			});

			$wrap.find('.switch-'+ (visualMode ? 'tmce' : 'html') +':first').trigger('click');

			if (editor && !visualMode) {
				$textarea.val(wp.editor.removep(editor.getContent()));
			}
		};

	var init = function () {
		var $option = $(this),
			$textarea = $option.find('.wp-editor-area:first'),
			$wrap = $textarea.closest('.wp-editor-wrap'),
			id = $option.attr('data-dms-editor-id');

		/**
		 * Dynamically set tinyMCEPreInit.mceInit and tinyMCEPreInit.qtInit
		 * based on the data-dms-mce-settings and data-dms-qt-settings
		 */
		tinyMCEPreInit.mceInit[ id ] = JSON.parse($option.attr('data-dms-mce-settings'));
		tinyMCEPreInit.qtInit[ id ] = JSON.parse($option.attr('data-dms-qt-settings'));

		// Set width
		$option.closest('.dms-backend-option-input-type-wp-editor').addClass(
			'width-type-'+ ($option.attr('data-size') == 'large' ? 'full' : 'fixed')
		);

		// TinyMCE Editor http://stackoverflow.com/a/21519323/1794248
		if (tinyMCEPreInit.mceInit[ id ]) {
			if (typeof tinyMCEPreInit.mceInit[ id ] == 'undefined') {
				console.error('Can\'t find "'+ id +'" in tinyMCEPreInit.mceInit');
				return;
			}

			tinymce.execCommand('mceRemoveEditor', false, id);

			tinyMCEPreInit.mceInit[ id ].setup = function(ed) {
				var initialContent = $textarea.val(); // before \r\n were replaced

				ed.once('init', function (e) {
					var editor = e.target,
						id = editor.id;

					editor.on('change', function(){
						editor.save();
						$textarea.trigger('change'); 
					});

					// Fixes when wpautop is false
					if (!editor.getParam('wpautop')) {
						if (initialContent.indexOf('<p>') !== -1) {
							initialContent = wp.editor.removep(initialContent);
						}
						editor.setContent(initialContent.replace(/\r?\n/g, '<br />'));

						editor
							.on('SaveContent', function(event){
								// Remove <p> in Visual mode
								if (event.content && $wrap.hasClass('tmce-active')) {
									event.content = wp.editor.removep(event.content);
								}
							})
							.on('BeforeSetContent', function(event){
								// Prevent inline all content when switching from Text to Visual mode
								if (event.content && !$wrap.hasClass('tmce-active')) {
									event.content = wp.editor.autop(event.content);
								}
							});
					}

					qTagsInit(id, $option, $wrap, $textarea, editor);

					if (!editor.getParam('wpautop') && $wrap.hasClass('tmce-active')) {
						/**
						 * fixes: when initialContent is with <p>
						 *        if no changes are made in editor the <p> are not removed
						 */
						{
							$wrap.find('.switch-html:first').trigger('click');
							$wrap.find('.switch-tmce:first').trigger('click');
						}
					}

					initialContent = null; // free memory
				});
			};

			try {
				tinymce.init( tinyMCEPreInit.mceInit[ id ] );

				// Remove garbage. This caused lag on page scroll after OptionsModal with wp-editor close
				$option.on('remove', function(){ tinymce.execCommand('mceRemoveEditor', false, id); });
			} catch(e){
				console.error('wp-editor init error', id, e);
				return;
			}

			if (typeof window.wpLink != 'undefined') {
				try {
					
					window.wpLink.close();

					/**
					 * hide link edit toolbar on wp-editor destroy (on options modal close)
					 */
					$option.one('remove', function () {
						window.wpLink.close();
					});
				} catch (e) {
					$('#wp-link-wrap,#wp-link-backdrop').css('display', '');
				}
			}
		} else {
			qTagsInit(id, $option, $wrap, $textarea);
		}
	};

	dmse.on('dms:options:init', function (data) {
		data.$elements
			.find('.dms-option-type-wp-editor:not(.dms-option-initialized)')
			.each(init)
			.addClass('dms-option-initialized');
	});

	dms.options.register('wp-editor', {
		startListeningForChanges: function (optionDescriptor) {
			$(optionDescriptor.el).find('textarea.wp-editor-area')
				.on('change', function (e) {
					dms.options.trigger.changeForEl(e.target);
				});
		},

		getValue: function (optionDescriptor) {
			return {
				value: $(optionDescriptor.el).find(
					'textarea.wp-editor-area'
				).val(),

				optionDescriptor: optionDescriptor
			}
		}
	});

})(jQuery, dmsEvents);

/**
 * Find all wp-editor option types from container
 * and give them new IDs (random MD5).
 *
 * Copy their preinit data from currentId.
 *
 * The main callback we have below will take care about populating
 * tinyMCEPreInit.mceInit and tinyMCEPreInit.qtInit for them.
 */
function dmsWpEditorRefreshIds(currentId, container) {
	_.map(
		jQuery(container).find('.dms-option-type-wp-editor').toArray(),
		refreshEditor
	);

	function refreshEditor (editor) {
		var html = jQuery(editor).clone().wrap('<p>').parent().html();

		var regexp = new RegExp(currentId, 'g');
		html = html.replace(regexp, dms.randomMD5());

		jQuery(editor).replaceWith(html);
	}
}

