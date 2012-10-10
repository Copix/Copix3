<form id="eventWorkflow" action="" class="CopixForm" method="post">
<table class="CopixTable">
<thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:23%">{i18n key="copix:common.actions.title"}</th>
   </tr>
</thead>
<tbody>
   <!--- ONLINE EVENT -->
   {if count($arEventPublish)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.online"} ({$arEventPublish|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arEventPublish item=event}
   <tr {cycle values=',class="alternate"' name="arEventPublish"}>
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
      <td>{if $publishEnabled}
            <a href="{copixurl dest="schedule|admin|delete" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="schedule|admin|cut" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.cut"}"><img src="{copixresource path="img/tools/cut.png"}" alt="{i18n key="copix:common.buttons.cut"}" /></a>
            <a href="{copixurl dest="schedule|admin|prepareEdit" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- DRAFT EVENT -->
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.draft"} ({$arEventDraft|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th><a href="{copixurl dest="admin|create" id_head=$id_head}" title="{i18n key="schedule.title.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="schedule.title.new"}" /></a>
          {if $pasteEnabled}
            <a href="{copixurl dest="admin|paste" level=$id_head}"><img src="{copixresource path="img/tools/paste.png"}" alt="{i18n key="copix:common.buttons.paste"}" title="{i18n key="copix:common.buttons.paste"}" /></a>
          {/if}</th>
   </tr>
   {foreach from=$arEventDraft item=event}
   <tr {cycle values=',class="alternate"' name="arEventDraft"}>
      <td>{popupinformation text=$event->title_evnt}
            {i18n key=dao.schedule.fields.datedisplayfrom_evnt} : {$event->datedisplayfrom_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datedisplayto_evnt} : {$event->datedisplayto_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datefrom_evnt} : {$event->datefrom_evnt|datei18n}<br />
            {if $event->dateto_evnt}{i18n key=dao.schedule.fields.dateto_evnt} : {$event->dateto_evnt|datei18n}<br />{/if}
            {if $event->editionkind_evnt eq "WIKI"}{$event->content_evnt|wiki}{else}{$event->content_evnt}{/if}<br />
            {i18n key="copixheadings|workflow.messages.createBy" param1=$event->statusauthor_evnt param2=$event->statusdate_evnt|datei18n}
            {if $event->statuscomment_evnt}{i18n key="copixheadings|workflow.messages.withComment" param=$event->statuscomment_evnt}{/if}
          {/popupinformation}
          {$event->title_evnt}</td>
      <td>{$event->author_evnt}</td>
      <td>{if $contribEnabled}
            {copixurl dest="schedule|admin|statusTrash" id_evnt=$event->id_evnt assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="eventWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="schedule|admin|prepareEdit" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="schedule|admin|statusPropose" id_evnt=$event->id_evnt assign=urlPropose}
            <a href="#" onclick="{jssubmitform href=$urlPropose form="eventWorkflow"}" title="{i18n key="copix:common.buttons.propose"}"><img src="{copixresource path="img/tools/propose.png"}" alt="{i18n key="copix:common.buttons.propose"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_evnt_{$event->id_evnt}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {foreachelse}
   <tr>
      <td colspan="3">{i18n key="copixheadings|workflow.messages.noDraft"}</td>
   </tr>
   {/foreach}

   <!--- REFUSE EVENT -->
   {if count($arEventRefuse)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.refuse"} ({$arEventRefuse|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}">-->{/if}</th>
   </tr>
   {foreach from=$arEventRefuse item=event}
   <tr {cycle values=',class="alternate"' name="arEventRefuse"}>
      <td>{popupinformation text=$event->title_evnt}
            {i18n key=dao.schedule.fields.datedisplayfrom_evnt} : {$event->datedisplayfrom_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datedisplayto_evnt} : {$event->datedisplayto_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datefrom_evnt} : {$event->datefrom_evnt|datei18n}<br />
            {if $event->dateto_evnt}{i18n key=dao.schedule.fields.dateto_evnt} : {$event->dateto_evnt|datei18n}<br />{/if}
            {if $event->editionkind_evnt eq "WIKI"}{$event->content_evnt|wiki}{else}{$event->content_evnt}{/if}<br />
            {i18n key="copixheadings|workflow.messages.refuseBy" param1=$event->statusauthor_evnt param2=$event->statusdate_evnt|datei18n}
            {if $event->statuscomment_evnt}{i18n key="copixheadings|workflow.messages.withComment" param=$event->statuscomment_evnt}{/if}
          {/popupinformation}
          {$event->title_evnt}</td>
      <td>{$event->author_evnt}</td>
      <td>{if $contribEnabled}
            {copixurl dest="schedule|admin|statusTrash" id_evnt=$event->id_evnt assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="eventWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="schedule|admin|prepareEdit" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_evnt_{$event->id_evnt}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TOVALID EVENT -->
   {if count($arEventPropose)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toValid"} ({$arEventPropose|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $validEnabled}<!--
            <img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copixheadings|workflow.messages.validAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arEventPropose item=event}
   <tr {cycle values=',class="alternate"' name="arEventPropose"}>
      <td>{popupinformation text=$event->title_evnt}
            {i18n key=dao.schedule.fields.datedisplayfrom_evnt} : {$event->datedisplayfrom_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datedisplayto_evnt} : {$event->datedisplayto_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datefrom_evnt} : {$event->datefrom_evnt|datei18n}<br />
            {if $event->dateto_evnt}{i18n key=dao.schedule.fields.dateto_evnt} : {$event->dateto_evnt|datei18n}<br />{/if}
            {if $event->editionkind_evnt eq "WIKI"}{$event->content_evnt|wiki}{else}{$event->content_evnt}{/if}<br />
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$event->statusauthor_evnt param2=$event->statusdate_evnt|datei18n}
            {if $event->statuscomment_evnt}{i18n key="copixheadings|workflow.messages.withComment" param=$event->statuscomment_evnt}{/if}
          {/popupinformation}
          {$event->title_evnt}</td>
      <td>{$event->author_evnt}</td>
      <td>{if $validEnabled}
            {copixurl dest="schedule|admin|statusTrash" id_evnt=$event->id_evnt assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="eventWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="schedule|admin|prepareEdit" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="schedule|admin|statusValid" id_evnt=$event->id_evnt assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="eventWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="schedule|admin|statusRefuse" id_evnt=$event->id_evnt assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="eventWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_evnt_{$event->id_evnt}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TOPUBLISH EVENT -->
   {if count($arEventValid)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toPublish"} ({$arEventValid|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--
            <img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copixheadings|workflow.messages.publishAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arEventValid item=event}
   <tr {cycle values=',class="alternate"' name="arEventValid"}>
      <td>{popupinformation text=$event->title_evnt}
            {i18n key=dao.schedule.fields.datedisplayfrom_evnt} : {$event->datedisplayfrom_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datedisplayto_evnt} : {$event->datedisplayto_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datefrom_evnt} : {$event->datefrom_evnt|datei18n}<br />
            {if $event->dateto_evnt}{i18n key=dao.schedule.fields.dateto_evnt} : {$event->dateto_evnt|datei18n}<br />{/if}
            {if $event->editionkind_evnt eq "WIKI"}{$event->content_evnt|wiki}{else}{$event->content_evnt}{/if}<br />
            {i18n key="copixheadings|workflow.messages.validBy" param1=$event->statusauthor_evnt param2=$event->statusdate_evnt|datei18n}
            {if $event->statuscomment_evnt}{i18n key="copixheadings|workflow.messages.withComment" param=$event->statuscomment_evnt}{/if}
          {/popupinformation}
          {$event->title_evnt}</td>
      <td>{$event->author_evnt}</td>
      <td>{if $publishEnabled}
            {copixurl dest="schedule|admin|statusTrash" id_evnt=$event->id_evnt assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="eventWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="schedule|admin|prepareEdit" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="schedule|admin|statusPublish" id_evnt=$event->id_evnt assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="eventWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="schedule|admin|statusRefuse" id_evnt=$event->id_evnt assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="eventWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_evnt_{$event->id_evnt}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TRASH EVENT -->
   {if count($arEventTrash)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.trash"} ({$arEventTrash|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arEventTrash item=event}
   <tr {cycle values=',class="alternate"' name="arEventTrash"}>
      <td>{popupinformation text=$event->title_evnt}
            {i18n key=dao.schedule.fields.datedisplayfrom_evnt} : {$event->datedisplayfrom_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datedisplayto_evnt} : {$event->datedisplayto_evnt|datei18n}<br />
            {i18n key=dao.schedule.fields.datefrom_evnt} : {$event->datefrom_evnt|datei18n}<br />
            {if $event->dateto_evnt}{i18n key=dao.schedule.fields.dateto_evnt} : {$event->dateto_evnt|datei18n}<br />{/if}
            {if $event->editionkind_evnt eq "WIKI"}{$event->content_evnt|wiki}{else}{$event->content_evnt}{/if}<br />
            {i18n key="copixheadings|workflow.messages.trashBy" param1=$event->statusauthor_evnt param2=$event->statusdate_evnt|datei18n}
            {if $event->statuscomment_evnt}{i18n key="copixheadings|workflow.messages.withComment" param=$event->statuscomment_evnt}{/if}
          {/popupinformation}
          {$event->title_evnt}</td>
      <td>{$event->author_evnt}</td>
      <td>{if $contribEnabled}
            <a href="{copixurl dest="schedule|admin|delete" id_evnt=$event->id_evnt}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            {copixurl dest="schedule|admin|statusDraft" id_evnt=$event->id_evnt assign=urlDraft}
            <a href="#" onclick="{jssubmitform href=$urlDraft form="eventWorkflow"}" title="{i18n key="copix:common.buttons.restore"}"><img src="{copixresource path="img/tools/restore.png"}" alt="{i18n key="copix:common.buttons.restore"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_evnt_{$event->id_evnt}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
</tbody>
</table>
</form>