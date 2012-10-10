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
	var filter = [0,8,9,16,17,18,37,38,39,40,46];
	if(document.getElementById(id) && theField.value.length >= len && !containsElement(filter,keyCode)) {
		document.getElementById(id).focus();
	}
	if (keyCode==8 && theField.value.length==0 && previous!=null) {
		focusprevious(theField,previous);
	}				
	return true;
}

function focusprevious(theField,previous) {
	if (previous==true) {
	    if (getIndex(theField)!=1 && previous!=null) {
		    theField.form[(getIndex(theField)-1) % theField.form.length].focus();
	    }
             } else {
		if (document.getElementById(previous)) {
			document.getElementById(previous).focus();
		}
	}
         }

function containsElement(arr, ele) {
	var found = false, index = 0;
	while(!found && index < arr.length)
		if(arr[index] == ele)
			found = true;
		else
			index++;
	return found;
}

function getIndex(input) {
	var index = -1, i = 0, found = false;
	while (i < input.form.length && index == -1)
		if (input.form[i] == input)
			index = i;
		else 
			i++;
	return index;
}