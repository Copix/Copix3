{if $arDocuments}
<table>
   <tr>
      <th>{i18n key="document.messages.title"}</th>
      <th>{i18n key="document.messages.description"}</th>
      <th>{i18n key="copix:common.actions.title"}</th>
   </tr>
   {foreach from=$arDocuments item=document}
   <tr>
      <td>{$document->title_doc}</td>
      <td>{$document->desc_doc}</td>
      <td><input type="button" onclick="{if $select=='HTMLAREA'}javascript:window.opener.{$editorName}._doc.execCommand('createlink', false, '{copixurl dest="document||download" id_doc=$document->id_doc}');window.close();{else}document.location='{$select}{$document->id_doc}'{/if}" value="{i18n key="copix:common.buttons.select"}" />
      </td>
   </tr>
   {/foreach}
</table>
<div align="center">{$pager}</div>
{else}
{i18n key="document.messages.noDoc} {i18n key="document.messages.addDoc"} <a href="{copixurl dest="document|admin|contrib"}">{i18n key="document.messages.here"}</a>.
{/if}
<br />
<br />
<input type="button" value="{i18n key=copix:common.buttons.back}" onclick="{if $back=='HTMLAREA'}javascript:window.close();{else}javascript:document.location='{$back}'{/if}" />
