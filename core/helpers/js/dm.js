var dm;

if (typeof Object['create'] != 'undefined') {
	/**
	 * create clean object
	 * autocomplete in console to show only defined methods, without other unnecesary methods from Object prototype
	 */
	dm = Object.create(null);
} else {
	dm = {};
}

/**
 * URI to framework directory
 */
dm.DEVM_URI = _devm_localized.DEVM_URI;
dm.SITE_URI = _devm_localized.SITE_URI;
/**
 * Load ajax.php
 */
dm.AJAX_URL = _devm_localized.AJAX_URL;

/**
 * parseInt() alternative
 * Like intval() in php. Returns 0 on failure, not NaN
 * @param val
 */
dm.intval = function (val) {
	val = parseInt(val);
	return !isNaN(val) ? val : 0;
};

/**
 * Calculate md5 hash of the string
 * @param {String} string
 */
dm.md5 = (function () {
	"use strict";

	/*
	 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
	 * Digest Algorithm, as defined in RFC 1321.
	 * Copyright (C) Paul Johnston 1999 - 2000.
	 * Updated by Greg Holt 2000 - 2001.
	 * See http://pajhome.org.uk/site/legal.html for details.
	 */

	/*
	 * Convert a 32-bit number to a hex string with ls-byte first
	 */
	var hex_chr = "0123456789abcdef";

	function rhex(num) {
		var str = "",
			j;
		for (j = 0; j <= 3; j++)
			str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
			hex_chr.charAt((num >> (j * 8)) & 0x0F);
		return str;
	}

	/*
	 * Convert a string to a sequence of 16-word blocks, stored as an array.
	 * Append padding bits and the length, as described in the MD5 standard.
	 */
	function str2blks_MD5(str) {
		var nblk = ((str.length + 8) >> 6) + 1,
			blks = new Array(nblk * 16),
			i;
		for (i = 0; i < nblk * 16; i++) blks[i] = 0;
		for (i = 0; i < str.length; i++)
			blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
		blks[i >> 2] |= 0x80 << ((i % 4) * 8);
		blks[nblk * 16 - 2] = str.length * 8;
		return blks;
	}

	/*
	 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
	 * to work around bugs in some JS interpreters.
	 */
	function add(x, y) {
		var lsw = (x & 0xFFFF) + (y & 0xFFFF);
		var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
		return (msw << 16) | (lsw & 0xFFFF);
	}

	/*
	 * Bitwise rotate a 32-bit number to the left
	 */
	function rol(num, cnt) {
		return (num << cnt) | (num >>> (32 - cnt));
	}

	/*
	 * These functions implement the basic operation for each round of the
	 * algorithm.
	 */
	function cmn(q, a, b, x, s, t) {
		return add(rol(add(add(a, q), add(x, t)), s), b);
	}

	function ff(a, b, c, d, x, s, t) {
		return cmn((b & c) | ((~b) & d), a, b, x, s, t);
	}

	function gg(a, b, c, d, x, s, t) {
		return cmn((b & d) | (c & (~d)), a, b, x, s, t);
	}

	function hh(a, b, c, d, x, s, t) {
		return cmn(b ^ c ^ d, a, b, x, s, t);
	}

	function ii(a, b, c, d, x, s, t) {
		return cmn(c ^ (b | (~d)), a, b, x, s, t);
	}

	/*
	 * Take a string and return the hex representation of its MD5.
	 */
	function calcMD5(str) {
		var x = str2blks_MD5(str);
		var a = 1732584193;
		var b = -271733879;
		var c = -1732584194;
		var d = 271733878;

		var i, olda, oldb, oldc, oldd;

		for (i = 0; i < x.length; i += 16) {
			olda = a;
			oldb = b;
			oldc = c;
			oldd = d;

			a = ff(a, b, c, d, x[i + 0], 7, -680876936);
			d = ff(d, a, b, c, x[i + 1], 12, -389564586);
			c = ff(c, d, a, b, x[i + 2], 17, 606105819);
			b = ff(b, c, d, a, x[i + 3], 22, -1044525330);
			a = ff(a, b, c, d, x[i + 4], 7, -176418897);
			d = ff(d, a, b, c, x[i + 5], 12, 1200080426);
			c = ff(c, d, a, b, x[i + 6], 17, -1473231341);
			b = ff(b, c, d, a, x[i + 7], 22, -45705983);
			a = ff(a, b, c, d, x[i + 8], 7, 1770035416);
			d = ff(d, a, b, c, x[i + 9], 12, -1958414417);
			c = ff(c, d, a, b, x[i + 10], 17, -42063);
			b = ff(b, c, d, a, x[i + 11], 22, -1990404162);
			a = ff(a, b, c, d, x[i + 12], 7, 1804603682);
			d = ff(d, a, b, c, x[i + 13], 12, -40341101);
			c = ff(c, d, a, b, x[i + 14], 17, -1502002290);
			b = ff(b, c, d, a, x[i + 15], 22, 1236535329);

			a = gg(a, b, c, d, x[i + 1], 5, -165796510);
			d = gg(d, a, b, c, x[i + 6], 9, -1069501632);
			c = gg(c, d, a, b, x[i + 11], 14, 643717713);
			b = gg(b, c, d, a, x[i + 0], 20, -373897302);
			a = gg(a, b, c, d, x[i + 5], 5, -701558691);
			d = gg(d, a, b, c, x[i + 10], 9, 38016083);
			c = gg(c, d, a, b, x[i + 15], 14, -660478335);
			b = gg(b, c, d, a, x[i + 4], 20, -405537848);
			a = gg(a, b, c, d, x[i + 9], 5, 568446438);
			d = gg(d, a, b, c, x[i + 14], 9, -1019803690);
			c = gg(c, d, a, b, x[i + 3], 14, -187363961);
			b = gg(b, c, d, a, x[i + 8], 20, 1163531501);
			a = gg(a, b, c, d, x[i + 13], 5, -1444681467);
			d = gg(d, a, b, c, x[i + 2], 9, -51403784);
			c = gg(c, d, a, b, x[i + 7], 14, 1735328473);
			b = gg(b, c, d, a, x[i + 12], 20, -1926607734);

			a = hh(a, b, c, d, x[i + 5], 4, -378558);
			d = hh(d, a, b, c, x[i + 8], 11, -2022574463);
			c = hh(c, d, a, b, x[i + 11], 16, 1839030562);
			b = hh(b, c, d, a, x[i + 14], 23, -35309556);
			a = hh(a, b, c, d, x[i + 1], 4, -1530992060);
			d = hh(d, a, b, c, x[i + 4], 11, 1272893353);
			c = hh(c, d, a, b, x[i + 7], 16, -155497632);
			b = hh(b, c, d, a, x[i + 10], 23, -1094730640);
			a = hh(a, b, c, d, x[i + 13], 4, 681279174);
			d = hh(d, a, b, c, x[i + 0], 11, -358537222);
			c = hh(c, d, a, b, x[i + 3], 16, -722521979);
			b = hh(b, c, d, a, x[i + 6], 23, 76029189);
			a = hh(a, b, c, d, x[i + 9], 4, -640364487);
			d = hh(d, a, b, c, x[i + 12], 11, -421815835);
			c = hh(c, d, a, b, x[i + 15], 16, 530742520);
			b = hh(b, c, d, a, x[i + 2], 23, -995338651);

			a = ii(a, b, c, d, x[i + 0], 6, -198630844);
			d = ii(d, a, b, c, x[i + 7], 10, 1126891415);
			c = ii(c, d, a, b, x[i + 14], 15, -1416354905);
			b = ii(b, c, d, a, x[i + 5], 21, -57434055);
			a = ii(a, b, c, d, x[i + 12], 6, 1700485571);
			d = ii(d, a, b, c, x[i + 3], 10, -1894986606);
			c = ii(c, d, a, b, x[i + 10], 15, -1051523);
			b = ii(b, c, d, a, x[i + 1], 21, -2054922799);
			a = ii(a, b, c, d, x[i + 8], 6, 1873313359);
			d = ii(d, a, b, c, x[i + 15], 10, -30611744);
			c = ii(c, d, a, b, x[i + 6], 15, -1560198380);
			b = ii(b, c, d, a, x[i + 13], 21, 1309151649);
			a = ii(a, b, c, d, x[i + 4], 6, -145523070);
			d = ii(d, a, b, c, x[i + 11], 10, -1120210379);
			c = ii(c, d, a, b, x[i + 2], 15, 718787259);
			b = ii(b, c, d, a, x[i + 9], 21, -343485551);

			a = add(a, olda);
			b = add(b, oldb);
			c = add(c, oldc);
			d = add(d, oldd);
		}
		return rhex(a) + rhex(b) + rhex(c) + rhex(d);
	}

	return calcMD5;
})();

