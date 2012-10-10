{mootools}
{literal}
<script type="text/javascript">
var inputElements = new Array (
	'profile', 'host', 'port', 'user', 'password', 'dbname',
	'clientFlags_128', 'clientFlags_MYSQL_CLIENT_SSL', 'clientFlags_MYSQL_CLIENT_COMPRESS',
	'clientFlags_MYSQL_CLIENT_IGNORE_SPACE', 'clientFlags_MYSQL_CLIENT_INTERACTIVE',
	'btnTestConnection', 'btnAddProfile', 'installCopix'
);
var profilesExists = new Array ();
{/literal}
{assign var=index value=0}
{foreach from=$ppo->profilesExists item=profile}
	profilesExists[{$index}] = '{$profile}';
	{assign var=index value=$index+1}
{/foreach}
{literal}
var profileType = '';
var canAddProfile = false;

function onChangeDriver () {
	driver = getRadioValue ('driver');
	exProfileType = profileType;
	
	if (driver.indexOf ('mysql') >= 0) {
		profileType = 'mysql';
	} else if (driver.indexOf ('sqlite') >= 0) {
		profileType = 'sqlite';
	} else {
		profileType = 'other';
	}
	
	if (exProfileType != profileType) {
		if ($('dbtype_' + exProfileType) != undefined) {
			$('dbtype_' + exProfileType).style.display = 'none';
		}
		$('dbtype_' + profileType).style.display = '';
	}
	
	$('installCopix').style.display = 'none';
	$('divTestConnection').innerHTML = '{/literal}{i18n key="database2.connectionNotTested"}{literal}';
}

function getRadioValue (pName) {
	for (boucle = 0; boucle < $('formProfile').elements[pName].length; boucle++) {
		if ($('formProfile').elements[pName][boucle].checked) {
			return $('formProfile').elements[pName][boucle].value;
		}
	}
}

function validForm () {
	toReturn = true;
	
	if ($('profile').value == '') {
		$('profileError').innerHTML = '{/literal}{i18n key="database2.error.profileEmpty"}{literal}';
		$('profile').addClass ('inputError');
		toReturn = false;
	} else {
		$('profile').removeClass ('inputError');
		$('profileError').innerHTML = '';
		for (boucle = 0; boucle < profilesExists.length; boucle++) {
			if ($('profile').value == profilesExists[boucle]) {
				$('profileError').innerHTML = '{/literal}{i18n key="database2.error.profileExists"}{literal}';
				$('profile').addClass ('inputError');
				toReturn = false;
			}
		}
	}
	
	if (profileType == 'mysql') {
		if ($('mysql_dbname').value == '') {
			$('mysql_dbnameError').innerHTML = '{/literal}{i18n key="database2.error.dbnameEmpty"}{literal}';
			$('mysql_dbname').addClass ('inputError');
			toReturn = false;
		} else {
			$('mysql_dbname').removeClass ('inputError');
			$('mysql_dbnameError').innerHTML = '';
		}
	}
	
	return toReturn;
}

function disableFormElements (pDisabled) {
	$$('#formProfile input').each (
		function (element) {
			if ((element.id == 'btnAddProfile' && canAddProfile) || pDisabled || element.id != 'btnAddProfile') {
				element.disabled = pDisabled;
			}
		}
	);
}

