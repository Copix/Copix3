<div id="messageConfirm">
{i18n key="install.module.confirmDelete"}<br />
{ulli values=$arModuleToDelete}
<input id="yes" type="button" value="{i18n key="Copix:common.buttons.yes"}" /><input type="button" value="{i18n key="Copix:common.buttons.no"}" onclick="javascript:document.location.href='{copixurl dest="admin|install|manageModules"}'" />
</div>

{copixzone id=$id process='admin|deletemodule' ajax=true}
<input id="back" type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location.href='{copixurl dest='admin|install|manageModules'}'" />

{copixhtmlheader kind="jsCode"}
{literal}
window.addEvent('domready', function () {
	$('back').setStyle('display','none');
	$('yes').addEvent ('click', function () {
		$('messageConfirm').setStyle('display','none');
	    $('{/literal}{$id}{literal}').fireEvent('display');
	    $('{/literal}{$id}{literal}').setStyle('display','');
	});
});
{/literal}
{/copixhtmlheader}