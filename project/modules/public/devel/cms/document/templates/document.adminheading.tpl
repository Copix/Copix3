{**
* Front page to the documents actions
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
   <!--- ONLINE DOCUMENT -->
   {if count($arDocumentPublish)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.online"} ({$arDocumentPublish|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arDocumentPublish item=document}
   <tr {cycle values=',class="alternate"' name="arDocumentPublish"}>
      <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
      <td>{$document->author_doc}</td>
      <td>{if $publishEnabled}
            <a href="{copixurl dest="document|admin|delete" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|admin|cut" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.cut"}"><img src="{copixresource path="img/tools/cut.png"}" alt="{i18n key="copix:common.buttons.cut"}" /></a>
          {/if}
          {if $contribEnabled}
            <a href="{copixurl dest="document|default|download" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="document|admin|prepareEdit" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            <a href="{copixurl dest="document|admin|viewVersion" id_doc=$document->id_doc}" title="{i18n key="document.buttons.viewVersion"}"><img src="{copixresource path="img/tools/history.png"}" alt="{i18n key="document.buttons.viewVersion"}" /></a>
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- DRAFT DOCUMENT -->
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.draft"} ({$arDocumentDraft|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th><a href="{copixurl dest="admin|create" id_head=$id_head}" title="{i18n key="document.title.newdocument"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="document.title.newdocument"}" /></a>
         {if $pasteEnabled}
         <a href="{copixurl dest="admin|paste" id_head=$id_head}"><img src="{copixresource path="img/tools/paste.png"}" alt="{i18n key="copix:common.buttons.paste"}" title="{i18n key="copix:common.buttons.paste"}" /></a>
         {/if}
      </th>
   </tr>
   {foreach from=$arDocumentDraft item=document}
   <tr {cycle values=',class="alternate"' name="arDocumentDraft"}>
      <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.createBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
      <td>{$document->author_doc}</td>
      <td>{if $contribEnabled}
            {copixurl dest="document|admin|statusTrash" id_doc=$document->id_doc assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="documentWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|default|download" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="document|admin|prepareEdit" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="document|admin|statusPropose" id_doc=$document->id_doc assign=urlPropose}
            <a href="#" onclick="{jssubmitform href=$urlPropose form="documentWorkflow"}" title="{i18n key="copix:common.buttons.propose"}"><img src="{copixresource path="img/tools/propose.png"}" alt="{i18n key="copix:common.buttons.propose"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_doc_{$document->id_doc}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {foreachelse}
   <tr>
      <td colspan="3">{i18n key="copixheadings|workflow.messages.noDraft"}</td>
   </tr>
   {/foreach}
   
   <!--- REFUSE DOCUMENT -->
   {if count($arDocumentRefuse)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.refuse"} ({$arDocumentRefuse|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}">-->{/if}</th>
   </tr>
   {foreach from=$arDocumentRefuse item=document}
   <tr {cycle values=',class="alternate"' name="arDocumentRefuse"}>
      <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.refuseBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
      <td>{$document->author_doc}</td>
      <td>{if $contribEnabled}
            {copixurl dest="document|admin|statusTrash" id_doc=$document->id_doc assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="documentWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|default|download" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="document|admin|prepareEdit" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_doc_{$document->id_doc}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- TOVALID DOCUMENT -->
   {if count($arDocumentPropose)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toValid"} ({$arDocumentPropose|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $validEnabled}<!--
            <img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copixheadings|workflow.messages.validAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arDocumentPropose item=document}
   <tr {cycle values=',class="alternate"' name="arDocumentPropose"}>
      <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
      <td>{$document->author_doc}</td>
      <td>{if $validEnabled}
            {copixurl dest="document|admin|statusTrash" id_doc=$document->id_doc assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="documentWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|default|download" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="document|admin|prepareEdit" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="document|admin|statusValid" id_doc=$document->id_doc assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="documentWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="document|admin|statusRefuse" id_doc=$document->id_doc assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="documentWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_doc_{$document->id_doc}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- TOPUBLISH DOCUMENT -->
   {if count($arDocumentValid)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toPublish"} ({$arDocumentValid|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--
            <img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copixheadings|workflow.messages.publishAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arDocumentValid item=document}
   <tr {cycle values=',class="alternate"' name="arDocumentValid"}>
      <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.validBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
      <td>{$document->author_doc}</td>
      <td>{if $publishEnabled}
            {copixurl dest="document|admin|statusTrash" id_doc=$document->id_doc assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="documentWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|default|download"  id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="document|admin|prepareEdit" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="document|admin|statusPublish" id_doc=$document->id_doc assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="documentWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="document|admin|statusRefuse" id_doc=$document->id_doc assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="documentWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_doc_{$document->id_doc}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- TRASH DOCUMENT -->
   {if count($arDocumentTrash)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.trash"} ({$arDocumentTrash|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arDocumentTrash item=document}
   <tr {cycle values=',class="alternate"' name="arDocumentTrash"}>
      <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.trashBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
      <td>{$document->author_doc}</td>
      <td>{if $contribEnabled}
            <a href="{copixurl dest="document|admin|delete" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|default|download"  id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            {copixurl dest="document|admin|statusDraft" id_doc=$document->id_doc assign=urlDraft}
            <a href="#" onclick="{jssubmitform href=$urlDraft form="documentWorkflow"}" title="{i18n key="copix:common.buttons.restore"}"><img src="{copixresource path="img/tools/restore.png"}" alt="{i18n key="copix:common.buttons.restore"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_doc_{$document->id_doc}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
</tbody>
</table>
</form>

