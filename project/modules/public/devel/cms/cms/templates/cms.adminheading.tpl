{**
* Front page to the cms actions
*}
<form id="CMSPageWorkflow" action="" class="CopixForm" method="post">
<table class="CopixTable">
<thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:28%">{i18n key="copix:common.actions.title"}</th>
   </tr>
</thead>
<tbody>
   <!--- ONLINE CMSPAGE -->
   {if count($arCMSPagePublish)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.online"} ({$arCMSPagePublish|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th></th>
   </tr>
   {foreach from=$arCMSPagePublish item=CMSPage}
   <tr {cycle values=',class="alternate"' name="arCMSPagePublish"}>
      <td>{popupinformation text=$CMSPage->title_cmsp}
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$CMSPage->statusauthor_cmsp param2=$CMSPage->statusdate_cmsp|datei18n}
            {if $CMSPage->statuscomment_cmsp}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsp}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsp}</td>
      <td>{$CMSPage->author_cmsp}</td>
      <td>
         {if $publishEnabled}
            <a href="{copixurl dest="cms|workflow|deleteOnline" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
         {/if}
         
         <a href="{copixurl dest="default|get" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
         {if $publishEnabled}
            <a href="{copixurl dest="admin|cut" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.cut"}"><img src="{copixresource path="img/tools/cut.png"}" alt="{i18n key="copix:common.buttons.cut"}" /></a>
         {/if}
         {if $contribEnabled}
            <a href="{copixurl dest="admin|newFromPage" version=$CMSPage->version_cmsp id=$CMSPage->publicid_cmsp update=1}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            <a href="{copixurl dest="admin|newFromPage" version=$CMSPage->version_cmsp id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.copy"}"><img src="{copixresource path="img/tools/replicate.png"}" alt="{i18n key="copix:common.buttons.copy"}" /></a>
         {/if}
         {if $CMSPage->version_cmsp > 1}
            <a href="{copixurl dest="admin|showHistory" id=$CMSPage->publicid_cmsp}" title="{i18n key="admin.actions.history"}"><img src="{copixresource path="img/tools/history.png"}" alt="{i18n key="admin.actions.history"}" /></a>
         {/if}
         {if $newsletterSendEnabled|default:false}
            <a href="{copixurl dest="cms|newsletter|send" id=$CMSPage->publicid_cmsp}" title="{i18n key="admin.actions.newsletter"}"><img src="{copixresource path="img/tools/mail.png"}" alt="{i18n key="admin.actions.newsletter"}" /></a>
         {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- DRAFT CMSPage OK-->
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.draft"} ({$arCMSPageDraft|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>
         <a href="{copixurl dest="admin|create" id_head=$id_head}" title="{i18n key="admin.buttons.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="admin.buttons.new"}" /></a>
         {if $pasteEnabled}
         <a href="{copixurl dest="admin|paste" id_head=$id_head}"><img src="{copixresource path="img/tools/paste.png"}" alt="{i18n key="copix:common.buttons.paste"}" title="{i18n key="copix:common.buttons.paste"}" /></a>
         {/if}
      </th>
   </tr>
   {foreach from=$arCMSPageDraft item=CMSPage}
   <tr {cycle values=',class="alternate"' name="arCMSPageDraft"}>
      <td>{popupinformation text=$CMSPage->title_cmsp}
            {i18n key="copixheadings|workflow.messages.createBy" param1=$CMSPage->statusauthor_cmsp param2=$CMSPage->statusdate_cmsp|datei18n}
            {if $CMSPage->statuscomment_cmsp}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsp}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsp}</td>
      <td>{$CMSPage->author_cmsp}</td>
      <td>{if $contribEnabled}
            {copixurl dest="cms|workflow|trash" id=$CMSPage->publicid_cmsp assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="admin|getDraft" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
            <a href="{copixurl dest="cms|admin|prepareEdit" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="cms|workflow|propose" id=$CMSPage->publicid_cmsp assign=urlPropose}
            <a href="#" onclick="{jssubmitform href=$urlPropose form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.propose"}"><img src="{copixresource path="img/tools/propose.png"}" alt="{i18n key="copix:common.buttons.propose"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_cmsp_{$CMSPage->publicid_cmsp}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {foreachelse}
   <tr>
      <td colspan="3">{i18n key="copixheadings|workflow.messages.noDraft"}</td>
   </tr>
   {/foreach}
   
   <!--- REFUSE CMSDraft -->
   {if count($arCMSPageRefuse)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.refuse"} ({$arCMSPageRefuse|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th></th>
   </tr>
   {foreach from=$arCMSPageRefuse item=CMSPage}
   <tr {cycle values=',class="alternate"' name="arCMSPageRefuse"}>
      <td>{popupinformation text=$CMSPage->title_cmsp}
            {i18n key="copixheadings|workflow.messages.refuseBy" param1=$CMSPage->statusauthor_cmsp param2=$CMSPage->statusdate_cmsp|datei18n}
            {if $CMSPage->statuscomment_cmsp}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsp}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsp}</td>
      <td>{$CMSPage->author_cmsp}</td>
      <td>{if $contribEnabled}
            {copixurl dest="cms|workflow|trash" id=$CMSPage->publicid_cmsp assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="admin|getDraft" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
            <a href="{copixurl dest="cms|admin|prepareEdit" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixurl assign=copixurl}
          {assign var=urlImg value="img/tools/comments.png"}
          {assign var=url value=$copixurl$urlImg}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_cmsp_{$CMSPage->publicid_cmsp}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- TOVALID CMSPAGE -->
   {if count($arCMSPagePropose)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toValid"} ({$arCMSPagePropose|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th></th>
   </tr>
   {foreach from=$arCMSPagePropose item=CMSPage}
   <tr {cycle values=',class="alternate"' name="arCMSPagePropose"}>
      <td>{popupinformation text=$CMSPage->title_cmsp}
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$CMSPage->statusauthor_cmsp param2=$CMSPage->statusdate_cmsp|datei18n}
            {if $CMSPage->statuscomment_cmsp}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsp}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsp}</td>
      <td>{$CMSPage->author_cmsp}</td>
      <td>{if $validEnabled}
            {copixurl dest="cms|workflow|trash" id=$CMSPage->publicid_cmsp assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="admin|getDraft" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
            <a href="{copixurl dest="cms|admin|prepareEdit" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="cms|workflow|valid" id=$CMSPage->publicid_cmsp assign=urlValid notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlValid form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="cms|workflow|refuse" id=$CMSPage->publicid_cmsp assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_cmsp_{$CMSPage->publicid_cmsp}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- TOPUBLISH CMSPAGE -->
   {if count($arCMSPageValid)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toPublish"} ({$arCMSPageValid|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th></th>
   </tr>
   {foreach from=$arCMSPageValid item=CMSPage}
   <tr {cycle values=',class="alternate"' name="arCMSPageValid"}>
      <td>{popupinformation text=$CMSPage->title_cmsp}
            {i18n key="copixheadings|workflow.messages.validBy" param1=$CMSPage->statusauthor_cmsp param2=$CMSPage->statusdate_cmsp|datei18n}
            {if $CMSPage->statuscomment_cmsp}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsp}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsp}</td>
      <td>{$CMSPage->author_cmsp}</td>
      <td>{if $publishEnabled}
            {copixurl dest="cms|workflow|trash" id=$CMSPage->publicid_cmsp assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="admin|getDraft" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
            <a href="{copixurl dest="cms|admin|prepareEdit" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="cms|workflow|publish" id=$CMSPage->publicid_cmsp assign=urlPublish notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="cms|workflow|refuse" id=$CMSPage->publicid_cmsp assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_cmsp_{$CMSPage->publicid_cmsp}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
   
   <!--- TRASH CMSPAGE -->
   {if count($arCMSPageTrash)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.trash"} ({$arCMSPageTrash|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th></th>
   </tr>
   {foreach from=$arCMSPageTrash item=CMSPage}
   <tr {cycle values=',class="alternate"' name="arCMSPageTrash"}>
      <td>{popupinformation text=$CMSPage->title_cmsp}
            {i18n key="copixheadings|workflow.messages.trashBy" param1=$CMSPage->statusauthor_cmsp param2=$CMSPage->statusdate_cmsp|datei18n}
            {if $CMSPage->statuscomment_cmsp}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsp}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsp}</td>
      <td>{$CMSPage->author_cmsp}</td>
      <td>{if $contribEnabled}
            <a href="{copixurl dest="cms|workflow|delete" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="admin|getDraft" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
            {copixurl dest="cms|workflow|restore" id=$CMSPage->publicid_cmsp assign=urlDraft}
            <a href="#" onclick="{jssubmitform href=$urlDraft form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.restore"}"><img src="{copixresource path="img/tools/restore.png"}" alt="{i18n key="copix:common.buttons.restore"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_cmsp_{$CMSPage->publicid_cmsp}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
</tbody>
</table>
</form>