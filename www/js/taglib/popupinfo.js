function popupinfo () {
	 $$('.divpopup').each( function (el) {
			el.removeClass('divpopup');
		    el.addEvent('sync', function (e) {
			    var rel = $(el.getProperty('rel'));
				rel.fixdivHide();
				rel.fixdivShow();
			});

			el.addEvent('trash',function () {
			    var rel = $(el.getProperty('rel'));
			    if (rel != null) {
					rel.remove();
				} 
			});

			 var rel = $(el.getProperty('rel'));
			 rel.injectInside(document.body);
			 el.addEvent('mouseenter', function (e) {
				 var zone = $('zone_'+el.getProperty('rel'));
				 if (zone != null) {
				     zone.fireEvent('display');
				 }
				 var e = new Event(e);
 				         rel.setStyle('display','');
			 });

			 el.addEvent('mousemove', function (e) {
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
				rel.fixdivShow();
			 });
			 el.addEvent('mouseleave', function (e) {
			     var e = new Event(e);
		         rel.setStyle('display','none');
				 rel.fixdivHide();
			 });							
	 });
}
 