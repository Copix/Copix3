var mutex = null;

var addDocument = function(identifiantFormulaire, portletId, pageId, publicId){
	$('position_'+portletId).value = ($('position_'+portletId).value).toInt() + 1; 
	var div = new Element('div', {'id' : 'div_'+portletId+'_pos_' + $('position_'+portletId).value}).injectBefore($('addDocument_' + portletId));
	if (mutex!= null){
		mutex.push ();
	}
	if(publicId == null){
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('document|ajax|addEmptyDocument'),
			update : div,
			evalScripts: true,
			onComplete : function(){
				if (mutex!= null){
					mutex.pop ();
				}
				ajaxOff();
			},
		}).post({'editId' : pageId,
					'portletId' : portletId,
					'position' : $('position_'+portletId).value.toInt()});
	}
	else{
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('document|ajax|addDocument'),
			update : div,
			evalScripts: true,
			onComplete : function(){
				if (mutex!= null){
					mutex.pop ();
				}
				ajaxOff();
			},
		}).post({'editId' : pageId,
					'portletId' : portletId,
					'position' : $('position_'+portletId).value.toInt(),
					'id_document' : publicId});
	}
}

var updateDocument = function(identifiantFormulaire, portletId, pageId){
		
		if(typeof updateToolBar =='function'){
			updateToolBar(portletId, pageId);
		}
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('document|ajax|getDocument',  {'id_document': $('id_document_' + identifiantFormulaire).value, 'position' : $('position_doc_'+identifiantFormulaire).value.toInt(), 'formId':identifiantFormulaire,  'template' : $('template_' + identifiantFormulaire).value}),
			evalScripts: true,
			onComplete : function(){
				if($('id_document_' + identifiantFormulaire).value == ''){
					if($(portletId).getElements('div[id^=div_'+portletId+']').length > 1){	
						$('div_'+identifiantFormulaire).destroy ();
					}
				}	
				ajaxOff();
				//on affiche la div de choix de document
				if (!$('docChoix'+portletId)){
					addDocument(identifiantFormulaire, portletId, pageId, null, false);
				}
			},
			update : 'document_' + identifiantFormulaire
		}).post($('formOptionDocument' + identifiantFormulaire));
	}

var arDocumentTreeId = [];
function refreshDocumentTrees(selected){
	arDocumentTreeId.each(function(el){
		eval('refreshTree'+el+'('+selected+')');
	});
}