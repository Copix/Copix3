var mutex = null;

var addArticle = function(identifiantFormulaire, portletId, pageId, publicId){
	$('position_'+portletId).value = ($('position_'+portletId).value).toInt() + 1; 
	var div = new Element('div', {'id' : 'div_'+portletId+'_pos_' + $('position_'+portletId).value}).injectBefore($('addArticle_' + portletId));
	if (mutex!= null){
		mutex.push ();
	}
	
	if(publicId == null){
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('articles|ajax|addEmptyArticle'),
			evalScripts: true,
			update : div,
			onComplete : function(){
				if (mutex!= null){
					mutex.pop ();
				}
				ajaxOff();
			}
		}).post({'editId' : pageId,
					'portletId' : portletId,
					'position' : $('position_'+portletId).value.toInt()});
	}
	else{
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('articles|ajax|addArticle', {'id_article': publicId, 'position' : $('position_'+portletId).value.toInt(), 'formId':identifiantFormulaire}),
			evalScripts: true,
			update : div,
			onComplete : function(){
				if (mutex!= null){
					mutex.pop ();
				}
				ajaxOff();
			}
		
		}).post($('formOptionArticle' + identifiantFormulaire));
	}
}

var updateArticle = function(identifiantFormulaire, portletId, pageId){

	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	}
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('articles|ajax|getArticle', {'id_article': $('id_article_' + identifiantFormulaire).value, 'position' : $('position_article_'+identifiantFormulaire).value.toInt(), 'formId':identifiantFormulaire, 'template' : $('template_' + identifiantFormulaire).value}), 
		evalScripts: true,
		onComplete : function(){
			if($('id_article_' + identifiantFormulaire).value == ''){
				if($(portletId).getElements('div[id^=div_'+portletId+']').length > 1){	
					$('div_'+identifiantFormulaire).destroy ();
				}
			}	
			ajaxOff();
			//on affiche la div de choix d'article
			if (!$('articleChoix'+portletId)){
				addArticle(identifiantFormulaire, portletId, pageId, null, false);
			}
		},
		update : 'article_' + identifiantFormulaire
	}).post($('formOptionArticle' + identifiantFormulaire));
		

}

var arArticleTreeId = [];
function refreshArticleTrees(selected){
	arArticleTreeId.each(function(el){
		eval('refreshTree'+el+'('+selected+')');
	});
}

/**
 * Preview dans le articleChooser
 */
function showArticleContent(content, formId){
	$("contentArticlePreview"+formId).innerHTML = content;
}