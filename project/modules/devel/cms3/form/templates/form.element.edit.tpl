<script>
function submitNewElement() 
{ldelim}
	
	var onSubmitNewElement = function() {ldelim}$('form_fields_list').innerHTML += this.response.text;{rdelim}
	
	var ajaxNewElementRequest = new Request.HTML ( 
								 {ldelim}
								 'url' : '{copixurl dest="adminajax|submitNewElement"}',
								 'method': 'post',
								 'evalScripts':true,
								 'onSuccess' : onSubmitNewElement
								 {rdelim}
	);
	
	var requestNewElementVar = {ldelim}
		'cfe_label': $('cfe_label').value,
		'cfe_type' : $('cfe_type').value
	{rdelim};


	ajaxNewElementRequest.post(requestNewElementVar);
{rdelim}
</script>

{error message=$ppo->errors}

<table class="CopixTable">
	<tr>
		<th>Libellé * :</th>
		<td><input id='cfe_label' name='cfe_label' type='text' /></td>
		<th>Type * :</th>
		<td>{select id='cfe_type' name='cfe_type' values=$ppo->arTypeElement emptyShow=false}</td>
		<td style="width:20px;cursor:pointer;" onclick="submitNewElement();">
			<img id="save_btn" src="{copixresource path='img/tools/save.png'}"/>
		</td>
		<td style="width:20px;cursor:pointer;" onclick="myNewElementSlide.slideOut();">
			<img id="cancel_btn" src="{copixresource path='img/tools/undo.png'}"/>
		</td>
	</tr>
</table>
