{literal}
<script type="text/javascript">
function showDiv (div_id) {
	var div = document.getElementById (div_id);
	div.style.display = (div.style.display == '') ? 'none' : '';
}

function getSyncLang () {
	var selectSync = document.getElementById ('synchronizeLanguages');
	var toReturn = '';
	
	for (boucle = 0; boucle < selectSync.options.length; boucle++) {
		toReturn += selectSync.options[boucle].value + ',';
	}
	toReturn = toReturn.substring (0, toReturn.length - 1);
	
	return toReturn;
}

function showDifferencesLanguage () {
	document.location = '{/literal}{copixurl dest="synchronize|showDifferences"}{literal}?baseLang=' + document.getElementById ('syncLanguage').value + '&syncLang=' + getSyncLang ();
}

function moveOptions (selectFromId, selectToId) {
	var selectFrom = document.getElementById (selectFromId);
	var selectTo = document.getElementById (selectToId);
	var optionsToDelete = new Array ();

	for (boucle = 0; boucle < selectFrom.options.length; boucle++) {
  		if (selectFrom.options[boucle].selected) {
    		selectTo.options[selectTo.options.length] = new Option(selectFrom.options[boucle].text, selectFrom.options[boucle].value);
    		optionsToDelete[optionsToDelete.length] = boucle;
    	}
    }

   	deleteOptions (selectFrom, optionsToDelete);
   	enableSyncButtons ();
}

function deleteOptions (select, options) {
	for (boucle = 0; boucle < options.length; boucle++) {
		var optionIndex = options[boucle] - boucle;
		for (boucle2 = optionIndex; boucle2 < Number (select.options.length - 1); boucle2++) {
  			select.options[boucle2] = new Option ((select.options[Number (boucle2 + 1)].text), (select.options[Number (boucle2 + 1)].value));
		}
		select.options.length--;
	}
}

function addLanguage () {
	select = document.getElementById ('synchronizeLanguages');
	lang = document.getElementById ('addLang').value.toLowerCase ();
	country = document.getElementById ('addCountry').value.toUpperCase ();
	langCountry = lang;
	if (country != '') {
		langCountry += '_' + country;
	}
	
	if (langCountry.length != 2 && langCountry.length != 5) {
		alert ('{/literal}{i18n key="synchronize.error.addLangCountryInvalid" noEscape="1"}{literal}');
	} else {
		document.getElementById ('addLang').value = '';
		document.getElementById ('addCountry').value = '';
		select.options[select.options.length] = new Option ('[' + langCountry + '] {/literal}{i18n key="global.other.unknowLang"}{literal} (0 / {/literal}{$ppo->nbrProperiesFiles}{literal} )', langCountry);
		enableSyncButtons ();
	}
}

function enableSyncButtons () {
	disabled = (document.getElementById ('synchronizeLanguages').options.length == 0);
	//document.getElementById ('buttonSyncGlobale').disabled = disabled;
	document.getElementById ('buttonSyncLanguage').disabled = disabled;
}

function confirmDeleteLang () {
	lang = document.getElementById ('availableLanguagesToDelete').value;
	document.location = '{/literal}{copixurl dest="synchronize|delete"}{literal}?lang=' + lang;
}
</script>
{/literal}

<h2>{i18n key="synchronize.title.deleteLang"}</h2>
<select id="availableLanguagesToDelete">
	{foreach from=$ppo->arLanguages item=item key=key}
	{if $key != 'default_default'}
	<option value="{$key}">[{$key}] {$item.langName} ({$item.nbr} / {$ppo->nbrProperiesFiles})</option>
	{/if}
	{/foreach}
</select>
<input type="button" value="{i18n key="synchronize.input.showFilesToDelete"}" onclick="javascript: confirmDeleteLang ();" />

<h2>{i18n key="synchronize.title.langToSync"}</h2>

<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<th align="center">{i18n key="global.th.availableLanguages"}</th>
		<th></th>
		<th align="center">{i18n key="global.th.synchronizeLanguages"}</th>
	</tr>
	<tr>
		<td align="center">
			<select multiple size="5" id="availableLanguages">
				{foreach from=$ppo->arLanguages item=item key=key}
				<option value="{$key}">[{$key}] {$item.langName} ({$item.nbr} / {$ppo->nbrProperiesFiles})</option>
				{/foreach}
			</select>
		</td>
		<td>
			&nbsp;<input type="button" value="  >>  " onclick="javascript:moveOptions ('availableLanguages', 'synchronizeLanguages');" />
			<br /><br />
			&nbsp;<input type="button" value="  <<  " onclick="javascript:moveOptions ('synchronizeLanguages', 'availableLanguages');" />
		</td>
		<td align="center">
			<select multiple size="5" id="synchronizeLanguages" name="synchronizeLanguages"></select>
		</td>
	</tr>
