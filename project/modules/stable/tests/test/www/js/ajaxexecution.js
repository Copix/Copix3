/* 
 * Appels ajax pour l'éxécution des tests
 */
 var arTests = new Array ();
 arTests =  tests.split('|'); // Définition d'un tableau de tests

// on attend que la page soit complètement chargé pour lancer les tests
 window.addEvent('domready', function () {
 	 i = 0; // Itérateur de test
	 ajaxCall ();
 });

 
 function ajaxCall () {
 
 	if (i+1 < arTests.length) {
 		id = arTests[i];
 	}
 	
 	var newDiv = document.createElement ('div');
	newDiv.setAttribute ("id", "result_" + id);
	var childDiv = document.getElementById('table_result');
	var parentDiv = childDiv.parentNode;
	
	parentDiv.insertBefore (newDiv, childDiv);
	
	new Ajax(url, {
		data: 'id=' + id,
		method: 'get',
		update: $(newDiv),
		evalScripts: true,
		onRequest: function () {
		},
		onComplete: function () {
			if (i+2 < arTests.length) {
				i = i + 1;
				ajaxCall ();
			}
		}
	}).request ();
}

