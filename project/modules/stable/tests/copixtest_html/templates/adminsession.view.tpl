<table class="CopixTable">
	<thead>
		<tr>
			<th> {i18n key='copixtest_html.adminsession.captionsession'} </th>
			<th> {i18n key='copixtest_html.adminsession.caption_login'} </th>
			<th> {i18n key='copixtest_html.adminsession.caption_logout'} </th>
			<th> {i18n key='copixtest_html.adminsession.actions'} </th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$ppo->arData key=cle item=element}
		<tr>
			<td> {$element->caption_session} </td>
			<td> {$element->login_session} </td>
			<td> {$element->logout_session} </td>
			<td>
				<a href="{copixurl dest="adminsession|edit" id=$element->id_session}">
			 	{copixicon type='update'}
			 	</a>
				<a href="{copixurl dest="adminsession|delete" id=$element->id_session}"> 
				{copixicon type='delete'}
				</a>
		   </td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br /> <br />
{copixicon type='new'} <a href="{copixurl dest='adminsession|create'}"> {i18n key='copixtest_html.adminsession.create'} </a>
<br />
<input type="submit" onclick="location.href='{copixurl dest="admin||"}'" name="back" value="{i18n key='copixtest_html.admindomain.back'}" />