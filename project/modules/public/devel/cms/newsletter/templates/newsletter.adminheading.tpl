{**
* Front page to the cms actions
*}
{if $newsletterSendEnabled}
<ul class="copixCMSNav">
     <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="copixheadings|admin|default" browse="newsletter" level=$id_head kind="0"}">{i18n key="newsletter.title.general"}</a></li>
     <li {if $kind=="history"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="copixheadings|admin|default" browse="newsletter" level=$id_head kind="3"}">{i18n key="newsletter.title.history"}</a></li>
     {if $newsletterModerateEnabled}
     <li {if $kind=="groups"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="copixheadings|admin|default" browse="newsletter" level=$id_head kind="1"}">{i18n key="newsletter.title.groups"}</a></li>
     <li {if $kind=="users"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="copixheadings|admin|default" browse="newsletter" level=$id_head kind="2"}">{i18n key="newsletter.title.users"}</a></li>
     {/if}
</ul>

{if $kind=="users"}
{if count ($arMails)}
<table class="CopixTable">
   <thead>
   <tr>
    <th>{i18n key="dao.newslettermail.fields.mail_nlm"}</th>
    <th>{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {foreach from=$arMails item=mail}
   {if $mail->name_nlg <> $oldNameGroup}
   <tr><th colspan="2">{$mail->name_nlg}</th>
   </tr>
   {/if}
   {assign var='oldNameGroup' value=$mail->name_nlg}
   <tr {cycle values=',class="alternate"' name=$oldNameGroup}>
    <td>{$mail->mail_nlm}</td>
    <td><a href="{copixurl dest="newsletter|mail|prepareEdit"   mail_nlm=$mail->mail_nlm id_head=$id_head}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key='copix:common.buttons.update'}" /></a>
        <a href="{copixurl dest="newsletter|mail|delete" mail_nlm=$mail->mail_nlm id_head=$id_head}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key='copix:common.buttons.delete'}" /></a>
    </td>
   </tr>
   {/foreach}
   </tbody>
</table>
<p style="text-align:center">
{$multipage}
</p>
{else}
   {i18n key="newsletter.messages.noSubscriber"}
{/if}
<br />
<p style="text-align:left">
   <a href="{copixurl dest="newsletter|mail|create" id_head=$id_head}" title="{i18n key="copix:common.buttons.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}" />{i18n key="copix:common.buttons.new"}</a>
</p>
{/if}


{if $kind=="history"}
{if count($arAlreadySend)}
<table class="CopixTable">
<thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="dao.newslettersend.fields.date_nls"}</th>
      <th>{i18n key="newsletter.messages.group"}</th>
      <th style="width:10%">{i18n key="copix:common.actions.title"}</th>
   </tr>
</thead>
<tbody>
   {foreach from=$arAlreadySend item=newsletter}
   <tr {cycle values=',class="alternate"' name="arCMSPagePublish"}>
      <td>{$newsletter->title_nls}</td>
      <td>{$newsletter->date_nls|datei18n}</td>
      <td><ul>
          {foreach from=$newsletter->groups item=group}
            <li>{$group}</li>
          {/foreach}
          </ul>
      </td>
      <td><a href="{copixurl dest="newsletter|default|get" id=$newsletter->id_cmsp date=$newsletter->date_nls}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a></td>
   </tr>
   {/foreach}
</tbody>
</table>
{else}
{i18n key="newsletter.messages.noHistory"}
{/if}
{/if}

{if $kind=="general"}
{if count($arCMSPagePublish)}
<form id="Newsletter" action="" class="CopixForm" method="post">
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
   {foreach from=$arCMSPagePublish item=CMSPage}
   <tr {cycle values=',class="alternate"' name="arCMSPagePublish"}>
      <td>{popupinformation text=$CMSPage->title_cmsp}
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$CMSPage->statusauthor_cmsp param2=$CMSPage->statusdate_cmsp|datei18n}
            {if $CMSPage->statuscomment_cmsp}{i18n key="copixheadings|workflow.messages.withComment" param=$CMSPage->statuscomment_cmsp}{/if}
          {/popupinformation}
          {$CMSPage->title_cmsp}</td>
      <td>{$CMSPage->author_cmsp}</td>
      <td>
         <a href="{copixurl dest="cms|default|get" id=$CMSPage->publicid_cmsp}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
         {if $newsletterSendEnabled}
            <a href="{copixurl dest="newsletter|admin|prepareSendToGroup" id=$CMSPage->publicid_cmsp}" title="{i18n key="newsletter.messages.send"}"><img src="{copixresource path="img/tools/mail.png"}" alt="{i18n key="newsletter.messages.send"}" /></a>
            <a href="{copixurl dest="newsletter|admin|prepareSendTest"    id=$CMSPage->publicid_cmsp}" title="{i18n key="newsletter.messages.test"}"><img src="{copixresource path="img/tools/test.png"}" alt="{i18n key="newsletter.messages.test"}" /></a>
         {/if}
      </td>
   </tr>
   {/foreach}
