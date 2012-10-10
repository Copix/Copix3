{if $messageConfirm==true}
<div id="messageConfirm_{$id}">
{i18n key="install.module.confirmInstall"}<br />
{ulli values=$arModuleToInstall}

{foreach from=$arModuleInfos key=moduleName item=description}
<div id="moduleInfo_{$module}_{$id}" >
    <h2>{$description}</h2>
</div>
<br />
{/foreach}

<input id="yes_{$id}" type="button" value="{i18n key="Copix:common.buttons.yes"}" /><input type="button" value="{i18n key="Copix:common.buttons.no"}" onclick="javascript:document.location.href='{copixurl dest="admin|install|manageModules"}'" />
</div>
{copixhtmlheader kind="jsDomReadyCode"}
$('yes_{$id}').addEvent ('click', function () {ldelim}
	$('{$id}').fireEvent('display');
	$('messageConfirm_{$id}').setStyle('display','none');
{rdelim});
{/copixhtmlheader}
{copixzone id=$id process='admin|installmodule' ajax=true zoneParams_url=$url}
{else}
{copixzone id=$id process='admin|installmodule' ajax=true zoneParams_url=$url auto=true}
{/if}
<input id="back_{$id}" style="display:none" type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location.href='{$url}'" />
