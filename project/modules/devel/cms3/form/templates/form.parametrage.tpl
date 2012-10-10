<script>
/**
 * Ajout / Suppression d'un champs du formulaire
 */
function addRemoveRequiredContent(idElement)
{ldelim}
	ajaxOn();
	var action = 'remove';  
	if ($('cb_form_content_' + idElement).checked == true) {ldelim}
		action = 'add';
	{rdelim}
	var ajaxAddRemoveRequiredRequest = new Request.HTML ( 
								 {ldelim}
								 'url' : '{copixurl dest="adminajax|addRemoveRequiredContent"}',
								 'method': 'get',
								 'evalScripts':true,
								 'onSuccess':ajaxOff
								 {rdelim}
	);
	
	var requestAddRemoveRequiredVar = {ldelim}
		'editId' : $('editId').value,
		'cfc_id_element' : idElement,
		'do' : action
	{rdelim};
	
	ajaxAddRemoveRequiredRequest.post(requestAddRemoveRequiredVar);
{rdelim}

function moveUpElement(idElement) {ldelim}
	ajaxOn();
	var myHTMLRequest = new Request.HTML({ldelim}url:'{copixurl dest="adminajax|moveUpElement"}', 'update': 'form_params_div','onSuccess':ajaxOff{rdelim});
	myHTMLRequest.get({ldelim}'id_element':idElement, 'editId': '{'editId'|request}'{rdelim});
{rdelim}

function moveDownElement(idElement) {ldelim}
	ajaxOn();
	var myHTMLRequest = new Request.HTML({ldelim}url:'{copixurl dest="adminajax|moveDownElement"}', 'update': 'form_params_div', 'onSuccess':ajaxOff{rdelim});
	myHTMLRequest.get({ldelim}'id_element':idElement, 'editId': '{'editId'|request}'{rdelim});
{rdelim}

function setOrientationContent(idElement) {ldelim}
    ajaxOn();
    if($('cfc_orientation_'+idElement+'_0').checked) {ldelim} var orientation = 0; {rdelim} else {ldelim} var orientation = 1; {rdelim}

    var myRequest = new Request({ldelim}url:'{copixurl dest="adminajax|setOrientationContent"}', 'onSuccess':ajaxOff{rdelim});
    myRequest.get({ldelim}'idElement':idElement, 'editId': '{'editId'|request}', 'orientation' : orientation{rdelim});

{rdelim}
</script>
{section name=content loop=$arFormContent}
	{if $smarty.section.content.first}
    <table id="form_content" class="CopixTable">
    	<thead>
			<tr><th>Type</th><th>Libellé</th><th id="th_obligatoire">Obligatoire</th><th id="th_orientation">Orientation</th><th colspan="3" width="150" id="th_actions">Actions</th></tr>
		</thead>
		<tbody id="form_content_body">
  	{/if}
	
	<tr id="form_tr_{$arFormContent[content]->cfc_id_element}" id_element="{$arFormContent[content]->cfc_id_element}" class="{cycle values='alternate sortable, sortable'}">
		<td>{$arFormContent[content]->cfe_type_label}</td>
		<td style="min-width: 120px">{$arFormContent[content]->cfe_label}</td>
		<td style="text-align:center;">
			<input id="cb_form_content_{$arFormContent[content]->cfc_id_element}" name="cb_form_content_{$arFormContent[content]->cfc_id_element}"
                onclick="addRemoveRequiredContent({$arFormContent[content]->cfc_id_element});" type='checkbox' 
                {if $arFormContent[content]->cfc_required == 1} checked="checked"{/if} />
		</td>
		<td>
			{assign var='id_element' value=$arFormContent[content]->cfc_id_element}
			{assign var='selected' value=$arFormContent[content]->cfc_orientation}
			{radiobutton name="cfc_orientation_$id_element" values="0=>horizontal;1=>vertical"|toarray selected=$selected extra="onclick='setOrientationContent($id_element);'"}
		</td>
		<td align="center">
			{if !$smarty.section.content.last || true}
			<a href="{copixurl dest="adminajax|moveDownElement"}" onclick="moveDownElement({$arFormContent[content]->cfc_id_element});return false;" />
				<img class="image_button" src="{copixresource path='img/tools/movedown.png'}"/>
			</a>
			{/if}
		</td>
		<td align="center">
			{if !$smarty.section.content.first || true}
			<a href="{copixurl dest="adminajax|moveUpElement"}" onclick="moveUpElement({$arFormContent[content]->cfc_id_element});return false;" />
				<img class="image_button" src="{copixresource path='img/tools/moveup.png'}"/>
			</a>
			{/if}
		</td>
		<td align="center" class="draggable">
			<img src="{copixresource path='heading|img/actions/move_up_down.png'}"/>
		</td>
	</tr>
	{if $smarty.section.content.last}
		</tbody>
	</table>
	{/if}
{sectionelse}
	<table id="form_content" class="CopixTable">
    	<thead>
			<tr><th>Type</th><th>Libellé</th><th id="th_obligatoire">Obligatoire</th><th id="th_orientation">Orientation</th><th colspan="3" id="th_actions">Actions</th></tr>
		</thead>
		<tbody id="form_content_body"></tbody>
	</table>
{/section}

<script>
	/* Drap / Drop du contenu */
	initDragContent ("{copixurl dest='adminajax|updateContentOrder'}");
</script>
	