function testConnection () {
	if (!validForm ()) {
		return ;
	}
	$('divTestConnection').innerHTML = '{/literal}{i18n key="database2.connectionIsInTest"}{literal}';
	$('installCopix').style.display = 'none';
	canAddProfile = false;

	if (profileType == 'mysql') {
		$('connectionString').value = 'dbname=' + $('mysql_dbname').value;
		if (getRadioValue ('driver') == 'pdo_mysql') {
			$('connectionString').value += ';host=' + $('mysql_host').value + ';port=' + $('mysql_port').value;
		} else {
			$('connectionString').value += ';host=' + $('mysql_host').value + ':' + $('mysql_port').value;
		}
		$('user').value = $('mysql_user').value;
		$('password').value = $('mysql_password').value;
		
	} else if (profileType == 'sqlite') {
		if (getRadioValue ('sqlite_type') == 'file') {
			$('connectionString').value = $('sqlite_file').value;
		} else {
			$('connectionString').value = ':memory:';
		}
		$('user').value = '';
		$('password').value = '';
		
	} else {
		$('connectionString').value = $('connectionStringOther').value;
	}
	
	new Request.HTML ({
		url: $('formProfile').action,
		update: 'divTestConnection',
		evalScripts: true,
		data: $('formProfile'),
		onComplete: function () {
			disableFormElements (false);
		},
		onFailure: function (response) {
			disableFormElements (false);
			$('divTestConnection').innerHTML = '<font color="red">' + response.responseText + '</font>';
		}
	}).send ();
	
	disableFormElements (true);
}

function addProfile () {
	$('formProfile').elements['add'].value = 1;
	$('formProfile').submit ();
}
</script>
{/literal}

<form id="formProfile" name="formProfile" method="post" action="{copixurl dest="admin|database2|testConnection"}" />
<input type="hidden" name="connectionString" id="connectionString" value="" />
<input type="hidden" name="user" id="user" value="" />
<input type="hidden" name="password" id="password" value="" />
<input type="hidden" name="add" id="add" value="0" />

<h2 class="first">{i18n key="database2.general"}</h2>
<table class="CopixVerticalTable">
	<tr>
		<th width="180">{i18n key="database2.defaultProfile"}</th>
		<td>
			<input type="radio" name="defaultProfile" value="1" id="defaultProfile_yes" {if $ppo->isDefaultProfile}checked="checked"{/if}
			/><label for="defaultProfile_yes">{i18n key="copix:common.buttons.yes"}</label>
			<input type="radio" name="defaultProfile" value="0" id="defaultProfile_no" {if !$ppo->isDefaultProfile}checked="checked"{/if}
			/><label for="defaultProfile_no">{i18n key="copix:common.buttons.no"}</label>
		</td>
	</tr>
	<tr class="alternate">
		<th width="180">{i18n key="database2.profil"}</th>
		<td><input class="inputText" type="text" name="profile" id="profile" value="testoui" /> <span class="spanError" id="profileError"></span></td>
	</tr>
	<tr>
		<th>{i18n key="database2.driver"}</th>
		<td>
			{assign var=index value=0}
			{foreach from=$ppo->dbtypes key=dbtype item=drivers}
				{if $index > 0}<br />{/if}
				{$dbtype} : 
				{foreach from=$drivers item=driverName}
					<input type="radio" name="driver" value="{$driverName}" id="radio_{$driverName}" onchange="javascript: onChangeDriver ();"
					{if $ppo->defaultDriver == $driverName}checked="checked"{/if}
					/><label for="radio_{$driverName}">{$driverName}</label>
				{/foreach}
				{assign var=index value=$index+1}
			{/foreach}
		</td>
	</tr>
</table>

<h2>{i18n key="database2.connectionInfos"}</h2>

