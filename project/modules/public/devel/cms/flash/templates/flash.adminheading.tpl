{**
* Opérations d'administration sur les documents flash
*}
<form id="documentWorkflow" action="" class="CopixForm" method="post">
<table class="CopixTable">
<thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:28%">{i18n key="copix:common.actions.title"}</th>
   </tr>
</thead>
<tbody>
   {foreach from=$arDocuments item=document}
   <tr {cycle values=',class="alternate"' name="arDocumentPublish"}>
      <td>{$document->name_flash}</td>
      <td>{$document->author_flash}</td>
      <td>  <a href="{copixurl dest="flash|admin|delete" id_flash=$document->id_flash}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="flash|admin|viewVersion" id_flash=$document->id_flash}" title="{i18n key="document.buttons.viewVersion"}"><img src="{copixresource path="img/tools/history.png"}" alt="{i18n key="document.buttons.viewVersion"}" /></a>
      </td>
   </tr>
   {/foreach}
</tbody>
</table>
</form>