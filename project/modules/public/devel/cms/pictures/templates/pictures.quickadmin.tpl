<div class="quickAdminModule">
{copixurl dest="quickadmin|admin|" currentModule="pictures" assign=backUrl}
<!-- PICTURE TO VALID -->
<form id="pictureWorkflow" action="" class="CopixForm" method="post">
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
   {foreach from=$toValid item=picture}
   {if $picture->id_head != $heading}
      {assign var=heading value=$picture->id_head}
      <tr>
      <th colspan="3">{$picture->caption_head}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$picture->id_head}>
       <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if (count($picture->theme)>0)}
            {i18n key=pictures.title.theme} :
            <ul>
            {foreach from=$picture->theme item=theme}
            <li>{$theme}</li>
            {/foreach}
            </ul>
            {/if}
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
       <td>{$picture->author_pict}</td>
       <td>
          {copixurl dest="pictures|admin|statusTrashPicture" id_pict=$picture->id_pict back=$backUrl|urlencode assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="pictures|admin|prepareEditPicture" back=$backUrl|urlencode id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixurl}img/tools/update.png" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="pictures|admin|statusValidPicture" back=$backUrl|urlencode id_pict=$picture->id_pict assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="pictures|admin|statusRefusePicture" back=$backUrl|urlencode id_pict=$picture->id_pict assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_pict_{$picture->id_pict}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}

<!-- PICTURE TO PUBLISH -->
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
   {foreach from=$toPublish item=picture}
   {if $picture->id_head != $heading}
      {assign var=heading value=$picture->id_head}
      <tr>
      <th colspan="3">{$picture->caption_head}</td>
      </tr>
   {/if}
   <tr{cycle values=',class="alternate"' name=$picture->id_head}>
       <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if (count($picture->theme)>0)}
            {i18n key=pictures.title.theme} :
            <ul>
            {foreach from=$picture->theme item=theme}
            <li>{$theme}</li>
            {/foreach}
            </ul>
            {/if}
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
       <td>{$picture->author_pict}</td>
       <td>
          {copixurl dest="pictures|admin|statusTrashPicture" back=$backUrl|urlencode id_pict=$picture->id_pict assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="pictures|admin|prepareEditPicture" back=$backUrl|urlencode id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="pictures|admin|statusPublishPicture" back=$backUrl|urlencode id_pict=$picture->id_pict assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="pictures|admin|statusRefusePicture" back=$backUrl|urlencode id_pict=$picture->id_pict assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_pict_{$picture->id_pict}" value=""/>
          {/popupinformation}
       </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
</form>
</div>
