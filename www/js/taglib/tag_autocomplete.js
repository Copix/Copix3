var completer= {};
function tag_autocomplete (id, name, length, postData, url, onrequest, onselect) {
	var elem = $(id);
	$('autocompleteload_'+name).setStyle('display', 'none');
	completer[name] = new Autocompleter.Request.HTML(elem, url, {
        'indicatorClass': 'autocompleter-loading',
        'postData': postData,
		'injectChoice': function(el) {
            var first = el.getFirst();
            var value = first.innerHTML;
            el.inputValue = value;
            first.set('html', this.markQueryValue(value));
            this.addChoiceEvents(el);
		 },
         'width' : '300',
         'minLength' : length
    });
}
