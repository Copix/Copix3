{if $messageConfirm==true}
<div id="messageConfirm">
{i18n key="install.module.confirmInstall"}<br />
{ulli values=$arModuleToInstall}
<input id="yes" type="button" value="{i18n key="Copix:common.buttons.yes"}" /><input type="button" value="{i18n key="Copix:common.buttons.no"}" onclick="javascript:document.location.href='{copixurl dest="admin|install|manageModules"}'" />
</div>
{/if}
{copixzone id=$id process='admin|installmodule' ajax=true}
<input id="back" type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location.href='{$url}'" />

{copixhtmlheader kind="jsCode"}
{literal}
window.addEvent('domready', function () {
	$('back').setStyle('display','none');
	{/literal}
	{if $messageConfirm==true}
	{literal}
	$('yes').addEvent ('click', function () {
		$('messageConfirm').setStyle('display','none');
	    $('{/literal}{$id}{literal}').fireEvent('display');
	    $('{/literal}{$id}{literal}').setStyle('display','');
	});
	{/literal}
	{else}
	{literal}
    $('{/literal}{$id}{literal}').fireEvent('display');
    $('{/literal}{$id}{literal}').setStyle('display','');
	{/literal}
	{/if}
	{literal}
});
{/literal}
{/copixhtmlheader}