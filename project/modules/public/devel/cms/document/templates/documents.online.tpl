{if $arDocuments}
<table class="copixTable">
   <thead>
   <tr>
      <th>{i18n key="document.messages.title"}</th>
      <th>{i18n key="document.messages.description"}</th>
      <th>{i18n key="dao.document.fields.version_doc"}</th>
      <th>{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {foreach from=$arDocuments item=document}
   <tr>
      <td>{$document->title_doc}</td>
      <td>{$document->desc_doc}</td>
      <td>{$document->version_doc}</td>
      <td><a href="{copixurl dest="document|admin|prepareEdit" id_doc=$document->id_doc}" ><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}"></a>
      <a href="{copixurl dest="document|admin|deleteDocument" id_doc=$document->id_doc}" ><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}"></a>
      <a href="{copixurl dest="document|admin|viewVersion" id_doc=$document->id_doc}" ><img src="{copixresource path="img/tools/history.png"}" alt="{i18n key="document.buttons.viewVersion"}"></a>
      </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
<div class="pager"><p>{$pager}</p></div>
<p>
<input type="button" value="{i18n key=copix:common.buttons.back}" onclick="javascript:document.location='{copixurl dest="document|admin|"}'" />
</p>
