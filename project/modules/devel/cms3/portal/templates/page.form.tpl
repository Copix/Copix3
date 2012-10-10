{copixhtmlheader kind="csslink"}
	{copixresource path="heading|css/headingadmin.css"}
{/copixhtmlheader}

{mootools plugins="copixformobserver;resize"}

{copixhtmlheader kind="JSDomReadyCode"}
	{literal}
	var formIdentifiantObserver = new CopixFormObserver ('formPage', {
		  onChanged : function (){
			 var myHTMLRequest = new Request.HTML({
				url:'{/literal}{copixurl dest="ajax|updateInfosGenerales" editId=$ppo->editId}{literal}'
			 });
			 myHTMLRequest.post($('formPage'));
		  },
		  checkIntervall :50
	   });

		new Resizing('description_hei',{'min':100,'max':400});

	{/literal}
{/copixhtmlheader}

{copixresource path="img/tools/help.png" assign=imgSrc}
{error message=$ppo->error}

{beginblock title="Informations" isFirst=true}

<form id="formPage" action="{copixurl dest="admin|valid" editId=$ppo->editId}" method="POST">
<input type="hidden" name="publish" id="publish" value="0" />
<input type="hidden" name="published_date" id="published_date" />
<input type="hidden" name="end_published_date" id="end_published_date" />
<table class="CopixVerticalTable">
	<tr>
		<th style="width: 100px" colspan="2">Nom</th>
		<td>{inputtext name="caption_hei" value=$ppo->editedElement->caption_hei maxlength="255" style="width: 99%"}</td>
	</tr>
	<tr class="alternate">
		<th>Titre</th>
		<th class="help">
			{popupinformation width="450" alt=$imgAlt img=$imgSrc}
				Titre de la page, visible par l'internaute. Si rien n'est spécifié, le titre de la page sera identique au nom de la page.
			{/popupinformation}
		</th>
		<td>{inputtext name="title_hei" value=$ppo->editedElement->title_hei|escape maxlength="255" style="width: 99%"}</td>
	</tr>
	<tr>
		<th>Titre du navigateur</th>
		<th class="help">
			{popupinformation width="500" alt=$imgAlt img=$imgSrc}
				Titre du navigateur lorsque la page est affichée à l'internaute.
				<br />Si rien n'est spécifié, le titre du navigateur sera identique au titre de la page.
				{/popupinformation}
		</th>
		<td>{inputtext name="browser_page" value=$ppo->editedElement->browser_page|escape maxlength="255" style="width: 99%"}</td>
	</tr>
	<tr class="alternate">
		<th>Titre de la page dans les menus</th>
		<th class="help">
			{popupinformation width="500" alt=$imgAlt img=$imgSrc}
				Titre de la page lorsque celle-ci est affichée dans un menu.
				<br />Si rien n'est spécifié, le titre du menu sera identique au titre de la page.
				{/popupinformation}
		</th>
		<td>{inputtext name="menu_caption_hei" value=$ppo->editedElement->menu_caption_hei|escape maxlength="255" style="width: 99%"}</td>
	</tr>
	<tr>
		<th style="vertical-align: top">Description</th>
		<th class="help" style="vertical-align: top">
			{popupinformation width="450" alt=$imgAlt img=$imgSrc}
				Cette description sera utilisée par les moteurs de recherche lors du référencement, ainsi que par le
				moteur de recherche interne de CopixCMS, lorsqu'il présentera les résultats de sa recherche.
			{/popupinformation}
		</th>
		<td><textarea class="cmsElementDescription" id="description_hei" name="description_hei" style="width: 99%" class="formTextarea">{$ppo->editedElement->description_hei}</textarea></td>
	</tr>
	<tr class="alternate">
		<th class="last">Modèle de page</th>
		<th class="help last">
			{popupinformation width="460" alt=$imgAlt img=$imgSrc class="popupinformation"}
				Le modèle permet de régir l'apparence globale de votre page.
				<br />Il existe plusieurs modèles (2 colonnes, 3 colonnes, 2 tiers 1 tiers, ...) que vous pouvez sélectionner.
				<br />S'il manque des modèles, vous pouvez demander a votre administrateur d'en créer des nouveaux.
			{/popupinformation}
		</th>
		<td>
			{copixzone process=portal|templateChooser showOptions=false xmlPath=$ppo->xmlPath inputId=template_page selected=$ppo->editedElement->template_page module=portal textBouton=$ppo->textBouton showSelection=>true}
		</td>
	</tr>
</table>
</form>

{endblock}
{formfocus id='caption_hei'}
{copixzone process=pagemenu edition=true element=$ppo->editedElement}