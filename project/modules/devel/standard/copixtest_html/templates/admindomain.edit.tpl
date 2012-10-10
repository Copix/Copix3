<form action="{copixurl dest="admindomain|Save"}" name="form" method="POST">
<table class="CopixVerticalTable">
		<tr>
			<th> {i18n key='copixtest_html.admindomain.edit.caption'} </th>
				<td><input type="text" style="width:400px" name="caption_domain" value="{$ppo->edit->caption_domain}"></td>
		</tr>
		<tr>
			<th> {i18n key='copixtest_html.admindomain.edit.url'} </th>
				<td><input type="text" style="width:400px" name="url_domain"  value="{$ppo->edit->url_domain}"></td>
		</tr>
</table>
<br /><br />
<input type="submit" name="send" value="{i18n key='copixtest_html.admindomain.edit.submit'}">
</form>
<br />
	<input type="submit" onclick="location.href='{copixurl dest="admindomain|default"}'" name="back" value="{i18n key='copixtest_html.admindomain.back'}" />
