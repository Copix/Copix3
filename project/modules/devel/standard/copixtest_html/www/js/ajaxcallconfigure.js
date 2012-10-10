/**
  * Appels ajax pour la configuration des balises
  */
  
 // Variable pour incrémenter les balises lorsque l'on fait des tests libres
 var newTest = 0;
 
function call (id, type, path, name, attributes, contains) {
	var newDiv = document.createElement ('div');
	newDiv.setAttribute ("id", "ConfigureTag_" + id);
	var childDiv = document.getElementById('config');
	var parentDiv = childDiv.parentNode;
	
	parentDiv.insertBefore (newDiv, childDiv);
	
	new Ajax(ajaxurl, {
		data: 'id_tag=' + id + '&' + 'type=' + type + '&' + 'path=' + path + '&' + 'name=' + name + '&' + 'attributes=' + attributes + '&' + 'contains=' + contains,
		method: 'get',
		update: $('ConfigureTag_'+id)
	}).request ();
}

function freeConfigure (count) {
	var id = count + newTest;
	var newDiv = document.createElement ('div');
	newDiv.setAttribute ("id", "ConfigureTag_" + id);
	var childDiv = document.getElementById ('config');
	var parentDiv = childDiv.parentNode;
	parentDiv.insertBefore (newDiv, childDiv);
	new Ajax (ajaxnew, {
		data: 'id_tag=' + id,
		method: 'get',
		update: $('ConfigureTag_' + id)
		}).request ();
		newTest = newTest + 1;
}

function freeTest (test, id) {
	var newDiv = document.createElement ('div');
	newDiv.setAttribute ("id", "ConfigureTag_" + id);
	var childDiv = document.getElementById  ('config');
	var parentDiv = childDiv.parentNode;
	parentDiv.insertBefore (newDiv, childDiv);
	new Ajax (ajaxnew, {
	data: 'id_tag=' + id + '&' + 'id_test=' + test,
	method: 'get',
	update: $('ConfigureTag_' + id)
	}).request ();
}