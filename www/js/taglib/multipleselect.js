function multipleselect () {
	$$('.multipleselect_id').each(function (elementDiv) {
		var id = elementDiv.getProperty('rel');
	 			var input = $('input_'+id);
	 			var divinput = $('div_'+id);
	 			var div   = $('divdata_'+id);
	 			div.injectInside(document.body);
		if (!window.ie) {
			divinput.setStyle('border-top','1px solid transparent');
		}
	 			divinput.addEvent('click', function () {
	 				if (div.getStyle('visibility') != 'visible') {
	     				div.setStyles({
	     					'visibility':'visible',
	     					'position':'absolute',
	     					'top':input.getTop ()+input.getSize().size.y,
	     					'left':input.getLeft (),
	     					'height':$('height_'+id).getProperty('rel'),
	     					'overflow':'auto'
	     				});
	 					if (div.getSize().size.x < divinput.getSize().size.x) {
	 						div.setStyle('width',divinput.getSize().size.x);
	 					}
	 					div.fixdivShow();
	     				input.testZone ( divinput.getTop()-5, divinput.getLeft()-5, divinput.getSize().size.y+div.getSize().size.y+10,div.getSize().size.x+10 );
	 				} else {
	 					div.setStyles({
	     			    	'visibility':'hidden'
	     				});
	 					div.fixdivHide();
	 				}
	 			});
	 			
	     
	     			input.addEvent('reset' , function () {
				$('hidden_'+id).setHTML ('');
				if (input.getProperty('noreset')!=1) {
	         				$$('.multipleselect_check_'+id).each ( function (el) {
	         					el.checked = false;
	         				});
				} else {
					var value = '';
	  					$('hidden_'+id).setHTML(''); 
	  					$$('.multipleselect_check_'+id).each ( function (elem) {
	  						if (elem.checked) {
	  							if (value!='') {
	  								value += ',';
	  							}
	  							value += $('label_'+elem.getProperty('id')).innerHTML;
	  							$('hidden_'+id).setHTML ($('hidden_'+id).innerHTML+'<input type=\"hidden\" name=\"'+$('name_'+id).getProperty('rel')+'[]\" value=\"'+elem.value+'\" />');
	  						}
	  					});
	  					input.value = value;
				}
				
	     			});
	
	 			input.addEvent('mouseleavezone', function () {
	 				div.fixdivHide();
	 				div.setStyles({
	     			    'visibility':'hidden'
	     			});
	 			});
	 			$$('.multipleselect_checker_'+id).each (function (el) {
	 				el.addEvent ('click', function () {
	 					var value = '';
	 					$('hidden_'+id).setHTML(''); 
	 					$$('.multipleselect_check_'+id).each ( function (elem) {
	 						if (elem.checked) {
	 							if (value!='') {
	 								value += ',';
	 							}
	 							value += $('label_'+elem.getProperty('id')).innerHTML;
	 							$('hidden_'+id).setHTML ($('hidden_'+id).innerHTML+'<input type=\"hidden\" name=\"'+$('name_'+id).getProperty('rel')+'[]\" value=\"'+elem.value+'\" />');
	 						}
	 					});
	 					input.value = value;
				input.fireEvent('change');
	 				});
	 			});
	});
}