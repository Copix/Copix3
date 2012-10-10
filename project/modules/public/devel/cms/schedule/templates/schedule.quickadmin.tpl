<div class="quickAdminModule">
<!-- EVENT TO VALID -->
{copixurl dest="quickadmin|admin|" currentModule="schedule" assign=backUrl}
<form id="eventWorkflow" action="" class="CopixForm" method="post">
{if count($toValid)}
<h2>{i18n key="copixheadings|workflow.messages.toValid"} ({$toValid|@count} {i18n key="copixheadings|workflow.messages.item"})</h2>
<table class="CopixTable">
   <thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:23%">{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {assign var=heading value='NOT_AN_ID'}
   {foreach from=$toValid item=event}
   {if $event->id_head != $heading}
      {assign var=heading value=$event->id_head}
      <tr>
      <th colspan="3">{if $event->caption_head}{$event->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$event->id_head}>
       <td>{popupinformation text=$event->title_evnt}
            {i18n key=dao.schedule.fields.datedisplayfrom_evnt} : {$event->datedisplayfrom_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datedisplayto_evnt} : {$event->datedisplayto_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datefrom_evnt} : {$event->datefrom_evnt|datei18n}<br />
            {if $event->dateto_evnt}{i18n key=dao.schedule.fields.dateto_evnt} : {$event->dateto_evnt|datei18n}<br />{/if}
            {if $event->editionkind_evnt eq "WIKI"}{$event->content_evnt|wiki}{else}{$event->content_evnt}{/if}<br />
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$event->statusauthor_evnt param2=$event->statusdate_evnt|datei18n}
            {if $event->statuscomment_evnt}{i18n key="copixheadings|workflow.messages.withComment" param=$event->statuscomment_evnt}{/if}
          {/popupinformation}
          {$event->title_evnt}</td>
       <td>{$event->author_evnt}</td>
       <td>
          {copixurl dest="schedule|admin|statusTrash" back=$backUrl|urlencode id_evnt=$event->id_evnt assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="eventWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="schedule|admin|prepareEdit" back=$backUrl|urlencode id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="schedule|admin|statusValid" back=$backUrl|urlencode id_evnt=$event->id_evnt assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="eventWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="schedule|admin|statusRefuse" back=$backUrl|urlencode id_evnt=$event->id_evnt assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="eventWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_evnt_{$event->id_evnt}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}

<!-- EVENT TO PUBLISH -->
{if count($toPublish)}
<h2>{i18n key="copixheadings|workflow.messages.toPublish"} ({$toPublish|@count} {i18n key="copixheadings|workflow.messages.item"})</h2>
<table class="CopixTable">
   <thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:23%">{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {assign var=heading value='NOT_AN_ID'}
   {foreach from=$toPublish item=event}
   {if $event->id_head != $heading}
      {assign var=heading value=$event->id_head}
      <tr>
      <th colspan="3">{if $event->caption_head}{$event->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$event->id_head}>
       <td>{popupinformation text=$event->title_evnt}
            {i18n key=dao.schedule.fields.datedisplayfrom_evnt} : {$event->datedisplayfrom_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datedisplayto_evnt} : {$event->datedisplayto_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datefrom_evnt} : {$event->datefrom_evnt|datei18n}<br />
            {if $event->dateto_evnt}{i18n key=dao.schedule.fields.dateto_evnt} : {$event->dateto_evnt|datei18n}<br />{/if}
            {if $event->editionkind_evnt eq "WIKI"}{$event->content_evnt|wiki}{else}{$event->content_evnt}{/if}<br />
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$event->statusauthor_evnt param2=$event->statusdate_evnt|datei18n}
            {if $event->statuscomment_evnt}{i18n key="copixheadings|workflow.messages.withComment" param=$event->statuscomment_evnt}{/if}
          {/popupinformation}
          {$event->title_evnt}</td>
       <td>{$event->author_evnt}</td>
       <td>
          {copixurl dest="schedule|admin|statusTrash" back=$backUrl|urlencode id_evnt=$event->id_evnt assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="eventWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="schedule|admin|prepareEdit" back=$backUrl|urlencode id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="schedule|admin|statusPublish" back=$backUrl|urlencode id_evnt=$event->id_evnt assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="eventWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="schedule|admin|statusRefuse" back=$backUrl|urlencode id_evnt=$event->id_evnt assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="eventWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_evnt_{$event->id_evnt}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
</form>
</div>
