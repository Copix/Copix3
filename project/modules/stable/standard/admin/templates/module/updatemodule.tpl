<div id="messageConfirm">
{i18n key="install.module.confirmUpdate" module=$ppo->module}<br />
<input id="yes" type="button" value="{i18n key="copix:common.buttons.yes"}" /><input type="button" value="{i18n key="copix:common.buttons.no"}" onclick="javascript:document.location.href='{copixurl dest="admin|install|manageModules"}'" />
</div>
{copixzone id="divinstall" process='admin|updatemodule' moduleName=$ppo->module ajax=true}
<input id="retour" type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location.href='{copixurl dest='admin|install|manageModules'}'" />
{copixhtmlheader kind="jsCode"}
{literal}
window.addEvent('domready', function () {
	$('retour').setStyle('display','none');
	$('yes').addEvent ('click', function () {
		$('messageConfirm').setStyle('display','none');
	    $('divinstall').fireEvent('display');
	    $('divinstall').setStyle('display','');
	    $('retour').setStyle('display','');
	});
});
{/literal}
{/copixhtmlheader}