{if $arDocuments}
<table class="CopixTable">
   <thead>
   <tr>
      <th>{i18n key="document.messages.title"}</th>
      <th>{i18n key="document.messages.description"}</th>
      <th>{i18n key="dao.document.fields.version_doc"}</th>
      <th class="actions">{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {foreach from=$arDocuments item=document}
   <tr>
      <td>{$document->title_doc}</td>
      <td>{$document->desc_doc}</td>
      <td>{$document->version_doc}</td>
      <td><a href="{copixurl dest="document|admin|prepareEdit" id_doc=$document->id_doc version=$document->version_doc}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
      </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
<input type="button" value="{i18n key=copix:common.buttons.back}" onclick="javascript:document.location='{copixurl dest="copixheadings|admin|" browse="document" id_head=$document->id_head}'" />