var mutex = null;

var addImage = function(identifiantFormulaire, portletId, pageId, publicId, islite){
	
	$('position_'+portletId).value = ($('position_'+portletId).value).toInt() + 1; 
	var div = new Element('div', {'id' : 'div_'+portletId+'_pos_' + $('position_'+portletId).value}).injectBefore($('addImage_' + portletId));
	if (mutex!= null){
		mutex.push ();
	}
	if(publicId == null){
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('images|ajax|addEmptyImage'),
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
					'position' : $('position_'+portletId).value.toInt(),
					'islite' : (islite) ? 'true' : 'false',
					'editionMode':$('editionMode_'+portletId).value
		});
	}
	else{
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('images|ajax|addImage'),
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
					'position' : $('position_'+portletId).value.toInt(),
					'id_image' : publicId,
					'islite' : (islite) ? 'true' : 'false',
					'formId' : identifiantFormulaire,
					'editionMode':$('editionMode_'+portletId).value});
	}
}

var updateImage = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 

	ajaxOn();
	if ($('imgClicker'+identifiantFormulaire)){
		$('imgClicker'+identifiantFormulaire).setStyle('opacity',0.6);
	}
	var request = new Request.HTML({
		url : Copix.getActionURL('images|ajax|getImage', {'id_image': $('id_image_' + identifiantFormulaire).value, 'position' : $('position_image_'+identifiantFormulaire).value.toInt(), 'formId':identifiantFormulaire, 'editionMode':$('editionMode_'+portletId).value}),
		evalScripts: true,
		onComplete : function(){
			if($('id_image_' + identifiantFormulaire).value == ''){
				if($(portletId).getElements('div[id^=div_'+portletId+']').length > 1){	
					$('div_'+identifiantFormulaire).destroy ();
				}
			}else {
				$('divImageOptions'+identifiantFormulaire).setStyle('display','');
			}
			ajaxOff();	
			//on affiche la div de choix d'image
			if (!$('imgChoix'+portletId)){
				addImage(identifiantFormulaire, portletId, pageId, null, false);
			}
		},
		update : ($('updateImage_'+identifiantFormulaire).value ? 'image_' + identifiantFormulaire : '')
	}).post($('formOptionImage' + identifiantFormulaire));
}

var updateImageLite = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('images|ajax|getImage'),
		evalScripts: true,
		onComplete : function(){
			if($('id_image_' + identifiantFormulaire).value == ''){
				if($(portletId).getElements('div[id^=div_'+portletId+']').length > 1){	
					$('div_'+identifiantFormulaire).destroy ();
				}
			}
			ajaxOff();	
			//on affiche la div de choix d'image
			if (!$('imgChoix'+portletId)){
				addImage(identifiantFormulaire, portletId, pageId, null, true);
			}
		},
		update : 'image_' + identifiantFormulaire
	}).post({'id_image' : $('id_image_' + identifiantFormulaire).value,
				'editId' : pageId,
				'portletId' : portletId,
				'alt_image' : true,
				'file_image' : true,
				'image_align' : 'center',
				'islite' : 'true',
				'position' : $('position_image_'+identifiantFormulaire).value.toInt(),
				'editionMode':$('editionMode_'+portletId).value
				});
}


function checkProportion(axe, value, toUpdate, identifiantFormulaire, portletId, pageId){	
	if($('proportion_image_'+identifiantFormulaire).checked == true){
		ajaxOn();
		var myHTMLRequest = new Request.HTML({url : Copix.getActionURL('images|ajax|getProportion'), 
				update : $('retourProportion'+identifiantFormulaire), 
				evalscripts : true, 
				onSuccess : function(){
					updateImage(identifiantFormulaire, portletId, pageId);
					ajaxOff();
				}
		});
		myHTMLRequest.post({valeur : value, axe:axe, toUpdate : toUpdate, 'id_image' : $('id_image_'+identifiantFormulaire).value});
	}
}

function showGalery(identifiantFormulaire){
	$('trgalery' + identifiantFormulaire).setStyle('display', ($('thumb_show_image'+identifiantFormulaire).value == "smoothbox" ? '' : 'none'));
}

function updateView (newTemplate, portletId, identifiantFormulaire, pageId){
	if($('currentTemplate_'+portletId).value != newTemplate){
		$('currentTemplate_'+portletId).value = newTemplate;
		ajaxOn();
		var request = new Request.HTML({
			url : Copix.getActionURL('images|ajax|updateView'),
			evalScripts: true,
			onComplete : function(){
				ajaxOff();	
			},
			update : 'imageEditView' + portletId
		}).get({'template' : newTemplate, 'currentEditionMode': $('editionMode_' + portletId).value, 'editId' : pageId, 'portletId' : portletId});
	}
	
}

var arImageTreeId = [];
function refreshImageTrees(selected){
	arImageTreeId.each(function(el){
		eval('refreshTree'+el+'('+selected+')');
	});
}