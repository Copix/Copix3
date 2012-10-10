{copixhtmlheader kind="csslink"}
	{copixresource path="heading|css/headingadmin.css"}
{/copixhtmlheader}

{copixresource path="img/tools/help.png" assign=imgSrc}
{mootools plugin="resize"}
{copixhtmlheader kind="JSDomReadyCode"}
{literal}
new Resizing('description_hei',{'min':100,'max':400});
{/literal}
{/copixhtmlheader}
{beginblock title=Informations isFirst=true}
<form action="{copixurl dest="admin|valid" editId=$ppo->editId}" method="POST" id="formHeading">
<input type="hidden" name="publish" id="publish" value="0" />
<table class="CopixVerticalTable">
	<tr>
		<th style="width: 110px">Libellé</th>
		<th class="help">
			{popupinformation img=$imgSrc}
			Nom utilisé pour identifier la rubrique dans le CMS
			{/popupinformation}
		</th>
		<td><input type="text" name="caption_hei" id="caption_hei" value="{$ppo->editedElement->caption_hei}" class="inputText" maxlength="255" style="width: 99%" /></td>
	</tr>
	<tr class="alternate">
		<th>Titre</th>
		<th>
			{popupinformation img=$imgSrc}
			Titre de la rubrique, visible par l'internaute. Si rien n'est spécifié, le titre de la rubrique sera identique au "libellé"
			{/popupinformation}
		</th>
		<td><input type="text" name="title_hei" value="{$ppo->editedElement->title_hei}" class="inputText" maxlength="255" style="width: 99%" /></td>
	</tr>
	<tr>
		<th>Description</th>
		<th></th>
		<td><textarea class="cmsElementDescription" id="description_hei" name="description_hei" class="formTextarea">{$ppo->editedElement->description_hei}</textarea></td>
	</tr>
	<tr class="alternate">
		<th class="last">Page d'accueil</th>
		<th class="last">
			{popupinformation img=$imgSrc}
			Définir une page d'accueil pour la rubrique permet de definir un lien sur une rubrique affichée dans un menu.
			{/popupinformation}
		</th>
		<td>
			{copixzone process='heading|headingelement/headingelementchooser' arTypes=";"|explode:"page;link" selectedIndex=$ppo->editedElement->home_heading inputElement=home_heading}
		</td>
	</tr>
</table>
</form>
{endblock}
{copixzone process="heading|headingelement/HeadingElementButtons" form="formHeading" actions="savepublish"|toarray}

{formfocus id="caption_hei"}