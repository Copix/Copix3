<table class="CopixVerticalTable">
	<tr>
		<th>{i18n key="global.name"}</th>
		<td><input type="text" name="actiongroupName" size="30" value="MyActionGroup" /></td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="global.author"}</th>
		<td><input type="text" name="actiongroupAuthor" size="30" value="Steevan BARBOYON" /></td>
	</tr>
	<tr>
		<th>{i18n key="global.description"}</th>
		<td><input type="text" name="actiongroupDescription" value="Description de l'actiongroup" size="60" /></td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="createmodule.th.actiongroupCredential"}</th>
		<td><input type="text" name="actiongroupCredential" value="basic:admin" size="30" /></td>
	</tr>
	<tr>
		<th valign="top">{i18n key="createmodule.actiongroup.actions"}</th>
		<td>
			<div id="actiongroup_actions"></div>
			<img src="{copixresource path="img/tools/add.png"}" onclick="javascript: addAction ()" style="cursor:pointer" />
		</td>
	</tr>
</table>

{i18n key="global.name" assign=globalName}
{i18n key="createmodule.action.credential" assign=actionCredential}

{literal}
<script type="text/javascript">
var nbrActions = 0;
function addAction () {
	// on ne peut pas faire un simple innerHTML += avec les nouveaux champs
	// car sinon firefox "perd" les valeurs saisies (pourquoi ???)
	nbrActions++;
	var html = '';
	for (boucle = 1; boucle <= nbrActions; boucle++) {
		if (boucle > 1) {
			html += '<br />';
		}
		if (boucle < nbrActions) {
			nameValue = document.getElementById ('actionName' + (boucle)).value;
			descriptionValue = document.getElementById ('actionDescription' + (boucle)).value;
			credentialValue = document.getElementById ('actionCredential' + (boucle)).value;
		} else {
			nameValue = 'default';
			descriptionValue = 'Description';
			credentialValue = 'test';
		}
		html += getActionHtml (boucle, nameValue, descriptionValue, credentialValue, (nbrActions > 1));
	}
	document.getElementById ('actiongroup_actions').innerHTML = html;
}

function deleteAction (deleteIndex) {
	actionIndex = 1;
	html = '';
	for (boucle = 1; boucle <= nbrActions; boucle++) {
		if (deleteIndex != boucle) {
			if (actionIndex > 1) {
				html += '<br />';
			}
			nameValue = document.getElementById ('actionName' + (boucle)).value;
			descriptionValue = document.getElementById ('actionDescription' + (boucle)).value;
			rightValue = document.getElementById ('actionCredential' + (boucle)).value;
			
			html += getActionHtml (actionIndex, nameValue, descriptionValue, rightValue, (nbrActions > 2));
			
			actionIndex++;
		}
	}
	document.getElementById ('actiongroup_actions').innerHTML = html;
	nbrActions--;
}

function getActionHtml (actionIndex, nameValue, descriptionValue, credentialValue, canDelete) {
	html = '<table>';
	html += '<tr>';
	html += '<td>';
	html += '{/literal}{$globalName|escape}{literal}';
	html += ' <input type="text" id="actionName' + actionIndex + '" name="actionName' + actionIndex + '" size="25" value="' + nameValue + '" />';
	html += '&nbsp;&nbsp;{/literal}{$actionCredential|escape}{literal}';
	html += ' <input type="text" id="actionCredential' + actionIndex + '" name="actionCredential' + actionIndex + '" value="' + credentialValue + '" />';
	if (canDelete) {
		html += ' <img src="{/literal}{copixresource path="img/tools/delete.png"}{literal}" onclick="javascript: deleteAction (' + actionIndex + ')" style="cursor:pointer" />';
	}
	html += '</td>';
	html += '</tr>';
	html += '<tr>';
	html += '<td>';
	html += '{/literal}{i18n key="global.description"}{literal}';
	html += ' <input type="text" id="actionDescription' + actionIndex + '" name="actionDescription' + actionIndex + '" size="50" value="' + descriptionValue + '" />';
	html += '</td>';
	html += '</tr>';
	html += '</table>';
	
	return html;
}
addAction ();
</script>
{/literal}
