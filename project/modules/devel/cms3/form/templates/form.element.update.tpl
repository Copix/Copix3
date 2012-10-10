<script type="text/javascript">

function submitUpdateElement() 
{ldelim}
	ajaxOn();
	var ajaxUpdateElementRequest = new Request.HTML (
								 {ldelim}
								 'url' :'{copixurl dest="form|adminajax|submitUpdateElement"}', 
								 'method': 'post',
								 'evalScripts':true,
								 'onSuccess':ajaxOff
								 {rdelim}
	);
	
	var requestUpdateElementVar = {ldelim}
		'cfe_id' : $('cfe_id').value,
		'cfe_label': $('cfe_label').value,
		'cfe_aide' : $('cfe_aide').value,
		'cfe_type' : $('cfe_type') ? $('cfe_type').value : null,
		{if $ppo->record->arValues == null}
		'cfe_default' : $('cfe_default').value,
		'cfe_default_data' : $('cfe_default_data').value
		{else}
		'cfe_orientation' : $('cfe_orientation').value,
		'cfe_columns' : $('cfe_columns').value
		{/if}
	{rdelim};

	ajaxUpdateElementRequest.post(requestUpdateElementVar);
{rdelim}

</script>


{error message=$ppo->errors}

<input type='hidden' id='cfe_id' name='cfe_id' value='{$ppo->record->cfe_id}' />

<table id="update_element_div_content" style="width: 100%;">
	<tr>
		<th style="width:120px;">Champ de type :</th>
		<td>
			<span id="th_type">{$ppo->record->libelle_type}</span>
			<img id="td_button_type_update" class="img_button" onclick="updateFieldType('{$ppo->record->cfe_type}')" src="{copixresource path='img/tools/update.png'}"/>
			<img id="td_button_type_save" onclick="submitUpdateElement()" class="img_button" style="display:none;" src="{copixresource path='img/tools/save.png'}"/>
			<img id="td_button_type_undo" class="img_button" style="display:none;" src="{copixresource path='img/tools/undo.png'}"/>
		</td>		
	</tr>
	<tr>
		<th>Libellé * :</th>
		<td><input id='cfe_label' name='cfe_label' type='text' value="{$ppo->record->cfe_label}" style="width:99%"/></td>
	</tr>
	<tr>
		<th>Aide : </th>
		<td><input id='cfe_aide' name='cfe_aide' type='text' value="{$ppo->record->cfe_aide}" style="width:99%"/></td>
	</tr>
	
	
	{if $ppo->record->arValues}
		<tr>
			<th>Orientation :</th>
			<td>
				{select id="cfe_orientation" name="cfe_orientation" values="0=>Horizontal;1=>Vertical"|toarray selected=$ppo->record->cfe_orientation 
					emptyShow=false extra='style="width:99%;"'}
			</td>
		</tr>
		{if $ppo->record->cfe_type == 'checkbox'}
		<tr>
			<th>Colonnes :</th>
			<td>
				<input id='cfe_columns' name='cfe_columns' type='text' value="{$ppo->record->cfe_columns}" style="width:25px; text-align: right;"/>
				<em>Nombres de colonnes pour l'affichage, 0 pour affichage automatique</em>		
			</td>
		</tr>
		{/if}
		<tr><th>Liste de valeurs disponibles :</th>
			<td>
				<table id="element_value_list">
				{foreach from=$ppo->record->arValues item=element_value}
					<tr id="element_value_line_{$element_value->cfev_id}">
						{if $ppo->record->cfe_type == 'select' || $ppo->record->cfe_type == 'checkbox'}
						<td class="td_button">
							<a class="smoothbox" href="{copixurl dest='form|admin|getValueOption id=$element_value->cfev_id TB_iframe=true height=600 width=1024}">
								<img src="{copixresource path='img/tools/plus.png'}"/>
							</a>
						</td>
						{/if}
						<td id="element_value_cell_{$element_value->cfev_id}">
							{$element_value->cfev_value}
						</td>
						<td id="element_value_edit_{$element_value->cfev_id}" style="width:15px;cursor:pointer;" onclick="switchUpdateElementValue({$element_value->cfev_id})">
							<img src="{copixresource path='img/tools/update.png'}"/>
						</td>
						<td class="td_button" onclick="if (window.confirm('Voulez-vous vraiment supprimer l\'élément :  {$element_value->cfev_value} ?')){ldelim}deleteElementValue({$element_value->cfev_id});{rdelim}">
							<img src="{copixresource path='img/tools/delete.png'}"/>
						</td>
						<td id="element_value_save_{$element_value->cfev_id}" class="td_button" style="display:none;" onclick="submitUpdateElementValue({$element_value->cfev_id})">
							<img src="{copixresource path='img/tools/save.png'}"/>
						</td>
						<td id="element_value_undo_{$element_value->cfev_id}" class="td_button" style="display:none;" onclick="switchDisplayElementValue({$element_value->cfev_id}, '{$element_value->cfev_value}')">
							<img src="{copixresource path='img/tools/undo.png'}"/>
						</td>
					</tr>
				{/foreach}
				</table>
			</td>
		</tr>
		<tr>
			<td>Nouvelle valeur :</td>
			<td>
				<input id="cfev_value" type="text" style="width:95%;"/>
			</td>
			<td onclick="submitElementValue();" style="cursor:pointer;">
				<img src="{copixresource path='img/tools/add.png'}" />
			</td>
		</tr>
	{else}
	<tr>
		<th>Valeur par défaut :</th>
		<td><input id='cfe_default' name='cfe_default' type='text' value="{$ppo->record->cfe_default}" style="width:99%"/></td>
	</tr>
	<tr>
		<th>Préremplir avec :</th>
		<td>
			{assign var='selected' value=$ppo->record->cfe_default_data}
			{assign var='values' value=$ppo->arUserInfos}
			{select name="cfe_default_data" values=$values selected=$selected extra="style='width:100%;'"}
		</td>
	</tr>
	{/if}
	
	<tr>
		<td colspan="2" align="right">
			<img id="save_btn" class="img_button" onclick="submitUpdateElement()" src="{copixresource path='img/tools/save.png'}"/>
			<img class="img_button" id="delete_btn" onclick="if (window.confirm('Voulez-vous vraiment supprimer l\'élément :  {$ppo->record->cfe_label} ?')){ldelim}deleteElement('{$ppo->record->cfe_id}'){rdelim}" src="{copixresource path='img/tools/delete.png'}"/>
			<img id="cancel_btn" class="img_button" src="{copixresource path='img/tools/undo.png'}" onclick="myUpdateElementSlide.slideOut();"/>
		</td>
	</tr>
</table>
<script type="text/javascript">
$('td_button_type_undo').addEvent('click', function(){ldelim}
	$('td_button_type_undo').setStyle ('display', 'none');
	$('td_button_type_save').setStyle ('display', 'none');
	$('td_button_type_update').setStyle ('display', '');
	$('th_type').innerHTML = '{$ppo->record->libelle_type}';
{rdelim});
</script>