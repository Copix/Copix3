function selectoverload (name) {
	var div = $('div'+name);
	var input = $('caption'+name);
	var inputhidden = $(name);
	div.injectInside(document.body);
	
	/*
	$$('.'+name).each (function (el) {
		//td
		el.setStyle('cursor','pointer');
		el.addEvent('mouseenter', function () {
			el.setStyle('background-color','#AAAAAA');
		});
		el.addEvent('mouseleave', function () {
			el.setStyle('background-color','white');
		});
		el.addEvent('click',function () {
			$(inputhidden).value=el.getProperty('rel');
			$(input).value = el.innerHTML;
			input.fireEvent('mouseleavezone');
		});
	});
	*/
	$('clicker_'+name).addEvent('click', function () {
		if (div.getStyle('display') == 'none') {
			div.setStyles({
				'display':'',
				'height':'auto',
				'position':'absolute',
				'top':input.getTop ()+input.getSize().size.y,
				'left':input.getLeft (),
				'overflow':'auto'
			});
			scrollTop = (document.documentElement.scrollTop)?document.documentElement.scrollTop:document.body.scrollTop;
			//place en bas
			placeBas = window.getSize ().size.y - (input.getTop () - scrollTop);
			//place en haut
			placeHaut = input.getTop () - scrollTop;
			//si ya pas la place en bas
			if ( placeBas < div.getSize ().size.y){
				//on l'affiche en haut si ya la place
				positionTop = '';
				if ( placeHaut < div.getSize().size.y ){
					//si ya pas la place, on reduit la taille du div, on regarde alors où il y a le plus de place
					hauteurDiv = '';
					if (placeBas  > placeHaut){
						//on affiche en bas
						positionTop = input.getTop ()+input.getSize().size.y;
						hauteurDiv = placeBas - input.getSize().size.y;
					}else{
						//affichage en haut
						positionTop = input.getTop () - placeHaut;
						hauteurDiv = placeHaut;
					}
					//reduction de hauteur
					div.setStyle ('height', hauteurDiv);
				}else{				
					positionTop = input.getTop () - div.getSize ().size.y;
				}
				//place en haut
				div.setStyle ('top', positionTop);
			}
			div.fixdivShow();
			input.testZone ( div.getTop()-5, div.getLeft()-5, div.getSize().size.y+10, div.getSize().size.x+10 );
		} else {
			div.setStyles({
			 'display':'none'
			});
			div.fixdivHide();
		}
	});
	
	input.addEvent('mouseleavezone', function () {
		input.deleteZone ();
		div.fixdivHide();
		div.setStyles({
			'display':'none'
		});
		input.fireEvent ('change');
	});
}