<div id="dbtype_mysql" style="display:none">
<table class="CopixVerticalTable">
	<tr>
		<th width="180">{i18n key="database2.host"}</th>
		<td><input class="inputText" type="text" name="mysql_host" id="mysql_host" value="{$ppo->mysqlDefaultHost}" /></td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="database2.port"}</th>
		<td><input class="inputText" type="text" name="mysql_port" id="mysql_port" value="{$ppo->mysqlDefaultPort}" size="7" /></td>
	</tr>
	<tr>
		<th>{i18n key="database2.user"}</th>
		<td><input class="inputText" type="text" name="mysql_user" id="mysql_user" value="root" /></td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="database2.password"}</th>
		<td><input class="inputText" type="password" name="mysql_password" id="mysql_password" /></td>
	</tr>
	<tr>
		<th>{i18n key="database2.database"}</th>
		<td><input class="inputText" type="text" name="mysql_dbname" id="mysql_dbname" value="dbname" /> <span class="spanError" id="mysql_dbnameError"></span></td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="database2.clientFlags"}</th>
		<td>
			<input type="checkbox" name="mysql_clientFlags_128" id="mysql_clientFlags_128"
			/><label for="mysql_clientFlags_128">128 (LOAD DATA LOCAL)</label><br />
			<input type="checkbox" name="mysql_clientFlags_MYSQL_CLIENT_SSL" id="mysql_clientFlags_MYSQL_CLIENT_SSL"
			/><label for="mysql_clientFlags_MYSQL_CLIENT_SSL">MYSQL_CLIENT_SSL</label><br />
			<input type="checkbox" name="mysql_clientFlags_MYSQL_CLIENT_COMPRESS" id="mysql_clientFlags_MYSQL_CLIENT_COMPRESS"
			/><label for="mysql_clientFlags_MYSQL_CLIENT_COMPRESS">MYSQL_CLIENT_COMPRESS</label><br />
			<input type="checkbox" name="mysql_clientFlags_MYSQL_CLIENT_IGNORE_SPACE" id="mysql_clientFlags_MYSQL_CLIENT_IGNORE_SPACE"
			/><label for="mysql_clientFlags_MYSQL_CLIENT_IGNORE_SPACE">MYSQL_CLIENT_IGNORE_SPACE</label><br />
			<input type="checkbox" name="mysql_clientFlags_MYSQL_CLIENT_INTERACTIVE" id="mysql_clientFlags_MYSQL_CLIENT_INTERACTIVE"
			/><label for="mysql_clientFlags_MYSQL_CLIENT_INTERACTIVE">MYSQL_CLIENT_INTERACTIVE</label>
		</td>
	</tr>
</table>	
</div>

<div id="dbtype_sqlite" style="display:none">
<table class="CopixVerticalTable">
	<tr>
		<th width="180">{i18n key="database2.version"}</th>
		<td>SQLite 3</td>
	</tr>
	<tr class="alternate">
		<th width="180">{i18n key="database2.type"}</th>
		<td>
			<input type="radio" name="sqlite_type" id="sqlite_type_file" value="file" checked="checked"
			/><label for="sqlite_type_file">{i18n key="database2.typeFile"}</label>
			&nbsp;&nbsp;<input type="file" name="sqlite_file" id="sqlite_file" size="40" />
			<br />
			<input type="radio" name="sqlite_type" id="sqlite_type_memory" value="memory"
			/><label for="sqlite_type_memory">{i18n key="database2.typeMemory"}</label>
		</td>
	</tr>
</table>
</div>

<div id="dbtype_other" style="display:none">
<table class="CopixVerticalTable">
	<tr>
		<th width="180">{i18n key="database2.connectionString"}</th>
		<td><textarea id="other_connectionString" style="width: 99%" rows="5"></textarea></td>
	</tr>
</table>
</div>

<br />
<center>
<div id="divTestConnection">{i18n key="database2.connectionNotTested"}</div>
<br />
<input type="button" value="{i18n key="database2.testConnection"}" onclick="javascript: testConnection ();" id="btnTestConnection" />
<input type="button" value="{i18n key="database2.addThisProfile"}" id="btnAddProfile" disabled="true" onclick="javascript: addProfile ();" />
</center>
</form>

<div id="installCopix" style="display:none">
<h2>{i18n key="database2.installCopix"}</h2>
{i18n key="database2.installCopixText"}
<br />
{i18n key="database2.installCopixModules" param1=$ppo->defaultModules}
<br /><br />
<center>
<input type="button" value="{i18n key="database2.button.installCopix"}" id="installCopix" />
</center>
</div>

<br />
{back url="admin|database2|"}

<script type="text/javascript">
onChangeDriver ();
</script>