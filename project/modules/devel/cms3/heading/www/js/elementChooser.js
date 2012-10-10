function chooseElement(libelle, id, identifiantFormulaire, inputElement, type){
	if ($('libelleElement'+identifiantFormulaire)){
		$('libelleElement'+identifiantFormulaire).innerHTML = libelle;
	}
	
	$$('.headingelementchooser_selected').each(function (el){
		el.removeClass ('headingelementchooser_selected');
	});
	if ($('li_' + identifiantFormulaire + '_' + id)){
		$('li_' + identifiantFormulaire + '_' + id).addClass('headingelementchooser_selected');
	}
	
	if ($('deleteElement'+identifiantFormulaire)){
		$('deleteElement'+identifiantFormulaire).set({'styles': {'display':((id == '') ? 'none' : 'inline')}});
	}	
	
	$(inputElement).set('type_hei', type);
	$(inputElement).value = id;
	// bien laisser le fireEvent en dernier,
	// sinon si on a une surcharge de l'event change, toutes les données du chooseElement ne seront pas modifiées avant l'appel à cette surcharge
	$(inputElement).fireEvent('change');
}

function addElements (identifiantFormulaire, inputElement, multipleSelect){
	var selectedElements = getSelectedElements (identifiantFormulaire);
	if (selectedElements.length == 1 || !multipleSelect){
		var element = selectedElements.getLast();
		if ($('libelleElement'+identifiantFormulaire)){
			$('libelleElement'+identifiantFormulaire).innerHTML = element.get('libelle');
		}
		$(inputElement).value = element.get('pih');
		$(inputElement).fireEvent('change');
		if ($('deleteElement'+identifiantFormulaire)){
			$('deleteElement'+identifiantFormulaire).set({'styles': {'display':'inline'}});
		}
	} else {
		mutex = new Mutex (function (){
			if($("deleteElement"+identifiantFormulaire)){
				$("deleteElement"+identifiantFormulaire).fireEvent("click");
			}
		});
		eval ('addElements'+identifiantFormulaire+'()');
		mutex.execute ();
	}
}

function addHeading (identifiantFormulaire, inputElement){
	if ($('libelleElement'+identifiantFormulaire)){
		$('libelleElement'+identifiantFormulaire).innerHTML = "Rubrique " + $('selectedTreeElement'+identifiantFormulaire).get('rel');
	}
	$(inputElement).value = $('selectedTreeElement'+identifiantFormulaire).value;
	$(inputElement).fireEvent('change');
	if ($('deleteElement'+identifiantFormulaire)){
		$('deleteElement'+identifiantFormulaire).set({'styles': {'display':'inline'}});
	}
}

