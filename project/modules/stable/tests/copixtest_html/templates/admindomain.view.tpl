<table class="CopixTable">
	<thead>
		<tr>
			<th> {i18n key='copixtest_html.admindomain.view.caption'} </th>
			<th> {i18n key='copixtest_html.admindomain.view.url'} </th>
			<th> {i18n key='copixtest_html.admindomain.view.actions'} </th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$ppo->arData item=value}
		<tr>
			<td> {$value->caption_domain|escape} </td>
			<td> {$value->url_domain|escape} </td>
			<td>
			<a href="{copixurl dest="admindomain|edit" id=$value->url_domain}"> {copixicon type="update"} </a>
			<a href="{copixurl dest="admindomain|delete" id=$value->url_domain}"> {copixicon type="delete"} </a> 
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<a href="{copixurl dest="admindomain|create"}"> {copixicon type="new"} Nouveau domaine </a>
<br /><br />
<input type="submit" onclick="location.href='{copixurl dest="admin||"}'" name="back" value="{i18n key='copixtest_html.admindomain.back'}" />