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
		<td><input type="text" name="actiongroupDescription" value="Descriptio de l'actiongroup" size="60" /></td>
	</tr>
	<tr class="alternate">
		<th valign="top">{i18n key="createmodule.actiongroup.actions"}</th>
		<td>
			<div id="actiongroup_actions"></div>
			<img src="{copixresource path="img/tools/add.png"}" onclick="javascript: addAction ()" style="cursor:pointer" />
		</td>
	</tr>
</table>

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
		} else {
			nameValue = '';
			descriptionValue = '';
		}
		html += '{/literal}{i18n key="global.name"}{literal} <input type="text" id="actionName' + boucle + '" name="actionName' + boucle + '" size="25" value="' + nameValue + '" />';
		html += '&nbsp;&nbsp;{/literal}{i18n key="global.description"}{literal} <input type="text" id="actionDescription' + boucle + '" name="actionDescription' + boucle + '" size="50" value="' + descriptionValue + '" />';
		if (nbrActions > 1) {
			html += ' <img src="{/literal}{copixresource path="img/tools/delete.png"}{literal}" onclick="javascript: deleteAction (' + boucle + ')" style="cursor:pointer" />';
		}
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
	
			html += '{/literal}{i18n key="global.name"}{literal} <input type="text" id="actionName' + actionIndex + '" name="actionName' + actionIndex + '" size="25" value="' + nameValue + '" />';
			html += '&nbsp;&nbsp;{/literal}{i18n key="global.description"}{literal} <input type="text" id="actionDescription' + actionIndex + '" name="actionDescription' + actionIndex + '" size="50" value="' + descriptionValue + '" />';
			if (nbrActions > 2) {
				html += ' <img src="{/literal}{copixresource path="img/tools/delete.png"}{literal}" onclick="javascript: deleteAction (' + actionIndex + ')" style="cursor:pointer" />';
			}
			actionIndex++;
		}
	}
	document.getElementById ('actiongroup_actions').innerHTML = html;
	nbrActions--;
}
addAction ();
</script>
{/literal}