</table> 

<br />
<b>{i18n key="synchronize.addLanguage"}</b>
<br />
{i18n key="synchronize.lang"} <input type="text" id="addLang" size="3" maxlength="2" />
{i18n key="synchronize.country"} <input type="text" id="addCountry" size="3" maxlength="2" />
<input type="button" value="{i18n key="global.other.add"}" onclick="javascript: addLanguage ();" />
<br />
{i18n key="synchronize.addThisToIso"}

<!-- mode de synchronisation à faire
<h2>{i18n key="synchronize.title.globalSynchronization"}</h2>

{i18n key="synchronize.global.presentation"}
<br /><br />
<a href="#" onclick="javascript: showDiv ('exampleGlobal')">{i18n key="synchronize.example"}</a>

<div id="exampleGlobal" style="display: none">
<br />
<b>{i18n key="synchronize.beforeSync"}</b>
<br /><br />
{i18n key="synchronize.global.admin_fr"}
<ul>
	<li>{i18n key="synchronize.global.admin_fr_ligne1"}</li>
	<li>{i18n key="synchronize.global.admin_fr_ligne2"}</li>
</ul>
{i18n key="synchronize.global.admin_en"}
<ul>
	<li>{i18n key="synchronize.global.admin_en_ligne1"}</li>
	<li>{i18n key="synchronize.global.admin_en_title_page"}</li>
</ul>
<b>{i18n key="synchronize.afterSync"}</b>
<br /><br />
{i18n key="synchronize.global.admin_fr"}
<ul>
	<li>{i18n key="synchronize.global.result_fr_ligne1"}</li>
	<li>{i18n key="synchronize.global.result_fr_ligne2"}</li>
	<li>{i18n key="synchronize.global.result_fr_title_page"}</li>
</ul>
{i18n key="synchronize.global.admin_en"}
<ul>
	<li>{i18n key="synchronize.global.result_en_ligne1"}</li>
	<li>{i18n key="synchronize.global.result_en_ligne2"}</li>
	<li>{i18n key="synchronize.global.result_en_title_page"}</li>
</ul>
</div>

<center><input type="button" id="buttonSyncGlobale" disabled value="{i18n key="synchronize.input.showDifferences"}" onclick="javascript: document.location='{copixurl dest="synchronize|showDifferences" mode=global}'" /></center>
-->

<h2>{i18n key="synchronize.title.languageSynchronization"}</h2>

{i18n key="synchronize.language.presentation"}
<br />
{i18n key="synchronize.language.selectLanguage"}
<select name="syncLanguage" id="syncLanguage">
	{foreach from=$ppo->arLanguages item=item key=key}
	<option value="{$key}">[{$key}] {$item.langName} ({$item.nbr} / {$ppo->nbrProperiesFiles})</option>
	{/foreach}
</select>

<br /><br />
<a href="#" onclick="javascript: showDiv ('exampleLanguage')">{i18n key="synchronize.example"}</a>

<div id="exampleLanguage" style="display: none">
<br />
<b>{i18n key="synchronize.beforeSync"}</b>
<br /><br />
{i18n key="synchronize.language.admin_fr"} {i18n key="synchronize.language.syncWithThisLanguage"}
<ul>
	<li>{i18n key="synchronize.language.admin_fr_ligne1"}</li>
	<li>{i18n key="synchronize.language.admin_fr_ligne2"}</li>
</ul>
{i18n key="synchronize.language.admin_en"}
<ul>
	<li>{i18n key="synchronize.language.admin_en_ligne1"}</li>
	<li>{i18n key="synchronize.language.admin_en_title_page"}</li>
</ul>
<b>{i18n key="synchronize.afterSync"}</b>
<br /><br />
{i18n key="synchronize.language.admin_fr"}
<ul>
	<li>{i18n key="synchronize.language.result_fr_ligne1"}</li>
	<li>{i18n key="synchronize.language.result_fr_ligne2"}</li>
</ul>
{i18n key="synchronize.language.admin_en"}
<ul>
	<li>{i18n key="synchronize.language.result_en_ligne1"}</li>
	<li>{i18n key="synchronize.language.result_en_ligne2"}</li>
</ul>
</div>

<center><input type="button" id="buttonSyncLanguage" disabled value="{i18n key="synchronize.input.showDifferences"}" onclick="javascript: showDifferencesLanguage ()" /></center>

<br /><br />
<input type="button" value="{i18n key="synchronize.input.back"}" onclick="javascript: document.location='{copixurl dest="admin||"}'" />