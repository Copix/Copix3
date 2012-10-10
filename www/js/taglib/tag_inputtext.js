function autofocus(theField,len,e,previous) {
	var keyCode = e.keyCode;
	var filter = [0,8,9,16,17,18,37,38,39,40,46];
	if(theField.value.length >= len && !containsElement(filter,keyCode)) {
		theField.form[(getIndex(theField)+1) % theField.form.length].focus();
	}
	if (keyCode==8 && theField.value.length==0 && previous!=null) {
		focusprevious(theField,previous);
	}
	return true;
}

function focusid(theField,len,e,id,previous) {
	var keyCode = e.keyCode;
	var pos = getCaretPos(theField);
	var filter = [0,8,9,16,17,18,37,38,39,40,46];
	if($(id) && theField.value.length >= len && !containsElement(filter,keyCode) && pos == len) {
		$(id).focus();
		if($(id).get("maxlength") == $(id).value.length){
			replaceCharacter(id, 0);
		}else{
			setCaretTo(id, 0);
		}
	}
	if(pos < $(theField).value.length && !containsElement(filter,keyCode)) {
		if(theField.value.length == len){
			replaceCharacter(theField, pos);
		}else{
			setCaretTo(theField, pos);
		}
	}
	if(pos == 0 && keyCode == 37){
		if($(previous)){
			focusprevious(theField,previous);
			setCaretTo(previous, $(previous).value.length);
		}
	}
	if(pos == $(theField).value.length && keyCode == 39){
		if($(id)){
			$(id).focus();
			setCaretTo($(id), 0);
		}
	}
	if (keyCode==8 && theField.value.length==0 && previous!=null) {
		focusprevious(theField,previous);
	}
	return true;
}

/**
* Remplace les caractères déja présents
*/
function replaceCharacter(theField, pos){
	if($(theField).setSelectionRange){
		$(theField).setSelectionRange(pos, pos+1);
	}else{
		var range = $(theField).createTextRange();
		range.collapse(true);
		range.moveEnd("character", pos+1);
		range.moveStart("character", pos);
		range.select();
	}
}

function focusprevious(theField,previous) {
	if (previous==true) {
		if (getIndex(theField)!=1 && previous!=null) {
			theField.form[(getIndex(theField)-1) % theField.form.length].focus();
		}
	} else {
		if ($(previous)) {
			$(previous).focus();
			setCaretTo($(previous), $(previous).value.length);
		}
	}
}

function containsElement(arr, ele) {
	var found = false, index = 0, l = arr.length;
	while(!found && index < l) {
		if(arr[index] == ele) {
			found = true;
		} else {
			index++;
		}
	}
	return found;
}

function getIndex(input) {
	var index = -1, i = 0;
	while (i < input.form.length && index == -1) {
		if (input.form[i] == input) {
			index = i;
		} else {
			i++;
		}
	}
	return index;
}

function setCaretTo(obj, pos) {
	if(obj.createTextRange) {
		/* Create a TextRange, set the internal pointer to
		   a specified position and show the cursor at this
		   position
		*/
		var range = obj.createTextRange();
		range.move("character", pos);
		range.select();
	} else if(obj.selectionStart) {
		/* Gecko is a little bit shorter on that. Simply
		   focus the element and set the selection to a
		   specified position
		*/
		obj.focus();
		obj.setSelectionRange(pos, pos);
	}
}

function getCaretPos(el) {
	var rng, ii=-1;
	if(typeof el.selectionStart=="number") {
		ii=el.selectionStart;
	} else if (document.selection && el.createTextRange){
		rng=document.selection.createRange();
		rng.collapse(true);
		rng.moveStart("character", -el.value.length);
		ii=rng.text.length;
	}
	return ii;
}