<form action="{copixurl dest="adminlevel|Save"}" method="POST">
<table class="CopixTableVertical">
	<tr>
		<td>{i18n key='test.level.form.caption'}</td>
		<td><input type="textfield" name="caption_level"
			value="{$ppo->arData->caption_level|escape}"></td>
	</tr>
	<tr>
		<td>{i18n key='test.level.form.contact'}</td>
		<td><input type="textfield" name="email" value="{$ppo->arData->email}">
		</td>
	</tr>
</table>
<input type="submit" style="width:100px" name="envoyer" value="{i18n key='test.level.form.submit'}"></form>
<a href="{copixurl dest="adminlevel|cancel"}">
<input type="button" style="width:100px" onclick=location.href='{copixurl dest="adminlevel|cancel"}' value="{i18n key='test.cancel'}">
 </a>