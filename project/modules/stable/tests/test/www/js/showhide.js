/*
 * Fonctions qui permettent d'afficher ou de cacher un div
 */

function show (obj, id) {
var error = document.getElementById(obj);
	if (error.style.display == "none") {
	error.style.display = "";
	} else {
	error.style.display = "none";
	}
}

function hide (obj) {
var error = document.getElementById(obj);
	if(error.style.display == "") {
	error.style.display = "none";
	}
}