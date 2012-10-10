var cmsWikiPreview = function (id){
	var request = new Request.HTML({
		url : Copix.getActionURL('cms_editor|ajax|GetTextPreview'),
		evalScripts: true,
		update:'preview'+id
	}).post({'text':$(id).value});
}

var cmsWysiwygPreview = function (id, text){
	var request = new Request.HTML({
		url : Copix.getActionURL('cms_editor|ajax|GetWysiwygPreview'),
		evalScripts: true,
		update:'preview'+id
	}).post({'text':text});
}

function setCmsWikiPolice(beginTag, endTag, id)
{
	var textArea = $(id);
	poscurseur = textArea.scrollTop;
	
	objectValue = textArea.value;

	deb = textArea.selectionStart;
	fin = textArea.selectionEnd;

	objectValueDeb = objectValue.substring( 0 , textArea.selectionStart );
	objectValueFin = objectValue.substring( textArea.selectionEnd , textArea.textLength );
	objectSelected = objectValue.substring( textArea.selectionStart ,textArea.selectionEnd );

	textArea.value = objectValueDeb + beginTag + objectSelected;
	if (endTag){
		textArea.value += endTag ;
	}
	textArea.value += objectValueFin;
	
	textArea.focus();
	textArea.fireEvent('keyup');
	textArea.scrollTop = poscurseur;	
}