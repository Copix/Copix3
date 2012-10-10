CopixClass.implement({
	registerSelectcomplex: function(options) {
		
		var id = options['id'];
		var name = options['name'];
		var selected = options['selected'];
		
		$(id).selectcomplexSetValue = function (selected, noevent) {
			this.value = selected;
			var selectOld = $(id+'_valueselected');
			if (selectOld) {
				selectOld.destroy ();
			}
			var selection = $$('.selectcomplex_selectedView_value_'+id+'_'+selected);
			if (!selection.length) {
				selection = $$('.selectcomplex_option_value_'+id+'_'+selected);
			}
			
			selection.each (function (el) {
				
				var newElem = el.clone ();
				newElem.removeClass ('selectcomplex_option');
				newElem.removeClass ('selectcomplex_option_alternate');
				var input   = new Element (
					'input',
					{
						'name'  : name,
						'type'  : 'hidden',
						'value' : selected
					}
				);
				newElem.id=id+'_valueselected';
				input.inject (newElem);
				newElem.inject ($(id+'_value'));
			});
			
			if (!noevent) {
				$(id).fireEvent ('change');
			}
		};
		
		// Champs de selection
		value = $(id+'_values');
		value.selectcomplexOpen = false;
		value.selectcomplexClose = function () {
			this.selectcomplexOpen = false;
			this.setStyle ('visibility', 'hidden');
			$(id+'_value').removeClass ('selectcomplex_box_focus');
		};
		value.selectcomplexOpenClose = function (e) {
			var open = this.selectcomplexOpen;
			$(window.document).fireEvent ('click');
			if (this.selectcomplexOpen = !open) {
				var min = ($(id+'_values_list').getSize ().y  < $(id+'_values').getSize ().y) ? $(id+'_values_list').getSize ().y : $(id+'_values').getSize ().y;
				var top = $(id+'_value').getTop () + $(id+'_value').getSize ().y;
				var left = $(id+'_value').getLeft ();
				if ((top+min) > (window.getSize().y+window.getScroll ().y)) {
					top = $(id+'_value').getTop () - min;
					if (top < window.getScroll ().y) {
						top = $(id+'_value').getTop () + $(id+'_value').getSize ().y;
					}
				}
				
				this.setStyles ({
					'top': top+'px',
					'left': left+'px',
					'visibility': ''
				});
				$(id+'_value').addClass ('selectcomplex_box_focus');
			}
			e = new Event (e);
			e.stop ();
		};
		
		value.inject (document.body);
		
		// Clicker
		$(window.document).addEvent ('click', function () {
			$(id+'_values').selectcomplexClose ();
		});
		$(id+'_clicker').addEvent ('click', function (e) {
			$(id+'_values').selectcomplexOpenClose (e);
		});
		$(id+'_value').addEvent ('click', function (e) {
			$(id+'_values').selectcomplexOpenClose (e);
		});
		
		$(id+'_values_list').getElements ('div').each (function (el) {
			el.addEvent ('click', function (e) {
				var inputValue = null;
				el.getElements ('.selectcomplex_value_var').each (function (input) {
					inputValue = input.value;
				});
				
				$(id).selectcomplexSetValue (inputValue);
			});
		});
		
		
		$(id).selectcomplexSetValue (selected, true);
	}
});