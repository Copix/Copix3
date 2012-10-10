
{if $idBloc}
    {assign var='suffixe' value='_update'}
{else}
    {assign var='suffixe' value='_new'}
{/if}

<form id="form{$suffixe}" class="form{$suffixe}">
    {if $idBloc}
        <input type="hidden" name="cbf_id" id="cbf_id" value="{$idBloc}" />
        <h2>Mise à jour d'un bloc</h2>
    {else}
        <h2>Ajout d'un bloc</h2>
    {/if}
    <div id="form_error{$suffixe}"></div>
    <table style="width:500px;">
        <tr>
            <th><label for="cfb_nom">Nom :</label></th>
            <td><input name="cfb_nom" type="text" value="{$bloc->cfb_nom}"/></td>
        </tr>
        <tr>
            <td><label for="cfb_description">Description :</label></td>
            <td><textarea name="cfb_description" rows=2>{$bloc->cfb_description}</textarea></td>
        </tr>
    </table>
    <div id="form_fields_list">
		<h3>Liste des champs disponibles</h3>
		{section name=fields loop=$arFormElement}
			<div id="form_field_line_{$arFormElement[fields]->cfe_id}" class="form_field">
				<span class="form_field_chk">
					<input id="cb_form_field_{$arFormElement[fields]->cfe_id}" name="form_field[]" 
						value="{$arFormElement[fields]->cfe_id}" type='checkbox'
						{if in_array($arFormElement[fields]->cfe_id, $arFormElementSelected)} checked="checked"{/if} />
				</span>
				<div id="cfe_label_{$arFormElement[fields]->cfe_id}" class="form_field_label">
					{$arFormElement[fields]->cfe_label}
				</div>
			</div>
		{sectionelse}
			<p>Aucun élément disponible.</p>
		{/section}
	</div>
    <div class="flor clear">
    	<img id="save_btn_link{$suffixe}" class="img_button" src="{copixresource path='img/tools/save.png'}"/>
		<img class="img_button" id="delete_btn_link{$suffixe}" src="{copixresource path='img/tools/delete.png'}"/>
		<img id="cancel_btn_link{$suffixe}" class="img_button" src="{copixresource path='img/tools/undo.png'}" />
    </div>
	<div class="clear"></div>
</form>



<script type="text/javascript">
    function submitBloc()
    {ldelim}
        var myHTMLRequest = new Request.HTML({ldelim}
            url:'{copixurl dest="adminajax|submitBloc"}',
            evalScripts: true,
            method: 'get'
        {rdelim});
        myHTMLRequest.send($('form{$suffixe}').toQueryString());
        return false;
    {rdelim}
    
    function deleteBloc()
    {ldelim}
    	if (window.confirm('Voulez-vous vraiment supprimer l\'élément :  {$ppo->record->cfe_label} ?')){ldelim}
	        var myHTMLRequest = new Request.HTML({ldelim}
	            url:'{copixurl dest="adminajax|deleteBloc"}',
	            evalScripts: true,
	            method: 'get'
	        {rdelim});
	        myHTMLRequest.post({ldelim}'id_bloc' : '{$idBloc}' {rdelim});
	        {if $idBloc}
	        	hideUpdateDiv();
	        {else}
	        	hideNewDiv();
	        {/if}
        {rdelim}
    {rdelim}

    $('save_btn_link{$suffixe}').addEvent('click', submitBloc);
{if $idBloc}
    $('cancel_btn_link{$suffixe}').addEvent('click', hideUpdateDiv);
{else}
    $('cancel_btn_link{$suffixe}').addEvent('click', hideNewDiv);
{/if}
    $('delete_btn_link{$suffixe}').addEvent('click', deleteBloc);
</script>

