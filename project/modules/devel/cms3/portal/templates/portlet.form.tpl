{mootools plugins="copixformobserver;resize"}

{if $ppo->editedElement}
	{copixhtmlheader kind="JSDomReadyCode"}
	{literal}
	var formIdentifiantObserver = new CopixFormObserver ('formPortlet', {
		onChanged : function (){
			var myHTMLRequest = new Request.HTML({
				url:'{/literal}{copixurl dest="ajax|updatePortletInfos" editId=$ppo->editId}{literal}',
				onComplete : function(){
					ajaxOff();
				}

			});
			ajaxOn();
			myHTMLRequest.post($('formPage'));
		},
		checkIntervall :50
	});
	
	new Resizing('description_hei',{'min':100,'max':400});

	{/literal}
	{/copixhtmlheader}
		
	{copixresource path="img/tools/help.png" assign=imgSrc}

	{beginblock title="Informations" isFirst=true}
	<form id="formPortlet" action="{copixurl dest="adminportlet|valid" editId=$ppo->editId heading=$ppo->heading}" method="POST">
	<input type="hidden" name="publish" id="publish" value="0" />
	<table class="CopixVerticalTable">
		<tr>
			<th>Type</th>
			<th></th>
			<td>{$ppo->type_portlet}</td>
		</tr>
		<tr class="alternate">
			<th style="width: 90px">Nom</th>
			<th style="width: 1px">
				{popupinformation width="300" alt=$imgAlt img=$imgSrc}
				Le nom de la page apparaît dans la barre du navigateur ainsi que dans l'emplacement "titre"
				en fonction de la charte graphique
				{/popupinformation}
			</th>
			<td><input type="text" name="caption_hei" id="caption_hei" value="{$ppo->editedElement->caption_hei|escape}" class="inputText" maxlength="255" style="width: 99%" /></td>
		</tr>
		<tr>
			<th>Description</th>
			<th></th>
			<td><textarea class="cmsElementDescription" id="description_hei" name="description_hei">{$ppo->editedElement->description_hei}</textarea></td>
		</tr>
	</table>
	</form>
	{endblock}
	{formfocus id='caption_hei'}
	<br />
	{copixzone process=HeadingElementPortletMenu edition=true}
{else}
	{beginblock title="Création de portlet" isFirst=true}
	<form id="formPortlet" action="{copixurl dest="portal|adminportlet|prepareEdit" type=portlet editId=$ppo->editId }" method="POST">
	<input type="hidden" name="publish" id="publish" value="0" />
		<table class="CopixVerticalTable">
			 <tr>
				 <th width="110px" class="last">Type</th>
				<td>
					{select name="type_portlet" values=$ppo->arTypes emptyShow=false selected=$ppo->type_portlet}
				</td>
			</tr>
		</table>
	</form>

	<br />
	{copixzone process="heading|headingelement/HeadingElementButtons" form="formPortlet" actions="next"|toarray backUrl=$ppo->backUrl}
	{endblock}
{/if}