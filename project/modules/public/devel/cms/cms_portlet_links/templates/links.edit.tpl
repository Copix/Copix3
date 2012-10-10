{*
* template de modification d'une enquête.
* param toEdit
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
{if $kind == "general"}
   var myForm = document.formEdit;
   myForm.action = pUrl;
   myForm.submit ();
{else}
   document.location.href=pUrl;
{/if}
{literal}
}

function submitLink (){
   var myForm    = document.formEdit;
   {/literal}
   myForm.action = '{copixurl dest="cms_portlet_links||validedit" next='addLink' kind='0' notxml=true}';
   {literal}
   myForm.submit ();
}

function submitLink2 (){
   var myForm    = document.formEdit;
   {/literal}
   myForm.action = '{copixurl dest="cms_portlet_links||validedit" next='selectPage' kind='0'  notxml=true}';
   {literal}
   myForm.submit ();
}

//]]>
</script>
{/literal}

<ul class="copixCMSNav">
     <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_links||validedit" kind="0"}')">{i18n key="links.title.general"}</a></li>
     <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_links||validedit" kind="1"}')">{i18n key="links.title.preview"}</a></li>
</ul>


<div>
{if $kind == "general"}
<form name="formEdit" action="{copixurl dest="cms_portlet_links||valid"}" method="post" class="copixForm">
   <fieldset>
	<table>
      <tr>
		<th>{i18n key="links.messages.title"}</th>
		<td><input type="text" size="48" name="title" value="{$toEdit->title|escape}"/></td>
     </tr>
      <tr>
		 <th>{i18n key="links.messages.displayKind"}</th>
		 <td>{select name="template" values=$possibleKinds selected=$objDocs->templateId}</td>
		</tr>
	</table>
	</fieldset>
	<br />
	<fieldset>
   <table class="Copixtable">
      <thead>
      <tr>
        <th>{i18n key="links.messages.name"}</th>
        <th>{i18n key="links.messages.destination"}</th>
        <th>{i18n key="copix:common.actions.title"}</th>
      </tr>
      </thead>
      <tbody>
      {foreach from=$toEdit->links item=link}
      <tr>
        <td>{$link->linkName|escape}</td>
        <td>{$link->linkDestination|escape}</td>
        <td><a href="{copixurl dest="cms_portlet_links||removeLink" linkId=$link->id}"><img src="{copixurl}img/tools/delete.png" /></a>
           <a href="javascript:doUrl ('{copixurl dest="cms_portlet_links||moveUp" id=$link->id}')"><img src="{copixurl}img/tools/up.png" /></a>
           <a href="javascript:doUrl ('{copixurl dest="cms_portlet_links||moveDown" id=$link->id}')"><img src="{copixurl}img/tools/down.png" /></a>
           </td>
      </tr>
      {/foreach}
      <tr>
         <td><input type="text" size="20" name="linkName" value="{$toEdit->linkName|default:''|escape}" /></td>
         <td><input type="text" size="20" name="linkDestination" value="{$toEdit->linkDestination|default:''}" /></td>
         <td><a href="#" onclick="javascript:submitLink ()"><img src="{copixurl}img/tools/add.png" alt="{i18n key="copix:common.buttons.ok"}" /></a>
             <a href="#" onclick="javascript:submitLink2 ()"><img src="{copixurl}img/tools/selectin.png" alt="{i18n key="links.messages.selectPage"}" /></a>
         </td>
      </tr>
      </tbody>
	</table>
   </fieldset>
   <p class="validButtons">
	<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
	<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
   </p>
</form>
{/if}

{if $kind == "preview"}
   {$show}
{/if}
</div>
