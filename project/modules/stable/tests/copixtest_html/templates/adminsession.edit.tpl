{mootools}
<form name="form" action="{copixurl dest='adminsession|save'}" method="POST">
<input type="hidden" value="id" value="{$ppo->id}" />
<table class="CopixVerticalTable">
		<tr>
			<th> {i18n key='copixtest_html.edit.caption_session'} *</th>
			<td> <input type="text" style="width:600px" name="caption_session" value="{$ppo->caption_session}" />  </td>
		</tr>
		<tr>
			<th> {i18n key='copixtest_html.edit.login_session'} *</th>
			<td> {autocomplete name="login_session" extra="style="width:600px"" id="login_session" dao="copixtesthtml" field="url" view="id_test;url" value=$ppo->login_session}
			</td>
		</tr>
		<tr>
			<th> {i18n key='copixtest_html.edit.logout_session'} </th>
			<td> {autocomplete name="logout_session" extra="style="width:600px"" id="logout_session" dao="copixtesthtml" field="url" view="id_test;url" value=$ppo->logout_session}
			</td>
		</tr>
</table>
<br />
<div align="right">
	<input type="submit" name="confirm" value="Sauvegarder" />
</div>
</form>

<br />
<input type="submit" onclick="location.href='{copixurl dest="adminsession|default"}'" name="back" value="{i18n key='copixtest_html.admindomain.back'}" />