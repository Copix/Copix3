<table class="CopixTable" >
<tr>
	<th>Libellé</th>
	<th>Description</th>
	<th></th>
</tr>
{foreach from=$results key=id item=line}
	<tr {cycle values='class="alternate",'}>
	
	<td>{copixform_start form=$id dao='forms_playground' id=$line->id deleteUrl='form_playground||list'}{copixform_field field='caption' type='varchar'}</td>
	<td>{copixform_field field='description' type='varchar'}
	{copixform_field field='istestordie' type='hidden' value="test" valid='form_playground|validation::testordie'}</td>
	<td>
	{copixform_edit}<img src="{copixresource path='img/tools/update.png'}" />{/copixform_edit}{copixform_delete}<img src="{copixresource path='img/tools/delete.png'}" />{/copixform_delete}{copixform_end}
	</td>
	</tr>
{/foreach}
	<tr {cycle values='class="alternate",'}>
	<td>{copixform_start form='new' id=-1 dao='forms_playground'}{copixform_field field='caption' type='varchar'}</td>
	<td>{copixform_field field='description' type='varchar'}
	{copixform_field field='istestordie' type='hidden' value="test" valid='form_playground|validation::testordie'}{copixform_end}</td>
	</tr>
</table>
