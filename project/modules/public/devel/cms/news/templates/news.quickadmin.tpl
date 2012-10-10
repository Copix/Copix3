<div class="quickAdminModule">
<!-- NEWS TO VALID -->
{copixurl dest="quickadmin|admin|" currentModule="news" assign=backUrl}
<form id="newsWorkflow" action="" class="CopixForm" method="post">
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
   {foreach from=$toValid item=news}
   {if $news->id_head != $heading}
      {assign var=heading value=$news->id_head}
      <tr>
      <th colspan="3">{if $news->caption_head}{$news->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$news->id_head}>
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
       <td>
          {copixurl dest="news|admin|statusTrash" back=$backUrl|urlencode id_news=$news->id_news assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="newsWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="news|admin|prepareEdit" back=$backUrl|urlencode id_news=$news->id_news}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="news|admin|statusValid" back=$backUrl|urlencode id_news=$news->id_news assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="newsWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="news|admin|statusRefuse" back=$backUrl|urlencode id_news=$news->id_news assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="newsWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_news_{$news->id_news}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}

<!-- NEWS TO PUBLISH -->
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
   {foreach from=$toPublish item=news}
   {if $news->id_head != $heading}
      {assign var=heading value=$news->id_head}
      <tr>
      <th colspan="3">{if $news->caption_head}{$news->caption_head}{else}{i18n key="copixheadings|headings.message.root"}{/if}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$news->id_head}>
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
       <td>
          {copixurl dest="news|admin|statusTrash" back=$backUrl|urlencode id_news=$news->id_news assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="newsWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="news|admin|prepareEdit" back=$backUrl|urlencode id_news=$news->id_news}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="news|admin|statusPublish" back=$backUrl|urlencode id_news=$news->id_news assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="newsWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="news|admin|statusRefuse" back=$backUrl|urlencode id_news=$news->id_news assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="newsWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_news_{$news->id_news}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
</form>
</div>
