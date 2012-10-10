var completer= {};
function tag_autocomplete (id, name, length, tab, url, onrequest, onselect) {
	var elem = $(id);
	$('autocompleteload_'+name).setStyle('display', 'none');
	completer['name'] = new Autocompleter.Ajax.Xhtml(elem, url, {
        'postData': tab,
		'onRequest': function(el) {			
			$('autocompleteload_'+name).setStyle('display', '');
			onrequest(el);
		},
		'onComplete': function(el) {
			$('autocompleteload_'+name).setStyle('display', 'none');
		},
		'onSelect': function (el,eleme,element) {
			onselect(el,eleme,element);
		},
		'parseChoices': function(el) {
		    try{
				var value = el.getFirst().innerHTML;
				el.inputValue = value;
				this.addChoiceEvents(el).getFirst().setStyles({'width':'200px'}).setHTML(this.markQueryValue(value));
			}catch (e){};
		 },
		 minLength:length,
		 maxChoices: 3
    });
}