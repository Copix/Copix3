{mootools}
{literal}
<script type="text/javascript">
function showDiv (div, show) {
	if (show) {
		document.getElementById (div).style.display = '';
	} else {
		document.getElementById (div).style.display = 'none';
	}
}

function openHelp (help, width, height) {
	var url = '{/literal}{copixurl dest="moduleeditor|help"}{literal}?help=' + help;
	var left = (screen.width / 2) - (width / 2);
	var top  = (screen.height / 2) - (height / 2);
	window.open(url, 'help', 'left=' + left + ', top=' + top + ', height=' + height + ', width=' + width + ', toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no');
}
</script>
{/literal}

{ajax_submitform form=formCreateModule submit=formSubmit divErrors=formErrors urlVerif="createmodule|verifCreate" urlSubmit="createmodule|create"}

<div id="formErrors"></div>

<form action="{copixurl dest="createmodule|create"}" method="post" id="formCreateModule">

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td>
			<h2>{i18n key="createmodule.title.moduleInfos"}</h2>
		</td>
		<td align="right">
			<img src="{copixresource path="img/tools/help.png"}" style="cursor:pointer" onclick="javascript: openHelp ('actiongroup', 400, 300);" />
		</td>
	</tr>
</table>
<div id="module_infos">{copixzone process="moduleinfos"}</div>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td>
			<h2>
				{i18n key="createmodule.title.createActiongroup"}
				<input type="radio" name="actiongroup" value="1" id="actiongroup_1" checked="checked" onclick="javascript:showDiv ('create_actiongroup', true)" /><label for="actiongroup_1">{i18n key="global.yes"}</label>
				<input type="radio" name="actiongroup" value="0" id="actiongroup_0" onclick="javascript:showDiv ('create_actiongroup', false)" /><label for="actiongroup_0">{i18n key="global.no"}</label>
			</h2>
		</td>
		<td align="right">
			<img src="{copixresource path="img/tools/help.png"}" style="cursor:pointer" onclick="javascript: openHelp ('actiongroup', 550, 400);" />
		</td>
	</tr>
</table>

<div id="create_actiongroup">{copixzone process="actiongroup"}</div>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td>
			<h2>
				{i18n key="createmodule.title.createTemplates"}
				<input type="radio" name="templates" value="1" id="templates_1" checked="checked" onclick="javascript:showDiv ('create_templates', true)" /><label for="templates_1">{i18n key="global.yes"}</label>
				<input type="radio" name="templates" value="0" id="templates_0" onclick="javascript:showDiv ('create_templates', false)" /><label for="templates_0">{i18n key="global.no"}</label>
			</h2>
		</td>
		<td align="right">
			<img src="{copixresource path="img/tools/help.png"}" style="cursor:pointer" onclick="javascript: openHelp ('actiongroup', 550, 400);" />
		</td>
	</tr>
</table>
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
<center><input type="button" id="formSubmit" value="{i18n key="createmodule.submit"}" /></center>
</form>

<br />
<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />