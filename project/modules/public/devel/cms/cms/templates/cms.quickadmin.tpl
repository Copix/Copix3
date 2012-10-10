<div class="quickAdminModule">
<!--- TOVALID CMSPAGE -->
{copixurl dest="quickadmin|admin|" currentModule="cms" assign=backUrl}
<form id="CMSPageWorkflow" action="" class="CopixForm" method="post">
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
   {foreach from=$toValid item=CMSPage}
   {if $CMSPage->id_head != $heading}
      {assign var=heading value=$CMSPage->id_head}
      <tr>
      <th colspan="3">{if $CMSPage->caption_head}{$CMSPage->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr {cycle values=',class="alternate"' name="arCMSPagePropose"}>
      <td>{popupinformation text=$CMSPage->title_cmsd}
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$CMSPage->statusauthor_cmsd param2=$CMSPage->statusdate_cmsd|datei18n}
            {if $CMSPage->statuscomment_cmsd}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsd}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsd}</td>
      <td>{$CMSPage->author_cmsd}</td>
      <td>
            {copixurl dest="cms|workflow|trash" back=$backUrl|urlencode id=$CMSPage->id_cmsd assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="admin|getDraft" id=$CMSPage->id_cmsd}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
            <a href="{copixurl dest="cms|admin|prepareEdit" back=$backUrl|urlencode id=$CMSPage->id_cmsd}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="cms|workflow|valid" back=$backUrl|urlencode id=$CMSPage->id_cmsd assign=urlValid notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlValid form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="cms|workflow|refuse" back=$backUrl|urlencode id=$CMSPage->id_cmsd assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_cmsd_{$CMSPage->id_cmsd}" value=""/>
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
   {foreach from=$toPublish item=CMSPage}
   {if $CMSPage->id_head != $heading}
      {assign var=heading value=$CMSPage->id_head}
      <tr>
      <th colspan="3">{if $CMSPage->caption_head}{$CMSPage->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr {cycle values=',class="alternate"' name="arCMSPageValid"}>
      <td>{popupinformation text=$CMSPage->title_cmsd}
            {i18n key="copixheadings|workflow.messages.validBy" param1=$CMSPage->statusauthor_cmsd param2=$CMSPage->statusdate_cmsd|datei18n}
            {if $CMSPage->statuscomment_cmsd}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsd}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsd}</td>
      <td>{$CMSPage->author_cmsd}</td>
      <td>
            {copixurl dest="cms|workflow|trash" back=$backUrl|urlencode id=$CMSPage->id_cmsd assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="admin|getDraft" id=$CMSPage->id_cmsd}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
            <a href="{copixurl dest="cms|admin|prepareEdit" back=$backUrl|urlencode id=$CMSPage->id_cmsd}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="cms|workflow|publish" back=$backUrl|urlencode id=$CMSPage->id_cmsd assign=urlPublish notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="cms|workflow|refuse" back=$backUrl|urlencode id=$CMSPage->id_cmsd assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="CMSPageWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_cmsd_{$CMSPage->id_cmsd}" value=""/>
          {/popupinformation}
      </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
</form>
</div>