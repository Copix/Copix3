<div class="quickAdminModule">
<!-- DOCUMENT TO VALID -->
{copixurl dest="quickadmin|admin|" currentModule="document" assign=backUrl}
<form id="documentWorkflow" action="" class="CopixForm" method="post">
{if count($toValid)}
<h2>{i18n key="copixheadings|workflow.messages.toValid"} ({$toValid|@count} {i18n key="copixheadings|workflow.messages.item"})</h2>
<table class="CopixTable">
   <thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:28%">{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {assign var=heading value='NOT_AN_ID'}
   {foreach from=$toValid item=document}
   {if $document->id_head != $heading}
      {assign var=heading value=$document->id_head}
      <tr>
      <th colspan="3">{if $document->caption_head}{$document->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$document->id_head}>
       <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
       <td>{$document->author_doc}</td>
       <td>
          {copixurl dest="document|admin|statusTrash" back=$backUrl|urlencode id_doc=$document->id_doc assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="documentWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|default|download" id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="document|admin|prepareEdit" back=$backUrl|urlencode id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="document|admin|statusValid" back=$backUrl|urlencode id_doc=$document->id_doc assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="documentWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="document|admin|statusRefuse" back=$backUrl|urlencode id_doc=$document->id_doc assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="documentWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_doc_{$document->id_doc}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}

<!-- DOCUMENT TO VALID -->
{if count($toPublish)}
<h2>{i18n key="copixheadings|workflow.messages.toPublish"} ({$toPublish|@count} {i18n key="copixheadings|workflow.messages.item"})</h2>
<table class="CopixTable">
   <thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:28%">{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {assign var=heading value='NOT_AN_ID'}
   {foreach from=$toPublish item=document}
   {if $document->id_head != $heading}
      {assign var=heading value=$document->id_head}
      <tr>
      <th colspan="3">{if $document->caption_head}{$document->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$document->id_head}>
       <td>{popupinformation text=$document->title_doc}
            {i18n key="dao.document.fields.extension_doc"} : {$document->extension_doc}<br />
            {i18n key="copix:common.messages.desc"} : {$document->desc_doc}<br />
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$document->statusauthor_doc param2=$document->statusdate_doc|datei18n}
            {if $document->statuscomment_doc}{i18n key="copixheadings|workflow.messages.withComment" param=$document->statuscomment_doc}{/if}
          {/popupinformation}
          {$document->title_doc}</td>
       <td>{$document->author_doc}</td>
       <td>
          {copixurl dest="document|admin|statusTrash" back=$backUrl|urlencode id_doc=$document->id_doc assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="documentWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="document|default|download"  id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="document|admin|prepareEdit" back=$backUrl|urlencode id_doc=$document->id_doc}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="document|admin|statusPublish" back=$backUrl|urlencode id_doc=$document->id_doc assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="documentWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="document|admin|statusRefuse" back=$backUrl|urlencode id_doc=$document->id_doc assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="documentWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_doc_{$document->id_doc}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
</form>
</div>
