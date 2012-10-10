<script>
/**
 *	Affichage du block de mise à jour d'un élément
 */
function showUpdateDiv()
{ldelim}
	myUpdateElementSlide.slideIn();
	TB_init();
	ajaxOff();
{rdelim}


/**
 * Ouverture du bloc de mise à jour d'un élément
 */
function updateElement(idElement) 
{ldelim}
	ajaxOn();
	//On supprime le formulaire d'ajout pour éviter les conflits d'id et éclaircir
	myNewElementSlide.slideOut();
	$('newelement_div').innerHTML = '';
	
	var ajaxShowUpdateElementRequest = new Request.HTML (
								 {ldelim}
								 'url' : '{copixurl dest="adminajax|updateElement"}', 
								 'method': 'post',
								 'evalScripts':true,
								 'update':'update_element_div',
								 'onSuccess' : showUpdateDiv
								 {rdelim}
	);
	
	var requestShowUpdateElementVar = {ldelim}
		'cfe_id': idElement
	{rdelim};
	
	ajaxShowUpdateElementRequest.post(requestShowUpdateElementVar);
{rdelim}

/**
 * Ajout / Suppression d'un champs du formulaire
 */
function addRemoveField(idElement)
{ldelim}
	ajaxOn();
	var action = 'remove';  
	var onAddRemoveField = function() {ldelim}eval(this.response.text);ajaxOff();{rdelim}
	if ($('cb_form_field_' + idElement).checked == true) {ldelim}
		action = 'add';
		onAddRemoveField = function() {ldelim}
			var lastchild = $('form_content_body').getLast();	
			$('form_content_body').innerHTML += this.response.text;initDragContent ("{copixurl dest='adminajax|updateContentOrder'}");
			if (lastchild && !lastchild.hasClass('alternate')){ldelim}
				$('form_content_body').getLast().addClass('alternate');
			{rdelim}
			ajaxOff();
		{rdelim}
	{rdelim}
	var ajaxAddRemoveFieldRequest = new Request.HTML (
								 {ldelim}
								 'url' : '{copixurl dest="adminajax|addRemoveField"}', 
								 'method': 'get',
								 'evalScripts':true, 
								 'onSuccess' : onAddRemoveField
								 {rdelim}
	);
	
	var requestAddRemoveFieldVar = {ldelim}
		'editId' : $('editId').value,
		'cfe_id' : idElement,
		'do' : action
	{rdelim};
	
	ajaxAddRemoveFieldRequest.post(requestAddRemoveFieldVar);
{rdelim}
</script>

<form id="form_fields" class="div_element_list">
	<input type='hidden' id='editId' name='editId' value={'editId'|request} />
	<div id="form_fields_list">
		<h3>Liste des champs disponibles</h3>
		{section name=fields loop=$arFormElement}
			<div id="form_field_line_{$arFormElement[fields]->cfe_id}" class="form_field">
				<span class="form_field_chk">
					<input id="cb_form_field_{$arFormElement[fields]->cfe_id}" name="form_field[]" type='checkbox' 
						   value="{$arFormElement[fields]->cfe_id}" onclick="addRemoveField({$arFormElement[fields]->cfe_id})"
						   {if in_array($arFormElement[fields]->cfe_id, $arFormElementSelected)} checked="checked"{/if} />
				</span>
				<div id="cfe_label_{$arFormElement[fields]->cfe_id}" class="form_field_label" onclick="updateElement({$arFormElement[fields]->cfe_id})">
					{$arFormElement[fields]->cfe_label}
				</div>
			</div>
		{sectionelse}
			<p>Aucun élément disponible.</p>
		{/section}
		
	</div>
	<div class="clear"></div>
</form>

<div id="update_element_div" class="alternate"></div>