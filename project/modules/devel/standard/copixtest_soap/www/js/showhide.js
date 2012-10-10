/**
  * Script permettant d'afficher et de cacher un div
  */

function show (obj, id) {
var configure = document.getElementById(obj);
	if (configure.style.display == "none") {
	configure.style.display = "";
	} else {
	configure.style.display = "none";
	}
}

function hide (obj) {
var configure = document.getElementById(obj);
	if(configure.style.display == "") {
	configure.style.display = "none";
	}
}
