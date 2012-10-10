<table class="CopixVerticalTable">
	<tr>
		<th>{i18n key="createmodule.th.path"}</th>
		<td>
			<select name="modulePath">
				{foreach from=$modulePaths item=path}
				<option value="{$path}">{$path}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	
	<tr class="alternate">
		<th>{i18n key="createmodule.th.name"}</th>
		<td><input type="text" name="moduleName" size="30" value="test" /></td>
	</tr>
	
	<tr>
		<th valign="top">{i18n key="createmodule.th.description"}</th>
		<td>
			<input type="text" name="moduleDescription" size="60" value="desc" />
			<br />
			<input type="checkbox" name="moduleDescriptionCreateI18n" id="moduleDescriptionCreateI18n" checked="checked" /><label for="moduleDescriptionCreateI18n">{i18n key="global.createI18n"}</label>
		</td>
	</tr>
	
	<tr class="alternate">
		<th valign="top">{i18n key="createmodule.th.longDescription"}</th>
		<td>
			<input type="text" name="moduleLongDescription" size="60" value="test" />
			<br />
			<input type="checkbox" name="moduleLongDescriptionCreateI18n" id="moduleLongDescriptionCreateI18n" checked="checked" /><label for="moduleLongDescriptionCreateI18n">{i18n key="global.createI18n"}</label>
		</td>
	</tr>
</table>