/**
 * dm.loading
 * Show/Hide loading on the page
 *
 * Usage:
 * - dm.loading.show('unique-id');
 * - dm.loading.hide('unique-id');
 */
(function ($) {
	var inst = {
		$el: null,
		queue: [],
		current: null,
		timeoutId: 0,
		pendingHide: false,
		$getEl: function () {
			if (this.$el === null) { // lazy init
				this.$el = $(
					'<div id="devm-loading" style="display: none;">' +
					'<table width="100%" height="100%"><tbody><tr><td valign="middle" align="center">' +
					'<img src="' + dm.img.logoSvg + '"' +
					' width="30"' +
					' class="devm-animation-rotate-reverse-180"' +
					' alt="Loading"' +
					' onerror="this.onerror=null; this.src=\'' + dm.devm_URI + '/static/img/logo-100.png\';"' +
					' />' +
					'</td></tr></tbody></table>' +
					'</div>'
				);

				$(document.body).prepend(this.$el);
			}

			return this.$el;
		},
		removeFromQueue: function (id) {
			this.queue = _.filter(this.queue, function (item) {
				return item.id != id;
			});
		},
		show: function (id, opts) {
			if (typeof id != 'undefined') {
				this.removeFromQueue(id);

				{
					opts = jQuery.extend({
						autoClose: 30000
					}, opts || {});

					opts.id = id;

					/**
					 * @type {string} pending|opening|open|closing
					 */
					opts.state = 'pending';
				}

				this.queue.push(opts);

				return this.show();
			}

			if (this.current) {
				return false;
			}

			if (this.current && this.current.customClass !== null) {
				this.$modal.removeClass(this.current.customClass);
			}

			if (this.$modal && !this.current.wrapWithTable) {
				this.wrapWithTable(this.$modal);
			}

			this.current = this.queue.shift();

			if (!this.current) {
				return false;
			}

			this.current.state = 'opening';

			{
				clearTimeout(this.timeoutId);

				this.timeoutId = setTimeout(_.bind(function () {
					if (
						!this.current ||
						this.current.state != 'opening'
					) {
						return;
					}

					this.current.state = 'open';

					this.$getEl().removeClass('opening closing closed').addClass('open');

					if (this.current.autoClose) {
						clearTimeout(this.timeoutId);

						this.timeoutId = setTimeout(_.bind(function () {
							this.hide();
						}, this), this.current.autoClose);
					}

					if (this.pendingHide) {
						this.pendingHide = false;
						this.hide();
					}
				}, this), 300);
			}

			this.$getEl().removeClass('open closing closed').addClass('opening').show();

			return true;
		},
		hide: function (id) {
			if (typeof id != 'undefined') {
				if (
					this.current &&
					this.current.id == id
				) {
					// the script below will handle this
				} else {
					this.removeFromQueue(id);
					return true;
				}
			}

			if (!this.current) {
				return false;
			}

			var forceClose = false;

			if (this.current.state == 'opening') {
				if (this.current.id == id) {
					/**
					 * If the currently opening loading was requested to hide
					 * hide it immediately, do not wait full open.
					 * Maybe the script that started the loading was executed so quickly
					 * so the user don't event need to see the loading.
					 */
					// do nothing here, just allow the close script below to be executed
					forceClose = true;
				} else {
					this.pendingHide = true;
					return true;
				}
			} else {
				if (this.current.state != 'open') {
					return false;
				}
			}

			this.current.state = 'closing';

			{
				clearTimeout(this.timeoutId);

				this.timeoutId = setTimeout(_.bind(function () {
					if (
						!this.current ||
						this.current.state != 'closing'
					) {
						return;
					}

					this.current.state = 'closed';

					this.$getEl().hide().removeClass('opening open closing').addClass('closed');

					if (this.$modal && this.current.customClass !== null) {
						this.$modal.removeClass(this.current.customClass);
					}

					if (this.$modal && !this.current.wrapWithTable) {
						this.wrapWithTable(this.$modal);
					}

					this.current = null;

					this.show();
				}, this), 300);
			}

			if (forceClose) {
				this.$getEl().fadeOut('fast', _.bind(function () {
					this.$getEl().removeClass('force-closing').addClass('closed').removeAttr('style');
				}, this));

				this.$getEl().addClass('force-closing');
			}

			this.$getEl().removeClass('closed').addClass('closing');
		}
	};

	dm.loading = {
		show: function (id, opts) {
			/**
			 * Compatibility with old version of dm.loading.show()
			 * which didn't had the (id,opts) parameters
			 */
			if (typeof id == 'undefined') {
				id = 'main';
			}

			return inst.show(id, opts);
		},
		hide: function (id) {
			/**
			 * Compatibility with old version of dm.loading.hide()
			 * which didn't had the (id) parameter
			 */
			if (typeof id == 'undefined') {
				id = 'main';
			}

			return inst.hide(id);
		}
	};
})(jQuery);