var currentWindow = new Array();
var previousWindow = new Array();

function popupinfo_window (wHandle) {
	$$('.divwindowpopup').each( function (el) {
		var rel = $(el.getProperty('rel'));
        rel.injectInside(document.body);
		el.removeClass('divwindowpopup');
		el.addEvent('trash',function () {
		    var rel = $(el.getProperty('rel')); 
			rel.remove(); 
		});
		el.addEvent('sync', function (e) {
		    popup_window_sync (el, wHandle);
		});

		el.addEvent('click', function (e) {
			if (rel.getStyle('display') == 'none') {
				var zone = $('zone_'+el.getProperty('rel'));
				if (zone != null) {
					zone.fireEvent('display');
				} else {
					popup_window_sync (el, wHandle);
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
					'zIndex':'999'
				});
				rel.setStyle('display','');
				rel.fixdivShow();
								
			} else {
				rel.fixdivHide();
                rel.setStyle('display','none');
            } 
		});
	});
}



	    


function popup_window_sync (el, wHandle) {
    var rel = $(el.getProperty('rel'));
	rel.fixdivHide();
	rel.fixdivShow();
	previousWindow[rel.getProperty('rel')] = currentWindow[rel.getProperty('rel')]
	currentWindow[rel.getProperty('rel')] = rel;
	if (previousWindow[rel.getProperty('rel')] != null) {
		previousWindow[rel.getProperty('rel')].setStyle('zIndex','999');
	}
	window.fireEvent('windowChange'+rel.getProperty('rel'));
	currentWindow[rel.getProperty('rel')].setStyle('zIndex','1000');
	rel.addEvent('click', function () {
		if (currentWindow[rel.getProperty('rel')] != rel && rel.getStyle('display')!='none') {
			previousWindow[rel.getProperty('rel')] = currentWindow[rel.getProperty('rel')]
			currentWindow[rel.getProperty('rel')] = rel;
			if (previousWindow[rel.getProperty('rel')] != null) {
				previousWindow[rel.getProperty('rel')].setStyle('zIndex','999');
			}
			window.fireEvent('windowChange'+rel.getProperty('rel'));
				currentWindow[rel.getProperty('rel')].setStyle('zIndex','1000');
			}
		});
		rel.setStyle('display','none');
		rel.setStyle('display','');
		var closer = rel.getElement('.divcloser');
		if (closer == null) {
			closer = new Element('div');
			closer.addClass('divcloser');
			closer.injectInside(rel);
			closer.setHTML('X Fermer');
			closer.setOpacity(0.5);
			closer.setStyles({
				'position':'absolute',
				'background-color':'white',
				'border':'1px solid black',
				'margin':'0',
				'padding-left':'4px',
				'padding-right':'4px',
				'cursor':'pointer'
			});
			
			var temp = rel.getSize().size.x - 70;
			closer.setStyles({
				'top':0,
				'left':temp
			});
		}
		closer.addEvent('click', function () {
		rel.fixdivHide();
		rel.setStyle('display','none');
		closer.setStyle('display','none');
		previousWindow[rel.getProperty('rel')] = currentWindow[rel.getProperty('rel')];
		currentWindow[rel.getProperty('rel')] = null;
		window.fireEvent('windowChange'+rel.getProperty('rel'));
	});
	closer.setStyle('display','');
	
	tabHandle = {};
	if (wHandle != null) {
		tabHandle['handle'] = $(wHandle);
	}
	tempTab = $extend(tabHandle,{
		'onStart':function () {
			rel.setOpacity(0.5);
		},
		'onComplete': function () {
			rel.setOpacity(1);
		},
		'onDrag':function () {
			rel.fixdivUpdate();
		}
		
	})
	rel.makeDraggable(
		tempTab
	);
}