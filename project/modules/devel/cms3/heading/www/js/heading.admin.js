function checkActionIcons () {
	var actions = new Array ('copy', 'cut', 'delete', 'archive', 'publish');
	var displays = new Array (null, null, null, null, null);

	var hasChecked = false;
	$$ ('.elementCheckbox').each (function (element) {
		if (element.checked) {
			hasChecked = true;
			for (x = 0; x < actions.length; x++) {
				displays[x] = ((displays[x] == null && element.hasClass ('can_' + actions[x])) || (displays[x] == 'inline' && element.hasClass ('can_' + actions[x]))) ? 'inline' : 'none';
			}
		}
	});
	if (!hasChecked) {
		displays = new Array ('none', 'none', 'none', 'none', 'none');
	}

	for (x = 0; x < actions.length; x++) {
		if ($ ('action_' + actions[x]) != undefined) {
			$ ('action_' + actions[x]).setStyle ('display', displays[x]);
		}
	}
}

function save (pIdElement, pTypeElement, pDivId) {
	// Lancement de la sauvegarde
	var AjaxRequest = new Request.HTML({
	    url: Copix.getActionURL ('heading|elementinformation|save', {'id_helt':pIdElement, 'type_hei':pTypeElement}),
		evalScripts: true,
		update: pDivId
	}).send ();
	return false;
}

var moveElement = function (oldPosition, newPosition, id, type){
	document.location.href = Copix.getActionURL("heading|element|move")+"?oldPosition="+oldPosition+"&newPosition="+newPosition+"&id="+id+"&type="+type;
}

var checkState = false;
function checkUncheck () {
    checkState = !checkState;
    var first = null;
    //suppression des classes de selection
	$$(".trSelectedElement").each (function (el){
		el.removeClass ("trSelectedElement");
	});
	var elementCheckbox = $$(".elementCheckbox").each (function (el){
		el.checked = checkState;
		if (checkState){
			if (first == null) {
				first = el;
			}
			var reg=new RegExp("[|]+", "g");
			var tab=el.value.split(reg);
			$$("tr[id_helt=" + tab[0] + "][type=" + tab[1] + "]").each (function (tr){
				tr.addClass ("trSelectedElement");
			});
		}
	});
	if (first != null){
		var reg=new RegExp("[|]+", "g");
		var tab=first.value.split(reg);		
		showMultipleHeadingElementInformationsIn (tab[0], tab[1], tab[2], 'HeadingElementInformationDiv', checkState);	
	}
	// si tout est deselectionné, on affiche la rubrique parente.
	else if (!checkState){
		var el = $('trParentHeading');
		showHeadingElementInformationsIn (el.get('id_helt'), el.get('type'), el.get('public_id'), 'HeadingElementInformationDiv');
	}	
	return false;
}

var arrowPosition = function (){
	/*$("arrow").setStyle('display','');
	$$(".trSelectedElement").each (function (el){
		var border_height = el.getSize ().y / 2;
		$("arrow").setStyles ({"border-bottom-width" : border_height, 
								"border-top-width" : border_height,
								"top" : el.getCoordinates ().top,
								"left" : $('elementTable').getCoordinates ().left + el.getCoordinates ().width -1
								});
		if ($('arrow').getCoordinates().left != $('elementTable').getCoordinates ().left + el.getCoordinates ().width -1){
			$("arrow").setStyle ("left", el.getCoordinates ().width  - 1);
		}
		$("arrow").setStyle ("top", el.getCoordinates ().top - ($("arrow").getCoordinates().top - el.getCoordinates ().top));
	});	*/
}

var lastPublicId; 

