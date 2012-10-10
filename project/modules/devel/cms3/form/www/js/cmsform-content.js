/* *** Gestion du contenu du formulaire *** */

/**
 * Initialisation du drag sur le contenu du formulaire
 */
function initDragContent (pUrlUpdateOrder) {
	myTblSort = new mooTblSort("form_content",{  
		ignore: $$("#form_content thead tr"),
		handle: $$(".draggable"),  
	   
		onStart : function(el){
			el.setStyle("background-color", "#AAA");
			el.setStyle('cursor','move'); 
		},
		onComplete : function(el){
			el.setStyle("background-color", "");
			el.setStyle('cursor'); 
			
			ajaxOn();
			
			var arElementOrder = new Array ();
			$$('.sortable').each(function(el, index) {
				arElementOrder[index] = el.get("id_element");
			});
			
			var myRequest = new Request({url:pUrlUpdateOrder,onSuccess:ajaxOff});
			myRequest.get({'arElementOrder': arElementOrder.toString(), 'editId': $('editId').value});
		}
	});	  
}


/**
 * Methodes de mise à jour
 */


function submitElementValue(){
	ajaxOn();
	var submitElementRequest = new Request.HTML (
								 {
								 'url' : Copix.getActionURL("form|adminajax|submitElementValue"),
								 'method': 'get',
								 'evalScripts':true,
								 'onSuccess': function () {
											$('element_value_list').innerHTML += this.response.text;
											$('cfev_value').value = '';
											myUpdateElementSlide.slideIn();
											ajaxOff();
										}
								 }
	);
	
	var requestSubmitElementVar = {
		'cfe_id' : $('cfe_id').value,
		'cfev_value' : $('cfev_value').value
	};
	
	submitElementRequest.post(requestSubmitElementVar);
}

function deleteElementValue(idElement){
	ajaxOn();
	var deleteElementValueRequest = new Request.HTML (
								 {
								 'url' : Copix.getActionURL("form|adminajax|deleteElementValue"),
								 'method': 'get',
								 'evalScripts':true,
								 'onSuccess':ajaxOff
								 }
	);
	var requestDeleteElementValueVar = {
		'cfev_id' : idElement
	};
	
	deleteElementValueRequest.post(requestDeleteElementValueVar);
}

/**
 * Passage en mode édition d'une element_value
 */
function switchUpdateElementValue(idElement)
{
	//Modification de l'input
	var value = $('element_value_cell_' + idElement).innerHTML;
	
	$('element_value_cell_' + idElement).innerHTML = "<input id='element_value_input_" + idElement + "' type='text' value='" + value + "' style='width:100%;'";
	
	//Affichage des boutons
	$('element_value_edit_' + idElement).style.display = 'none';
	$('element_value_save_' + idElement).style.display = '';
	$('element_value_undo_' + idElement).style.display = '';
}
/**
 * Passage en mode display element_value
 */
function switchDisplayElementValue(idElement, value)
{
	//Modification de l'input
	$('element_value_cell_' + idElement).innerHTML = value;
	
	//Affichage des boutons
	$('element_value_edit_' + idElement).style.display = '';
	$('element_value_save_' + idElement).style.display = 'none';
	$('element_value_undo_' + idElement).style.display = 'none';
}

function submitUpdateElementValue(idElement){
	ajaxOn();
	var submitUpdateElementValueRequest = new Request.HTML (
								 {
								 'url' : Copix.getActionURL("form|adminajax|submitUpdateElementValue"),
								 'method': 'get',
								 'evalScripts':true,
								 'onSuccess':ajaxOff
								 }
	);
	var requestSubmitUpdateElementValueVar = {
		'cfev_id' : idElement,
		'cfev_value': $('element_value_input_' + idElement).value
	};
	
	submitUpdateElementValueRequest.post(requestSubmitUpdateElementValueVar);
}

function deleteElement(idElement){
	ajaxOn();
	var deleteElementRequest = new Request.HTML (
								 {
								 'url' : Copix.getActionURL("form|adminajax|deleteElement"),
								 'method': 'get',
								 'evalScripts':true,
								 'onSuccess':ajaxOff
								 }
	);
	var requestDeleteElementVar = {
		'cfe_id' : idElement
	};
	
	deleteElementRequest.post(requestDeleteElementVar);
}


function updateFieldType(pId, pType) {
	ajaxOn();
	var request = new Request.HTML (
							 {
							 'url' : Copix.getActionURL("form|adminajax|getFieldType"),
							 'method': 'get',
							 'evalScripts':true,
							 'onSuccess':function(){
							 	ajaxOff();
						 		$('td_button_type_undo').setStyle ('display', '');
								$('td_button_type_update').setStyle ('display', 'none');
								},
							 'update': 'th_type'
							 
							 }
	);
	var requestVar = {
		'cfe_id' : pId,
		'cfe_type' : pType
	};
	
	request.post(requestVar);
}
