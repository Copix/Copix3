function popupinfo_clickdelay () {
	$$('.divclickdelaypopup').each( function (el) {
		var rel = $(el.getProperty('rel'));
		rel.removeClass('divclickdelaypopup');
		rel.addEvent ('mousemove', function () {
			save.flag = true;
		});

		rel.removeEvents('mouseleave');
					rel.addEvent ('mouseleave', function () {
			save.flag = false;
						save.hide.delay(1000);
				
					});



		el.removeEvents();
		el.addEvent('trash',function () {
		    var rel = $(el.getProperty('rel')); 
			rel.remove(); 
		});
		rel.injectInside(document.body);
		var save = {
			click : false,
			flag : false,
			hide : function () {
				if (!save.flag) {
					save.flag = false;
					save.click = false;
					rel.fixdivHide();
					rel.setStyle('display','none');
				}
			} 
		};

	    el.addEvent('sync', function (e) {
		    var rel = $(el.getProperty('rel'));
			rel.fixdivHide();
			rel.fixdivShow();
			rel.setStyle('display','none');
			rel.setStyle('display','');
		});

		el.addEvent('click', function (e) {
                           if (rel.getStyle('display') == 'none') {
                             rel.setStyle('display','');
			 	 var zone = $('zone_'+el.getProperty('rel'));
				 if (zone != null) {
				     zone.fireEvent('display');
				 }
					var e = new Event(e);
				
					var largeurElem = parseInt(rel.getSize().size.x);
					var hauteurElem = parseInt(rel.getSize().size.y);
					var correctionPlacementx = 0;
					var correctionPlacementy = 0;
				
					if( (temp = (window.getSize().size.y - (e.client.y + hauteurElem))) < 0 ){
						//20px c'est la taille du navigateur en bas (scroll + barre d'état)
						correctionPlacementy = temp-20;
					}								
					if( (temp = (window.getSize().size.x - (e.client.x + largeurElem))) < 0 ){
						correctionPlacementx = temp+temp*0.1;
					}

					rel.setStyles({
						'position':'absolute',
						'top' : (e.page.y+5+correctionPlacementy)+'px',
						'left' : (e.page.x+5+correctionPlacementx)+'px',
						'zIndex':'1001'
					});
					save.click = true;
			    	//rel.fixdivShow();
                        	} else {
					rel.fixdivHide();
					save.flag = false;
					save.click = false;
                            	rel.setStyle('display','none');
                        	}
			 	
		});

					el.addEvent('mouseleave', function () {
			save.flag = false;
						if (save.click) {
							save.hide.delay(1000);
						}
					});

	});
}
 