/*********** CREATION DE BRANCHE EN AJAX******************/
function createTree (treeroot,root, nodesInfos, formId, options){
	var filter = options.filter
	var isChildren = false;
	for (var i = 0 ; i < nodesInfos.length ; i++){
		nodeInfo = nodesInfos[i];
		var node = root.insert({
			text:nodeInfo.caption_hei,
			id:'node_' + formId + '_' + nodeInfo.public_id_hei,
			icon:nodeInfo.icon,
			open:nodeInfo.open,
			data : nodeInfo,
			onExpand: function(state) {
				if (this.data.open == false){
					this.clear();
					nodeInfo = this.data;
					if (nodeInfo.children == true){
						isChildren = true;
						eval("mutex"+formId+".push()");
						startLoading(nodeInfo.searchIndex != null && nodeInfo.searchIndex != '' ? 'elementChooserTree' : false, formId);
						treeroot.disable();
						var ajax = new Request.HTML({
							url : Copix.getActionURL ('heading|ajax|getElementChooserNode'),
							update : 'divTreeConstruct'+formId,
							onComplete : function (){ eval("mutex"+formId+".pop();mutex"+formId+".execute();");},
							evalScripts : true
						}).post({'public_id_hei':nodeInfo.public_id_hei, 'formId':formId, 'selectedIndex':$('selectedIndex' + formId).value, 'searchIndex':nodeInfo.searchIndex, 'open':true, 'options':options});
					}
					this.data.open = true;
				}
			}
		});

		//on a trouve le resultat de la recherche : onselectionne
		if (nodeInfo.public_id_hei == options.searchIndex){
			node.div.text.addClass("mooTree_search");
			stopLoading('elementChooserTree', formId);
			filepreview(nodeInfo.public_id_hei, formId, nodeInfo.type_hei);
		}
		
		if (nodeInfo.public_id_hei == $('selectedIndex' + formId).value){
			treeroot.selected = node;
			node.select(true);
		}
		
		if (nodeInfo.children == true){
			isChildren = true;
			eval("mutex"+formId+".push()");
			var ajax = new Request.HTML({
				url : Copix.getActionURL ('heading|ajax|getElementChooserNode'),
				update : 'divTreeConstruct'+formId,
				evalScripts : true,
				onComplete : function(){
					eval("mutex"+formId+".pop()");
				}.bind(this)
			}).post({'public_id_hei':nodeInfo.public_id_hei, 'formId':formId, 'selectedIndex':$('selectedIndex' + formId).value, 'searchIndex':options.searchIndex, 'open':nodeInfo.open, 'options':options});			
		}
	}
	if (nodesInfos.length == 0){
		for (var i = 0 ; i < root.nodes.length ; i++){
			var nodeInfo = root.nodes[i].data;
			if (nodeInfo.children == true){
				isChildren = true;
				eval("mutex"+formId+".push()");
				var ajax = new Request.HTML({
					url : Copix.getActionURL ('heading|ajax|getElementChooserNode'),
					update : 'divTreeConstruct'+formId,
					evalScripts : true,
					onComplete : function(){
						eval("mutex"+formId+".pop()");
					}.bind(this)
				}).post({'public_id_hei':nodeInfo.public_id_hei, 'formId':formId, 'searchIndex':options.searchIndex, 'open':nodeInfo.open, 'options':options});			
			}
			//on a trouve le resultat de la recherche : onselectionne
			if (nodeInfo.public_id_hei == options.searchIndex){
				root.nodes[i].div.text.addClass("mooTree_search");
				stopLoading('elementChooserTree', formId);
				filepreview(nodeInfo.public_id_hei, formId, nodeInfo.type_hei);
			}
		}
	}
}

function startLoading(idDiv, identifiantFormulaire){
	if ($('divElementChooserTreeLoad'+identifiantFormulaire)){
		$('divElementChooserTreeLoad'+identifiantFormulaire).setStyle('display', '');
	}
	if ($('divElementChooserLoad'+identifiantFormulaire) == null && idDiv != false){
		var div = new Element('div', {id :'divElementChooserLoad'+identifiantFormulaire, 'class':'divElementChooserLoad'});
		var span = new Element('span', {text: 'Chargement...'});
		var img = new Element('img', {src: Copix.getResourceURL('') + 'js/mootools/img/load.gif'});
		div.adopt (img);
		div.adopt (span);
		$(idDiv+identifiantFormulaire).setStyle('opacity', '0.5');
		$(idDiv+identifiantFormulaire).getParent().getParent().adopt(div);
		var coordonnee = $(idDiv+identifiantFormulaire).getCoordinates();
		var parentCoordonnee = $(idDiv+identifiantFormulaire).getParent().getParent().getCoordinates();
		coordonnee.width = coordonnee.width == 0 ? 100 : coordonnee.width;
		coordonnee.height = coordonnee.height == 0 ? 20 : coordonnee.height;
		div.setStyles({'left':coordonnee.left - parentCoordonnee.left - 1,'top':coordonnee.top - parentCoordonnee.top -1, 'width':coordonnee.width, 'height':coordonnee.height, 'line-height':coordonnee.height});
	}
}

function stopLoading(idDiv, identifiantFormulaire){
	if ($('divElementChooserLoad'+identifiantFormulaire)){
		$(idDiv+identifiantFormulaire).setStyle('opacity', '1');
		$('divElementChooserLoad'+identifiantFormulaire).dispose ();
	}
	if ($('divElementChooserTreeLoad'+identifiantFormulaire)){
		$('divElementChooserTreeLoad'+identifiantFormulaire).setStyle('display', 'none');
	}
}	

/********************FONCTIONS POUR LE MODE IMAGE/DOC CHOOSER et SELECTION MULTIPLE******************/

function changeView (identifiantFormulaire, view, public_id, type){
	$('elementchoosercontentfilesview'+identifiantFormulaire).value = view;
	Copix.savePreference ('heading|'+type+'Chooser', view, true, null, null, null)
	filepreview (public_id, identifiantFormulaire, type);
}