</tbody>
</table>
</form>
{else}
{i18n key="newsletter.messages.noPages"}
{/if}
{i18n key="newsletter.messages.explainNewsletter"}
{/if}

{if $kind=="groups"}
   {if $newsletterModerateEnabled}
   {if $showErrors}
   <ul>
      {if $erreur}
      <li>{$erreur}</li>
      {else}
      {foreach from=$errors item=message}
        <li>{$message}</li>
      {/foreach}
      {/if}
   </ul>
   {/if}
   {if count ($arGroups)}
    <table class="CopixTable">
       <thead>
          <tr>
              <th>{i18n key="dao.newslettergroups.fields.name_nlg"}</th>
              <th class="actions">{i18n key="copix:common.actions.title"}</th>
          </tr>
       </thead>
       <tbody>
            {foreach from=$arGroups item=group}
               {if $toEdit->id_nlg eq $group->id_nlg}
               <form action="{copixurl dest="newsletter|groups|valid"}" method="post">
                <tr>
                <td><input size="48" type="text" name="name_nlg" value="{$toEdit->name_nlg|escape}" /></td>
                <td>
                 <input type="image" src="{copixresource path="img/tools/valid.png"}" value="{i18n key="copix:common.buttons.ok"}" />
                 <a href="{copixurl dest="newsletter|groups|cancelEdit"}"><img src="{copixresource path="img/tools/cancel.png"}" alt="{i18n key="copix:common.buttons.cancel"}" /></a>
                </td>
                <tr>
                  <td colspan="2">{i18n key=dao.newslettergroups.fields.desc_nlg}<br /><textarea cols="48" name="desc_nlg">{$toEdit->desc_nlg|escape}</textarea></td>
                </tr>
               </tr>
               </form>
               {else}
               <tr {cycle values=',class="alternate"'}>
                <td>{popupinformation text=$group->name_nlg}
                        {i18n key='dao.newslettergroups.fields.desc_nlg'} : {$group->desc_nlg}
                    {/popupinformation}
                    {$group->name_nlg|escape:html}</td>
                <td>
                 <a href="{copixurl dest="newsletter|groups|prepareEdit" id_nlg=$group->id_nlg id_head=$id_head}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
                 <a href="{copixurl dest="newsletter|groups|confirmDelete" id_nlg=$group->id_nlg id_head=$id_head}" title="{i18n key="copix:common.buttons.delete"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
                </td>
               </tr>
               {/if}
            {/foreach}
       </tbody>
    </table>
   {else}
   <p>
   {i18n key="newsletter.messages.noGroup"}
   </p>
   {/if}
   {if $toEdit}
      {if $toEdit->id_nlg > 0}
      {else}
      <h2>{i18n key="newsletter.title.createGroup"}</h2>
      <form action="{copixurl dest="newsletter|groups|valid"}" method="post">
         <table class="CopixTable">
            <tr><th>{i18n key='dao.newslettergroups.fields.name_nlg'}</th>
                <td><input size="48" type="text" name="name_nlg" value="{$toEdit->name_nlg|escape}" /></td></tr>
            <tr><th>{i18n key='dao.newslettergroups.fields.desc_nlg'}</th>
                <td><textarea cols="44" name="desc_nlg">{$toEdit->desc_nlg|escape}</textarea></td></tr>
         </table>
         <p>
         <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
         <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="newsletter|groups|cancelEdit"}'" />
         </p>
      </form>
      {/if}
   {else}
   <br />
   <p>
   <a href="{copixurl dest="newsletter|groups|create" id_head=$id_head}" title="{i18n key="copix:common.buttons.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}" />{i18n key="copix:common.buttons.new"}</a>
   </p>
   {/if}
   {/if}
{/if}
{/if}
