window.addEvent ('domready', function () {
	$$('a input').each (function (elem) {
		elem.addEvent('click', function () {
			document.location.href = elem.parentNode.getProperty('href');
		});
	});
});