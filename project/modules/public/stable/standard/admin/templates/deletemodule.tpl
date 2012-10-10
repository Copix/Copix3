<div id="messageConfirm">
{i18n key="install.module.confirmDelete" module=$ppo->module}<br />
<input id="yes" type="button" value="{i18n key="Copix:common.buttons.yes"}" /><input type="button" value="{i18n key="Copix:common.buttons.no"}" onclick="javascript:document.location.href='{copixurl dest="admin|install|manageModules"}'" />
</div>
{ajax_divzone id="divinstall" zone='admin|deletemodulewithdep' moduleName=$ppo->module}

{copixhtmlheader kind="jsCode"}
{literal}
window.addEvent('domready', function () {
	$('yes').addEvent ('click', function () {
		$('messageConfirm').setStyle('display','none');
	    $('divinstall').fireEvent('display');
	    $('divinstall').setStyle('display','');
	});
});
{/literal}
{/copixhtmlheader}