function showMultipleHeadingElementInformationsIn (pIdElement, pTypeElement, pPublicId, pDivId, checked){
	//avant de changer la selection d'element on efface les menus affichés au cas ou on soit dans l'onglet menu
	if (typeof hideMenus == 'function'){
		hideMenus ();
	}
	lastPublicId = false;
	if (!checked){
		$$("tr[id_helt=" + pIdElement + "][type=" + pTypeElement + "]").each (function (el){
			el.removeClass ("trSelectedElement");
		});
	} else {
		$$("tr[id_helt=" + pIdElement + "][type=" + pTypeElement + "]").each (function (el){
			el.addClass ("trSelectedElement");
			$("checkbox_" + pIdElement + "_" + pTypeElement).checked = true;
		});
	}
	
	var elements = $$('.elementCheckbox:checked');
	
	if (elements.length == 1){
		elements.each (function (el){
			var reg=new RegExp("[|]+", "g");
			var tab=el.value.split(reg);

			return showHeadingElementInformationsIn (tab[0], tab[1], tab[2], pDivId);
		});
	} else {
		var tabId = new Array ();
		elements.each (function (el){
			tabId.push(el.value);
		});
        if(tabId.length > 0)
        {
            //lancement de la mise à jour
            var AjaxRequest = new Request.HTML({
                url: Copix.getActionURL('heading|elementinformation|', {'id_helt':tabId, 'type_hei':pTypeElement}),
                onRequest: function(){
                    $(pDivId).setStyles({'opacity': '0.3'});
                },
                onSuccess: function(){
                    $(pDivId).setStyles({'opacity': '1'});
                },
                evalScripts: true,
                update: $(pDivId)
            }).send ();
        }
        // Si aucun élément n'est sélectionné, on sélectionne la rubrique principale
        else
        {
            var myElem;
            // on récupère les infos de l'élément principal dans la balise de lien, propriété onClick
            if( ($('trParentHeading')) && (myElem = $('trParentHeading').getElement('a')) )
            {
                var reg = new RegExp('^(return )(showHeadingElementInformationsIn.*)')
                var aRetour;
                if(aRetour = reg.exec(myElem.get('onClick')))
                {
                  // Appel à la fonction showHeadingElementInformationsIn(...)
                  eval(aRetour[2]);
                }
                else
                {
                    $(pDivId).set('html', '');
                }

            }
            // si on n'a pas pu récupérer l'info, on n'affiche rien dans la colonne de droite.
            else
            {
                $(pDivId).set('html', '');
            }
        }
    }
}

function showHeadingElementInformationsIn (pIdElement, pTypeElement, pPublicId, pDivId){
	//avant de changer l'element selectionné, on efface les menus affichés par l'onglet menu
	if (typeof hideMenus == 'function'){
		hideMenus ();
	}
	//selection
	$$(".trSelectedElement").each (function (el){
		el.removeClass ("trSelectedElement");
	});
	
	$$("tr[id_helt=" + pIdElement + "][type=" + pTypeElement + "]").each (function (el){
		el.addClass ("trSelectedElement");
		/*var border_height = el.getSize ().y / 2;
		$("arrow").setStyles ({"border-bottom-width" : border_height, 
								"border-top-width" : border_height,
								"top" : el.getCoordinates ().top,
								"left" : $('elementTable').getCoordinates ().left + el.getCoordinates ().width -1
								});
		if ($('arrow').getCoordinates().left != $('elementTable').getCoordinates ().left + el.getCoordinates ().width -1){
			$("arrow").setStyle ("left", el.getCoordinates ().width  - 1);
		}
		$("arrow").setStyle ("top", el.getCoordinates ().top - ($("arrow").getCoordinates().top - el.getCoordinates ().top));*/
	});

	//gestion du double clic
    if (pPublicId == lastPublicId){
       if (pTypeElement == 'heading'){
          document.location = Copix.getActionURL ('heading|element|', {'heading' : pPublicId});
          return false;
       } 
    }
    lastPublicId  = pPublicId;
    
    //selection checkbox
    $$(".elementCheckbox").each(function(el){
    	el.checked = false;
    });
    if ($("checkbox_" + pIdElement + "_" + pTypeElement)){
    	$("checkbox_" + pIdElement + "_" + pTypeElement).checked = true;
    }
    
	//lancement de la mise à jour
	var AjaxRequest = new Request.HTML({
	    url: Copix.getActionURL('heading|elementinformation|', {'id_helt':pIdElement, 'type_hei':pTypeElement}),
		evalScripts: true,
        onRequest: function(){
            $(pDivId).setStyles({'opacity': '0.3'});
        },
        onSuccess: function(){
            $(pDivId).setStyles({'opacity': '1'});
        },
		update: pDivId
	}).send ();
	return false;
} 