function filepreview (id, identifiantFormulaire, type){
	if (['article','image','document','video','flash','audio'].indexOf(type) != -1){
		startLoading('elementchoosercontentfiles', identifiantFormulaire);
		var urlpreview;
		if (type == "image"){
			urlpreview = Copix.getActionURL ('heading|ajax|getImagePreview');
		} else if (type == "document"){
			urlpreview = Copix.getActionURL ('heading|ajax|getDocPreview')
		} else if (type == "video" || type == "flash" || type == "audio"){
			urlpreview = Copix.getActionURL ('heading|ajax|getMediaPreview', {'type':type})
		} else {
			urlpreview = Copix.getActionURL ('heading|ajax|getArticlePreview')
		}
		var ajax = new Request.HTML({
			url : urlpreview,
			update : 'elementchoosercontentfiles'+identifiantFormulaire,
			evalScripts : true,
			onComplete : function(){
				var elements = $('elementchoosercontentfiles' + identifiantFormulaire).getElements ('.elementchooserfilenoselectedstate').extend($('elementchoosercontentfiles' + identifiantFormulaire).getElements ('.elementchooserfileselected'));
				elements.each(function(el){
					el.addEvent('click', function(event){
						selectElement(this, identifiantFormulaire, event);
					});
				});
				stopLoading('elementchoosercontentfiles', identifiantFormulaire);
				checkSelection(identifiantFormulaire);
			}.bind(this)
		}).post({'public_id_hei':id, 'view': $('elementchoosercontentfilesview'+identifiantFormulaire).value, 'formId':identifiantFormulaire});		
	}		
}

/*
 * elementchooserfilenoselectedstate et elementchooserfileselectedstate sont les flags de classe qui permettent de récupérer 
 * les elements selectionnés que ce soit en mode detaillé ou miniature 
 *
 */
lastSelectedElement = false;
function selectElement (element, formId, event){
	if (event.shift){
		var selectedElement = getSelectedElements (formId);
		if (selectedElement.length > 0){		
			var lastElement = lastSelectedElement;
			var children = element.getParent().getChildren();
			unSelectAll(formId);
			var toSelect = false;
			for (i=0; i<children.length;i++){
				if (children[i].get('pih') == element.get('pih') || children[i].get('pih') == lastElement.get('pih')){
					toSelect = !toSelect;
				}
				if (toSelect || children[i].get('pih') == element.get('pih') || children[i].get('pih') == lastElement.get('pih')){
					children[i].set("class", "elementchooserfileselectedstate");
					children[i].addClass("elementchooserfileselected");
				}
			}
			checkSelection(formId);
			return;
		}
	}

	element.set('class',element.hasClass("elementchooserfilenoselectedstate") ? 'elementchooserfileselectedstate' : 'elementchooserfilenoselectedstate');
	if(!element.type){
		//cas miniature : on ajoute la classe css particuliere à la div
		element.addClass(element.hasClass("elementchooserfileselectedstate") ? 'elementchooserfileselected' : 'elementchooserfile');
	}
	checkSelection(formId);
	lastSelectedElement = element;
}

function checkSelection(formId){
	var nb = $('elementchoosercontentfiles'+formId).getElements('.elementchooserfileselectedstate').length;
	if (nb > 0){
		$('filechoosersubmit' + formId).disabled = false;
	} else {
		$('filechoosersubmit' + formId).disabled = true;
	}
	$('stateelementchooser'+formId).innerHTML = nb + ' élément(s) séléctionnés';
}

function selectAll(formId){
	$('elementchoosercontentfiles'+formId).getElements('.elementchooserfilenoselectedstate').each(function (el){
		el.set("class", "elementchooserfileselectedstate");
		if(el.type == "checkbox"){
			el.checked = true;
		} else {
			el.addClass("elementchooserfileselected");
		}
	});
	checkSelection(formId);
}

function unSelectAll(formId){
	$('elementchoosercontentfiles'+formId).getElements('.elementchooserfileselectedstate').each(function (el){
		el.set("class", "elementchooserfilenoselectedstate");
		if(el.type == "checkbox"){
			el.checked = false;
		} 
		else {
			el.addClass("elementchooserfile");
		}
	});
	checkSelection(formId);
}

function getSelectedElements (identifiantFormulaire){
	return $('elementchoosercontentfiles'+identifiantFormulaire).getElements('.elementchooserfileselectedstate');
}

function createHeadingElement (editId, elementType, actiongroup){
	var r=confirm("La page sera automatiquement sauvegardée. Continuer ?");
	if (r==true)
  	{
  		document.location.href = Copix.getActionURL('portal|' + actiongroup + '|valid', {'editId':editId,'toCreate':elementType});
  	}
}

