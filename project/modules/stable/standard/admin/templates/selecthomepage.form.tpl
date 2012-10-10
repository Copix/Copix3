{copixhtmlheader kind=jsCode} {literal} function openInputUrl (id){ 
	var inputElement; 
	if (inputElement = document.getElementById (id)){
		window.open("{/literal}{copixurl}{literal}"+inputElement.value);
	} 
}
{/literal}
{/copixhtmlheader}
<form name="selectHomePage" action="{copixurl dest=admin|install|setHomePage}" method="POST">
	<table class="CopixVerticalTable">
 		<tr>
  			<th><label for="urlinput">{i18n key="install.messages.homePage"}</label></th>
  			<td><input type="text" size="40" value="{$homepageUrl|escape:html}" name="urlinput" id="urlinput" /><a title="{i18n key=copix:common.buttons.test}" href="#" onclick="openInputUrl ('urlinput');return false;"><img src="{copixresource path="img/tools/test.png"}" alt="{i18n key=copix:common.buttons.test}" /></a></td>
 		</tr>
	</table>
	<input type="submit"  value="{i18n key=copix:common.buttons.valid}" />
	<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:window.location='{copixurl dest="admin||"}'" />
</form>
{formfocus id="urlinput"}