var previous_copixcheckbox = null;
var previous_copixcheckbox_action = null;

function copixcheckboxes (className) {
	$$('.'+className).each (function (el) {
		el.addEvent ('click', function (e) {
			var e = new Event(e);
			if (e.shift) {
			    el.fireEvent ('majclick');
			} else {
				previous_copixcheckbox = el;
				previous_copixcheckbox_action = el.getProperty('checked');
			}
		});
		
		el.addEvent('majclick', function () {
			if (previous_copixcheckbox) {
				var areIn = false;
				$$('.'+className).each (function (elem) {
					if (el == elem || elem == previous_copixcheckbox) {
						areIn = !areIn;
						elem.setProperty ('checked', previous_copixcheckbox_action);
					}
					if (areIn) {
						elem.setProperty ('checked', previous_copixcheckbox_action);
					}
				});
			}
		});
	});
}