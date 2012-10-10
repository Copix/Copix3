/**
* Simple Mootools plugin adding support for the placeholder attribute to browsers not supporting it natively
* Author: goulven CHAMPENOIS
* Usage: simply add a link to this file in the <head> of your documents. Inputs with placeholder text will be improved automatically
* May 7th 201: you can now style the "placeholder" class to the element in "placeholder" state. May change it when the official syntax is defined officially
*/

function placeholderText(e) {
	if (e) {
		if (typeof(e.target) === 'undefined') {
			return;
		}
		var el = e.target;
		if (e.type === 'focus' && el.value === el.get('placeholder')) {
			el.value = '';
			el.removeClass('placeholder');
			return;
		}
		if (e.type === 'blur' && el.value === '') {
			el.value = el.get('placeholder')
			el.addClass('placeholder');
			return;
		}
	} else { //setup
		// do nothing in case of native browser support
		if ('placeholder' in new Element('input')) {
			return;
		}
		var els = $$('input[placeholder]');
		els.each(function(el) {
			el.addEvent('focus', placeholderText);
			el.addEvent('blur', placeholderText);
			// Prefill with placeholder text if empty
			// The second test is to set the class on "soft" reloads
			if (!el.value || el.value == el.get('placeholder')) {
				el.value = el.get('placeholder');
				el.addClass('placeholder');
			}
		});
	}
}
window.addEvent('domready',placeholderText);