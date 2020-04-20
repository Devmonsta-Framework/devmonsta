/**
 * Basic options registry
 */
dms.options = (function ($, currentDmsOptions) {
	/**
	 * An object of hints
	 */
	var allOptionTypes = {};

	currentDmsOptions.get = get;
	currentDmsOptions.getAll = getAll;
	currentDmsOptions.register = register;
	currentDmsOptions.__unsafePatch = __unsafePatch;
	currentDmsOptions.getOptionDescriptor = getOptionDescriptor;
	currentDmsOptions.startListeningToEvents = startListeningToEvents;
	currentDmsOptions.getContextOptions = getContextOptions;
	currentDmsOptions.findOptionInContextForPath = findOptionInContextForPath;
	currentDmsOptions.findOptionInSameContextFor = findOptionInSameContextFor;

	/**
	 * dms.options.getValueForEl(element)
	 *   .then(function (values, optionDescriptor) {
	 *     // current values for option type
	 *     console.log(values)
	 *   })
	 *   .fail(function () {
	 *     // value extraction failed for some reason
	 *   });
	 */
	currentDmsOptions.getValueForEl = getValueForEl;
	currentDmsOptions.getContextValue = getContextValue;

	return currentDmsOptions;

	/**
	 * get hint object for a specific type
	 */
	function get (type) {
		return allOptionTypes[type] || allOptionTypes['dms-undefined'];
	}

	function getAll () {
		return allOptionTypes;
	}

	/**
	 * Returns:
	 *   el
	 *   ID
	 *   type
	 *   isRootOption
	 *   context
	 *   nonOptionContext
	 */
	function getOptionDescriptor (el) {
		var data = {};

		if (! el) return null;

		data.context = detectDOMContext(el);

		data.el = findOptionDescriptorEl(el);

		data.rootContext = findNonOptionContext(data.el);
		data.id = $(data.el).attr('data-dms-option-id');
		data.type = $(data.el).attr('data-dms-option-type');
		data.isRootOption = isRootOption(data.el, findNonOptionContext(data.el));
		data.hasNestedOptions = hasNestedOptions(data.el);

		data.pathToTheTopContext = data.isRootOption
									? []
									: findPathToTheTopContext(data.el, findNonOptionContext(data.el));

		return data;
	}

	function findOptionInSameContextFor (el, path) {
		var rootContext = getOptionDescriptor(el).rootContext;

		return findOptionInContextForPath(
			rootContext, path
		);
	}

	/**
	 * This receives a context (option as context works too)
	 * and returns the option descriptor which respects the path
	 *
	 * - form
	 * - .dms-backend-options-virtual-context
	 * - .dms-backend-option-descriptor
	 *
	 * path:
	 *  id/other_id/another_one
	 */
	function findOptionInContextForPath (context, path) {
		var pathToTheTop = path.split('/');

		return pathToTheTop.reduce(function (currentContext, path, index) {
			if (! currentContext) return false;

			var elOrDescriptorForPath = _.compose(
				index === pathToTheTop.length - 1
					? getOptionDescriptor
					: _.identity,

				_.partial(
					maybeFindFirstLevelOptionInContext,
					currentContext
				)

			);

			return elOrDescriptorForPath(path);

		}, context);

		function maybeFindFirstLevelOptionInContext (context, firstLevelId) {
			return (getContextOptions(context).filter(
				function (optionDescriptor) {
					return optionDescriptor.id === firstLevelId;
				}
			)[0] || {}).el;
		}
	}

	/**
	 * This receives a context (option as context works too)
	 * and returns the first level of options underneath it.
	 *
	 * - form
	 * - .dms-backend-options-virtual-context
	 * - .dms-backend-option-descriptor
	 */
	function getContextOptions (el) {
		el = (el instanceof jQuery) ? el[0] : el;

		if (! (
			el.tagName === 'FORM'
			||
			el.classList.contains('dms-backend-options-virtual-context')
			||
			el.classList.contains('dms-backend-option-descriptor')
		)) {
			throw "You passed an incorrect context element."
		}

		return $(el)
			.find('.dms-backend-option-descriptor')
			.not(
				$(el).find('.dms-backend-options-virtual-context .dms-backend-option-descriptor')
			)
			.toArray()
			.map(getOptionDescriptor)
			.filter(function (descriptor) {
				return isRootOption(descriptor.el, el)
			})
	}

	function getContextValue (el) {
		var optionDescriptors = getContextOptions(el);

		var promise = $.Deferred();

		dms.whenAll(optionDescriptors.map(getValueForOptionDescriptor))
			.then(function (valuesAsArray) {
				var values = {};

				optionDescriptors.map(function (optionDescriptor, index) {
					values[optionDescriptor.id] = valuesAsArray[index].value;
				});

				promise.resolve({
					valueAsArray: valuesAsArray,
					optionDescriptors: optionDescriptors,
					value: values
				});
			})
			.fail(function () {
				// TODO: pass a reason
				promise.reject();
			});

		return promise;
	}

	function getValueForOptionDescriptor (optionDescriptor) {
    var maybePromise = get(optionDescriptor.type).getValue(optionDescriptor)

		var promise = maybePromise;

		/**
		 * A promise has a then method usually
		 */
		if (! promise.then) {
			promise = $.Deferred();
			promise.resolve(maybePromise);
		}

		return promise;
	}

	function getValueForEl (el) {
		return getValueForOptionDescriptor(getOptionDescriptor(el));
	}

	/**
	 * You are not registering here a full fledge class definition for an
	 * option type just like we have on backend. It is more of a hint on how
	 * to treat the option type on frontend. Everything should be working
	 * almost fine even if you don't provide any hints.
	 *
	 * interface:
	 *
	 *   startListeningForChanges
	 *   getValue
	 */
	function register (type, hintObject) {
		// TODO: maybe start triggering events on option type register

		if (allOptionTypes[type]) {
			throw "Can't re-register an option type again";
		}

		allOptionTypes[type] = jQuery.extend(
			{}, defaultHintObject(),
			hintObject || {}
		);
	}

	function __unsafePatch (type, hintObject) {
		allOptionTypes[type] = jQuery.extend(
			{}, defaultHintObject(),
			(allOptionTypes[type] || {}),
			hintObject || {}
		);
	}

	/**
	 * This will be automatically called at each dms:options:init event.
	 * This will make each option type start listening to events
	 */
	function startListeningToEvents (el) {
		// TODO: compute path up untill non-option context
		el = (el instanceof jQuery) ? el[0] : el;

		[].map.call(
			el.querySelectorAll('.dms-backend-option-descriptor[data-dms-option-type]'),
			function (el) {
				startListeningToEventsForSingle(getOptionDescriptor(el));
			}
		);
	}

  function startListeningToEventsForSingle (optionDescriptor) {
    get(optionDescriptor.type).startListeningForChanges(optionDescriptor)
  }

	/**
	 * We rely on the fact that by default, when we try to register some option
	 * type -- the undefined and default one will be already registered.
	 */
	function defaultHintObject () {
		return get('dms-undefined') || {};
	}

	function detectDOMContext (el) {
		el = findOptionDescriptorEl(el);

		var nonOptionContext = findNonOptionContext(el);

		return isRootOption(el, nonOptionContext)
			? nonOptionContext
			: findOptionDescriptorEl(el.parentElement);
	}

	function findOptionDescriptorEl (el) {
		el = (el instanceof jQuery) ? el[0] : el;

		if (! el) return false;

		if (el.classList.contains('dms-backend-option-descriptor')) {
			return el;
		} else {
			var closestOption = $(el).closest(
				'.dms-backend-option-descriptor'
			);

			if (closestOption.length === 0) {
				throw "There is no option descriptor for that element."
			}

			return closestOption[0];
		}
	}

	function isRootOption(el, nonOptionContext) {
		var parent;

		// traverse parents
		while (el) {
			parent = el.parentElement;

			if (parent === nonOptionContext) {
				return true;
			}

			if (parent && elementMatches(parent, '.dms-backend-option-descriptor')) {
				return false;
			}

			el = parent;
		}
	}

	function findPathToTheTopContext (el, nonOptionContext) {
		var parent;

		var result = [];

		// traverse parents
		while (el) {
			parent = el.parentElement;

			if (parent === nonOptionContext) {
				return result;
			}

			if (parent && elementMatches(parent, '.dms-backend-option-descriptor')) {
				// result.push(parent.getAttribute('data-dms-option-type'));
				result.push(parent);
			}

			el = parent;
		}

		return result.reverse();
	}

	/**
	 * A non-option context has two possible values:
	 *
	 * - a form tag which encloses a list of root options
	 * - a virtual context is an el with `.dms-backend-options-virtual-context`
	 */
	function findNonOptionContext (el) {
		var parent;

		// traverse parents
		while (el) {
			parent = el.parentElement;

			if (parent && elementMatches(parent, '.dms-backend-options-virtual-context, form')) {
				return parent;
			}

			el = parent;
		}

		return null;
	}

	function hasNestedOptions (el) {
		// exclude nested options within a virtual context

		var optionDescriptor = findOptionDescriptorEl(el);

		var hasVirtualContext = optionDescriptor.querySelector(
			'.dms-backend-options-virtual-context'
		);

		if (! hasVirtualContext) {
			return !! optionDescriptor.querySelector(
				'.dms-backend-option-descriptor'
			);
		}

		// check if we have options which are not in the virtual context
		return optionDescriptor.querySelectorAll(
			'.dms-backend-option-descriptor'
		).length > optionDescriptor.querySelectorAll(
			'.dms-backend-options-virtual-context .dms-backend-option-descriptor'
		).length;
	}

	function elementMatches (element, selector) {
		var matchesFn;

		// find vendor prefix
		[
			'matches','webkitMatchesSelector','mozMatchesSelector',
			'msMatchesSelector','oMatchesSelector'
		].some(function(fn) {
			if (typeof document.body[fn] === 'function') {
				matchesFn = fn;
				return true;
			}

			return false;
		})

		return element[matchesFn](selector);
	}
})(jQuery, (dms.options || {}));

