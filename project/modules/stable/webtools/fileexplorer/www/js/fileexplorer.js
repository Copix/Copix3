var checkState = false;
function checkUncheck () {
    checkState = !checkState;
	$$("input[type=\"checkbox\"]").each (function (el){
		el.checked = checkState;
	});
	return false;
}