function popupinfo_click() {
	$$('.divclickpopup').each( function (el) {
        var rel = $(el.getProperty('rel'));
        rel.injectInside(document.body);
		el.removeClass('divclickpopup');
		el.addEvent('trash',function () {
		    var rel = $(el.getProperty('rel')); 
			rel.remove(); 
		});
	    el.addEvent('sync', function (e) {
		    var rel = $(el.getProperty('rel'));
			rel.fixdivHide();
			rel.fixdivShow();
			rel.setStyle('display','none');
			rel.setStyle('display','');
		});

        el.addEvent('click', function (e) {
           if (rel.getStyle('display') == 'none') {
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
				rel.setStyle('display','');
				rel.fixdivShow();
                        } else {
                            rel.setStyle('display','none');
				rel.fixdivHide();
                        }  
		});
	});

}
 