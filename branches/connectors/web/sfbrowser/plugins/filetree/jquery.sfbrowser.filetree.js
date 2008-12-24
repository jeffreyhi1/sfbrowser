;(function($) {
	//
	var p;
	//
	$.fn.extend($.sfbrowser, {
		filetree: function(_p) {
			p = _p;
			p.trace("filetree");
		}
	});
	$.extend($.sfbrowser.filetree, {
		resizeWindow: function(iWdt,iHgt) {
			p.trace("filetree resizeWindow");
		}
	});
	function asdf(el) {
	}
})(jQuery);