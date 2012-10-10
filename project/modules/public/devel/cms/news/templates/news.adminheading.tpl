<form id="newsWorkflow" action="" class="CopixForm" method="post">
<table class="CopixTable">
<thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:23%">{i18n key="copix:common.actions.title"}</th>
   </tr>
</thead>
<tbody>
   <!--- ONLINE NEWS -->
   {if count($arNewsPublish)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.online"} ({$arNewsPublish|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arNewsPublish item=news}
   <tr {cycle values=',class="alternate"' name="arNewsPublish"}>
      <td>{popupinformation text=$news->title_news}
            <dl>
            <dt>{i18n key="dao.news.fields.summary_news"}</dt>
            <dd>{$news->summary_news}</dd>
            <dt>{i18n key="dao.news.fields.content_news"}</dt>
            <dd>{$news->content_news}</dd>
            </dl>
            {i18n key=dao.news.fields.datewished_news} : {$news->datewished_news}<br />
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$news->statusauthor_news param2=$news->statusdate_news|datei18n}
            {if $news->statuscomment_news}{i18n key="copixheadings|workflow.messages.withComment" param=$news->statuscomment_news}{/if}
          {/popupinformation}
          {$news->title_news}</td>
      <td>{$news->author_news}</td>
      <td>{if $publishEnabled}
            <a href="{copixurl dest="news|admin|delete" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="news|admin|cut" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.cut"}"><img src="{copixresource path="img/tools/cut.png"}" alt="{i18n key="copix:common.buttons.cut"}" /></a>
            <a href="{copixurl dest="news|admin|prepareEdit" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- DRAFT NEWS -->
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.draft"} ({$arNewsDraft|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th><a href="{copixurl dest="admin|create" id_head=$id_head}" title="{i18n key="news.title.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="news.title.new"}" /></a>
          {if $pasteEnabled}
           <a href="{copixurl dest="admin|paste" level=$id_head}"><img src="{copixresource path="img/tools/paste.png"}" alt="{i18n key="copix:common.buttons.paste"}" title="{i18n key="copix:common.buttons.paste"}" /></a>
          {/if}</th>
   </tr>
   {foreach from=$arNewsDraft item=news}
   <tr {cycle values=',class="alternate"' name="arNewsDraft"}>
      <td>{popupinformation text=$news->title_news}
            <dl>
            <dt>{i18n key="dao.news.fields.summary_news"}</dt>
            <dd>{$news->summary_news}</dd>
            <dt>{i18n key="dao.news.fields.content_news"}</dt>
            <dd>{$news->content_news}</dd>
            </dl>
            {i18n key=dao.news.fields.datewished_news} : {$news->datewished_news}<br />
            {i18n key="copixheadings|workflow.messages.createBy" param1=$news->statusauthor_news param2=$news->statusdate_news|datei18n}
            {if $news->statuscomment_news}{i18n key="copixheadings|workflow.messages.withComment" param=$news->statuscomment_news}{/if}
          {/popupinformation}
          {$news->title_news}</td>
      <td>{$news->author_news}</td>
      <td>{if $contribEnabled}
            {copixurl dest="news|admin|statusTrash" id_news=$news->id_news assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="newsWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="news|admin|prepareEdit" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="news|admin|statusPropose" id_news=$news->id_news assign=urlPropose}
            <a href="#" onclick="{jssubmitform href=$urlPropose form="newsWorkflow"}" title="{i18n key="copix:common.buttons.propose"}"><img src="{copixresource path="img/tools/propose.png"}" alt="{i18n key="copix:common.buttons.propose"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_news_{$news->id_news}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {foreachelse}
   <tr>
      <td colspan="3">{i18n key="copixheadings|workflow.messages.noDraft"}</td>
   </tr>
   {/foreach}

   <!--- REFUSE NEWS -->
   {if count($arNewsRefuse)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.refuse"} ({$arNewsRefuse|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}">-->{/if}</th>
   </tr>
   {foreach from=$arNewsRefuse item=news}
   <tr {cycle values=',class="alternate"' name="arNewsRefuse"}>
      <td>{popupinformation text=$news->title_news}
            <dl>
            <dt>{i18n key="dao.news.fields.summary_news"}</dt>
            <dd>{$news->summary_news}</dd>
            <dt>{i18n key="dao.news.fields.content_news"}</dt>
            <dd>{$news->content_news}</dd>
            </dl>
            {i18n key=dao.news.fields.datewished_news} : {$news->datewished_news}<br />
            {i18n key="copixheadings|workflow.messages.refuseBy" param1=$news->statusauthor_news param2=$news->statusdate_news|datei18n}
            {if $news->statuscomment_news}{i18n key="copixheadings|workflow.messages.withComment" param=$news->statuscomment_news}{/if}
          {/popupinformation}
          {$news->title_news}</td>
      <td>{$news->author_news}</td>
      <td>{if $contribEnabled}
            {copixurl dest="news|admin|statusTrash" id_news=$news->id_news assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="newsWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="news|admin|prepareEdit" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_news_{$news->id_news}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TOVALID NEWS -->
   {if count($arNewsPropose)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toValid"} ({$arNewsPropose|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $validEnabled}<!--
            <img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copixheadings|workflow.messages.validAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arNewsPropose item=news}
   <tr {cycle values=',class="alternate"' name="arNewsPropose"}>
      <td>{popupinformation text=$news->title_news}
            <dl>
            <dt>{i18n key="dao.news.fields.summary_news"}</dt>
            <dd>{$news->summary_news}</dd>
            <dt>{i18n key="dao.news.fields.content_news"}</dt>
            <dd>{$news->content_news}</dd>
            </dl>
            {i18n key=dao.news.fields.datewished_news} : {$news->datewished_news}<br />
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$news->statusauthor_news param2=$news->statusdate_news|datei18n}
            {if $news->statuscomment_news}{i18n key="copixheadings|workflow.messages.withComment" param=$news->statuscomment_news}{/if}
          {/popupinformation}
          {$news->title_news}</td>
      <td>{$news->author_news}</td>
      <td>{if $validEnabled}
            {copixurl dest="news|admin|statusTrash" id_news=$news->id_news assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="newsWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="news|admin|prepareEdit" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="news|admin|statusValid" id_news=$news->id_news assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="newsWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="news|admin|statusRefuse" id_news=$news->id_news assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="newsWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_news_{$news->id_news}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TOPUBLISH NEWS -->
   {if count($arNewsValid)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toPublish"} ({$arNewsValid|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--
            <img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copixheadings|workflow.messages.publishAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arNewsValid item=news}
   <tr {cycle values=',class="alternate"' name="arNewsValid"}>
      <td>{popupinformation text=$news->title_news}
            <dl>
            <dt>{i18n key="dao.news.fields.summary_news"}</dt>
            <dd>{$news->summary_news}</dd>
            <dt>{i18n key="dao.news.fields.content_news"}</dt>
            <dd>{$news->content_news}</dd>
            </dl>
            {i18n key=dao.news.fields.datewished_news} : {$news->datewished_news}<br />
            {i18n key="copixheadings|workflow.messages.validBy" param1=$news->statusauthor_news param2=$news->statusdate_news|datei18n}
            {if $news->statuscomment_news}{i18n key="copixheadings|workflow.messages.withComment" param=$news->statuscomment_news}{/if}
          {/popupinformation}
          {$news->title_news}</td>
      <td>{$news->author_news}</td>
      <td>{if $publishEnabled}
            {copixurl dest="news|admin|statusTrash" id_news=$news->id_news assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="newsWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="news|admin|prepareEdit" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="news|admin|statusPublish" id_news=$news->id_news assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="newsWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="news|admin|statusRefuse" id_news=$news->id_news assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="newsWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_news_{$news->id_news}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TRASH NEWS -->
   {if count($arNewsTrash)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.trash"} ({$arNewsTrash|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arNewsTrash item=news}
   <tr {cycle values=',class="alternate"' name="arNewsTrash"}>
      <td>{popupinformation text=$news->title_news}
            <dl>
            <dt>{i18n key="dao.news.fields.summary_news"}</dt>
            <dd>{$news->summary_news}</dd>
            <dt>{i18n key="dao.news.fields.content_news"}</dt>
            <dd>{$news->content_news}</dd>
            </dl>
            {i18n key=dao.news.fields.datewished_news} : {$news->datewished_news}<br />
            {i18n key="copixheadings|workflow.messages.trashBy" param1=$news->statusauthor_news param2=$news->statusdate_news|datei18n}
            {if $news->statuscomment_news}{i18n key="copixheadings|workflow.messages.withComment" param=$news->statuscomment_news}{/if}
          {/popupinformation}
          {$news->title_news}</td>
      <td>{$news->author_news}</td>
      <td>{if $contribEnabled}
            <a href="{copixurl dest="news|admin|delete" id_news=$news->id_news}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            {copixurl dest="news|admin|statusDraft" id_news=$news->id_news assign=urlDraft}
            <a href="#" onclick="{jssubmitform href=$urlDraft form="newsWorkflow"}" title="{i18n key="copix:common.buttons.restore"}"><img src="{copixresource path="img/tools/restore.png"}" alt="{i18n key="copix:common.buttons.restore"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_news_{$news->id_news}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
</tbody>
</table>
</form>
