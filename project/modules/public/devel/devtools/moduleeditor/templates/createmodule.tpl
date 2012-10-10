{literal}
<script type="text/javascript">
function showDiv (div, show) {
	if (show) {
		document.getElementById (div).style.display = '';
	} else {
		document.getElementById (div).style.display = 'none';
	}
}
</script>
{/literal}

<form action="{copixurl dest="createmodule|create"}" method="post">

<h2>{i18n key="createmodule.title.moduleInfos"}</h2>
<div id="module_infos">{copixzone process="infos"}</div>

<h2>
{i18n key="createmodule.title.createActiongroup"}
<input type="radio" name="actiongroup" value="1" id="actiongroup_1" checked="checked" onclick="javascript:showDiv ('create_actiongroup', true)" /><label for="actiongroup_1">{i18n key="global.yes"}</label>
<input type="radio" name="actiongroup" value="0" id="actiongroup_0" onclick="javascript:showDiv ('create_actiongroup', false)" /><label for="actiongroup_0">{i18n key="global.no"}</label>
</h2>
<div id="create_actiongroup">{copixzone process="actiongroup"}</div>

<h2>
{i18n key="createmodule.title.createTemplates"}
<input type="radio" name="templates" value="1" id="templates_1" checked="checked" onclick="javascript:showDiv ('create_templates', true)" /><label for="templates_1">{i18n key="global.yes"}</label>
<input type="radio" name="templates" value="0" id="templates_0" onclick="javascript:showDiv ('create_templates', false)" /><label for="templates_0">{i18n key="global.no"}</label>
</h2>
<div id="create_templates">{copixzone process="templates"}</div>



<!--
	<tr>
		<th valign="top">{i18n key="createmodule.title.createLinks"}</th>
		<td valign="top">
			<input type="radio" name="links" value="1" id="links_1" checked="checked" onclick="javascript:showDiv ('create_links', true)" /><label for="links_1">{i18n key="global.yes"}</label>
			<input type="radio" name="links" value="0" id="links_0" onclick="javascript:showDiv ('create_links', false)" /><label for="links_0">{i18n key="global.no"}</label>
			<div id="create_links">
			<br />
			{i18n key="global.name"} <input type="text" name="linkName1" size="30" />
			{i18n key="global.link"} <input type="text" name="linkUrl1" size="20" />
			{i18n key="global.credential"} <input type="text" name="linkCredential1" size="15" />
			<br />
			{i18n key="global.name"} <input type="text" name="linkName1" size="30" />
			{i18n key="global.link"} <input type="text" name="linkUrl2" size="30" />
			{i18n key="global.credential"} <input type="text" name="linkCredential2" />
			<br />
			{i18n key="global.name"} <input type="text" name="linkName1" size="30" />
			{i18n key="global.link"} <input type="text" name="linkUrl3" size="30" />
			{i18n key="global.credential"} <input type="text" name="linkCredential3" />
			<br />
			{i18n key="global.name"} <input type="text" name="linkName1" size="30" />
			{i18n key="global.link"} <input type="text" name="linkUrl4" size="30" />
			{i18n key="global.credential"} <input type="text" name="linkCredential4" />
			</div>
		</td>
	</tr>
</table>
-->
<br />
<center><input type="submit" value="{i18n key="createmodule.submit"}" /></center>
</form>

<br />
<input type="button" value="{i18n key="copix:copix.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />