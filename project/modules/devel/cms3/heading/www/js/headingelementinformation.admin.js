//fonctions de sauvegarde du formulaire
function saveHeadingElementInformationForm (){
	var formSubmit = new Request.HTML({
		url: Copix.getActionURL('heading|elementinformation|save'),
		onComplete: verifRetour,
		update:'retour'
		}).post($('headingElementInformationForm')	
	);		
}

function verifRetour (responseTree, responseElements, responseHTML, responseJavaScript) {
	var CopixObserver = Copix.get_observer ('headingElementInformationForm');
	CopixObserver.saveState ();
}

//DROITS - heritage ou pas
function rightsInherited () {
	$$('.rightSelect').each(function(el){
		el.disabled = $('rights_inherited').checked;
	});
}

var refreshMenus = function (){
	$$('.headingmenu').each(function (menu){
		var caption = menu.get('caption') == null ? "Menu" : menu.get('caption');
		if($('divMenuPosition' + caption.replace(/ /g, ""))){
			var div  = $('divMenuPosition' + caption.replace(/ /g, ""));
			var coordonnee = menu.getCoordinates();
			coordonnee.width = coordonnee.width == 0 ? 108 : coordonnee.width;
			coordonnee.height = coordonnee.height == 0 ? 20 : coordonnee.height;
			div.setStyles({'overflow':'hidden', 'padding':'2px','left':coordonnee.left,'top':coordonnee.top, 'width':coordonnee.width-8, 'height':coordonnee.height-8, 'text-align':'center', 'line-height':coordonnee.height-8});
		}
	});
}

function showMenus (){
	$$('.headingmenu').each(function (menu){
		var caption = menu.get('caption') == null ? "Menu" : menu.get('caption');
		var div  = new Element('div', {'class': 'divMenuPosition', id :'divMenuPosition'+caption.replace(/ /g, "")});
		var span  = new Element('span', {text: caption});
		div.adopt (span);
		$(document.body).adopt(div);
		var coordonnee = menu.getCoordinates();
		coordonnee.width = coordonnee.width == 0 ? 108 : coordonnee.width;
		coordonnee.height = coordonnee.height == 0 ? 20 : coordonnee.height;
		div.setStyles({'overflow':'hidden', 'padding':'2px','left':coordonnee.left,'top':coordonnee.top, 'width':coordonnee.width-8, 'height':coordonnee.height-8, 'text-align':'center', 'line-height':coordonnee.height-8});
	});
}

function hideMenus (){
	$$('.headingmenu').each(function (menu){
		var caption = menu.get('caption') == null ? "Menu" : menu.get('caption');
		if($('divMenuPosition' + caption.replace(/ /g, ""))){
			var div  = $('divMenuPosition' + caption.replace(/ /g, ""));
			div.dispose ();
		}
	});
}