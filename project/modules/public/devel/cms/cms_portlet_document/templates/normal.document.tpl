{if $subject}
<h2>{$subject|escape:html}</h2>
{/if}

{if count($arDocs)}
<table>
   {foreach from=$arDocs item=document}
      <tr>
         <th>{$document->title_doc|escape:html}</th>
         <td><a href="{copixurl dest="document||download" id_doc=$document->id_doc}"><img src="{copixresource path="img/tools/download.png"}" /></a></td>
      </tr>
   {/foreach}
</table>
{/if}