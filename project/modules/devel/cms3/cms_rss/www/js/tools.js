var updateRss = function(id_rss, caption_rss, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('cms_rss|ajax|UpdateRss'),
		evalScripts: true,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'id_rss' : id_rss,
				'caption_rss' : caption_rss
				});
}