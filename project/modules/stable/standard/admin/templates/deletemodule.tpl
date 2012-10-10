<div id="messageConfirm">
{i18n key="install.module.confirmDelete" module=$ppo->module}<br />
<input id="yes" type="button" value="{i18n key="copix:common.buttons.yes"}" /><input type="button" value="{i18n key="copix:common.buttons.no"}" onclick="javascript:document.location.href='{copixurl dest="admin|install|manageModules"}'" />
</div>
{copixzone id="divinstall" process='admin|deletemodulewithdep' moduleName=$ppo->module ajax=true}

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