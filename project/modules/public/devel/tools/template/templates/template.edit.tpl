{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
   var myForm = document.templateEdit;
   myForm.action = pUrl;
   myForm.submit ('{copixurl dest="template|admin|validForm"}');
   return false;
{literal}
}

var popupGeneratorWindow = null;
function popUpGenerator(url){
	if (popupGeneratorWindow == null || popupGeneratorWindow.closed){
		popupGeneratorWindow = window.open(url,'', 'resizable=no, location=no, width=800, height=600, menubar=no, status=no, scrollbars=yes, menubar=no');
	}else{
		popupGeneratorWindow.focus ();
	}
}

function toggleSelect (){
	var inputForm;
	inputForm = document.getElementById ("modulequalifier_ctpl");
	if (inputForm)
       inputForm.disabled = ! inputForm.disabled;

    inputForm = document.getElementById ("caption_ctpl");
	if (inputForm)
       inputForm.disabled = ! inputForm.disabled;
       
}
//]]>
</script>
{/literal}

{if $showErrors}
<div class="errorMessage">
<h1>{i18n key="copix:common.messages.error"}</h1>
{ulli values=$errors}
</div>
{/if}

<ul class="copixCMSNav">
 <li {if $selectedTab==0}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="admin|validForm" selectedTab="0" editId=$editId}" onclick="return doUrl ('{copixurl dest="admin|validForm" selectedTab="0" editId=$editId}')">{i18n key="template.title.general"}</a></li>
 <li {if $selectedTab==1}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="admin|validForm" selectedTab="1" editId=$editId}" onclick="return doUrl ('{copixurl dest="admin|validForm" selectedTab="1"  editId=$editId}')">{i18n key="template.title.textContent"}</a></li>
 <li {if $selectedTab==2}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="admin|validForm" selectedTab="2"  editId=$editId}" onclick="return doUrl ('{copixurl dest="admin|validForm" selectedTab="2" editId=$editId}')">{i18n key="template.title.wysiwygContent"}</a></li>
 {if $edited->dynamicTemplate != 0}
	 <li><a href="#" onclick="popUpGenerator('{copixurl dest="admin|templateGenerator" editId=$editId}'); return false;">{i18n key="template.title.templateGenerator"}</a></li>
 {/if}
</ul>

<form action="{copixurl dest="template|admin|valid" editId=$editId}" name="templateEdit" method="post" class="copixForm">
<fieldset>
{if $selectedTab == 0}
 <table>
   <input type="hidden" name="id_ctpl" value="{$edited->id_ctpl}" />
   <tr>
    <th><label for="caption_ctpl">{i18n key=template.dao.caption_ctpl}</label></th>
    <td><input id="caption_ctpl" name="caption_ctpl" type="text" value="{$edited->caption_ctpl|escape}" /></td>
   </tr>

   {if $edited->dynamicTemplate == 0}
   <tr>
    <th>{i18n key=template.dao.qualifier_ctpl}</th>
    <td>
		{if strlen($edited->qualifier_ctpl)}
				{$edited->qualifier_ctpl}<a href="javascript:doUrl ('{copixurl dest="admin|validForm" editId=$editId standardTemplate="" sourceTemplate=""}')"><img src="{copixurl}img/tools/delete.png" />
			{else}
				{if $edited->publicid_ctpl != $edited->id_ctpl && strlen($edited->publicid_ctpl) }
					{$sourceTemplate}
					<a href="javascript:doUrl ('{copixurl dest="admin|validForm" editId=$editId standardTemplate="" sourceTemplate=""}')"><img src="{copixurl}img/tools/delete.png" />
				{else}
					{i18n key=copix:common.none}
				{/if}
		{/if}
		<a href="javascript:doUrl ('{copixurl dest="admin|selectStandardTemplate" editId=$editId}');"><img src="{copixurl}img/tools/select.png" alt="{i18n key="copix:common.buttons.select"}"/></a></td>
   </tr>
   {else}
   <tr> 
    <th><label for="modulequalifier_ctpl">{i18n key=template.dao.modulequalifier_ctpl}</label></th>
    {if $edited->mainTemplateUpdate}
    <td>{select values=$arModules name="modulequalifier_ctpl" objectMap="name;description" selected=$edited->modulequalifier_ctpl}</td>
    {else}
    <td>{select extra='disabled="disabled"' values=$arModules name="modulequalifier_ctpl" objectMap="name;description" selected=$edited->modulequalifier_ctpl}</td>
    {/if}
   </tr>
   {/if}
   {if $edited->dynamicTemplate == 0}
   <tr>
    <th><label for="id_ctpt">{i18n key=template.theme.dao.id_ctpt}</label></th>
    <td>{select values=$arTheme name="id_ctpt" objectMap="id_ctpt;caption_ctpt" selected=$edited->id_ctpt}</td>
   </tr>
   {/if}

   {if $edited->dynamicTemplate == 1 && $edited->publicid_ctpl != null}
   <tr>
    <th><label for="mainTemplateUpdate">{i18n key=template.theme.dao.mainTemplateUpdate}</label></th>
    <td><input type="checkbox" id="mainTemplateUpdate" name="mainTemplateUpdate" {if $edited->mainTemplateUpdate}checked="checked"{/if} onclick="toggleSelect ()" id="mainTemplateUpdate" /></td>
   </tr>
   {/if}
   </table>
{/if}

{if $selectedTab == 0}
{formfocus id=caption_ctpl}
{/if}

{if ($selectedTab == 1 || $selectedTab == 2) && strlen ($edited->qualifier_ctpl)>0}
   <a href="{copixurl dest="admin|importStandardTemplate" editId=$editId selectedTab=$selectedTab standardTemplate=$edited->qualifier_ctpl}"><img src="{copixurl}img/tools/import.png" alt="" />{i18n key=template.action.importFromStandardTemplate qualifier=$edited->qualifier_ctpl}</a>
{else}
	{if ($selectedTab == 1 || $selectedTab == 2) && $edited->id_cptl != $edited->publicid_ctpl}
   		<a href="{copixurl dest="admin|importNonStandardTemplate" editId=$editId selectedTab=$selectedTab nonStandardTemplate=$edited->publicid_ctpl}"><img src="{copixurl}img/tools/import.png" alt="" />{i18n key=template.action.importFromNonStandardTemplate caption=$sourceTemplate}</a>
	{/if}
{/if}

{if $selectedTab == 1}
   <textarea cols="70" rows="20" name="content_ctpl">{$edited->content_ctpl|escape}</textarea>
{/if}

{if $selectedTab == 2}
   {htmleditor name="content_ctpl" content=$edited->content_ctpl}
{/if}

{if $selectedTab == 3}
   Nothing right now
{/if}
</fieldset>

<p class="validButtons">
 <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
 <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="admin|cancelEdit" editId=$editId}'" />
</p>
</form>