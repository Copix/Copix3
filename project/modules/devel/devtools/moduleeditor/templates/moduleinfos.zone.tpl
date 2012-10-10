<table class="CopixVerticalTable">
	<tr>
		<th>{i18n key="createmodule.th.path"}</th>
		<td>
			<select name="modulePath">
				{foreach from=$arModulePaths item=path}
					<option value="{$path}">{$path}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	
	<tr class="alternate">
		<th>{i18n key="createmodule.th.name"}</th>
		<td valign="middle">
			<input type="text" id="moduleName" name="moduleName" size="30" value="test" />
		</td>
	</tr>
	
	<tr>
		<th valign="top">{i18n key="createmodule.th.description"}</th>
		<td>
			<input type="text" name="moduleDescription" size="60" value="desc" />
			<input type="checkbox" name="moduleDescriptionCreateI18n" id="moduleDescriptionCreateI18n" checked="checked" /><label for="moduleDescriptionCreateI18n">{i18n key="global.createI18n"}</label>			
		</td>
	</tr>
	
	<tr class="alternate">
		<th valign="top">{i18n key="createmodule.th.longDescription"}</th>
		<td>
			<input type="text" name="moduleLongDescription" size="60" value="test" />
			<input type="checkbox" name="moduleLongDescriptionCreateI18n" id="moduleLongDescriptionCreateI18n" checked="checked" /><label for="moduleLongDescriptionCreateI18n">{i18n key="global.createI18n"}</label>
		</td>
	</tr>
	
	<tr>
		<th valign="top">{i18n key="createmodule.th.version"}</th>
		<td>
			<input type="text" name="moduleVersion" size="5" value="1.0.0" />
		</td>
	</tr>
	
	<tr class="alternate">
		<th valign="top">{i18n key="createmodule.th.moduleGroup"}</th>
		<td>
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="2">{select name=moduleGroupIdSelect id=moduleGroupIdSelect values=$arModuleGroups extra="onchange=javascript:onChangeGroup();"}</td>
				</tr>
				<tr>
					<td>{i18n key="createmodule.moduleGroup.id"}&nbsp;</td>
					<td><input type="text" name="moduleGroupId" id="moduleGroupId" size="15" /></td>
				</tr>
				<tr>
					<td>{i18n key="createmodule.moduleGroup.caption"}&nbsp;</td>
					<td>
						<input type="text" name="moduleGroupCaption" id="moduleGroupCaption" size="30" />
						<input type="checkbox" name="moduleGroupCaptionCreateI18n" id="moduleGroupCaptionCreateI18n" checked="checked" /><label for="moduleGroupCaptionCreateI18n">{i18n key="global.createI18n"}</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

{literal}
<script type="text/javascript">
function onChangeGroup () {
	if (document.getElementById ('moduleGroupIdSelect').value == '') {
		document.getElementById ('moduleGroupId').disabled = false;
		document.getElementById ('moduleGroupCaption').disabled = false;
		document.getElementById ('moduleGroupCaptionCreateI18n').disabled = false;
	} else {
		document.getElementById ('moduleGroupId').disabled = true;
		document.getElementById ('moduleGroupCaption').disabled = true;
		document.getElementById ('moduleGroupCaptionCreateI18n').disabled = true;
	}
}
onChangeGroup ();
</script>
{/literal}