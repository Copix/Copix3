<table class="CopixVerticalTable">
	<tr>
		<th valign="top">{i18n key="global.templates"}</th>
		<td valign="top">
			<div id="templates_templates"></div>
			<img src="{copixresource path="img/tools/add.png"}" onclick="javascript: addTemplate ()" style="cursor:pointer" />
		</td>
	</tr>
</table>

{literal}
<script type="text/javascript">
var nbrTemplates = 0;
function addTemplate () {
	// on ne peut pas faire un simple innerHTML += avec les nouveaux champs
	// car sinon firefox "perd" les valeurs saisies (pourquoi ???)
	nbrTemplates++;
	var html = '';
	for (boucle = 1; boucle <= nbrTemplates; boucle++) {
		if (boucle > 1) {
			html += '<br />';
		}
		if (boucle < nbrTemplates) {
			nameValue = document.getElementById ('templateName' + (boucle)).value;
			typeSmartyChecked = (document.getElementById ('templateType' + (boucle) + 'Smarty').checked) ? 'checked="checked"' : '';
			typePHPChecked = (typeSmartyChecked.length == 0) ? 'checked="checked"' : '';
		} else {
			nameValue = '';
			typeSmartyChecked = 'checked="checked"';
			typePHPChecked = '';
		}
		html += '<input type="text" id="templateName' + boucle + '" name="templateName' + boucle + '" size="30" value="' + nameValue + '" />';
		html += ' <input type="radio" name="templateType' + boucle + '" value="smarty" id="templateType' + boucle + 'Smarty" ' + typeSmartyChecked + ' /><label for="templateType' + boucle + 'Smarty">{/literal}{i18n key="createmodule.templates.smarty"}{literal}</label>';
		html += ' <input type="radio" name="templateType' + boucle + '" value="php" id="templateType' + boucle + 'PHP" ' + typePHPChecked + ' /><label for="templateType' + boucle + 'PHP">{/literal}{i18n key="createmodule.templates.php"}{literal}</label>';
		if (nbrTemplates > 1) {
			html += '&nbsp;&nbsp;<img src="{/literal}{copixresource path="img/tools/delete.png"}{literal}" onclick="javascript: deleteTemplate (' + boucle + ')" style="cursor:pointer" />';
		}
	}
	document.getElementById ('templates_templates').innerHTML = html;
}

function deleteTemplate (deleteIndex) {
	templateIndex = 1;
	html = '';
	for (boucle = 1; boucle <= nbrTemplates; boucle++) {
		if (deleteIndex != boucle) {
			if (templateIndex > 1) {
				html += '<br />';
			}
			nameValue = document.getElementById ('templateName' + (boucle)).value;
			typeSmartyChecked = (document.getElementById ('templateType' + (boucle) + 'Smarty').checked) ? 'checked="checked"' : '';
			typePHPChecked = (typeSmartyChecked.length == 0) ? 'checked="checked"' : '';

			html += '<input type="text" id="templateName' + templateIndex + '" name="templateName' + templateIndex + '" size="30" value="' + nameValue + '" />';
			html += ' <input type="radio" name="templateType' + templateIndex + '" value="smarty" id="templateType' + templateIndex + 'Smarty" ' + typeSmartyChecked + ' /><label for="templateType' + templateIndex + 'Smarty">{/literal}{i18n key="createmodule.templates.smarty"}{literal}</label>';
			html += ' <input type="radio" name="templateType' + templateIndex + '" value="php" id="templateType' + templateIndex + 'PHP" ' + typePHPChecked + ' /><label for="templateType' + templateIndex + 'PHP">{/literal}{i18n key="createmodule.templates.php"}{literal}</label>';
			if (nbrTemplates > 2) {
				html += '&nbsp;&nbsp;<img src="{/literal}{copixresource path="img/tools/delete.png"}{literal}" onclick="javascript: deleteTemplate (' + templateIndex + ')" style="cursor:pointer" />';
			}
			templateIndex++;
		}
	}
	document.getElementById ('templates_templates').innerHTML = html;
	nbrTemplates--;
}
addTemplate ();
</script>
{/literal}