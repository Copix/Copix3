<div id="messageConfirm">
{i18n key="install.module.confirmInstall"}<br />
{ulli values=$arModuleToInstall}
<input id="yes" type="button" value="{i18n key="Copix:common.buttons.yes"}" /><input type="button" value="{i18n key="Copix:common.buttons.no"}" onclick="javascript:document.location.href='{copixurl dest="admin|install|manageModules"}'" />
</div>

{ajax_divzone id=$id zone='admin|installmodule'}
<input id="back" type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location.href='{copixurl dest='admin|install|manageModules'}'" />

{copixhtmlheader kind="jsCode"}
{literal}
window.addEvent('domready', function () {
	$('back').setStyle('display','none');
	$('yes').addEvent ('click', function () {
		$('messageConfirm').setStyle('display','none');
	    $('{/literal}{$id}{literal}').fireEvent('display');
	    $('{/literal}{$id}{literal}').setStyle('display','');
	    $('back').setStyle('display','');
	});
});
{/literal}
{/copixhtmlheader}