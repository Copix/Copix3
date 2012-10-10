{mootools}
{literal}<script type="text/javascript">
function showAddressesList (pShow) {
	display = (pShow) ? '' : 'none';
	$ ('addressesList1').style.display = display;
	$ ('addressesList2').style.display = display;
}
</script>{/literal}

{error message=$ppo->errors}

<h2{if count ($ppo->errors) == 0} class="first"{/if}>{i18n key="proxy.edit.title.generalInfos"}</h2>
<table class="CopixVerticalTable">
	<form action="{copixurl dest="proxy|edit"}" method="POST">
	<tr>
		<th width="220">{i18n key="proxy.edit.enabled"}</th>
		<td>
			<input type="radio" name="enabled" value="1" id="enabled_1" checked="checked"
			/><label for="enabled_1">{i18n key="proxy.edit.enabledYes"}</label>
			<input type="radio" name="enabled" value="0" id="enabled_0"
			/><label for="enabled_0">{i18n key="proxy.edit.enabledNo"}</label>
		</td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="proxy.edit.id"}</th>
		<td><input type="text" name="id" value="proxy_id" /></td>
	</tr>
</table>

<h2>{i18n key="proxy.edit.title.connexion"}</h2>
<table class="CopixVerticalTable">
	<tr>
		<th width="220">{i18n key="proxy.edit.host"}</th>
		<td><input type="text" name="host" size="35" value="proxy_host" /></td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="proxy.edit.port"}</th>
		<td><input type="text" name="port" size="5" value="1821" /></td>
	</tr>
	<tr>
		<th>{i18n key="proxy.edit.user"}</th>
		<td><input type="text" name="user" /></td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="proxy.edit.password"}</th>
		<td><input type="password" name="password" /></td>
	</tr>
</table>

<h2>{i18n key="proxy.edit.title.hosts"}</h2>
<table class="CopixVerticalTable">
	<tr>
		<th width="220">{i18n key="proxy.edit.addresses"}</th>
		<td>
			<input type="radio" name="addresses" id="addresses_all" checked"checked" onclick="javascript: showAddressesList (false);"
			/><label for="addresses_all">{i18n key="proxy.edit.allAddresses"}</label>
			<input type="radio" name="addresses" id="addresses_list" onclick="javascript: showAddressesList (true);"
			/><label for="addresses_list">{i18n key="proxy.edit.listAddresses"}</label>
		</td>
	</tr>
	<tr class="alternate" id="addressesList1" style="display: none">
		<th>{i18n key="proxy.edit.addressesWith"}</th>
		<td>
			{i18n key="proxy.edit.oneAddressePerLine"}<br />
			<textarea name="forHosts" cols="25"></textarea>
		</td>
	</tr>
	<tr id="addressesList2" style="display: none">
		<th>{i18n key="proxy.edit.addressesWithout"}</th>
		<td>
			{i18n key="proxy.edit.oneAddressePerLine"}<br />
			<textarea name="notForHosts" cols="25"></textarea>
		</td>
	</tr>
</table>

<br />
<center>
<input type="submit" value="{i18n key="proxy.edit.submitAdd"}" />
</center>

</form>
{back url="admin|proxy|"}