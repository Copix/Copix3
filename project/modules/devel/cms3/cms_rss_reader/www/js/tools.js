var addFeed = function (portletId) {
    ajaxOn();
    eval('position'+portletId+'++;');
    eval('var currentPosition = position'+portletId+';');
    eval('var urlBtnDelete  = urlDeleteBtn'+portletId+';');
    eval('var editId = editId'+portletId+';');
    var feedContainer = new Element('div', {
        'id': 'feed_'+portletId+currentPosition
    });
    var feedLabel = new Element('label', {
        'for': 'url_feed_'+portletId+currentPosition,
        'html': 'Url du flux '+currentPosition+' '
    });
    var feedInput = new Element('input', {
        'id': 'url_feed_'+portletId+currentPosition,
        'size': '40',
        'name': 'url_feed_'+portletId+'[]',
        'type': 'text',
        'events': {
            'change': function() {
                updateFeed(portletId, editId);
            }
        }
    });
    var feedDeleteBtn = new Element('input', {
        'id': 'deleteFeed'+portletId+currentPosition,
        'type': 'image',
        'width': '14px',
        'src': urlBtnDelete,
        'title': 'supprimer',
        'events': {
            'click': function(e){
                 e.stop();
                deleteFeed(portletId, editId,'feed_'+portletId+currentPosition)
            }
        }
    });
    feedLabel.inject(feedContainer);
    feedInput.inject(feedContainer);
    feedDeleteBtn.inject(feedContainer);
    feedContainer.inject($('portlerForm'+portletId));
	ajaxOff();
}
var deleteFeed = function(portletId, pageId, feedId) {
    $(feedId).dispose();
    updateFeed (portletId, pageId);
}

var updateFeed = function(portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	eval('$(\'portlerForm'+portletId+'\').fireEvent(\'submit\');');
}