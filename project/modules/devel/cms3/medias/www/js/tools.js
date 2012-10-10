var addMedia = function(identifiantFormulaire, portletId, pageId){
	$('position_'+identifiantFormulaire).value = ($('position_'+identifiantFormulaire).value).toInt() + 1;
	var div = new Element('div', {'id' : 'div_'+portletId+'_new_' + $('position_'+identifiantFormulaire).value}).injectBefore($('addMedia_' + identifiantFormulaire));
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('medias|ajax|addEmptyMedia'),
		evalScripts: true,
		update : div,
		onComplete : function(){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'position' : $('position_'+identifiantFormulaire).value.toInt()});
}

var updateMedia = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	}
	ajaxOn();
    
	new Request.HTML({
		url : Copix.getActionURL('medias|ajax|getMedia', {'id_media': $('id_media_' + identifiantFormulaire).value, 'position' : $('position_media_'+identifiantFormulaire).value.toInt(), 'formId':identifiantFormulaire}), 
		evalScripts: true,
		onComplete : function(){
			if($('id_media_' + identifiantFormulaire).value == ''){
				if($(portletId).getElements('div[id^=div_'+portletId+']').length > 1){	
					$('div_'+identifiantFormulaire).destroy ();
				}
			}	
			ajaxOff();
		},
		update : 'media_' + identifiantFormulaire
	}).post($('formOptionMedia' + identifiantFormulaire));
    oParam = null;
}

var arMediaTreeId = [];
function refreshMediaTrees(selected){
	arMediaTreeId.each(function(el){
		eval('refreshTree'+el+'('+selected+')');
	});
}

/**
 * Preview dans mediaChooser
 */
function showMediaContent(content, formId){
	var scripts = '';
	var text = content.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(){
		scripts += arguments[1] + '\n';
		return '';
	});
	
	$("contentMediaPreview"+formId).innerHTML = content;
	$exec(scripts);
}